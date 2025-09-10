<?php
$cambio = $this->cierre->Ultimo(); // Obtener las cotizaciones del último cierre

// Obtener el monto de la compra (puede ser edición o nueva)
$id_compra = isset($_GET['id_compra']) ? $_GET['id_compra'] : 0;
$monto_venta = $this->compra_tmp->ObtenerMonto($id_compra);

$pagototal = 0;
$pago_gs = 0;

// Verificar si hay pagos existentes (es una edición)
$pagos_existentes = $this->pago_tmp->ListarPagos();
$es_edicion = count($pagos_existentes) > 0;

// Calcular el total pagado convertido a GS usando la cotización específica de cada pago
foreach ($this->pago_tmp->ListarPagos() as $monto_pago) :
    if ($monto_pago->moneda == 'GS') {
        $pago_gs = $monto_pago->monto;
    } else {
        // Usar la cotización específica del pago si existe, sino usar la del cierre
        $cotizacion_pago = isset($monto_pago->cambio) ? $monto_pago->cambio : 1;
        if ($monto_pago->moneda == 'USD' && $cotizacion_pago == 1) {
            $cotizacion_pago = $cambio->cot_dolar; // Fallback para compatibilidad
        } elseif ($monto_pago->moneda == 'RS' && $cotizacion_pago == 1) {
            $cotizacion_pago = $cambio->cot_real; // Fallback para compatibilidad
        }
        $pago_gs = $monto_pago->monto * $cotizacion_pago;
    }
    $pagototal += $pago_gs;
endforeach;

if ($monto_venta) $monto_venta->monto = intval($monto_venta->monto);

// El saldo está en Guaraníes (monto_venta->monto ya está en GS)
$saldo = $monto_venta->monto - $pagototal;

if ($saldo < 0.5) {
    $saldo = 0;
}

$cliente = isset($id_cliente) ? $id_cliente : $_POST['id_cliente'];
if(isset($idCliente)){
    $valores = $this->deuda->listar_cliente_deuda($idCliente);
    $saldo_existente = !empty($valores) ? $valores[0]->saldo : '';
}

?>
<input type="hidden" name="monto_total" id="monto_total" value="<?php echo $monto_venta->monto; ?>">
<input type="hidden" id="id_cliente" value="<?php echo isset($_POST['id_cliente']) ? $_POST['id_cliente'] : ''; ?>">
<input type="hidden" id="monto_sumado" value="">
<input type="hidden" name="cot_dolar" id="cot_dolar" value="<?php echo $cambio->cot_dolar ?>">
<input type="hidden" name="cot_real" id="cot_real" value="<?php echo $cambio->cot_real ?>">
<input type="hidden" id="monto_saldo" value="<?php echo $saldo; ?>">

<?php if ($es_edicion): ?>
<div class="alert alert-warning">
    <i class="fa fa-edit"></i> 
    <strong>Edición de pagos:</strong> Se han cargado los pagos actuales de esta compra. Puede eliminarlos, modificarlos o agregar nuevos pagos según sea necesario.
</div>
<?php endif; ?>

<div class="form-group mt-3">
    <table class="table table-sm table-bordered table-striped display responsive nowrap" style="text-align: center; border-collapse: collapse;">
        <tbody>
            <tr style="background-color: #eeeeee;">
                <th style="text-align: center;">Saldo (GS)</th>
                <th style="text-align: center;">Saldo (USD)</th>
                <th style="text-align: center;">Saldo (RS)</th>
            </tr>
            <tr>
                <td id="total_gs"><?php echo number_format($saldo, 0, ".", ".") ?></td>
                <td id="total_usd"><?php echo number_format(($saldo / $cambio->cot_dolar), 3, ".", ".") ?></td>
                <td id="total_rs"><?php echo number_format(($saldo / $cambio->cot_real), 3, ".", ".") ?></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="contenedor-pagos">
    <?php if ($saldo != 0) : ?>
        <h3>Pagos</h3>
        <form method="post" id="pago_frm">
            <div class="row">
                <div class="form-group col-sm-3">
                    <label>Método de pago</label>
                    <select class="form-control" id="forma_pago_tmp">
                        <?php foreach ($this->metodo->Listar() as $m) : ?>
                            <option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-sm-2">
                    <label>Moneda</label>
                    <select name="moneda" id="moneda_pago" class="form-control">
                        <option value="GS">GS</option>
                        <option value="USD">USD</option>
                        <option value="RS">RS</option>
                    </select>
                </div>
                <div class="form-group col-sm-2">
                    <label>Cotización</label>
                    <input type="number" name="cambio" id="cambio_pago" class="form-control" step="1" value="1" placeholder="Cotización">
                </div>
                <div class="form-group col-sm-3">
                    <label>Monto</label>
                    <input type="number" name="monto" id="monto" class="form-control" step="0.001" value="<?php echo number_format($saldo, 0, '', ''); ?>" placeholder="Ingrese el Monto">
                    <input type="hidden" id="saldoTotal" value="<?php echo $saldo ?>" >
                </div>
                <div class="form-group col-sm-2" style="display: flex; align-items: end;">
                    <button type="button" class="btn btn-primary btn-block" id="agregar-pago-btn">Agregar</button>
                </div>
            </div>
        </form>
        <br>
        <div id="nota_credito_form" style="display: none;">
            <h3 id="credito-form">Nota de Crédito</h3><br>
            <form method="post">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-2" style="width: 10%;">
                            <label>Metodo</label>
                            <select class="form-control" id="metodo_nc">
                                <?php foreach ($this->metodo->Listar() as $m) : ?>
                                    <option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Deuda</label>
                            <select name="nota_credito" class="form-control select-nota-credito" id="nota_credito">
                                <?php foreach ($this->deuda->listar_cliente_deuda($cliente) as $index => $d) : ?>
                                    <option id="id_deuda" value="<?php echo $d->id_deuda ?>" <?php if ($index === 0) echo 'selected'; ?>>
                                        <?php echo 'Deuda Nº: ' . $d->id_deuda ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Saldo</label>
                            <?php foreach ($this->deuda->listar_cliente_deuda($cliente) as $index => $d) : ?>
                                <?php if ($index === 0) : ?>
                                    <?php $saldo_actual = $this->deuda->Obtener($d->id_deuda); ?>
                                    <input type="text" name="saldo_credito" id="saldo_credito" class="form-control" value="<?php echo $saldo_actual->saldo; ?>" readonly>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-sm-2">
                            <label>Monto a descontar</label>
                            <input type="number" name="monto_credito" id="monto_credito" class="form-control" value="" placeholder="Ingrese el Monto">
                        </div>
                        <div class="col-sm-2">
                            <div style="height: 25px;"></div>
                            <input class="btn btn-primary" type="button" value="OK" id="pago-credito">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php endif ?>
    <br>

    <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">
        <thead>
            <tr style="background-color: #000; color:#fff">
                <th>Pago</th>
                <th>Moneda</th>
                <th>Cotización</th>
                <th>Deuda (si hay)</th>
                <th>Monto</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $sumaTotal = 0;
            foreach ($this->pago_tmp->ListarPagos() as $r) : ?>
                <tr class="click">
                    <td><?php echo $r->pago_info; ?></td>
                    <td><?php echo $r->moneda ?? 'GS'; ?></td>
                    <td>
                        <?php 
                        if (isset($r->cambio)) {
                            echo number_format($r->cambio, 0, ".", ".");
                        } else {
                            echo "1";
                        }
                        ?>
                    </td>
                    <td><?php echo $r->id_deuda != 0 ? 'Deuda Nº '.$r->id_deuda : ''; ?></td>
                    <td>
                        <?php 
                        if ($r->moneda == 'USD' || $r->moneda == 'RS') {
                            echo number_format($r->monto, 3, ".", ".");
                        } else {
                            echo number_format($r->monto, 0, ".", ".");
                        }
                        ?>
                    </td>
                    <td>
                        <a class="btn btn-danger eliminar" monto="<?php echo $r->monto; ?>" id_deuda="<?php echo $r->id_deuda; ?>" pago="<?php echo $r->pago; ?>" id_pago="<?php echo $r->id; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php 
                // Convertir a GS para el total usando la cotización específica del pago
                if ($r->moneda == 'GS') {
                    $sumaTotal += $r->monto;
                } else {
                    // Usar la cotización específica del pago si existe, sino usar la del cierre
                    $cotizacion_pago = isset($r->cambio) ? $r->cambio : 1;
                    if ($r->moneda == 'USD' && $cotizacion_pago == 1) {
                        $cotizacion_pago = $cambio->cot_dolar; // Fallback para compatibilidad
                    } elseif ($r->moneda == 'RS' && $cotizacion_pago == 1) {
                        $cotizacion_pago = $cambio->cot_real; // Fallback para compatibilidad
                    }
                    $sumaTotal += $r->monto * $cotizacion_pago;
                }
            endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #000; color:#fff">
                <th>Total cubierto (GS)</th>
                <th></th>
                <th></th>
                <th></th>
                <th><?php echo number_format($sumaTotal, 0, ".", "."); ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
<ul class="list-inline pull-right">
    <li><button type="button" class="default-btn prev-step">Anterior</button></li>
    <li><button type="button" class="default-btn next-step" id="btn-finalizar2" value="Finalizar">Finalizar</button></li>
</ul>

<?php include("view/crud-modal.php"); ?>
<script src="assets/admin/js/step.js"></script>
<script>
    $('#btn-finalizar2').on('click', function() {
        var btnFinalizar = this;

        $.ajax({
            url: '?c=pago_tmp&a=ObtenerPago',
            type: 'POST',
            data: {
                bandera: 1  // Importante: indicar que es para compras
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response); // Para debug
                if (response == 1) {
                    btnFinalizar.disabled = true;
                    btnFinalizar.value = 'Guardando, Espere...';
                    btnFinalizar.form.submit();
                } else {
                    alert("¡ Por favor, complete el pago !");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
                console.log('Respuesta completa:', jqXHR.responseText); // Para debug adicional
            }
        });
    });
    
    $(document).ready(function(){
        var id_cliente = $("#id_cliente").val();
        var url = "?c=deuda&a=NotaCredito&id_cliente=" + id_cliente;
        $.ajax({
            url: url,
            method: "POST",
            success: function(respuesta) {
                var data = JSON.parse(respuesta);
                if (data.length === 0) {
                    $("#nota_credito_form").hide();
                } else {
                    $("#nota_credito_form").show();
                }
            }
        });
        
        // Controlar la habilitación/deshabilitación del selector de moneda al cargar
        controlMonedaSelector();
        
        // Inicializar eventos de pagos
        reinicializarEventosPagos();
    });

    function controlMonedaSelector() {
        var metodoPago = $("#forma_pago_tmp").val();
        var monedaSelect = $("#moneda_pago");
        var cambioInput = $("#cambio_pago");
        var cot_dolar = $("#cot_dolar").val();
        var cot_real = $("#cot_real").val();
        
        if (metodoPago === "Efectivo") {
            // Habilitar selector de moneda para Efectivo
            monedaSelect.prop('disabled', false);
            cambioInput.prop('disabled', false);
            
            // Establecer cotización según la moneda seleccionada (sin decimales)
            var moneda = monedaSelect.val();
            if (moneda === 'USD') {
                cambioInput.val(Math.round(parseFloat(cot_dolar)));
            } else if (moneda === 'RS') {
                cambioInput.val(Math.round(parseFloat(cot_real)));
            } else {
                cambioInput.val('1');
            }
        } else {
            // Deshabilitar selector y fijar en GS para otros métodos
            monedaSelect.val('GS');
            monedaSelect.prop('disabled', true);
            cambioInput.val('1');
            cambioInput.prop('disabled', true);
            
            // Actualizar el monto al cambiar a GS
            var monto_saldo_gs = $("#monto_saldo").val();
            $("#monto").val(Math.round(monto_saldo_gs));
        }
    }

    // Evento para controlar el selector de moneda cuando cambia el método de pago
    $('#forma_pago_tmp').on('change', function() {
        controlMonedaSelector();
    });

    $('.select-nota-credito').on('change', function() {
        var id_deuda = $(this).val();
        $.ajax({
            url: '?c=compra&a=ObtenerSaldo',
            type: 'POST',
            data: {id_deuda: id_deuda},
            dataType: 'json',
            success: function(response) {
                $('#saldo_credito').val(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
            }
        });
    });
    
    // Cambio de moneda actualiza el monto y cotización
    $('#moneda_pago').on('change', function() {
        var valor = $(this).val();
        var metodoPago = $("#forma_pago_tmp").val();
        var monto_saldo_gs = $("#monto_saldo").val();
        var cot_dolar = $("#cot_dolar").val();
        var cot_real = $("#cot_real").val();

        // Solo aplicar conversiones si el método es Efectivo
        if (metodoPago === "Efectivo") {
            if (valor == "USD") {
                // Actualizar cotización a USD (sin decimales) y convertir monto
                $("#cambio_pago").val(Math.round(parseFloat(cot_dolar)));
                var monto_usd = monto_saldo_gs / cot_dolar;
                $("#monto").val(monto_usd.toFixed(3));
            } else if (valor == "RS") {
                // Actualizar cotización a RS (sin decimales) y convertir monto
                $("#cambio_pago").val(Math.round(parseFloat(cot_real)));
                var monto_rs = monto_saldo_gs / cot_real;
                $("#monto").val(monto_rs.toFixed(3));
            } else {
                // GS - cotización 1 y mantener el valor original
                $("#cambio_pago").val('1');
                $("#monto").val(Math.round(monto_saldo_gs));
            }
        } else {
            // Para otros métodos, siempre mantener cotización 1 y valor en GS
            $("#cambio_pago").val('1');
            $("#monto").val(Math.round(monto_saldo_gs));
        }
    });

    // Evento para recalcular el monto cuando cambie la cotización manualmente
    $('#cambio_pago').on('input', function() {
        var metodoPago = $("#forma_pago_tmp").val();
        var moneda = $("#moneda_pago").val();
        var cotizacion = parseFloat($(this).val()) || 1;
        var monto_saldo_gs = $("#monto_saldo").val();

        // Solo recalcular si es efectivo y no es GS
        if (metodoPago === "Efectivo" && moneda !== "GS") {
            var monto_convertido = monto_saldo_gs / cotizacion;
            $("#monto").val(monto_convertido.toFixed(3));
        }
    });

    $('#agregar-pago-btn').on('click', function() {
        var pago = $("#forma_pago_tmp").val();
        var monto = parseFloat($("#monto").val());
        var moneda = $("#moneda_pago").val();
        var cambio = $("#cambio_pago").val();
        var saldo = parseFloat($("#saldoTotal").val());
        var monto_total = parseInt($("#monto_total").val());
        var id_cliente = $("#id_cliente").val();
        var bandera = 1;
        var cot_real = parseFloat($("#cot_real").val());
        var cot_dolar = parseFloat($("#cot_dolar").val());
        
        // Convertir monto a GS para validación usando la cotización especificada
        var monto_gs;
        if (pago === "Efectivo") {
            // Para efectivo usar la cotización especificada por el usuario
            if (moneda == 'USD' || moneda == 'RS') {
                monto_gs = monto * parseFloat(cambio);
            } else {
                monto_gs = monto;
            }
        } else {
            // Para otros métodos, la moneda es siempre GS y cotización = 1
            monto_gs = monto;
        }

        if (monto_gs > saldo + 1) {
            alert('El monto no puede ser mayor al saldo.');
            $('#monto').val('');
            return false;
        }

        if(monto > 0){
            var url = "?c=pago_tmp&a=guardar&pago=" + pago + "&monto=" + monto + "&moneda=" + moneda + "&cambio=" + cambio + "&bandera=" + bandera + "&id_cliente=" + id_cliente;
            
            $.ajax({
                url: url,
                method: "POST",
                data: {pago: pago, monto: monto, moneda: moneda, cambio: cambio, bandera: bandera, id_cliente: id_cliente},
                success: function(respuesta) {
                    // Determinar si estamos en contexto de edición o normal
                    var contenedorPagos = $("#pagos_compras_edicion").length > 0 ? "#pagos_compras_edicion" : "#pagos_compras";
                    $(contenedorPagos).html(respuesta);
                    $("#monto").focus();
                    // Reinicializar los controles después de actualizar
                    controlMonedaSelector();
                }
            });
        } else {
            alert("No se puede poner un valor 0 o negativo.");
            $('#monto').val('');
        }
    });

    $('#pago-credito').on('click', function() {
        
        var pago = $("#metodo_nc").val();
        var id_deuda = $("#nota_credito").val();
        var total = $("#monto_total").val();
        var id_cliente = $("#id_cliente").val();
        var monto = $("#monto_credito").val();
        var saldoTotal = parseInt($("#saldoTotal").val());
        var bandera = 1;

        var url = "?c=pago_tmp&a=guardar&pago=" + pago + "&monto=" + monto + "&bandera=" + bandera + "&id_cliente=" + id_cliente + "&id_deuda=" + id_deuda;
        var url_saldo = "?c=pago_tmp&a=SaldoReal&monto=" + monto + "&id_deuda=" + id_deuda;
        var url_buscar = "?c=pago_tmp&a=BuscarNota&id_deuda=" + id_deuda + "&metodo=" +pago;

        var montoADescontar = parseInt(monto);
        var total_entero = parseInt(total);
        var saldo = parseInt($("#saldo_credito").val());
        if(id_deuda != null){
            $.ajax({
                url: url_buscar,
                method: "POST",
                success: function(respuesta) {
                    console.log(respuesta);
                    if(respuesta == 1){
                        alert('Ya esta cargado la nota de credito con su metodo');
                    }else{
                        if(monto <= saldoTotal){
                            if (monto <= 0) {
                                alert("No se puede poner 0 o negativo.");
                                $('#monto_credito').val('');
                            }else if (montoADescontar > saldo || montoADescontar > total) {
                                alert("El monto a descontar no puede ser mayor que el saldo.");
                            } else {
                                $.ajax({
                                    url: url,
                                    method: "POST",
                                    data: {pago: pago, monto: monto, bandera: bandera, id_cliente: id_cliente, id_deuda: id_deuda, moneda: 'GS'},
                                    success: function(respuesta) {
                                        // Determinar si estamos en contexto de edición o normal
                                        var contenedorPagos = $("#pagos_compras_edicion").length > 0 ? "#pagos_compras_edicion" : "#pagos_compras";
                                        $(contenedorPagos).html(respuesta);
                                        $('.selectpicker').selectpicker();
                                        // Reinicializar eventos después de la actualización AJAX
                                        reinicializarEventosPagos();

                                        $.ajax({
                                            url: url_saldo,
                                            method: "POST",
                                            success: function(respuesta) {
                                                $("#saldo_credito").val(respuesta); 
                                                $("#nota_credito").val(id_deuda); 
                                            }
                                        });
                                    }
                                });
                            }
                        }else{
                            alert("Los valores superan al monto total");
                            $('#monto_credito').val('');
                        }
                    }
                }
            });
        }else{
            alert('Ingrese una nota de crédito válida');
        }

    });

    // Función para reinicializar eventos después de la actualización AJAX
    function reinicializarEventosPagos() {
        // Reinicializar eventos para botones eliminar que se crearon dinámicamente
        $('.eliminar').off('click').on('click', function() {
            var id = $(this).attr("id_pago");
            var pago = $(this).attr("pago");
            var id_deuda = $(this).attr("id_deuda");
            var id_cliente = $("#id_cliente").val();
            var monto = $(this).attr("monto");
            var bandera = 1;
            var url = "?c=pago_tmp&a=eliminar&id=" + id + "&bandera=" + bandera + "&id_cliente=" + id_cliente;

            var saldo_sumar = "?c=pago_tmp&a=SaldoReal&id_deuda=" + id_deuda;
            console.log('Saldo sumar: '+saldo_sumar);
            $.ajax({
                url: url,
                method: "POST",
                data: {id: id, bandera: bandera, id_cliente: id_cliente},
                success: function(respuesta) {
                    // Determinar si estamos en contexto de edición o normal
                    var contenedorPagos = $("#pagos_compras_edicion").length > 0 ? "#pagos_compras_edicion" : "#pagos_compras";
                    $(contenedorPagos).html(respuesta);
                    // Reinicializar los controles después de actualizar
                    controlMonedaSelector();
                    // Reinicializar eventos después de la actualización AJAX
                    reinicializarEventosPagos();

                    $.ajax({
                        url: saldo_sumar,
                        method: "POST",
                        success: function(respuesta) {
                            console.log('Actualiza con este valor: '+respuesta);
                            $("#saldo_credito").val(respuesta); 
                            $("#nota_credito").val(id_deuda); 
                        }
                    });
                }
            });
        });
    }
</script>
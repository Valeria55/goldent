<?php
session_start();
$cambio = $this->cierre->Ultimo(); // Obtener las cotizaciones del último cierre
$monto_venta = $this->venta_tmp->Obtener();
$pagototal = 0;
$pago_gs = 0;

// Calcular el total pagado convertido a GS usando la cotización específica de cada pago
foreach ($this->pago_tmp->Listar() as $monto_pago) :
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

// El saldo está en Guaraníes (monto_venta->monto ya está en GS)
$saldo = $monto_venta->monto - $pagototal;

if ($saldo < 0.5) {
    $saldo = 0;
}
?>
<div class="form-group col-sm-12" id="banco" style="display: none;">
    <label>Banco</label>
    <input type="text" name="banco" class="form-control" placeholder="Ingrese nombre de banco">
</div>
<input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
<input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
<input type="hidden" name="cot_dolar" id="cot_dolar" value="<?php echo $cambio->cot_dolar ?>">
<input type="hidden" name="cot_real" id="cot_real" value="<?php echo $cambio->cot_real ?>">
<input type="hidden" name="descuentoval" id="descuentoval" value="0">
<input type="hidden" name="ivaval" id="ivaval" value="0">
<input type="hidden" name="id_vendedor" value="12">
<input type="hidden" id="sub" value="<?php echo $monto_venta->monto ?>">

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

<?php if ($saldo == 0) : ?>
    <div align="center">
        <input type="submit" class="btn btn-primary" value="Finalizar venta" onclick="this.disabled=true;this.value='Guardando, Espere...';this.form.submit();">
    </div>
<?php endif ?>
<div align="center" style="display:none" id="fin">
    <input type="submit" class="btn btn-primary" value="Finalizar venta" onclick="this.disabled=true;this.value='Guardando, Espere...';this.form.submit();">
</div>
</form>
<br>

<div id="creditos">
    <center><h3>Pagos</h3></center>
    <?php if ($saldo != 0) : ?>
        <form method="POST" id="pago_frm">
            <div class="row">
                <div class="form-group col-sm-3">
                    <label>Método de pago</label>
                    <select name="pago" class="form-control" id="pago" required>
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
                </div>

                <div class="form-group col-sm-2" style="display: flex; align-items: end;">
                    <input class="btn btn-primary btn-block" type="submit" value="Agregar" style="margin-top: 25px;">
                </div>

                <input type="hidden" id="monto_saldo" value="<?php echo $saldo; ?>">
                <input type="hidden" name="descuento" id="descuento_tmp" value="0">
            </div>
        </form>
    <?php endif ?>
    <br>
    
    <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">
        <thead>
            <tr style="background-color: #000; color:#fff">
                <th>Pago</th>
                <th>Moneda</th>
                <th>Cotización</th>
                <th>Monto</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->pago_tmp->Listar() as $r) : ?>
                <tr class="click">
                    <td><?php echo $r->pago; ?></td>
                    <td><?php echo $r->moneda; ?></td>
                    <td>
                        <?php 
                        if (isset($r->cambio)) {
                            echo number_format($r->cambio, 0, ".", ".");
                        } else {
                            echo "1";
                        }
                        ?>
                    </td>
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
                        <a class="btn btn-danger eliminar" id_pago="<?php echo $r->id; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #000; color:#fff">
                <th>Total cubierto (GS)</th>
                <th></th>
                <th></th>
                <th><?php echo number_format($pagototal, 0, ".", "."); ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<?php include("view/crud-modal.php"); ?>

<script>
$(document).ready(function() {
    $(window).keydown(function(event){
        if(event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });
    
    // Controlar la habilitación/deshabilitación del selector de moneda
    controlMonedaSelector();
});

function controlMonedaSelector() {
    var metodoPago = $("#pago").val();
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

$('#pago_frm').on('submit', function(e) {
    e.preventDefault();
    var pago = $("#pago").val();
    var monto = $("#monto").val();
    var moneda = $("#moneda_pago").val();
    var cambio = $("#cambio_pago").val();
    var descuento = $("#descuento_tmp").val();
    var saldo = parseFloat($("#monto_saldo").val());

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
        return false;
    }
    
    var url = "?c=pago_tmp&a=guardar";
    
    $.ajax({
        url: url,
        method: "POST",
        data: {
            pago: pago, 
            monto: monto, 
            moneda: moneda, 
            cambio: cambio,
            descuento: descuento,
            bandera: 0
        },
        success: function(respuesta) {
            $("#pagos").html(respuesta);
            $("#monto").focus();
            if (typeof $('.selectpicker').selectpicker === 'function') {
                $('.selectpicker').selectpicker('refresh');
            }
            // Reinicializar los controles después de actualizar
            controlMonedaSelector();
        },
        error: function(xhr, status, error) {
            console.log('Error: ' + error);
            alert('Error al guardar el pago. Intente nuevamente.');
        }
    });
});

$(document).on('click', '.eliminar', function() {
    var id = $(this).attr("id_pago");
    var url = "?c=pago_tmp&a=eliminar";

    $.ajax({
        url: url,
        method: "POST",
        data: {
            id: id,
            bandera: 0
        },
        success: function(respuesta) {
            $("#pagos").html(respuesta);
            if (typeof $('.selectpicker').selectpicker === 'function') {
                $('.selectpicker').selectpicker('refresh');
            }
            // Reinicializar los controles después de actualizar
            controlMonedaSelector();
        },
        error: function(xhr, status, error) {
            console.log('Error: ' + error);
            alert('Error al eliminar el pago. Intente nuevamente.');
        }    });
});

// Evento para controlar el selector de moneda cuando cambia el método de pago
$('#pago').on('change', function() {
    controlMonedaSelector();
});

$('#moneda_pago').on('change', function() {
    var valor = $(this).val();
    var metodoPago = $("#pago").val();
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
    var metodoPago = $("#pago").val();
    var moneda = $("#moneda_pago").val();
    var cotizacion = parseFloat($(this).val()) || 1;
    var monto_saldo_gs = $("#monto_saldo").val();

    // Solo recalcular si es efectivo y no es GS
    if (metodoPago === "Efectivo" && moneda !== "GS") {
        var monto_convertido = monto_saldo_gs / cotizacion;
        $("#monto").val(monto_convertido.toFixed(3));
    }
});
</script>
<?php
$id_compra = isset($_GET['id_compra']) ? $_GET['id_compra'] : 0;
$monto_tmp = $this->compra_tmp->ObtenerMonto($id_compra);
$subtotal = $monto_tmp ? $monto_tmp->monto : 0;

// Obtener los datos actuales de la compra para pre-llenar el formulario
$compra_actual = null;
if ($id_compra > 0) {
    $compra_actual = $this->compra->ObtenerCompra($id_compra);
    
    // Cargar los egresos (pagos) existentes de la compra en pago_tmp
    $this->pago_tmp->CargarEgresosDeCompra($id_compra);
}
?>
<link rel="stylesheet" href="assets/admin/css/step.css">

<hr>
<section class="signup-step-container">
    <div class="container" style="width:100%;">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12">
                <div class="wizard">
                    <div class="wizard-inner">
                        <div class="connecting-line"></div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#step1-edicion" data-toggle="tab" aria-controls="step1-edicion" role="tab" aria-expanded="true"><span class="round-tab">1 </span> <i>Detalle de la compra editada</i></a>
                            </li>
                            <li role="presentation" class="disabled">
                                <a href="#step2-edicion" data-toggle="tab" aria-controls="step2-edicion" role="tab"><span class="round-tab">2</span> <i>Pago</i></a>
                            </li>
                        </ul>
                    </div>

                    <form method="post" action="?c=compra&a=actualizar_compra&id_compra=<?php echo $id_compra; ?>" class="login-box">
                        <input type="hidden" name="id_compra" value="<?php echo $id_compra; ?>">
                        <div class="tab-content" id="main_form_edicion">
                            <div class="tab-pane active" role="tabpanel" id="step1-edicion">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php if ($compra_actual): ?>
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> 
                                            <strong>Datos actuales de la compra:</strong> Los siguientes campos contienen la información actual de la compra. Puede modificarlos si es necesario.
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="form-group col-sm-4" id="nro_comprobante">
                                            <label>Fecha de compra</label>
                                            <input type="datetime-local" name="fecha_compra" class="form-control" value="<?php echo $compra_actual ? date('Y-m-d\TH:i', strtotime($compra_actual->fecha_compra)) : date("Y-m-d\TH:i"); ?>">
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label>Proveedor</label>
                                            <select name="id_cliente" id="id_cliente_edicion" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control">
                                                <option value="0">Proveedor sin nombre</option>
                                                <?php foreach($this->cliente->Listar() as $clie): ?> 
                                                <option value="<?php echo $clie->id; ?>" <?php echo ($compra_actual && $compra_actual->id_cliente == $clie->id) ? 'selected' : ''; ?>><?php echo $clie->nombre." ( ".$clie->ruc." )"; ?> </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>  

                                        <div class="form-group col-sm-4">
                                            <label>Comprobante</label>
                                            <select name="comprobante" id="comprobante_edicion" class="form-control">
                                                <option value="Ticket" <?php echo ($compra_actual && $compra_actual->comprobante == 'Ticket') ? 'selected' : ''; ?>>Ticket</option>
                                                <option value="Factura" <?php echo ($compra_actual && $compra_actual->comprobante == 'Factura') ? 'selected' : ''; ?>>Factura</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-sm-4" id="nro_comprobante">
                                            <label>Nro. comprobante</label>
                                            <input type="text" name="nro_comprobante" class="form-control" placeholder="Ingrese el nro de comprobante" value="<?php echo $compra_actual ? htmlspecialchars($compra_actual->nro_comprobante) : ''; ?>">
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label>Pago</label>
                                            <select name="contado" id="contado_edicion" class="form-control"> 
                                                <option value="Contado" <?php echo ($compra_actual && $compra_actual->contado == 'Contado') ? 'selected' : ''; ?>>Contado</option>
                                                <option value="Credito" <?php echo ($compra_actual && $compra_actual->contado == 'Credito') ? 'selected' : ''; ?>>Crédito</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-sm-4" id="plazo_edicion" style="<?php echo ($compra_actual && $compra_actual->contado == 'Credito') ? 'display: block;' : 'display: none;'; ?>">
                                            <label>Plazo</label>
                                            <input type="date" name="plazo" class="form-control" placeholder="Ingrese el plazo" value="">
                                        </div>

                                        <div class="form-group col-sm-4" id="entrega_edicion" style="<?php echo ($compra_actual && $compra_actual->contado == 'Credito') ? 'display: block;' : 'display: none;'; ?>">
                                            <label>Entrega</label>
                                            <input type="number" name="entrega" min="0" max="<?php echo $subtotal ?>" class="form-control" value="0" placeholder="Ingrese entrega">
                                        </div>

                                        <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
                                        <input type="hidden" name="total" class="totaldesc" id="totaldesc_edicion" value="<?php echo $subtotal ?>">
                                        <input type="hidden" name="descuentoval" id="descuentoval_edicion" value="0">
                                        <input type="hidden" name="ivaval" id="ivaval_edicion" value="0">
                                        <input type="hidden" name="id_vendedor" value="<?php echo $_SESSION['user_id']; ?>">
                                    </div>
                                </div>
                                <ul class="list-inline pull-right">
                                    <li><button type="button" class="default-btn next-step" id="btn-siguiente-edicion">
                                        <span id="texto-boton-siguiente">Siguiente</span>
                                    </button></li>
                                </ul>
                            </div>
                            
                            <input type="hidden" name="pago" value="5">
                            <div class="tab-pane" role="tabpanel" id="step2-edicion">
                                <div class="col-md-12" id="pagos_compras_edicion">
                                    <?php require_once 'view/pago_tmp/pago_compra_tmp.php'; ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/admin/js/step.js"></script>
<script>
    // Manejar cambios en contado/crédito
    $('#contado_edicion').on('change',function(){
        var valor = $(this).val();
        var textoBoton = $('#texto-boton-siguiente');
        
        if (valor == "Contado") {
            $("#plazo_edicion").hide();
            $("#entrega_edicion").hide();
            textoBoton.text("Siguiente");
        }else{
            $("#plazo_edicion").show();
            $("#entrega_edicion").show();
            textoBoton.text("Finalizar compra a crédito");
        }
    });

    // Personalizar el comportamiento del step para la edición
    $(document).ready(function () {
        $("#btn-siguiente-edicion").show();
        
        // Inicializar selectpicker para el select de proveedores
        $('.selectpicker').selectpicker();
        
        // Configurar estado inicial de campos según tipo de pago cargado
        var contado_inicial = $('#contado_edicion').val();
        var textoBoton = $('#texto-boton-siguiente');
        
        if (contado_inicial == "Contado") {
            $("#plazo_edicion").hide();
            $("#entrega_edicion").hide();
            textoBoton.text("Siguiente");
        } else {
            $("#plazo_edicion").show();
            $("#entrega_edicion").show();
            textoBoton.text("Finalizar compra a crédito");
        }
        
        // Reemplazar el comportamiento del step.js para este modal específico
        $('#btn-siguiente-edicion').on('click', function() {
            var tipoContado = $('#contado_edicion').val();
            
            if (tipoContado === 'Credito') {
                // Si es crédito, procesar directamente sin ir al step 2
                var btnSiguiente = this;
                btnSiguiente.disabled = true;
                btnSiguiente.innerHTML = 'Procesando...';
                
                // Enviar el formulario directamente
                btnSiguiente.form.submit();
                
            } else {
                // Si es contado, ir al step 2 de pagos
                $('.nav-tabs li:first-child').removeClass('active');
                $('.nav-tabs li:last-child').addClass('active').removeClass('disabled');
                
                $('#step1-edicion').removeClass('active');
                $('#step2-edicion').addClass('active');
                
                // Reinicializar selectpickers que puedan estar en el step 2
                $('.selectpicker').selectpicker('refresh');
            }
        });
    });
</script>

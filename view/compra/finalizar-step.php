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
                                <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true"><span class="round-tab">1 </span> <i>Detalle de la compra</i></a>
                            </li>
                            <li role="presentation" class="disabled">
                                <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab"><span class="round-tab">2</span> <i>Pago</i></a>
                            </li>
                        </ul>
                    </div>

                    <form method="post" action="?c=compra&a=guardar" class="login-box">
                        <div class="tab-content" id="main_form">
                            <div class="tab-pane active" role="tabpanel" id="step1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group col-sm-4" id="nro_comprobante">
                                            <label>Fecha de compra</label>
                                            <input type="datetime-local" name="fecha_compra" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label>Proveedor</label>
                                            <select name="id_cliente" id="id_cliente" class="form-control selectpicker id_cliente" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione al proveedor --" style="width:100%;">
                                                <option value="0">Sin seleccionar</option>
                                                <?php foreach ($this->cliente->Listar() as $clie) : ?>
                                                    <option value="<?php echo $clie->id; ?>"><?php echo $clie->nombre . " ( " . $clie->ruc . " )"; ?> </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label>Comprobante</label>
                                            <select name="comprobante" id="comprobante" class="form-control">
                                                <option value="Ticket">Ticket</option>
                                                <option value="Factura">Factura</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-sm-3" id="nro_comprobante">
                                            <label>Nro. comprobante</label>
                                            <input type="text" name="nro_comprobante" class="form-control" placeholder="Ingrese el número">
                                        </div>

                                        <!-- <div class="form-group col-sm-2">
                                            <label>Forma de pago</label>
                                            <select name="pago" id="pago" class="form-control pago">
                                                <?php /*foreach ($this->metodo->Listar() as $m) : ?>
                                                    <option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
                                                <?php endforeach; */?>
                                            </select>
                                        </div> -->

                                        <div class="form-group col-md-3" id="caja_descontar" class="caja_descontar">
                                            <label>Caja a descontar</label>
                                            <select name="id_caja" id="id_caja" class="form-control">
                                                <?php if ($_SESSION['nivel'] ==1 || $_SESSION['nivel'] ==4) : //Ovidio solo puede descontar de tesoreria 
                                                ?>
                                                    <option value="3">Tesorería</option>
                                                    <option value="1">Caja chica</option>
                                                <?php else : ?>
                                                    <option value="1">Caja chica</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-sm-3">
                                            <label>Pago</label>
                                            <select name="contado" id="c" class="form-control contado">
                                                <option value="Contado">Contado</option>
                                                <option value="Credito">Crédito</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-sm-3" id="entrega" style="display: none;">
                                            <label>Entrega</label>
                                            <input type="number" name="entrega" id="entrega-credito" min="0" max="<?php echo $subtotal ?>" class="form-control" value="0" placeholder="Ingrese entrega">
                                        </div>

                                        <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
                                        <input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
                                        <input type="hidden" name="descuentoval" id="descuentoval" value="0">
                                        <input type="hidden" name="ivaval" id="ivaval" value="0">
                                        <input type="hidden" name="id_vendedor" value="12">
                                    </div>
                                </div>
                                <ul class="list-inline pull-right">
                                    <li><button type="button" class="default-btn next-step" id="btn-siguiente">Siguiente</button></li>
                                    <li><button type="button" class="default-btn next-step2" value="Finalizar" id="btn-finalizar">Finalizar</button></li>
                                </ul>
                            </div>
                            
                            <input type="hidden" name="pago" value="5">
                            <div class="tab-pane" role="tabpanel" id="step2">
                                <div class="col-md-12" id="pagos_compras">
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
    $('#btn-finalizar').on('click', function() {
        // var entrega = $("#entrega-credito").val();
        // if(entrega <= 0 || entrega === ''){
        //     alert("Por favor, ingrese un valor en el campo de entrega.");
        // } else {
            this.disabled = true;
            this.value = 'Guardando, Espere...';
            this.form.submit();
       // }
    });
    $(document).ready(function () {
        $("#btn-siguiente").show();
        $("#btn-finalizar").hide();
        
        $('#c').on('change', function() {
            var valor = $(this).val();
            if (valor == "Credito") {
                $("#entrega").show();
                $("#btn-siguiente").hide();
                $("#btn-finalizar").show();
            } else {
                $("#entrega").hide();
                $("#btn-siguiente").show();
                $("#btn-finalizar").hide();
            }
        }); 
    });
</script>
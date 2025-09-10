            <?php
            $caja_origen = $this->model->ObtenerBalance($_REQUEST['id_caja']);

            if (!($cierre = $this->cierre->Consultar($_SESSION['user_id'])) && $caja_origen->id == 1) {
                die("<h3>Por favor, realice una apertura de caja para poder efectuar esta transferencia.</h3>");
            }
            if (!($caja_origen->disponible > 0)) {
                die("<h3>No hay fondos.</h3>");
            }
            ?>

            <h3 class="page-header">
                Transferir de: <span class="" style="text-decoration: underline;"><?php echo $caja_origen->caja ?></span>
            </h3>
            <h4><b style="">Monto disponible:</b> Gs. <span><?php echo number_format($caja_origen->disponible, 0, ",", ".") ?></span></h4>
            <br>
            <form method="post" action="?c=caja&a=transferencia" enctype="multipart/form-data">
                <input type="hidden" name="c" value="caja" id="c" />

                <div class="form-group" style="display: none;">
                    <label>Origen</label>
                    <input type="text" class="form-control" readonly value="<?php echo $caja_origen->caja ?>">
                </div>
                <div class="form-group" style="display: none;">
                    <label>Monto disponible</label>
                    <input type="text" class="form-control" readonly value="Gs. <?php echo number_format($caja_origen->disponible, 0, ",", ".") ?>">
                </div>
                <input type="hidden" value="<?php echo $caja_origen->id ?>" id="id_emisor" name="id_emisor">

                <div class="form-group">
                    <label>Destino</label>
                    <select name="id_receptor" id="id_receptor" class="form-control">
                        <?php
                        foreach ($this->caja->Listar() as $r) :
                            if ($caja_origen->id == 1 && $r->id != 3) continue;
                            if ($r->id != $caja_origen->id) :
                        ?>
                                <option value="<?php echo $r->id ?>" id="<?php echo $r->id ?>"><?php echo $r->caja ?></option>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </select>
                </div>

                <div class="form-group" id="cajero_div" style="display: none;">
                    <label>Cajero <small>(usuarios con apertura de caja activa)</small></label>
                    <select name="id_cajero" id="id_cajero" disabled class="form-control" required>
                        <option value="">Seleccione</option>
                        <?php
                        foreach ($this->usuario->ListarUsuarios() as $r) :
                            $caja_abierta = ($this->cierre->Consultar($r->id));
                            if (!$caja_abierta) continue;
                        ?>
                            <option value="<?php echo $r->id ?>" id="<?php echo $r->id ?>"><?php echo $r->user; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>

                <?php if ($caja_origen->id == 3) : ?>
                    <div class="form-group" id="comprobante_div" style="">
                        <label>Comprobante de transferencia</label>
                        <input type="text" name="comprobante" id="comprobante" class="form-control" placeholder="Ingrese su comprobante" required>
                    </div>

                <?php endif; ?>


                <div class="form-group">
                    <label>Monto</label>
                    <input type="number" name="monto" step="any" value="0" max="<?php echo $caja_origen->disponible; ?>" min="0" class="form-control" placeholder="Ingrese su monto" required>
                </div>

                <hr />

                <div class="modal-footer">
                    <button class="btn btn-primary">Transferir</button>
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                </div>
            </form>

            <script>
                $("#id_receptor").change(function(e) {
                    // e.preventDefault();
                    if (parseInt($(this).val()) == 1) { // si es caja chica, mostrar cajero 
                        $("#id_cajero").attr("disabled", false);
                        $("#cajero_div").show();
                    } else {
                        $("#id_cajero").attr("disabled", true);
                        $("#cajero_div").hide();
                    }

                    if (parseInt($(this).val()) == 2) { // si es banco, mostrar comprobante 
                        $("#comprobante").attr("disabled", false);
                        $("#comprobante_div").show();
                    } else {
                        $("#comprobante").attr("disabled", true);
                        $("#comprobante_div").hide();
                    }
                });
                $("#id_emisor").change(function() {
                    var caja = $(this).val();
                    $("#" + caja).hide();
                });
            </script>
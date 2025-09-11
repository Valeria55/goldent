<div id="finalizarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <form method="post" action="?c=transferencia_producto&a=guardar" id="finalizar">
                    <h3 align="center">Datos de la transferencia</h3>

                    <div class="form-group col-sm-12" style="display:none;">
                        <label>Fecha de la venta</label>
                        <input type="datetime-local" name="fecha_transferencia_producto" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Destino de transferencia </label>
                        <select name="destino_transferencia" id="cliente" class="form-control selectpickerr" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el cliente --" autofocus require>
                            <option value="cent_inst_1" selected>GOLDENT</option>
                        </select>
                    </div>

                    <div align="center">
                        <input type="submit" class="btn btn-primary" value="Finalizar" onclick="this.disabled=true;this.value='Guardando, Espere...';this.form.submit();">
                    </div>

            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>

        </div>
    </div>
</div>

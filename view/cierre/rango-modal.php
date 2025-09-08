<div id="cajaModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="edit_form">  
                <form method="get">
                    <h1>Generar cierre de caja</h1>
                    <input type="hidden" name="c" value="cierre">
                    <input type="hidden" name="a" value="cierrepdf">
                    
                   <div class="form-group">
                        <label>Desde</label>
                        <input type="date" name="desde" value="<?php echo (isset($_GET['desde']))? $_GET['desde']:''; ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Hasta</label>
                        <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta']))? $_GET['hasta']:''; ?>" class="form-control" required>
                     </div>
                     <div class="form-group col-md-12">
                        <label>Usuario</label>
                        <select name="id_usuario" id="id_usuario" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                    title="-- Seleccione a la persona --" style="width:100%; display:0">
                            <?php foreach($this->usuario->Listar() as $clie): ?> 
                            <option value="<?php echo $clie->id; ?>"><?php echo $clie->user; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary">Generar</button>
                    </div>
                                    

                </form>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
        </div>
    </div>
</div>


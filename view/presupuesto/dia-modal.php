<div id="diaModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="edit_form">  
                <form method="get">
                    <h1>Generar informe del día</h1>
                    <input type="hidden" name="c" value="venta">
                    <input type="hidden" name="a" value="cierre">
                    <div class="form-group">
                        <label>Buscar día</label>
                        <input type="date" min="2020-11-01" max="<?php echo date("Y-m-d") ?>" name="fecha" class="form-control">
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

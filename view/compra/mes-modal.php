<div id="mesModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="edit_form">  
                <form method="get">
                    <h1>Generar informe mensual</h1>
                    <input type="hidden" name="c" value="compra">
                    <input type="hidden" name="a" value="compraMes">
                    <div class="form-group">
                        <label>Buscar mes</label>
                        <input type="month" name="fecha" class="form-control">
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

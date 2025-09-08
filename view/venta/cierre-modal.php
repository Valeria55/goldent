<div id="cierreModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="edit_form">  
                <form method="get">
                    <h1>Generar cierre de caja</h1>
                    <input type="hidden" name="c" value="cierre">
                    <input type="hidden" name="a" value="cierre">
                    <div class="form-group">
                        <label>Monto Efectivo(GS)</label>
                        <input type="number" value="0" name="monto_cierre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Monto Efectivo(USD)</label>
                        <input type="number" value="0" name="monto_dolares" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Monto Efectivo(RS)</label>
                        <input type="number" value="0" name="monto_reales" class="form-control" required>
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

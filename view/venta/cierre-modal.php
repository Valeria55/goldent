<div id="cierreModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="edit_form">                <form method="get">
                    <h1>Generar cierre de caja</h1>
                    <input type="hidden" name="c" value="cierre">
                    <input type="hidden" name="a" value="cierre">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Monto en Guaraníes (GS)</label>
                                <input type="number" step="0.01" value="0" name="monto_cierre" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Monto en Reales (RS)</label>
                                <input type="number" step="0.01" value="0" name="monto_cierre_rs" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Monto en Dólares (USD)</label>
                                <input type="number" step="0.01" value="0" name="monto_cierre_usd" class="form-control">
                            </div>
                        </div>
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

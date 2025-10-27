<!-- Modal Registrar Servicio -->
<div class="modal fade" id="modalRegistrarServicio" tabindex="-1" role="dialog" aria-labelledby="modalRegistrarServicioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRegistrarServicioLabel">Registrar Servicio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formRegistrarServicio">
          <div class="form-group">
            <label for="codigo">Código</label>
            <input type="text" class="form-control" id="codigo" name="codigo" readonly required>
          </div>
          <div class="form-group">
            <label for="servicio">Servicio</label>
            <input type="text" class="form-control" id="servicio" name="servicio" required>
          </div>
          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" form="formRegistrarServicio" class="btn btn-primary">Registrar</button>
      </div>
    </div>
  </div>
</div>

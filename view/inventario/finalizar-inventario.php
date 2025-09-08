<div id="finalizar" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=cierre_inventario&a=cierre" id="finalizarI">
					<h2 align="center">Justificación</h2>
					<input type="hidden" name="id_c" value="<?php echo $_REQUEST['id_c']; ?>">
					<div class="form-group">
						<label>Motivo del cambio</label>
						<input type="text" required class="form-control" id="concepto" name="motivo">
					</div>
					<div class="form-group">
						<label>Sobrante en caja</label>
						<input type="number" required class="form-control" id="sobrante_caja" name="sobrante_caja">
					</div>
					<p>
						Al finalizar, confirma que desea aplicar los cambios especificados en el inventario a la tabla de productos, esta operación no se puede deshacer.
					</p>
					<div class="text-right">
						<button class="btn btn-primary" id="guardar">Guardar</button>
					</div>
				</form>

			</div>
			<div class="modal-footer">
				<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
			</div>

		</div>
	</div>
</div>
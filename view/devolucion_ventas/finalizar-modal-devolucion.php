<div id="devolucionModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=devolucion_ventas&a=guardar" id="devolucionFinalizar">
					<h2 align="center">Motivo de devolución</h2>

					<input type="hidden" name="id_venta" value="<?php echo $id_venta ?>">
					<input type="hidden" name="id_producto" value="<?php echo $id_producto ?>">
					<input type="hidden" name="precio_costo" value="<?php echo $precio_costo ?>">
					<input type="hidden" name="precio_venta" value="<?php echo $precio_venta ?>">
					<input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
					<input type="hidden" name="descuento" value="<?php echo $descuento ?>">
					<input type="hidden" name="iva" value="<?php echo $iva ?>">
					<input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
					<input type="hidden" name="cantidad" value="<?php echo $cantidad ?>">
					<input type="hidden" name="margen_ganancia" value="<?php echo $margen_ganancia ?>">
					<input type="hidden" name="fecha_venta" value="<?php echo $fecha_venta ?>">
					<input type="hidden" name="metodo" value="<?php echo $metodo ?>">
					<input type="hidden" name="moneda" value="<?php echo $moneda ?>">
					<input type="hidden" name="cambio" value="<?php echo $cambio ?>">
					<input type="hidden" name="banco" value="0">
					<input type="hidden" name="contado" value="Efectivo">


					<!--<div class="form-group">
						<label>En forma de</label>
						<select name="contado" id="contado" class="form-control" > 
							<option value="Efectivo">Efectivo</option>
							<option value="Credito">Crédito</option>
						</select>
				    </div>-->


					<div class="form-group ">
						<label>Motivo</label>
						<textarea name="motivo" class="form-control"></textarea>
					</div>

					<input type="button" onclick="finalizarDevolucionVenta()" class="btn btn-primary" value="Finalizar devolución">


				</form>

			</div>
			<div class="modal-footer">
				<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
			</div>

		</div>
	</div>
</div>

<script>
	function finalizarDevolucionVenta() {
		var total = $("#total").text();
		Swal.fire({
			title: 'Desea confirmar la devolución?',
			icon: 'warning',
			html: `<p>Verifique si todos los datos están correctos antes de confirmar</p>
					<table class="table">
						<tbody>
							<tr>
							<th scope="row">Monto total</th>
							<td>${total}</td>
							</tr>
						</tbody>
					</table>
				`,
			showCancelButton: true,
			focusCancel: true,
			customClass: 'swal-lg',
			confirmButtonText: 'Finalizar',
			cancelButtonText: `Cancelar`,
			// timer: 3000, // para cerrar automaticamente
		}).then((result) => {
			/* Read more about isConfirmed, isDenied below */
			if (result.isConfirmed) {
				Swal.fire({
					title: 'Registrando devolución',
					html: 'Por favor, aguarde...',
					customClass: 'swal-lg',
					onOpen: () => {
						Swal.showLoading()
						$("#devolucionFinalizar").submit();
					}
				});
			} else if (result.isDenied) { // cancelado
			}
		})
	}

	$('#pago').on('change', function() {
		var valor = $(this).val();
		if (valor == "Transferencia" || valor == "Giro") {
			$("#banco").show();
		} else {
			$("#banco").hide();
		}
	});
	$('#contado').on('change', function() {
		var valor = $(this).val();
		if (valor == "Cuota") {
			$("#entrega").show();
		} else {
			$("#entrega").hide();
		}
	});
</script>
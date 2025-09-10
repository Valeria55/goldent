<div id="finalizarModal" class="modal fade bd-example-modal-lg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=venta&a=guardar" id="finalizar">
					<h3 align="center">Datos de venta</h3>

					<div class="form-group col-sm-12">
						<label>Fecha de la venta</label>
						<input type="datetime-local" name="fecha_venta" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
					</div>

					<?php $ven = $this->presupuesto->ObtenerId_presupuesto($r->id_presupuesto); ?>
					<input type="hidden" name="m" value="<?php echo $r->id_presupuesto ?>">

					<div class="form-group col-sm-12">
						<label>Cliente </label>
						<select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el cliente --" autofocus>
							<option value="0" selected>CLiente ocasional</option>
							<?php foreach ($this->cliente->Listar() as $cliente) : ?>
								<option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>" <?php echo ($ven->id_cliente == $cliente->id) ? "selected" : ""; ?>><?php echo $cliente->nombre . ' ' . $cliente->ruc; ?> </option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group col-sm-6" id="nro_comprobante">
						<label>Nro. comprobante</label>
						<input type="text" name="nro_comprobante" class="form-control" placeholder="Ingrese el nro de comprobante">
					</div>
					<div class="form-group col-sm-6">
						<label>Comprobante</label>
						<select name="comprobante" id="comprobante" class="form-control">
							<option value="Ticket">Ticket</option>
							<option value="TicketSi">Sin impresión</option>
							<option value="Factura">Factura</option>
						</select>
					</div>
					<div class="col-sm-5">
						<label>Gift Card</label>
						<select name="id_gift" id="id_gift" class="form-control " data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el Gift Card --" autofocus>
							<option value="" selected>Sin seleccionar</option>
							<?php foreach ($this->gift_card->ListarClientesSinAnular() as $gift) : ?>
								<option value="<?php echo $gift->id; ?>"><?php echo $gift->nombre . ' (' . $gift->nro_tarjeta; ?> )
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-sm-3">
						<label>Monto</label>
						<input type="number" id="monto_gift" class="form-control">
					</div>

					<div class="form-group col-sm-12" id="banco" style="display: none;">
						<label>Entidad</label>
						<input type="text" name="banco" class="form-control" placeholder="Ingrese nombre de la entidad">
					</div>
					<div class="form-group col-sm-12" id="cuotas" style="display: none;">
						<label>Cantidad de cuotas</label>
						<input type="number" name="cuotas" min="1" max="12" class="form-control" value="1" placeholder="Cantidad de cuotas">
					</div>
					<div class="form-group col-sm-4">
						<label>Formas de pago</label>
						<select name="contado" id="contado" class="form-control">
							<option value="Contado">Contado</option>
							<option value="Credito">Credito</option>
						</select>
					</div>
					<div class="col-sm-6" style="display: none;">
						<label>Entrega</label>
						<input type="number" name="entrega" min="0" max="<?php echo $subtotal ?>" class="form-control" value="0" placeholder="Ingrese entrega">
					</div>
					<div class="col-sm-6" style="display: none;" >
						<label>Forma de pago</label>
						<select name="forma_pago" class="form-control">
							<?php foreach ($this->metodo->Listar() as $m) : ?>
								<option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-sm-12">
						<label>Devoluciones</label>
						<select name="id_devolucion" id="id_devolucion" class="form-control " data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el Gift Card --" autofocus>
							<option value="0" selected>Sin seleccionar</option>
							<?php foreach ($this->devolucion_ventas->ListarId_venta() as $gift) : ?>
								<option value="<?php echo $gift->id_venta; ?>"><?php echo $gift->id_venta . ' ' . $gift->nombre . ' (' . $gift->ruc; ?> )
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<input type="hidden" name="pago" value="5">

					<?php if (!$tiene_descuento) : ?>
						<div class="form-group col-sm-12" id="descuento_input">
							<label>Descuento Final (%)</label>
							<input type="number" name="descuento_final" id="descuento_final" value="<?php echo $ven->descuento ?>" class="form-control" placeholder="Porcentaje de descuento" <?php //echo ($_SESSION['nivel']==1) ? "" : "readonly"; ?>>
						</div>
					<?php endif; ?>

					<div id="pagos">
						<?php require_once 'view/pago_tmp/pago_tmp.php'; ?>
					</div>

			</div>
			<div class="modal-footer">
				<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
			</div>

		</div>
	</div>
</div>

<script>
	$('#descuento_final').on('keyup', function(e) {
		e.preventDefault();
		var descuento_final = $("#descuento_final").val();
		var url = "?c=venta_tmp&a=agregarDescuento&descuento_final=" + descuento_final;
		$.ajax({

			url: url,
			method: "POST",
			success: function(respuesta) {
				$("#pagos").html(respuesta);
				$("#monto").focus();
				$('.selectpicker').selectpicker();
			}

		})
	});

	$('#id_gift').on('change', function() {
		var id = $(this).val();
		var url = "?c=gift_card&a=buscar&id=" + id;

		$.ajax({

			url: url,
			method: "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success: function(respuesta) {
				var gift = JSON.parse(respuesta);
				$("#monto_gift").val(gift.monto);
				$('.selectpicker').selectpicker();

			}

		})
	});
	$('#pago').on('change', function() {
		var valor = $(this).val();
		if (valor == "Transferencia" || valor == "Giro") {
			$("#banco").show();
		} else {
			$("#banco").hide();
		}
	});

	$('#monto_efectivo').on('keyup', function() {
		var valor = parseInt($(this).val());
		var total = $("#sub").val();
		var vuelto = valor - total;
		$("#vuelto").html((vuelto).toLocaleString('de-DE'));
	});

	$('#contado').on('change', function() {
		var valor = $(this).val();
		if (valor == "Credito") {
			$("#creditos").hide();
			$("#fin").show();
			$("#entrega").show();
			$("#forma_pago").show();

		} else {
			$("#creditos").show();
			$("#fin").hide();
			$("#entrega").hide();
			$("#forma_pago").hide();
		}
	});
</script>
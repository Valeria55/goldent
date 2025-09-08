  <style>
    @media (min-width: 768px) {
      .modal-lg {
        width: 80%;
       max-width:1500px;
      }
    }
</style>


<div id="finalizarModal" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=venta&a=guardar" id="finalizar">
					<h3 align="center">Datos de venta</h3>
<h5>
					<input type="hidden" name="cot_dolar" id="cot_dolar" class="form-control" value="<?php echo $cierre->cot_dolar_tmp; ?>">
					<input type="hidden" name="cot_real" id="cot_real" class="form-control" value="<?php echo $cierre->cot_real_tmp; ?>">

					<div class="form-group col-sm-12" >
						<label>Fecha de la venta</label>
						<input type="datetime-local" name="fecha_venta" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
					</div>

					<?php $ven = $this->presupuesto->ObtenerId_presupuesto($r->id_presupuesto);
					$v = $this->venta->ultimo();
					?>
					<input type="hidden" name="m" value="<?php echo $r->id_presupuesto ?>">

					<div class="form-group col-sm-12">
						<label>Cliente </label>
						<select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el cliente --" autofocus>
							<option value="0" selected>Cliente ocasional</option>
							<?php foreach ($this->cliente->Listar() as $cliente) : ?>
								<option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>" <?php echo ($ven->id_cliente == $cliente->id) ? "selected" : ""; ?>><?php echo $cliente->nombre . ' - ' . $cliente->ruc . ' - ' . $cliente->direccion; ?> </option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group col-sm-4" id="nro_comprobante">
						<label>Nro. comprobante</label>
						<input type="text" name="nro_comprobante" class="form-control" value="<?php echo $v->id_venta + 1; ?>">
					</div>
					<div class="form-group col-sm-4">
						<label>Comprobante</label>
						<select name="comprobante" id="comprobante" class="form-control">
							<!-- <option value="Ticket">Ticket</option> -->
							<option value="TicketSi">Sin impresión</option>
							<option value="Recibo">Recibo</option>
							<option value="Factura">Factura</option>
						</select>
					</div>
					<!--<div class="col-sm-5">-->
					<!--	<label>Gift Card</label>-->
					<!--	<select name="id_gift" id="id_gift" class="form-control " data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el Gift Card --" autofocus>-->
					<!--		<option value="" selected>Sin seleccionar</option>-->
					<!--		<?php //foreach ($this->gift_card->ListarClientesSinAnular() as $gift) : ?>-->
					<!--			<option value="<?php// echo $gift->id; ?>"><?php //echo $gift->nombre . ' (' . $gift->nro_tarjeta; ?> )-->
					<!--			</option>-->
					<!--		<?php// endforeach; ?>-->
					<!--	</select>-->
					<!--</div>-->
					<!--<div class="col-sm-3">-->
					<!--	<label>Monto</label>-->
					<!--	<input type="number" id="monto_gift" class="form-control" readonly autocomplete="off">-->
					<!--</div>-->

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

					<div class="col-sm-4" id="entrega" style="display: none;">
						<label>Entrega</label>
						<input type="number" name="entrega" min="0" max="<?php echo $subtotal ?>" class="form-control" value="0" placeholder="Ingrese entrega">
					</div>

					<div class="col-sm-4" style="display: none;" id="forma_pago">
						<label>Forma de pago</label>
						<select name="forma_pago" class="form-control">
							<?php foreach ($this->metodo->Listar() as $m) : ?>
								<option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="form-group col-md-2" style="display: none;" id="mon">
						<label>Moneda</label>
						<select name="moneda" id="moneda" class="form-control">
							<option value="GS">GS</option>
							<option value="USD">USD</option>
							<option value="RS">RS</option>
						</select>
					</div>


					<div class="form-group col-md-2" style="display: none;" id="can">
						<label style="color: black">Cambio</label>
						<input type="text" name="cambio" id="cambio" value="1" class="form-control" min="1">
					</div>


					<div class="col-sm-6">
						<label>Devoluciones</label>
						<select name="id_devolucion" id="id_devolucion" class="form-control " data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el Gift Card --" autofocus>
							<option value="" selected>Sin seleccionar</option>
							<?php foreach ($this->devolucion_ventas->ListarId_venta() as $gift) : ?>
								<option value="<?php echo $gift->id_venta; ?>"><?php echo $gift->id_venta . ' ' . $gift->nombre . ' (' . $gift->ruc; ?> )
								</option>
							<?php endforeach; ?>
						</select>
					</div>


					<div class="col-sm-6">
						<label>Monto</label>
						<input type="text" id="mon_dev" class="form-control" value="" readonly>
					</div>
					<input type="hidden" name="pago" value="5">
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
	$('#id_devolucion').on('change', function() {
		var id = $(this).val();
		var url = "?c=devolucion_ventas&a=buscar&id=" + id;

		$.ajax({

			url: url,
			method: "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success: function(respuesta) {
				var dev = JSON.parse(respuesta);
				var monto = dev.monto;
				console.log(monto);
				console.log((monto).toLocaleString("es-ES", {
					style: 'currency'
				}));
				$("#mon_dev").val((dev.monto).toLocaleString("es-ES", {
					style: 'currency',
					currency: 'PYG'
				}));
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
// 	$('#pago').on('change', function() {
// 		var valor = $(this).val();
// 		if (valor == "Transferencia" || valor == "Giro") {
// 			$("#banco").show();
// 		} else {
// 			$("#banco").hide();
// 		}
// 	});

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
			$("#can").show();
			$("#mon").show();

		} else {
			$("#creditos").show();
			$("#fin").hide();
			$("#entrega").hide();
			$("#forma_pago").hide();
			$("#can").hide();
			$("#mon").hide();
		}
	});


	$('#moneda').on('change', function() {
		var valor = $(this).val();

		var cot_gs = '1';
		var cot_real = <?php echo $cierre->cot_real_tmp; ?>;
		var cot_dol = <?php echo $cierre->cot_dolar_tmp; ?>;

		console.log($(this).val());
		if (valor == "GS") {
			$("#cambio").val(cot_gs);
			console.log('valor = ' + valor);

		} else if (valor == "RS") {
			$("#cambio").val(cot_real);
			console.log('valor = ' + valor);

		} else {
			$("#cambio").val(cot_dol);
			console.log('valor = ' + valor);
		}

	});
</script>
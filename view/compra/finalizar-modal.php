<div id="finalizarModal" class="modal fade -modal-lg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=compra&a=guardar">
					<div class="row">
						<div class="form-group col-md-12" id="nro_comprobante">
							<label>ID de la compra</label>
							<h4><?php echo $r->id_compra; ?></h4>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-12" id="nro_comprobante">
							<label>Fecha de compra</label>
							<input type="datetime-local" name="fecha_compra" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
						</div>
					</div>
					<div class="row">

						<div class="form-group col-md-12">
							<label>Proveedor</label>
							<select name="id_cliente" id="id_cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" autofocus="autofocus">
								<option value="0">Proveedor sin nombre</option>
								<?php foreach($this->cliente->Listar() as $clie): ?> 
								<option value="<?php echo $clie->id; ?>"><?php echo $clie->nombre." ( ".$clie->ruc." )"; ?> </option>
								<?php endforeach; ?>
							</select>
						</div>  

						<div class="form-group col-md-6">
							<label>Comprobante</label>
							<select name="comprobante" id="comprobante" class="form-control">
								<option value="Ticket">Ticket</option>
								<option value="Factura">Factura</option>
							</select>
						</div>

						<div class="form-group col-md-6" id="nro_comprobante">
							<label>Nro. comprobante</label>
							<input type="text" name="nro_comprobante" class="form-control" placeholder="Ingrese el nro de comprobante">
						</div>
					</div>
				<div class="row">
					<div class="form-group col-md-6">
						<label>Método de pago</label>
						<select name="pago" id="pago" class="form-control">
							<option value="Efectivo">Efectivo</option>
							<option value="Transferencia">Transferencia</option>
							<option value="Cheque">Cheque</option>
						</select>
					</div>
					<div class="form-group col-md-6" id="caja_descontar">
						<label>Caja a descontar</label>
						<select name="id_caja" id="id_caja" class="form-control">
							<option value="1">Caja chica</option>
							<option value="3">Tesorería</option>
						</select>
					</div>
					<div class="form-group" id="nro_cheque" style="display: none;">
						<label>Nro. Cheque</label>
						<input type="text" name="nro_cheque" class="form-control" placeholder="Ingrese el nro del cheque">
					</div>


				</div>

					<div class="row">
						<div class="form-group col-md-6">
							<label>Pago</label>
							<select name="contado" id="contado" class="form-control"> 
								<option value="Contado">Contado</option>
								<option value="Credito">Crédito</option>
							</select>
						</div>

						<div class="form-group col-md-6" id="plazo" style="display: none;">
							<label>Plazo</label>
							<input type="date" name="plazo" class="form-control" placeholder="Ingrese el plazo">
						</div>
					</div>
					<div class="row">
						<div class="form-group" id="entrega" style="display: none;">
							<label>Entrega</label>
							<input type="number" name="entrega" min="0" max="<?php echo $subtotal ?>" class="form-control" value="0" placeholder="Ingrese entrega">
						</div>
					</div>

					<input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
					<input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
					<input type="hidden" name="descuentoval" id="descuentoval" value="0">
					<input type="hidden" name="ivaval" id="ivaval" value="0">
					<input type="hidden" name="id_vendedor" value="12">

					<div class="form-group col-md-12">
						<center>
							<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
							<input type="submit" class="btn btn-primary" value="Finalizar compra">
						</center>
					</div>
				</form>

			</div>

			<div class="modal-footer">
			</div>

		</div>
	</div>
</div>

<script>
	
    $('#contado').on('change',function(){
		var valor = $(this).val();
		if (valor == "Contado") {
			$("#plazo").hide();
			$("#creditos").hide();
			// $("#fin").show();
			// $("#entrega").show();
			$("#forma_pago").show();
			
		}else{
			$("#plazo").show();
			$("#creditos").show();
			// $("#fin").hide();
			// $("#entrega").hide();
			$("#forma_pago").hide();
		}
	});

	$('#pago').on('change',function(){
		var valor = $(this).val();

		if(valor == "Efectivo"){
			$("#caja_descontar").show();
		}else{
			$("#caja_descontar").hide();
		}
	});

	$('#monto_efectivo').on('keyup', function() {
		var valor = parseInt($(this).val());
		var total = $("#sub").val();
		var vuelto = valor - total;
		$("#vuelto").html((vuelto).toLocaleString('de-DE'));
	});

	// $('#contado').on('change',function(){
	// 	var valor = $(this).val();
	// 	if (valor == "Cuota") {

	// 		$("#entrega").show();
	// 	}else{
	// 		$("#entrega").hide();
	// 	}
	// });
</script>
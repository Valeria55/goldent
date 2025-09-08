<div id="finalizarPresupuestoModal" class="modal fade bd-example-modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=presupuesto_compra&a=guardarcomocompra">
	    
					<h3 align="center">Datos del presupuesto de compra N° <?php echo $r->id_presupuesto; ?></h3>
					<div class="form-group col-sm-12" >
				        <label>Fecha de presupuesto de compra</label>
				        <input type="datetime-local" name="fecha_presupuesto" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
				    </div>
				    
					<div class="form-group col-sm-12">
						<label>Proveedor</label>
					    <select name="id_cliente" id="id_cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" autofocus="autofocus">
							<option value="0">Proveedor sin nombre</option>
				        	<?php foreach($this->cliente->Listar() as $clie): ?> 
				        	<option value="<?php echo $clie->id; ?>"><?php echo $clie->nombre." ( ".$clie->ruc." )"; ?> </option>
				        	<?php endforeach; ?>
				        </select>
					</div>
					<div class="form-group col-sm-4">
						<label>Producto</label>
						<select name="facturable" class="form-control">
							<option value="sin factura">Sin factura</option>
							<option value="con factura">Con factura</option>
						</select>
					</div>
					<div class="form-group col-sm-4">
						<label>Comprobante</label>
						<select name="comprobante" id="comprobante" class="form-control">
							<option value="Sin comprobante">Sin comprobante</option>
							<option value="Ticket">Ticket</option>
							<option value="Factura">Factura</option>
						</select>
					</div>

					<div class="form-group col-sm-6" id="nro_comprobante">
						<label>Nro. comprobante</label>
						<input type="text" name="nro_comprobante" class="form-control" placeholder="Ingrese el nro de comprobante">
					</div>	


					<div class="form-group col-sm-4">
						<label>Monto</label>
						<input type="float" name="monto" id="monto_finalizar" value="<?php echo number_format($subtotal, 2, ",", "."); ?>" class="form-control" readonly>
					</div>
					<div class="form-group col-sm-4">
						<label>Moneda</label>
						<select type="text" name="monedas" id="monedas" class="form-control">
							<option value="USD">USD</option>
							<option value="GS">GS</option>
							<option value="RS">RS</option>
						</select>
					</div>
					<div class="form-group col-sm-4">
						<label>Cambio</label>
						<input type="float" name="cambios" id="cambios" value="1" class="form-control" required>
					</div>
					<div class="form-group col-sm-6">
						<label>Forma de pago</label>
						<select name="pago" class="form-control">
							<?php foreach ($this->metodo->Listar() as $m) : ?>
								<option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group col-sm-6">
						<label>Pago</label>
						<select name="contado" id="contado" class="form-control">
							<option value="Contado">Contado</option>
							<option value="Credito">Crédito</option>
						</select>
					</div>

					<div class="form-group col-sm-12" id="entrega" style="display: none;">
						<label>Entrega</label>
						<input type="number" step="0.01" name="entrega" min="0" max="<?php echo $subtotal ?>" class="form-control" value="0" placeholder="Ingrese entrega">
					</div>
					<h3 align="center">Otros gastos</h3>
					<div class="form-group col-sm-4">
						<label>Concepto</label>
						<textarea name="otro_gasto" placeholder="Describa el concepto" class="form-control"></textarea>
					</div>
					<div class="form-group col-sm-3">
						<label>Total</label>
						<input type="float" name="monto_gasto" value="0" class="form-control">
					</div>
					<div class="form-group col-md-2" id="mon">
						<label>Moneda</label>
						<select name="moneda" id="moneda" class="form-control" required>
							<option value="USD">USD</option>
							<option value="GS">GS</option>
							<option value="RS">RS</option>
						</select>
					</div>

					<div class="form-group col-md-3" id="can">
						<label style="color: black">Cambio</label>
						<input type="text" name="cambio" id="cambio" value="1" class="form-control" min="1" required>
					</div>
					<div class="form-group col-sm-12">
						<label>SALIDA DE CAJA</label>
						<select name="cajas"  class="form-control"> 
							<option value="1">CAJA CHICA</option>
							<option value="3">CAJA GRANDE</option>
							<option value="2">CAJA BANCO</option>
						</select>
					</div>

					<div class="form-group col-sm-12" id="nro_cheque" style="display: none;">
						<label>Nro. Cheque</label>
						<input type="text" name="nro_cheque" class="form-control" placeholder="Ingrese el nro del cheque">
					</div>

					<div class="form-group col-sm-12" id="banco" style="display: none;">
						<label>Banco</label>
						<input type="text" name="banco" class="form-control" placeholder="Ingrese nombre de banco">
					</div>
					<div class="form-group col-sm-12" id="plazo" style="display: none;">
						<label>Plazo</label>
						<input type="date" name="plazo" class="form-control" placeholder="Ingrese el plazo">
					</div>



				    <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
				    <input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
					<input type="hidden" name="descuentoval" id="descuentoval" value="0">
					<input type="hidden" name="ivaval" id="ivaval" value="0">
					<input type="hidden" name="id_vendedor" value="12">
					<center><input type="submit" class="btn btn-primary" value="Finalizar presupuesto"></center>
				</form>

            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
            
        </div>
    </div>
</div>

<script>
	/*$('#pago').on('change',function(){
		var valor = $(this).val();
		if (valor == "Cheque") {
			$("#banco").show();
			$("#plazo").show();
			$("#nro_cheque").show();
		}else{
			$("#banco").hide();
			$("#plazo").hide();
			$("#nro_cheque").hide();
		}
	});*/
	
	$('#contado').on('change',function(){
		var valor = $(this).val();
		if (valor == "Credito") {
			$("#entrega").show();
		}else{
			$("#entrega").hide();
		}
	});

	$('#moneda').on('change',function(){
		var valor = $(this).val();
		
		var cot_dol = '1';
		var cot_real = <?php echo $cierre->cot_real_tmp; ?>;
		var cot_gs = <?php echo $cierre->cot_dolar_tmp; ?>;
		
		console.log($(this).val());
		if (valor == "GS") {
			$("#cambio").val(cot_gs);
			console.log('valor = '+ valor);
			
		}else if(valor == "RS"){
			$("#cambio").val(cot_real);
			console.log('valor = '+ valor);

		}else{
			$("#cambio").val(cot_dol);
			console.log('valor = '+ valor);
		}

	});
		$('#monedas').on('change',function(){
		var valor = $(this).val();
		
		var cot_dol = '1';
		var cot_real = <?php echo $cierre->cot_real_tmp; ?>;
		var cot_gs = <?php echo $cierre->cot_dolar_tmp; ?>;
		
		console.log($(this).val());
		if (valor == "GS") {
			$("#cambios").val(cot_gs);
			console.log('valor = '+ valor);
			
		}else if(valor == "RS"){
			$("#cambios").val(cot_real);
			console.log('valor = '+ valor);

		}else{
			$("#cambios").val(cot_dol);
			console.log('valor = '+ valor);
		}

	});
</script>
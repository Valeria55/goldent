<div id="finalizarModal" class="modal fade bd-example-modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=presupuesto_compra&a=guardar">
	    
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

					<div class="form-group col-sm-6">
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

				    <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
				    <input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
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
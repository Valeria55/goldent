	<?php 
    $cierre = $this->cierre->ObtenerCierre($_SESSION['user_id']);  
	?>
<form method="post">
	
		    	<input type="hidden" name="c" value="deuda">
		    	<input type="hidden" name="a" value="Cobrar">
		    	<input type="hidden" name="id" value="<?php echo $r->id ?>">
		    	<input type="hidden" name="id_cliente" value="<?php echo $r->id_cliente ?>">
				
				<?php $d = $this->model->ObtenerTodos($r->id_cliente);?>

		    	<input type="hidden" name="id_venta" value="<?php echo $r->id_venta ?>">
		    	<input type="hidden" name="cli" value="<?php echo $r->nombre ?>">
		    	<input type="hidden" name="moneda_actual" value="<?php echo $r->moneda ?>">

		    	<h3>Cobro por <?php echo $r->concepto ?></h3>
		    	<br>
		    	<h4>Saldo: <?php echo number_format($d->saldototal,0, ".", "."); echo ' '.$r->moneda ; ?></h4>
		    	<br>
		    	<div class="form-group col-md-12">
		        	<label>Monto</label>
		        	<input type="number" step="0.01" name="mon" min="0" class="form-control">
		    	</div>

				<div class="form-group col-md-6" id="mon">
					<label>Moneda</label>
					<select name="moneda" id="moneda"  class="form-control">
						<option value="GS">GS</option>
						<option value="USD">USD</option>
						<option value="RS">RS</option>
					</select>
				</div>

				<div class="form-group col-md-6" id="can">
					<label style="color: black">Cambio</label>
					<input type="text" name="cambio" id="cambio" value="1" class="form-control" min="1">
				</div>
				
                <div class="form-group col-md-12">
                    <label>Forma de pago</label>
                    <select name="forma_pago" class="form-control" >
                      <?php foreach ($this->metodo->Listar() as $m): ?>
                         <option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
                     <?php endforeach; ?>
                    </select>
                </div>		    
		    
				<div class="form-group col-sm-12">
					<label>Comprobante</label>
					<select name="comprobante" id="comprobante" class="form-control">
						<option value="Sin comprobante">Sin comprobante</option>
						<option value="Ticket">Ticket</option> 
						<option value="Factura">Factura</option> 
					</select>
				</div>
				<div id="nro_comprobante" class="form-group col-md-12">
		        	<label>Nro. Comprobante</label>
		        	<input type="text" name="nro_comprobante"  class="form-control" value="">
		    	</div>
				<br>
		    	<div class="form-group">
		        	<input type="submit" value="cobrar" class="btn btn-primary">
		    	</div>
			
          	</form>

<script>
	$('#moneda').on('change',function(){
		var valor = $(this).val();
		
		var cot_dol = <?php echo $cierre->cot_dolar_tmp; ?>;
		var cot_real = <?php echo $cierre->cot_real_tmp; ?>;
		var cot_gs = '1';
		
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
</script>
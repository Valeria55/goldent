<div id="devolucionModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=devolucion_compras&a=guardar" id="devolucionFinalizar" >
					<h2 align="center">Motivo de devolución</h2>

					<input type="hidden" name="id_compra" value="<?php echo $id_compra ?>">
				    <input type="hidden" name="id_producto" value="<?php echo $id_producto ?>">
				    <input type="hidden" name="precio_costo" value="<?php echo $precio_costo ?>">
				    <input type="hidden" name="precio_compra" value="<?php echo $precio_compra ?>">
				    <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
				    <input type="hidden" name="descuento" value="<?php echo $descuento ?>">
				    <input type="hidden" name="iva" value="<?php echo $iva ?>">
				    <input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
				    <input type="hidden" name="cantidad" value="<?php echo $cantidad ?>">
				    <input type="hidden" name="margen_ganancia" value="<?php echo $margen_ganancia ?>">
				    <input type="hidden" name="fecha_compra" value="<?php echo $fecha_compra ?>">
				    <input type="hidden" name="metodo" value="<?php echo $metodo ?>">
				    <input type="hidden" name="banco" value="0">
				    <!-- <input type="hidden" name="contado" value="Efectivo"> -->
				    

					<div class="form-group" >
				    	<label>Forma de pago</label>
				    	<select name="forma_pago" class="form-control">
				    		<?php foreach ($this->metodo->Listar() as $m): ?>
				    			<option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
				    		<?php endforeach; ?>
				    	</select>
				    </div>

					<div class="form-group" >
				    	<label>Sucursal</label>
				    	<select name="sucursal" class="form-control">
							<option value="tienda">Show Room</option>
				    		<option value="taller">Centro de Instalaciones</option>
				    	</select>
				    </div>

					<div class="form-group" >
				    	<label>Nota de Credito</label>
				    	<select name="nota_credito" class="form-control">
							<option value="1">SÍ</option>
				    		<option value="0">NO</option>
				    	</select>
				    </div>

            		<div class="form-group">
				    	<label>Motivo</label>
				    	<textarea name="motivo" class="form-control"></textarea>
				    </div>

				    <input type="submit" class="btn btn-primary" value="Finalizar devolución">
				    
				</form>

            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
            
        </div>
    </div>
</div>

<script>
	$('#pago').on('change',function(){
		var valor = $(this).val();
		if (valor == "Transferencia" || valor == "Giro") {
			$("#banco").show();
		}else{
			$("#banco").hide();
		}
	});
	$('#contado').on('change',function(){
		var valor = $(this).val();
		if (valor == "Cuota") {
			$("#entrega").show();
		}else{
			$("#entrega").hide();
		}
	});
</script>
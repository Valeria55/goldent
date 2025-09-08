<div id="finalizarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=venta&a=guardar">
					<h2 align="center">Datos de venta</h2>
				    <div class="form-group">
				        <label>Fecha de la venta</label>
				        <input type="datetime-local" name="fecha_venta" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
				    </div>
				    <div class="form-group">
						<label>Cliente</label>
                            <select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                    title="-- Seleccione el cliente --" autofocus>
                                <?php foreach($this->cliente->ListarMayorista() as $cliente): ?> 
                                <option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>"><?php echo $cliente->nombre; ?> </option>
                                <?php endforeach; ?>
                            </select>
				    </div>
				    
                    <div class="form-group">
						<label>Comprobante</label>
						<select name="comprobante" id="comprobante" class="form-control">
							<option value="Ticket">Ticket</option> 
							<option value="Factura">Factura</option> 
						</select>
				    </div>
				    
				    <div class="form-group" id="nro_comprobante">
				        <label>Nro. comprobante</label>
				        <input type="text" name="nro_comprobante" class="form-control" placeholder="Ingrese el nro de comprobante">
				    </div>

				    <div class="form-group">
						<label>Método de pago</label>
						<select name="pago" id="pago" class="form-control">
							<option value="Efectivo">Efectivo</option> 
							<option value="Tarjeta">Tarjeta</option> 
							<option value="Transferencia">Transferencia</option> 
						</select>
				    </div>


				    <div class="form-group" id="banco" style="display: none;">
				        <label>Banco</label>
				        <input type="text" name="banco" class="form-control" placeholder="Ingrese nombre de banco">
				    </div>

				    <div class="form-group">
						<label>Pago</label>
						<select name="contado" id="contado" class="form-control"> 
							<option value="Contado">Contado</option>
							<option value="Cuota">Cuota</option>
							<option value="Credito">Crédito</option>
						</select>
				    </div>
				    
				    <div class="form-group" id="entrega" style="display: none;">
				        <label>Entrega</label>
				        <input type="number" name="entrega" min="0" max="<?php echo $subtotal ?>" class="form-control" value="0" placeholder="Ingrese entrega">
				    </div>

				    <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
				    <input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
				    <input type="hidden" name="descuentoval" id="descuentoval" value="0">
				    <input type="hidden" name="ivaval" id="ivaval" value="0">
				    <input type="hidden" name="id_vendedor" value="12">
				    <input type="submit" class="btn btn-primary" value="Finalizar venta">
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
		if (valor == "Transferencia") {
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
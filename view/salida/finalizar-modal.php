<div id="finalizarModal" class="modal fade bd-example-modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=salida&a=guardar" id="finalizar">
					<h3 align="center">Detalles de la tranferencia</h3>
					
				    <div class="form-group col-sm-12" style="display:none;">
				        <label>Fecha</label>
				        <input type="datetime-local" name="fecha_venta" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
				    </div>
					
				    <div class="form-group col-sm-12" >
						<label>Receptor </label>
                        <select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                title="-- Seleccione el cliente --" autofocus>
                            <option value="0" selected>CLiente ocasional</option>
                            <?php foreach($this->cliente->Listar() as $cliente): ?> 
                            <option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>"<?php echo ($ven->id_cliente == $cliente->id)? "selected":""; ?>><?php echo $cliente->nombre.' '.$cliente->ruc; ?> </option>
                            <?php endforeach; ?>
                        </select>
				    </div>
				     <div class="form-group col-sm-6" id="nro_comprobante">
				        <label>Nro. Transferencia</label>
				        <input type="text" name="nro_comprobante" class="form-control" placeholder="Ingrese el nro de comprobante">
				    </div>
				    
				    <input type="hidden" name="pago" value="5">

            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
            
        </div>
    </div>
</div>

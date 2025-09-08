
<div id="finalizarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=factura&a=guardar" id="finalizar">
					<h3 align="center">Datos del factura</h3>
					
				    <div class="form-group col-sm-12">
				        <label>Fecha de la venta</label>
						<input type="datetime-local" name="fecha_factura" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">

				    </div>
				    <div class="form-group col-sm-12">
						<label>Cliente </label>
                        <select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                title="-- Seleccione el cliente --" required>
                            
                            <?php foreach($this->cliente->Listar() as $cliente): ?> 
                            <option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>"><?php echo $cliente->nombre.' '.$cliente->ruc; ?> </option>
                            <?php endforeach; ?>
                        </select>
				    </div>
                    <div class="form-group col-sm-12">
                        <label>Nro. Factura </label>
                        <select name="nro_comprobante" id="comprobante" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el nro de factura --" required>
                            <?php foreach($this->venta->Listar(0) as $venta): ?> 
                                <?php if($venta->anulado == 0): ?>
                                    <option value="<?php echo $venta->nro_comprobante; ?>"><?php echo $venta->nro_comprobante.' - '.$venta->nombre_cli.' ('.$venta->ruc.')'; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
				    <div align="center">
                        <input type="submit" class="btn btn-primary" value="Finalizar" onclick="return Guardar()">
                    </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
            
        </div>
    </div>
</div>

<script>
    function Guardar() {
        this.disabled = true;
        this.value = 'Guardando, Espere...';
        this.form.submit();
    }
</script>

<div id="finalizarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=presupuesto&a=guardar" id="finalizar">
					<h3 align="center">Datos del presupuesto</h3>
					
				    <div class="form-group col-sm-12" style="display:none;">
				        <label>Fecha de la venta</label>
				        <input type="datetime-local" name="fecha_presupuesto" class="form-control" value="<?php echo date("Y-m-d") ?>T<?php echo date("H:i") ?>">
				    </div>
				    <div class="form-group col-sm-12">
						<label>Cliente</label>
                        <select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                title="-- Seleccione el cliente --" autofocus require>
                            <option value="0" selected>Cliente ocasional</option>
                            <?php foreach($this->cliente->Listar() as $cliente): ?> 
                            <option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>"><?php echo $cliente->nombre.' '.$cliente->ruc; ?> </option>
                            <?php endforeach; ?>
                        </select>
				    </div>
				    
				    <div class="form-group col-sm-12" id="div-adelantos" style="display:none;">
						<label>Adelanto disponible</label>
                        <select name="id_adelanto" id="id_adelanto" class="form-control">
                            <option value="">-- No usar adelanto --</option>
                        </select>
				    </div>

				    <div class="form-group col-sm-12">
						<label>Descuento Global (%) </label>
                        <input type="number" name="descuento_global" class="form-control" value="0" min="0" max="100" step="0.01" id="descuento_global">
                        <small class="text-warning">* Si es mayor a 0, reemplazará los descuentos individuales. Si es mayor a 10%, el presupuesto requerirá aprobación.</small>
				    </div>

				    <div align="center">
                        <input type="submit" class="btn btn-primary" value="Finalizar" onclick="this.disabled=true;this.value='Guardando, Espere...';this.form.submit();">
                    </div>

            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
            
        </div>
    </div>
</div>

<script>
    $('#cliente').on('change', function() {
        var id_cliente = $(this).val();
        if (id_cliente > 0) {
            $.post('?c=adelanto&a=ListarPendientes', {id_cliente: id_cliente}, function(data) {
                var adelantos = JSON.parse(data);
                var $select = $('#id_adelanto');
                $select.empty();
                $select.append('<option value="">-- No usar adelanto --</option>');
                if (adelantos.length > 0) {
                    $('#div-adelantos').show();
                    adelantos.forEach(function(a) {
                        $select.append('<option value="' + a.id + '">Adelanto #' + a.id + ' - GS ' + parseFloat(a.monto).toLocaleString('es-PY') + '</option>');
                    });
                } else {
                    $('#div-adelantos').hide();
                }
            });
        } else {
            $('#div-adelantos').hide();
        }
    });
</script>

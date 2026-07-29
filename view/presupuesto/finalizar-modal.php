
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
				    
				    <div class="form-group col-sm-12" id="div-obs-cliente-presu" style="display:none;">
				        <div class="alert alert-warning" style="margin-bottom: 0; border-left: 5px solid #f0ad4e;">
				            <i class="fa fa-exclamation-triangle"></i> <strong>Observación del Cliente:</strong> <span id="txt-obs-cliente-presu"></span>
				        </div>
				    </div>

				    <div class="form-group col-sm-12" id="div-adelantos" style="display:none;">
						<label>Adelantos disponibles (Selección múltiple)</label>
                        <select name="id_adelanto[]" id="id_adelanto" class="form-control selectpicker" multiple data-actions-box="true" data-live-search="true" title="-- Seleccione adelanto(s) a descontar --">
                        </select>
                        <small class="text-info">* Puede seleccionar 1 o varios adelantos del cliente.</small>
				    </div>

				    <div class="form-group col-sm-12">
						<label>Descuento Global (%) </label>
                        <input type="number" name="descuento_global" class="form-control" value="0" min="0" max="100" step="0.01" id="descuento_global">
                        <small class="text-warning">* Si es mayor a 0, reemplazará los descuentos individuales. Si es mayor a 10%, el presupuesto requerirá aprobación.</small>
				    </div>

				    <div class="form-group col-sm-12">
						<label><i class="fa fa-truck text-primary"></i> Responsable del Área de Entregas</label>
                        <select name="responsable_entrega_id" id="responsable_entrega_id" class="form-control selectpicker" data-live-search="true" data-style="form-control" title="-- Seleccione el responsable de entrega --">
                            <option value="0" selected>-- Sin asignar / No requiere entrega --</option>
                            <?php 
                            $entregadoresList = method_exists($this->usuario, 'ListarEntregadores') ? $this->usuario->ListarEntregadores() : array();
                            if (empty($entregadoresList)) {
                                foreach($this->usuario->ListarUsuarios() as $u) {
                                    if ($u->nivel == 11) $entregadoresList[] = $u;
                                }
                            }
                            foreach($entregadoresList as $u): 
                            ?>
                            <option value="<?php echo $u->id; ?>"><?php echo htmlspecialchars($u->user); ?> (<?php echo htmlspecialchars($u->sucursal ? $u->sucursal : 'General'); ?>)</option>
                            <?php endforeach; ?>
                        </select>
				    </div>

				    <div class="form-group col-sm-12">
						<label><i class="fa fa-comment text-info"></i> Observaciones del Presupuesto (Orden de Entrega)</label>
                        <textarea name="observacion_presupuesto" id="observacion_presupuesto" class="form-control" rows="3" placeholder="Ingrese notas o indicaciones especiales para la entrega..."></textarea>
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
            $.post('?c=cliente&a=Buscar', {id: id_cliente}, function(data) {
                var c = typeof data === 'string' ? JSON.parse(data) : data;
                var obs = (c && c.observacion_cliente && $.trim(c.observacion_cliente) !== '') ? c.observacion_cliente : (c && c.observacion ? c.observacion : '');
                if (obs && $.trim(obs) !== '') {
                    $('#txt-obs-cliente-presu').text(obs);
                    $('#div-obs-cliente-presu').slideDown();
                } else {
                    $('#div-obs-cliente-presu').slideUp();
                }
            });

            $.post('?c=adelanto&a=ListarPendientes', {id_cliente: id_cliente}, function(data) {
                var adelantos = JSON.parse(data);
                var $select = $('#id_adelanto');
                $select.empty();
                if (adelantos.length > 0) {
                    $('#div-adelantos').show();
                    adelantos.forEach(function(a) {
                        var desc = a.descripcion ? ' (' + a.descripcion + ')' : '';
                        $select.append('<option value="' + a.id + '">Adelanto #' + a.id + ' - Gs. ' + parseFloat(a.monto).toLocaleString('es-PY') + desc + '</option>');
                    });
                    $select.selectpicker('refresh');
                } else {
                    $('#div-adelantos').hide();
                    $select.selectpicker('refresh');
                }
            });
        } else {
            $('#div-obs-cliente-presu').slideUp();
            $('#div-adelantos').hide();
            $('#id_adelanto').empty().selectpicker('refresh');
        }
    });

    $('#finalizarModal').on('shown.bs.modal', function () {
        var id_cli = $('#cliente').val();
        if (id_cli > 0) {
            $('#cliente').trigger('change');
        }
    });
</script>

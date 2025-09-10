<div id="devolucionModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-body">
				<form method="post" action="?c=devolucion_ventas&a=guardar" id="devolucionFinalizar" >
					<h2 align="center">Motivo de devolución</h2>

					<input type="hidden" name="id_venta" value="<?php echo $id_venta ?>">
				    <input type="hidden" name="id_producto" value="<?php echo $id_producto ?>">
				    <input type="hidden" name="precio_costo" value="<?php echo $precio_costo ?>">
				    <input type="hidden" name="precio_venta" value="<?php echo $precio_venta ?>">
				    <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
				    <input type="hidden" name="descuento" value="<?php echo $descuento ?>">
				    <input type="hidden" name="iva" value="<?php echo $iva ?>">
				    <input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
				    <input type="hidden" name="cantidad" value="<?php echo $cantidad ?>">
				    <input type="hidden" name="margen_ganancia" value="<?php echo $margen_ganancia ?>">
				    <input type="hidden" name="fecha_venta" value="<?php echo $fecha_venta ?>">
				    <input type="hidden" name="metodo" value="<?php echo $metodo ?>">
				    <input type="hidden" name="banco" value="0">
				    <input type="hidden" name="contado" value="Efectivo">
				    
				    <!-- Mostrar total de la devolución -->
				    <div class="form-group">
				        <label><strong>Total de la devolución (Guaraníes):</strong></label>
				        <div class="well" style="background-color: #f5f5f5; padding: 10px; text-align: center;">
				            <h4 id="totalDevolucion" style="color: #d9534f; margin: 0;">
				                <?php 
				                // Obtener total desde la tabla mostrada en la vista
				                $total = 0;
				                foreach($this->model->Listar() as $r) {
				                    $totalItem = ($r->precio_venta*$r->cantidad)-(($r->precio_venta*$r->cantidad)*($r->descuento/100));
				                    $total += $totalItem;
				                }
				                echo '₲ ' . number_format($total, 0, '.', ',');
				                ?>
				            </h4>
				        </div>
				    </div>

					<div class="form-group" >
				    	<label>Forma de pago</label>
				    	<select name="forma_pago" class="form-control">
				    		<?php foreach ($this->metodo->Listar() as $m): ?>
				    			<option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
				    		<?php endforeach; ?>
				    	</select>
				    </div>
				    
				    <!-- Selección de moneda -->
				    <div class="form-group">
				        <label>Moneda de pago</label>
				        <select name="moneda_display" id="moneda" class="form-control" onchange="toggleCambio()">
				            <option value="Guaranies">Guaraníes</option>
				            <option value="Dolares">Dólares</option>
				            <option value="Reales">Reales</option>
				        </select>
				        <!-- Campo hidden para enviar el código de moneda correcto -->
				        <input type="hidden" name="moneda" id="monedaCodigo" value="GS">
				    </div>
				    
				    <!-- Campo de cambio (solo visible si no es Guaraníes) -->
				    <?php 
				    // Obtener cotizaciones del último cierre
				    $ultimoCierre = $this->cierre->Ultimo();
				    $cotDolar = $ultimoCierre ? $ultimoCierre->cot_dolar : 7500;
				    $cotReal = $ultimoCierre ? $ultimoCierre->cot_real : 1300;
				    ?>
				    <div class="form-group" id="campoCambio" style="display: none;">
				        <label>Tipo de cambio <small class="text-info">(del último cierre)</small></label>
				        <div class="input-group">
				            <span class="input-group-addon">1 <span id="monedaSeleccionada"></span> =</span>
				            <input type="number" name="cambio" id="cambio" class="form-control" step="0.01" value="1" onchange="calcularEquivalencia()" oninput="calcularEquivalencia()">
				            <span class="input-group-addon">Guaraníes</span>
				        </div>
				        <small class="text-muted">Puede modificar el tipo de cambio si es necesario</small>
				    </div>
				    
				    <!-- Mostrar equivalencia en moneda seleccionada -->
				    <div class="form-group" id="equivalencia" style="display: none;">
				        <label>Equivalente en moneda seleccionada:</label>
				        <div class="well" style="background-color: #e7f3ff; padding: 10px; text-align: center;">
				            <h5 id="totalEquivalente" style="color: #337ab7; margin: 0;"></h5>
				        </div>
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
	
	// Función para mostrar/ocultar el campo de cambio
	function toggleCambio() {
		var moneda = document.getElementById('moneda').value;
		var campoCambio = document.getElementById('campoCambio');
		var equivalencia = document.getElementById('equivalencia');
		var monedaSeleccionada = document.getElementById('monedaSeleccionada');
		var campoInput = document.getElementById('cambio');
		var monedaCodigo = document.getElementById('monedaCodigo');
		
		// Cotizaciones del último cierre
		var cotDolar = <?php echo $cotDolar; ?>;
		var cotReal = <?php echo $cotReal; ?>;
		
		if (moneda === 'Guaranies') {
			campoCambio.style.display = 'none';
			equivalencia.style.display = 'none';
			campoInput.value = 1;
			monedaCodigo.value = 'GS';
		} else {
			campoCambio.style.display = 'block';
			equivalencia.style.display = 'block';
			monedaSeleccionada.textContent = moneda.slice(0, -1); // Quitar la 's' final
			
			// Establecer cotización por defecto y código según la moneda
			if (moneda === 'Dolares') {
				campoInput.value = cotDolar;
				monedaCodigo.value = 'USD';
			} else if (moneda === 'Reales') {
				campoInput.value = cotReal;
				monedaCodigo.value = 'RS';
			}
			
			calcularEquivalencia();
		}
	}
	
	// Función para calcular la equivalencia en la moneda seleccionada
	function calcularEquivalencia() {
		var totalGuaranies = <?php 
		// Calcular el total en PHP para JavaScript
		$total = 0;
		foreach($this->model->Listar() as $r) {
		    $totalItem = ($r->precio_venta*$r->cantidad)-(($r->precio_venta*$r->cantidad)*($r->descuento/100));
		    $total += $totalItem;
		}
		echo $total;
		?>;
		var cambio = parseFloat(document.getElementById('cambio').value) || 1;
		var moneda = document.getElementById('moneda').value;
		
		if (moneda !== 'Guaranies' && cambio > 0) {
			var equivalente = totalGuaranies / cambio;
			var simbolo = '';
			
			switch(moneda) {
				case 'Dolares':
					simbolo = 'USD $';
					break;
				case 'Reales':
					simbolo = 'R$';
					break;
			}
			
			document.getElementById('totalEquivalente').textContent = 
				simbolo + ' ' + equivalente.toLocaleString('es-PY', {
					minimumFractionDigits: 3,
					maximumFractionDigits: 3
				});
		}
	}
	
	// Inicializar al cargar la página
	document.addEventListener('DOMContentLoaded', function() {
		toggleCambio();
	});
</script>
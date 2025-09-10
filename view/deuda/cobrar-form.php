<form method="post">
    <input type="hidden" name="c" value="deuda">
    <input type="hidden" name="a" value="Cobrar">
    <input type="hidden" name="id" value="<?php echo $r->id ?>">
    <input type="hidden" name="id_cliente" value="<?php echo $r->id_cliente ?>">
    <input type="hidden" name="id_venta" value="<?php echo $r->id_venta ?>">
    <input type="hidden" name="cli" value="<?php echo $r->nombre ?>">
    <input type="hidden" id="saldo_gs" value="<?php echo $r->saldo ?>">
    
    <!-- Agregar las cotizaciones como campos ocultos -->
    <input type="hidden" id="cot_dolar" value="<?php echo $cotizacion_usd ?>">
    <input type="hidden" id="cot_real" value="<?php echo $cotizacion_rs ?>">
    
    <h3>Cobro por <?php echo $r->concepto ?></h3>
    <br>
    
    <!-- Mostrar saldo en diferentes monedas -->
    <div class="row">
        <div class="col-md-4">
            <h4>Saldo en Gs: <?php echo number_format($r->saldo, 0, ",", ".") ?></h4>
        </div>
        <div class="col-md-4">
            <h4 id="saldo_usd">Saldo en USD: $<?php echo number_format($r->saldo / $cotizacion_usd, 3, ".", ".") ?></h4>
        </div>
        <div class="col-md-4">
            <h4 id="saldo_rs">Saldo en R$: R$<?php echo number_format($r->saldo / $cotizacion_rs, 3, ".", ".") ?></h4>
        </div>
    </div>
    <br>
    
    <!-- Forma de pago -->
    <div class="form-group">
        <label>Forma de pago</label>
        <select name="forma_pago" id="forma_pago" class="form-control">
            <?php foreach ($this->metodo->Listar() as $m): ?>
                <option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- Selector de moneda -->
    <div class="form-group">
        <label>Moneda</label>
        <select name="moneda" id="moneda" class="form-control">
            <option value="Gs">Guaraníes (Gs)</option>
            <option value="USD">Dólares (USD)</option>
            <option value="RS">Reales (R$)</option>
        </select>
    </div>
    
    <!-- Monto -->
    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="mon" id="monto_cobro" min="1" class="form-control" step="0.001">
        <small class="form-text text-muted" id="equivalencia"></small>
    </div>
    
    <!-- Comprobante -->
    <div class="form-group">
        <label>Nro. Comprobante</label>
        <input type="text" name="comprobante" class="form-control" value="Recibo Nº ">
    </div>
    
    <div class="form-group">
        <input type="submit" value="Cobrar" class="btn btn-primary">
    </div>
</form>

<script>
$(document).ready(function() {
    // Obtener cotizaciones desde los campos ocultos
    var cotizacionUsd = parseFloat($('#cot_dolar').val());
    var cotizacionRs = parseFloat($('#cot_real').val());
    
    window.cotizaciones = {
        usd: cotizacionUsd,
        rs: cotizacionRs
    };
    
    // Controlar la habilitación del selector de moneda al cargar
    controlMonedaSelector();
    configurarValidacion();
    
    $('#forma_pago').on('change', function() {
        controlMonedaSelector();
        configurarValidacion();
    });
    
    $('#moneda').on('change', function() {
        configurarValidacion();
    });
    
    $('#monto_cobro').on('keyup', function() {
        mostrarEquivalencia();
    });
    
    function controlMonedaSelector() {
        var formaPago = $('#forma_pago').val();
        var monedaSelect = $('#moneda');
        
        if (formaPago === "Efectivo") {
            monedaSelect.prop('disabled', false);
        } else {
            // Para otros métodos, solo Guaraníes
            monedaSelect.prop('disabled', true);
            monedaSelect.val('Gs');
        }
    }
    
    function configurarValidacion() {
        var moneda = $('#moneda').val();
        var formaPago = $('#forma_pago').val();
        var saldoGs = parseFloat($('#saldo_gs').val());
        var maxMonto;
        
        // Si no es efectivo, forzar a Guaraníes
        if (formaPago !== "Efectivo") {
            moneda = 'Gs';
        }
        
        if (moneda === 'Gs') {
            maxMonto = saldoGs;
        } else if (moneda === 'USD') {
            maxMonto = (saldoGs / window.cotizaciones.usd).toFixed(3);
        } else if (moneda === 'RS') {
            maxMonto = (saldoGs / window.cotizaciones.rs).toFixed(3);
        }
        
        $('#monto_cobro').attr('max', maxMonto);
        $('#monto_cobro').val('');
        $('#equivalencia').text('');
    }
    
    function mostrarEquivalencia() {
        var monto = parseFloat($('#monto_cobro').val());
        var moneda = $('#moneda').val();
        var formaPago = $('#forma_pago').val();
        
        if (!monto) return;
        
        // Si no es efectivo, no mostrar equivalencia (siempre es GS)
        if (formaPago !== "Efectivo") {
            $('#equivalencia').text('');
            return;
        }
        
        var equivalenciaGs;
        
        if (moneda === 'USD') {
            equivalenciaGs = monto * window.cotizaciones.usd;
            $('#equivalencia').text('Equivale a: Gs ' + equivalenciaGs.toLocaleString('es-PY'));
        } else if (moneda === 'RS') {
            equivalenciaGs = monto * window.cotizaciones.rs;
            $('#equivalencia').text('Equivale a: Gs ' + equivalenciaGs.toLocaleString('es-PY'));
        } else {
            $('#equivalencia').text('');
        }
    }
});
</script>
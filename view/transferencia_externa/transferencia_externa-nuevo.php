<?php
$monto_val = '';
$monto_vis = '';
$concepto_val = '';
$comprobante_txt = '';

if (isset($transf) && $transf->id > 0) {
    $monto_val = $transf->monto;
    if (floor($monto_val) == $monto_val) {
        $monto_vis = number_format($monto_val, 0, ',', '.');
    } else {
        $formatted = number_format($monto_val, 3, ',', '.');
        $monto_vis = rtrim(rtrim($formatted, '0'), ',');
    }
    $concepto_val = $transf->concepto;
    
    $comprobante = $transf->comprobante_url;
    if (strpos($comprobante, '|') !== false) {
        list($text_url, $file_url) = explode('|', $comprobante, 2);
        $comprobante_txt = $text_url;
    } else {
        if (strpos($comprobante, '/transferencias/comprobante_') === false) {
            $comprobante_txt = $comprobante;
        }
    }
}
?>
<h1 class="page-header">
    <?php echo isset($transf) && $transf->id > 0 ? 'Editar Transferencia' : 'Nueva Transferencia a Paseo de la Sonrisa'; ?>
</h1>

<ol class="breadcrumb">
    <li><a href="?c=transferencia_externa">Transferencias Externas</a></li>
    <li class="active"><?php echo isset($transf) && $transf->id > 0 ? 'Editar Registro' : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=transferencia_externa&a=Guardar" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo isset($transf) ? $transf->id : ''; ?>" />
    
    <div class="form-group">
        <label>Monto</label>
        <input type="text" id="monto_visible" class="form-control" placeholder="Ingrese el monto" value="<?php echo htmlspecialchars($monto_vis); ?>" required autocomplete="off" inputmode="decimal">
        <input type="hidden" name="monto" id="monto_real" value="<?php echo htmlspecialchars($monto_val); ?>">
    </div>

    <script>
    (function() {
        const visibleInput = document.getElementById('monto_visible');
        const hiddenInput = document.getElementById('monto_real');
        
        if (!visibleInput || !hiddenInput) return;
        
        visibleInput.addEventListener('input', function() {
            let val = this.value;
            
            // Remove all dots (thousands separators)
            let cleaned = val.replace(/\./g, '');
            
            // Keep only digits and the first comma as decimal separator
            cleaned = cleaned.replace(/[^0-9,]/g, '');
            const commaIndex = cleaned.indexOf(',');
            if (commaIndex !== -1) {
                cleaned = cleaned.substring(0, commaIndex + 1) + cleaned.substring(commaIndex + 1).replace(/,/g, '');
            }
            
            // Split integer and decimal parts
            let parts = cleaned.split(',');
            let integerPart = parts[0];
            let decimalPart = parts.length > 1 ? parts[1] : null;
            
            // Remove leading zeros
            if (integerPart.length > 1 && integerPart.startsWith('0')) {
                integerPart = integerPart.replace(/^0+/, '');
                if (integerPart === '') integerPart = '0';
            }
            
            // Save cursor positions to prevent jumps
            const selStart = this.selectionStart;
            const selEnd = this.selectionEnd;
            const oldLen = this.value.length;
            
            // Format integer part with dot as thousands separator
            let formattedInt = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            
            // Reconstruct the formatted value
            let formattedVal = formattedInt;
            if (decimalPart !== null) {
                formattedVal += ',' + decimalPart.substring(0, 3);
            }
            
            this.value = formattedVal;
            
            // Calculate new cursor positions
            const newLen = formattedVal.length;
            const diff = newLen - oldLen;
            this.setSelectionRange(selStart + diff, selEnd + diff);
            
            // Set the raw numeric value in the hidden input
            // E.g., '1.234.567,89' -> '1234.567'
            let rawValue = formattedVal.replace(/\./g, '').replace(',', '.');
            hiddenInput.value = rawValue;
        });
    })();
    </script>

    <div class="form-group">
        <label>Concepto</label>
        <input type="text" name="concepto" value="<?php echo htmlspecialchars($concepto_val); ?>" class="form-control" placeholder="Ej: Envío de recaudación" required>
    </div>

    <div class="form-group">
        <label>Comprobante</label>
        <input type="text" name="comprobante" value="<?php echo htmlspecialchars($comprobante_txt); ?>" class="form-control" placeholder="Ingrese URL de comprobante">
    </div>

    <div class="form-group">
        <label>Subir Archivo o Imagen (Opcional)</label>
        <input type="file" name="comprobante_file" class="form-control" accept="image/*,application/pdf">
        <?php
        $file_url = '';
        if (isset($transf) && $transf->id > 0) {
            $comprobante = $transf->comprobante_url;
            if (strpos($comprobante, '|') !== false) {
                list($text_url, $file_url) = explode('|', $comprobante, 2);
            } elseif (strpos($comprobante, '/transferencias/comprobante_') !== false) {
                $file_url = $comprobante;
            }
        }
        if (!empty($file_url)): ?>
            <p class="help-block">
                Archivo cargado actualmente: <a href="<?php echo htmlspecialchars($file_url); ?>" target="_blank">Ver Archivo</a> (si sube otro se reemplazará).
            </p>
        <?php endif; ?>
    </div>

    <hr />
    <div class="alert alert-info">
        <strong>Atención:</strong> Esta transferencia será enviada directamente a la bandeja de entrada de Paseo de la Sonrisa. Quedará en estado PENDIENTE hasta que sea procesada en la central.
        <br><br>
        <em>Nota: Este registro no afecta los saldos locales de las cajas en este sistema. Es meramente un reporte externo.</em>
    </div>

    <div class="text-right">
        <button class="btn btn-primary"><?php echo isset($transf) && $transf->id > 0 ? 'Guardar Cambios' : 'Enviar Transferencia'; ?></button>
    </div>
</form>

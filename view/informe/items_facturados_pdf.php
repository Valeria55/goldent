<?php
require_once('plugins/tcpdf2/tcpdf.php');

$desde_f = date("d/m/Y", strtotime($_REQUEST["desde"]));
$hasta_f = date("d/m/Y", strtotime($_REQUEST["hasta"]));
$comprobante_sel = isset($_REQUEST['comprobante']) ? $_REQUEST['comprobante'] : 'Todos';
$agrupado_sel = isset($_REQUEST['agrupado']) ? (int)$_REQUEST['agrupado'] : 0;

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Informe de Items Facturados');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Adjust orientation for detailed report
if ($agrupado_sel === 1) {
    $pdf->AddPage('P', 'A4'); // Portrait for grouped
} else {
    $pdf->AddPage('L', 'A4'); // Landscape for detailed (more columns)
}

$comprobante_txt = 'Todos';
if ($comprobante_sel === 'Factura') $comprobante_txt = 'Facturas';
elseif ($comprobante_sel === 'Ticket') $comprobante_txt = 'Tickets';
elseif ($comprobante_sel === 'TicketSi') $comprobante_txt = 'Sin Comprobante / Sin impresión';

$agrupacion_txt = $agrupado_sel === 1 ? 'Agrupado por Producto' : 'Detallado (Desagrupado)';

$html = <<<EOF
<style>
    .header { font-family: 'Helvetica', 'Arial', sans-serif; }
    .company-name { font-size: 16pt; font-weight: bold; margin-bottom: 2px; color: #2c3e50; }
    .company-info { font-size: 9pt; color: #7f8c8d; }
    .range { font-size: 10pt; margin-top: 10px; font-weight: bold; }
    .title { font-size: 12pt; font-weight: bold; margin-top: 15px; color: #348993; text-transform: uppercase; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background-color: #348993; color: white; font-weight: bold; text-align: center; font-size: 9pt; padding: 6px; }
    td { font-size: 8.5pt; padding: 6px; border-bottom: 0.5px solid #ddd; }
    .total-row { font-weight: bold; background-color: #eaeded; }
</style>

<div class="header">
    <div class="company-name">GOLDENT S.A</div>
    <div class="company-info">Dirección: Calle Ernesto Baez y Los Rosales | Tel.: 061 571136</div>
    <hr>
    
    <table style="width: 100%; margin-bottom: 10px; border: none;">
        <tr>
            <td style="width: 50%; border: none; font-size: 10pt; padding: 0;"><b>Rango:</b> $desde_f al $hasta_f</td>
            <td style="width: 50%; border: none; font-size: 10pt; padding: 0; text-align: right;"><b>Comprobantes:</b> $comprobante_txt</td>
        </tr>
        <tr>
            <td style="width: 50%; border: none; font-size: 10pt; padding: 0;"><b>Agrupación:</b> $agrupacion_txt</td>
            <td style="width: 50%; border: none; font-size: 10pt; padding: 0; text-align: right;"><b>Moneda:</b> Guaraníes (Gs)</td>
        </tr>
    </table>
    
    <div class="title">Informe de Ítems Facturados</div>
</div>

<table>
    <thead>
EOF;

if ($agrupado_sel === 1) {
    // Grouped layout headers
    $html .= <<<EOF
        <tr>
            <th width="60%">Item (Producto / Servicio)</th>
            <th width="12%" style="text-align: center;">Cant. Total</th>
            <th width="14%" style="text-align: right;">P. Promedio (Gs)</th>
            <th width="14%" style="text-align: right;">Total Acum. (Gs)</th>
        </tr>
    </thead>
    <tbody>
EOF;
    
    $totalCant = 0;
    $totalMonto = 0;
    foreach ($resultados as $r) {
        $totalCant += $r->cantidad;
        $totalMonto += $r->total;
        
        $producto = htmlspecialchars($r->producto);
        $cantidad = number_format($r->cantidad, 0, ",", ".");
        $precio_p = number_format($r->precio_venta, 0, ",", ".");
        $total = number_format($r->total, 0, ",", ".");
        
        $html .= <<<EOF
        <tr>
            <td>$producto</td>
            <td style="text-align: center; font-weight: bold;">$cantidad</td>
            <td style="text-align: right;">$precio_p</td>
            <td style="text-align: right; font-weight: bold;">$total</td>
        </tr>
EOF;
    }
    
    if (count($resultados) === 0) {
        $html .= '<tr><td colspan="5" style="text-align: center; padding: 20px;">Sin registros para el rango seleccionado.</td></tr>';
    }
    
    $totalCant_f = number_format($totalCant, 0, ",", ".");
    $totalMonto_f = number_format($totalMonto, 0, ",", ".");
    
    $html .= <<<EOF
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="1" style="text-align: right; border-top: 1px solid #333;">TOTALES GENERALES:</td>
            <td style="text-align: center; border-top: 1px solid #333; color: #348993;">$totalCant_f</td>
            <td border-top="1px solid #333;"></td>
            <td style="text-align: right; border-top: 1px solid #333; color: #27ae60;">$totalMonto_f</td>
        </tr>
    </tfoot>
EOF;

} else {
    // Detailed layout headers
    $html .= <<<EOF
        <tr>
            <th width="6%" style="text-align: center;">Nº Venta</th>
            <th width="6%" style="text-align: center;">Nº Orden</th>
            <th width="13%" style="text-align: center;">Cliente</th>
            <th width="13%" style="text-align: center;">Paciente</th>
            <th width="18%">Item (Producto / Servicio)</th>
            <th width="10%" style="text-align: center;">Fecha</th>
            <th width="8%" style="text-align: center;">Comprobante</th>
            <th width="6%" style="text-align: center;">Cant.</th>
            <th width="10%" style="text-align: right;">Precio Unit.</th>
            <th width="10%" style="text-align: right;">Total (Gs)</th>
        </tr>
    </thead>
    <tbody>
EOF;
    
    $totalCant = 0;
    $totalMonto = 0;
    foreach ($resultados as $r) {
        $totalCant += $r->cantidad;
        $totalMonto += $r->total;
        
        $id_venta = htmlspecialchars($r->id_venta);
        $id_presupuesto = htmlspecialchars($r->id_presupuesto ?? '-');
        $cliente = htmlspecialchars($r->cliente ?? '-');
        $paciente = htmlspecialchars($r->paciente ?? '-');
        $producto = htmlspecialchars($r->producto);
        $fecha = date('d/m/Y H:i', strtotime($r->fecha_venta));
        
        $compLabel = 'Sin Comp.';
        if ($r->comprobante === 'Factura') $compLabel = 'Factura';
        elseif ($r->comprobante === 'Ticket') $compLabel = 'Ticket';
        
        $cantidad = number_format($r->cantidad, 0, ",", ".");
        $precio = number_format($r->precio_venta, 0, ",", ".");
        $total = number_format($r->total, 0, ",", ".");
        
        $html .= <<<EOF
        <tr>
            <td style="text-align: center; font-weight: bold;">$id_venta</td>
            <td style="text-align: center;">$id_presupuesto</td>
            <td style="text-align: center;">$cliente</td>
            <td style="text-align: center;">$paciente</td>
            <td>$producto</td>
            <td style="text-align: center;">$fecha</td>
            <td style="text-align: center;">$compLabel</td>
            <td style="text-align: center; font-weight: bold;">$cantidad</td>
            <td style="text-align: right;">$precio</td>
            <td style="text-align: right; font-weight: bold;">$total</td>
        </tr>
EOF;
    }
    
    if (count($resultados) === 0) {
        $html .= '<tr><td colspan="9" style="text-align: center; padding: 20px;">Sin registros para el rango seleccionado.</td></tr>';
    }
    
    $totalCant_f = number_format($totalCant, 0, ",", ".");
    $totalMonto_f = number_format($totalMonto, 0, ",", ".");
    
    $html .= <<<EOF
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="7" style="text-align: right; border-top: 1px solid #333;">TOTALES GENERALES:</td>
            <td style="text-align: center; border-top: 1px solid #333; color: #348993;">$totalCant_f</td>
            <td border-top="1px solid #333;"></td>
            <td style="text-align: right; border-top: 1px solid #333; color: #27ae60;">$totalMonto_f</td>
        </tr>
    </tfoot>
EOF;
}

$html .= <<<EOF
</table>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Items_Facturados_$desde_f-$hasta_f.pdf", 'I');

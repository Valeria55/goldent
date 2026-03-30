<?php
require_once('plugins/tcpdf2/tcpdf.php');

$desde = date("d/m/Y", strtotime($_REQUEST["desde"]));
$hasta = date("d/m/Y", strtotime($_REQUEST["hasta"]));
$fechaHoy = date("d/m/Y");
$horaHoy = date("H:i");

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Informe de Ventas Sin Factura');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage('P', 'A4');

$html = <<<EOF
<style>
    .header { font-family: 'Helvetica', 'Arial', sans-serif; }
    .company-name { font-size: 18pt; font-weight: bold; margin-bottom: 0; }
    .company-info { font-size: 10pt; margin-top: 0; margin-bottom: 0; }
    .range { font-size: 11pt; margin-top: 15px; }
    .client-info { font-size: 11pt; margin-top: 5px; }
    .title { font-size: 12pt; font-weight: bold; margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background-color: #efefef; color: #333; font-weight: bold; text-align: center; border-bottom: 1px solid #333; font-size: 10pt; padding: 5px; }
    td { font-size: 9pt; padding: 5px; border-bottom: 0.5px solid #eee; }
    .total-row { font-weight: bold; background-color: #f9f9f9; }
</style>

<div class="header">
    <div class="company-name">GOLDENT S.A</div>
    <div class="company-info">Dirección: Calle Ernesto Baez y Los Rosales</div>
    <div class="company-info">Tel.: 061 571136</div>
    <hr>
    
    <div class="range"><b>Rango:</b> $desde a $hasta</div>
    <div class="client-info"><b>Cliente:</b> Varios / Todos</div>
    <div class="client-info"><b>Moneda:</b> Guaraníes</div>
    
    <div class="title">LISTA DE VENTAS SIN FACTURA:</div>
</div>

<table>
    <thead>
        <tr>
            <th width="12%">Fecha</th>
            <th width="28%">Cliente</th>
            <th width="15%">Tipo</th>
            <th width="20%">Nro. Comprobante</th>
            <th width="25%">Total (Gs)</th>
        </tr>
    </thead>
    <tbody>
EOF;

$ventas = $this->venta->ListarVentasPorComprobante($_REQUEST['desde'], $_REQUEST['hasta'], false);
$totalVenta = 0;
foreach ($ventas as $v) {
    if ($v->anulado) continue;
    $totalVenta += $v->total;
    $total_f = number_format($v->total, 0, ",", ".");
    $fecha_f = date("d/m/Y", strtotime($v->fecha_venta));
    $cliente = htmlspecialchars($v->cliente_nombre ?? 'General');
    $tipo = htmlspecialchars($v->comprobante);
    $nro = htmlspecialchars($v->nro_comprobante);

    $html .= <<<EOF
        <tr>
            <td style="text-align: center;">$fecha_f</td>
            <td>$cliente</td>
            <td style="text-align: center;">$tipo</td>
            <td style="text-align: center;">$nro</td>
            <td style="text-align: right;">$total_f</td>
        </tr>
EOF;
}

if (count($ventas) == 0) {
    $html .= '<tr><td colspan="5" style="text-align: center; padding: 20px;">Sin registros en el rango seleccionado.</td></tr>';
}

$totalG_f = number_format($totalVenta, 0, ",", ".");
$html .= <<<EOF
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" style="text-align: right; border-top: 1px solid #333;">TOTAL GENERAL:</td>
            <td style="text-align: right; border-top: 1px solid #333;">$totalG_f</td>
        </tr>
    </tfoot>
</table>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Ventas_Sin_Factura_$desde-$hasta.pdf", 'I');

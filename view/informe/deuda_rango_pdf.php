<?php
require_once('plugins/tcpdf2/tcpdf.php');

$desde = date("d/m/Y", strtotime($_REQUEST["desde"]));
$hasta = date("d/m/Y", strtotime($_REQUEST["hasta"]));
$fechaHoy = date("d/m/Y");
$horaHoy = date("H:i");

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Informe de Deudas (Cuentas por Cobrar)');
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
    
    <div class="title">LISTA DE DEUDAS (CLIENTES):</div>
</div>

<table>
    <thead>
        <tr>
            <th width="12%">Fecha</th>
            <th width="30%">Cliente</th>
            <th width="30%">Concepto</th>
            <th width="14%">Total</th>
            <th width="14%">Saldo Pend.</th>
        </tr>
    </thead>
    <tbody>
EOF;

$totalDeuda = 0;
$totalSaldo = 0;

// Obtención de datos usando el nuevo método del modelo
$deudas = $this->deuda->ListarRango($_REQUEST['desde'], $_REQUEST['hasta']);

foreach ($deudas as $d) {
    $totalDeuda += $d->monto;
    $totalSaldo += $d->saldo;
    $monto_f = number_format($d->monto, 0, ",", ".");
    $saldo_f = number_format($d->saldo, 0, ",", ".");
    $fecha_f = date("d/m/Y", strtotime($d->fecha));
    $cliente = htmlspecialchars($d->nombre);
    $concepto = htmlspecialchars($d->concepto);

    $html .= <<<EOF
        <tr>
            <td style="text-align: center;">$fecha_f</td>
            <td>$cliente</td>
            <td>$concepto</td>
            <td style="text-align: right;">$monto_f</td>
            <td style="text-align: right; font-weight: bold; color: #c0392b;">$saldo_f</td>
        </tr>
EOF;
}

if (count($deudas) == 0) {
    $html .= '<tr><td colspan="5" style="text-align: center; padding: 20px;">Sin registros en el rango seleccionado.</td></tr>';
}

$totalD_f = number_format($totalDeuda, 0, ",", ".");
$totalS_f = number_format($totalSaldo, 0, ",", ".");

$html .= <<<EOF
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" style="text-align: right; border-top: 1px solid #333;">TOTALES:</td>
            <td style="text-align: right; border-top: 1px solid #333;">$totalD_f</td>
            <td style="text-align: right; border-top: 1px solid #333; color: #c0392b;">$totalS_f</td>
        </tr>
    </tfoot>
</table>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Informe_Deudas_$desde-$hasta.pdf", 'I');

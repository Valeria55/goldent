<?php
require_once('plugins/tcpdf2/tcpdf.php');

$desde = date("d/m/Y", strtotime($_REQUEST["desde"]));
$hasta = date("d/m/Y", strtotime($_REQUEST["hasta"]));
$fechaHoy = date("d/m/Y");
$horaHoy = date("H:i");

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Resumen General de Operaciones');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage('P', 'A4');

// Data preparation
$totalI = 0;
foreach ($this->ingreso->Listar_rango($_REQUEST['desde'], $_REQUEST['hasta']) as $r) { if(!$r->anulado) $totalI += ($r->monto * ($r->cambio ?? 1)); }

$totalE = 0;
foreach ($this->egreso->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']) as $r) { if($r->categoria != "Transferencia") $totalE += $r->monto; }

$totalVF = 0;
foreach ($this->venta->ListarVentasPorComprobante($_REQUEST['desde'], $_REQUEST['hasta'], true) as $r) { if(!$r->anulado) $totalVF += $r->total; }

$totalVSF = 0;
foreach ($this->venta->ListarVentasPorComprobante($_REQUEST['desde'], $_REQUEST['hasta'], false) as $r) { if(!$r->anulado) $totalVSF += $r->total; }

$totalD = 0;
foreach ($this->deuda->ListarRango($_REQUEST['desde'], $_REQUEST['hasta']) as $r) { $totalD += $r->saldo; }

$totalA = 0;
foreach ($this->acreedor->ListarRango($_REQUEST['desde'], $_REQUEST['hasta']) as $r) { $totalA += $r->saldo; }

$balance = $totalI - $totalE;

$ti = number_format($totalI, 0, ",", ".");
$te = number_format($totalE, 0, ",", ".");
$tvf = number_format($totalVF, 0, ",", ".");
$tvsf = number_format($totalVSF, 0, ",", ".");
$td = number_format($totalD, 0, ",", ".");
$ta = number_format($totalA, 0, ",", ".");
$tb = number_format($balance, 0, ",", ".");

$totalVBrutas = number_format($totalVF + $totalVSF, 0, ",", ".");

$html = <<<EOF
<style>
    .header { font-family: 'Helvetica', 'Arial', sans-serif; }
    .company-name { font-size: 18pt; font-weight: bold; margin-bottom: 0; }
    .company-info { font-size: 10pt; margin-top: 0; margin-bottom: 0; }
    .range { font-size: 11pt; margin-top: 15px; }
    .client-info { font-size: 11pt; margin-top: 5px; }
    .title { font-size: 12pt; font-weight: bold; margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 5px; border: 1px solid #ddd; }
    th { background-color: #efefef; color: #333; font-weight: bold; text-align: left; border-bottom: 1px solid #333; font-size: 11pt; padding: 10px; }
    td { font-size: 10pt; padding: 10px; border-bottom: 0.5px solid #eee; }
    .label { width: 70%; }
    .value { width: 30%; text-align: right; font-weight: bold; }
    .total-row { background-color: #f9f9f9; font-weight: bold; }
</style>

<div class="header">
    <div class="company-name">GOLDENT S.A</div>
    <div class="company-info">Dirección: Calle Ernesto Baez y Los Rosales</div>
    <div class="company-info">Tel.: 061 571136</div>
    <hr>
    
    <div class="range"><b>Rango:</b> $desde a $hasta</div>
    <div class="client-info"><b>Reporte:</b> Resumen General de Operaciones</div>
    <div class="client-info"><b>Moneda:</b> Guaraníes</div>
</div>

<div class="title">RESUMEN FINANCIERO:</div>
<table>
    <tr>
        <td class="label">Total Ingresos de Caja:</td>
        <td class="value" style="color: #27ae60;">+ $ti Gs.</td>
    </tr>
    <tr>
        <td class="label">Total Egresos de Caja:</td>
        <td class="value" style="color: #c0392b;">- $te Gs.</td>
    </tr>
    <tr class="total-row">
        <td class="label" style="font-size: 11pt;">BALANCE (Neto Caja):</td>
        <td class="value" style="font-size: 11pt;">$tb Gs.</td>
    </tr>
</table>

<div class="title">DETALLE DE VENTAS:</div>
<table>
    <tr>
        <td class="label">Ventas Facturadas:</td>
        <td class="value">$tvf Gs.</td>
    </tr>
    <tr>
        <td class="label">Ventas Sin Factura:</td>
        <td class="value">$tvsf Gs.</td>
    </tr>
    <tr class="total-row">
        <td class="label" style="font-size: 11pt;">TOTAL VENTAS BRUTAS:</td>
        <td class="value" style="font-size: 11pt;">$totalVBrutas Gs.</td>
    </tr>
</table>

<div class="title" style="margin-top: 20px;">ESTADO DE CARTERA (PENDIENTES):</div>
<table>
    <tr>
        <td class="label">Cuentas por Cobrar (Deudas Clientes):</td>
        <td class="value" style="color: #e67e22;">$td Gs.</td>
    </tr>
    <tr>
        <td class="label">Cuentas por Pagar (Acreedores):</td>
        <td class="value" style="color: #c0392b;">$ta Gs.</td>
    </tr>
</table>

<br><br>
<p align="center" style="font-size: 9pt; color: #7f8c8d; border-top: 1px solid #eee; padding-top: 10px;">
    Este documento es un resumen informativo generado automáticamente desde el sistema de gestión.
</p>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Resumen_General_$desde-$hasta.pdf", 'I');

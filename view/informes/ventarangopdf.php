<?php

require_once('plugins/tcpdf/pdf/tcpdf_include.php');

$moneda = $this->venta_tmp->ObtenerMoneda();


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$fechahoy = date("d/m/Y");
$fechaInforme = date("d/m/Y", strtotime($_REQUEST['fecha']));
$fechadesde = date("d/m/Y", strtotime($_REQUEST['desde']));
$fechahasta = date("d/m/Y", strtotime($_REQUEST['hasta']));
$horahoy = date("H:i", strtotime("-4 HOUR"));

$inicial=number_format($moneda->monto_inicial,0,",",".");
$caja_inicial = $moneda->monto_inicial;
$real=number_format($moneda->reales,0,",",".");
$dolar=number_format($moneda->dolares,0,",",".");

$html1 = <<<EOF
		<h2 align="center">Informe de ventas desde de la fecha $fechadesde hasta $fechahasta</h2>
		<p>Generado a las $horahoy de la fecha $fechahoy</p>
		<div>
		<table width="100%">
		<tr>
		<td>
		</td>
		<td>
		
		</td>
		</tr>
		</table>
		</div>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');





$html1 = <<<EOF
		<h1 align="center">Ventas</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="11%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
                <th width="33%" style="border-left-width:1px ; border-right-width:1px">Cliente</th>
				<th width="10%" style="border-left-width:1px ; border-right-width:1px">Método</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Pago</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Costo</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Precio</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Gana..</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$totalVenta = 0;

foreach($this->model->ListarRango($_REQUEST['desde'], $_REQUEST['hasta']) as $r):

$total=number_format($r->total,0,",",".");
$costo=number_format($r->costo,0,",",".");
$ganancia=number_format(($r->total - $r->costo),0,",",".");
$hora = date("d/m/Y", strtotime($r->fecha_venta));
if($r->id_cliente != 14){
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="right">
				<td width="11%" style="border-left-width:1px ; border-right-width:1px">$hora</td>
				<td width="33%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->nombre_cli $r->apellido_cli</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->metodo</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->contado</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$costo</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$total</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$ganancia</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
}
$totalCosto += $r->costo;
$totalVenta += $r->total;

if($r->contado=='Contado'){
    $totalContado += $r->total;
}else{
    $totalCredito += $r->total;
}
endforeach;

$totalCostoV = number_format($totalCosto,0,",",".");
$totalVentaV = number_format($totalVenta,0,",",".");
$totalGananciaV = number_format(($totalVenta - $totalCosto),0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px;">
			<tr align="right">
				<td width="30%" style="border-left-width:1px">Total:</td>
				<td width="14%" style="border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$totalCostoV</b></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px"><b>$totalVentaV</b></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px"><b>$totalGananciaV</b></td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$margen = number_format((((($totalVenta - $totalCosto)*100)/$totalCosto)),2,",",".");  

$totalContadoV=number_format($totalContado,0,",",".");
$totalCreditoV=number_format($totalCredito,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total Contado (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalContadoV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total Crédito (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalCreditoV</td>
			</tr>
			
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total Ganancia (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalGananciaV</td>
			</tr>
			
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Margen Total: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$margen</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');



$pdf->Output('cierre.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
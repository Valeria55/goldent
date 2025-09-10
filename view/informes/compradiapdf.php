<?php

require_once('plugins/tcpdf/pdf/tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$fechahoy = date("d/m/Y");
$fechaInforme = date("d-m-Y", strtotime($_REQUEST['fecha']));
$horahoy = date("H:i");


$html1 = <<<EOF
		<h1 align="center">Informe de compras de la fecha $fechaInforme</h1>
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
		<h1 align="center">Compras</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px">Hora</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">Proveedor</th>
             	<th width="14%" style="border-left-width:1px ; border-right-width:1px">Comprador</th>
				<th width="10%" style="border-left-width:1px ; border-right-width:1px">Método</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Pago</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Precio compra</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Cantidad</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Total</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$totalVenta = 0;

foreach($this->model->ListarDiaItems($_REQUEST['fecha']) as $r):

$total=number_format(($r->precio_compra*$r->cantidad),0,",",".");
$costo=number_format($r->precio_compra,0,",",".");
$hora = date("H:i", strtotime($r->fecha_compra));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$hora</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->nombre_cli</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px">$r->vendedor</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->metodo</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->contado</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$costo</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$r->cantidad</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$total</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCosto += $r->precio_compra*$r->cantidad;
$totalVenta += $r->total;

if($r->contado=='Contado'){
    $totalContado += $r->precio_compra*$r->cantidad;
}else{
    $totalCredito += $r->precio_compra*$r->cantidad;
}
endforeach;

$totalCostoV = number_format($totalCosto,0,",",".");
$totalVentaV = number_format($totalVenta,0,",",".");
$totalGananciaV = number_format(($totalVenta - $totalCosto),0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="30%" style="border-left-width:1px ; border-right-width:1px">Total:</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalCostoV</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalVentaV</td>
			</tr>
		</table>

EOF;

//$pdf->writeHTML($html1, false, false, false, false, '');

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
			
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');



$pdf->Output("Informe de compras de la fecha $fechaInforme.pdf", 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
<?php

require_once('plugins/tcpdf2/tcpdf.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');
$Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

$_REQUEST['fecha'] .= '-01';
//$mes = date("m", strtotime($_REQUEST['fecha']));
$mes = $Meses[intval(date("m", strtotime($_REQUEST['fecha'])))-1];
$ano = date("Y", strtotime($_REQUEST['fecha']));
$fechaHoraHoy = date("d/m/Y H:i", strtotime("-4 HOURS"));

$fechahoy = date("d/m/Y");
$fechaInforme = date("d-m-Y", strtotime($_REQUEST['fecha']));
$horahoy = date("H:i");


$html1 = <<<EOF
		<h1 align="center">Informe de compras del mes de $mes del $ano</h1>
		<p>Generado el $fechaHoraHoy</p>
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
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px">Día</th>
                <th width="28%" style="border-left-width:1px ; border-right-width:1px">Proveedor</th>
             	<th width="32%" style="border-left-width:1px ; border-right-width:1px">Producto</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Precio compra</th>
             	<th width="6%" style="border-left-width:1px ; border-right-width:1px">Cant</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Total</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$totalVenta = 0;

foreach($this->model->ListarMesItems($_REQUEST['fecha']) as $r):

$total=number_format(($r->precio_compra*$r->cantidad),0,",",".");
$costo=number_format($r->precio_compra,0,",",".");
$hora = date("d - H:i", strtotime($r->fecha_compra));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$hora</td>
				<td width="28%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->nombre_cli</td>
				<td width="32%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->producto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$costo</td>
				<td width="6%" style="border-left-width:1px ; border-right-width:1px">$r->cantidad</td>
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



$pdf->Output("Informe de compras del mes de $mes del $ano.pdf", 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
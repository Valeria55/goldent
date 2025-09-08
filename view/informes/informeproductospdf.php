<?php

require_once('plugins/tcpdf/pdf/tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$fechahoy = date("d/m/Y", strtotime($_REQUEST['fecha']));
$horahoy = date("H:i");


$html1 = <<<EOF
		<h1 align="center">Informe de productos fecha $fechahoy</h1>
		<p>Generado a las $horahoy</p>
		<div>
		</div>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');





$html1 = <<<EOF
		<h1 align="center">Productos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:10px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">Cod.</th>
                <th width="32%" style="border-left-width:1px ; border-right-width:1px">Producto</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Precio Costo</th>
             	<th width="5%" style="border-left-width:1px ; border-right-width:1px">Local</th>
             	<th width="5%" style="border-left-width:1px ; border-right-width:1px">Dep</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Costo local</th>
				<th width="12%" style="border-left-width:1px ; border-right-width:1px">Costo depo</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Suma</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalLocal = 0;
$totalDepo = 0;

foreach($this->model->Listar() as $r):

$totalStock1 =number_format(($r->precio_costo*$r->stock),2,",",".");
$totalStock2 =number_format(($r->precio_costo*$r->stock2),2,",",".");
$suma = number_format(($r->precio_costo*$r->stock + $r->precio_costo*$r->stock2),2,",",".");
$costo=number_format($r->precio_costo,2,",",".");
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:9px">
			<tr align="center">
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$r->codigo</td>
				<td width="32%" style="border-left-width:1px ; border-right-width:1px">$r->producto</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$costo</td>
				<td width="5%" style="border-left-width:1px ; border-right-width:1px">$r->stock</td>
				<td width="5%" style="border-left-width:1px ; border-right-width:1px">$r->stock2</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalStock1</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalStock2</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$suma</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalLocal += $r->precio_costo*$r->stock;
$totalDepo += $r->precio_costo*$r->stock2;

endforeach;

$totalLocalV = number_format($totalLocal,2,",",".");
$totalDepoV = number_format($totalDepo,2,",",".");
$SumaV = number_format(($totalLocal + $totalDepo),2,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">Total:</td>
				<td width="32%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="5%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="5%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalLocalV</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalDepoV</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$SumaV</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');




$pdf->Output('productos.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
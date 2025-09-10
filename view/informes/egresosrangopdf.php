<?php

require_once('plugins/tcpdf/pdf/tcpdf_include.php');

$Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
       
$desde = date("d-m-Y", strtotime($_REQUEST["desde"]));
$hasta = date("d-m-Y", strtotime($_REQUEST["hasta"]));

$fechaHoy = date("d-m-Y");
$horaHoy = date("H:i");


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$html1 = <<<EOF
		<h1 align="center">Informe de gastos de la fecha $desde hasta $hasta</h1>
		<p>Generado el $fechaHoy a las $horaHoy</p>
		<div>
		</div>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');



/*

   INICIO GASTOS
*/

$html1 = <<<EOF
		<br>
		<h1 align="center">Gastos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
                <th width="76%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Monto</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalEgreso = 0;

foreach($this->model->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']) as $e):
if($e->categoria != "Transferencia"){
$monto=number_format($e->monto,0,",",".");
$dia = date("d", strtotime($e->fecha));
$fecha_gasto = date("d/m/Y", strtotime($e->fecha));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">$fecha_gasto</th>
				<td width="76%" style="border-left-width:1px ; border-right-width:1px" align="left">$e->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalEgreso += $e->monto;
}
endforeach;

$egreso=number_format($totalEgreso,0,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td width="87%" style="border-left-width:1px ; border-right-width:1px" align="left"><b>RESULTADO (-)</b></td>
				<td width="13%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$egreso</b></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN GASTOS

*/




$pdf->Output("Informe de gastos de la fecha $desde hasta $hasta.pdf", 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
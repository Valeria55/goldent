<?php

require_once('plugins/tcpdf/pdf/tcpdf_include.php');

$moneda = $this->venta_tmp->ObtenerMoneda();
$Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
       


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');
//$mes = date("m", strtotime($_REQUEST['fecha']));
$mes = $Meses[intval(date("m", strtotime($_REQUEST['fecha'])))-1];
$ano = date("Y", strtotime($_REQUEST['fecha']));
$fechaHoraHoy = date("d/m/Y H:i", strtotime("-4 HOURS"));

$inicial=number_format($moneda->monto_inicial,0,",",".");
$caja_inicial = $moneda->monto_inicial;
$real=number_format($moneda->reales,0,",",".");
$dolar=number_format($moneda->dolares,0,",",".");

$html1 = <<<EOF
		<h1 align="center">Balance del mes de $mes del año $ano</h1>
		<p>Generado el $fechaHoraHoy</p>
		<div>
		</div>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');





$html1 = <<<EOF
		<br>
		<h1 align="center">Ingresos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="80%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="20%" style="border-left-width:1px ; border-right-width:1px">Monto(Gs)</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalIngreso = 0;

foreach($this->model->AgrupadoMes($_REQUEST['fecha']) as $i):
$monto=number_format($i->monto,0,",",".");
if($i->categoria != "Transferencia"){
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="80%" style="border-left-width:1px ; border-right-width:1px" align="left">$i->categoria</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalIngreso += $i->monto;
    
}
endforeach;
$ingreso=number_format($totalIngreso,0,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total: </td>
				<td style="border-left-width:1px ; border-right-width:1px" align="right">$ingreso</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');


$html1 = <<<EOF
		<br>
		<h1 align="center">Gastos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="80%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="20%" style="border-left-width:1px ; border-right-width:1px">Monto (Gs)</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalEgreso = 0;

foreach($this->egreso->AgrupadoMes($_REQUEST['fecha']) as $e):
if($e->categoria != "Transferencia"){
$monto=number_format($e->monto,0,",",".");
$dia = date("d", strtotime($e->fecha));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="80%" style="border-left-width:1px ; border-right-width:1px" align="left">$e->categoria</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
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
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total: </td>
				<td style="border-left-width:1px ; border-right-width:1px" align="right">$egreso</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

$general = $totalIngreso - $totalEgreso;

$generalV = number_format($general,0,".",".");

$html1 = <<<EOF
        <br>
		<h1 align="center">Balance</h1>
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right">Saldo: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$generalV</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');


$pdf->Output("Balance del mes de $mes del año $ano.pdf", 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
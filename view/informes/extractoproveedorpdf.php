<?php

require_once('plugins/tcpdf/pdf/tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

//$fechahoy = date("d/m/Y", strtotime($_REQUEST['fecha']));
$fechahoy = date("d/m/Y");
$horahoy = date("H:i", strtotime("-4 HOUR"));

$cliente = $_REQUEST['cli'];
$id_cliente = $_REQUEST['id'];

$html1 = <<<EOF
		<br>
		<h1 align="center">Deudas con $cliente</h1>
		<h4 align="center">Generado el $fechahoy a las $horahoy</h4>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalDeuda = 0;
foreach($this->model->Listar_cliente($_REQUEST['id']) as $d):
$fecha = date("d/m/Y", strtotime($d->fecha));
$monto=number_format($d->monto,0,",",".");
$saldo=number_format($d->saldo,0,",",".");

$html1 = <<<EOF

		&nbsp;
		<h4>Fecha $fecha</h4>
		<table width"100%" style="border: 1px solid #333; font-size:10px;background-color: #348993; color: white">
			<tr align="center">
				<td width="50%" style="border-left-width:1px ; border-right-width:1px">Producto</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">Cantidad</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px">Precio</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px">Total</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

foreach($this->compra->Listar($d->id_compra) as $r):

$total=number_format($r->subtotal,0,",",".");
$precio=number_format($r->precio_compra,0,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:9px">
			<tr align="center">
				<td width="50%" style="border-left-width:1px ; border-right-width:1px">$r->producto $r->codigo</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->cantidad</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px">$precio</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px">$total</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
endforeach;

$html1 = <<<EOF
	
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="right">
				<td style="border-left-width:1px ; border-right-width:1px">$d->concepto</td>
			</tr>
			<tr align="right">
				<td style="border-left-width:1px ; border-right-width:1px">Monto: <b>$monto Gs.</b></td>
			</tr>
			<tr align="right">
				<td style="border-left-width:1px ; border-right-width:1px">Saldo: <b>$saldo Gs.</b></td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalDeuda += $d->saldo;


endforeach;
$deuda=number_format($totalDeuda,0,",",".");
$html1 = <<<EOF
		&nbsp;
		<h1 align="center"> Saldo total = $deuda Gs</h1>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

$pdf->Output('cierre.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
<?php

require_once('plugins/tcpdf/pdf/tcpdf_include.php');

$moneda = $this->venta_tmp->ObtenerMoneda();
$Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
       


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');
$_REQUEST['fecha'] .= '-01';
//$mes = date("m", strtotime($_REQUEST['fecha']));
$mes = $Meses[intval(date("m", strtotime($_REQUEST['fecha'])))-1];
$ano = date("Y", strtotime($_REQUEST['fecha']));
$fechaHoraHoy = date("d/m/Y H:i", strtotime("-4 HOURS"));

$inicial=number_format($moneda->monto_inicial,0,",",".");
$caja_inicial = $moneda->monto_inicial;
$real=number_format($moneda->reales,0,",",".");
$dolar=number_format($moneda->dolares,0,",",".");

$html1 = <<<EOF
		<h1 align="center">Informe del mes de $mes del año $ano</h1>
		<p>Generado el $fechaHoraHoy</p>
		<div>
		</div>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');





$html1 = <<<EOF
		<h1 align="center">Ventas</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px">Día</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">Cliente</th>
             	<th width="14%" style="border-left-width:1px ; border-right-width:1px">Vendedor</th>
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

foreach($this->model->ListarMesSinAnular($_REQUEST['fecha']) as $r):

$total=number_format($r->total,0,",",".");
$costo=number_format($r->costo,0,",",".");
$ganancia=number_format(($r->total - $r->costo),0,",",".");
$hora = date("d", strtotime($r->fecha_venta));
if($r->id_cliente != 14){
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$hora</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->nombre_cli $r->apellido_cli</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px">$r->vendedor</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->metodo</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->contado</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$costo</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$total</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$ganancia</td>
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
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="right">
				<td width="30%" style="border-left-width:1px ; border-right-width:1px">Total:</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalCostoV</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalVentaV</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$totalGananciaV</td>
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

$html1 = <<<EOF
		<br>
		<h1 align="center">Otros Ingresos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="8%"  style="border-left-width:1px ; border-right-width:1px">Día</th>
                <th width="80%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Monto(Gs)</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalIngreso = 0;

foreach($this->ingreso->ListarMesSinVenta($_REQUEST['fecha']) as $i):
$dia = date("d", strtotime($i->fecha));
$monto=number_format($i->monto,0,",",".");
if($i->categoria != "Transferencia"){
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			    <td width="8%" style="border-left-width:1px ; border-right-width:1px">$dia</td>
				<td width="80%" style="border-left-width:1px ; border-right-width:1px" align="left">$i->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
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
				<td style="border-left-width:1px ; border-right-width:1px">$ingreso</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF
        <br>
		<h1 align="center">Compras</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="55%" style="border-left-width:1px ; border-right-width:1px" >Proveedor</th>
             	<th width="13%" style="border-left-width:1px ; border-right-width:1px">Comprador</th>
				<th width="10%" style="border-left-width:1px ; border-right-width:1px">Método</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Pago</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Total</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCreditoCompra = 0;
$totalContadoCompra = 0;

foreach($this->compra->ListarMesSinAnular($_REQUEST['fecha']) as $r):

$total=number_format($r->subtotal,0,",",".");
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="55%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->nombre_cli</td>
				<td width="13%" style="border-left-width:1px ; border-right-width:1px">$r->vendedor</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->metodo</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->contado</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$total</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');



if($r->contado=='Contado'){
    $totalContadoCompra += $r->total;
}else{
    $totalCreditoCompra += $r->total;
}
endforeach;

$totalContadoCompraV=number_format($totalContadoCompra,0,",",".");
$totalCreditoCompraV=number_format($totalCreditoCompra,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total Contado (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalContadoCompraV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total Crédito (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalCreditoCompraV</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF
		<br>
		<h1 align="center">Gastos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="8%" style="border-left-width:1px ; border-right-width:1px">Día</th>
                <th width="80%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Monto (Gs)</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalEgreso = 0;

foreach($this->egreso->ListarSinCompraMes($_REQUEST['fecha']) as $e):
if($e->categoria != "Transferencia"){
$monto=number_format($e->monto,0,",",".");
$dia = date("d", strtotime($e->fecha));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			    <td width="8%" style="border-left-width:1px ; border-right-width:1px">$dia</td>
				<td width="80%" style="border-left-width:1px ; border-right-width:1px" align="left">$e->concepto</td>
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
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$egreso</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF
        <br>
		<h1 align="center">Resumen</h1>
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Venta al contado: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalContadoV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Venta a crédito: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalCreditoV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Otros ingresos: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$ingreso</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Compra al contado: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalContadoCompraV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Compra a crédito: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalCreditoCompraV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Gastos: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$egreso</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');


$pdf->Output('cierremes.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
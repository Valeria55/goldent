<?php

require_once('plugins/tcpdf2/tcpdf.php');

$id_cierre = $_GET['id_cierre'];
$cierre = $this->model->Obtener($id_cierre);
$id_usuario = $cierre->id_usuario;
$desde = $cierre->fecha_apertura;
$desdeV = date("d/m/Y H:i", strtotime($desde));
$hasta = $cierre->fecha_cierre;
$hastaV = date("d/m/Y H:i", strtotime($hasta));

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$fechahoy = date("d/m/Y");
$horahoy = date("H:i");

$inicial=number_format($cierre->monto_apertura,0,",",".");
$caja_inicial = $cierre->monto_apertura;
$real=number_format($cierre->cot_real,0,",",".");
$dolar=number_format($cierre->cot_dolar,0,",",".");

$html1 = <<<EOF
		<h1 align="center">Cierre nro $id_cierre</h1>
		<p>Desde $desdeV hasta $hastaV Generado a las $horahoy de la fecha $fechahoy</p>
		<div>
		<table width="100%">
		<tr>
		<td>
		<table width="60%" style="border: 1px solid #333; float:right">
			<tr>
                <th style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white; text-align:center" colspan="2">Cotización del día</th>
        	</tr>
			<tr>
				<td style="border-left-width:1px ; border-right-width:1px ; border-bottom-width:1px; text-align:center">Real </td>
				<td style="border-left-width:1px ; border-bottom-width:1px; border-right-width:1px; text-align:center">$real</td>
			</tr>
			<tr>
				<td style="border-left-width:1px ; border-right-width:1px ;  text-align:center">Dolar </td>
				<td style="border-left-width:1px ; border-right-width:1px; text-align:center">$dolar</td>
			</tr>
		</table>
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
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px">Hora</th>
                <th width="25%" style="border-left-width:1px ; border-right-width:1px">Cliente</th>
             	<th width="14%" style="border-left-width:1px ; border-right-width:1px">Vendedor</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Pago</th>
             	<th width="17%" style="border-left-width:1px ; border-right-width:1px">Precio</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Descuento</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Total</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$subtotalVenta = 0;
$totalVenta = 0;
$totalDescuento = 0;
$totalContadoEfec = 0;

foreach($this->venta->ListarRangoSinAnular($desde, $hasta, $id_usuario) as $r):
$subtotal=number_format($r->subtotal,0,",",".");
$total=number_format($r->total,0,",",".");
$descuento = $r->subtotal - $r->total;
$descuentoV = number_format($descuento,0,",",".");
$costo=number_format($r->costo,0,",",".");
$ganancia=number_format(($r->total - $r->costo),0,",",".");
$hora = date("H:i", strtotime($r->fecha_venta));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="right">
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$hora</td>
				<td width="25%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->nombre_cli</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->vendedor</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$r->contado</td>
				<td width="17%" style="border-left-width:1px ; border-right-width:1px">$subtotal</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$descuentoV</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">$total</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCosto += $r->costo;
$totalVenta += $r->total;
$totalDescuento += $descuento;
$subtotalVenta += $r->subtotal;

if($r->contado=='Contado'){
    $totalContado += $r->total;
    if($r->metodo = "efectivo"){
		$totalContadoEfec += $r->total;
	}
}else{
    $totalCredito += $r->total;
}



endforeach;

$totalVentaV = number_format($totalVenta,0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalVentaV</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

/*==============================================================
        RESUMEN DE METODOS DE PAGO
================================================================*/

$html1 = <<<EOF
	<br>
	<h1 align="center">Resúmen de métodos de pago</h1>
	<br>
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$pagos[]="";
foreach($this->metodo->Listar() as $m) {
    $pagos[''.$m->metodo.'']=0;
}

foreach($this->cierre->ListarMetodos($desde, $hasta, $id_usuario) as $r): 
      
    if($r->anulado != 1){
        $pagos[''.$r->forma_pago.'']+=$r->monto;
        $total +=$r->monto;
    }
   
endforeach; 

foreach($this->metodo->Listar() as $m): 

$metodo = number_format($pagos[''.$m->metodo.''],0,".",",");

$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="right">
				<td width="80%" style="border-left-width:1px ; border-right-width:1px">Total $m->metodo</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px">$metodo</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
         
endforeach;


/*==============================================================
        FIN RESUMEN DE METODOS DE PAGO
================================================================*/
$totalIngreso = 0;

$html1 = <<<EOF
		<br>
		<h1 align="center">Depósitos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="85%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="15%" style="border-left-width:1px ; border-right-width:1px">Monto (Gs)</th>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

foreach($this->ingreso->ListarRangoSesion($desde, $hasta, $id_usuario) as $i):

$monto = number_format($i->monto,0,",",".");
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="85%" style="border-left-width:1px ; border-right-width:1px">$i->concepto</td>
				<td width="15%" style="border-left-width:1px ; border-right-width:1px">$monto</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalIngreso += $i->monto;
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



$totalEgreso = 0;

$html1 = <<<EOF
		<br>
		<h1 align="center">Otros Egresos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="85%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="15%" style="border-left-width:1px ; border-right-width:1px">Monto (Gs)</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

foreach($this->egreso->ListarRangoSesion($desde, $hasta, $id_usuario) as $e):



$monto=number_format($e->monto,0,",",".");
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="85%" style="border-left-width:1px ; border-right-width:1px">$e->concepto</td>
				<td width="15%" style="border-left-width:1px ; border-right-width:1px">$monto</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalEgreso += $e->monto;
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



/*
VD = $subtotalVenta
CEF = $cierre->monto_cierre
CEI = $cierre->monto_apertura
OV = null
OG = null
DTO = $subtotalVenta - $totalVenta
CC = TotalContado
*/

$totalVentaV = number_format($subtotalVenta,0,",","."); //VD VISTA
$totalCierreV = number_format($cierre->monto_cierre,0,",","."); //CEF VISTA
$totalAperturaV = number_format($cierre->monto_apertura,0,",","."); //CEI VISTA
$totalDescuentoV = number_format(($subtotalVenta - $totalVenta),0,",","."); //CEI VISTA
$totalCostoV = number_format($totalCosto,0,",",".");
$totalGananciaV = number_format(($totalVenta - $totalCosto),0,",",".");
$totalContadoV=number_format(($totalContado+$cierre->monto_apertura),0,",","."); //CC VISTA
$totalCreditoV=number_format($totalCredito,0,",",".");
$diferenciaV=number_format(($cierre->monto_cierre - ($totalContado+$cierre->monto_apertura+$totalIngreso-$totalEgreso)),0,",","."); // DIFERENCIA

$html1 = <<<EOF
  <p> </p>
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total en la apertura en caja (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalAperturaV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total Venta (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalVentaV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Descuentos (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalDescuentoV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total a crédito (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalCreditoV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total a depósitos (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$ingreso</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total a egresos (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$egreso</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total al cierre en caja (Gs): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalCierreV</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');




$pdf->Output('cierre.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
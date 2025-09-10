<?php

require_once('plugins/tcpdf2/tcpdf.php');

$moneda = $this->venta_tmp->ObtenerMoneda();


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$fechahoy = date("d/m/Y");
$fechaInforme = date("d/m/Y", strtotime($_REQUEST['fecha']));
$horahoy = date("H:i");

$inicial = number_format($moneda->monto_inicial, 0, ",", ".");
$caja_inicial = $moneda->monto_inicial;
$real = number_format($moneda->reales, 0, ",", ".");
$dolar = number_format($moneda->dolares, 0, ",", ".");

$html1 = <<<EOF
		<h1 align="center">Informe de la fecha $fechaInforme</h1>
		<p>Generado a las $horahoy de la fecha $fechahoy</p>
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
				<th width="15%" style="border-left-width:1px ; border-right-width:1px">Hora</th>
				<th width="35%" style="border-left-width:1px ; border-right-width:1px">Cliente</th>
				<th width="14%" style="border-left-width:1px ; border-right-width:1px">Vendedor</th>
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

foreach ($this->model->ListarDiaSinAnular($_REQUEST['fecha']) as $r):

	$total = number_format($r->total, 0, ",", ".");
	$costo = number_format($r->costo, 0, ",", ".");
	$ganancia = number_format(($r->total - $r->costo), 0, ",", ".");
	$hora = date("H:i", strtotime($r->fecha_venta));
	$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="15%" style="border-left-width:1px ; border-right-width:1px">$hora</td>
				<td width="35%" style="border-left-width:1px ; border-right-width:1px">$r->nombre_cli $r->apellido_cli</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px">$r->vendedor</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$costo</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$total</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$ganancia</td>
			</tr>
		</table>

EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');

	$totalCosto += $r->costo;
	$totalVenta += $r->total;

	if ($r->contado == 'Contado') {
		$totalContado += $r->total;
	} else {
		$totalCredito += $r->total;
	}
endforeach;

$totalCostoV = number_format($totalCosto, 0, ",", ".");
$totalContadoV = number_format($totalContado, 0, ",", ".");
$totalCreditoV = number_format($totalCredito, 0, ",", ".");
$totalVentaV = number_format($totalVenta, 0, ",", ".");
$totalGananciaV = number_format(($totalVenta - $totalCosto), 0, ",", ".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
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


$html1 = <<<EOF
<br><br>
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



/*==============================================================
		RESUMEN DE METODOS DE PAGO
================================================================*/

$html1 = <<<EOF
	<br>
	<h1 align="center">Resúmen de métodos de pago</h1>
	<br>
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$pagos[] = "";
foreach ($this->metodo->Listar() as $m) {
	$pagos['' . $m->metodo . ''] = 0;
}


foreach ($this->cierre->ListarMovimientosDia($_REQUEST['fecha']) as $r):

	if ($r->anulado != 1) {
		$pagos['' . $r->forma_pago . ''] += $r->monto;
		$total += $r->monto;
	}

endforeach;

foreach ($this->metodo->Listar() as $m):

	$metodo = number_format($pagos['' . $m->metodo . ''], 0, ".", ",");

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

$html1 = <<<EOF
		<br>
		<h1 align="center">Otros Ingresos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
				<th style="border-left-width:1px ; border-right-width:1px">Concepto</th>
				<th style="border-left-width:1px ; border-right-width:1px">Monto</th>
				<th style="border-left-width:1px ; border-right-width:1px">Moneda</th>
				<th style="border-left-width:1px ; border-right-width:1px">Monto en Gs</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalIngreso = 0;


foreach ($this->ingreso->ListarSinVenta($_REQUEST['fecha']) as $i):
	$monto = number_format($i->monto, 0, ",", ".");
	$monto_guaranies_format = number_format($i->monto_guaranies, 0, ",", ".");
	$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$i->concepto</td>
				<td style="border-left-width:1px ; border-right-width:1px">$monto</td>
				<td style="border-left-width:1px ; border-right-width:1px">$i->moneda</td>
				<td style="border-left-width:1px ; border-right-width:1px">$monto_guaranies_format</td>
			</tr>
		</table>
EOF;
	$pdf->writeHTML($html1, false, false, false, false, '');
	$totalIngreso += $i->monto_guaranies;
endforeach;
$ingreso = number_format($totalIngreso, 0, ",", ".");
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
				<th style="border-left-width:1px ; border-right-width:1px">Cliente</th>
				<th style="border-left-width:1px ; border-right-width:1px">Total (GS)</th>
				<th style="border-left-width:1px ; border-right-width:1px">Vendedor</th>
				<th style="border-left-width:1px ; border-right-width:1px">Método</th>
				<th style="border-left-width:1px ; border-right-width:1px">Pago</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCreditoCompra = 0;
$totalContadoCompra = 0;

foreach ($this->compra->ListarDiaSinAnular($_REQUEST['fecha']) as $r):

	$total = number_format($r->subtotal, 0, ",", ".");
	$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$r->nombre_cli $r->apellido_cli</td>
				<td style="border-left-width:1px ; border-right-width:1px">$total</td>
				<td style="border-left-width:1px ; border-right-width:1px">$r->vendedor</td>
				<td style="border-left-width:1px ; border-right-width:1px">$r->metodo</td>
				<td style="border-left-width:1px ; border-right-width:1px">$r->contado</td>
			</tr>
		</table>

EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');



	if ($r->contado == 'Contado') {
		$totalContadoCompra += $r->total;
	} else {
		$totalCreditoCompra += $r->total;
	}
endforeach;

$totalContadoCompraV = number_format($totalContadoCompra, 0, ",", ".");
$totalCreditoCompraV = number_format($totalCreditoCompra, 0, ",", ".");

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
		<h1 align="center">Otros Egresos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
				<th style="border-left-width:1px ; border-right-width:1px">Concepto</th>
				<th style="border-left-width:1px ; border-right-width:1px">Monto</th>
				<th style="border-left-width:1px ; border-right-width:1px">Moneda</th>
				<th style="border-left-width:1px ; border-right-width:1px">Monto en Gs</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalEgreso = 0;


foreach ($this->egreso->ListarSinCompra($_REQUEST['fecha']) as $e):
	$monto = number_format($e->monto, 0, ",", ".");
	$monto_guaranies_format = number_format($e->monto_guaranies, 0, ",", ".");
	$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$e->concepto</td>
				<td style="border-left-width:1px ; border-right-width:1px">$monto</td>
				<td style="border-left-width:1px ; border-right-width:1px">$e->moneda</td>
				<td style="border-left-width:1px ; border-right-width:1px">$monto_guaranies_format</td>
			</tr>
		</table>
EOF;
	$pdf->writeHTML($html1, false, false, false, false, '');
	$totalEgreso += $e->monto_guaranies;
endforeach;

$egreso = number_format($totalEgreso, 0, ",", ".");
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
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Otros egresos: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$egreso</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');


//============================================================+
// MOVIMIENTOS DE CAJAS
//============================================================+


$html1 = <<<EOF
		<br>
		<h1 align="center">Movimientos Entre Cajas</h1>

		<h2 align="left">Enviado</h2>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
				<th style="border-left-width:1px ; border-right-width:1px">Concepto</th>
				<th style="border-left-width:1px ; border-right-width:1px">Comprobante</th>
				<th style="border-left-width:1px ; border-right-width:1px">Fecha</th>
				<th style="border-left-width:1px ; border-right-width:1px">Forma Pago</th>
				<th style="border-left-width:1px ; border-right-width:1px">Monto</th>
				<th style="border-left-width:1px ; border-right-width:1px">Moneda</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$total_monto_enviado = 0;

foreach ($this->egreso->ListarMovimientoCajaDia($_REQUEST['fecha']) as $e):

	$total_monto_enviado += $e->monto;
	$monto_format = number_format($e->monto, 0, ",", ".");


	$html1 = <<<EOF
			
			<table width"100%" style="border: 1px solid #333; font-size:10px">
				<tr align="center">
					
					<td style="border-left-width:1px ; border-right-width:1px">$e->concepto</td>
					<td style="border-left-width:1px ; border-right-width:1px">$e->comprobante</td>
					<td style="border-left-width:1px ; border-right-width:1px">$e->fecha</td>
					<td style="border-left-width:1px ; border-right-width:1px">$e->forma_pago</td>
					<td style="border-left-width:1px ; border-right-width:1px">$monto_format</td>
					<td style="border-left-width:1px ; border-right-width:1px">$e->moneda</td>
				</tr>
			</table>
	
	EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');

endforeach;

$html1 = <<<EOF
	<br>

	<h2 align="left">Recibido</h2>

	<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
		<tr align="center">
			
			<th style="border-left-width:1px ; border-right-width:1px">Concepto</th>
			<th style="border-left-width:1px ; border-right-width:1px">Comprobante</th>
			<th style="border-left-width:1px ; border-right-width:1px">Fecha</th>
			<th style="border-left-width:1px ; border-right-width:1px">Forma Pago</th>
			<th style="border-left-width:1px ; border-right-width:1px">Monto</th>
			<th style="border-left-width:1px ; border-right-width:1px">Moneda</th>
		</tr>
	</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$total_monto_recibido = 0;

foreach ($this->ingreso->ListarMovimientoCajaDia($_REQUEST['fecha']) as $e):

	$total_monto_recibido += $e->monto;
	$monto_format = number_format($e->monto, 0, ",", ".");



	$html1 = <<<EOF
			
			<table width"100%" style="border: 1px solid #333; font-size:10px">
				<tr align="center">
					
					<td style="border-left-width:1px ; border-right-width:1px">$e->concepto</td>
					<td style="border-left-width:1px ; border-right-width:1px">$e->comprobante</td>
					<td style="border-left-width:1px ; border-right-width:1px">$e->fecha</td>
					<td style="border-left-width:1px ; border-right-width:1px">$e->forma_pago</td>
					<td style="border-left-width:1px ; border-right-width:1px">$monto_format</td>
					<td style="border-left-width:1px ; border-right-width:1px">$e->moneda</td>
				</tr>
			</table>
	
	EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');

endforeach;

//============================================================+
// FIN DE MOVIMIENTOS DE CAJAS
//============================================================+



ob_end_clean();
$pdf->Output('cierre.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+

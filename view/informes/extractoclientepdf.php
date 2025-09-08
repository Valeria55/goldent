<?php

require_once('plugins/tcpdf2/tcpdf.php');

$desde = date("d-m-Y", strtotime($_REQUEST["desde"]));
$hasta = date("d-m-Y", strtotime($_REQUEST["hasta"]));

class MYPDF extends TCPDF
{

	//Page header


	// Page footer
	public function Footer()
	{
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages() . " - Generado el " . date("d/m/Y \a \l\a\s H:i") . " hs.", 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');
$pag_vacia = true;

//$fechahoy = date("d/m/Y", strtotime($_REQUEST['fecha']));
$fechahoy = date("d/m/Y");
$horahoy = date("H:i");


/* ================================ 
	ESTILOS PARA LAS TABLAS
================================ */
$body_table_style = 'font-size:8px; border-top: 1px solid #ccc; border-bottom: .5px solid #ccc;';
$header_table_style = 'border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:8.5px; background-color: #c0c0c0; color: #202020; font-weight: bold;';
// ********************************



$html1 = <<<EOF
	
		<h4 align="center">Extracto de $cli->nombre</h4>
		<br>
		

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	LISTA DE COMPRAS DEL CLIENTE
================================ */


$totalDeuda = 0;
foreach($this->model->Listar_cliente($_REQUEST['id'],$_REQUEST['desde'],$_REQUEST['hasta']) as $d):
$fecha = date("d/m/Y", strtotime($d->fecha));
$monto=number_format($d->monto,0,",",".");
$saldo=number_format($d->saldo,0,",",".");


$html1 = <<<EOF

	
		<h5>Fecha $fecha</h5>
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="50%" style=" ">Concepto</td>
				<td width="10%" style=" ">Cantidad</td>
				<td width="20%" style=" ">Precio</td>
				<td width="20%" style=" ">Total</td>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['vent_credito'])){
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
} 

if( !($d->id_venta > 0)){
	$producto = $d->concepto;

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$total=number_format($d->monto,0,",",".");
	$precio=number_format($r->precio_venta,0,",",".");
	
	$html1 = <<<EOF
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
				<td width="50%" style="">$producto</td>
				<td width="10%" style="">1</td>
				<td width="20%" style="">$total GS</td>
				<td width="20%" style="" align="right">$total GS</td>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['vent_credito'])){
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
}

$indice++;

}
foreach($this->venta->ListarExtracto($d->id_venta, $desde, $hasta) as $r):


	$producto = ($d->id_venta > 0) ? $r->producto." ".$r->codigo : $d->concepto;

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$total=number_format($r->total,0,",",".");
	$precio=number_format($r->precio_venta-$r->descuento,0,",",".");
	
	$html1 = <<<EOF
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
				<td width="50%" style="">$producto</td>
				<td width="10%" style="">$r->cantidad</td>
				<td width="20%" style="">$precio GS</td>
				<td width="20%" style="" align="right">$total GS</td>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['vent_credito'])){
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
}

$indice++;

endforeach;

$in = $this->ingreso->ObtenerVenta($d->id_venta);

$monto_entrega = $in->monto;
$monto_entrega =number_format($monto_entrega,0,",",".");



$html1 = <<<EOF
	
		<table width"100%" style="border-bottom: .5px solid #ccc; font-size:8px">
			<tr align="right">
				<td style=" ">$d->concepto</td>
			</tr>
			<tr align="right">
				<td style=" ">Monto: <b>$monto GS.</b></td>
			</tr>
			<tr align="right">
				<td style=" ">Saldo actual: <b>$saldo GS.</b></td>
			</tr>
		</table>
		<br><br>
EOF;

if (isset($_REQUEST['items_informe']['vent_credito'])){
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
}

$totalDeuda += $d->saldo;

endforeach;

$deuda=number_format($totalDeuda,0,",",".");

$html1 = <<<EOF
	
		<h3 align="center"> Saldo total = $deuda GS</h3>

EOF;

if (isset($_REQUEST['items_informe']['vent_credito']))$pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	FIN
================================ */



/* ================================ 
	PAGOS Y DEUDAS
================================ */

$sum_cred = 0;
$sum_deb = 0;
$sum_saldo = 0;
// $cliente = $this->model->Listar_cliente($_REQUEST['id'],$_REQUEST['desde'],$_REQUEST['hasta']) ;
$fecha = date("d/m/Y", strtotime($d->fecha));
$monto=number_format($d->monto,0,",",".");
$saldo=number_format($d->saldo,0,",",".");


$html1 = <<<EOF

	
		<h5>Fecha $fecha</h5>
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="13%" style=" ">Fecha</td>
				<td width="14%" style=" ">Comprobante</td>
				<td width="13%" style=" "></td>
				<td width="20%" style=" ">Débitos</td>
				<td width="20%" style=" ">Créditos</td>
				<td width="20%" style=" ">Saldo</td>
			</tr>
		</table>

EOF;



if (isset($_REQUEST['items_informe']['pagos_client'])){
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
} 


foreach($this->model->ListarExtracto($_REQUEST['id'], $desde, $hasta) as $r):


	$concepto = (!$r->categoria) ? 'Compra a crédito' : $r->categoria;

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$fecha = date("d/m/Y", strtotime($r->fecha));

	$total=number_format($r->subtotal,0,",",".");
	$precio=number_format($r->precio_venta,0,",",".");
	
	$html1 = <<<EOF
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">

				<td width="13%" style=" ">$fecha</td>
				<td width="14%" style=" "></td>
				<td width="13%" style=" ">$concepto</td>
				<td width="20%" style=" ">Débitos</td>
				<td width="20%" style=" ">Créditos</td>
				<td width="20%" style=" ">Saldo</td>

			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['pagos_client'])){
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
} 
$indice++;

endforeach;

$in = $this->ingreso->ObtenerVenta($d->id_venta);

$monto_entrega = $in->monto;
$monto_entrega =number_format($monto_entrega,0,",",".");



$html1 = <<<EOF
	
		<table width"100%" style="border-bottom: .5px solid #ccc; font-size:8px">
			<tr align="right">
				<td style=" ">$d->concepto</td>
			</tr>
			<tr align="right">
				<td style=" ">Monto: <b>$monto GS.</b></td>
			</tr>
			<tr align="right">
				<td style=" ">Saldo actual: <b>$saldo GS.</b></td>
			</tr>
		</table>
		<br><br>
EOF;

if (isset($_REQUEST['items_informe']['pagos_client'])){
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
} 

$totalDeuda += $d->saldo;


$deuda=number_format($totalDeuda,0,",",".");
$html1 = <<<EOF
	
		<h3 align="center"> Saldo total = $deuda GS</h3>

EOF;

if (isset($_REQUEST['items_informe']['pagos_client'])){
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
} 

/* ================================ 
	FIN
================================ */


$pdf->Output('extracto.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
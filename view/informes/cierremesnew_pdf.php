<?php

// require_once('plugins/tcpdf/pdf/tcpdf_include.php');
require_once('plugins/tcpdf2/tcpdf.php');

// $moneda = $this->venta_tmp->ObtenerMoneda();
$Meses = array(
	'Enero',
	'Febrero',
	'Marzo',
	'Abril',
	'Mayo',
	'Junio',
	'Julio',
	'Agosto',
	'Septiembre',
	'Octubre',
	'Noviembre',
	'Diciembre'
);

$desde = date("d-m-Y", strtotime($_REQUEST["desde"]));
$hasta = date("d-m-Y", strtotime($_REQUEST["hasta"]));


/* HEREDAR LA CLASE PARA PODER SOBREESCRIBIR EL HEADER Y FOOTER POR DEFECTO */

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{

	//Page header
	// public function Header()
	// {
	// 	// Logo
	// 	// $image_file = K_PATH_IMAGES . 'logo_example.jpg';
	// 	// $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	// 	// // Set font
	// 	$this->SetFont('helvetica', 'B', 20);
	// 	// Title
	// 	$this->writeHTMLCell(
	// 		$w = 0,
	// 		$h = 10,
	// 		$x = '',
	// 		$y = '',
	// 		"<small>Documento generado el ". date("d/m/Y H:i") . "</small>",
	// 		$border = 0,
	// 		$ln = 1,
	// 		$fill = 0,
	// 		$reseth = true,
	// 		$align = 'center',
	// 		$autopadding = true
	// 	);
	// 	// $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
	// }

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
// $_REQUEST['fecha'] .= '-01';
//$mes = date("m", strtotime($_REQUEST['fecha']));
// $mes = $Meses[intval(date("m", strtotime($_REQUEST['fecha'])))-1];
// $ano = date("Y", strtotime($_REQUEST['fecha']));
$fechaHoraHoy = date("d/m/Y \a \l\a\s H:i \h\s");

// $inicial=number_format($moneda->monto_inicial,0,",",".");
// $caja_inicial = $moneda->monto_inicial;
// $real=number_format($moneda->reales,0,",",".");
// $dolar=number_format($moneda->dolares,0,",",".");


/* ================================ 
	ESTILOS PARA LAS TABLAS
================================ */
$body_table_style = 'font-size:8px; border-top: 1px solid #ccc; border-bottom: .5px solid #ccc;';
$header_table_style = 'border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:8.5px; background-color: #c0c0c0; color: #202020; font-weight: bold;';
// ********************************

/* ================================ 
	INICIALIZACIÓN DE VARIABLES
================================ */
$totalCobrosDeudas = 0;
$totalCobrosDeudasG = 0;
$cobrosDeudas = '0';
$cobrosDeudasG = '0';
$totalEgresoReporte = 0;
$totalEgresoGs = 0;
$totalEgresoUsd = 0;
$totalEgresoReal = 0;
// ********************************

$html1 = <<<EOF
		<h1 align="center">ScoreCar Pro - Centro de Instalaciones</h1>
		<h3 align="center">Informe de la fecha $desde hasta $hasta</h3>
		<p>Generado el $fechaHoraHoy</p>
	

	EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

//Traer array y llamar a funciones por separado
// call_user_func(array($controller, $accion));


/*------------------------
	VENTAS contado - AGRUPADO VENTA
	-------------------------*/
$html1 = <<<EOF
		<h1 align="center">Ventas al contado</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			<th width="18%" style=""><b>Fecha</b></th>
                <th width="13%" style=""><b>Vendedor</b></th>
                <th width="22%" style=""><b>Cliente</b></th>
				<th width="14%" style="" align="right"><b>Venta</b></th>
				<th width="14%" style="" align="right"><b>Costo</b></th>
				<th width="10%" style="" align="right"><b>Utilidad</b></th>
				<th width="8%" style="" align="right"><b>%</b></th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['vent_cont'])) {
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
}

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$totalVenta = 0;


$sumvv = 0;
$sumcv = 0;
$sumuv = 0;

$indice = 0;
foreach ($this->model->AgrupadoVenta($_REQUEST['desde'], $_REQUEST['hasta']) as $v) :

	$totalv = number_format($v->total, 0, ",", ".");
	$costov = number_format($v->costo, 0, ",", ".");
	$utilidadv = number_format($v->total - $v->costo, 0, ",", ".");
	$sumvv += $v->total;
	$sumcv += $v->costo;
	$sumuv += $v->total - $v->costo;
	$porventa = number_format(((($v->total - $v->costo) * 100) / $v->total), 2, ",", ".");

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			<th width="18%" style="" align="left">$v->fecha_venta</th>
                <th width="13%" style=" font-size:8px; " align="center">$v->vendedor ($v->id_venta)</th>
                <th width="22%" style="" align="left">$v->nombre</th>
                <th width="14%" style="" align="right">$totalv</th>
             	<th width="14%" style="" align="right">$costov</th>
             	<th width="10%" style="" align="right">$utilidadv</th>
             	<th width="8%" style="" align="right">$porventa %</th>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['vent_cont'])) $pdf->writeHTML($html1, false, false, false, false, '');

	$indice++;
endforeach;
$g = $sumuv; //ganancia bruta
$gv = $sumvv; //venta
$gc = $sumcv; //costo

$sumvv = number_format($sumvv, 0, ",", ".");
$sumcv = number_format($sumcv, 0, ",", ".");
$sumuv = number_format($sumuv, 0, ",", ".");

$gv = ($gv != 0) ? $gv : 1;
$porventas = number_format(((($gv - $gc) * 100) / $gv), 2, ",", ".");

$html1 = <<<EOF
		
		<table width"100%" style="$header_table_style" align="left">
			<tr align="center" style="padding:10px">
                <th width="53%" style="" align="left";><b>RESULTADOS (+)</b></th>
                <th width="14%" style="" align="right"><b>$sumvv</b></th>
             	<th width="14%" style="" align="right"><b>$sumcv</b></th>
             	<th width="10%" style="" align="right"><b>$sumuv</b></th>
             	<th width="8%" style="" align="right"><b>$porventas %</b></th>
			</tr>
		</table>
		<br><br>

EOF;

if (isset($_REQUEST['items_informe']['vent_cont'])) $pdf->writeHTML($html1, false, false, false, false, '');
/* ================================ 
	fin agrupado ventas
	================================ */

/*------------------------
	VENTAS A CREDITO - AGRUPADO VENTA CREDITO  
	-------------------------*/
if (isset($_REQUEST['items_informe']['vent_cred'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}

$html1 = <<<EOF
		<h1 align="center">Ventas a crédito</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			<th width="18%" style=""><b>Fecha</b></th>
                <th width="13%" style=""><b>Vendedor</b></th>
                <th width="22%" style=""><b>Cliente</b></th>
				<th width="14%" style="" align="right"><b>Venta</b></th>
				<th width="14%" style="" align="right"><b>Costo</b></th>
				<th width="10%" style="" align="right"><b>Utilidad</b></th>
				<th width="8%" style="" align="right"><b>%</b></th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['vent_cred'])) {
	$pdf->writeHTML($html1, false, false, false, false, '');
	$pag_vacia = false;
}

$totalCreditoC = 0;
$totalContadoC = 0;
$totalCostoC = 0;
$totalVentaC = 0;

$sumvvC = 0;
$sumcvC = 0;
$sumuvC = 0;

$indice = 0;
foreach ($this->model->AgrupadoVentaCredito($_REQUEST['desde'], $_REQUEST['hasta']) as $v) :

	$totalvC = number_format($v->total, 0, ",", ".");
	$costovC = number_format($v->costo, 0, ",", ".");
	$utilidadvC = number_format($v->total - $v->costo, 0, ",", ".");
	$sumvvC += $v->total;
	$sumcvC += $v->costo;
	$sumuvC += $v->total - $v->costo;
	$porventaC = number_format(((($v->total - $v->costo) * 100) / $v->total), 2, ",", ".");

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			<th width="18%" style="" align="left">$v->fecha_venta</th>
                <th width="13%" style=" font-size:8px; " align="center">$v->vendedor ($v->id_venta)</th>
                <th width="22%" style="" align="left">$v->nombre</th>
                <th width="14%" style="" align="right">$totalvC</th>
             	<th width="14%" style="" align="right">$costovC</th>
             	<th width="10%" style="" align="right">$utilidadvC</th>
             	<th width="8%" style="" align="right">$porventaC %</th>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['vent_cred'])) $pdf->writeHTML($html1, false, false, false, false, '');

	$indice++;
endforeach;

$gC = $sumuvC; //ganancia bruta
$gvC = $sumvvC; //venta
$gcC = $sumcvC; //costo

$sumvvC = number_format($sumvvC, 0, ",", ".");
$sumcvC = number_format($sumcvC, 0, ",", ".");
$sumuvC = number_format($sumuvC, 0, ",", ".");

$gvC = ($gvC != 0) ? $gvC : 1;
$porventasC = number_format(((($gvC - $gcC) * 100) / $gvC), 2, ",", ".");

$html1 = <<<EOF
		
		<table width"100%" style="$header_table_style" align="left">
			<tr align="center" style="padding:10px">
                <th width="53%" style="" align="left";><b>RESULTADOS (+)</b></th>
                <th width="14%" style="" align="right"><b>$sumvvC</b></th>
             	<th width="14%" style="" align="right"><b>$sumcvC</b></th>
             	<th width="10%" style="" align="right"><b>$sumuvC</b></th>
             	<th width="8%" style="" align="right"><b>$porventasC %</b></th>
			</tr>
		</table>
		<br><br>

EOF;

if (isset($_REQUEST['items_informe']['vent_cred'])) $pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	fin agrupado ventas a credito
	================================ */

/*------------------------
	COMPRA VENTA POR PRODUCTO
	MOVIMIENTOS DE PRODUCTOS
	-------------------------*/
if (
	isset($_REQUEST['items_informe']['mov_prod'])
) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<h1 align="center">Movimientos de cada producto</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="13%" style="" align="left"><b>Cod.</b></th>
                <th width="35%"  style="" align="left"><b>Producto</b></th>
                <th width="8%" style="" align="left"><b>Cant. Compras</b></th>
                <th width="14%"  style="" align="left"><b>Tot. Compras (Gs.)</b></th>
                <th width="8%" style="" align="left"><b>Cant. Ventas</b></th>
                <th width="14%"  style="" align="left"><b>Tot. Ventas (Gs.)</b></th>
             	<th width="9%" style="" align="left"><b>Ganancia (%)</b></th>
			</tr>
		</table>
EOF;

if (isset($_REQUEST['items_informe']['mov_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

$total_cantidad_compras = 0;
$total_monto_compras = 0;
$total_cantidad_ventas = 0;
$total_monto_ventas = 0;

$indice = 0; //indice para saber si la fila es par o impar
foreach ($this->model->CompraVentaPorProducto($_REQUEST['desde'], $_REQUEST['hasta']) as $r):
	//format para numeros
	$cantidad_compra = number_format(($r->cantidad_compra ?? 0), 0, ",", ".");
	$total_compra = number_format(($r->total_compra ?? 0), 0, ",", ".");

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$cantidad_venta = number_format(($r->cantidad_venta ?? 0), 0, ",", ".");
	$total_venta = number_format(($r->total_venta ?? 0), 0, ",", ".");

	//totalizar
	$total_cantidad_compras += ($r->cantidad_compra ?? 0);
	$total_monto_compras += ($r->total_compra ?? 0);
	$total_cantidad_ventas += ($r->cantidad_venta ?? 0);
	$total_monto_ventas += ($r->total_venta ?? 0);


	$porc_ganancia = number_format(($r->porcentaje_ganancia ?? 0), 2, ",", ".");

	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			    <td width="13%" style="" align="left">$r->codigo</td>
                <td width="35%" style=" font-size:7.3px" align="left">$r->producto</td>
                <td width="8%" style="" align="left">$cantidad_compra</td>
                <td width="14%" style="" align="left">$total_compra</td>
             	<td width="8%" style="" align="left">$cantidad_venta</td>
             	<td width="14%" style="" align="left">$total_venta</td>
             	<td width="9%" style="" align="left">$porc_ganancia %</td>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['mov_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

	$indice += 1;

endforeach;

// format de totalizacion
$total_cantidad_compras_f = number_format(($total_cantidad_compras ?? 0), 0, ",", ".");
$total_monto_compras_f = number_format(($total_monto_compras ?? 0), 0, ",", ".");
$total_cantidad_ventas_f = number_format(($total_cantidad_ventas ?? 0), 0, ",", ".");
$total_monto_ventas_f = number_format(($total_monto_ventas ?? 0), 0, ",", ".");

$html1 = <<<EOF
		
	<table width"100%" style="$header_table_style">
			
			<tr align="left" style="">
                <th width="48%" style=" color: #101010 " ;><b>SUMATORIAS</b></th>
                <th width="8%" style="" ><b>$total_cantidad_compras_f</b></th>
             	<th width="14%" style="" ><b>$total_monto_compras_f</b></th>
             	<th width="8%" style="" ><b>$total_cantidad_ventas_f</b></th>
             	<th width="14%" style="" ><b>$total_monto_ventas_f</b></th>
             	<th width="9%" style="" ><b></b></th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['mov_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	fin movimientos por producto
================================ */




/*------------------------
	VENTAS AGRUPADO PRODUCTO
	-------------------------*/
if (isset($_REQUEST['items_informe']['ven_prod'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}

$html1 = <<<EOF
		<h1 align="center">Ventas por producto</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="7%" style="">Cod.</th>
                <th width="40%" style="">Producto</th>
                <th width="5%" style="">Ca</th>
                <th width="14%" style="">Venta</th>
             	<th width="14%" style="">Costo</th>
             	<th width="11%" style="">Utilidad</th>
             	<th width="9%" style="">%</th>
			</tr>
		</table>
EOF;

if (isset($_REQUEST['items_informe']['ven_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$totalVenta = 0;

$indice = 0;
foreach ($this->model->AgrupadoProductoVenta($_REQUEST['desde'], $_REQUEST['hasta']) as $r):

	$total = number_format($r->total, 0, ",", ".");
	$u = number_format($r->precio_venta, 0, ",", ".");
	$costo = number_format($r->costo, 0, ",", ".");
	$fecha_venta = date("d/m/Y ", strtotime($r->fecha_venta));
	$cantidad = number_format($r->cantidad, 0, ",", ".");
	$ganancia = number_format(($r->total - $r->costo), 0, ",", ".");

	$por = ((($r->total - $r->costo) * 100));

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$porcentaje = number_format(((($r->total - $r->costo) * 100) / $r->total), 2, ",", ".");

	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			    <th width="7%" style=" " align="left">$r->codigo</th>
                <th width="40%" style="" align="left">$r->producto <b>($u)</b></th>
                <th width="5%" style="" align="right">$cantidad</th>
                <th width="14%" style="" align="right">$total</th>
             	<th width="14%" style="" align="right">$costo</th>
             	<th width="11%" style="" align="right">$ganancia</th>
             	<th width="9%" style=" " align="left">$porcentaje %</th>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['ven_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

	$totalCosto += $r->costo;
	$totalVenta += $r->total;

	if ($r->contado == 'Contado') {
		$totalContado += $r->total;
	} else {
		$totalCredito += $r->total;
	}

	$indice++;
endforeach;

$totalCostoV = number_format($totalCosto, 0, ",", ".");
$totalVentaV = number_format($totalVenta, 0, ",", ".");
$totalGananciaV = number_format(($totalVenta - $totalCosto), 0, ",", ".");

$totalVenta = ($totalVenta != 0) ? ($totalVenta) : 1;
$porcentaje = number_format(((((($totalVenta - $totalCosto) * 100) / $totalVenta))), 2, ",", ".");

$html1 = <<<EOF
		
	<table width"100%" style="$header_table_style">
			<tr align="center" style="padding:10px">
                <th width="52%" style=" " align="left";><b>RESULTADOS (+)</b></th>
                <th width="14%" style="" align="right"><b>$totalVentaV</b></th>
             	<th width="14%" style="" align="right"><b>$totalCostoV</b></th>
             	<th width="11%" style="" align="right"><b>$totalGananciaV</b></th>
             	<th width="9%" style=" " align="left"><b>$porcentaje %</b></th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['ven_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');
/* ================================ 
	fin de ventas por producto
================================ */


/* ================================ 
	CLIENTES QUE MAS COMPRAN
================================ */

if (isset($_REQUEST['items_informe']['cli_comp'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<br>
		<h1 align="center">Clientes con más compras</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="25%" style="">Nombre</th>
			    <th width="10%" style="">RUC</th>
			    <th width="17%" style="">Dirección</th>
                <th width="10%" style="">Teléfono</th>
             	<th width="15%" style="" align="right">Total Compras</th>
             	<th width="15%" style="" align="right">Utilidad Total</th>
             	<th width="8%" style="" align="center">Margen Gan. (%)</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['cli_comp'])) $pdf->writeHTML($html1, false, false, false, false, '');

$total_ventas = 0;
$total_utilidad = 0;
$total_costo = 0;

$indice = 0;

foreach ($this->venta->ClientesVentas($_REQUEST['desde'], $_REQUEST['hasta'], 'DESC') as $i) :

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$total_formatted = number_format($i->total, 0, ",", ".");
	$utilidad_formatted = number_format($i->utilidad, 0, ",", ".");
	$margen_porc_formatted = number_format($i->margen_ganancia, 2, ",", ".");

	$nombre_cliente = $i->nombre_cliente ?? 'Cliente Ocasional';

	$total_ventas += $i->total;
	$total_utilidad += $i->utilidad;
	$total_costo += $i->costo;

	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="">
			    <th width="25%" style="">$nombre_cliente</th>
			    <th width="10%" style="">$i->ruc</th>
			    <th width="17%" style="">$i->direccion</th>
                <th width="10%" style="">$i->telefono</th>
             	<th width="15%" align="right" style="">$total_formatted</th>
             	<th width="15%" align="right" style="">$utilidad_formatted</th>
             	<th width="8%" align="right" style="">$margen_porc_formatted%</th>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['cli_comp'])) $pdf->writeHTML($html1, false, false, false, false, '');

	$indice++;
endforeach;

$total_ventas_formatted = number_format($total_ventas, 0, ",", ".");
$total_utilidad_formatted = number_format($total_utilidad, 0, ",", ".");

$total_ventas = ($total_ventas != 0) ? $total_ventas : 1;
$margen_total = ($total_ventas - $total_costo) / $total_ventas * 100;
$margen_total_formatted = number_format($margen_total, 2, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="62%" style="" align="left">RESULTADO (+)</td>
				<td width="15%" style="" align="right">$total_ventas_formatted</td>
				<td width="15%" style="" align="right">$total_utilidad_formatted</td>
				<td width="8%" style="" align="right">$margen_total_formatted%</td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['cli_comp'])) $pdf->writeHTML($html1, false, false, false, false, '');


/*

   FIN CLIENTES QUE MAS COMPRAN

*/



/* ================================ 
	VENDEDORES QUE MAS VENDIERON
================================ */
// var_dump($pag_vacia); //die;
// var_dump(isset($_REQUEST['items_informe']['vent_vend'])); die;

if (isset($_REQUEST['items_informe']['vent_vend'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}

$html1 = <<<EOF
		<br>
		<h1 align="center">Ventas por vendedores</h1>
		<p></p>
		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="27%" style="">Usuario</th>
			    <th width="30%" align="right" style="">Ventas Totales</th>
			    <th width="30%" align="right" style="">Utilidad Total</th>
			    <th width="13%" align="right" style="">Margen de Gan. (%)</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['vent_vend'])) $pdf->writeHTML($html1, false, false, false, false, '');

$total_ventas = 0;
$total_utilidad = 0;
$total_costo = 0;

$indice = 0;

foreach ($this->venta->UsuariosPresupuesto($_REQUEST['desde'], $_REQUEST['hasta'], 'DESC') as $i) :

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$total_formatted = number_format($i->total - $i->devolucion, 0, ",", ".");
	$utilidad_formatted = number_format($i->utilidad - $i->devolucion_costo, 0, ",", ".");
	$margen_porc_formatted = number_format($i->margen_ganancia, 2, ",", ".");

	$total_ventas += $i->total - $i->devolucion;
	$total_utilidad += $i->utilidad - $i->devolucion_costo;
	$total_costo += $i->costo - $i->devolucion_costo;

	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="">
			    <th width="27%" style="">$i->user</th>
             	<th width="30%" align="right" style="">$total_formatted</th>
             	<th width="30%" align="right" style="">$utilidad_formatted</th>
             	<th width="13%" align="right" style="">$margen_porc_formatted%</th>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['vent_vend'])) $pdf->writeHTML($html1, false, false, false, false, '');

	$indice++;
endforeach;

$total_ventas_formatted = number_format($total_ventas, 0, ",", ".");
$total_utilidad_formatted = number_format($total_utilidad, 0, ",", ".");

$total_ventas = ($total_ventas != 0) ? $total_ventas : 1;
$margen_total = ($total_ventas - $total_costo) / $total_ventas * 100;
$margen_total_formatted = number_format($margen_total, 2, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="27%" style="" align="left">RESULTADO (+)</td>
				<td width="30%" style="" align="right">$total_ventas_formatted</td>
				<td width="30%" style="" align="right">$total_utilidad_formatted</td>
				<td width="13%" style="" align="right">$margen_total_formatted%</td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['vent_vend'])) $pdf->writeHTML($html1, false, false, false, false, '');


/*

   FIN CLIENTES QUE MAS COMPRAN

*/



/* ================================ 
	COBROS DE DEUDAS
================================ */

if (isset($_REQUEST['items_informe']['cobr_deud'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<br>
		<h1 align="center">Cobros de deudas</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="12%" style="">Fecha</th>
                <th width="58%" style="">Concepto</th>
             	<th width="15%" style="">Monto</th>
             	<th width="15%" style="">Utilidad</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['cobr_deud'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalCobrosDeudas = 0;
$totalCobrosDeudasG = 0;

$indice = 0;

foreach ($this->ingreso->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']) as $i):

	if ($i->categoria != "Transferencia") {
		$montoG = number_format(($i->monto_guaranies * ($i->margen_ganancia / 100)), 0, ",", ".");
		$monto = number_format(($i->monto_guaranies), 0, ",", ".");
		$dia = date("d", strtotime($i->fecha));
		$fecha_gasto = date("d/m/Y", strtotime($i->fecha));

		$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

		$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
			    <th width="12%" style="">$fecha_gasto</th>
				<td width="58%" style="" align="left">$i->concepto</td>
				<td width="15%" style="" align="right">$monto</td>
				<td width="15%" style="" align="right">$montoG</td>
				
			</tr>
		</table>

EOF;

		if (isset($_REQUEST['items_informe']['cobr_deud'])) $pdf->writeHTML($html1, false, false, false, false, '');
		$totalCobrosDeudas += $i->monto_guaranies;
		$totalCobrosDeudasG += $i->monto_guaranies * ($i->margen_ganancia / 100);
	}
	$indice++;
endforeach;

$cobrosDeudas = number_format($totalCobrosDeudas, 0, ",", ".");
$cobrosDeudasG = number_format($totalCobrosDeudasG, 0, ",", ".");
$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="70%" style="" align="left"><b>RESULTADO (+)</b></td>
				<td width="15%" style="" align="right"><b>$cobrosDeudas</b></td>
				<td width="15%" style="" align="right"><b>$cobrosDeudasG</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['cobr_deud'])) $pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN COBROS DE DEUDAS

*/


/* ================================ 
	INGRESOS
================================ */

if (isset($_REQUEST['items_informe']['ingr'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<br>
		<h1 align="center">Ingresos</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
				<th width="10%" style="">Fecha</th>
				<th width="6%" style="">ID</th>
				<th width="8%" style="">ID Venta</th>
				<th width="20%" style="">Cliente</th>
				<th width="15%" style="">Categoría</th>
				<th width="26%" style="">Concepto</th>
				<th width="15%" style="" align="right">Monto</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['ingr'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalIngreso = 0;
$ingresosPorMoneda = array(
	'Guaranies' => 0,
	'Dolares' => 0,
	'Reales' => 0
);

$indice = 0;

foreach ($this->ingreso->Listar_rango($_REQUEST['desde'], $_REQUEST['hasta']) as $i):

	if ($i->categoria != "Transferencia" && $i->anulado == null) {
		// Convertir el monto considerando el tipo de cambio
		$montoConvertido = $i->monto * ($i->cambio ?? 1);
		$monto = number_format($montoConvertido, 0, ",", ".");
		$dia = date("d", strtotime($i->fecha));
		$fecha_gasto = date("d/m/Y", strtotime($i->fecha));
		$cliente_nombre = $i->nombre ?? 'Sin cliente';
		$id_venta_display = ($i->id_venta && $i->id_venta != 0) ? $i->id_venta : '-';

		$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

		$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
				<th width="10%" style="">$fecha_gasto</th>
			    <th width="6%" style="">$i->id</th>
			    <th width="8%" style="">$id_venta_display</th>
			    <td width="20%" style="" align="left">$cliente_nombre</td>
			    <td width="15%" style="" align="left">$i->categoria</td>
				<td width="26%" style="" align="left">$i->concepto</td>
				<td width="15%" style="" align="right">$monto</td>
				
			</tr>
		</table>

EOF;

		if (isset($_REQUEST['items_informe']['ingr'])) $pdf->writeHTML($html1, false, false, false, false, '');
		$totalIngreso += $montoConvertido;

		// Acumular por moneda (monto original sin convertir)
		$moneda = $i->moneda ?? 'Guaranies';
		$monedaKey = '';

		if (stripos($moneda, 'dolar') !== false || stripos($moneda, 'usd') !== false) {
			$monedaKey = 'Dolares';
		} elseif (stripos($moneda, 'real') !== false || stripos($moneda, 'rs') !== false) {
			$monedaKey = 'Reales';
		} else {
			$monedaKey = 'Guaranies';
		}

		$ingresosPorMoneda[$monedaKey] += $i->monto;
	}
	$indice++;
endforeach;

$ingreso = number_format($totalIngreso, 0, ",", ".");
$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="85%" style="" align="left"><b>RESULTADO (+)</b></td>
				<td width="15%" style="" align="right"><b>$ingreso</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['ingr'])) $pdf->writeHTML($html1, false, false, false, false, '');

// Mostrar resumen por monedas
$guaranies_formatted = number_format($ingresosPorMoneda['Guaranies'], 0, ",", ".");
$dolares_formatted = number_format($ingresosPorMoneda['Dolares'], 3, ",", ".");
$reales_formatted = number_format($ingresosPorMoneda['Reales'], 3, ",", ".");

$html1 = <<<EOF
		<br>
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="85%" style="" align="left"><b>Ingresos por moneda:</b></td>
				<td width="15%" style="" align="right"><b></b></td>
			</tr>
		</table>
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="70%" style="" align="left">• Guaraníes:</td>
				<td width="15%" style="" align="right">$guaranies_formatted</td>
				<td width="15%" style="" align="right"></td>
			</tr>
		</table>
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="70%" style="" align="left">• Dólares:</td>
				<td width="15%" style="" align="right">$dolares_formatted</td>
				<td width="15%" style="" align="right"></td>
			</tr>
		</table>
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="70%" style="" align="left">• Reales:</td>
				<td width="15%" style="" align="right">$reales_formatted</td>
				<td width="15%" style="" align="right"></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['ingr'])) $pdf->writeHTML($html1, false, false, false, false, '');


$totalCobroV = number_format($totalIngreso, 0, ",", ".");

/*

   FIN INGRESOS

*/


/*

   INICIO EGRESOS

*/
if (isset($_REQUEST['items_informe']['egr'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<br>
		<h1 align="center">Egresos</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
				<th width="10%" style="">Fecha</th>
			    <th width="6%" style="">ID</th>
			    <th width="8%" style="">ID Compra</th>
			    <th width="18%" style="">Cliente</th>
			    <th width="12%" style="">Categoría</th>
                <th width="34%" style="">Concepto</th>
				<th width="12%" style="" align="right">Monto</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['egr'])) $pdf->writeHTML($html1, false, false, false, false, '');

$egresosPorMoneda = array(
	'Guaranies' => 0,
	'Dolares' => 0,
	'Reales' => 0
);

foreach ($this->egreso->Listar_rango_informe($_REQUEST['desde'], $_REQUEST['hasta']) as $e):
	if ($e->categoria != "Transferencia" && $e->anulado == null) {
		// Usar el monto ya convertido a guaraníes del método Listar_rango_informe
		$monto = number_format($e->monto_guaranies, 0, ",", ".");
		$fecha_egreso = date("d/m/Y", strtotime($e->fecha));
		$cliente_nombre = $e->nombre ?? 'Sin cliente';
		$id_compra_display = ($e->id_compra && $e->id_compra != 0) ? $e->id_compra : '-';

		$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
				<th width="10%" style="">$fecha_egreso</th>
			    <th width="6%" style="">$e->id</th>
			    <th width="8%" style="">$id_compra_display</th>
			    <td width="18%" style="" align="left">$cliente_nombre</td>
			    <td width="12%" style="" align="left">$e->categoria</td>
				<td width="34%" style="" align="left">$e->concepto</td>
				<td width="12%" style="" align="right">$monto</td>
			</tr>
		</table>

EOF;

		if (isset($_REQUEST['items_informe']['egr'])) $pdf->writeHTML($html1, false, false, false, false, '');

		// Sumar al total convertido
		$totalEgresoReporte += $e->monto_guaranies;

		// Acumular por moneda (monto original sin convertir)
		$moneda = $e->moneda ?? 'Guaranies';
		$monedaKey = '';

		if (stripos($moneda, 'usd') !== false || stripos($moneda, 'dolar') !== false) {
			$monedaKey = 'Dolares';
		} elseif (stripos($moneda, 'real') !== false || stripos($moneda, 'rs') !== false) {
			$monedaKey = 'Reales';
		} else {
			$monedaKey = 'Guaranies';
		}

		$egresosPorMoneda[$monedaKey] += $e->monto;
	}
endforeach;

$egreso_reporte = number_format($totalEgresoReporte, 0, ",", ".");
$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="88%" style="" align="left"><b>RESULTADO (-)</b></td>
				<td width="12%" style="" align="right"><b>$egreso_reporte</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['egr'])) $pdf->writeHTML($html1, false, false, false, false, '');

// Mostrar resumen por monedas
$guaranies_formatted = number_format($egresosPorMoneda['Guaranies'], 0, ",", ".");
$dolares_formatted = number_format($egresosPorMoneda['Dolares'], 3, ",", ".");
$reales_formatted = number_format($egresosPorMoneda['Reales'], 3, ",", ".");

$html1 = <<<EOF
		<br>
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="88%" style="" align="left"><b>Egresos por moneda:</b></td>
				<td width="12%" style="" align="right"><b></b></td>
			</tr>
		</table>
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="88%" style="" align="left">• Guaraníes:</td>
				<td width="12%" style="" align="right">$guaranies_formatted</td>
			</tr>
		</table>
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="88%" style="" align="left">• Dólares:</td>
				<td width="12%" style="" align="right">$dolares_formatted</td>
			</tr>
		</table>
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="88%" style="" align="left">• Reales:</td>
				<td width="12%" style="" align="right">$reales_formatted</td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['egr'])) $pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN EGRESOS

*/


/* ================================ 
	compras
================================ */
if (isset($_REQUEST['items_informe']['compr'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<h1 align="center">Compras</h1>

	<table width"100%" style="$header_table_style">
			<tr align="">
			 <th width="15%" style="">Fecha</th>
			 <th width="10%" style="">Tipo</th>
				<th width="43%" style="">Producto</th>
				<th width="10%" style="">Cantidad</th>
				<th width="10%" style="">Precio</th>
				<th width="12%" style="" align="right">Total</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['compr'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalContadoCompra = 0;
$totalCreditoCompra = 0;
$totalCompra = 0;

$indice = 0;
foreach ($this->compra->AgrupadoProducto($_REQUEST['desde'], $_REQUEST['hasta']) as $r):

	$total = number_format($r->total, 0, ",", ".");
	$unidad = number_format($r->precio_compra, 0, ",", ".");
	$cantidad = number_format($r->cantidad, 2, ",", ".");
	$fecha_compra = date("d/m/Y ", strtotime($r->fecha_compra));
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';


	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="left">
			<td width="15%" style="" >$fecha_compra</td>
			<td width="10%" style="" >$r->contado</td>
				<td width="43%" style="" >$r->producto</td>
				<td width="10%" style="">$cantidad</td>
				<td width="10%" style="">$unidad</td>
				<td width="12%" style="" align="right">$total</td>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['compr'])) $pdf->writeHTML($html1, false, false, false, false, '');



	if ($r->contado == 'Contado') {
		$totalContadoCompra += $r->total;
	} else {
		$totalCreditoCompra += $r->total;
	}

	$totalCompra += $r->total;

	$indice++;
endforeach;

$totalCompraV = number_format($totalCompra, 0, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="73%" style="" align="left"><b>RESULTADO (-)</b></td>
				<td width="15%" style=""></td>
				<td width="12%" style="" align="right"><b>$totalCompraV</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['compr'])) $pdf->writeHTML($html1, false, false, false, false, '');
/* ================================ 
	fin compras
================================ */




/*

   INICIO GASTOS

*/
if (isset($_REQUEST['items_informe']['gast'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<br>
		<h1 align="center">Gastos</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="12%" style="">Fecha</th>
                <th width="76%" style="">Concepto</th>
             	<th width="12%" style="">Monto</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['gast'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalEgreso = 0;

foreach ($this->egreso->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']) as $e):
	if ($e->categoria != "Transferencia") {
		$monto = number_format($e->monto, 0, ",", ".");
		$dia = date("d", strtotime($e->fecha));
		$fecha_gasto = date("d/m/Y", strtotime($e->fecha));
		$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
			    <th width="12%" style="">$fecha_gasto</th>
				<td width="76%" style="" align="left">$e->concepto</td>
				<td width="12%" style="" align="right">$monto</td>
			</tr>
		</table>

EOF;

		if (isset($_REQUEST['items_informe']['gast'])) $pdf->writeHTML($html1, false, false, false, false, '');
		$totalEgreso += $e->monto;
	}
endforeach;

$egreso = number_format($totalEgreso, 0, ",", ".");
$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="87%" style="" align="left"><b>RESULTADO (-)</b></td>
				<td width="13%" style="" align="right"><b>$egreso</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['gast'])) $pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN GASTOS

*/


/*

  INICIO RESUMEN

*/

if (
	!$pag_vacia
) {
	$pdf->AddPage();
} else {
	$pag_vacia = false;
}

// Obtener datos específicos para el nuevo resumen
$resumenVentas = $this->model->ResumenVentasPorRango($_REQUEST['desde'], $_REQUEST['hasta']);
$gastosOperativos = $this->egreso->GastosOperativosPorRango($_REQUEST['desde'], $_REQUEST['hasta']);

// Calcular valores del nuevo resumen con validaciones
$ventasContado = isset($resumenVentas->ventas_contado) ? $resumenVentas->ventas_contado : 0;
$ventasCredito = isset($resumenVentas->ventas_credito) ? $resumenVentas->ventas_credito : 0;
$totalVentasNuevo = isset($resumenVentas->total_ventas) ? $resumenVentas->total_ventas : 0;
$costoProductos = isset($resumenVentas->costo_productos) ? $resumenVentas->costo_productos : 0;
$gastosOperativos = $gastosOperativos ? $gastosOperativos : 0;

$utilidadBruta = $totalVentasNuevo - $costoProductos;
$utilidadNeta = $utilidadBruta - $gastosOperativos;

// Formatear números para mostrar
$ventasContadoF = number_format($ventasContado, 0, ",", ".");
$ventasCreditoF = number_format($ventasCredito, 0, ",", ".");
$totalVentasNuevoF = number_format($totalVentasNuevo, 0, ",", ".");
$costoProductosF = number_format($costoProductos, 0, ",", ".");
$utilidadBrutaF = number_format($utilidadBruta, 0, ",", ".");
$gastosOperativosF = number_format($gastosOperativos, 0, ",", ".");
$utilidadNetaF = number_format($utilidadNeta, 0, ",", ".");

$bg = 'background-color: #eeeeee;';
$html1 = <<<EOF
		<h1 align="center">Resumen Financiero</h1>
		<table width"100%" style="border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:10px; background-color: #fff; color: #202020; font-weight: bold;">
			<tr align="center" style="$bg">
				<td style="" colspan="3">Ventas al contado (+): </td>
				<td style="">$ventasContadoF</td>
			</tr>
			<tr align="center">
				<td style="" colspan="3">Ventas a crédito (+): </td>
				<td style="">$ventasCreditoF</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3"><strong>Total Ventas:</strong> </td>
				<td style=""><strong>$totalVentasNuevoF</strong></td>
			</tr>
		    <tr align="center">
				<td style="" colspan="3">Costo de productos (-): </td>
				<td style="">$costoProductosF</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3"><strong>Utilidad Bruta:</strong> </td>
				<td style=""><strong>$utilidadBrutaF</strong></td>
			</tr>
			<tr align="center">
				<td style="" colspan="3">Gastos operativos (-): </td>
				<td style="">$gastosOperativosF</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="color: #0066cc;" colspan="3"><strong>UTILIDAD NETA:</strong> </td>
				<td style="color: #0066cc;"><strong>$utilidadNetaF</strong></td>
			</tr>
		</table>
		<!--<br>
		<table width"100%" style="font-size:8px; color: #666666;">
			<tr>
				<td><strong>Descripción de conceptos:</strong></td>
			</tr>
			<tr>
				<td>• <strong>Ventas al contado:</strong> Total de ventas cobradas en el momento</td>
			</tr>
			<tr>
				<td>• <strong>Ventas a crédito:</strong> Ventas que el cliente aún no pagó</td>
			</tr>
			<tr>
				<td>• <strong>Total Ventas:</strong> Todas las ventas realizadas (contado + crédito)</td>
			</tr>
			<tr>
				<td>• <strong>Costo de productos:</strong> Costo de los productos que se vendieron</td>
			</tr>
			<tr>
				<td>• <strong>Utilidad Bruta:</strong> Ganancia antes de gastos (Total Ventas - Costo productos)</td>
			</tr>
			<tr>
				<td>• <strong>Gastos operativos:</strong> Flete, sueldos, alquiler, etc.</td>
			</tr>
			<tr>
				<td>• <strong>Utilidad Neta:</strong> Ganancia final (Utilidad Bruta - Gastos operativos)</td>
			</tr>
		</table>-->

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

  FIN RESUMEN

*/

/* ================================ 
	CUENTAS POR COBRAR
================================ */

// Obtener datos de cuentas por cobrar
$cuentasCobrar = $this->deuda->ResumenCuentasPorCobrar($_REQUEST['desde'], $_REQUEST['hasta']);

// Formatear números para mostrar
$saldoAnteriorCobrarF = number_format($cuentasCobrar->saldo_anterior, 0, ",", ".");
$ventasCreditoF = number_format($cuentasCobrar->ventas_credito, 0, ",", ".");
$cobrosRecibidosF = number_format($cuentasCobrar->cobros_recibidos, 0, ",", ".");
$saldoFinalCobrarF = number_format($cuentasCobrar->saldo_final, 0, ",", ".");

$bg = 'background-color: #eeeeee;';
$html1 = <<<EOF
		<br><br>
		<h1 align="center">CUENTAS POR COBRAR</h1>
		<p align="center" style="font-size:10px; color: #666666;">Clientes que deben</p>
		<table width"100%" style="border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:10px; background-color: #fff; color: #202020; font-weight: bold;">
			<tr align="center" style="$bg">
				<td style="" colspan="3">Saldo anterior (+): </td>
				<td style="">$saldoAnteriorCobrarF</td>
			</tr>
			<tr align="center">
				<td style="" colspan="3">Ventas a crédito (+): </td>
				<td style="">$ventasCreditoF</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3">Cobros recibidos (-): </td>
				<td style="">$cobrosRecibidosF</td>
			</tr>
			<tr align="center">
				<td style="color: #0066cc;" colspan="3"><strong>SALDO FINAL A COBRAR:</strong> </td>
				<td style="color: #0066cc;"><strong>$saldoFinalCobrarF</strong></td>
			</tr>
		</table>
		<!--<br>
		<table width"100%" style="font-size:8px; color: #666666;">
			<tr>
				<td><strong>Descripción de conceptos de Cuentas por Cobrar:</strong></td>
			</tr>
			<tr>
				<td>• <strong>Saldo anterior:</strong> Lo que ya te debían antes del período</td>
			</tr>
			<tr>
				<td>• <strong>Ventas a crédito:</strong> Ventas nuevas no cobradas en este período</td>
			</tr>
			<tr>
				<td>• <strong>Cobros recibidos:</strong> Cobros de créditos anteriores en este período</td>
			</tr>
			<tr>
				<td>• <strong>Saldo final a cobrar:</strong> Lo que siguen debiendo actualmente</td>
			</tr>
		</table>-->

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

  FIN CUENTAS POR COBRAR

*/

/* ================================ 
	CUENTAS POR PAGAR
================================ */

// Obtener datos de cuentas por pagar
$cuentasPagar = $this->acreedor->ResumenCuentasPorPagar($_REQUEST['desde'], $_REQUEST['hasta']);

// Formatear números para mostrar
$saldoAnteriorPagarF = number_format($cuentasPagar->saldo_anterior, 0, ",", ".");
$comprasCreditoF = number_format($cuentasPagar->compras_credito, 0, ",", ".");
$pagosRealizadosF = number_format($cuentasPagar->pagos_realizados, 0, ",", ".");
$saldoFinalPagarF = number_format($cuentasPagar->saldo_final, 0, ",", ".");

$colorDeuda = $cuentasPagar->saldo_final > 0 ? '#cc0000' : '#008000'; // Rojo si hay deuda, verde si no

$bg = 'background-color: #eeeeee;';
$html1 = <<<EOF
		<br><br>
		<h1 align="center">CUENTAS POR PAGAR</h1>
		<p align="center" style="font-size:10px; color: #666666;">Cuentas por pagar a proveedores</p>
		<table width"100%" style="border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:10px; background-color: #fff; color: #202020; font-weight: bold;">
			<tr align="center" style="$bg">
				<td style="" colspan="3">Saldo anterior (+): </td>
				<td style="">$saldoAnteriorPagarF</td>
			</tr>
			<tr align="center">
				<td style="" colspan="3">Compras a crédito (+): </td>
				<td style="">$comprasCreditoF</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3">Pagos realizados (-): </td>
				<td style="">$pagosRealizadosF</td>
			</tr>
			<tr align="center">
				<td style="color: $colorDeuda;" colspan="3"><strong>SALDO FINAL A PAGAR:</strong> </td>
				<td style="color: $colorDeuda;"><strong>$saldoFinalPagarF</strong></td>
			</tr>
		</table>
		<!--<br>
		<table width"100%" style="font-size:8px; color: #666666;">
			<tr>
				<td><strong>Descripción de conceptos de Cuentas por Pagar:</strong></td>
			</tr>
			<tr>
				<td>• <strong>Saldo anterior:</strong> Lo que ya debías antes del período</td>
			</tr>
			<tr>
				<td>• <strong>Compras a crédito:</strong> Compras no pagadas en este período</td>
			</tr>
			<tr>
				<td>• <strong>Pagos realizados:</strong> Pagos hechos en este período</td>
			</tr>
			<tr>
				<td>• <strong>Saldo final a pagar:</strong> Lo que seguís debiendo actualmente</td>
			</tr>
		</table>-->

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

  FIN CUENTAS POR PAGAR

*/

/* ================================ 
	CONTROL DE CAJA
================================ */

// Obtener datos del control de caja
$flujoCaja = $this->model->FlujoCajaPorRango($_REQUEST['desde'], $_REQUEST['hasta']);

// Formatear números para mostrar
$ventasCobradasF = number_format($flujoCaja->ventas_cobradas, 0, ",", ".");
$cobrosDeudasF = number_format($flujoCaja->cobros_deudas, 0, ",", ".");
$totalIngresosF = number_format($flujoCaja->total_ingresos, 0, ",", ".");
$pagosGastosF = number_format($flujoCaja->pagos_gastos, 0, ",", ".");
$pagosProveedoresF = number_format($flujoCaja->pagos_proveedores, 0, ",", ".");
$totalEgresosF = number_format($flujoCaja->total_egresos, 0, ",", ".");
$flujoNetoF = number_format($flujoCaja->flujo_neto, 0, ",", ".");

$colorFlujo = $flujoCaja->flujo_neto >= 0 ? '#008000' : '#ff0000'; // Verde si positivo, rojo si negativo

$bg = 'background-color: #eeeeee;';
$html1 = <<<EOF
		<br><br>
		<h1 align="center">CONTROL DE CAJA</h1>
		<p align="center" style="font-size:10px; color: #666666;">Flujo de efectivo por conceptos principales</p>
		<table width"100%" style="border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:10px; background-color: #fff; color: #202020; font-weight: bold;">
			<tr align="center" style="$bg">
				<td style="" colspan="3">Ventas cobradas (+): </td>
				<td style="">$ventasCobradasF</td>
			</tr>
			<tr align="center">
				<td style="" colspan="3">Cobros de deudas (+): </td>
				<td style="">$cobrosDeudasF</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3"><strong>Total Cobrados:</strong> </td>
				<td style=""><strong>$totalIngresosF</strong></td>
			</tr>
		    <tr align="center">
				<td style="" colspan="3">Pagos de gastos (-): </td>
				<td style="">$pagosGastosF</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3">Pagos a proveedores (-): </td>
				<td style="">$pagosProveedoresF</td>
			</tr>
			<tr align="center">
				<td style="" colspan="3"><strong>Total Pagos:</strong> </td>
				<td style=""><strong>$totalEgresosF</strong></td>
			</tr>
			<tr align="center" style="$bg">
				<td style="color: $colorFlujo;" colspan="3"><strong>CONTROL NETO DE CAJA:</strong> </td>
				<td style="color: $colorFlujo;"><strong>$flujoNetoF</strong></td>
			</tr>
		</table>
		<!--<br>
		<table width"100%" style="font-size:8px; color: #666666;">
			<tr>
				<td><strong>Descripción de conceptos del Control de Caja:</strong></td>
			</tr>
			<tr>
				<td>• <strong>Ventas cobradas:</strong> Ventas al contado + crédito cobrado</td>
			</tr>
			<tr>
				<td>• <strong>Cobros de deudas:</strong> Deudas de clientes cobradas</td>
			</tr>
			<tr>
				<td>• <strong>Pagos de gastos:</strong> Gastos efectivamente pagados</td>
			</tr>
			<tr>
				<td>• <strong>Pagos a proveedores:</strong> Pagos a proveedores realizados</td>
			</tr>
			<tr>
				<td>• <strong>Control Neto:</strong> Diferencia entre ingresos y egresos principales (no incluye otros movimientos menores)</td>
			</tr>
		</table>-->

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

  FIN CONTROL DE CAJA

*/


/*

   INICIO PRODUCTOS

*/
if (isset($_REQUEST['items_informe']['prod'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
        <br>
		<h1 align="center">Productos</h1>

	<table width"100%" style="$header_table_style">
			<tr align="left">
                <th width="60%" style="">Producto</th>
                <th width="18%" style="">Precio Costo</th>
                <th width="10%" style="">Cantidad</th>
                <th width="12%" style="" align="">Total</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['prod'])) $pdf->writeHTML($html1, false, false, false, false, '');



$totalStock = 0;

foreach ($this->producto->ListarTodo() as $r):

	if ($r->stock > 0) {

		$total = number_format(($r->precio_costo * $r->stock), 0, ",", ".");
		$cantidad = number_format($r->stock, 0, ",", ".");
		$costo = number_format($r->precio_costo, 0, ",", ".");

		$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="60%" style="" align="left">$r->producto</td>
				<td width="18%" style="">$costo</td>
				<td width="10%" style="">$cantidad</td>
				<td width="12%" style="" align="">$total</td>
			</tr>
		</table>

EOF;

		if (isset($_REQUEST['items_informe']['prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

		$totalStock += ($r->precio_costo * $r->stock);
	}
endforeach;

$totalStockV = number_format($totalStock, 0, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="">
				<td width="78%" style="" align="left"><b>RESULTADO (+)</b></td>
				<td width="10%" style=""></td>
				<td width="12%" style="" align=""><b>$totalStockV</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN PRODUCTOS

*/




/*

   INICIO DEUDAS

*/
if (isset($_REQUEST['items_informe']['cuent_cobr'])) {
	if (!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}

$html1 = <<<EOF
        <br>
		<h1 align="center">CUENTAS A COBRAR</h1>

		<table width"100%" style="$header_table_style">
				<tr align="">
			<th width="30%" style="">Cliente</th>
			<th width="25%" style="">Concepto</th>
			<th width="15%" style="" align="right">Monto</th>
			<th width="15%" style="" align="right">Pagado</th>
			<th width="15%" style="" align="right">Saldo</th>
				</tr>
			</table>

EOF;

if (isset($_REQUEST['items_informe']['cuent_cobr'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalDeuda = 0;

foreach ($this->deuda->ListarAgrupadoCliente() as $r):

	$monto = number_format($r->monto, 0, ",", ".");
	$saldo = number_format($r->saldo, 0, ",", ".");
	$pagado = number_format($r->monto - $r->saldo, 0, ",", ".");

	$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="center">
				<td width="30%" style="" align="left">$r->nombre</td>
				<td width="25%" style="" align="left">$r->concepto</td>
				<td width="15%" style="" align="right">$monto</td>
				<td width="15%" style="" align="right">$pagado</td>
				<td width="15%" style="" align="right">$saldo</td>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['cuent_cobr'])) $pdf->writeHTML($html1, false, false, false, false, '');


	$totalDeuda += $r->saldo;

endforeach;

$totalDeudaV = number_format($totalDeuda, 0, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="left">
				<td width="70%" style=" " align="left"><b>RESULTADO</b></td>
				<td width="15%" style=""></td>
				<td width="15%" style=" " align="right"><b>$totalDeudaV</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['cuent_cobr'])) $pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN DEUDAS

*/




/*

   INICIO ACREEDORES

*/

$html1 = <<<EOF
        <br>
		<h1 align="center">CUENTAS A PAGAR</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
                <th width="30%" style="">Cliente</th>
                <th width="25%" style="">Concepto</th>
                <th width="15%" style="">Monto</th>
                <th width="15%" style="">Pagado</th>
                <th width="15%" style="">Saldo</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['cuent_pag'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalAcreedor = 0;

foreach ($this->acreedor->Listar() as $r):

	$monto = number_format($r->monto, 0, ",", ".");
	$saldo = number_format($r->saldo, 0, ",", ".");
	$pagado = number_format($r->monto - $r->saldo, 0, ",", ".");

	$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="30%" style="" align="left">$r->nombre</td>
				<td width="25%" style="" align="left">$r->concepto</td>
				<td width="15%" style="" align="right">$monto</td>
				<td width="15%" style="" align="right">$pagado</td>
				<td width="15%" style="" align="right">$saldo</td>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['cuent_pag'])) $pdf->writeHTML($html1, false, false, false, false, '');


	$totalAcreedor += $r->saldo;

endforeach;

$totalAcreedorV = number_format($totalAcreedor, 0, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="">
				<td width="70%" style=" " align="left"><b>RESULTADO</b></td>
				<td width="15%" style=""></td>
				<td width="15%" style=" " align="right"><b>$totalAcreedorV</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['cuent_pag'])) $pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN ACREEDORES

*/


/*

$html1 = <<<EOF
		<h1 align="center">Productos en falta</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="88%" style="border-left-width:1px ; border-right-width:1px">Producto</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Precio</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

    

$totalStock = 0;

foreach($this->producto->ListarTodo() as $r):

if($r->stock <= 0){

$total=number_format(($r->precio_costo),0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="88%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->producto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$total</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');


}
endforeach;
*/

$pdf->Output("Informe de la fecha $desde hasta $hasta.pdf", 'I');


//============================================================+
// END OF FILE
//============================================================+

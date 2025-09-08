<?php

// require_once('plugins/tcpdf/pdf/tcpdf_include.php');
require_once('plugins/tcpdf2/tcpdf.php');

// $moneda = $this->venta_tmp->ObtenerMoneda();
$Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
       
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
$pdf->SetTitle("Informe de la fecha $desde hasta $hasta");
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

$html1 = <<<EOF
		<h1 align="center"></h1>
		<h3 align="center">Informe de la fecha $desde hasta $hasta</h3>
		<p>Generado el $fechaHoraHoy</p>
	

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

//Traer array y llamar a funciones por separado
// call_user_func(array($controller, $accion));


	/*------------------------------
	VENTAS contado - AGRUPADO VENTA
	---------------------------------*/
$html1 = <<<EOF
		<h1 align="center">Ventas al contado</h1>

		<table width"100%" style="$header_table_style">
		<tr align="center">
			<th width="25%" style=""><b>Fecha</b></th>
                <th width="25%" style=""><b>Vendedor</b></th>
                <th width="25%" style=""><b>Cliente</b></th>
                <th width="25%" style="" align="right"><b>Venta</b></th>
			</tr>
		</table>

EOF;

	if (isset($_REQUEST['items_informe']['vent_cont'])){
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
		$porventa = number_format(((($v->total - $v->costo) * 100) / $v->total), 0, ",", ".");

		$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

		$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			<th width="25%" style="" align="left">$v->fecha_venta</th>
                <th width="25%" style=" font-size:8px; " align="center">$v->vendedor ($v->id_venta)</th>
                <th width="25%" style="" align="left">$v->nombre</th>
                <th width="25%" style="" align="right">$totalv</th>
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
	$porventas = number_format(((($gv - $gc) * 100) / $gv), 0, ",", ".");

	$html1 = <<<EOF
		
		<table width"100%" style="$header_table_style" align="left">
			<tr align="center" style="padding:10px">
                <th width="60%" style="" align="left";><b>RESULTADOS (+)</b></th>
             	<th width="40%" style="" align="right"><b>$sumvv</b></th>
			</tr>
		</table>
		<br><br>

EOF;

	if (isset($_REQUEST['items_informe']['vent_cont'])) $pdf->writeHTML($html1, false, false, false, false, '');
	/* ================================ 
	fin agrupado ventas
	================================ */

	
	/*------------------------
	COMPRA VENTA POR PRODUCTO
	MOVIMIENTOS DE PRODUCTOS
	-------------------------*/
	if(
		isset($_REQUEST['items_informe']['mov_prod'])) {
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
                <th width="16%"  style="" align="left"><b>Tot. Compras (USD.)</b></th>
                <th width="14%" style="" align="left"><b>Cant. Ventas</b></th>
                <th width="14%" style="" align="left"><b>Tot. Ventas</b></th>
			</tr>
		</table>
EOF;

if(isset($_REQUEST['items_informe']['mov_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

$total_cantidad_compras = 0;
$total_monto_compras = 0;
$total_cantidad_ventas = 0;
$total_monto_ventas = 0;

$indice = 0; //indice para saber si la fila es par o impar
foreach($this->model->CompraVentaPorProducto($_REQUEST['desde'], $_REQUEST['hasta']) as $r):
	//format para numeros
$cantidad_compra =number_format(($r->cantidad_compra ?? 0),0,",",".");
$total_compra =number_format(($r->total_compra ?? 0),0,",",".");

$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '' ;

$cantidad_venta =number_format(($r->cantidad_venta ?? 0),0,",",".");
$total_venta =number_format(($r->total_venta ?? 0),0,",",".");

//totalizar
$total_cantidad_compras += ($r->cantidad_compra ?? 0);
$total_monto_compras += ($r->total_compra ?? 0);
$total_cantidad_ventas += ($r->cantidad_venta ?? 0);
$total_monto_ventas += ($r->total_venta ?? 0);


$porc_ganancia =number_format(($r->porcentaje_ganancia ?? 0 ) , 0, "," ,".");

$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			    <td width="13%" style="" align="left">$r->codigo</td>
                <td width="35%" style=" font-size:7.3px" align="left">$r->producto</td>
                <td width="8%" style="" align="left">$cantidad_compra</td>
                <td width="16%" style="" align="left">$total_compra</td>
             	<td width="14%" style="" align="left">$cantidad_venta</td>
             	<td width="14%" style="" align="left">$total_venta</td>
			</tr>
		</table>

EOF;

if(isset($_REQUEST['items_informe']['mov_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

	$indice += 1;

endforeach;

// format de totalizacion
$total_cantidad_compras_f = number_format(($total_cantidad_compras ?? 0), 0, ",", ".");
$total_monto_compras_f = number_format(($total_monto_compras ?? 0),0, ",", ".");
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

if(isset($_REQUEST['items_informe']['mov_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	fin movimientos por producto
================================ */




	/*------------------------
	VENTAS AGRUPADO PRODUCTO
	-------------------------*/
if(isset($_REQUEST['items_informe']['ven_prod'])  ){
	if(!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}

$html1 = <<<EOF
		<h1 align="center">Ventas por producto</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="15%" style="">Cod.</th>
                <th width="20%" style="">Producto</th>
                <th width="10%" style="">Ca</th>
                <th width="20%" style="">Costo</th>
                <th width="15%" style="">Venta</th>
                <th width="20%" style="">Utilidad</th>
			</tr>
		</table>
EOF;

if(isset($_REQUEST['items_informe']['ven_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$totalVenta = 0;

$indice = 0;
foreach($this->model->AgrupadoProductoVenta($_REQUEST['desde'], $_REQUEST['hasta']) as $r):
    
$total=number_format($r->total,0,",",".");
$u=number_format($r->precio_venta,0,",",".");
$costo=number_format($r->costo,0,",",".");
$fecha_venta = date("d/m/Y ", strtotime($r->fecha_venta));
$cantidad=number_format($r->cantidad,0,",",".");
$ganancia=number_format(($r->total - $r->costo),0,",",".");

$por=((($r->total - $r->costo)*100));

$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

$porcentaje=number_format((($por)/$g),2,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			    <th width="15%" style=" " align="left">$r->codigo</th>
                <th width="20%" style="" align="left">$r->producto <b>($u)</b></th>
                <th width="10%" style="" align="right">$cantidad</th>
                <th width="20%" style="" align="right">$costo</th>
                <th width="15%" style="" align="right">$total</th>
                <th width="20%" style="" align="right">$ganancia</th>
                
			</tr>
		</table>

EOF;

if(isset($_REQUEST['items_informe']['ven_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalCosto += $r->costo;
$totalVenta += $r->total;

if($r->contado=='Contado'){
    $totalContado += $r->total;
}else{
    $totalCredito += $r->total;
}

$indice++;
endforeach;

$totalCostoV = number_format($totalCosto,0,",",".");
$totalVentaV = number_format($totalVenta,0,",",".");
$totalGananciaV = number_format(($totalVenta - $totalCosto),0,",",".");

$totalVenta = ($totalVenta != 0) ? ($totalVenta) : 1;
$porcentaje = number_format(((((($totalVenta-$totalCosto)*100)/$totalVenta))),0,",",".");

$html1 = <<<EOF
		
	<table width"100%" style="$header_table_style">
			<tr align="center" style="padding:10px">
			    <th width="15%" style=" " align="left"></th>
                <th width="20%" style="" align="left"><b>RESULTADOS</b></th>
                <th width="10%" style="" align="right"></th>
                <th width="20%" style="" align="right">$totalCostoV</th>
                <th width="15%" style="" align="right">$totalVentaV</th>
                <th width="20%" style="" align="right">$totalGananciaV</th>
			</tr>
		</table>

EOF;

if(isset($_REQUEST['items_informe']['ven_prod'])) $pdf->writeHTML($html1, false, false, false, false, '');
/* ================================ 
	fin de ventas por producto
================================ */



/* ================================ 
	CLIENTES QUE MAS COMPRAN
================================ */

if (isset($_REQUEST['items_informe']['cli_comp'])  ){
	if(!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<br>
		<h1 align="center">Clientes con más compras</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="20%" style="">Nombre</th>
			    <th width="20%" style="">RUC</th>
			    <th width="20%" style="">Dirección</th>
                <th width="20%" style="">Teléfono</th>
             	<th width="20%" style="" align="right">Total Compras</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['cli_comp']))$pdf->writeHTML($html1, false, false, false, false, '');

$total_ventas = 0;
$total_utilidad = 0;
$total_costo = 0;

$indice = 0;

foreach ($this->venta->ClientesVentas($_REQUEST['desde'], $_REQUEST['hasta'], 'DESC') as $i) :

$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '' ;

	$total_formatted = number_format($i->total, 0, ",", ".");
	$utilidad_formatted = number_format($i->utilidad, 0, ",", ".");
	$margen_porc_formatted = number_format($i->margen_ganancia, 0, ",", ".");

	$nombre_cliente = $i->nombre_cliente ?? 'Cliente Ocasional';

	$total_ventas += $i->total;
	$total_utilidad += $i->utilidad;
	$total_costo += $i->costo;

		$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="">
			    <th width="20%" style="">$nombre_cliente</th>
			    <th width="20%" style="">$i->ruc</th>
			    <th width="20%" style="">$i->direccion</th>
                <th width="20%" style="">$i->telefono</th>
             	<th width="20%" align="right" style="">$total_formatted</th>
			</tr>
		</table>

EOF;

		if (isset($_REQUEST['items_informe']['cli_comp']))$pdf->writeHTML($html1, false, false, false, false, '');

$indice++;
endforeach;

$total_ventas_formatted = number_format($total_ventas, 0, ",", ".");
$total_utilidad_formatted = number_format($total_utilidad, 0, ",", ".");

$total_ventas = ($total_ventas != 0 ) ? $total_ventas : 1;
$margen_total = ($total_ventas - $total_costo) / $total_ventas * 100;
$margen_total_formatted = number_format($margen_total, 0, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="60%" style="" align="left">RESULTADO (+)</td>
				<td width="40%" style="" align="right">$total_ventas_formatted</td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['cli_comp']))$pdf->writeHTML($html1, false, false, false, false, '');


/*

   FIN CLIENTES QUE MAS COMPRAN

*/



/* ================================ 
	VENDEDORES QUE MAS VENDIERON
================================ */
// var_dump($pag_vacia); //die;
// var_dump(isset($_REQUEST['items_informe']['vent_vend'])); die;

if (isset($_REQUEST['items_informe']['vent_vend']) ){
	if(!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}

$html1 = <<<EOF
		<br>
		<h1 align="center">Ventas por vendedores</h1>
		<p></p>
		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="10%" style="">Usuario</th>
			    <th width="30%" align="right" style="">Ventas Totales</th>
			    <th width="20%" align="right" style="">Costos Totales</th>
			    <th width="20%" align="right" style="">Margen Ganancia</th>
			    <th width="20%" align="right" style="">Utilidad Total</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['vent_vend'])) $pdf->writeHTML($html1, false, false, false, false, '');

$total_ventas = 0;
$total_utilidad = 0;
$total_costo = 0;

$indice = 0;

foreach ($this->venta->UsuariosPresupuesto($_REQUEST['desde'], $_REQUEST['hasta'], 'DESC') as $i) :

$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '' ;

	$total_formatted = number_format($i->total, 0, ",", ".");
	$utilidad_formatted = number_format($i->utilidad, 0, ",", ".");
	$margen_porc_formatted = number_format($i->margen_ganancia, 0, ",", ".");
	$costo_formatted = number_format($i->costo, 0, ",", ".");

	$total_ventas += $i->total;
	$total_utilidad += $i->utilidad;
	$total_costo += $i->costo;

		$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="">
			    <th width="10%" style="">$i->user</th>
             	<th width="30%" align="right" style="">$total_formatted</th>
             	<th width="20%" align="right" style="">$costo_formatted</th>
             	<th width="20%" align="right" style="">$margen_porc_formatted</th>
             	<th width="20%" align="right" style="">$utilidad_formatted</th>
			</tr>
		</table>

EOF;

		if (isset($_REQUEST['items_informe']['vent_vend'])) $pdf->writeHTML($html1, false, false, false, false, '');

$indice++;
endforeach;

$total_ventas_formatted = number_format($total_ventas, 0, ",", ".");
$total_utilidad_formatted = number_format($total_utilidad, 0, ",", ".");
$total_costo_formatted = number_format($total_costo, 2, ",", ".");

$total_ventas = ($total_ventas != 0) ? $total_ventas : 1 ;
$margen_total = ($total_ventas - $total_costo) / $total_ventas * 100;
$margen_total_formatted = number_format($margen_total, 0, ",", ".");


$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="15%" style="" align="left">RESULTADO (+)</td>
				<td width="25%" style="" align="right">$total_ventas_formatted</td>
				<td width="20%" style="" align="right">$total_costo_formatted</td>
				<td width="20%" style="" align="right">$margen_total_formatted%</td>
				<td width="20%" style="" align="right">$total_utilidad_formatted</td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['vent_vend'])) $pdf->writeHTML($html1, false, false, false, false, '');


/*

   FIN CLIENTES QUE MAS COMPRAN

*/



/* ================================ 
	INGRESOS
================================ */
                      
if (isset($_REQUEST['items_informe']['ingr'])  ){
	if(!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<br>
		<h1 align="center">Ingresos</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="12%" style="">Fecha</th>
                <th width="58%" style="">Concepto</th>
             	<th width="22%" style="" align="right">Ingreso</th>
             	<th width="8%" style="" align="right">Moneda</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['ingr'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalIngreso = 0;

$indice = 0;

foreach($this->ingreso->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']) as $i):

if($i->categoria != "Transferencia"){
$monto=number_format(($i->monto),0,",",".");
$dia = date("d", strtotime($i->fecha));
$fecha_gasto = date("d/m/Y", strtotime($i->fecha));

$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
			    <th width="12%" style="">$fecha_gasto</th>
				<td width="58%" style="" align="left">$i->concepto</td>
				<td width="22%" style="" align="right">$monto</td>
				<td width="8%" style="" align="left">$i->moneda</td>
				
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['ingr'])) $pdf->writeHTML($html1, false, false, false, false, '');
	if($i->moneda == 'GS'){
		$totalIngreso_GS += $i->monto;
	}else if ($i->moneda == 'USD'){
		$totalIngreso_USD += $i->monto;
	}else if ($i->moneda == 'RS'){
		$totalIngreso_RS += $i->monto;
	}
}

$indice++;
endforeach;

$ingreso_GS=number_format($totalIngreso_GS,0,",",".");
$ingreso_USD=number_format($totalIngreso_USD,0,",",".");
$ingreso_RS=number_format($totalIngreso_RS,0,",",".");
$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="74%" style="" align="left"><b>RESULTADO (+)</b></td>
				<td width="18%" style="" align="right"><b>$ingreso_USD</b></td>
				<td width="8%" style="" align="left"><b>USD</b></td>
			</tr>
			<tr align="center">
				<td width="74%" style="" align="left"><b>RESULTADO (+)</b></td>
				<td width="18%" style="" align="right"><b>$ingreso_GS</b></td>
				<td width="8%" style="" align="left"><b>PYG</b></td>
			</tr>
			<tr align="center">
				<td width="74%" style="" align="left"><b>RESULTADO (+)</b></td>
				<td width="18%" style="" align="right"><b>$ingreso_RS</b></td>
				<td width="8%" style="" align="left"><b>BRL</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['ingr'])) $pdf->writeHTML($html1, false, false, false, false, '');


$totalCobroV = number_format($totalIngreso,0,",",".");

/*

   FIN INGRESOS

*/


/* ================================ 
	compras
================================ */
if (isset($_REQUEST['items_informe']['compr'])  ){
	if(!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<h1 align="center">Compras</h1>

	<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="15%" style="">Fecha</th>
			    <th width="20%" style="">Cod</th>
                <th width="40%" style="">Producto</th>
                <th width="8%" style="">Can</th>
                <th width="10%" style="">Precio</th>
                <th width="12%" style="">Total</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['compr'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalContadoCompra = 0;
$totalCreditoCompra = 0;
$totalCompra = 0;

$indice = 0;
foreach($this->compra->AgrupadoProducto($_REQUEST['desde'],$_REQUEST['hasta']) as $r):

$total=number_format($r->total,0,",",".");
$unidad=number_format($r->precio_costo,0,",",".");
$cantidad=number_format($r->cantidad,2,",",".");
$fecha_compra = date("d/m/Y ", strtotime($r->fecha_compra));
$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';


$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="left">
			    <td width="15%" style="" >$fecha_compra</td>
			    <th width="20%" style="">$r->codigo</th>
				<td width="40%" style="" >$r->producto</td>
				<td width="8%" style="">$cantidad</td>
				<td width="10%" style="">$unidad</td>
				<td width="12%" style="" align="left">$total</td>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['compr'])) $pdf->writeHTML($html1, false, false, false, false, '');



if($r->contado=='Contado'){
    $totalContadoCompra += $r->total;
}else{
    $totalCreditoCompra += $r->total;
}

$totalCompra += $r->total;

$indice++;
endforeach;

$totalCompraV=number_format($totalCompra,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="78%" style="" align="left"><b>RESULTADO (-)</b></td>
				<td width="10%" style=""></td>
				<td width="12%" style="" align="left"><b>$totalCompraV</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['compr'])) $pdf->writeHTML($html1, false, false, false, false, '');
/* ================================ 
	fin compras
================================ */




/* ================================ 
	inicio gastos
================================ */
if (isset($_REQUEST['items_informe']['gast'])  ){
	if(!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}
$html1 = <<<EOF
		<br>
		<h1 align="center">Gastos</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="12%" style="">Fecha</th>
                <th width="30%" style="">Concepto</th>
                <th width="28%" style=""></th>
             	<th width="22%" style="" align="right">Monto</th>
             	<th width="8%" style="">Moneda</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['gast'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalEgreso_GS = 0;
$totalEgreso_USD = 0;
$totalEgreso_RS = 0;

foreach($this->egreso->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']) as $e):
if($e->categoria != "Transferencia"){
	
	if ($e->moneda == 'GS'){
		$totalEgreso_GS += $e->monto;
	}else if($e->moneda == 'USD'){
		$totalEgreso_USD+= $e->monto;
	}else if($e->moneda == 'RS'){
		$totalEgreso_RS += $e->monto;
	}

$monto=number_format($e->monto,0,",",".");
$dia = date("d", strtotime($e->fecha));
$fecha_gasto = date("d/m/Y", strtotime($e->fecha));
$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
			    <th width="12%" style="">$fecha_gasto</th>
				<td width="30%" style="" align="left">$e->concepto</td>
				<td width="28%" style="" align="left">$e->categoria</td>
				<td width="22%" style="" align="right">$monto</td>
				<td width="8%" style="" align="left">$e->moneda</td>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['gast'])) $pdf->writeHTML($html1, false, false, false, false, '');
$totalEgreso += $e->monto;
}
endforeach;

$egreso_GS=number_format($totalEgreso_GS,0,",",".");
$egreso_USD=number_format($totalEgreso_USD,2,",",".");
$egreso_RS=number_format($totalEgreso_RS,2,",",".");
$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
				<td width="79%" style="" align="left"><b>RESULTADO (-)</b></td>
				<td width="13%" style="" align="right"><b>$egreso_USD</b></td>
				<td width="8%" style="" align="left"><b>USD</b></td>
			</tr>
			<tr align="center">
				<td width="79%" style="" align="left"><b>RESULTADO (-)</b></td>
				<td width="13%" style="" align="right"><b>$egreso_GS</b></td>
				<td width="8%" style="" align="left"><b>PYG</b></td>
			</tr>
			<tr align="center">
				<td width="79%" style="" align="left"><b>RESULTADO (-)</b></td>
				<td width="13%" style="" align="right"><b>$egreso_RS</b></td>
				<td width="8%" style="" align="left"><b>BRL</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['gast'])) $pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	fin gastos
================================ */


/*

  INICIO RESUMEN

*/

if(
	!$pag_vacia
) {
	$pdf->AddPage();
} else {
	$pag_vacia = false;
}


$utilidad_real = number_format((($totalVenta - $totalCosto) - $totalEgreso + $totalIngreso),2,",",".");

$utilidads_USD = number_format((($totalVenta+$totalIngreso_USD)-($totalCompra+$totalEgreso_USD)),0,",",".");

$bg = 'background-color: #eeeeee;';
$html1 = <<<EOF
		<h1 align="center">Resumen</h1>
		<table width"100%" style="border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:10px; background-color: #fff; color: #202020; font-weight: bold;">
			<tr align="center" style="$bg">
				<td style="" colspan="3">Ventas (+): </td>
				<td style="" align="right">$totalVentaV</td>
				<td style="" align="left">USD</td>
			</tr>
			<tr align="center">
				<td style="" colspan="3">Ingresos USD(+): </td>
				<td style="" align="right">$ingreso_USD</td>
				<td style="" align="left">USD</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3">Ingresos PYG (+): </td>
				<td style="" align="right">$ingreso_GS</td>
				<td style="" align="left">PYG</td>
			</tr>

			<tr align="center">
				<td style="" colspan="3">Ingresos BRL(+): </td>
				<td style="" align="right">$ingreso_RS</td>
				<td style="" align="left">BRL</td>
			</tr>
		    <tr align="center" style="$bg">
				<td style="" colspan="3">Compras (-): </td>
				<td style="" align="right">$totalCompraV</td>
				<td style="" align="left">USD</td>
			</tr>
			<tr align="center">
				<td style="" colspan="3">Gastos USD (-): </td>
				<td style="" align="right">$egreso_USD</td>
				<td style="" align="left">USD</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3">Gastos PYG (-): </td>
				<td style="" align="right">$egreso_GS</td>
				<td style="" align="left">PYG</td>
			</tr>
			<tr align="center">
				<td style="" colspan="3">Gastos BRL (-): </td>
				<td style="" align="right">$egreso_RS</td>
				<td style="" align="left">BRL</td>
			</tr>
			<tr align="center" style="$bg">
				<td style="" colspan="3">Utilidad Bruta: </td>
				<td style="" align="right">$utilidads_USD</td>
				<td style="" align="left"></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

  FIN RESUMEN

*/


/* ================================ 
	Inicio productos
================================ */
	if (isset($_REQUEST['items_informe']['prod'])  ){
	if(!$pag_vacia) $pdf->AddPage();
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

foreach($this->producto->ListarTodo() as $r):

if($r->stock > 0){

$total=number_format(($r->precio_costo*$r->stock),0,",",".");
$cantidad=number_format($r->stock,2,",",".");
$costo=number_format($r->precio_costo,0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="60%" style="" align="left">$r->producto</td>
				<td width="18%" style="">$costo</td>
				<td width="10%" style="">$cantidad</td>
				<td width="12%" style="" align="">$total </td>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalStock += ($r->precio_costo*$r->stock);
}
endforeach;

$totalStockV=number_format($totalStock,2,",",".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="">
				<td width="78%" style="" align="left"><b>RESULTADO (+)</b></td>
				<td width="10%" style=""></td>
				<td width="12%" style="" align=""><b>$totalStockV USD</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['prod'])) $pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	fin productos
================================ */




/* ================================ 
	inicio de deudas
================================ */
if (isset($_REQUEST['items_informe']['cuent_cobr'])  ){
	if(!$pag_vacia) $pdf->AddPage();
	$pag_vacia = false;
}

$html1 = <<<EOF
        <br>
		<h1 align="center">CUENTAS A COBRAR</h1>

        <table width"100%" style="$header_table_style">
			<tr align="">
                <th width="40%" style="">Cliente</th>
                <th width="31%" style="">Concepto</th>
                <th width="14%" style="">Monto</th>
                <th width="15%" style="">Saldo</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['cuent_cobr'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalDeuda = 0;

foreach($this->deuda->ListarAgrupadoCliente() as $r):


$monto=number_format($r->monto,0,",",".");
$saldo=number_format($r->saldo,0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="center">
				<td width="40%" style="" align="left">$r->nombre</td>
				<td width="31%" style="" align="left">$r->concepto</td>
				<td width="14%" style="" align="left">$monto</td>
				<td width="15%" style="" align="left">$saldo</td>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['cuent_cobr'])) $pdf->writeHTML($html1, false, false, false, false, '');


$totalDeuda += $r->saldo;

endforeach;

$totalDeudaV=number_format($totalDeuda,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="left">
				<td width="75%" style=" " align="left"><b>RESULTADO</b></td>
				<td width="10%" style=""></td>
				<td width="15%" style=" " align="left"><b>$totalDeudaV USD</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['cuent_cobr'])) $pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	fin de deudas
================================ */




/* ================================ 
	inicio de acreedores
================================ */

$html1 = <<<EOF
        <br>
		<h1 align="center">CUENTAS A PAGAR</h1>

		<table width"100%" style="$header_table_style">
			<tr align="">
                <th width="40%" style="">Cliente</th>
                <th width="31%" style="">Concepto</th>
                <th width="14%" style="">Monto</th>
                <th width="15%" style="">Saldo</th>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['cuent_pag'])) $pdf->writeHTML($html1, false, false, false, false, '');

$totalAcreedor = 0;

foreach($this->acreedor->ListarAPagar() as $r):


$monto=number_format($r->monto,0,",",".");
$saldo=number_format($r->saldo,0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="$body_table_style">
			<tr align="">
				<td width="40%" style="" align="left">$r->nombre</td>
				<td width="31%" style="" align="left">$r->concepto</td>
				<td width="14%" style="" align="left">$monto</td>
				<td width="15%" style="" align="left">$saldo</td>
			</tr>
		</table>

EOF;

if (isset($_REQUEST['items_informe']['cuent_pag'])) $pdf->writeHTML($html1, false, false, false, false, '');


$totalAcreedor += $r->saldo;

endforeach;

$totalAcreedorV=number_format($totalAcreedor,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="">
				<td width="75%" style=" " align="left"><b>RESULTADO</b></td>
				<td width="10%" style=""></td>
				<td width="15%" style=" " align="left"><b>$totalAcreedorV</b></td>
			</tr>
		</table>

EOF;
if (isset($_REQUEST['items_informe']['cuent_pag']))$pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	fin acreedores
================================ */



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
// $pdf->Output('Folder Label.pdf','D');


//============================================================+
// END OF FILE
//============================================================+
  ?>
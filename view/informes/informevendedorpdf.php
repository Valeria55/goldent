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
// $_REQUEST['fecha'] .= '-01';
//$mes = date("m", strtotime($_REQUEST['fecha']));
// $mes = $Meses[intval(date("m", strtotime($_REQUEST['fecha'])))-1];
// $ano = date("Y", strtotime($_REQUEST['fecha']));
$fechaHoraHoy = date("d/m/Y \a \l\a\s H:i \h\s");
$usuario = $this->usuario->Obtener($_SESSION['user_id']);


// $inicial=number_format($moneda->monto_inicial,0,",",".");
// $caja_inicial = $moneda->monto_inicial;
// $real=number_format($moneda->reales,0,",",".");
// $dolar=number_format($moneda->dolares,0,",",".");

$html1 = <<<EOF
		<h1 align="center">ScoreCar Pro - Centro de Instalaciones</h1>
		<h3 align="center">Informe de la fecha $desde hasta $hasta</h3>
		<p>Fecha de emisión del documento: $fechaHoraHoy</p>
		<p>Generado por: $usuario->user</p>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

/* ================================ 
	ESTILOS PARA LAS TABLAS
================================ */
$body_table_style = 'font-size:10px; border-top: 1px solid #ccc; border-bottom: .5px solid #ccc;';
$header_table_style = 'border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:10px; background-color: #c0c0c0; color: #202020; font-weight: bold;';
// ********************************


/* ================================ 
	VENDEDORES QUE MAS VENDIERON
================================ */


// $pdf->AddPage();
$html1 = <<<EOF
		<br>
		<h1 align="center">Ventas por vendedores</h1>
		<p></p>
		<table width"100%" style="$header_table_style">
			<tr align="">
			    <th width="50%" style="">Usuario</th>
			    <th width="50%" align="right" style="">Ventas Totales</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$total_ventas = 0;
$total_utilidad = 0;
$total_costo = 0;

$indice = 0;

foreach ($this->venta->UsuariosPresupuesto($_REQUEST['desde'], $_REQUEST['hasta'], 'DESC') as $i) :

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$total_formatted = number_format($i->total, 0, ",", ".");
	$utilidad_formatted = number_format($i->utilidad, 0, ",", ".");
	$margen_porc_formatted = number_format($i->margen_ganancia, 2, ",", ".");

	$total_ventas += $i->total;
	$total_utilidad += $i->utilidad;
	$total_costo += $i->costo;

	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="">
			    <th width="50%" style="">$i->user</th>
             	<th width="50%" align="right" style="">$total_formatted</th>
			</tr>
		</table>

EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');

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
				<td width="50%" style="" align="left">RESULTADO (+)</td>
				<td width="50%" style="" align="right">$total_ventas_formatted</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');


/*

   FIN vendedores que mas vendieron

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

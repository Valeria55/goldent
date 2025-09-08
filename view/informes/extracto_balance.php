<?php

// require_once('plugins/tcpdf/pdf/tcpdf_include.php');
require_once('plugins/tcpdf2/tcpdf.php');

// $moneda = $this->venta_tmp->ObtenerMoneda();
$Meses = array(
	'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
	'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
);

$desde = date("d-m-Y", strtotime($_REQUEST["desde"]));
$hasta = date("d-m-Y", strtotime($_REQUEST["hasta"]));

// $desde = '2023-09-01';
// $hasta = '2023-09-30';


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
$pdf->AddPage('L', 'A4');
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
		<h3 align="center">BALANCE AFRODITE SAN LORENZO</h3>
		<h3 align="center">Informe de la fecha $desde hasta $hasta</h3>
		<p>Generado el $fechaHoraHoy</p>
	

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

//Traer array y llamar a funciones por separado
// call_user_func(array($controller, $accion));

/*-----------------------------------
			HEADER	SALDO ANTERIOR
	---------------------------------*/
$header_anterior = <<<EOF
		<h3 align="center">SALDO ANTERIOR</h3>

		<table width"100%" style="$header_table_style">
		<tr align="center">
			<th width="20%" style=""><b>Metodo</b></th>
            <th width="30%" style=""align="right"><b>Ingresos</b></th>
			<th width="30%" style=""align="right"><b>Egresos</b></th>
			<th width="20%" style=""align="right"><b>Diferencia</b></th>
			</tr>
		</table>

EOF;
/*-----------------------------------
			ITEMS SALDO ANTERIOR
	---------------------------------*/
$indice = 0;
$total = 0;
$suma_total = 0;
$totalSuma = 0;
$ingresos=0;
$egresos=0;
$venta=0;
foreach ($this->metodo->SaldoAnteriorBalance($_REQUEST["desde"]) as $e) :

	$ingresos = number_format($e->ingresos, 0, ",", ".");
	$egresos = number_format($e->egresos, 0, ",", ".");
	$diferencia = number_format($e->ingresos-$e->egresos, 0, ",", ".");
	$fecha = date("Y-m-d", strtotime($e->fecha));

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$items_anterior .= <<<EOF

	<table width"100%"  style="$bg $body_table_style">
	<tr align="left">
		<th width="20%" style="">$e->metodo</th>
		<th width="30%" style=""align="right">$ingresos</th>
		<th width="30%" style=""align="right">$egresos</th>
		<th width="20%" style=""align="right">$diferencia</th>
		</tr>
	</table>

EOF;

	//$pdf->writeHTML($body_venta, false, false, false, false, '');

	$indice++;
	$ing += $e->ingresos;
	$egr += $e->egresos;
	$dif += $e->ingresos-$e->egresos;
endforeach;
	
/*-----------------------------------
			FOOTER	SALDO ANTERIOR
	---------------------------------*/
	$ing = number_format($ing, 0, ",", ".");
	$egr = number_format($egr, 0, ",", ".");
	$dif = number_format($dif, 0, ",", ".");

	$footer_anterior= <<<EOF
	<table width"100%" style="$header_table_style">
	<tr align="center">
		<th width="20%" style=""><b>Total</b></th>
		<th width="30%" style=""align="right">$ing</th>
		<th width="30%" style=""align="right">$egr</th>
		<th width="20%" style=""align="right">$dif</th>
		</tr>
	</table>

EOF;
/*------------------------------------
			HEADER	SALDO ACTUALES
	---------------------------------*/
	$header_actual = <<<EOF
	<h3 align="center">SALDO ACTUAL</h3>

	<table width"100%" style="$header_table_style">
	<tr align="center">
		<th width="20%" style=""><b>Metodo</b></th>
		<th width="30%" style=""align="right"><b>Ingresos</b></th>
		<th width="30%" style=""align="right"><b>Egresos</b></th>
		<th width="20%" style=""align="right"><b>Diferencia</b></th>
		</tr>
	</table>

EOF;
/*-----------------------------------
		ITEMS SALDO ACTUAL
---------------------------------*/
$indice = 0;
$total = 0;
$suma_total = 0;
$totalSuma = 0;
$ingresos=0;
$egresos=0;
$venta=0;
foreach ($this->metodo->SaldoActualBalance($_REQUEST["hasta"]) as $e) :

$ingresos = number_format($e->ingresos, 0, ",", ".");
$egresos = number_format($e->egresos, 0, ",", ".");
$diferencia = number_format($e->ingresos-$e->egresos, 0, ",", ".");
$fecha = date("Y-m-d", strtotime($e->fecha));

$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

$items_actual .= <<<EOF

<table width"100%"  style="$bg $body_table_style">
<tr align="left">
	<th width="20%" style="">$e->metodo</th>
	<th width="30%" style=""align="right">$ingresos</th>
	<th width="30%" style=""align="right">$egresos</th>
	<th width="20%" style=""align="right">$diferencia</th>
	</tr>
</table>

EOF;

//$pdf->writeHTML($body_venta, false, false, false, false, '');

$indice++;
$ing += $e->ingresos;
$egr += $e->egresos;
$dif += $e->ingresos-$e->egresos;
endforeach;

/*-----------------------------------
		FOOTER	SALDO ACTUAL
---------------------------------*/
$ing = number_format($ing, 0, ",", ".");
$egr = number_format($egr, 0, ",", ".");
$dif = number_format($dif, 0, ",", ".");

$footer_actual= <<<EOF
<table width"100%" style="$header_table_style">
<tr align="center">
	<th width="20%" style=""><b>Total</b></th>
	<th width="30%" style=""align="right">$ing</th>
	<th width="30%" style=""align="right">$egr</th>
	<th width="20%" style=""align="right">$dif</th>
	</tr>
</table>

EOF;
/*------------------------------
				COMPRAS
	---------------------------------*/

$header_compra = <<<EOF
		<h3 align="center">COMPRAS</h3>

		<table width"100%" style="$header_table_style">
		<tr align="center">
			<th width="25%" style=""><b>Fecha</b></th>
                <th width="30%" style=""><b>Proveedor</b></th>
                <th width="25%" style=""align="right"><b>Total</b></th>
				<th width="20%" style=""><b>Metodo</b></th>
			</tr>
		</table>

EOF;


//$pdf->writeHTML($header_compra, false, false, false, false, '');


$indice = 0;
$total = 0;
$suma_total = 0;
$totalSuma = 0;

foreach ($this->egreso->ListarExtractoCompras($_REQUEST["desde"], $_REQUEST["hasta"]) as $e) :

	$total = number_format($e->monto, 0, ",", ".");
	$fecha = date("Y-m-d", strtotime($e->fecha));

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$body_compra .= <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			<th width="20%" style="" align="left">$fecha</th>
                <th width="35%" style="font-size:6px;" align="left">$e->nombre</th>
                <th width="25%" style="" align="right">$total </th>
				<th width="20%" style=" font-size:6px; " align="center">$e->forma_pago</th>
			</tr>
		</table>

EOF;

	////$pdf->writeHTML($body_compra, false, false, false, false, '');

	$indice++;
	$suma_total += $e->monto;
	$compra += $e->monto;
endforeach;
$totalSuma = number_format($suma_total, 0, ",", ".");
$footer_compra = <<<EOF
		
		<table width"100%" style="$header_table_style" align="left">
			<tr align="center" style="padding:10px">
                <th width="40%" style="" align="left";><b>RESULTADOS (-)</b></th>
             	<th width="40%" style="font-size:10px;" align="right"><b>$totalSuma</b></th>
				<th width="20%" style="" align="left";><b></b></th>
			</tr>
		</table>
		<br><br>

EOF;

//$pdf->writeHTML($html1, false, false, false, false, '');
/*------------------------------
				FIN COMPRAS
	---------------------------------*/

/*------------------------------
				VENTAS
	---------------------------------*/
$header_venta = <<<EOF
<h3 align="center">VENTAS</h3>

<table width"100%" style="$header_table_style">
<tr align="center">
			<th width="20%" style=""><b>Fecha</b></th>
                <th width="35%" style=""><b>Cliente</b></th>
                <th width="25%" style=""align="right"><b>Total</b></th>
				<th width="20%" style=""><b>Metodo</b></th>
			</tr>
</table>

EOF;


//$pdf->writeHTML($header_venta, false, false, false, false, '');

$indice = 0;
$total = 0;
$suma_total = 0;
$totalSuma = 0;
foreach ($this->ingreso->ListarExtracto($_REQUEST["desde"], $_REQUEST["hasta"]) as $e) :

	$total = number_format($e->monto, 0, ",", ".");
	$fecha = date("Y-m-d", strtotime($e->fecha));

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$body_venta .= <<<EOF

	<table width"100%" style="$bg $body_table_style">
		<tr align="center">
		<th width="20%" style="" align="left">$fecha</th>
			<th width="35%" style="" align="left">$e->nombre</th>
			<th width="25%" style="" align="right">$total </th>
			<th width="20%" style=" font-size:6px; " align="center">$e->forma_pago</th>
		</tr>
	</table>
EOF;

	//$pdf->writeHTML($body_venta, false, false, false, false, '');

	$indice++;
	$suma_total += $e->monto;
	$venta += $e->monto;
endforeach;
$totalSuma = number_format($suma_total, 0, ",", ".");
$footer_venta = <<<EOF

<table width"100%" style="$header_table_style" align="left">
			<tr align="center" style="padding:10px">
                <th width="40%" style="" align="left";><b>RESULTADOS (-)</b></th>
             	<th width="40%" style="font-size:10px;" align="right"><b>$totalSuma</b></th>
				<th width="20%" style="" align="left";><b></b></th>
			</tr>
		</table>
<br><br>

EOF;

//$pdf->writeHTML($footer_venta, false, false, false, false, '');
/*------------------------------
		FIN VENTAS
---------------------------------*/
/*------------------------------
				GASTOS FIJOS 
	---------------------------------*/
$heder_gasto_fijo = <<<EOF
		<h3 align="center">GASTOS FIJOS</h3>

		<table width"100%" style="$header_table_style">
		<tr align="center">
				<th width="20%" style=""><b>Fecha</b></th>
                <th width="40%" style=""><b>Concepto</b></th>
                <th width="20%" style="" align="right"><b>Total</b></th>
				<th width="20%" style=""><b>Metodo</b></th>
			</tr>
		</table>

EOF;


//$pdf->writeHTML($heder_gasto_fijo, false, false, false, false, '');


$indice = 0;
$total = 0;
$suma_total = 0;
$totalSuma = 0;

foreach ($this->egreso->ListarExtractoGastosFijos($_REQUEST["desde"], $_REQUEST["hasta"]) as $e) :

	$total = number_format($e->monto, 0, ",", ".");
	$fecha = date("Y-m-d", strtotime($e->fecha));
	$mes = date("M", strtotime($e->fecha_gasto_fijo));
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$body_gasto_fijo .= <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
				<th width="20%" style="" align="left">$fecha</th>
                <th width="40%" style=" font-size:5px; " align="left">$e->concepto</th>
                <th width="20%" style="" align="right">$total </th>
				<th width="20%" style=" font-size:6px; " align="center">$e->forma_pago</th>
			</tr>
		</table>

EOF;

	//$pdf->writeHTML($body_gasto_fijo, false, false, false, false, '');

	$indice++;
	$suma_total += $e->monto;
	$fijo += $e->monto;
endforeach;
$totalSuma = number_format($suma_total, 0, ",", ".");
$footer_gasto_fijo = <<<EOF
		
<table width"100%" style="$header_table_style" align="left">
<tr align="center" style="padding:10px">
	<th width="40%" style="" align="left";><b>RESULTADOS (-)</b></th>
	 <th width="40%" style="font-size:10px;" align="right"><b>$totalSuma</b></th>
	<th width="20%" style="" align="left";><b></b></th>
</tr>
</table>
		<br><br>

EOF;

//$pdf->writeHTML($footer_gasto_fijo, false, false, false, false, '');
/*------------------------------
				FIN GASTOS FIJOS
	---------------------------------*/
/*------------------------------
				GASTOS VARIABLES
	---------------------------------*/
$heder_gasto_variable = <<<EOF
<h3 align="center">GASTOS VARIABLES</h3>

<table width"100%" style="$header_table_style">
	<tr align="center">
		<th width="20%" style=""><b>Fecha</b></th>
		<th width="40%" style=""><b>Concepto</b></th>
		<th width="20%" style="" align="right"><b>Total</b></th>
		<th width="20%" style=""><b>Metodo</b></th>
	</tr>
</table>

EOF;


//$pdf->writeHTML($html1, false, false, false, false, '');


$indice = 0;
$total = 0;
$suma_total = 0;
$totalSuma = 0;

foreach ($this->egreso->ListarExtractoVariable($_REQUEST["desde"], $_REQUEST["hasta"]) as $va) :

	$total = number_format($va->monto, 0, ",", ".");
	$fecha = date("Y-m-d", strtotime($va->fecha));
	$mes = date("M", strtotime($va->fecha_gasto_fijo));
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$body_gasto_variable .= <<<EOF

<table width"100%" style="$bg $body_table_style">
	<tr align="center">
		<th width="20%" style="" align="left">$fecha</th>
		<th width="40%" style="font-size:5px; " align="left">$va->concepto</th>
		<th width="20%" style="" align="right">$total </th>
		<th width="20%" style=" font-size:6px; " align="center">$va->forma_pago</th>
	</tr>
</table>

EOF;

	//$pdf->writeHTML($body_gasto_variable, false, false, false, false, '');

	$indice++;
	$suma_total += $va->monto;
	$variable += $va->monto;
endforeach;
$totalSuma = number_format($suma_total, 0, ",", ".");
$footer_gasto_variable = <<<EOF

<table width"100%" style="$header_table_style" align="left">
			<tr align="center" style="padding:10px">
                <th width="40%" style="" align="left";><b>RESULTADOS (-)</b></th>
             	<th width="40%" style="font-size:10px;" align="right"><b>$totalSuma</b></th>
				<th width="20%" style="" align="left";><b></b></th>
			</tr>
		</table>
<br><br>

EOF;

//$pdf->writeHTML($body_gasto_variable, false, false, false, false, '');
/*------------------------------
		FIN GASTOS VARIABLES
---------------------------------*/
/*------------------------------
				MOVIMIENTO
	---------------------------------*/
$heder_movimiento = <<<EOF
<h3 align="center">MOVIMIENTOS</h3>

<table width"100%" style="$header_table_style">
	<tr align="center">
		<th width="20%" style=""><b>Fecha</b></th>
		<th width="40%" style=""><b>Concepto</b></th>
		<th width="20%" style="" align="right"><b>Total</b></th>
		<th width="20%" style=""><b>Metodo</b></th>
	</tr>
</table>

EOF;


//$pdf->writeHTML($heder_movimiento, false, false, false, false, '');


$indice = 0;
$total = 0;
$suma_total = 0;
$totalSuma = 0;

foreach ($this->egreso->ListarExtractoMovimiento($_REQUEST["desde"], $_REQUEST["hasta"]) as $e) :

	$total = number_format($e->monto, 0, ",", ".");
	$mes = date("M", strtotime($e->fecha_gasto_fijo));
	$fecha = date("Y-m-d", strtotime($e->fecha));
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$body_movimiento .= <<<EOF

<table width"100%" style="$bg $body_table_style">
	<tr align="center">
		<th width="20%" style="" align="left">$fecha</th>
		<th width="40%" style=" font-size:5px; "align="left">$e->concepto</th>
		<th width="20%" style="" align="right">$total </th>
		<th width="20%" style=" font-size:6px; " align="center">$e->forma_pago</th>
	</tr>
</table>

EOF;

	//$pdf->writeHTML($body_movimiento, false, false, false, false, '');

	$indice++;
	$suma_total += $e->monto;
	$movimiento += $e->monto;
endforeach;
$totalSuma = number_format($suma_total, 0, ",", ".");
$footer_movimiento = <<<EOF

<table width"100%" style="$header_table_style" align="left">
			<tr align="center" style="padding:10px">
                <th width="40%" style="" align="left";><b>RESULTADOS (-)</b></th>
             	<th width="40%" style="font-size:10px;" align="right"><b>$totalSuma</b></th>
				<th width="20%" style="" align="left";><b></b></th>
			</tr>
		</table>
<br><br>

EOF;

//$pdf->writeHTML($footer_movimiento, false, false, false, false, '');
/*------------------------------
		FIN MOVIMIENTO
---------------------------------*/
/*------------------------------
				GASTO
	---------------------------------*/
$header_gasto = <<<EOF
	<h3 align="center">GASTO</h3>
	
	<table width"100%" style="$header_table_style">
		<tr align="center">
			<th width="20%" style=""><b>Fecha</b></th>
			<th width="40%" style=""><b>Concepto</b></th>
			<th width="20%" style="" align="right"><b>Total</b></th>
			<th width="20%" style=""><b>Metodo</b></th>
		</tr>
	</table>
	
	EOF;


//$pdf->writeHTML($header_gasto, false, false, false, false, '');


$indice = 0;
$total = 0;
$suma_total = 0;
$totalSuma = 0;

foreach ($this->egreso->ListarExtractoGasto($_REQUEST["desde"], $_REQUEST["hasta"]) as $e) :

	$total = number_format($e->monto, 0, ",", ".");
	$mes = date("M", strtotime($e->fecha_gasto_fijo));
	$fecha = date("Y-m-d", strtotime($e->fecha));
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	$body_gasto .= <<<EOF
	
	<table width"100%" style="$bg $body_table_style">
		<tr align="center">
			<th width="20%" style="" align="left">$fecha</th>
			<th width="40%" style=" font-size:5px; " align="left">$e->concepto</th>
			<th width="20%" style="" align="right">$total </th>
			<th width="20%" style=" font-size:6px; " align="center">$e->forma_pago</th>
		</tr>
	</table>
	
	EOF;

	//$pdf->writeHTML($body_gasto, false, false, false, false, '');

	$indice++;
	$suma_total += $e->monto;
	$gasto += $e->monto;
endforeach;
$totalSuma = number_format($suma_total, 0, ",", ".");
$footer_gasto = <<<EOF

	<table width"100%" style="$header_table_style" align="left">
		<tr align="center" style="padding:10px">
			<th width="40%" style="" align="left";><b>RESULTADOS (-)</b></th>
			<th width="40%" style="font-size:10px;" align="right"><b>$totalSuma</b></th>
			<th width="20%" style="" align="left";><b></b></th>
		</tr>
	</table>
	<br><br>

EOF;
//$pdf->writeHTML($footer_gasto, false, false, false, false, '');
/*------------------------------
			FIN GASTO
	---------------------------------*/

/*---------------------------------------
			SALDO ANTERIOR
	------------------------------------*/

$header_anterior_actual = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$header_anterior</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$header_actual</td>
      </tr>
    </table>
EOF;

$pdf->writeHTML($header_anterior_actual, false, false, false, false, '');
$items_anterior_actual = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$items_anterior</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$items_actual</td>
      </tr>
    </table>
EOF;

$pdf->writeHTML($items_anterior_actual, false, false, false, false, '');
$footer_anterior_actual = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$footer_anterior</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$footer_actual</td>
      </tr>
    </table>
EOF;

$pdf->writeHTML($footer_anterior_actual, false, false, false, false, '');


/*------------------------------
			COMPRA VENTA
	---------------------------------*/
$header_Compra_Venta = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$header_compra</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$header_venta</td>
      </tr>
    </table>
EOF;

$pdf->writeHTML($header_Compra_Venta, false, false, false, false, '');
$body_Compra_Venta = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$body_compra</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$body_venta</td>
      </tr>
    </table>

EOF;

$pdf->writeHTML($body_Compra_Venta, false, false, false, false, '');

$footer_Compra_Venta = <<<EOF

  <table  width="100%">
	<tr>
	  <td width="49%">$footer_compra</td>
	  <td width="2%">&nbsp;</td>
	  <td width="49%">$footer_venta</td>
	</tr>
  </table>

EOF;

$pdf->writeHTML($footer_Compra_Venta, false, false, false, false, '');

/*---------------------------------------
			GASTOS FIJOS / VARIABLES
	------------------------------------*/

$header_gastos = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$heder_gasto_fijo</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$heder_gasto_variable</td>
      </tr>
    </table>
EOF;

$pdf->writeHTML($header_gastos, false, false, false, false, '');
$body_gastos = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$body_gasto_fijo</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$body_gasto_variable</td>
      </tr>
    </table>

EOF;

$pdf->writeHTML($body_gastos, false, false, false, false, '');

$footer_gastos = <<<EOF

  <table  width="100%">
	<tr>
	  <td width="49%">$footer_gasto_fijo</td>
	  <td width="2%">&nbsp;</td>
	  <td width="49%">$footer_gasto_variable</td>
	</tr>
  </table>

EOF;

$pdf->writeHTML($footer_gastos, false, false, false, false, '');
/*---------------------------------------
			GASTOS / MOVIMIENTOS
	------------------------------------*/

$header_movimiento = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$header_gasto</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$heder_movimiento</td>
      </tr>
    </table>
EOF;

$pdf->writeHTML($header_movimiento, false, false, false, false, '');
$body_movimiento = <<<EOF

    <table  width="100%">
      <tr>
        <td width="49%">$body_gasto</td>
        <td width="2%">&nbsp;</td>
        <td width="49%">$body_movimiento</td>
      </tr>
    </table>

EOF;

$pdf->writeHTML($body_movimiento, false, false, false, false, '');

$footer_movimiento = <<<EOF

  <table  width="100%">
	<tr>
	  <td width="49%">$footer_gasto</td>
	  <td width="2%">&nbsp;</td>
	  <td width="49%">$footer_movimiento</td>
	</tr>
  </table>

EOF;

$pdf->writeHTML($footer_movimiento, false, false, false, false, '');

/*---------------------------------------
			GASTOS / MOVIMIENTOS
	------------------------------------*/
$diferencia = number_format($venta - $compra - $fijo - $variable, 0, ",", ".");

$respuesta = <<<EOF

<table width"100%" style="$header_table_style" align="left">
	<tr align="center" style="padding:10px">
		<th width="60%" style="" align="left";><b>Resultado</b></th>
		<th width="20%" style="" align="right"><b>$diferencia</b></th>
		<th width="20%" style="" align="right"><b></b></th>
	</tr>
</table>

EOF;

$pdf->writeHTML($respuesta, false, false, false, false, '');
// <tr align="center" style="padding:10px">
// 		<th width="60%" style="" align="left";><b>Compras</b></th>
// 		<th width="20%" style="" align="right"><b>$compra</b></th>
// 		<th width="20%" style="" align="right"><b></b></th>
// 	</tr>
// 	<tr align="center" style="padding:10px">
// 		<th width="60%" style="" align="left";><b>Gastos Fijos</b></th>
// 		<th width="20%" style="" align="right"><b>$fijo</b></th>
// 		<th width="20%" style="" align="right"><b></b></th>
// 	</tr>
// 	<tr align="center" style="padding:10px">
// 		<th width="60%" style="" align="left";><b>Gastos Variables</b></th>
// 		<th width="20%" style="" align="right"><b>$variable</b></th>
// 		<th width="20%" style="" align="right"><b></b></th>
// 	</tr>
// 	<tr align="center" style="padding:10px">
// 		<th width="60%" style="" align="left";><b>Gastos</b></th>
// 		<th width="20%" style="" align="right"><b>$gasto</b></th>
// 		<th width="20%" style="" align="right"><b></b></th>
// 	</tr>
// 	<tr align="center" style="padding:10px">
// 		<th width="60%" style="" align="left";><b>Movimientos</b></th>
// 		<th width="20%" style="" align="right"><b>$movimiento</b></th>
// 		<th width="20%" style="" align="right"><b></b></th>
// 	</tr>
$pdf->Output("Informe de la fecha $desde hasta $hasta.pdf", 'I');
// $pdf->Output('Folder Label.pdf','D');


//============================================================+
// END OF FILE
//============================================================+

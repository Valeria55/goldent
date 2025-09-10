<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
<?php

require_once('plugins/tcpdf2/tcpdf.php');

//$id_cierre = $_GET['id_usuario'];
//buscar usuario a partir del campo id_usuario de ciere_id
$usuario = $this->usuario->Obtener($cierre_id->id_usuario);
$id_usuario = $cierre_id->id_usuario;
$desdeV = date("d/m/Y H:i", strtotime($cierre_id->fecha_apertura));
$hastaV = date("d/m/Y H:i", strtotime($cierre_id->fecha_cierre));
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
$cierre = $this->cierre->ListarCierreUsuario($cierre_id->fecha_apertura, $id_usuario);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('L', 'A4');
$fechahoy = date("d/m/Y");
$horahoy = date("H:i");

/* ================================ 
	ESTILOS PARA LAS TABLAS
================================ */
$body_table_style = 'font-size:9px; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;';
$header_table_style = 'border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:10px; background-color: #c0c0c0; color: #202020; font-weight: bold;';
// ********************************


$inicial = number_format($cierre_id->monto_apertura, 2, ",", ".");
$caja_inicial = $cierre_id->monto_apertura;
$real = number_format($cierre_id->cot_real, 2, ",", ".");
$dolar = number_format($cierre_id->cot_dolar, 2, ",", ".");

$html1 = <<<EOF
		<h3 align="center">Caja $usuario->user </h3>
		<h5 align="center">Desde $desdeV hasta $hastaV </h5>
		<p>Generado a las $horahoy de la fecha $fechahoy</p>
	
<table width="100%">
	<tr>
	  <td>
		<table width="60%" style="border: 1px solid #333; float:right">
		  <tr>
            <th style="background-color: #c0c0c0; color: #202020; font-weight: bold; text-align:center" colspan="2">Cotización del día
            </th>
          </tr>
		  <tr>
			<td style="border-left-width:1px ; border-right-width:1px ; border-bottom-width:1px; text-align:center">Dolares</td>
			<td style="border-left-width:1px ; border-bottom-width:1px; border-right-width:1px; text-align:center">$real</td>
		  </tr>
		  <tr>
			<td style="border-left-width:1px ; border-right-width:1px ;  text-align:center">Guaraníes</td>
			<td style="border-left-width:1px ; border-right-width:1px; text-align:center">$dolar</td>
			</tr>
		</table>
		</td>
		<td>
		
		</td>
		</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
/* ------------------------------------------------
         venta caja CONTADO EFECTIVO por VENDEDOR y RANGO de FECHA 
        -------------------------------------------------*/

if ($_SESSION['nivel'] == 1) {
	$precio_costo_string = '<th width="7%" style="border-left-width:1px ; border-right-width:1px">P.Costo</th>';
} else {
	$precio_costo_string = '';
}

$html1 = <<<EOF
		<h3 align="center">Ventas Efectivo</h3>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="11%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
			    <th width="13%" style="border-left-width:1px ; border-right-width:1px">Vendedor</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">P/real</th>
             	
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">Des.</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">P/venta</th>
             	$precio_costo_string
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">GS</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">RS</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">USD</th>
             	<th width="18%" style="border-left-width:1px ; border-right-width:1px">Pago</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$tventa = 0;
$tsubtotal = 0;
$tdescuento = 0;
$subtotalVenta = 0;
$totalVenta = 0;
$totalDescuento = 0;
$totalContadoEfec = 0;
$gs = 0;
$usd = 0;
$rs = 0;
$indice = 0;
// Initialize total for GS column (will contain sale totals)
$total_ventas_efectivo_gs = 0;

foreach ($this->venta->ListarRangoSinAnularContado($cierre_id->fecha_apertura, $cierre_id->fecha_cierre, $id_usuario) as $r):
	$cobroGS = 0;
	$cobroRS = 0;
	$cobroUSD = 0;

	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

	if ($r->efectivo != null) {
		$tventa += $r->total;
		$tsubtotal += $r->subtotal;
		$tdescuento += $r->descuento;
		$totalCosto += $r->costo;
		
		// Add sale total to GS column total
		$total_ventas_efectivo_gs += $r->total;

		// Initialize payment form variable
		$forma_pago = '';

		foreach ($this->ingreso->ObtenerCobro($r->id_venta) as $ingresos):
			//var_dump($ingresos->moneda);

			if ($ingresos->forma_pago == 'Efectivo') {

				if ($ingresos->moneda == 'GS') {
					$gs += $ingresos->monto;
					$c_gs += $ingresos->monto;
				} elseif ($ingresos->moneda == 'RS') {
					$cobroRS = number_format($ingresos->monto, 2, ",", ".");
					$rs += $ingresos->monto;
					$c_rs += $ingresos->monto;
				} elseif ($ingresos->moneda == 'USD') {
					$cobroUSD = number_format($ingresos->monto, 2, ",", ".");
					$usd += $ingresos->monto;
					$c_usd += $ingresos->monto;
				}

				$forma_pago = $ingresos->forma_pago;
			}
		endforeach;
		
		$subtotal = number_format($r->subtotal, 2, ",", ".");
		$total = number_format(($r->total), 2, ",", ".");
		$descuento = $r->descuento;
		$descuentoV = number_format($descuento, 2, ",", ".");
		$costo = number_format($r->costo, 2, ",", ".");
		$ganancia = number_format(($r->total - $r->costo), 0, ",", ".");
		$hora = date("d/m/Y H:i", strtotime($r->fecha_venta));
		
		// Display total sale amount in GS column
		$total_venta_gs = number_format($r->total, 2, ",", ".");
		
		session_start();
		if ($_SESSION['nivel'] == 1) {
			$precio_costo_cuerpo = '<td width="7%" style="border-left-width:1px ; border-right-width:1px">' . $costo . '</td>';
		} else {
			$precio_costo_cuerpo = '';
		}

		$html1 = <<<EOF
		
	<table width"100%" style="$bg $body_table_style">
		<tr align="right">
			<td width="11%" align="center" style="border-left-width:1px ; border-right-width:1px">$hora</td>
			<td width="13%"  align="left" style="border-left-width:1px ; border-right-width:1px">($r->id_venta ) $r->vendedor_salon</td>
			<td width="8%" style="border-left-width:1px ; border-right-width:1px">$subtotal</td>
			
			<td width="8%" style="border-left-width:1px ; border-right-width:1px">$descuentoV</td>
			<td width="8%" style="border-left-width:1px ; border-right-width:1px">$total</td>
			$precio_costo_cuerpo
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$total_venta_gs</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$cobroRS</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$cobroUSD</td>
			<td width="18%" style="border-left-width:1px ; border-right-width:1px">$forma_pago</td>
		</tr>
	</table>

EOF;

		$pdf->writeHTML($html1, false, false, false, false, '');
	}
endforeach;

// Fix the number formatting logic - ensure numeric values before formatting
$tventa_formatted = number_format($tventa, 2, ",", ".");
$tsubtotal_formatted = number_format($tsubtotal, 2, ",", ".");
$tdescuento_formatted = number_format($tdescuento, 2, ",", ".");
$tcosto_formatted = number_format($totalCosto, 2, ",", ".");

$gs_formatted = number_format($total_ventas_efectivo_gs, 2, ",", ".");
$usd_formatted = number_format($usd, 2, ",", ".");
$rs_formatted = number_format($rs, 2, ",", ".");

session_start();
if ($_SESSION['nivel'] == 1) {
	$precio_costo_string = '<th width="7%" style="border-left-width:1px ; border-right-width:1px">' . $tcosto_formatted . '</th>';
	$precio_costo_cuerpo = '<td width="7%" style="border-left-width:1px ; border-right-width:1px">' . $costo . '</td>';
} else {
	$precio_costo_string = '';
	$precio_costo_cuerpo = '';
}

$html1 = <<<EOF
		
	<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="11%" style="border-left-width:1px ; border-right-width:1px"></th>
			    <th width="13%" style="border-left-width:1px ; border-right-width:1px"></th>
                <th width="8%" style="border-left-width:1px ; border-right-width:1px">$tsubtotal_formatted</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">$tdescuento_formatted</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">$tventa_formatted</th>
             	$precio_costo_string
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$gs_formatted</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$rs_formatted</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$usd_formatted</th>
				<th width="18%" style="border-left-width:1px ; border-right-width:1px"></th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

/*       ------------------------------------------------
         venta caja CONTADO <> EFECTIVO por VENDEDOR y RANGO de FECHA 
        -------------------------------------------------*/
if ($_SESSION['nivel'] == 1) {
	$precio_costo_string = '<th width="7%" style="border-left-width:1px ; border-right-width:1px">P.Costo</th>';
} else {
	$precio_costo_string = '';
}
$html1 = <<<EOF
<br>
		<h3 align="center">Ventas sin Efectivo</h3>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="13%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">Vendedor</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">Subtotal</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">Des.</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">Total</th>
				$precio_costo_string
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">GS</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">RS</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">USD</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Pago</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCostot = 0;
$subtotalVenta = 0;
$totalVenta = 0;
$totalDescuento = 0;
$totalContadoEfec = 0;
$ttotalCostsubtotal = 0;
// Variables específicas para "Ventas sin Efectivo"
$tventa_sin_efectivo = 0;
$tsubtotal_sin_efectivo = 0;
$tdescuento_sin_efectivo = 0;
$costoT = 0;
$indice = 0;
// Inicializar variables para evitar warnings si no hay ventas sin efectivo
$gss = 0;
$usdss = 0;
$rss = 0;
// Initialize total for GS column (will contain sale totals)
$total_ventas_sin_efectivo_gs = 0;

foreach ($this->venta->ListarRangoSinAnularContado($cierre_id->fecha_apertura, $cierre_id->fecha_cierre, $id_usuario) as $t):
	$cobroGS = 0;
	$cobroRS = 0;
	$cobroUSD = 0;

	if ($t->otros != null) {
		$tventa_sin_efectivo += $t->total;
		$tsubtotal_sin_efectivo += $t->subtotal;
		$tdescuento_sin_efectivo += $t->descuento;
		$totalCostot += $t->costo;
		
		// Add sale total to GS column total
		$total_ventas_sin_efectivo_gs += $t->total;

		$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

		// Initialize payment form variable
		$forma_pago = '';

		foreach ($this->ingreso->ObtenerCobro($t->id_venta) as $ingres):

			if ($ingres->forma_pago != 'Efectivo') {

				if ($ingres->moneda == 'GS') {
					$gss += $ingres->monto;
					$c_gss += $ingres->monto;
				} elseif ($ingres->moneda == 'RS') {
					$cobroRSs = number_format($ingres->monto, 2, ",", ".");
					$rss += $ingres->monto;
					$c_rss += $ingres->monto;
				} elseif ($ingres->moneda == 'USD') {
					$cobroUSD = number_format($ingres->monto, 2, ",", ".");
					$usdss += $ingres->monto;
					$c_usdss += $ingres->monto;
				}

				$forma_pago = $ingres->forma_pago;
			}
		endforeach;

		$subtotal = number_format($t->subtotal, 2, ",", ".");
		$total = number_format(($t->total), 2, ",", ".");
		$descuento = $t->descuento;
		$descuentoV = number_format($descuento, 2, ",", ".");
		$costoT = number_format($t->costo, 2, ",", ".");
		$ganancia = number_format(($t->total - $t->costo), 0, ",", ".");
		$hora = date("d/m/Y H:i", strtotime($t->fecha_venta));
		
		// Display total sale amount in GS column
		$total_venta_sin_efectivo_gs = number_format($t->total, 2, ",", ".");
		
		if ($_SESSION['nivel'] == 1) {
			$precio_costo_cuerpo = '<td width="7%" style="border-left-width:1px ; border-right-width:1px">' . $costoT . '</td>';
		} else {
			$precio_costo_cuerpo = '';
		}
		$html1 = <<<EOF
		
	<table width"100%" style="$bg $body_table_style">
		<tr align="right">
			<td width="13%" align="center" style="border-left-width:1px ; border-right-width:1px">$hora</td>
			<td width="12%"  align="left" style="border-left-width:1px ; border-right-width:1px">($t->id_venta) $t->vendedor_salon</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$subtotal</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$descuentoV</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$total</td>
			$precio_costo_cuerpo
			<td width="11%" style="border-left-width:1px ; border-right-width:1px">$total_venta_sin_efectivo_gs</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$cobroRSs</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$cobroUSD</td>
			<td width="12%" style="border-left-width:1px ; border-right-width:1px">$forma_pago</td>
		</tr>
	</table>

EOF;

		$pdf->writeHTML($html1, false, false, false, false, '');
	}
endforeach;

// Fix number formatting for "Ventas sin Efectivo" totals
$tventa_sin_efectivo_formatted = number_format($tventa_sin_efectivo, 2, ",", ".");
$tsubtotal_sin_efectivo_formatted = number_format($tsubtotal_sin_efectivo, 2, ",", ".");
$tdescuento_sin_efectivo_formatted = number_format($tdescuento_sin_efectivo, 2, ",", ".");
$tcosto_tarjeta_formatted = number_format($totalCostot, 2, ",", ".");

session_start();
if ($_SESSION['nivel'] == 1) {
	$precio_costo_string = '<th width="7%" style="border-left-width:1px ; border-right-width:1px">' . $tcosto_tarjeta_formatted . '</th>';
} else {
	$precio_costo_string = '';
}
$gss_formatted = number_format($total_ventas_sin_efectivo_gs, 2, ",", ".");
$usdss_formatted = number_format($usdss, 2, ",", ".");
$rss_formatted = number_format($rss, 2, ",", ".");


$html1 = <<<EOF
		
	<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="13%" style="border-left-width:1px ; border-right-width:1px"></th>
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px"></th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$tsubtotal_sin_efectivo_formatted</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$tdescuento_sin_efectivo_formatted</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$tventa_sin_efectivo_formatted</th>
				 $precio_costo_string
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">$gss_formatted</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$rss_formatted</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$usdss_formatted</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px"></th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

/* ------------------------------------------------
                OTROS INGRESOS 
-------------------------------------------------*/
$totalIngreso = 0;

$html1 = <<<EOF
		<br>
		<h3 align="center">Otros Ingresos</h3>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="15%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
			    <th width="15%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
			    <th width="26%" style="border-left-width:1px ; border-right-width:1px">Nombre</th>
			    
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">GS</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">RS</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">USD</th>
             	<th width="14%" style="border-left-width:1px ; border-right-width:1px">Metodo</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$indice = 0;
$totalGs = 0; // Initialize the total for GS column
foreach ($this->ingreso->ListarOtrosIngresos($cierre_id->fecha_apertura, $cierre_id->fecha_cierre, $cierre_id->id_usuario) as $i):
	$iGS = '';
	$iRS = '';
	$iUSD = '';
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';
	if ($i->forma_pago == 'Efectivo') {

		if ($i->moneda == 'GS') {
			$iGS = number_format($i->monto, 0, ",", ".");
			$iRS = 0;
			$iUSD = 0;
			$igs += $i->monto;
			$igss += $i->monto;
		} elseif ($i->moneda == 'RS') {
			$iRS = number_format($i->monto, 2, ",", ".");
			$iGS = 0;
			$iUSD = 0;
			$irs += $i->monto;
			$irss += $i->monto;
		} elseif ($i->moneda == 'USD') {
			$iUSD = number_format($i->monto, 2, ",", ".");
			$iRS = 0;
			$iGS = 0;
			$iusd += $i->monto;
			$iusds += $i->monto;
		}
	} else if ($i->forma_pago != 'Efectivo') {

		if ($i->moneda == 'GS') {
			$iGSi = number_format($i->monto, 0, ",", ".");
			$iRSi = 0;
			$iUSDi = 0;
			$igsi += $i->monto;
			$igssi += $i->monto;
		} elseif ($i->moneda == 'RS') {
			$iRSi = number_format($i->monto, 2, ",", ".");
			$iGSi = 0;
			$iUSDi = 0;
			$irsi += $i->monto;
			$irssi += $i->monto;
		} elseif ($i->moneda == 'USD') {
			$iUSDi = number_format($i->monto, 2, ",", ".");
			$iRSi = 0;
			$iGSi = 0;
			$iusdi += $i->monto;
			$iusdsi += $i->monto;
		}
	}
	// Always assign the total amount to GS column and add to total
	$monto_gs_display = number_format($i->monto, 0, ",", ".");
	$totalGs += $i->monto; // Sum all amounts for footer
	$iUSDi = '';
	$iRSi = '';

	$fecha = date("d/m/Y H:i", strtotime($i->fecha));
	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="left">
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px" align="center">$fecha</td>
			    
				<td width="15%" style="border-left-width:1px ; border-right-width:1px">$i->concepto</td>
				<td width="26%" style="border-left-width:1px ; border-right-width:1px">$i->nombre</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto_gs_display</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px" align="right">$iRS $iRSi</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px" align="right">$iUSD $iUSDi</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px">$i->forma_pago</td>
			</tr>
		</table>

EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');
endforeach;

// Fix formatting for "Otros Ingresos" totals
$igs_formatted = number_format($totalGs, 0, ",", ".");
$irs_formatted = number_format($irs, 2, ",", ".");
$iusd_formatted = number_format($iusd, 2, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
			    <td width="26%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px" align="right">$igs_formatted</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px" align="right">$irs_formatted</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px" align="right">$iusd_formatted</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px"></td>
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
foreach ($this->metodo->ListarTodos() as $m) {
	$pagos['' . $m->metodo . ''] = 0;
}

foreach ($this->cierre->ListarMetodosCierre($cierre_id->fecha_apertura, $cierre_id->fecha_cierre, $cierre_id->id_usuario) as $r):

	if ($r->anulado != 1) {
		$pagos['' . $r->forma_pago . ''] += $r->monto;
		$total += $r->monto;
	}

endforeach;

foreach ($this->metodo->ListarTodos() as $m):

	$metodo = number_format($pagos['' . $m->metodo . ''], 2, ".", ",");

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
/*==============================================================
        EGRESOS
================================================================*/

$totalEgreso = 0;

$html1 = <<<EOF
		<br>
		<h1 align="center">Egresos</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="15%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
			    <th width="15%" style="border-left-width:1px ; border-right-width:1px">Nombre</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">GS</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">RS</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">USD</th>
             	<th width="14%" style="border-left-width:1px ; border-right-width:1px">Metodo</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$indice = 0;
foreach ($this->egreso->ListarOtrosEgresos($cierre_id->fecha_apertura, $cierre_id->fecha_cierre, $cierre_id->id_usuario) as $e):
	$eGS = '';
	$eRS = '';
	$eUSD = '';
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';
	if ($e->forma_pago == 'Efectivo') {

		if ($e->moneda == 'GS') {
			$eGS = number_format($e->monto, 0, ",", ".");
			$eRS = 0;
			$eUSD = 0;
			$egs += $e->monto;
			$egss += $e->monto;
		} elseif ($e->moneda == 'RS') {
			$eRS = number_format($e->monto, 2, ",", ".");
			$eGS = 0;
			$eUSD = 0;
			$ers += $e->monto;
			$erss += $e->monto;
		} elseif ($e->moneda == 'USD') {
			$eUSD = number_format($e->monto, 2, ",", ".");
			$eRS = 0;
			$eGS = 0;
			$eusd += $e->monto;
			$eusds += $e->monto;
		}
	}
	$eGSe = '';
	$eUSDe = '';
	$eRSe = '';
	if ($e->forma_pago != 'Efectivo') {

		if ($e->moneda == 'GS') {
			$eGSe = number_format($e->monto, 0, ",", ".");
			$eRSe = 0;
			$eUSDe = 0;
			$egse += $e->monto;
			$egsse += $e->monto;
		} elseif ($e->moneda == 'RS') {
			$eRSe = number_format($e->monto, 2, ",", ".");
			$eGSe = 0;
			$eUSDe = 0;
			$erse += $e->monto;
			$ersse += $e->monto;
		} elseif ($i->moneda == 'USD') {
			$eUSDe = number_format($e->monto, 2, ",", ".");
			$eRSe = 0;
			$eGSe = 0;
			$eusde += $e->monto;
			$eusdse += $e->monto;
		}
	}

	$fecha = date("d/m/Y H:i", strtotime($e->fecha));
	$monto = number_format($e->monto, 0, ",", ".");
	$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px ">$fecha</td>
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px;  font-size:8px">$e->nombre</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px">$e->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$eGS $eGSe</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$eRS $eRSe</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$eUSD $eUSDe</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px">$e->forma_pago</td>
			</tr>
		</table>

EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');
	$totalEgreso += $e->monto;
endforeach;

// Fix formatting for "Egresos" totals
$egs_formatted = number_format($egs, 0, ",", ".");
$ers_formatted = number_format($ers, 2, ",", ".");
$eusd_formatted = number_format($eusd, 2, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$egs_formatted</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$ers_formatted</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$eusd_formatted</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px"></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');
/* ------------------------------------------------
                RESULTADOS
================================================================*/
$apertura_gs = number_format($cierre_id->monto_apertura, 0, ",", ".");
$apertura_usd = number_format($cierre_id->apertura_usd, 2, ",", ".");
$apertura_rs = number_format($cierre_id->apertura_rs, 2, ",", ".");

$cierre_gs = number_format($cierre_id->monto_cierre, 0, ",", ".");
$cierre_usd = number_format($cierre_id->monto_dolares, 2, ",", ".");
$cierre_rs = number_format($cierre_id->monto_reales, 2, ",", ".");

$diferencia_rs = number_format($cierre_id->monto_reales - (($cierre_id->apertura_rs + $c_rs + $irss - $erss)), 2, ",", ".");
$diferencia_usd = number_format($cierre_id->monto_dolares - (($cierre_id->apertura_usd + $c_usd + $iusds - $eusds)), 2, ",", ".");
$diferencia_gs = number_format($cierre_id->monto_cierre - (($cierre_id->monto_apertura + $c_gs + $igss - $egss)), 0, ",", ".");

$sistema_rs = number_format((($cierre_id->apertura_rs + $c_rs + $irss - $erss)), 2, ",", ".");
$sistema_usd = number_format((($cierre_id->apertura_usd + $c_usd + $iusds - $eusds)), 2, ",", ".");
$sistema_gs = number_format((($cierre_id->monto_apertura + $c_gs + $igss - $egss)), 0, ",", ".");

$html1 = <<<EOF
		<br>
		<h1 align="center">RESULTADOS</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">DATOS</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">USD</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">GS</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">RS</th>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			   <td width="30%" align="left" style="$header_table_style">[+] Apertura de caja</td>
			   <th width="20%" style="border-left-width:1px ; border-right-width:1px">$apertura_usd </th>
			   <th width="30%" style="border-left-width:1px ; border-right-width:1px">$apertura_gs</th>
               <th width="20%" style="border-left-width:1px ; border-right-width:1px">$apertura_rs</th>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			    <td width="30%" align="left" style="$header_table_style">[+] Otros ingresos</td>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$iusd_formatted</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$igs_formatted</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$irs_formatted</th>
                
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			    <td width="30%" align="left" style="$header_table_style">[+] Total Venta Efectivo</td>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$usd_formatted</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$gs_formatted</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">$rs_formatted</th>
                
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			    <td width="30%" align="left" style="$header_table_style">[+] Total Deposito</td>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$usdss_formatted</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$gss_formatted</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">$rss_formatted</th>
			    
                
			</tr>
		</table><table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			    <td width="30%" align="left" style="$header_table_style">[-] Total Egresos</td>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$eusd_formatted</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$egs_formatted</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$ers_formatted</th>
                
			</tr>
		</table>

		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			   <td width="30%" align="left" style="$header_table_style">Total cierre de caja </td>
			   <th width="20%" style="border-left-width:1px ; border-right-width:1px">$cierre_usd</th>
			   <th width="30%" style="border-left-width:1px ; border-right-width:1px">$cierre_gs</th>
			   <th width="20%" style="border-left-width:1px ; border-right-width:1px">$cierre_rs</th>
			   
               
			</tr>
		</table>
		
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			   <td width="30%" align="left" style="$header_table_style">Total sistema efectivo </td>
			   <th width="20%" style="border-left-width:1px ; border-right-width:1px">$sistema_usd</th>
			   <th width="30%" style="border-left-width:1px ; border-right-width:1px">$sistema_gs</th>
			   <th width="20%" style="border-left-width:1px ; border-right-width:1px">$sistema_rs</th>
			   
               
			</tr>
		</table>
	

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF
	<table width"100%" style="border: 1px solid #333; font-size:15px; background-color: #348993; color: white">
			<tr align="right">
			    <th width="30%" style="$header_table_style">DIFERENCIA</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px;background-color: #c0c0c0; color: #202020; font-weight: bold;">$diferencia_usd</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px;background-color: #c0c0c0; color: #202020; font-weight: bold;">$diferencia_gs</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px;background-color: #c0c0c0; color: #202020; font-weight: bold;">$diferencia_rs</th>
			   
                
			</tr>
		</table>
EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

$usd_usd = $c_usd + $c_usdss;
$gs_usd = ($c_gs + $c_gss) / $cierre_id->cot_dolar;
$rs_usd = ($c_rs + $c_rss) / $cierre_id->cot_real;

$ventas = number_format(($gs_usd + $rs_usd + $usd_usd), 2, ",", ".");
$ventas_rs = number_format((($gs_usd + $rs_usd + $usd_usd) * $cierre_id->cot_real), 2, ",", ".");
$ventas_gs = number_format((($gs_usd + $rs_usd + $usd_usd) * $cierre_id->cot_dolar), 2, ",", ".");


// var_dump($cierre->cot_dolar);
/* ================================ 
	TOTAL DE COSTOS - CONVERSIONES
================================ */
$compras_us = number_format(($totalCosto + $totalCostot), 2, ",", ".");
$compras_rs = number_format((($totalCosto + $totalCostot) * $cierre_id->cot_real), 2, ",", ".");
$compras_gs = number_format((($totalCosto + $totalCostot) * $cierre_id->cot_dolar), 0, ",", ".");


// var_dump($cierre->cot_real); 
// var_dump($compras_us);
// var_dump($compras_rs);
// var_dump($compras_gs);

$html1 = <<<EOF
<br>
<br>

		<h1 align="center">TOTALES</h1>

	<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="center">
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">Total venta USD:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$ventas</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">Total costo USD:</th>
				<th width="30%" style="border-left-width:1px ; border-right-width:1px">$compras_us</th>
			</tr>
			<tr align="center">
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">Total venta GS:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$ventas_gs</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">Total costo GS:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$compras_gs</th>
			</tr>
			<tr align="center">
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">Total venta RS:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$ventas_rs</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">Total costo RS:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$compras_rs</th>
			</tr>
		</table>
EOF;
//$pdf->writeHTML($html1, false, false, false, false, '');

/* ------------------------------------------------
                TRANSFERENCIAS 
-------------------------------------------------*/
$totalTransferencias = 0;

$html1 = <<<EOF
		<br>
		<h1 align="center">Transferencias</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
			    <th width="8%" style="border-left-width:1px ; border-right-width:1px">Tipo</th>
			    <th width="15%" style="border-left-width:1px ; border-right-width:1px">Caja</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
                <th width="11%" style="border-left-width:1px ; border-right-width:1px">GS</th>
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">RS</th>
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">USD</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Metodo</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$indice = 0;
$transferencias_resumen = array();
// Variables para el footer de transferencias
$total_transferencias_gs = 0;
$total_transferencias_rs = 0;
$total_transferencias_usd = 0;

// Obtener transferencias de ingresos
foreach ($this->ingreso->ListarRangoSesion($cierre_id->fecha_apertura, $cierre_id->fecha_cierre, $cierre_id->id_usuario) as $t):
	if ($t->categoria == 'Transferencia') {
		$tGS = '';
		$tRS = '';
		$tUSD = '';
		$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

		// asignamos temporalmente el monto a GS, rs y usd
		$tGS = number_format($t->monto, 0, ",", ".");
		$tRS = 0;
		$tUSD = 0;

		// Para ingresos (positivos)
		$total_transferencias_gs += $t->monto;

		$fecha = date("d/m/Y H:i", strtotime($t->fecha));
		$tipo = "Ingreso";

		// Obtener el nombre de la caja
		$nombre_caja = $this->egreso->ObtenerNombreCaja($t->id_caja);

		// Para ingresos: id_caja es quien RECIBIÓ el dinero
		// Entonces se suma al "recibido" de esa caja
		$caja_receptora = $t->id_caja;
		if (!isset($transferencias_resumen[$caja_receptora])) {
			$transferencias_resumen[$caja_receptora] = array('enviado' => 0, 'recibido' => 0);
		}
		$transferencias_resumen[$caja_receptora]['recibido'] += $t->monto;

		$html1 = <<<EOF
        
        <table width"100%" style="$bg $body_table_style">
            <tr align="center">
                <td width="12%" style="border-left-width:1px ; border-right-width:1px">$fecha</td>
                <td width="8%" style="border-left-width:1px ; border-right-width:1px">$tipo</td>
                <td width="15%" style="border-left-width:1px ; border-right-width:1px; font-size:8px">$nombre_caja</td>
                <td width="20%" style="border-left-width:1px ; border-right-width:1px; font-size:8px">$t->concepto</td>
                <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$tGS</td>
                <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$tRS</td>
                <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$tUSD</td>
                <td width="12%" style="border-left-width:1px ; border-right-width:1px">$t->forma_pago</td>
            </tr>
        </table>

EOF;

		$pdf->writeHTML($html1, false, false, false, false, '');
		$totalTransferencias += $t->monto;
		$indice++;
	}
endforeach;

// Obtener transferencias de egresos
foreach ($this->egreso->ListarRangoSesion($cierre_id->fecha_apertura, $cierre_id->fecha_cierre, $cierre_id->id_usuario) as $t):
	if ($t->categoria == 'Transferencia') {
		$tGS = '';
		$tRS = '';
		$tUSD = '';
		$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

		// asignamos temporalmente el monto a GS, rs y usd
		$tGS = number_format($t->monto, 0, ",", ".");
		$tRS = 0;
		$tUSD = 0;

		// Para egresos (negativos)
		$total_transferencias_gs -= $t->monto;

		$fecha = date("d/m/Y H:i", strtotime($t->fecha));
		$tipo = "Egreso";

		// Obtener el nombre de la caja
		$nombre_caja = $this->egreso->ObtenerNombreCaja($t->id_caja);

		// Para egresos: id_caja es quien ENVIÓ el dinero
		// Entonces se suma al "enviado" de esa caja
		$caja_emisora = $t->id_caja;
		if (!isset($transferencias_resumen[$caja_emisora])) {
			$transferencias_resumen[$caja_emisora] = array('enviado' => 0, 'recibido' => 0);
		}
		$transferencias_resumen[$caja_emisora]['enviado'] += $t->monto;

		$html1 = <<<EOF
        
        <table width"100%" style="$bg $body_table_style">
            <tr align="center">
                <td width="12%" style="border-left-width:1px ; border-right-width:1px">$fecha</td>
                <td width="8%" style="border-left-width:1px ; border-right-width:1px">$tipo</td>
                <td width="15%" style="border-left-width:1px ; border-right-width:1px; font-size:8px">$nombre_caja</td>
                <td width="20%" style="border-left-width:1px ; border-right-width:1px; font-size:8px">$t->concepto</td>
                <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$tGS</td>
                <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$tRS</td>
                <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$tUSD</td>
                <td width="12%" style="border-left-width:1px ; border-right-width:1px">$t->forma_pago</td>
            </tr>
        </table>

EOF;

		$pdf->writeHTML($html1, false, false, false, false, '');
		$totalTransferencias += $t->monto;
		$indice++;
	}
endforeach;

// Footer con totales de transferencias
$total_gs_formatted = number_format($total_transferencias_gs, 0, ",", ".");
$total_rs_formatted = number_format($total_transferencias_rs, 2, ",", ".");
$total_usd_formatted = number_format($total_transferencias_usd, 2, ",", ".");

$html1 = <<<EOF
		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <td width="12%" style="border-left-width:1px ; border-right-width:1px"></td>
			    <td width="8%" style="border-left-width:1px ; border-right-width:1px"></td>
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
			    <td width="20%" style="border-left-width:1px ; border-right-width:1px">TOTAL</td>
			    <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$total_gs_formatted</td>
			    <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$total_rs_formatted</td>
			    <td width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$total_usd_formatted</td>
			    <td width="12%" style="border-left-width:1px ; border-right-width:1px"></td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

/* ------------------------------------------------
                RESUMEN DE TRANSFERENCIAS POR CAJA
-------------------------------------------------*/

$html1 = <<<EOF
		<br>
		<h2 align="center">Resumen de Transferencias por Caja</h2>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="40%" style="border-left-width:1px ; border-right-width:1px">Caja/Entidad</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">Enviado</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">Recibido</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$indice = 0;
foreach ($transferencias_resumen as $id_caja => $montos):
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';
	$enviado = number_format($montos['enviado'], 0, ",", ".");
	$recibido = number_format($montos['recibido'], 0, ",", ".");

	// Obtener el nombre de la caja
	$nombre_caja = $this->egreso->ObtenerNombreCaja($id_caja);

	$html1 = <<<EOF
    
    <table width"100%" style="$bg $body_table_style">
        <tr align="center">
            <td width="40%" style="border-left-width:1px ; border-right-width:1px">$nombre_caja</td>
            <td width="30%" style="border-left-width:1px ; border-right-width:1px" align="right">$enviado</td>
            <td width="30%" style="border-left-width:1px ; border-right-width:1px" align="right">$recibido</td>
        </tr>
    </table>

EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');
	$indice++;
endforeach;

$pdf->Output('cierre.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
?>
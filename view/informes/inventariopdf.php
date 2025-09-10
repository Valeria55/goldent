<?php

// require_once('plugins/tcpdf/pdf/tcpdf_include.php');
require_once('plugins/tcpdf2/tcpdf.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$fechahoy = date("d/m/Y");
$horahoy = date("H:i");

    function mostrarFecha($fecha)
    {
        if (is_null($fecha)) return '';
        $match_date = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
    
        if ($match_date) {
            $horastr = $match_date->format('H:i');
        } else {
            $match_date = DateTime::createFromFormat('Y-m-d', $fecha);
            $horastr = false;
        }
        $today = date_create_from_format('Y-m-d', date('Y-m-d'));
    
        $diff = $today->diff($match_date);
    
        $diffDays = (int)$diff->format("%R%a"); // Extract days count in interval
    
        $hora = "";
        switch ($diffDays) {
            default:
                if ($horastr) $hora = ", a las " . $horastr;
                return date("d/m/Y", strtotime($fecha)) . $hora;
        }
    }

$fech_apert = mostrarFecha($cierre_inventario->fecha_apertura);
$fech_cierre = mostrarFecha($cierre_inventario->fecha_cierre);

// $sucursal = $GLOBALS['nombre_sucursal'];


$html1 = <<<EOF
		<h1 align="center">Informe de inventario </h1>

		<p align="left">Documento generado: $fechahoy a las $horahoy</p>
		<h3 align="center">Información sobre el inventario </h3>
		<p align="left">Motivo: $cierre_inventario->motivo</p>
		<p align="left">Usuario encargado: $cierre_inventario->user</p>
		<p align="left">Inventario abierto: $fech_apert</p>
		<p align="left">Inventario cerrado: $fech_cierre</p>


EOF;

$pdf->writeHTML($html1, false, false, false, false, '');



/* ================================ 
	faltantes 
================================ */


$html1 = <<<EOF
		<h1 align="center">Faltantes</h1>
		<table width"100%" style="border: 1px solid #333; font-size:9px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="14%" style="border-left-width:1px ; border-right-width:1px">Cod.</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Categ.</th>
                <th width="21%" style="border-left-width:1px ; border-right-width:1px">Producto</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Precio Costo</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Precio Venta</th>
             	<th width="7%" style="border-left-width:1px ; border-right-width:1px">St. al Cargar</th>
             	<th width="7%" style="border-left-width:1px ; border-right-width:1px">St. al Finalizar</th>
             	<th width="7%" style="border-left-width:1px ; border-right-width:1px">Invent.</th>
             	<th width="5%" style="border-left-width:1px ; border-right-width:1px">Dif.</th>
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">Monto</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

// $totalLocal = 0;
// $totalDepo = 0;
$total_faltante = 0;

foreach($this->model->ListarInventario($id_c) as $r):

// $totalStock1 =number_format(($r->precio_costo*$r->stock),2,",",".");
// $totalStock2 =number_format(($r->precio_costo*$r->stock2),2,",",".");
// $suma = number_format(($r->precio_costo*$r->stock + $r->precio_costo*$r->stock2),2,",",".");
$categoria = $r->categoria_hijo ?? '(sin especificar)';
$stock_real = $r->stock_real ?? 0;
$cantidad_faltante = $r->faltante ?? $r->stock_actual;


$costo=number_format($r->precio_costo,0,",",".");
$minorista=number_format($r->precio_minorista,0,",",".");
$faltante = ($r->precio_minorista *$cantidad_faltante);
if (
		// (is_null($r->faltante)) || 
		$faltante >= 0
	) { //faltan mercaderias
	
	$total_faltante += $faltante;
	$monto_faltante = number_format(($faltante), 0, ",",".");

	$html1 = <<<EOF
			
			<table width"100%" style="border: 1px solid #333; font-size:7px">
				<tr align="center">
					<td width="14%" style="border-left-width:1px ; border-right-width:1px">$r->codigo</td>
					<td width="10%" style="border-left-width:1px ; border-right-width:1px">$categoria</td>
					<td width="21%" style="border-left-width:1px ; border-right-width:1px">$r->producto</td>
					<td width="10%" style="border-left-width:1px ; border-right-width:1px">$costo</td>
					<td width="10%" style="border-left-width:1px ; border-right-width:1px">$minorista</td>
					<td width="7%" style="border-left-width:1px ; border-right-width:1px">$r->stock_actual</td>
					<td width="7%" style="border-left-width:1px ; border-right-width:1px">$r->stock_tabla_productos</td>
					<td width="7%" style="border-left-width:1px ; border-right-width:1px">$stock_real</td>
					<td width="5%" style="border-left-width:1px ; border-right-width:1px">$cantidad_faltante</td>
					<td width="11%" style="border-left-width:1px ; border-right-width:1px">$monto_faltante</td>
				</tr>
			</table>

	EOF;

	$pdf->writeHTML($html1, false, false, false, false, '');

		// $totalLocal += $r->precio_costo*$r->stock;
		// $totalDepo += $r->precio_costo*$r->stock2;
}

endforeach;

// $totalLocalV = number_format($totalLocal,2,",",".");
// $totalDepoV = number_format($totalDepo,2,",",".");
// $SumaV = number_format(($totalLocal + $totalDepo),2,",",".");

$total_faltante_number = number_format($total_faltante);

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px; background-color: #348993; color: white">
			<tr align="center" >
				<td width="14%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="21%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="7%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="7%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">TOTAL</td>
				<td width="11%" style="border-left-width:1px ; border-right-width:1px">$total_faltante_number</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');


/* ================================ 
	sobrantes 
================================ */

$html1 = <<<EOF
		<h1 align="center">Sobrantes</h1>

		<table width"100%" style="border: 1px solid #333; font-size:9px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="14%" style="border-left-width:1px ; border-right-width:1px">Cod.</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Categ.</th>
                <th width="21%" style="border-left-width:1px ; border-right-width:1px">Producto</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Precio Costo</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Precio Venta</th>
             	<th width="7%" style="border-left-width:1px ; border-right-width:1px">St. al Cargar</th>
             	<th width="7%" style="border-left-width:1px ; border-right-width:1px">St. al Finalizar</th>
             	<th width="7%" style="border-left-width:1px ; border-right-width:1px">Invent.</th>
             	<th width="5%" style="border-left-width:1px ; border-right-width:1px">Dif.</th>
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">Monto</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

// $totalLocal = 0;
// $totalDepo = 0;
$total_sobrante = 0;

foreach ($this->model->ListarInventario($id_c) as $r) :

	// $totalStock1 =number_format(($r->precio_costo*$r->stock),2,",",".");
	// $totalStock2 =number_format(($r->precio_costo*$r->stock2),2,",",".");
	// $suma = number_format(($r->precio_costo*$r->stock + $r->precio_costo*$r->stock2),2,",",".");
	$categoria = $r->categoria ?? '(sin especificar)';
	$stock_real = $r->stock_real ?? 0;
	$cantidad_sobrante = $r->faltante ?? $r->stock_actual;


	$costo = number_format($r->precio_costo, 0, ",", ".");
	$minorista = number_format($r->precio_minorista, 0, ",", ".");
	$sobrante = ($r->precio_minorista *$cantidad_sobrante);
	if ($sobrante < 0) { //sobran mercaderias

			$total_sobrante += $sobrante;
			$monto_sobrante = number_format(($sobrante), 0, ",", ".");

			$html1 = <<<EOF
				
				<table width"100%" style="border: 1px solid #333; font-size:7px">
					<tr align="center">
						<td width="14%" style="border-left-width:1px ; border-right-width:1px">$r->codigo</td>
						<td width="10%" style="border-left-width:1px ; border-right-width:1px">$categoria</td>
						<td width="21%" style="border-left-width:1px ; border-right-width:1px">$r->producto</td>
						<td width="10%" style="border-left-width:1px ; border-right-width:1px">$costo</td>
						<td width="10%" style="border-left-width:1px ; border-right-width:1px">$minorista</td>
						<td width="7%" style="border-left-width:1px ; border-right-width:1px">$r->stock_actual</td>
						<td width="7%" style="border-left-width:1px ; border-right-width:1px">$r->stock_tabla_productos</td>
						<td width="7%" style="border-left-width:1px ; border-right-width:1px">$stock_real</td>
						<td width="5%" style="border-left-width:1px ; border-right-width:1px">$cantidad_sobrante</td>
						<td width="11%" style="border-left-width:1px ; border-right-width:1px">$monto_sobrante</td>
					</tr>
				</table>

		EOF;

			$pdf->writeHTML($html1, false, false, false, false, '');
	}

// $totalLocal += $r->precio_costo*$r->stock;
// $totalDepo += $r->precio_costo*$r->stock2;

endforeach;

// $totalLocalV = number_format($totalLocal,2,",",".");
// $totalDepoV = number_format($totalDepo,2,",",".");
// $SumaV = number_format(($totalLocal + $totalDepo),2,",",".");

$total_sobrante_number = number_format($total_sobrante);

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px; background-color: #348993; color: white">
			<tr align="center">
				<td width="14%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="21%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="7%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="7%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px">TOTAL</td>
				<td width="11%" style="border-left-width:1px ; border-right-width:1px">$total_sobrante_number</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$faltante_mercaderia = number_format($total_faltante, 0, ",", ".");

$sobrante_mercaderia = number_format($total_sobrante, 0, ",", ".");

$sobrante_caja = number_format(-$cierre_inventario->sobrante_caja, 0, ",", ".");

$total_faltante = $total_faltante + $total_sobrante + $cierre_inventario->sobrante_caja;

$total_faltante_format = number_format($total_faltante, 0, ",", ".");

$html1 = <<<EOF
        <br>
		<h1 align="center">Resumen</h1>

		<table width"100%" style="border: 1px solid #333;   border-collapse: collapse;
 font-size:12px;">
			<tr align="center">
                <th style="border-left-width:1px ; border-right-width:1px; ">Faltante de Mercaderías</th>
                <td style="border-left-width:1px ; border-right-width:1px">$faltante_mercaderia</td>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333;   border-collapse: collapse;
 font-size:12px;">
			<tr align="center">
                <th style="border-left-width:1px ; border-right-width:1px; ">Sobrante de Mercaderías</th>
                <td style="border-left-width:1px ; border-right-width:1px">$sobrante_mercaderia</td>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333;   border-collapse: collapse;
 font-size:12px;">
			<tr align="center">
            	<th style="border-left-width:1px ; border-right-width:1px; ">Sobrante en Caja</th>
                <td style="border-left-width:1px ; border-right-width:1px">$sobrante_caja</td>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; background-color: #348993; color: white;  border-collapse: collapse;
 font-size:12px;">
			<tr align="center">
				<th style="border-left-width:1px ; border-right-width:1px; ">TOTAL FALTANTE</th>
                <td style="border-left-width:1px ; border-right-width:1px">$total_faltante_format</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

ob_end_clean();

$pdf->Output('inventario.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+

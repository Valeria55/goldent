<?php

require_once('plugins/tcpdf2/tcpdf.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('L', 'A4');

$desde = (isset($_GET["desde"]))? $_GET["desde"]:"";
$hasta = (isset($_GET["hasta"]))? $_GET["hasta"]:"";


$desdes  = date("d/m/Y", strtotime($_GET["desde"]));
$hastas = date("d/m/Y", strtotime($_GET["hasta"]));

$horahoy = date("d/m/Y H:i");
/* ================================ 
	ESTILOS PARA LAS TABLAS
================================ */
$body_table_style = 'font-size:8px; border-top: 1px solid #ccc; border-bottom: .5px solid #ccc;';
$header_table_style = 'border-top: .5px solid #ccc; border-bottom: 1px solid #ccc; font-size:8.5px; background-color: #c0c0c0; color: #202020; font-weight: bold;';
// ********************************

$html1 = <<<EOF
        <h1 align="center">AFRODITE PY</h1>
		 <h5 align="center">Desde $desdes Hasta $hastas</h5>
		<p>Generado a las $horahoy</p>
	

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');


//die();
$metodo=$_GET['metodo'];

$html1 = <<<EOF
		<h1 align="center">Movimientos $metodo</h1>

		<table width"100%" style="$header_table_style">
			<tr align="center">
			    <th width="5%" style="border-left-width:1px ; border-right-width:1px">N°</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Persona</th>
             	<th width="20%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Comprobante</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Ingresos</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">-%</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Egreso</th>
             	<th width="5%" style="border-left-width:1px ; border-right-width:1px">Moneda</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$indice = 0;
    $sumaTotal = 0; 
    $c=1;
    $sumin_gs = 0;
    $sumeg_gs = 0;
    $i_descuento=0;
    $monto_descuento=0;
foreach($this->model->ListarMovimientos($_GET['metodo'],$desde,$hasta) as $r):
    
if($r->monto>0){
        $egreso = "";
        $ingreso = number_format(($r->monto),0,".",",");
        $sumin_gs += $r->monto;

         //  Quitar monto a los ingresos 
        if($r->tarjeta=='CREDITO'){
            $descuento=5.2.'%';
            $i_descuento=$r->monto*0.052;
            $monto_descuento = number_format(($i_descuento),0,".",",");
       
        }elseif($r->tarjeta=='DEBITO'){
            $descuento=3.3.'%';
            $i_descuento=$r->monto*0.033;
            $monto_descuento = number_format(($i_descuento),0,".",",");
           
        }else{
            $descuento=0;
            $i_descuento=0;
            $monto_descuento = number_format(($i_descuento),0,".",",");
        }

    }else{
        $descuento=0;
        $ingreso = 0;
        $i_descuento=0;
        $monto_descuento=0;
        $egreso = number_format(($r->monto),0,".",",");
        $sumeg_gs += ($r->monto);
       
    } 
    $fecha = date("d/m/Y H:i", strtotime($r->fecha));
	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';
	
$html1 = <<<EOF
		
		<table width"100%" style="$bg $body_table_style">
			<tr align="center">
			    <th width="5%" style="border-left-width:1px ; border-right-width:1px">$c</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">$fecha</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px"> $r->persona</th>
             	<th width="20%" style="border-left-width:1px ; border-right-width:1px">$r->concepto</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$r->nro_comprobante</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$ingreso</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">$monto_descuento</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$egreso</th>
             	<th width="5%" style="border-left-width:1px ; border-right-width:1px">$r->moneda</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$r->tarjeta</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$total += $r->monto;
$indice++;
$c++;
 $suma_descuento += $i_descuento;
endforeach;

$total= number_format($sumin_gs - $suma_descuento + $sumeg_gs,0,".",",");
 $suma_descuentoS = $suma_descuento;
 $sumin_gs = number_format(($sumin_gs),0,".",",");
 $suma_descuento=number_format($suma_descuento,0,".",",");
 $sumeg_gs=number_format($sumeg_gs,0,".",",");
 

$html1 = <<<EOF
		
		<table width"100%" style="$header_table_style">
		<tr align="center">
			    <th width="5%" style="border-left-width:1px ; border-right-width:1px"></th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px"></th>
             	<th width="20%" style="border-left-width:1px ; border-right-width:1px"></th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$sumin_gs</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">$suma_descuentoS</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$sumeg_gs</th>
             	<th width="5%" style="border-left-width:1px ; border-right-width:1px"></th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$total</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');




$pdf->Output('productos.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
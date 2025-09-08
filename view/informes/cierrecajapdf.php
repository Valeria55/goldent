<?php

require_once('plugins/tcpdf/pdf/tcpdf_include.php');

//$id_cierre = $_GET['id_usuario'];
$usuario = $this->usuario->Obtener($_GET['id_usuario']);
$id_usuario = $_GET['id_usuario'];
$desdeV = date("d/m/Y", strtotime($_GET['desde']));
$hastaV = date("d/m/Y", strtotime($_GET['hasta']));

$cierre = $this->cierre->ListarCierreUsuario($_GET['desde'], $id_usuario) ;

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('L', 'A4');

$fechahoy = date("d/m/Y");
$horahoy = date("H:i");

$inicial=number_format($cierre->monto_apertura,0,",",".");
$caja_inicial = $cierre->monto_apertura;
$real=number_format($cierre->cot_real,2,",",".");
$dolar=number_format($cierre->cot_dolar,2,",",".");

$html1 = <<<EOF
		<h1 align="center">Caja $usuario->user </h1>
		<h3 align="center">Desde $desdeV hasta $hastaV </h3>
		<p>Generado a las $horahoy de la fecha $fechahoy</p>
	
<table width="100%">
	<tr>
	  <td>
		<table width="60%" style="border: 1px solid #333; float:right">
		  <tr>
            <th style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white; text-align:center" colspan="2">Cotización del día
            </th>
          </tr>
		  <tr>
			<td style="border-left-width:1px ; border-right-width:1px ; border-bottom-width:1px; text-align:center">Real</td>
			<td style="border-left-width:1px ; border-bottom-width:1px; border-right-width:1px; text-align:center">$real</td>
		  </tr>
		  <tr>
			<td style="border-left-width:1px ; border-right-width:1px ;  text-align:center">Guaranies</td>
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

         if($_SESSION['nivel']== 1){
            $precio_costo_string= '<th width="8%" style="border-left-width:1px ; border-right-width:1px">P.Costo</th>';
          }else{
             $precio_costo_string= '';
          }

$html1 = <<<EOF
		<h1 align="center">Ventas Efectivo</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px">Vendedor</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Cliente</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">P/real</th>
             	
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Des.</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">P/venta</th>
             	$precio_costo_string
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">GS</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">RS</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">USD</th>
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
$gs =0;
$usd =0;
$rs =0;

foreach($this->venta->ListarRangoSinAnularContado($_GET['desde'], $_GET['hasta'], $id_usuario) as $r):
$cobroGS=0;
$cobroRS=0;
$cobroUSD=0;



if($r->efectivo !=null){
$tventa+=$r->total;
$tsubtotal+=$r->subtotal;
$tdescuento+=$r->descuento;
$totalCosto+=$r->costo;

foreach($this->ingreso->ObtenerCobro($r->id_venta) as $ingresos):
//var_dump($ingresos->moneda);


if($ingresos->forma_pago=='Efectivo'){  
    
    if($ingresos->moneda == 'GS'){
        $cobroGS=number_format($ingresos->monto,0,",",".");
        $gs +=$ingresos->monto ;
        $c_gs+=$ingresos->monto;
    }elseif($ingresos->moneda == 'RS'){
        $cobroRS=number_format($ingresos->monto,2,",","."); 
        $rs +=$ingresos->monto ;
        $c_rs+=$ingresos->monto;
    }elseif($ingresos->moneda == 'USD'){
        $cobroUSD=number_format($ingresos->monto,2,",",".");
        $usd +=$ingresos->monto ;
        $c_usd+=$ingresos->monto;
    }
    
}
endforeach;
$subtotal=number_format($r->subtotal,2,",",".");
$total=number_format(($r->total),0,",",".");
$descuento = $r->descuento;
$descuentoV = number_format($descuento,0,",",".");
$costo=number_format($r->costo,0,",",".");
$ganancia=number_format(($r->total - $r->costo),0,",",".");
$hora = date("d/m/Y H:i", strtotime($r->fecha_venta));
session_start();
      if($_SESSION['nivel']== 1){
        $precio_costo_cuerpo= '<td width="8%" style="border-left-width:1px ; border-right-width:1px">'.$costo.'</td>';
      }else{
        $precio_costo_cuerpo= '';
      }
          
$html1 = <<<EOF
		
	<table width"100%" style="border: 1px solid #333; font-size:12px">
		<tr align="right">
			<td width="12%" align="center" style="border-left-width:1px ; border-right-width:1px">$hora</td>
			<td width="10%"  align="left" style="border-left-width:1px ; border-right-width:1px">($r->id_presupuesto ) $r->vendedor_salon</td>
			<td width="10%" style="border-left-width:1px ; border-right-width:1px; font-size:10px " align="left">$r->nombre_cli</td>
			<td width="10%" style="border-left-width:1px ; border-right-width:1px">$subtotal</td>
			
			<td width="10%" style="border-left-width:1px ; border-right-width:1px">$descuentoV</td>
			<td width="10%" style="border-left-width:1px ; border-right-width:1px">$total</td>
			$precio_costo_cuerpo
			<td width="10%" style="border-left-width:1px ; border-right-width:1px">$cobroGS</td>
			<th width="10%" style="border-left-width:1px ; border-right-width:1px">$cobroRS</th>
			<th width="10%" style="border-left-width:1px ; border-right-width:1px">$cobroUSD</th>
		</tr>
	</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

}
endforeach;

$totalVentaV = number_format($totalVenta,0,",",".");
$tventa= number_format($tventa,0,",",".");
$tsubtotal= number_format($tsubtotal,0,",",".");
$tdescuento= number_format($tdescuento,0,",",".");
$tcosto= number_format($totalCosto,0,",","."); //COSTO TOTAL EN EFECTIVO
// var_dump($tcosto);

$gs= number_format($gs,0,",",".");
$usd= number_format($usd,2,",",".");
$rs= number_format($rs,2,",",".");

session_start();
         if($_SESSION['nivel']== 1){
            $precio_costo_string= '<th width="8%" style="border-left-width:1px ; border-right-width:1px">'.$tcosto.'</th>';
          }else{
             $precio_costo_string= '';
          }

$html1 = <<<EOF
		
	<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px"></th>
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$tsubtotal</th>
             	
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$tdescuento</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$tventa</th>
             	$precio_costo_string
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$gs</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$rs</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">$usd</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

/*       ------------------------------------------------
         venta caja CONTADO <> EFECTIVO por VENDEDOR y RANGO de FECHA 
        -------------------------------------------------*/
$html1 = <<<EOF
<br><br>
		<h1 align="center">Ventas Tarjeta</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px">Vendedor</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Cliente</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">USD</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">Des.</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">Total</th>
				<th width="8%" style="border-left-width:1px ; border-right-width:1px">P.Costo</th>
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">GS</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">RS</th>
             	<th width="7%" style="border-left-width:1px ; border-right-width:1px">USD</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px">Pago</th>
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
$ttotalCostsubtotal=0;
$tventa=0;
$tdescuento=0;
$costoT=0;

foreach($this->venta->ListarRangoSinAnularContado($_GET['desde'], $_GET['hasta'], $id_usuario) as $t):
$cobroGS=0;
$cobroRS=0;
$cobroUSD=0;


if($t->otros != null){
$tventa+=$t->total;
$tsubtotal+=$t->subtotal;
$tdescuento+=$t->descuento;
$totalCostot+=$t->costo;
// $totalCostot+=$r->costo;
// var_dump($totalCostot);	



foreach($this->ingreso->ObtenerCobro($t->id_venta) as $ingres):
//var_dump($ingresos->moneda);


if($ingres->forma_pago !='Efectivo'){  
    
    if($ingres->moneda == 'GS'){
        $cobroGSs=number_format($ingres->monto,0,",",".");
        $cobroRSs=0; 
        $cobroUSD=0;
        $gss+=$ingres->monto;
        $c_gss+=$ingres->monto;
    }elseif($ingres->moneda == 'RS'){
        $cobroRSs=number_format($ingres->monto,2,",",".");
        $cobroGSs=0;
        $cobroUSD=0;
        $rss+=$ingres->monto;
        $c_rss+=$ingres->monto;
    }elseif($ingres->moneda == 'USD'){
        $cobroUSD=number_format($ingres->monto,2,",",".");
        $cobroGSs=0;
        $cobroRSs=0; 
        $usdss+=$ingres->monto;
        $c_usdss+=$ingres->monto;
    }
    
    $forma_pago=$ingres->forma_pago;
    
}
endforeach;

$subtotal=number_format($t->subtotal,0,",",".");
$total=number_format(($t->total),0,",",".");
$descuento = $t->descuento;
$descuentoV = number_format($descuento,0,",",".");
$costoT=number_format($t->costo,0,",",".");
$ganancia=number_format(($t->total - $t->costo),0,",",".");
$hora = date("d/m/Y H:i", strtotime($t->fecha_venta));
if($_SESSION['nivel']== 1){
	$precio_costo_cuerpo= '<td width="8%" style="border-left-width:1px ; border-right-width:1px">'.$costoT.'</td>';
  }else{
	$precio_costo_cuerpo= '';
  }
$html1 = <<<EOF
		
	<table width"100%" style="border: 1px solid #333; font-size:12px">
		<tr align="right">
			<td width="10%" align="center" style="border-left-width:1px ; border-right-width:1px">$hora</td>
			<td width="10%"  align="left" style="border-left-width:1px ; border-right-width:1px">($t->id_presupuesto) $t->vendedor_salon</td>
			<td width="10%" style="border-left-width:1px ; border-right-width:1px; font-size:10px " align="left">$t->nombre_cli</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$subtotal</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$descuentoV</td>
			<td width="9%" style="border-left-width:1px ; border-right-width:1px">$total</td>
			$precio_costo_cuerpo
			<td width="11%" style="border-left-width:1px ; border-right-width:1px">$cobroGSs</td>
			<th width="8%" style="border-left-width:1px ; border-right-width:1px">$cobroRSs</th>
			<th width="7%" style="border-left-width:1px ; border-right-width:1px">$cobroUSD</th>
			<th width="10%" style="border-left-width:1px ; border-right-width:1px">$forma_pago</th>
		</tr>
	</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

}
endforeach;


$totalVentaV = number_format($totalVenta,0,",",".");
$tventa= number_format($tventa,0,",",".");
$tsubtotal= number_format($tsubtotal,0,",",".");
$tdescuento= number_format($tdescuento,0,",",".");
$tcosto_tarjeta= number_format($totalCostot,0,",","."); //COSTO TOTAL TARJETAS
// $tcosto = "prueba";
// var_dump($totalCostot);
// die();
session_start();
         if($_SESSION['nivel']== 1){
            $precio_costo_string= '<th width="8%" style="border-left-width:1px ; border-right-width:1px">'.$tcosto_tarjeta.'</th>';
          }else{
             $precio_costo_string= '';
          }
$gss= number_format($gss,0,",",".");
$usds= number_format($usdss,2,",",".");
$rss= number_format($rss,2,",",".");
$html1 = <<<EOF
		
	<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
			    <th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$tsubtotal</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$tdescuento</th>
             	<th width="9%" style="border-left-width:1px ; border-right-width:1px">$tventa</th>
				 $precio_costo_string
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">$gss</th>
             	<th width="8%" style="border-left-width:1px ; border-right-width:1px">$rss</th>
             	<th width="7%" style="border-left-width:1px ; border-right-width:1px">$usds</th>
             	<th width="10%" style="border-left-width:1px ; border-right-width:1px"></th>
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
		<h1 align="center">Otros Ingresos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
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

foreach($this->ingreso->ListarRangoSesion($_GET['desde'], $_GET['hasta'], $id_usuario) as $i):
    $iGS='';
    $iRS='';
    $iUSD='';
 if($i->forma_pago=='Efectivo'){  
      
    if($i->moneda == 'GS'){
            $iGS=number_format($i->monto,0,",",".");
            $iRS=0;
            $iUSD=0;
            $igs+=$i->monto;
            $igss+=$i->monto;
        }elseif($i->moneda == 'RS'){
            $iRS=number_format($i->monto,2,",","."); 
            $iGS=0;
            $iUSD=0;
            $irs+=$i->monto;
            $irss+=$i->monto;
        }elseif($i->moneda == 'USD'){
            $iUSD=number_format($i->monto,2,",",".");
            $iRS=0;
            $iGS=0;
            $iusd+=$i->monto;
            $iusds+=$i->monto;
        }
 }
 $iGSi='';
 $iUSDi='';
 $iRSi='';
 if($i->forma_pago!='Efectivo'){  
     
    if($i->moneda == 'GS'){
            $iGSi=number_format($i->monto,0,",",".");
            $iRSi=0;
            $iUSDi=0;
            $igsi+=$i->monto;
            $igssi+=$i->monto;
        }elseif($i->moneda == 'RS'){
            $iRSi=number_format($i->monto,2,",","."); 
            $iGSi=0;
            $iUSDi=0;
            $irsi+=$i->monto;
            $irssi+=$i->monto;
        }elseif($i->moneda == 'USD'){
            $iUSDi=number_format($i->monto,2,",",".");
            $iRSi=0;
            $iGSi=0;
            $iusdi+=$i->monto;
            $iusdsi+=$i->monto;
        }
 }
$fecha = date("d/m/Y H:i", strtotime($i->fecha));
$monto=number_format($i->monto,0,",",".");
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px">$fecha</td>
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px">$i->nombre</td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px">$i->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$iGS $iGSi</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$iRS $iRSi</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$iUSD $iUSDi</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px">$i->forma_pago</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalEgreso += $i->monto;
endforeach;

$igs=number_format($igs,0,",",".");
$irs=number_format($irs,2,",",".");
$iusd=number_format($iusd,2,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$igs</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$irs</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$iusd</td>
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

$pagos[]="";
foreach($this->metodo->Listar() as $m) {
    $pagos[''.$m->metodo.'']=0;
}

foreach($this->cierre->ListarMetodos($_GET['desde'], $_GET['hasta'], $id_usuario) as $r): 
      
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
/*==============================================================
        EGRESOS
================================================================*/

$totalEgreso = 0;

$html1 = <<<EOF
		<br>
		<h1 align="center">Otros Egresos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
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

foreach($this->egreso->ListarRangoSesion($_GET['desde'], $_GET['hasta'], $id_usuario) as $e):
    $eGS='';
    $eRS='';
    $eUSD='';
 if($e->forma_pago=='Efectivo'){  
     
    if($e->moneda == 'GS'){
            $eGS=number_format($e->monto,0,",",".");
            $eRS=0;
            $eUSD=0;
            $egs+=$e->monto;
            $egss+=$e->monto;
        }elseif($e->moneda == 'RS'){
            $eRS=number_format($e->monto,2,",","."); 
            $eGS=0;
            $eUSD=0;
            $ers+=$e->monto;
            $erss+=$e->monto;
        }elseif($e->moneda == 'USD'){
            $eUSD=number_format($e->monto,2,",",".");
            $eRS=0;
            $eGS=0;
            $eusd+=$e->monto;
            $eusds+=$e->monto;
        }
 }
  $eGSe='';
 $eUSDe='';
 $eRSe='';
 if($e->forma_pago!='Efectivo'){  
     
    if($e->moneda == 'GS'){
            $eGSe=number_format($e->monto,0,",",".");
            $eRSe=0;
            $eUSDe=0;
            $egse+=$e->monto;
            $egsse+=$e->monto;
        }elseif($e->moneda == 'RS'){
            $eRSe=number_format($e->monto,2,",","."); 
            $eGSe=0;
            $eUSDe=0;
            $erse+=$e->monto;
            $ersse+=$e->monto;
        }elseif($i->moneda == 'USD'){
            $eUSDe=number_format($e->monto,2,",",".");
            $eRSe=0;
            $eGSe=0;
            $eusde +=$e->monto;
            $eusdse +=$e->monto;
        }
 }

$fecha = date("d/m/Y H:i", strtotime($e->fecha));
$monto=number_format($e->monto,0,",",".");
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
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

$egs=number_format($egs,0,",",".");
$ers=number_format($ers,2,",",".");
$eusd=number_format($eusd,2,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
			    <td width="15%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="20%" style="border-left-width:1px ; border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$egs</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$ers</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$eusd</td>
				<td width="14%" style="border-left-width:1px ; border-right-width:1px"></td>
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

/*==============================================================
        CIERRE
================================================================*/
$apertura_gs=number_format($cierre->monto_apertura,0,",",".");
$apertura_usd=number_format($cierre->apertura_usd,2,",",".");
$apertura_rs=number_format($cierre->apertura_rs,2,",",".");

$cierre_gs=number_format($cierre->monto_cierre,0,",",".");
$cierre_usd=number_format($cierre->monto_dolares,2,",",".");
$cierre_rs=number_format($cierre->monto_reales,2,",",".");

$diferencia_gs=number_format($cierre->monto_cierre-(($cierre->monto_apertura + $c_gs + $igss- $egss )),0,",",".");
$diferencia_usd=number_format($cierre->monto_dolares-(($cierre->apertura_usd + $c_usd + $iusds- $eusds )),2,",",".");
$diferencia_rs=number_format($cierre->monto_reales-(($cierre->apertura_rs + $c_rs + $irss- $erss )),2,",",".");

$sistema_gs=number_format((($cierre->monto_apertura + $c_gs + $igss- $egss )),0,",",".");
$sistema_usd=number_format((($cierre->apertura_usd + $c_usd + $iusds- $eusds )),2,",",".");
$sistema_rs=number_format((($cierre->apertura_rs + $c_rs + $irss- $erss )),2,",",".");

$html1 = <<<EOF
		<br>
		<h1 align="center">RESULTADOS</h1>

		<table width"100%" style="border: 1px solid #333; font-size:15px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">DATOS</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">GS</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">RS</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">USD</th>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			   <td width="30%" align="left" style="border-left-width:1px ; border-right-width:1px">[+] Apertura de caja</td>
			   <th width="30%" style="border-left-width:1px ; border-right-width:1px">$apertura_gs</th>
			   <th width="20%" style="border-left-width:1px ; border-right-width:1px">$apertura_rs</th>
               <th width="20%" style="border-left-width:1px ; border-right-width:1px">$apertura_usd</th>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			    <td width="30%" align="left" style="border-left-width:1px ; border-right-width:1px">[+] Otros ingresos</td>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$igs</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$irs</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">$iusd</th>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			    <td width="30%" align="left" style="border-left-width:1px ; border-right-width:1px">[+] Total Venta Efectivo</td>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$gs</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$rs</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">$usd</th>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			    <td width="30%" align="left" style="border-left-width:1px ; border-right-width:1px">[+] Total Deposito</td>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$gss</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$rss</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">$usds</th>
			</tr>
		</table><table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			    <td width="30%" align="left" style="border-left-width:1px ; border-right-width:1px">[-] Total Egresos</td>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$egs</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$ers</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">$eusd</th>
			</tr>
		</table>

		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			   <td width="30%" align="left" style="border-left-width:1px ; border-right-width:1px">Total cierre de caja </td>
			   <th width="30%" style="border-left-width:1px ; border-right-width:1px">$cierre_gs</th>
			   <th width="20%" style="border-left-width:1px ; border-right-width:1px">$cierre_rs</th>
               <th width="20%" style="border-left-width:1px ; border-right-width:1px">$cierre_usd</th>
			</tr>
		</table>
		
		<table width"100%" style="border: 1px solid #333; font-size:15px">
			<tr align="right">
			   <td width="30%" align="left" style="border-left-width:1px ; border-right-width:1px">Total sistema efectivo </td>
			   <th width="30%" style="border-left-width:1px ; border-right-width:1px">$sistema_gs</th>
			   <th width="20%" style="border-left-width:1px ; border-right-width:1px">$sistema_rs</th>
               <th width="20%" style="border-left-width:1px ; border-right-width:1px">$sistema_usd</th>
			</tr>
		</table>
	

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF
	<table width"100%" style="border: 1px solid #333; font-size:15px; background-color: #348993; color: white">
			<tr align="right">
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">DIFERENCIA</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$diferencia_gs</th>
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">$diferencia_rs</th>
                <th width="20%" style="border-left-width:1px ; border-right-width:1px">$diferencia_usd</th>
			</tr>
		</table>
EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

$usd_usd=$c_usd+$c_usdss;
$gs_usd= ($c_gs+$c_gss)/$cierre->cot_dolar;
$rs_usd= ($c_rs+$c_rss)/$cierre->cot_real;

$ventas=number_format(($gs_usd+$rs_usd+$usd_usd),2,",",".");
$ventas_rs=number_format((($gs_usd+$rs_usd+$usd_usd)*$cierre->cot_real),2,",",".");
$ventas_gs=number_format((($gs_usd+$rs_usd+$usd_usd)*$cierre->cot_dolar),0,",",".");


// var_dump($cierre->cot_dolar);
/* ================================ 
	TOTAL DE COSTOS - CONVERSIONES
================================ */
$compras_gs = number_format(($totalCosto + $totalCostot),2,",",".");
$compras_rs=number_format((($totalCosto + $totalCostot)/$cierre->cot_real),2,",",".");
$compras_us=number_format((($totalCosto + $totalCostot)/$cierre->cot_dolar),0,",",".");


// var_dump($cierre->cot_real); 
// var_dump($compras_us);
// var_dump($compras_rs);
// var_dump($compras_gs);

// var_dump($tcosto_tarjeta);

$html1 = <<<EOF
<br>
<br>

		<h1 align="center">TOTALES</h1>

	<table width"100%" style="border: 1px solid #333; font-size:15px">
			
			<tr align="center">
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">Total venta GS:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$ventas_gs</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">Total costo GS:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$compras_gs</th>
			</tr>
			<tr align="center">
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">Total venta USD:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$ventas</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">Total costo USD:</th>
				<th width="30%" style="border-left-width:1px ; border-right-width:1px">$compras_us</th>
			</tr>
			<tr align="center">
			    <th width="20%" style="border-left-width:1px ; border-right-width:1px">Total venta RS:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$ventas_rs</th>
				<th width="20%" style="border-left-width:1px ; border-right-width:1px">Total costo RS:</th>
			    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$compras_rs</th>
			</tr>
		</table>
EOF;
$pdf->writeHTML($html1, false, false, false, false, '');


$pdf->Output('cierre.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
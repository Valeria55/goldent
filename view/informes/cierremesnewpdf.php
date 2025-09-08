<?php

require_once('plugins/tcpdf2/tcpdf.php');

$moneda = $this->venta_tmp->ObtenerMoneda();
$Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
       
$desde = date("d-m-Y", strtotime($_REQUEST["desde"]));
$hasta = date("d-m-Y", strtotime($_REQUEST["hasta"]));

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');
$_REQUEST['fecha'] .= '-01';
//$mes = date("m", strtotime($_REQUEST['fecha']));
$mes = $Meses[intval(date("m", strtotime($_REQUEST['fecha'])))-1];
$ano = date("Y", strtotime($_REQUEST['fecha']));
$fechaHoraHoy = date("d/m/Y H:i");

$inicial=number_format($moneda->monto_inicial,0,",",".");
$caja_inicial = $moneda->monto_inicial;
$real=number_format($moneda->reales,0,",",".");
$dolar=number_format($moneda->dolares,0,",",".");

$html1 = <<<EOF
		<h1 align="center">Informe de la fecha $desde hasta $hasta</h1>
		<p>Generado el $fechaHoraHoy</p>
		

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');


$html1 = <<<EOF
		<h1 align="center">Ventas</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			<th width="19%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
                <th width="35%" style="border-left-width:1px ; border-right-width:1px">Producto</th>
                <th width="5%" style="border-left-width:1px ; border-right-width:1px">Ca</th>
                <th width="15%" style="border-left-width:1px ; border-right-width:1px">Venta</th>
             	<th width="15%" style="border-left-width:1px ; border-right-width:1px">Costo</th>
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px">Utilidad</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$totalVenta = 0;

foreach($this->model->AgrupadoProducto($_REQUEST['desde'], $_REQUEST['hasta']) as $r):
    
$total=number_format($r->total,0,",",".");
$costo=number_format($r->costo,0,",",".");
$fecha_venta = date("d/m/Y ", strtotime($r->fecha_venta));
$cantidad=number_format($r->cantidad,2,",",".");
$ganancia=number_format(($r->total - $r->costo),0,",",".");
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			<th width="19%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->fecha_venta</th>
                <th width="35%" style="border-left-width:1px ; border-right-width:1px; font-size:8px" align="left">$r->producto</th>
                <th width="5%" style="border-left-width:1px ; border-right-width:1px" align="right">$r->cantidad</th>
                <th width="15%" style="border-left-width:1px ; border-right-width:1px" align="right">$total</th>
             	<th width="15%" style="border-left-width:1px ; border-right-width:1px" align="right">$costo</th>
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px" align="right">$ganancia</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalCosto += $r->costo;
$totalVenta += $r->total;

if($r->contado=='Contado'){
    $totalContado += $r->total;
}else{
    $totalCredito += $r->total;
}

endforeach;

$totalCostoV = number_format($totalCosto,0,",",".");
$totalVentaV = number_format($totalVenta,0,",",".");
$totalGananciaV = number_format(($totalVenta - $totalCosto),0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:9px">
			<tr align="center" style="padding:10px">
                <th width="19%" style="border-left-width:1px " align="left"><b>RESULTADOS (+)</b></th>
                <th width="35%" style="" align="right"></th>
                <th width="5%" style="border-left-width:1px ; border-right-width:1px" align="right"><b></b></th>
             	<th width="15%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$totalVentaV</b></th>
             	<th width="15%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$totalCostoV</b></th>
             	<th width="11%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$totalGananciaV</b></th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');


/*

   INICIO INGRESOS

*/

$html1 = <<<EOF
		<br>
		<h1 align="center">Ingresos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
                <th width="64%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Monto</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Utilidad</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalIngreso = 0;

foreach($this->ingreso->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']) as $i):
if($i->categoria != "Transferencia"){
$montoG=number_format(($i->monto*($i->margen_ganancia/100)),0,",",".");
$monto=number_format(($i->monto),0,",",".");
$dia = date("d", strtotime($i->fecha));
$fecha_gasto = date("d/m/Y", strtotime($i->fecha));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">$fecha_gasto</th>
				<td width="64%" style="border-left-width:1px ; border-right-width:1px" align="left">$i->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$montoG</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalIngreso += $i->monto;
$totalIngresoG += $i->monto*($i->margen_ganancia/100);
}
endforeach;
$ingreso=number_format($totalIngreso,0,",",".");
$ingresoG=number_format($totalIngresoG,0,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td width="76%" style="border-left-width:1px ; border-right-width:1px" align="left"><b>RESULTADO (+)</b></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$ingreso</b></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$ingresoG</b></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');


$totalCobroV = number_format($totalIngreso,0,",",".");

/*

   FIN INGRESOS

*/




$html1 = <<<EOF
		<h1 align="center">Compras</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			 <th width="20%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
                <th width="58%" style="border-left-width:1px ; border-right-width:1px">Producto</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Cantidad</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Total</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');



foreach($this->compra->AgrupadoProducto($_REQUEST['desde'],$_REQUEST['hasta']) as $r):

$total=number_format($r->total,0,",",".");
$cantidad=number_format($r->cantidad,2,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			<td width="20%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->fecha_compra</td>
				<td width="58%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->producto</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$cantidad</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$total</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');



if($r->contado=='Contado'){
    $totalContadoCompra += $r->total;
}else{
    $totalCreditoCompra += $r->total;
}

$totalCompra += $r->total;

endforeach;

$totalCompraV=number_format($totalCompra,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="78%" style="border-left-width:1px " align="left"><b>RESULTADO (-)</b></td>
				<td width="10%" style="border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$totalCompraV</b></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');






/*

   INICIO GASTOS

*/

$html1 = <<<EOF
		<br>
		<h1 align="center">Gastos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
                <th width="76%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Monto</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalEgreso = 0;

foreach($this->egreso->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']) as $e):
if($e->categoria != "Transferencia"){
$monto=number_format($e->monto,0,",",".");
$dia = date("d", strtotime($e->fecha));
$fecha_gasto = date("d/m/Y", strtotime($e->fecha));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">$fecha_gasto</th>
				<td width="76%" style="border-left-width:1px ; border-right-width:1px" align="left">$e->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalEgreso += $e->monto;
}
endforeach;

$egreso=number_format($totalEgreso,0,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td width="87%" style="border-left-width:1px ; border-right-width:1px" align="left"><b>RESULTADO (-)</b></td>
				<td width="13%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$egreso</b></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN GASTOS

*/


/*

   INICIO DEVOLUCIONES

*/

$html1 = <<<EOF
		<br>
		<h1 align="center">Devoluciones</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">Fecha</th>
                <th width="76%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th width="12%" style="border-left-width:1px ; border-right-width:1px">Monto</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalEgreso = 0;

foreach($this->egreso->ListarDevoluciones($_REQUEST['desde'], $_REQUEST['hasta']) as $e):
if($e->categoria != "Transferencia"){
$monto=number_format($e->monto,0,",",".");
$dia = date("d", strtotime($e->fecha));
$fecha_gasto = date("d/m/Y", strtotime($e->fecha));
$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
			    <th width="12%" style="border-left-width:1px ; border-right-width:1px">$fecha_gasto</th>
				<td width="76%" style="border-left-width:1px ; border-right-width:1px" align="left">$e->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$totalDevolucion += $e->monto;
}
endforeach;

$devolucion=number_format($totalDevolucion,0,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td width="87%" style="border-left-width:1px ; border-right-width:1px" align="left"><b>RESULTADO (-)</b></td>
				<td width="13%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$devolucion</b></td>
			</tr>
			
		</table>
		<br>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN GASTOS

*/


/*

  INICIO RESUMEN

*/
$utilidad = number_format((($totalVenta - $totalCosto) -$totalDevolucion - $totalEgreso + $totalIngreso),0,",",".");

$html1 = <<<EOF
<br><br><br>
		<h1 align="center">Resumen</h1>
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Ventas : </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalVentaV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Compras : </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalCompraV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Ingresos (+): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$ingresoG</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Cobros (+): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalCobroV</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Gastos (-): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$egreso</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Devoluciones (-): </td>
				<td style="border-left-width:1px ; border-right-width:1px">$devolucion</td>
			</tr>
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Utilidad: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$utilidad</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

  FIN RESUMEN

*/



/*

   INICIO PRODUCTOS

*/

$html1 = <<<EOF
        <br>
		<h1 align="center">Productos</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="78%" style="border-left-width:1px ; border-right-width:1px">Producto</th>
                <th width="10%" style="border-left-width:1px ; border-right-width:1px">Cantidad</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Total</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

    

$totalStock = 0;

foreach($this->producto->ListarTodo() as $r):

if($r->stock > 0){

$total=number_format(($r->precio_costo*$r->stock),0,",",".");
$cantidad=number_format($r->stock,2,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="78%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->producto</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px">$cantidad</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$total</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalStock += ($r->precio_costo*$r->stock);
}
endforeach;

$totalStockV=number_format($totalStock,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="78%" style="border-left-width:1px " align="left"><b>RESULTADO (+)</b></td>
				<td width="10%" style="border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$totalStockV</b></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN PRODUCTOS

*/

/*

   INICIO DEUDAS

*/ 

$html1 = <<<EOF
        <br>
		<h1 align="center">CUENTAS A COBRAR</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="40%" style="border-left-width:1px ; border-right-width:1px">Cliente</th>
                <th width="36%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Monto</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Saldo</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalDeuda = 0;

foreach($this->deuda->ListarAgrupadoCliente() as $r):


$monto=number_format($r->monto,0,",",".");
$saldo=number_format($r->saldo,0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="40%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->nombre</td>
				<td width="36%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$saldo</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');


$totalDeuda += $r->saldo;

endforeach;

$totalDeudaV=number_format($totalDeuda,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="78%" style="border-left-width:1px " align="left"><b>RESULTADO</b></td>
				<td width="10%" style="border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$totalDeudaV</b></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

/*

   FIN DEUDAS

*/

/*

   INICIO ACREEDORES

*/ 

$html1 = <<<EOF
        <br>
		<h1 align="center">CUENTAS A PAGAR</h1>

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
                <th width="40%" style="border-left-width:1px ; border-right-width:1px">Cliente</th>
                <th width="36%" style="border-left-width:1px ; border-right-width:1px">Concepto</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Monto</th>
                <th width="12%" style="border-left-width:1px ; border-right-width:1px">Saldo</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$totalAcreedor = 0;

foreach($this->acreedor->Listar() as $r):


$monto=number_format($r->monto,0,",",".");
$saldo=number_format($r->saldo,0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="40%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->nombre</td>
				<td width="36%" style="border-left-width:1px ; border-right-width:1px" align="left">$r->concepto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$monto</td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right">$saldo</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');


$totalAcreedor += $r->saldo;

endforeach;

$totalAcreedorV=number_format($totalAcreedor,0,",",".");

$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td width="78%" style="border-left-width:1px " align="left"><b>RESULTADO</b></td>
				<td width="10%" style="border-right-width:1px"></td>
				<td width="12%" style="border-left-width:1px ; border-right-width:1px" align="right"><b>$totalAcreedorV</b></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

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
  ?>
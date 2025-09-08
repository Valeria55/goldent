<?php


require_once('tcpdf_include.php');
require_once '../../dbconfig.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');
$semana = $_GET['semana'];
$stmt = $DB_con->prepare('SELECT p.producto, p.precio_venta, sum(v.cantidad) as cantidad, sum(v.total) as total, v.fecha  FROM ventas v JOIN productos p ON v.id_producto = p.id_producto WHERE WEEK(v.fecha) = :semana GROUP BY v.fecha, p.id_producto ORDER BY v.fecha DESC'); 
$stmt->bindParam(':semana', $_GET['semana']);
$stmt->execute();
$stmt_egreso = $DB_con->prepare('SELECT *  FROM egresos WHERE WEEK(fecha) = :semana ORDER BY fecha DESC'); 
$stmt_egreso->bindParam(':semana', $_GET['semana']);
$stmt_egreso->execute();
$sumaIngreso=0;
$sumaEgreso=0;
$sumaDia=0;
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");
$html1 = <<<EOF
		<br>
		<h2 align="center">Ventas de la semana N° $semana</h2>
		<br>
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
				<th style="border-left-width:1px ; border-right-width:1px" nowrap>Producto</th>
                <th style="border-left-width:1px ; border-right-width:1px">Precio unitario</th>
             	<th style="border-left-width:1px ; border-right-width:1px">Cantidad</th>
				<th style="border-left-width:1px ; border-right-width:1px">Total</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$contFecha=0;$c=0;
while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
extract($row);

if($fecha!=$contFecha){
if($c==1){
$sumaDiaView=number_format($sumaDia,0,",",".");
$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total dia: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$sumaDiaView</td>
			</tr>
		</table>

EOF;
$sumaDia = 0;
$pdf->writeHTML($html1, false, false, false, false, '');
}


$contFecha = $fecha;
$c++;
$fechaCompleta = $dias[date('w', strtotime($fecha))].' '.(date('d',strtotime($fecha))).' de '.$meses[date("m",strtotime($fecha))-1].' de '.date("Y", strtotime($fecha));
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #287372; color: white">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$fechaCompleta</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
}
$sumaDia=$sumaDia+$total;
$sumaIngreso=$sumaIngreso+$total;
$precio_ventaView=number_format($precio_venta,0,",",".");
$totalView=number_format($total,0,",",".");

$html1 = <<<EOF
		
		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$producto</td>
				<td style="border-left-width:1px ; border-right-width:1px">$precio_ventaView</td>
				<td style="border-left-width:1px ; border-right-width:1px">$cantidad</td>
				<td style="border-left-width:1px ; border-right-width:1px">$totalView</td>
			</tr>
		</table>

EOF;



$pdf->writeHTML($html1, false, false, false, false, '');

}
$sumaIngresoView=number_format($sumaIngreso,0,",",".");
$sumaDiaView=number_format($sumaDia,0,",",".");
$html1 = <<<EOF
		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total dia: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$sumaDiaView</td>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:13px" >
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$sumaIngresoView</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$pdf->AddPage('P', 'A4');

$html1 = <<<EOF
		<br>
		<h2 align="center">Gastos de semana N° $semana</h2>
		<br>
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333; font-size:12px; background-color: #348993; color: white">
			<tr align="center">
				<th style="border-left-width:1px ; border-right-width:1px" nowrap>Fecha</th>
				<th style="border-left-width:1px ; border-right-width:1px" nowrap>Categoría</th>
                <th style="border-left-width:1px ; border-right-width:1px">Concepto</th>
             	<th style="border-left-width:1px ; border-right-width:1px">Monto</th>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');
while($row=$stmt_egreso->fetch(PDO::FETCH_ASSOC)){
extract($row);
$sumaEgreso = $sumaEgreso + $monto;
$fecha = date("d/m/Y", strtotime($fecha));
$monto = number_format($monto,0,",",".");
$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333; font-size:10px;">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$fecha</td>
				<td style="border-left-width:1px ; border-right-width:1px">$categoria</td>
				<td style="border-left-width:1px ; border-right-width:1px" nowrap>$concepto</td>
				<td style="border-left-width:1px ; border-right-width:1px">$monto</td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

}
$sumaEgresoView=number_format($sumaEgreso,0,",",".");
$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$sumaEgresoView</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF
		<br>
		<h2 align="center">Balance de semana N° $semana</h2>
		<br>
EOF;
$ganancia = $sumaIngreso-$sumaEgreso;
$ganancia = number_format($ganancia,0,",",".");
$pdf->writeHTML($html1, false, false, false, false, '');
$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333;background-color: #348993; color: white">
			<tr align="center">
				<th style="border-left-width:1px ; border-right-width:1px" nowrap>Ingresos</th>
				<th style="border-left-width:1px ; border-right-width:1px" nowrap>Egresos</th>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:12px;">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$sumaIngresoView</td>
				<td style="border-left-width:1px ; border-right-width:1px">$sumaEgresoView</td>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333; font-size:13px;">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right">Ganancia: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$ganancia</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('Informe Sem N° '.$_GET['semana'].'.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
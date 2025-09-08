<?php


require_once('tcpdf_include.php');
require_once '../../dbconfig.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

if(isset($_GET['fecha'])){
$stmt = $DB_con->prepare('SELECT p.producto, p.precio_venta, v.cantidad, SUM(p.precio_venta*v.cantidad) as total  FROM ventas v JOIN productos p ON v.id_producto = p.id_producto WHERE v.fecha = :fecha GROUP BY p.id_producto ORDER BY v.cantidad DESC'); 
}
$stmt->bindParam(':fecha', $_GET['fecha']);
$stmt->execute();
$sumaTotal=0;
$fecha=date('d/m/Y', strtotime($_GET['fecha']));
$html1 = <<<EOF
		<br>
		<h2 align="center">Ventas de fecha $fecha</h2>
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


while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
extract($row);
$sumaTotal=$sumaTotal+$total;
$precio_venta=number_format($precio_venta,0,",",".");
$total=number_format($total,0,",",".");
$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333; font-size:10px">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$producto</td>
				<td style="border-left-width:1px ; border-right-width:1px">$precio_venta</td>
				<td style="border-left-width:1px ; border-right-width:1px">$cantidad</td>
				<td style="border-left-width:1px ; border-right-width:1px">$total</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

}
$sumaTotal=number_format($sumaTotal,0,",",".");
$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px ; text-align:right" colspan="3">Total: </td>
				<td style="border-left-width:1px ; border-right-width:1px">$sumaTotal</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($_GET['fecha'].'.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
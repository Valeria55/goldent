<?php


require_once('tcpdf_include.php');
require_once '../../dbconfig.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('L', 'A4');

$id_pedido = $_GET['id_pedido'];
$sql = mysqli_query($con, "SELECT * FROM pedidos WHERE id_pedido = $id_pedido");						
extract($row = mysqli_fetch_assoc($sql));
$fecha_entrega= date("d/m/Y", strtotime($fecha_entrega));

$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333">
			<tr align="left">
				<td width="20%" rowspan="2" ><img style="height:100px" src="../../$imagen"></td>
				<td width="40%">Nombre del equipo: <b>$nombre_equipo</b></td>
				<td width="40%">Fecha de entrega: <b>$fecha_entrega</b></td>
			</tr>
			<tr align="left">
				<td>Descripcion: <b>$descripcion</b></td>
				<td>Encargado: <b>$encargado</b></td>
			</tr>
		</table>
		<table width"100%" style="border: 1px solid #333">
			<tr align="center" style="font-size:14px">
				<th style="border-left-width:1px ; border-right-width:1px">Cantidad</th>
				<th style="border-left-width:1px ; border-right-width:1px">Tamaño remera</th>
				<th style="border-left-width:1px ; border-right-width:1px">N° imp.</th>
				<th style="border-left-width:1px ; border-right-width:1px">Tamaño short</th>
				<th style="border-left-width:1px ; border-right-width:1px">Observación</th>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$sql = mysqli_query($con, "SELECT * FROM items_pedidos WHERE id_pedido = $id_pedido");


while($row = mysqli_fetch_assoc($sql)){
extract($row);
$html1 = <<<EOF

		<table width"100%" style="border: 1px solid #333">
			<tr align="center">
				<td style="border-left-width:1px ; border-right-width:1px">$cantidad</td>
				<td style="border-left-width:1px ; border-right-width:1px">$tamano_remera</td>
				<td style="border-left-width:1px ; border-right-width:1px">$numero_impreso</td>
				<td style="border-left-width:1px ; border-right-width:1px">$tamano_short</td>
				<td style="border-left-width:1px ; border-right-width:1px">$observacion</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

}

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($id_pedido.'.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
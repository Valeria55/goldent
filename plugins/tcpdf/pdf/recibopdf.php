<?php


require_once('tcpdf_include.php');
include("../../conexion.php");

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$recibo = $_GET['rec'];
$sql = mysqli_query($con, "SELECT * FROM recibos JOIN clientes ON clientes.id_cliente = recibos.id_cliente WHERE recibos.id_recibo = '$recibo'");						
$row = mysqli_fetch_assoc($sql);
$fecha = date("d/m/Y", strtotime($row['fecha']));
$row['monto']=number_format($row['monto'],0,",",".");
$pdf->AddPage();



$html1 = <<<EOF

	<table width ="100%" style="border: 1px solid #333; text-align:center; line-height: 20px; font-size:10px">
		<tr>
			<td height="50px" valign="middle" colspan = "4" style="border: 1px solid #666; vertical-align: middle; justify-content: center;
   align-items: center;"><h1>Cooperativa </h1></td>
			<td height="50" colspan = "2" style="border: 1px solid #666"><b>Recibo N°:</b> $row[id_recibo] </td>
		</tr>
    <tr>
      <td colspan="3" align="left"><b>Fecha de Emisión:</b> $fecha</td>
      <td colspan="3" align="left"></td>
    </tr>
    <tr align="left">
      <td colspan="3"><b>RUC/CI:</b> $row[ci] </td>
      <td colspan="3"><b>Telefono:</b> $row[telefono] </td>
    </tr>
    <tr align="left">
      <td colspan="3"><b>Nombre:</b> $row[nombre] $row[apellido] </td>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr align="center">
      <td width="70%" style="border: 1px solid #666"><b>Descripción</b></td>
      <td width="10%" style="border: 1px solid #666"><b>Precio</b></td>
      <td width="10%" style="border: 1px solid #666"><b>Cantidad</b></td>
      <td width="10%" style="border: 1px solid #666"><b>Total</b></td>
    </tr>
    </table>
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');


$sql = mysqli_query($con, "SELECT * FROM items_recibo WHERE id_recibo = '$recibo'");

while($row = mysqli_fetch_assoc($sql)){
$row['monto']=number_format($row['monto'],0,",",".");
$html1 = <<<EOF

		<table width="100%" style="border-left-width:1px ; border-right-width:1px ; text-align:center; line-height: 20px; font-size:8px">
			<tr nowrap="nowrap">
				<td width="70%" style="border-left-width:1px ; border-right-width:1px ; text-align: left ">$row[concepto]</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px ; ">$row[monto]</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px ; ">1</td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px ; ">$row[monto]</td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

}

$html1 = <<<EOF

		<table width="100%" style="border-left-width:1px ; border-right-width:1px ; text-align:center; line-height: 20px; font-size:8px">
			<tr><td width="70%" style="border-left-width:1px ; border-right-width:1px ; "></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px ; "></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px ; "></td>
				<td width="10%" style="border-left-width:1px ; border-right-width:1px ; "></td>
			</tr>
		</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');		

$sql = mysqli_query($con, "SELECT * FROM recibos JOIN clientes ON clientes.id_cliente = recibos.id_cliente WHERE recibos.id_recibo = '$recibo'");						
$row = mysqli_fetch_assoc($sql);
$row['monto']=number_format($row['monto'],0,",",".");
$html1 = <<<EOF
	
	<table width="100%" style="border: 1px solid #333; text-align:center; line-height: 20px; font-size:8px">
	
	    <tr style="border: 1px solid #333">
	      <td colspan="4"></td>
	      <td align="right">TOTAL:</td>
	      <td style="border: 1px solid #333">$row[monto]</td>
	    </tr>
	</table>
	<table width="100%" style="border: 1px solid #333; text-align:left; vertical-align: middle; line-height: 20px; font-size:8px">
	    <tr width="100%" height="50px" valign="bottom">
		  <td colspan="6" nowrap="nowrap"><h5 align="left">Recibído por:</h5><h5 align="center"> Firma:</h5></td>
		</td> 
	</table>
	<table>
		<tr><td></td></tr>
		<tr><td></td></tr>
	</table>
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

/** COPIA DE FACTURA **/



// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($recibo.'.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
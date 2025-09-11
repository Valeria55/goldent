<?php

// require_once('plugins/tcpdf/pdf/tcpdf_include.php');
require_once('plugins/tcpdf2/tcpdf.php');
require_once('plugins/tcpdf2/tcpdf_barcodes_1d.php');
// require_once(dirname(__FILE__) . '/tcpdf_barcodes_1d_include.php');

$medidas = array(110, 50); // Ajustar aqui segun los milimetros necesarios;
$pdf = new TCPDF('L', 'mm', $medidas, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetDefaultMonospacedFont('courier');
$pdf->SetMargins(0, 0, 0, true);
$pdf->SetAutoPageBreak(TRUE, 0);

$pdf->AddPage();
$producto=$this->model->obtener($_GET['id']);
$descripcion=substr($producto->producto,0,17);
$html1 = <<<EOF
		<table width="100%" style="font-size:6px;">
			<tr align="left">
				<td width="34%"></td>
				<td width="34%"></td>
				<td></td>
			</tr>
			<tr align="left">
				<td width="34%" style="padding-top:8px;">$descripcion</td>
				<td width="34%" style="padding-top:8px;">$descripcion</td>
				<td style="padding-top:8px;">$descripcion</td>
			</tr>
		</table>
EOF;
//pdf->writeHTML($html1, false, false, false, false, '');

$code = $producto->codigo;  // Reemplaza esto con tu propio número de código de barras
$style = array(
	'position' => '',
	'align' => 'L',
	'stretch' => false,
	'fitwidth' => true,
	'cellfitalign' => '',
	'border' => false,
	'hpadding' => 'auto',
	'vpadding' => 'auto',
	'fgcolor' => array(0, 0, 0),
	'bgcolor' => false, // array(255,255,255),
	'text' => true,
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 4
);

// Logo, precio, descripción y código de barras para la primera etiqueta
$pdf->Image('assets/img/LogoTrinity.PNG', 8, 1, 20, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->SetFont('helvetica', '', 7);
$pdf->SetXY(6, 6); // Posicionar el precio en la segunda fila
$pdf->Cell(20, 4, 'Gs. ' . number_format($producto->precio_minorista, 0, ',', '.'), 0, 1, 'C', false, '', 0, false, 'T', 'M');
$pdf->SetFont('helvetica', '', 6);
$pdf->SetXY(6, 9);
$pdf->Cell(30, 4, $descripcion, 0, 1, 'L', false, '', 0, false, 'T', 'M');
$barcode = $pdf->write1DBarcode($code, 'C128', 3, 11, '', 10, 0.3, $style, 'N');

// Segunda etiqueta
$pdf->Image('assets/img/LogoTrinity.PNG', 44, 1, 20, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->SetFont('helvetica', '', 7);
$pdf->SetXY(42, 6);
$pdf->Cell(20, 4, 'Gs. ' . number_format($producto->precio_minorista, 0, ',', '.'), 0, 1, 'C', false, '', 0, false, 'T', 'M');
$pdf->SetFont('helvetica', '', 6);
$pdf->SetXY(40, 9);
$pdf->Cell(30, 4, $descripcion, 0, 1, 'L', false, '', 0, false, 'T', 'M');
$barcode2 = $pdf->write1DBarcode($code, 'C128', 37, 11, '', 10, 0.3, $style, 'N');

// Tercera etiqueta
$pdf->Image('assets/img/LogoTrinity.PNG', 80, 1, 20, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->SetFont('helvetica', '', 7);
$pdf->SetXY(78, 6);
$pdf->Cell(20, 4, 'Gs. ' . number_format($producto->precio_minorista, 0, ',', '.'), 0, 1, 'C', false, '', 0, false, 'T', 'M');
$pdf->SetFont('helvetica', '', 6);
$pdf->SetXY(75, 9);
$pdf->Cell(30, 4, $descripcion, 0, 1, 'L', false, '', 0, false, 'T', 'M');
$barcode = $pdf->write1DBarcode($code, 'C128', 72, 11, '', 10, 0.3, $style, 'N');

ob_end_clean();
$pdf->Output("Informe de compras de la fecha 567.pdf", 'I');


//============================================================+
// END OF FILE
//============================================================+

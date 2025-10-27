<?php
require_once('plugins/tcpdf2/tcpdf.php');
require_once(__DIR__ . '/../../utils/NumeroALetras.php'); // Corregir la ruta con __DIR__

// Crear una instancia de TCPDF
$pdf = new TCPDF();

// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema Goldent');
$pdf->SetTitle('Pagaré a la Orden');
$pdf->SetSubject('Pagaré');
$pdf->SetKeywords('Pagaré, PDF, TCPDF');

// Configurar márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

// Agregar una página
$pdf->AddPage();

// Estilo de fuente
$pdf->SetFont('helvetica', '', 12);
$venta=$this->model->ObtenerVenta($_GET['id']);
$fecha_vencimiento = date("d/m/Y", strtotime($venta->fecha_venta . " +30 days"));
$mes_letras = array(
    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril",
    5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto",
    9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
);

$mesLetra=$mes_letras[date("n", strtotime($venta->fecha_venta))];
$dia=date("d", strtotime($venta->fecha_venta));
$anio=date("Y", strtotime($venta->fecha_venta));
$valor_letra = NumeroALetras::convertir($venta->total, '', ''); // Usar la clase NumeroALetras

// Contenido del pagaré
$html = '<h1 style="text-align: center;">PAGARÉ A LA ORDEN</h1>
<table border="0" cellpadding="5">
<tr>
<td><b>Factura:</b> ' . $venta->nro_comprobante. '</td>
<td><b>Gs:</b> ' . number_format($venta->total, 0, ',', '.') . '</td>
</tr>
<tr>
<td>Ciudad del Este, ' . $dia . ' de ' . $mesLetra . ' de ' . $anio . '</td>
<td><b>Vencimiento:</b> ' . $fecha_vencimiento . '</td>
</tr>
<tr>
<td colspan="2"><b>Pagará a:</b> Calixto Villalba Ayala</td>
</tr>
<tr>
<td colspan="2">o a su orden la cantidad de Gs: <b> ' . $valor_letra . '</b></td>
</tr>
</table>
<p style="text-align: justify;">
Queda expresamente convenido que la falta de pago de este pagaré me (nos) constituirá en mora automáticamente, sin necesidad de interpelación judicial o extrajudicial alguna, devengado durante el tiempo de la mora un interés del ...% o una comisión del ...% por el simple retardo sin que esto implique prórroga del plazo de la obligación. El simple vencimiento establecerá la mora, autorizando a la inclusión a la base de datos de información conforme a lo establecido en la Ley 1682, como también para que se pueda proveer la información a terceros interesados. A los efectos Legales y Procesales nos sometemos a la jurisdicción de los tribunales de la ciudad de Ciudad del Este y renunciando a cualquier otra que pudiera corresponder. Las partes constituyen domicilio especial en los lugares indicados en el presente documento.
</p>
<table border="0" cellpadding="5">
<tr>
<td><b>Nombre:</b> ' . $venta->cliente . '</td>
</tr>
<tr>
<td><b>Domicilio:</b> ' . $venta->direccion . '</td>
</tr>
<tr>
<td><b>CI Nº:</b> ' . $venta->ruc . '</td>
</tr>
</table>
<p style="text-align: right;"><b>Firma:</b> ___________________________</p>';

// Escribir el contenido en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output('pagare_a_la_orden.pdf', 'I');

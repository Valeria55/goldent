<?php
require_once('plugins/tcpdf2/tcpdf.php');
require_once(__DIR__ . '/../../utils/NumeroALetras.php'); // Corregir la ruta con __DIR__

// Crear una instancia de TCPDF
$pdf = new TCPDF();

// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema Goldent');
$pdf->SetTitle('Orden de Entrega');
$pdf->SetSubject('Orden de Entrega');
$pdf->SetKeywords('Orden, Entrega, PDF, TCPDF');
$pdf->setPrintHeader(false);
// Configurar márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(FALSE, 15);

// Agregar una página
$pdf->AddPage();

// Estilo de fuente
$pdf->SetFont('helvetica', '', 12);

// Función para crear el contenido de la orden
function crearContenidoOrden($fecha, $items, $total_general, $valor_letra, $tipo = 'ORIGINAL') {
    $contenido = '<table border="0" cellpadding="3" style="width: 100%;">
    <tr>
        <td style="width: 30%; font-weight: bold; font-size: 10px;">GOLDENT S.A</td>
        <td style="width: 40%; text-align: center; font-weight: bold; font-size: 10px;">ORDEN DE ENTREGA - ' . $items[0]->id_presupuesto . '</td>
        <td style="width: 30%; text-align: right; font-size: 8px;">Ciudad del Este ' . $fecha . '</td>
    </tr>
    </table>
    <hr>
    
    <table border="0" cellpadding="1" cellspacing="0" style="width: 100%; line-height: 0.9;">
    <tr>
        <td style="width: 100%; font-size: 10px; padding: 1px;"></td>
    </tr>
    <tr>
        <td style="width: 100%; font-size: 10px; padding: 1px;"><b><u>DATOS</u></b></td>
    </tr>
     <tr>
        <td style="width: 100%; font-size: 10px; padding: 1px;"></td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px;"><b>Razón Social:</b> ' . $items[0]->nombre . '</td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px;"><b>R.U.C.:</b> ' . $items[0]->ruc . '</td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px;"><b>Punto de partida:</b> Calle Ernesto Baez y Los Rosales</td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px;"><b>Punto de llegada:</b> </td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px;"><b>Obs.:</b> </td>
    </tr>
    <tr>
        <td style="width: 100%; font-size: 10px; padding: 1px;"></td>
    </tr>
    <tr>
        <td style="width: 100%; font-size: 10px; padding: 1px;"><b><u>DELIVERY</u></b></td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px;"><b>Encargado:</b> </td>
    </tr>
    <tr>
        <td style="width: 100%; font-size: 10px; padding: 1px;"></td>
    </tr>
    </table>
    <table border="1" cellpadding="3" cellspacing="0" style="width: 100%;">
    
    <thead>
        <tr style="background-color: #D3D3D3; color: #000000;">
        <th style="width: 10%; text-align: center; font-size: 8px; font-weight: bold;">CANT</th>
        <th style="width: 40%; text-align: center; font-size: 8px; font-weight: bold;">ARTICULO</th>
        <th style="width: 20%; text-align: center; font-size: 8px; font-weight: bold;">PACIENTE</th>
        <th style="width: 10%; text-align: center; font-size: 8px; font-weight: bold;">PRECIO</th>
        <th style="width: 20%; text-align: center; font-size: 8px; font-weight: bold;">TOTAL</th>
        </tr>
    </thead>
    <tbody>';

    // Agregar items reales
    foreach ($items as $item) {
        $contenido .= '<tr>
        <td style="width: 10%; text-align: center; font-size: 8px;">' . $item->cantidad . '</td>
        <td style="width: 40%; font-size: 8px;">' . $item->producto . '</td>
        <td style="width: 20%; font-size: 8px;">' . $item->paciente . '</td>
        <td style="width: 10%; text-align: right; font-size: 8px;">' . number_format($item->precio_venta, 0, ',', '.') . '</td>
        <td style="width: 20%; text-align: right; font-size: 8px;">' . number_format($item->precio_venta * $item->cantidad, 0, ',', '.') . '</td>
        </tr>';
    }

    // Si hay menos de 5 items, agregar filas vacías para mantener el tamaño fijo
    $itemCount = count($items);
    if ($itemCount < 5) {
        for ($i = $itemCount; $i < 5; $i++) {
            $contenido .= '<tr>
            <td style="width: 10%; text-align: center; font-size: 8px; height: 20px;">&nbsp;</td>
            <td style="width: 40%; font-size: 8px;">&nbsp;</td>
            <td style="width: 20%; font-size: 8px;">&nbsp;</td>
            <td style="width: 10%; text-align: right; font-size: 8px;">&nbsp;</td>
            <td style="width: 20%; text-align: right; font-size: 8px;">&nbsp;</td>
            </tr>';
        }
    }

    $contenido .= '</tbody>
    </table>

    <table border="0" cellpadding="3" style="width: 100%;">
    <tr>
        <td style="width: 100%; padding: 1px;"><b></b> </td>
    </tr>
    <tr>
        <td style="width: 70%; text-align: left; font-size: 10px;"><b> ' . $valor_letra . '</b></td>
        <td style="width: 30%; text-align: right; font-size: 10px;"><b>Total Gs:</b> ' . number_format($total_general, 0, ',', '.') . '</td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px; font-size: 9px;">Certifico haber recibido íntegramente las mercaderías citadas</td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px; font-size: 9px;">FIRMA: ___________________________ CIN ___________________________ HORA ___________________________</td>
    </tr>
    <tr>
        <td style="width: 100%; padding: 1px; font-size: 9px;">ACLARACION: ______________________________________________________</td>
    </tr>
    </table>';
    
    return $contenido;
}

// Preparar datos
$meses_espanol = [
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
];
$mes = $meses_espanol[date("n") - 1];
$fecha = date("d") . ' de ' . $mes . ' del ' . date("Y");

// Calcular total
$total_general = 0;
foreach ($items as $item) {
    $total_general += ($item->precio_venta * $item->cantidad);
}
$valor_letra = NumeroALetras::convertir($total_general, '', '');

// Crear contenido completo con ORIGINAL y COPIA
$html = crearContenidoOrden($fecha, $items, $total_general, $valor_letra, 'ORIGINAL');
// $html .= '<hr style="border: 1px dashed #000; margin: 30px 0;">';
$html .= '<p style="text-align: center; font-size: 10px; margin: 20px 0;">---------------------------------------------------------------------------------------------------------------------------------------------------------</p>';
// $html .= '<hr style="border: 1px dashed #000; margin: 30px 0;">';
$html .= '<br><br><br>';
$html .= crearContenidoOrden($fecha, $items, $total_general, $valor_letra, 'COPIA');

// Escribir el contenido en el PDF
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
// Salida del PDF
$pdf->Output('orden_entrega.pdf', 'I');

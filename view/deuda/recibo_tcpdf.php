<?php
session_start();
require_once '../../model/dbconfig.php';
require_once '../../model/database.php';
require_once '../../model/deuda.php';
require_once '../../plugins/tcpdf2/tcpdf.php';

class ReciboTCPDF extends TCPDF
{
    // Constructor
    public function __construct() {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(false);
    }
    
    // Header
    public function Header() {
        // No header automático
    }
    
    // Footer
    public function Footer() {
        // No footer automático
    }
}

// Obtener el grupo_pago_id de la URL
$grupo_pago_id = $_GET['grupo_pago_id'] ?? '';
$download = $_GET['download'] ?? '';
$anulado = $_GET['anulado'] ?? '';

if (empty($grupo_pago_id)) {
    die('ID de grupo de pago requerido');
}

try {
    // DEBUG: Verificar parámetros recibidos
    error_log("DEBUG RECIBO ANULADO - grupo_pago_id: " . $grupo_pago_id);
    error_log("DEBUG RECIBO ANULADO - anulado: " . ($anulado ? 'SI' : 'NO'));
    
    // Obtener datos del recibo
    $deuda_model = new deuda();
    // Permitir obtener recibo anulado si se especifica el parámetro
    $permitir_anulado = !empty($anulado);
    error_log("DEBUG RECIBO ANULADO - permitir_anulado: " . ($permitir_anulado ? 'SI' : 'NO'));
    
    $detalle = $deuda_model->obtenerDetalleRecibo($grupo_pago_id, $permitir_anulado);
    
    if (empty($detalle['detalle_deudas'])) {
        die('No se encontraron datos para este recibo');
    }
    
    $primera_deuda = $detalle['detalle_deudas'][0];
    
    // Obtener el número de recibo de los datos
    $numero_recibo = $primera_deuda->nro_recibo ?? 'SIN-NUMERO';
    
    // Crear PDF
    $pdf = new ReciboTCPDF();
    $pdf->AddPage();
    
    // Función para generar un recibo
    function generarRecibo($pdf, $detalle, $numero_recibo, $primera_deuda, $y_offset, $tipo, $es_anulado = false) {
        $total_general = 0;
        
        // === HEADER PRINCIPAL ===
        $pdf->SetFont('helvetica', 'B', 10);
        
        // Caja izquierda - GOLDENT S.A. (con bordes redondeados)
        $pdf->RoundedRect(10, 7 + $y_offset, 95, 30, 2, '1111', 'D');
        $pdf->SetXY(10, 13 + $y_offset);
        $pdf->Cell(95, 5, 'GOLDENT S.A', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 7);
        // $pdf->SetXY(10, 18 + $y_offset);
        // $pdf->Cell(95, 3, 'de César Villalba Ayala', 0, 1, 'C');
        $pdf->SetXY(10, 21 + $y_offset);
        $pdf->Cell(95, 3, 'Actividades de Laboratorios - Médicos Dentales', 0, 1, 'C');
        $pdf->SetXY(10, 24 + $y_offset);
        $pdf->Cell(95, 3, 'TEL: 571136', 0, 1, 'C');
        $pdf->SetXY(10, 27 + $y_offset);
        $pdf->Cell(95, 3, 'Calle Ernesto Báez y Las Rosales', 0, 1, 'C');
        $pdf->SetXY(10, 30 + $y_offset);
        $pdf->Cell(95, 3, 'Ciudad del Este', 0, 1, 'C');
        
        // Caja derecha - RECIBO DE DINERO (con bordes redondeados)
        $pdf->RoundedRect(105, 7 + $y_offset, 95, 30, 2, '1111', 'D');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(105, 11 + $y_offset);
        $pdf->Cell(95, 5, 'RECIBO DE DINERO', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(105, 18 + $y_offset);
        $pdf->Cell(95, 4, $tipo, 0, 1, 'C'); // ORIGINAL o DUPLICADO
        
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY(105, 25 + $y_offset);
        $pdf->Cell(95, 3, 'RUC: 80108438-5', 0, 1, 'C');
        
        // Configurar color rojo oscuro y negrita para el número de recibo
        $pdf->SetTextColor(139, 0, 0); // Rojo oscuro (RGB: 139, 0, 0)
        $pdf->SetFont('helvetica', 'B', 14); // Negrita
        $pdf->SetXY(105, 30 + $y_offset);
        $pdf->Cell(95, 3, $numero_recibo, 0, 1, 'C');
        
        // Si es anulado, agregar marca de ANULADO
        if ($es_anulado) {
            $pdf->SetTextColor(255, 0, 0); // Rojo más intenso
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->SetXY(105, 36 + $y_offset);
            $pdf->Cell(95, 5, '*** ANULADO ***', 0, 1, 'C');
        }
        
        // Restaurar color negro para el resto del documento
        $pdf->SetTextColor(0, 0, 0);
        
        // === LAYOUT DE DOS COLUMNAS ===
        $y_start = 40 + $y_offset;
        if ($es_anulado) {
            $y_start += 5; // Dar espacio extra para la marca de anulado
        }
        
        // === COLUMNA DERECHA - TABLA DE FACTURAS (más pequeña) ===
        $tabla_x = 110; // Posición X de la tabla (lado derecho)
        $tabla_ancho = 80; // Ancho total de la tabla
        
        // Headers de la tabla (con bordes redondeados)
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Rect($tabla_x, $y_start, 37, 5, 'D'); // FACTURA N°
        $pdf->Rect($tabla_x + 37, $y_start, 27, 5, 1,  'D'); // FECHA
        $pdf->Rect($tabla_x + 64, $y_start, 26, 5, 1,  'D'); // IMPORTE
        
        $pdf->SetXY($tabla_x, $y_start + 1);
        $pdf->Cell(35, 3, 'FACTURA N°', 0, 0, 'C');
        $pdf->SetXY($tabla_x + 35, $y_start + 1);
        $pdf->Cell(25, 3, 'FECHA', 0, 0, 'C');
        $pdf->SetXY($tabla_x + 64, $y_start + 1);
        $pdf->Cell(20, 3, 'IMPORTE', 0, 0, 'C');
        
        // Datos de las facturas
        $pdf->SetFont('helvetica', '', 8);
        $y_pos = $y_start + 5;
        
        foreach ($detalle['detalle_deudas'] as $deuda) {
            // Líneas para cada fila (con bordes redondeados)
            $pdf->Rect($tabla_x, $y_pos, 37, 5, 1,  'D');
            $pdf->Rect($tabla_x + 37, $y_pos, 27, 5, 1,  'D');
            $pdf->Rect($tabla_x + 64, $y_pos, 26, 5, 1,  'D');
            
            // Datos
            $pdf->SetXY($tabla_x + 1, $y_pos + 1);
            $pdf->Cell(33, 3, $deuda->nro_comprobante ?: 'N/A', 0, 0, 'L');
            
            $pdf->SetXY($tabla_x + 36, $y_pos + 1);
            $fecha_formateada = date('d/m/Y', strtotime($deuda->deuda_fecha));
            $pdf->Cell(23, 3, $fecha_formateada, 0, 0, 'C');
            
            $pdf->SetXY($tabla_x + 68, $y_pos + 1);
            $monto_formateado = number_format($deuda->monto_aplicado, 0, '.', '.');
            $pdf->Cell(18, 3, $monto_formateado, 0, 0, 'R');
            
            $total_general += $deuda->monto_aplicado;
            $y_pos += 5;
        }
        
        // Completar filas vacías hasta llegar a 10 filas totales
        $filas_usadas = count($detalle['detalle_deudas']);
        $filas_vacias = max(0, 15 - $filas_usadas);
        
        for ($i = 0; $i < $filas_vacias; $i++) {
            $pdf->Rect($tabla_x, $y_pos, 37, 5, 1,  'D');
            $pdf->Rect($tabla_x + 37, $y_pos, 27, 5, 1, 'D');
            $pdf->Rect($tabla_x + 64, $y_pos, 26, 5, 1,  'D');
            $y_pos += 5;
        }
        
        // TOTAL (con bordes redondeados)
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Rect($tabla_x, $y_pos, 64, 5, 1,  'D');
        $pdf->Rect($tabla_x + 64, $y_pos, 26, 5, 1,  'D');
        
        $pdf->SetXY($tabla_x + 1, $y_pos + 1);
        $pdf->Cell(58, 3, 'TOTALES Gs.', 0, 0, 'L');
        $pdf->SetXY($tabla_x + 61, $y_pos + 1);
        $pdf->Cell(25, 3, number_format($total_general, 0, '.', '.'), 0, 0, 'R');
        
        // === COLUMNA IZQUIERDA - INFORMACIÓN DEL CLIENTE ===
        $y_left = $y_start;
        
        // Caja para COD. CLIENTE y LUGAR Y FECHA (con bordes redondeados)
        $pdf->RoundedRect(10, $y_left, 30, 10, 1.5, '1111', 'D');
        $pdf->RoundedRect(40, $y_left, 65, 10, 1.5, '1111', 'D');
        
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY(12, $y_left + 1);
        $pdf->Cell(36, 3, 'COD. CLIENTE', 0, 1, 'L');
        $pdf->SetXY(45, $y_left + 1);
        $pdf->Cell(61, 3, 'LUGAR Y FECHA', 0, 1, 'L');
        
        $pdf->SetXY(12, $y_left + 5);
        $pdf->Cell(36, 3, $primera_deuda->id_cliente ?: '156', 0, 1, 'L');
        $pdf->SetXY(45, $y_left + 5);
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');

        $fecha_actual = 'Ciudad del Este, ' . strftime('%d de %B de %Y');
        // $fecha_actual = 'Ciudad del Este, ' . date('d \d\e F \d\e Y');
        $pdf->Cell(61, 3, $fecha_actual, 0, 1, 'L');
        
        // HEMOS RECIBIDO DE:
        $y_left += 15;
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(10, $y_left);
        $pdf->Cell(110, 4, 'HEMOS RECIBIDO DE:', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY(10, $y_left + 5);
        $pdf->Cell(110, 4, strtoupper($primera_deuda->cliente_nombre), 0, 1, 'L');
        
        // R.U.C. / C.I.N°
        $pdf->SetXY(10, $y_left + 10);
        $pdf->Cell(110, 4, 'R.U.C. / C.I.N°: ' . ($primera_deuda->cliente_documento ?: '1361021-0'), 0, 1, 'L');
        
        // La Cantidad de Guaranies
        $pdf->SetXY(10, $y_left + 15);
        $pdf->Cell(110, 4, 'La Cantidad de Guaranies:', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', 'B', 7);
        $total_en_texto = numeroALetras($total_general);
        $pdf->SetXY(10, $y_left + 20);
        $pdf->Cell(110, 4, strtoupper($total_en_texto) . ' * * * * * * * * * * * * * *', 0, 1, 'L');
        
        // En concepto de pago
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY(10, $y_left + 26);
        $pdf->Cell(110, 4, 'En concepto de pago de factura(s) según detalle.', 0, 1, 'L');
        
        // === SECCIÓN INFERIOR - FIRMA Y SELLO ===
        $y_bottom = max($y_pos + 10, $y_left + 35);
        
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY(10, $y_bottom);
        $pdf->Cell(95, 4, '____________________________________', 0, 0, 'C');
        $pdf->SetXY(105, $y_bottom);
        $pdf->Cell(95, 4, $tipo . ': Archivo', 0, 0, 'R');
        
        $pdf->SetXY(10, $y_bottom + 4);
        $pdf->Cell(95, 4, 'FIRMA Y SELLO', 0, 0, 'C');
        
        return $total_general;
    }
    
    // Generar RECIBO ORIGINAL (parte superior)
    generarRecibo($pdf, $detalle, $numero_recibo, $primera_deuda, 0, 'ORIGINAL', !empty($anulado));
    
    // Línea divisoria
    $pdf->Line(10, 140, 200, 140);
    
    // Generar RECIBO DUPLICADO (parte inferior)
    generarRecibo($pdf, $detalle, $numero_recibo, $primera_deuda, 140, 'DUPLICADO', !empty($anulado));    // Output PDF
    $filename = 'Recibo_' . str_replace('-', '_', $numero_recibo);
    if (!empty($anulado)) {
        $filename .= '_ANULADO';
    }
    $filename .= '.pdf';
    
    if ($download) {
        $pdf->Output($filename, 'D'); // Descargar
    } else {
        $pdf->Output($filename, 'I'); // Mostrar en navegador
    }
    
} catch (Exception $e) {
    die('Error al generar el recibo: ' . $e->getMessage());
}

// Función para convertir números a letras
function numeroALetras($numero) {
    $unidades = array(
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'
    );
    
    $decenas = array(
        '', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
    );
    
    $centenas = array(
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    );
    
    if ($numero == 0) return 'cero';
    if ($numero == 100) return 'cien';
    
    $resultado = '';
    
    // Millones
    if ($numero >= 1000000) {
        $millones = floor($numero / 1000000);
        if ($millones == 1) {
            $resultado .= 'un millon ';
        } else {
            $resultado .= numeroALetras($millones) . ' millones ';
        }
        $numero %= 1000000;
    }
    
    // Miles
    if ($numero >= 1000) {
        $miles = floor($numero / 1000);
        if ($miles == 1) {
            $resultado .= 'mil ';
        } else {
            $resultado .= numeroALetras($miles) . ' mil ';
        }
        $numero %= 1000;
    }
    
    // Centenas
    if ($numero >= 100) {
        $resultado .= $centenas[floor($numero / 100)] . ' ';
        $numero %= 100;
    }
    
    // Decenas y unidades
    if ($numero >= 20) {
        $resultado .= $decenas[floor($numero / 10)];
        if ($numero % 10 != 0) {
            $resultado .= ' y ' . $unidades[$numero % 10];
        }
    } else if ($numero > 0) {
        $resultado .= $unidades[$numero];
    }
    
    return trim($resultado);
}
?>
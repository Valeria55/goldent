<?php

/**
 * Generador de Facturas PDF Optimizado
 * Versión mejorada con configuración centralizada y limpieza de código
 */

// Configuración de memoria y tiempo de ejecución
set_time_limit(120);
ini_set('memory_limit', '256M');

/**
 * Clase para conversión de números a letras
 */
class NumeroALetras
{
    private static $UNIDADES = [
        '',
        'un ',
        'dos ',
        'tres ',
        'cuatro ',
        'cinco ',
        'seis ',
        'siete ',
        'ocho ',
        'nueve ',
        'diez ',
        'once ',
        'doce ',
        'trece ',
        'catorce ',
        'quince ',
        'dieciséis ',
        'diecisiete ',
        'dieciocho ',
        'diecinueve ',
        'veinte '
    ];

    private static $DECENAS = [
        'venti',
        'treinta ',
        'cuarenta ',
        'cincuenta ',
        'sesenta ',
        'setenta ',
        'ochenta ',
        'noventa ',
        'cien '
    ];

    private static $CENTENAS = [
        'ciento ',
        'doscientos ',
        'trescientos ',
        'cuatrocientos ',
        'quinientos ',
        'seiscientos ',
        'setecientos ',
        'ochocientos ',
        'novecientos '
    ];

    public static function convertir($number, $moneda = '', $centimos = '', $forzarCentimos = false)
    {
        if (($number < 0) || ($number > 999999999)) {
            return 'No es posible convertir el número a letras';
        }

        $converted = '';
        $decimales = '';

        $div_decimales = explode('.', $number);

        if (count($div_decimales) > 1) {
            $number = $div_decimales[0];
            $decNumberStr = (string) $div_decimales[1];
            if (strlen($decNumberStr) == 2) {
                $decNumberStrFill = str_pad($decNumberStr, 9, '0', STR_PAD_LEFT);
                $decCientos = substr($decNumberStrFill, 6);
                $decimales = self::convertGroup($decCientos);
            }
        } elseif (count($div_decimales) == 1 && $forzarCentimos) {
            $decimales = 'cero ';
        }

        $numberStr = (string) $number;
        $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);

        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'un millón ';
            } else {
                $converted .= sprintf('%smillones ', self::convertGroup($millones));
            }
        }

        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'mil ';
            } else {
                $converted .= sprintf('%smil ', self::convertGroup($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'un ';
            } else {
                $converted .= sprintf('%s ', self::convertGroup($cientos));
            }
        }

        return empty($decimales)
            ? $converted . strtoupper($moneda)
            : $converted . strtoupper($moneda) . ' con ' . $decimales . ' ' . strtoupper($centimos);
    }

    private static function convertGroup($n)
    {
        $output = '';

        if ($n == '100') {
            $output = "cien ";
        } elseif ($n[0] !== '0') {
            $output = self::$CENTENAS[$n[0] - 1];
        }

        $k = intval(substr($n, 1));

        if ($k <= 20) {
            $output .= self::$UNIDADES[$k];
        } else {
            if (($k > 30) && ($n[2] !== '0')) {
                $output .= sprintf('%sy %s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            } else {
                $output .= sprintf('%s%s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            }
        }

        return $output;
    }
}

/**
 * Clase principal para generación de facturas
 * Con sistema de paginación dinámica profesional
 */
class FacturaGenerator
{
    private $pdf;
    private $venta;
    private $datosFactura;
    private $totales;
    private $config;

    public function __construct($venta)
    {
        $this->venta = $venta;

        // ==========================================
        // CONFIGURACIÓN CENTRALIZADA
        // Ajuste estos valores para alinear con el preimpreso
        // ==========================================
        $this->config = [
            // ╔══════════════════════════════════════════════════════════════╗
            // ║           CONFIGURACIÓN CENTRALIZADA DE POSICIONES            ║
            // ║   Modifique estos valores para alinear con el preimpreso     ║
            // ╚══════════════════════════════════════════════════════════════╝

            // ========================================
            // DIMENSIONES DE PÁGINA (mm)
            // ========================================
            // No modificar a menos que cambie el tamaño del papel
            'page_width' => 210,      // Ancho en mm (A4)
            'page_height' => 357,     // Alto en mm (Oficio/Legal)

            // ========================================
            // MÁRGENES GLOBALES (mm)
            // ========================================
            // ► margin_left: Mueve TODO hacia la derecha (+) o izquierda (-)
            // ► margin_top: Margen superior mínimo de la página
            // ► margin_right: Espacio del borde derecho
            'margin_left' => 10,
            'margin_top' => 5,
            'margin_right' => 5,

            // ========================================
            // FUENTES
            // ========================================
            // ► font_size_*: Aumentar = texto más grande, Reducir = texto más pequeño
            'font_family' => 'helvetica',
            'font_size_header' => 8,   // Tamaño de Fecha, Nombre, RUC
            'font_size_body' => 8,     // Tamaño de productos
            'font_size_footer' => 8,   // Tamaño de totales

            // ╔══════════════════════════════════════════════════════════════╗
            // ║              POSICIONES VERTICALES (eje Y)                    ║
            // ║   Los valores son en mm desde el INICIO de cada copia        ║
            // ║   AUMENTAR = mover hacia ABAJO, REDUCIR = mover hacia ARRIBA ║
            // ╚══════════════════════════════════════════════════════════════╝

            // ► copia_altura: Altura de cada copia (Original/Duplicado/Triplicado)
            //   - Si las copias se solapan → AUMENTAR (ej: 120, 122)
            //   - Si hay mucho espacio entre copias → REDUCIR (ej: 117, 115)
            'copia_altura' => 108,

            // ► header_y_offset: Posición de Fecha/Nombre/RUC
            //   - Si el header está muy arriba → AUMENTAR (ej: 35, 38)
            //   - Si el header está muy abajo → REDUCIR (ej: 30, 28)
            'header_y_offset' => 33,

            // ► header_line_height: Espacio entre líneas del header (mm)
            //   - Si las líneas del header están muy juntas → AUMENTAR (ej: 5, 5.5)
            //   - Si están muy separadas → REDUCIR (ej: 4, 3.5)
            'header_line_height' => 4.5,

            // ► productos_y_inicio: Donde empieza el PRIMER producto
            //   - Si los productos empiezan muy arriba → AUMENTAR (ej: 55, 58)
            //   - Si empiezan muy abajo → REDUCIR (ej: 50, 48)
            'productos_y_inicio' => 54,

            // ► productos_line_height: Espacio entre cada línea de producto
            //   - Si los productos están muy juntos → AUMENTAR (ej: 4.5, 5)
            //   - Si están muy separados → REDUCIR (ej: 4, 3.8)
            'productos_line_height' => 3.5,

            // ► max_lineas_productos: Cantidad máxima de líneas antes de saltar de página
            'max_lineas_productos' => 7,

            // ========================================
            // LÍMITE DE CARACTERES POR LÍNEA
            // ========================================
            // Si la descripción supera este límite → cuenta como 2 líneas
            // ► Si los productos saltan muy rápido a 2 líneas → AUMENTAR (ej: 45, 50)
            // ► Si no saltan cuando deberían → REDUCIR (ej: 35, 30)
            'chars_por_linea' => 40,

            // ► subtotal_y_offset: Donde aparece la línea de SUBTOTAL
            //   - Si el subtotal está muy arriba → AUMENTAR (ej: 85, 88)
            //   - Si está muy abajo → REDUCIR (ej: 80, 78)
            'subtotal_y_offset' => 78,

            // ► iva_y_offset: Donde aparece la LIQUIDACIÓN IVA
            //   - Si el IVA está muy arriba → AUMENTAR (ej: 100, 102)
            //   - Si está muy abajo → REDUCIR (ej: 95, 93)
            'iva_y_offset' => 90,

            // ╔══════════════════════════════════════════════════════════════╗
            // ║           ANCHOS DE COLUMNAS DE PRODUCTOS (%)                 ║
            // ║   Deben sumar 100%. Modificar para ajustar espacios.         ║
            // ╚══════════════════════════════════════════════════════════════╝
            // ► Si hay mucho espacio entre producto y precio → AUMENTAR col_producto
            // ► Si el texto del producto se corta → AUMENTAR col_producto
            'col_cantidad' => 10,   // Columna "CANT"
            'col_producto' => 55,   // Columna "PRODUCTO/SERVICIO"
            'col_precio' => 11,     // Columna "P.UNITARIO"
            'col_exenta' => 10,     // Columna "EXENTA"
            'col_iva5' => 7,        // Columna "IVA 5%"
            'col_iva10' => 7,       // Columna "IVA 10%"
        ];

        $this->initializePDF();
        $this->initializeTotales();
    }

    private function initializePDF()
    {
        require_once('plugins/tcpdf2/tcpdf.php');

        $medidas = [$this->config['page_width'], $this->config['page_height']];
        $this->pdf = new TCPDF('P', 'mm', $medidas, true, 'UTF-8', false);

        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetHeaderMargin(0);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetMargins(
            $this->config['margin_left'],
            $this->config['margin_top'],
            $this->config['margin_right']
        );
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->SetFont($this->config['font_family'], '', $this->config['font_size_body']);
        $this->pdf->AddPage();
    }

    private function initializeTotales()
    {
        $this->totales = [
            'sumaTotal' => 0,
            'sumaTotal5' => 0,
            'sumaTotal10' => 0,
            'sumaTotalexe' => 0,
            'iva5' => 0,
            'iva10' => 0,
            'exe' => 0,
            'cantidad_total' => 0
        ];
    }

    public function generarFactura($id_venta)
    {
        $this->cargarDatosFactura($id_venta);
        $this->procesarItems($id_venta);
        $this->generarOutputPDF();
    }

    private function cargarDatosFactura($id_venta)
    {
        $this->datosFactura = [
            'cliente' => "Cliente ocasional",
            'ruc' => "X",
            'fecha' => date("d/m/Y"),
            'telefono' => "",
            'vendedor' => "",
            'contado' => "",
            'credito' => "",
            'tipo' => "Guaraníes",
            'paciente' => ""
        ];

        $datos = $this->venta->Listar($id_venta);
        if (!empty($datos)) {
            foreach ($datos as $r) {
                $this->datosFactura['cliente'] = $r->nombre_cli ?? "Cliente ocasional";
                $this->datosFactura['ruc'] = $r->ruc ?? "X";
                $this->datosFactura['fecha'] = date("d/m/Y", strtotime($r->fecha_venta));
                $this->datosFactura['telefono'] = $r->telefono ?? "";
                $this->datosFactura['vendedor'] = $r->vendedor ?? "";

                if ($r->contado == "Contado") {
                    $this->datosFactura['contado'] = "X";
                    $this->datosFactura['credito'] = "";
                } else {
                    $this->datosFactura['contado'] = "";
                    $this->datosFactura['credito'] = "X";
                }

                $this->datosFactura['paciente'] = $r->paciente ?? "";
                break;
            }
        }
    }

    // ================================================================
    // FUNCIONES DE CÁLCULO DE LÍNEAS
    // ================================================================

    /**
     * Calcula cuántas líneas ocupa un producto según su descripción.
     * 
     * @param string $descripcion - Texto del producto
     * @param int $anchoDisponible - Caracteres por línea (default: config)
     * @param int $fontSize - Tamaño de fuente (no usado directamente, pero reservado)
     * @return int - 1 o 2 líneas
     */
    private function calcularLineasProducto($descripcion, $anchoDisponible = null, $fontSize = null)
    {
        if ($anchoDisponible === null) {
            $anchoDisponible = $this->config['chars_por_linea'];
        }

        $longitud = mb_strlen($descripcion, 'UTF-8');

        if ($longitud <= $anchoDisponible) {
            return 1;
        }

        // Por simplicidad, asumimos máximo 2 líneas.
        // Si quisieras más granularidad: return ceil($longitud / $anchoDisponible);
        return 2;
    }

    /**
     * Genera un array de productos con información de líneas ocupadas.
     * 
     * @param array $productos - Array de productos
     * @return array - Productos con campo 'lineas_ocupadas'
     */
    private function generarLineasProductos($productos)
    {
        $resultado = [];
        foreach ($productos as $producto) {
            $lineas = $this->calcularLineasProducto($producto['producto']);
            $producto['lineas_ocupadas'] = $lineas;
            $resultado[] = $producto;
        }
        return $resultado;
    }

    /**
     * Divide los productos en grupos que caben en cada página (max 7 líneas).
     * AHORA INCLUYE LOS TOTALES DE CADA PÁGINA.
     * 
     * @param array $productosConLineas - Productos con 'lineas_ocupadas'
     * @return array - Array de páginas, cada una con sus productos Y totales
     */
    private function dividirEnPaginasConTotales($productosConLineas)
    {
        $maxLineas = $this->config['max_lineas_productos'];
        $paginas = [];
        $paginaActual = [];
        $lineasUsadas = 0;

        // Totales de esta página
        $totalesPagina = [
            'sumaTotal' => 0,
            'sumaTotal5' => 0,
            'sumaTotal10' => 0,
            'sumaTotalexe' => 0,
            'iva5' => 0,
            'iva10' => 0
        ];

        foreach ($productosConLineas as $producto) {
            $lineasProducto = $producto['lineas_ocupadas'];

            // Si agregar este producto excede el límite, crear nueva página
            if ($lineasUsadas + $lineasProducto > $maxLineas && !empty($paginaActual)) {
                $paginas[] = [
                    'productos' => $paginaActual,
                    'lineas_usadas' => $lineasUsadas,
                    'totales' => $totalesPagina
                ];
                $paginaActual = [];
                $lineasUsadas = 0;
                // Reset totales para nueva página
                $totalesPagina = [
                    'sumaTotal' => 0,
                    'sumaTotal5' => 0,
                    'sumaTotal10' => 0,
                    'sumaTotalexe' => 0,
                    'iva5' => 0,
                    'iva10' => 0
                ];
            }

            // Acumular totales de esta página
            $rawTotal = $producto['raw_total'];
            $rawIva = $producto['raw_iva'];

            $totalesPagina['sumaTotal'] += $rawTotal;

            switch ($rawIva) {
                case 5:
                    $totalesPagina['sumaTotal5'] += $rawTotal;
                    $totalesPagina['iva5'] += ($rawTotal / 21);
                    break;
                case 10:
                    $totalesPagina['sumaTotal10'] += $rawTotal;
                    $totalesPagina['iva10'] += ($rawTotal / 11);
                    break;
                default:
                    $totalesPagina['sumaTotalexe'] += $rawTotal;
                    break;
            }

            $paginaActual[] = $producto;
            $lineasUsadas += $lineasProducto;
        }

        // Última página
        if (!empty($paginaActual)) {
            $paginas[] = [
                'productos' => $paginaActual,
                'lineas_usadas' => $lineasUsadas,
                'totales' => $totalesPagina
            ];
        }

        return $paginas;
    }

    // ================================================================
    // PROCESAMIENTO DE ITEMS
    // ================================================================

    private function procesarItems($id_venta)
    {
        $items = [];
        $datos = $this->venta->Listar($id_venta);

        foreach ($datos as $r) {
            $this->acumularTotalesIVA($r);

            $producto = $r->producto;
            if (!empty($this->datosFactura['paciente'])) {
                $producto .= " PACIENTE: " . $this->datosFactura['paciente'];
            }

            $colExenta = '';
            $col5 = '';
            $col10 = '';
            if ($r->iva == 5) $col5 = number_format($r->total, 0, ",", ".");
            elseif ($r->iva == 10) $col10 = number_format($r->total, 0, ",", ".");
            else $colExenta = number_format($r->total, 0, ",", ".");

            $items[] = [
                'cantidad' => $r->cantidad,
                'producto' => $producto,
                'precio_unit' => number_format($r->precio_venta, 0, ",", "."),
                'exenta' => $colExenta,
                'iva5' => $col5,
                'iva10' => $col10,
                // Datos RAW para calcular subtotales por página
                'raw_total' => $r->total,
                'raw_iva' => $r->iva
            ];

            $this->totales['cantidad_total'] += $r->cantidad;
            $this->totales['sumaTotal'] += $r->total;
        }

        // Preparar productos con información de líneas
        $productosConLineas = $this->generarLineasProductos($items);

        // Dividir en páginas si es necesario (ahora incluye totales por página)
        $paginas = $this->dividirEnPaginasConTotales($productosConLineas);

        // Generar las 3 copias (Original, Duplicado, Triplicado)
        $this->generarTresCopias($paginas);
    }

    private function acumularTotalesIVA($r)
    {
        switch ($r->iva) {
            case 5:
                $this->totales['sumaTotal5'] += $r->total;
                $this->totales['iva5'] += ($r->total / 21);
                break;
            case 10:
                $this->totales['sumaTotal10'] += $r->total;
                $this->totales['iva10'] += ($r->total / 11);
                break;
            default:
                $this->totales['sumaTotalexe'] += $r->total;
                $this->totales['exe'] += $r->total;
                break;
        }
    }

    // ================================================================
    // GENERACIÓN DE LAS 3 COPIAS CON PAGINACIÓN
    // ================================================================

    /**
     * Genera las 3 copias (Original, Duplicado, Triplicado).
     * 
     * ESTRUCTURA CORRECTA:
     * - Página física 1: Original + Duplicado + Triplicado (con productos de página lógica 1)
     * - Página física 2: Original + Duplicado + Triplicado (con productos de página lógica 2)
     * - etc.
     */
    private function generarTresCopias($paginas)
    {
        $copiaAltura = $this->config['copia_altura'];
        $totalPaginasProductos = count($paginas);

        // Iterar primero por PÁGINA DE PRODUCTOS, luego por las 3 COPIAS
        foreach ($paginas as $numPagina => $datosPagina) {
            $esUltimaPaginaProductos = ($numPagina === $totalPaginasProductos - 1);

            // Obtener los totales de ESTA página (no el global)
            $totalesPagina = $datosPagina['totales'];

            // Para cada página de productos, imprimir las 3 copias en la misma hoja física
            for ($indiceCopia = 0; $indiceCopia < 3; $indiceCopia++) {

                // Calcular Y base para esta copia (0, 119, 238 mm)
                $yBase = $indiceCopia * $copiaAltura;

                // 1. Imprimir HEADER (Fecha, Nombre, RUC, etc.)
                $this->imprimirHeader($yBase);

                // 2. Imprimir PRODUCTOS de esta página
                $this->imprimirProductos($yBase, $datosPagina['productos']);

                // 3. Imprimir FOOTER con los totales de ESTA página
                $this->imprimirFooter($yBase, $totalesPagina);
            }

            // Si hay más páginas de productos, crear nueva página física
            if (!$esUltimaPaginaProductos) {
                $this->pdf->AddPage();
            }
        }
    }

    // ================================================================
    // FUNCIONES DE IMPRESIÓN CON COORDENADAS EXACTAS
    // ================================================================

    /**
     * Imprime el header de la factura (Fecha, Nombre, RUC, etc.)
     */
    private function imprimirHeader($yBase)
    {
        $d = $this->datosFactura;
        $fs = $this->config['font_size_header'];
        $yHeader = $yBase + $this->config['header_y_offset'];
        $lh = $this->config['header_line_height'];
        $marginLeft = $this->config['margin_left'];
        $pageWidth = $this->config['page_width'] - $this->config['margin_left'] - $this->config['margin_right'];

        $this->pdf->SetFont($this->config['font_family'], '', $fs);

        // Línea 1: Fecha + Condición de venta (Contado/Crédito)
        $this->pdf->SetXY($marginLeft + 15, $yHeader);
        $this->pdf->Cell(40, $lh, $d['fecha'], 0, 0, 'L');

        // ► Posición X de Contado y Crédito
        // ► xContado: posición de la X de Contado
        // ► Crédito está 10mm (1cm) a la derecha de Contado
        $xContado = $marginLeft + 150;  // Ajustar para mover Contado
        $espacioContadoCredito = -3.5;     // 10mm = 1cm de separación

        $this->pdf->SetXY($xContado, $yHeader);
        $this->pdf->Cell(10, $lh, $d['contado'], 0, 0, 'C');
        $this->pdf->SetXY($xContado + $espacioContadoCredito + 20, $yHeader); // +20mm extra a la derecha
        $this->pdf->Cell(10, $lh, $d['credito'], 0, 0, 'C');

        // Línea 2: Nombre/Razón Social (Mivido 1mm hacia arriba)
        $this->pdf->SetXY($marginLeft + 35, $yHeader + $lh - 1);
        $this->pdf->Cell($pageWidth - 40, $lh, $d['cliente'], 0, 0, 'L');

        // Línea 3: RUC (más cerca del nombre)
        // ► Si querés más espacio entre nombre y RUC → AUMENTAR el multiplicador (ej: 1.8, 2)
        // ► Si querés menos espacio → REDUCIR el multiplicador (ej: 1.4, 1.2)
        $this->pdf->SetXY($marginLeft + 18, $yHeader + ($lh * 1.6));
        $this->pdf->Cell($pageWidth - 20, $lh, $d['ruc'], 0, 0, 'L');
    }

    /**
     * Imprime los productos en coordenadas exactas.
     * - Cantidad: Alineada con la PRIMERA línea del producto
     * - Precio Unitario: Movido 20mm hacia la izquierda
     */
    private function imprimirProductos($yBase, $productos)
    {
        $fs = $this->config['font_size_body'];
        $yInicio = $yBase + $this->config['productos_y_inicio'];
        $lineHeight = $this->config['productos_line_height'];
        $marginLeft = $this->config['margin_left'];
        $pageWidth = $this->config['page_width'] - $marginLeft - $this->config['margin_right'];

        $this->pdf->SetFont($this->config['font_family'], '', $fs);

        // Calcular anchos de columnas en mm
        $wCant = $pageWidth * ($this->config['col_cantidad'] / 100);
        $wProd = $pageWidth * ($this->config['col_producto'] / 100);
        $wPrec = $pageWidth * ($this->config['col_precio'] / 100);
        $wExe = $pageWidth * ($this->config['col_exenta'] / 100);
        $wIva5 = $pageWidth * ($this->config['col_iva5'] / 100);
        $wIva10 = $pageWidth * ($this->config['col_iva10'] / 100);

        // ► AJUSTE: Mover precio unitario hacia la izquierda (en mm)
        // Si querés moverlo más → AUMENTAR, si menos → REDUCIR
        $precioOffsetIzquierda = 20; // 20mm = 2cm hacia la izquierda

        $yActual = $yInicio;

        foreach ($productos as $item) {
            $lineasOcupadas = $item['lineas_ocupadas'];

            // Posición X inicial
            $x = $marginLeft;

            // ============================================================
            // CANTIDAD - Alineada con la PRIMERA línea del producto
            // Usamos lineHeight simple, no multiplicado por lineasOcupadas
            // ============================================================
            $this->pdf->SetXY($x, $yActual);
            $this->pdf->Cell($wCant, $lineHeight, $item['cantidad'], 0, 0, 'C');
            $x += $wCant;

            // ============================================================
            // PRODUCTO (puede ocupar 2 líneas)
            // Limitamos el ancho para que no solape con el precio desplazado
            // ============================================================
            $anchoProductoReal = $wProd - $precioOffsetIzquierda; // Restamos el offset del precio

            // Reducir fuente solo para el producto
            $this->pdf->SetFont($this->config['font_family'], '', $this->config['font_size_body'] - 1.5);

            $this->pdf->SetXY($x, $yActual);
            $this->pdf->MultiCell($anchoProductoReal, $lineHeight, $item['producto'], 0, 'L', false, 0);

            // Restaurar fuente normal
            $this->pdf->SetFont($this->config['font_family'], '', $fs);

            $x += $wProd; // Seguimos usando wProd para mantener la posición de las columnas siguientes

            // ============================================================
            // PRECIO UNITARIO - Movido hacia la izquierda
            // El offset lo desplaza 2cm a la izquierda de su posición normal
            // ============================================================
            $this->pdf->SetXY($x - $precioOffsetIzquierda, $yActual);
            $this->pdf->Cell($wPrec, $lineHeight, $item['precio_unit'], 0, 0, 'R');
            $x += $wPrec;

            // ============================================================
            // EXENTA - Alineado con primera línea
            // ============================================================
            $this->pdf->SetXY($x, $yActual);
            $this->pdf->Cell($wExe, $lineHeight, $item['exenta'], 0, 0, 'R');
            $x += $wExe;

            // ============================================================
            // IVA 5% - Alineado con primera línea
            // ============================================================
            $this->pdf->SetXY($x, $yActual);
            $this->pdf->Cell($wIva5, $lineHeight, $item['iva5'], 0, 0, 'R');
            $x += $wIva5;

            // ============================================================
            // IVA 10% - Alineado con primera línea
            // ============================================================
            $this->pdf->SetXY($x, $yActual);
            $this->pdf->Cell($wIva10, $lineHeight, $item['iva10'], 0, 0, 'R');

            // ============================================================
            // AVANZAR Y - Siempre avanzamos 1 línea por producto
            // Si el producto tiene texto largo, MultiCell lo maneja internamente
            // pero nosotros usamos altura fija para mantener espaciado uniforme
            // ============================================================
            // Opción 1: Altura fija por producto (más uniforme)
            $yActual += $lineHeight * $lineasOcupadas;
        }
    }

    /**
     * Imprime líneas de relleno vacías para completar las 7 líneas.
     */
    private function imprimirRelleno($yBase, $lineasUsadas)
    {
        // El relleno es visual, no imprimimos nada.
        // El espacio se mantiene porque usamos coordenadas fijas para el footer.
    }

    /**
     * Imprime el footer (Subtotal, Total, Liquidación IVA).
     * Los totales están ALINEADOS con las columnas de productos (Exenta, IVA5%, IVA10%)
     * La posición Y es FIJA según subtotal_y_offset y iva_y_offset
     * 
     * @param float $yBase - Posición Y base de la copia
     * @param array $totalesPagina - Totales de ESTA página (no globales)
     */
    private function imprimirFooter($yBase, $totalesPagina)
    {
        // Formatear los totales de ESTA página
        $t = [
            'total' => number_format($totalesPagina['sumaTotal'], 0, ",", "."),
            'total5' => number_format($totalesPagina['sumaTotal5'], 0, ",", "."),
            'total10' => number_format($totalesPagina['sumaTotal10'], 0, ",", "."),
            'exe' => number_format($totalesPagina['sumaTotalexe'], 0, ",", "."),
            'iva5' => number_format($totalesPagina['iva5'], 0, ",", "."),
            'iva10' => number_format($totalesPagina['iva10'], 0, ",", "."),
            'ivaTotal' => number_format(($totalesPagina['iva5'] + $totalesPagina['iva10']), 0, ",", ".")
        ];

        // Generar letras del total de esta página
        $letras = NumeroALetras::convertir($totalesPagina['sumaTotal']);

        $fs = $this->config['font_size_footer'];
        $marginLeft = $this->config['margin_left'];
        $pageWidth = $this->config['page_width'] - $marginLeft - $this->config['margin_right'];

        $this->pdf->SetFont($this->config['font_family'], '', $fs);

        // ============================================================
        // CALCULAR POSICIONES X EXACTAS BASADAS EN COLUMNAS DE PRODUCTOS
        // Así los totales quedan alineados con las mismas columnas
        // ============================================================
        $wCant = $pageWidth * ($this->config['col_cantidad'] / 100);
        $wProd = $pageWidth * ($this->config['col_producto'] / 100);
        $wPrec = $pageWidth * ($this->config['col_precio'] / 100);
        $wExe = $pageWidth * ($this->config['col_exenta'] / 100);
        $wIva5 = $pageWidth * ($this->config['col_iva5'] / 100);
        $wIva10 = $pageWidth * ($this->config['col_iva10'] / 100);

        // Posición X donde empieza cada columna
        $xCant = $marginLeft;
        $xProd = $xCant + $wCant;
        $xPrec = $xProd + $wProd;
        $xExe = $xPrec + $wPrec;
        $xIva5 = $xExe + $wExe;
        $xIva10 = $xIva5 + $wIva5;

        // ============================================================
        // POSICIÓN Y FIJA DEL SUBTOTAL (siempre en el mismo lugar)
        // ============================================================
        $ySubtotal = $yBase + $this->config['subtotal_y_offset'];

        // ============================================================
        // SUBTOTALES - Exenta e IVA 5% separados, IVA 10% alineado con producto
        // ============================================================
        // ► Offset para separar Exenta e IVA 5% del valor IVA 10%
        // Si los 0s están muy pegados al valor → AUMENTAR
        $separarIzquierda = 25; // mm para mover Exenta e IVA 5% a la izquierda

        // Subtotal Exenta (movido a la izquierda)
        $this->pdf->SetXY($xExe - $separarIzquierda, $ySubtotal);
        $this->pdf->Cell($wExe, 4, $t['exe'], 0, 0, 'R');

        // Subtotal IVA 5% (movido a la izquierda)
        $this->pdf->SetXY($xIva5 - ($separarIzquierda / 2), $ySubtotal);
        $this->pdf->Cell($wIva5, 4, $t['total5'], 0, 0, 'R');

        // Subtotal IVA 10% (alineado exactamente con columna de productos)
        $this->pdf->SetXY($xIva10, $ySubtotal);
        $this->pdf->Cell($wIva10, 4, $t['total10'], 0, 0, 'R');

        // ============================================================
        // TOTAL A PAGAR - Mismo X que columna IVA 10%
        // ============================================================
        $yTotal = $ySubtotal + 5;
        $this->pdf->SetXY($marginLeft + 30, $yTotal);
        $this->pdf->Cell(100, 4, 'Guaranies ' . $letras, 0, 0, 'L');

        $this->pdf->SetFont($this->config['font_family'], 'B', $fs);
        $this->pdf->SetXY($xIva10, $yTotal);
        $this->pdf->Cell($wIva10, 4, $t['total'], 0, 0, 'R');
        $this->pdf->SetFont($this->config['font_family'], '', $fs);

        // ============================================================
        // LIQUIDACIÓN IVA (posición Y fija)
        // ============================================================
        $yIVA = $yBase + $this->config['iva_y_offset'];

        $this->pdf->SetXY($marginLeft + 55, $yIVA);
        $this->pdf->Cell(25, 4, $t['iva5'], 0, 0, 'L');

        $this->pdf->SetXY($marginLeft + 100, $yIVA);
        $this->pdf->Cell(25, 4, $t['iva10'], 0, 0, 'C');

        $this->pdf->SetXY($marginLeft + 150, $yIVA);
        $this->pdf->Cell(30, 4, 'Total IVA: ' . $t['ivaTotal'], 0, 0, 'R');
    }

    // ================================================================
    // UTILIDADES
    // ================================================================

    private function formatearTotales()
    {
        return [
            'total' => number_format($this->totales['sumaTotal'], 0, ",", "."),
            'total5' => number_format($this->totales['sumaTotal5'], 0, ",", "."),
            'total10' => number_format($this->totales['sumaTotal10'], 0, ",", "."),
            'exe' => number_format($this->totales['sumaTotalexe'], 0, ",", "."),
            'iva5' => number_format($this->totales['iva5'], 0, ",", "."),
            'iva10' => number_format($this->totales['iva10'], 0, ",", "."),
            'ivaTotal' => number_format(($this->totales['iva5'] + $this->totales['iva10']), 0, ",", ".")
        ];
    }

    private function generarLetras()
    {
        $letrasDecimal = "";
        return NumeroALetras::convertir($this->totales['sumaTotal']) . $letrasDecimal;
    }

    private function generarOutputPDF()
    {
        $this->pdf->Output('factura.pdf', 'I');
    }
}

// Ejecución
try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID no especificado');
    }

    $id = (int)$_GET['id'];
    $venta = new venta(); // Se asume que $this->venta existe en el contexto donde se hace el require, 
    // PERO al ser 'require_once' desde un controller, el contexto de 'this' puede perderse 
    // si no estamos dentro de un método de clase.
    // En el código original se usaba $this->venta. 
    // PAra asegurar, instanciamos si es necesario o usamos global.
    // Revisando el controller: $this->venta = new venta();

    // Como esto es un archivo incluido dentro de un método de controller (Factura()), '$this' se refiere al Controller.
    // El controller tiene la propiedad 'venta'.

    $generator = new FacturaGenerator($this->venta);
    $generator->generarFactura($id);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
// Garbage removed

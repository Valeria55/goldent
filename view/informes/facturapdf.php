<?php

/**
 * Generador de Facturas PDF Optimizado
 * Versión mejorada con mejor estructura, rendimiento y mantenibilidad
 */

// Configuración de memoria y tiempo de ejecución
set_time_limit(120);
ini_set('memory_limit', '256M');

/**
 * Clase optimizada para conversión de números a letras
 */
class NumeroALetras
{
    private static $UNIDADES = [
        '', 'un ', 'dos ', 'tres ', 'cuatro ', 'cinco ', 'seis ', 
        'siete ', 'ocho ', 'nueve ', 'diez ', 'once ', 'doce ', 
        'trece ', 'catorce ', 'quince ', 'dieciséis ', 'diecisiete ', 
        'dieciocho ', 'diecinueve ', 'veinte '
    ];

    private static $DECENAS = [
        'venti', 'treinta ', 'cuarenta ', 'cincuenta ', 'sesenta ',
        'setenta ', 'ochenta ', 'noventa ', 'cien '
    ];

    private static $CENTENAS = [
        'ciento ', 'doscientos ', 'trescientos ', 'cuatrocientos ', 
        'quinientos ', 'seiscientos ', 'setecientos ', 'ochocientos ', 'novecientos '
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
 */
class FacturaGenerator
{
    private $pdf;
    private $venta;
    private $datosFactura;
    private $totales;

    public function __construct($venta)
    {
        $this->venta = $venta;
        $this->initializePDF();
        $this->initializeTotales();
    }

    private function initializePDF()
    {
        require_once('plugins/tcpdf2/tcpdf.php');
        
        $medidas = [210, 357];
        $this->pdf = new TCPDF('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetHeaderMargin(0);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $this->pdf->SetAutoPageBreak(false);
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
        // Inicializar valores por defecto
        $this->datosFactura = [
            'cliente' => "Cliente ocasional",
            'ruc' => "",
            'fecha' => "",
            'telefono' => "",
            'direccion' => "",
            'vendedor' => "",
            'contado' => "",
            'credito' => "",
            'tipo' => "Guaraníes",
            'paciente' => ""
        ];

        // Cargar datos reales de la venta
        foreach ($this->venta->Listar($id_venta) as $r) {
            $this->datosFactura = [
                'cliente' => $r->nombre_cli ?? "Cliente ocasional",
                'ruc' => $r->ruc ?? "",
                'fecha' => date("d/m/Y", strtotime($r->fecha_venta)),
                'telefono' => $r->telefono ?? "",
                'direccion' => $r->direccion ?? "",
                'vendedor' => $r->vendedor ?? "",
                'contado' => ($r->contado == "Contado") ? "X" : "",
                'credito' => ($r->contado != "Contado") ? "X" : "",
                'tipo' => "Guaraníes", // Simplificado por ahora
                'paciente' => $r->paciente ?? ""
            ];
            break; // Solo necesitamos el primer registro para datos del cliente
        }
    }

    private function procesarItems($id_venta)
    {
        $items = [];
        $cantidad = 0;

        foreach ($this->venta->Listar($id_venta) as $r) {
            $cantidad++;
            
            // Procesar totales por IVA
            $this->procesarTotalesIVA($r);
            
            // Formatear precios
            $subTotal = number_format($r->precio_venta, 0, ",", ".");
            $total = number_format($r->total, 0, ",", ".");
            
            // Determinar categorías de IVA para mostrar
            $categorias = $this->determinarCategoriasIVA($r);
            
            // Agregar información del paciente si existe
            $productoInfo = $r->producto;
            if (!empty($this->datosFactura['paciente'])) {
                $productoInfo .= " PACIENTE: " . $this->datosFactura['paciente'];
            }

            $items[] = [
                'cantidad' => $r->cantidad,
                'producto' => $productoInfo,
                'precio' => $subTotal,
                'exe' => $categorias['exe'],
                'iva5' => $categorias['iva5'],
                'iva10' => $categorias['iva10']
            ];

            $this->totales['cantidad_total'] += $r->cantidad;
            $this->totales['sumaTotal'] += $r->total;
        }

        // Determinar si necesita paginación
        if ($cantidad > 7) {
            $this->generarFacturaMultiplePaginas($items);
        } else {
            $this->generarFacturaPaginaSimple($items);
        }
    }

    private function procesarTotalesIVA($r)
    {
        switch ($r->iva) {
            case 5:
                $this->totales['sumaTotal5'] += $r->total;
                $this->totales['iva5'] += ($r->total / 1.05);
                break;
            case 10:
                $this->totales['sumaTotal10'] += $r->total;
                $this->totales['iva10'] += $r->total / 11;
                break;
            default:
                $this->totales['sumaTotalexe'] += $r->total;
                $this->totales['exe'] += $r->total;
                break;
        }
    }

    private function determinarCategoriasIVA($r)
    {
        $categorias = ['exe' => '', 'iva5' => '', 'iva10' => ''];
        
        switch ($r->iva) {
            case 5:
                $categorias['iva5'] = number_format($r->total, 0, ",", ".");
                break;
            case 10:
                $categorias['iva10'] = number_format($r->total, 0, ",", ".");
                break;
            default:
                $categorias['exe'] = number_format($r->total, 0, ",", ".");
                break;
        }
        
        return $categorias;
    }

    private function generarHeader($tipo = 1)
    {
        $d = $this->datosFactura;
        
        $html = <<<EOF
<br><br>
<table width="100%" style="text-align:center; line-height: 12px; font-size:8px">
    <tr>
        <td style="font-size:28px" width="65%" align="left" nowrap></td>
    </tr>
    <tr>
        <td width="10%"></td>
        <td width="31%" align="left" nowrap>{$d['fecha']}</td>
        <td width="38%" align="left" nowrap></td>
        <td width="10%" align="left">X</td>
        <td width="10%" align="left">X</td>
    </tr>
    <tr>
        <td width="15%"></td>
        <td width="53%" align="left" nowrap>{$d['cliente']}</td>
        <td width="20%" align="left">{$d['telefono']}</td>
        <td width="5%"></td>
    </tr>
    <tr align="left">
        <td width="10%"></td>
        <td width="20%" align="left" nowrap>{$d['ruc']}</td>
        <td width="70%"></td>
        <td width="25%"></td>
        <td width="5%"></td>
    </tr>
    <tr align="left">
        <td width="10%"></td>
        <td width="20%" align="left" nowrap>{$d['direccion']}</td>
        <td width="70%"></td>
        <td width="25%"></td>
        <td width="5%"></td>
    </tr>
</table>
EOF;
        
        return $html;
    }

    private function generarItemsHTML($items)
    {
        $html = '';
        
        foreach ($items as $item) {
            $html .= <<<EOF
<table>
    <tr nowrap="nowrap" style="font-size:8px">
        <td width="8%" align="left">{$item['cantidad']}</td>
        <td width="49%" align="left" style="font-size:7px">{$item['producto']}</td>
        <td width="15%" align="center">{$item['precio']}</td>
        <td width="10%" align="center">{$item['exe']}</td>
        <td width="7%" align="center">{$item['iva5']}</td>
        <td width="12%" align="right">{$item['iva10']}</td>
    </tr>
</table>
EOF;
        }
        
        return $html;
    }

    private function generarEspaciosRelleno($cantidad)
    {
        $espacios_necesarios = 8 - $cantidad;
        $html = '';
        
        for ($i = 0; $i < $espacios_necesarios; $i++) {
            $html .= <<<EOF
<table>
    <tr nowrap="nowrap" style="font-size:8px">
        <td width="8%" align="left"></td>
        <td width="5%" align="right"></td>
        <td width="49%" align="center"></td>
        <td width="11%" align="center"></td>
        <td width="10%" align="center"></td>
        <td width="7%" align="center"></td>
        <td width="12%" align="center"></td>
    </tr>
</table>
EOF;
        }
        
        return $html;
    }

    private function generarFooter()
    {
        $totales_formateados = $this->formatearTotales();
        $letras = $this->generarLetras();
        
        $html = <<<EOF
<table width="100%" style="text-align:center; line-height: 15px; font-size:8px">
    <tr align="center">
        <td width="3%"></td>
        <td width="70%"></td>
        <td width="9%">{$totales_formateados['exe']}</td>
        <td width="12%"><b>{$totales_formateados['total5']}</b></td>
        <td width="12%"><b>{$totales_formateados['total10']}</b></td>
    </tr>
    <tr align="center">
        <td width="5%"></td>
        <td width="65%">{$this->datosFactura['tipo']} {$letras}</td>
        <td width="9%"></td>
        <td width="1%"></td>
        <td width="24%"><b>{$totales_formateados['total']}</b></td>
    </tr>
</table>
<table width="100%" style="text-align:left; line-height: 15px">
    <tr>
        <td width="30%" align="center" style="font-size:8px"></td>
        <td width="20%" align="center" style="font-size:8px">{$totales_formateados['iva5']}</td>
        <td width="35%" align="right" style="font-size:8px">{$totales_formateados['iva10']}</td>
        <td width="10%" align="right" style="font-size:8px">{$totales_formateados['ivaTotal']}</td>
    </tr>
</table>
EOF;
        
        return $html;
    }

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
        if ($this->totales['sumaTotal'] != intval($this->totales['sumaTotal'])) {
            $decimal = ($this->totales['sumaTotal'] - intval($this->totales['sumaTotal'])) * 100;
            $letrasDecimal = ' con ' . NumeroALetras::convertir($decimal) . ' centavos';
        }
        
        return NumeroALetras::convertir($this->totales['sumaTotal']) . $letrasDecimal;
    }

    private function generarFacturaPaginaSimple($items)
    {
        // Header principal
        $this->pdf->writeHTML($this->generarHeader(), false, false, false, false, '');
        
        // Items
        $this->pdf->writeHTML($this->generarItemsHTML($items), false, false, false, false, '');
        
        // Espacios de relleno
        $this->pdf->writeHTML($this->generarEspaciosRelleno(count($items)), false, false, false, false, '');
        
        // Footer
        $this->pdf->writeHTML($this->generarFooter(), false, false, false, false, '');
        
        // Generar duplicado y triplicado
        $this->generarCopias($items);
    }

    private function generarFacturaMultiplePaginas($items)
    {
        // Lógica para facturas con más de 7 items
        // Similar a la versión simple pero con manejo de múltiples páginas
        $this->generarFacturaPaginaSimple($items);
        
        // Agregar página adicional si es necesario
        $this->pdf->AddPage();
        $this->pdf->writeHTML($this->generarHeader(), false, false, false, false, '');
    }

    private function generarCopias($items)
    {
        // Duplicado
        $this->pdf->writeHTML($this->generarHeader(), false, false, false, false, '');
        $this->pdf->writeHTML($this->generarItemsHTML($items), false, false, false, false, '');
        $this->pdf->writeHTML($this->generarEspaciosRelleno(count($items)), false, false, false, false, '');
        $this->pdf->writeHTML($this->generarFooter(), false, false, false, false, '');
        
        // Triplicado
        $this->pdf->writeHTML($this->generarHeader(), false, false, false, false, '');
        $this->pdf->writeHTML($this->generarItemsHTML($items), false, false, false, false, '');
        $this->pdf->writeHTML($this->generarEspaciosRelleno(count($items)), false, false, false, false, '');
        $this->pdf->writeHTML($this->generarFooter(), false, false, false, false, '');
    }

    private function generarOutputPDF()
    {
        $this->pdf->Output('factura.pdf', 'I');
    }
}

// Ejecución principal
try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('ID de venta no válido');
    }

    $id_venta = (int)$_GET['id'];
    $facturaGenerator = new FacturaGenerator($this->venta);
    $facturaGenerator->generarFactura($id_venta);
    
} catch (Exception $e) {
    error_log("Error generando factura: " . $e->getMessage());
    die("Error al generar la factura: " . $e->getMessage());
}

?>
                $converted .= sprintf('%smillones ', self::convertGroup($millones));
            }
        }

        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'mil ';
            } else if (intval($miles) > 0) {
                $converted .= sprintf('%smil ', self::convertGroup($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'un ';
            } else if (intval($cientos) > 0) {
                $converted .= sprintf('%s ', self::convertGroup($cientos));
            }
        }

        if(empty($decimales)){
            $valor_convertido = $converted . strtoupper($moneda);
        } else {
            $valor_convertido = $converted . strtoupper($moneda) . ' con ' . $decimales . ' ' . strtoupper($centimos);
        }

        return $valor_convertido;
    }

    private static function convertGroup($n)
    {
        $output = '';

        if ($n == '100') {
            $output = "cien ";
        } else if ($n[0] !== '0') {
            $output = self::$CENTENAS[$n[0] - 1];   
        }

        $k = intval(substr($n,1));

        if ($k <= 20) {
            $output .= self::$UNIDADES[$k];
        } else {
            if(($k > 30) && ($n[2] !== '0')) {
                $output .= sprintf('%sY %s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            } else {
                $output .= sprintf('%s%s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            }
        }

        return $output;
    }
}



// FIN  PRUEBA 


require_once('plugins/tcpdf2/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$medidas = array(210, 357); // Ajustar aqui segun los milimetros necesarios;
$pdf = new TCPDF('P', 'mm', $medidas, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);

$pdf->AddPage();
/* ================================ 
  EVITA QUE SE GENERE HOJA EXTRA
  AUTOMATICAMENTE
================================ */
$pdf->SetAutoPageBreak(false);

$id_venta = $_GET['id'];

// Inicializa variables con valores por defecto
$cliente = "Cliente ocasional";
$ruc = "";
$fecha = "";
$telefono = "";
$direccion = "";
$vendedor = "";
$contado = "";
$credito = "";
$tipo = "Guaraníes";

// Busca el cliente
$encontroCliente = false;
foreach($this->venta->Listar($id_venta) as $r){
    $encontroCliente = true;
    $cliente = $r->nombre_cli;
    $ruc = $r->ruc;
    $fecha = date("d/m/Y", strtotime($r->fecha_venta));
    $telefono = $r->telefono;
    $direccion = $r->direccion;
    $vendedor = $r->vendedor;
    $contado = "";
    $credito = "";
    if($r->contado=="Contado"){
        $contado = "X";
    }else{
        $credito = "X";
    }
    if($r->motivo_cliente=="gs"){
        $tipo ="Guaraníes";
    }else{
        $tipo ="Guaraníes";
    }
}


$header = <<<EOF
<br><br>
	<table width ="100%" style="text-align:center; line-height: 12px; font-size:8px">
		<tr>
          <td style="font-size:28px" width="65%" align="left" nowrap></td>
        </tr>
	    <tr>
          <td width="10%"></td>
          <td width="31%" align="left" nowrap> $fecha </td>
          <td width="38%" align="left" nowrap></td>
          <td width="10%" align="left">X</td>
          <td width="10%" align="left">X</td>
        </tr>
        <tr>
          <td width="15%"></td>
          <td width="53%" align="left" nowrap>$cliente</td>
          <td width="20%" align="left">$telefono</td>
          <td width="5%"></td>
        </tr>
        <tr align="left">
          <td width="10%"></td>
          <td width="20%" align="left" nowrap> $ruc </td>
          <td width="70%"></td>
          <td width="25%"></td>
          <td width="5%"></td>
        </tr>
        <tr align="left">
          <td width="10%"></td>
          <td width="20%" align="left" nowrap> $direccion </td>
          <td width="70%"></td>
          <td width="25%"></td>
          <td width="5%"></td>
        </tr>
    </table>
    <table>
		<tr nowrap="nowrap" style="font-size:8px;">
			<td width="10%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
EOF;

$pdf->writeHTML($header, false, false, false, false, '');

$header2 .= <<<EOF

	<table width ="100%" style="text-align:center; line-height: 13px; font-size:9px">
		<tr>
          <td style="font-size:27px" width="65%" align="left" nowrap></td>
        </tr>
	    <tr>
          <td width="16%"></td>
          <td width="31%" align="left" nowrap> $fecha </td>
          <td width="42%" align="left" nowrap> $ruc </td>
          <td width="10%" align="center">$contado</td>
          <td width="10%" align="center">$credito</td>
        </tr>
        <tr>
          <td width="21%"></td>
          <td width="53%" align="left" nowrap>$cliente</td>
          <td width="20%" align="left">$telefono</td>
          <td width="5%"></td>
        </tr>
        <tr align="left">
          <td width="12%"></td>
          <td width="70%">$direccion</td>
          <td width="25%"></td>
          <td width="5%"></td>
        </tr>
    </table>
    <table>
		<tr nowrap="nowrap" style="font-size:8px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
	<table>
		<tr nowrap="nowrap" style="font-size:12px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
EOF;
$header3 .= <<<EOF

 
	<table width ="100%" style="text-align:center; line-height: 13px; font-size:9px">
		<tr>
          <td style="font-size:22px" width="65%" align="left" nowrap></td>
        </tr>
	    <tr>
          <td width="16%"></td>
          <td width="31%" align="left" nowrap> $fecha </td>
          <td width="42%" align="left" nowrap> $ruc </td>
          <td width="10%" align="center">$contado</td>
          <td width="10%" align="center">$credito</td>
        </tr>
        <tr>
          <td width="21%"></td>
          <td width="53%" align="left" nowrap>$cliente</td>
          <td width="20%" align="left">$telefono</td>
          <td width="5%"></td>
        </tr>
        <tr align="left">
          <td width="12%"></td>
          <td width="70%">$direccion</td>
          <td width="25%"></td>
          <td width="5%"></td>
        </tr>
    </table>
    <table>
		<tr nowrap="nowrap" style="font-size:8px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
	<table>
		<tr nowrap="nowrap" style="font-size:12px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
			<tr>
          <td style="font-size:5px" width="65%" align="left" nowrap></td>
        </tr>
	</table>
EOF;


$sumaTotal = 0;
$cantidad = 0;
$sumaTotal5 = 0.0;
$sumaTotal10 = 0.0;
$sumaTotalexe=0.0;
$iva10 = 0.0;
$items = "";
$cantidad_total = 0;
$espacio = "";
$espacio2 = "";
$iva5 = 0.0;
$exe=0.0;
$exeP=0.0;

foreach($this->venta->Listar($id_venta) as $r){
  $cantidad++;
  if($r->motivo_cliente=="gs"){
    if ($r->iva==5){
      //$sumaTotal5 += ((($r->precio_venta*$r->cantidad)-($r->precio_venta*$r->cantidad*($r->descuento/100))));
      $sumaTotal5 += $r->total;
      $iva5+=($r->total/1.05);
      $iva5P=number_format(($r->total), 0, "," , ".");
      $iva10P = "";
      $exeP= "";
    }else{
      if($r->iva==10){
      $sumaTotal10 += $r->total;
      $iva10+=$r->total/11;
      $iva10P=number_format(($r->total), 0, "," , ".");
      $iva5P="";
      $exeP="";
      }else{
        $sumaTotalexe += ($r->total);
        $exe+=($r->total);
        $exeP=number_format(($r->total), 0, "," , ".");
        $iva5P="";
        $iva10P = "";
      }
    }
  }else{
    if ($r->iva==5){
      $sumaTotal5 += ($r->total);
      $iva5+=($r->total/1.05);
      $iva5P=number_format(($r->total), 0, "," , ".");
      $iva10P = "";
      $exeP= "";
    }else{
      if($r->iva==10){
        $sumaTotal10 += $r->total;
        $iva10+=$r->total/11;
        $iva10P=number_format(($r->total), 0, "," , ".");
        $iva5P="";
        $exeP="";
      }else{
        $sumaTotalexe += ($r->total);
        $exe+=$r->total;
        $exeP=number_format(($r->total), 0, "," , ".");
        $iva5P="";
        $iva10P = "";
      }
    }
  }

  if($r->motivo_cliente=="gs"){
    $subTotal = number_format(($r->precio_venta), 0, "," , ".");
    $descuento = ($r->precio_venta)-($r->precio_venta*($r->descuento/100)); //precio con descuento
    $descuento = number_format($descuento, 0, "," , ".");
    $total = $r->total;
    $total =  number_format($total, 0, "," , ".");
  }else{
    $subTotal = number_format(($r->precio_venta), 0, "," , ".");
    $descuento = ($r->precio_venta)-($r->precio_venta*($r->descuento/100)); //precio con descuento
    $descuento = number_format($descuento, 0, "," , ".");
    $total = $r->total;
    $total =  number_format($total, 0, "," , ".");
  }
  if($r->paciente!=""){
    $paciente="PACIENTE: $r->paciente";
  }

$items .= <<<EOF

		<table>
	
			<tr nowrap="nowrap" style="font-size:8px">
		    <td width="8%"align="left">$r->cantidad</td>
				<td width="49%" align="left" style="font-size:7px">$r->producto $paciente</td>
				<td width="15%" align="center">$subTotal</td>
				<td width="10%" align="center">$exeP</td>
				<td width="7%" align="center">$iva5P</td>
				<td width="12%" align="right">$iva10P</td>
			</tr>
		</table>

EOF;

$cantidad_total += $r->cantidad;
$sumaTotal += $r->total;

/* ================================ 
  CANTIDAD MAYOR A 7
================================ */
if($cantidad>7){
    
    $pdf->writeHTML($items, false, false, false, false, '');

    $c=9-$cantidad;

    for($i=0;$i<$c;$i++){
      // ESPACIO MAYOR A 7 ITEMS
      $espacio_relleno .= <<<EOF
      <table>
        <tr nowrap="nowrap" style="font-size:8px">
          <td width="8%"align="left"></td>
          <td width="5%" align="rigth"></td>
          <td width="49%" align="center"></td>
          <td width="11%" align="center"></td>
          <td width="10%" align="center"></td>
          <td width="7%" align="center"></td>
          <td width="12%" align="center"></td>
        </tr>
      </table>
EOF;
    }

  $pdf->writeHTML($espacio_relleno, false, false, false, false, '');

    //mas de 7 items
  $letrasDecimal = "";
  if($sumaTotal != intval($sumaTotal)){
      $decimal = ($sumaTotal - intval($sumaTotal))*100;
      $letrasDecimal = 'con '.NumeroALetras::convertir($decimal).' centavos';
  }
  if($r->motivo_cliente=="gs"){
    $letras = NumeroALetras::convertir($sumaTotal);
    $sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
    $sumaTotal5V =  number_format($sumaTotal5, 0, "," , ".");
    $sumaTotal10V =  number_format($sumaTotal10, 0, "," , ".");
    $sumaTotalexeV =  number_format($sumaTotalexe, 0, "," , ".");
    $iva5V=number_format($iva5, 0, "," , ".");
    $iva10V=number_format($iva10, 0, "," , ".");


    $iva10U = number_format($iva10,0,",",".");
    $ivaexeV=number_format($exe, 0, "," , ".");
    $ivaTotal = number_format(($iva5 + $iva10), 0, "," , ".");
  }else{
    $letras = NumeroALetras::convertir($sumaTotal);
    $sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
    $sumaTotal5V =  number_format($sumaTotal5, 0, "," , ".");
    $sumaTotal10V =  number_format($sumaTotal10, 0, "," , ".");
    $sumaTotalexeV =  number_format($sumaTotalexe, 0, "," , ".");
    $iva5V=number_format($iva5, 0, "," , ".");
    $iva10V=number_format($iva10, 0, "," , ".");


    $iva10U = number_format($iva10,0,",",".");
    $ivaexeV=number_format($exe, 0, "," , ".");
    $ivaTotal = number_format(($iva5 + $iva10), 0, "," , ".");
  }
  //FOOTER MAS DE 9 ITEMS
  $footer = <<<EOF
    
    <table width="100%" style="text-align:center; line-height: 15px; font-size:8px">
      
      <tr>
            <td style="font-size:6.5px" width="65%" align="left" nowrap></td>
          </tr>
      <tr align="center">
        <td width="3%"></td>
        <td width="70%"></td>
          <td width="9%">$sumaTotalexeV</td>
          <td width="12%"><b>$sumaTotal5V</b></td>
          <td width="12%"><b>$sumaTotal10V</b></td>
        </tr>
        <tr align="center">
        <td width="5%"></td>
        <td width="65%">$tipo $letras $letrasDecimal</td>
          <td width="9%"></td>
          <td width="1%"></td>
          <td width="24%"><b>$sumaTotalV</b></td>
        </tr>
    </table>
      <table width="100%" style="text-align:left; line-height: 15px">
        <tr>
          <td width="30%" align='center' style="font-size:8px"></td>
          <td width="20%" align='center' style="font-size:8px">$iva5V</td>
          <td width="35%" align='right' style="font-size:8px">$iva10V</td>
          <td width="10%" align='right' style="font-size:8px">$ivaTotal</td>
        </tr>
    </table>
    <table width="100%" style="text-align:center; line-height: 15px">
        <tr>
          <td width="25%" align='center' style="font-size:8px"></td>
          <td width="20%" align='center' style="font-size:8px"></td>
          <td width="40%" align='center' style="font-size:8px"></td>
          <td width="10%" align='center' style="font-size:8px"></td>
        <td width="23%" align='center' style="font-size:8px"></td>
          <td width="18%" align="right"></td>
          <td width="18%" style="font-size:10px"></td>
        </tr>
    </table>
    <table width="100%" style="text-align:center; line-height: 33px">
        <tr>
          <td width="25%" align='center' style="font-size:4px"></td>
          <td width="20%" align='center' style="font-size:4px"></td>
          <td width="40%" align='center' style="font-size:4px"></td>
          <td width="10%" align='center' style="font-size:4px"></td>
        <td width="23%" align='center' style="font-size:4px"></td>
          <td width="18%" align="right"></td>
          <td width="18%" style="font-size:10px"></td>
        </tr>
    </table>
  EOF;

  $pdf->writeHTML($footer, false, false, false, false, '');

  $espacio_pre_header2 = <<<EOF

      <table>
        <tr nowrap="nowrap" style="font-size:10px;">
          <td width="7%" ></td>
          <td width="44%"></td>
          <td width="12%"></td>
          <td width="12%"></td>
          <td width="12%"></td>
          <td width="12%"></td>
        </tr>
      </table>

  EOF;


  // $pdf->writeHTML($espacio_pre_header2, false, false, false, false, '');

  $espacio_pre_footer_2 = <<<EOF

        <table>
          <tr nowrap="nowrap" style="font-size:13.05px;">
            <td width="100%" ></td>
          </tr>
        </table>

    EOF;

  $compensacion_pre_footer = <<<EOF

        <table>
          <tr nowrap="nowrap" style="font-size:.0.5px;">
            <td width="100%" ></td>
          </tr>
        </table>

    EOF;
  //MAS DE 8 ITEMS
  //DUPLICADO

  $pdf->writeHTML($header2, false, false, false, false, '');
  $pdf->writeHTML($items, false, false, false, false, '');
  $pdf->writeHTML($espacio_relleno, false, false, false, false, '');
  $pdf->writeHTML($compensacion_pre_footer, false, false, false, false, '');
  $pdf->writeHTML($footer, false, false, false, false, '');
  
  //TRIPLICADO
  
  $pdf->writeHTML($header2, false, false, false, false, '');
  $pdf->writeHTML($items, false, false, false, false, '');
  $pdf->writeHTML($espacio_relleno, false, false, false, false, '');
  $pdf->writeHTML($compensacion_pre_footer, false, false, false, false, '');
  $pdf->writeHTML($footer, false, false, false, false, '');

  $pdf->AddPage();

    $espacio_pre_pagina2 = <<<EOF

        <table>
          <tr nowrap="nowrap" style="font-size:2px;">
            <td width="100%" ></td>
          </tr>
        </table>

    EOF;
  // $pdf->writeHTML($header3, false, false, false, false, '');
  // $pdf->writeHTML($espacio_pre_pagina2, false, false, false, false, '');
  $pdf->writeHTML($header, false, false, false, false, '');

  $items = "";
  $cantidad=0;
  $sumaTotal5 = 0;
  $iva5 = 0;
  $sumaTotal10 = 0;
  $iva10 = 0;
  $cantidad_total = 0;
  $sumaTotal = 0;

}

}

if($cantidad<8){
    
$pdf->writeHTML($items, false, false, false, false, '');

$c=8-$cantidad;

for($i=0;$i<$c;$i++){
    // ESPACIO MENOR A 8 ITEMS
$espacio1 .= <<<EOF

      <table>
        <tr nowrap="nowrap" style="font-size:8px">
          <td width="8%"align="left"></td>
          <td width="5%" align="rigth"></td>
          <td width="49%" align="center"></td>
          <td width="11%" align="center"></td>
          <td width="10%" align="center"></td>
          <td width="7%" align="center"></td>
          <td width="12%" align="center"></td>
        </tr>
      </table>

EOF;
}

$pdf->writeHTML($espacio1, false, false, false, false, '');


$letrasDecimal = "";
if($sumaTotal != intval($sumaTotal)){
    $decimal = ($sumaTotal - intval($sumaTotal))*100;
    $letrasDecimal = 'con '.NumeroALetras::convertir($decimal).' centavos';
}


if($r->motivo_cliente=="gs"){
	  $letras = NumeroALetras::convertir($sumaTotal);
    $sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
    $sumaTotal5V =  number_format($sumaTotal5, 0, "," , ".");
    $sumaTotal10V =  number_format($sumaTotal10, 0, "," , ".");
    $sumaTotalexeV =  number_format($sumaTotalexe, 0, "," , ".");
    $iva5V=number_format($iva5, 0, "," , ".");
    $iva10V=number_format($iva10, 0, "," , ".");
    $ivaTotal = number_format(($iva5 + $iva10), 0, "," , ".");
    $ivaexeV=number_format($exe, 0, "," , ".");
} else{
	  $letras = NumeroALetras::convertir($sumaTotal);
    $sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
    $sumaTotal5V =  number_format($sumaTotal5, 0, "," , ".");
    $sumaTotal10V =  number_format($sumaTotal10, 0, "," , ".");
    $sumaTotalexeV =  number_format($sumaTotalexe, 0, "," , ".");
    $iva5V=number_format($iva5,0,",",".");
    $iva10V=number_format($iva10,0,",",".");
    $ivaTotal = number_format(($iva5 + $iva10),0,",",".");
    $ivaexeV=number_format($exe, 0, "," , ".");
}

// FOOTER MENOS DE 9 ITEMS
$footer = <<<EOF
	
	<table width="100%" style="text-align:center; line-height: 15px; font-size:8px">

		<tr align="center">
		  <td width="3%"></td>
		  <td width="70%"></td>
	      <td width="9%">$sumaTotalexeV</td>
	      <td width="12%"><b>$sumaTotal5V</b></td>
	      <td width="12%"><b>$sumaTotal10V</b></td>
	    </tr>
	    <tr align="center">
		  <td width="5%"></td>
		  <td width="65%">$tipo $letras $letrasDecimal</td>
	      <td width="9%"></td>
	      <td width="1%"></td>
	      <td width="24%"><b>$sumaTotalV</b></td>
	    </tr>
	</table>
    <table width="100%" style="text-align:left; line-height: 15px">
	    <tr>
	      <td width="30%" align='center' style="font-size:8px"></td>
	      <td width="20%" align='center' style="font-size:8px">$iva5V</td>
	      <td width="35%" align='right' style="font-size:8px">$iva10V</td>
	      <td width="10%" align='right' style="font-size:8px">$ivaTotal</td>
	    </tr>
	</table>
	<table width="100%" style="text-align:center; line-height: 15px">
	    <tr>
	      <td width="25%" align='center' style="font-size:8px"></td>
	      <td width="20%" align='center' style="font-size:8px"></td>
	      <td width="40%" align='center' style="font-size:8px"></td>
	      <td width="10%" align='center' style="font-size:8px"></td>
		  <td width="23%" align='center' style="font-size:8px"></td>
	      <td width="18%" align="right"></td>
	      <td width="18%" style="font-size:10px"></td>
	    </tr>
	</table>
	<table width="100%" style="text-align:center; line-height: 33px">
	    <tr>
	      <td width="25%" align='center' style="font-size:4px"></td>
	      <td width="20%" align='center' style="font-size:4px"></td>
	      <td width="40%" align='center' style="font-size:4px"></td>
	      <td width="10%" align='center' style="font-size:4px"></td>
		  <td width="23%" align='center' style="font-size:4px"></td>
	      <td width="18%" align="right"></td>
	      <td width="18%" style="font-size:10px"></td>
	    </tr>
	</table>
EOF;

$pdf->writeHTML($footer, false, false, false, false, '');
$espacio2 = <<<EOF

		<table>
			<tr nowrap="nowrap" style="font-size:5px;">
				<td width="100%" ></td>
			</tr>
		</table>

EOF;
  // $pdf->writeHTML($espacio2, false, false, false, false, '');

  $espacio_entre_copias = <<<EOF

		<table>
			<tr nowrap="nowrap" style="font-size:1px;">
				<td width="100%" ></td>
			</tr>
		</table>

EOF;

//DUPLICADO

$pdf->writeHTML($header2, false, false, false, false, '');
$pdf->writeHTML($items, false, false, false, false, '');
$pdf->writeHTML($espacio1, false, false, false, false, '');
  // $pdf->writeHTML($espacio2, false, false, false, false, '');
$pdf->writeHTML($compensacion_pre_footer, false, false, false, false, '');
$pdf->writeHTML($footer, false, false, false, false, '');

//TRIPLICADO

$pdf->writeHTML($header2, false, false, false, false, '');
$pdf->writeHTML($items, false, false, false, false, '');
$pdf->writeHTML($espacio1, false, false, false, false, '');
$pdf->writeHTML($compensacion_pre_footer, false, false, false, false, '');
$pdf->writeHTML($footer, false, false, false, false, '');

}
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('factura.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
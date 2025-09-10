<?php

require_once('plugins/tcpdf2/tcpdf.php');

$medidas = array(80, 250); // Ajustar aqui segun los milimetros necesarios;
$pdf = new TCPDF('P', 'mm', $medidas, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetDefaultMonospacedFont('courier');
$pdf->SetMargins(7, 0, 7, true);
$pdf->SetAutoPageBreak(TRUE, 0);

$pdf->AddPage();

$id_venta = $_GET['id'];

// Obtener datos básicos de la venta
$datosVenta = null;
foreach ($this->venta->Listar($id_venta) as $r) {
    if (!$datosVenta) {
        $datosVenta = $r;
        break;
    }
}

// Obtener ingresos por moneda
$ingresosPorMoneda = $this->ingreso->ObtenerIngresosPorMoneda($id_venta);

// Obtener cotizaciones utilizadas de la tabla ventas
$cotizacionesUsadas = $this->ingreso->ObtenerCotizacionesUsadas($id_venta);

$html1 = <<<EOF
<br><br>
    <table width ="100%" style="text-align:center; line-height: 12px; font-size:8px">
        <tr>
            <td style="vertical-align: middle;"><img src="assets/img/SCORECARLOGO.png" width="120"></td>
        </tr>
        <tr>
            <td><b>Ticket N°:</b> $id_venta</td>    
        </tr>
        <tr>
            <td align="left"><b>Fecha:</b> {$datosVenta->fecha_venta}</td>
        </tr>
        <tr align="left">
            <td><b>RUC/CI:</b> {$datosVenta->ruc}</td>
        </tr>
        <tr align="left">
            <td><b>Cliente:</b> {$datosVenta->nombre_cli}</td>
        </tr>
    </table>
    
    <table>
        <tr><td>---------------------------------------------</td></tr>
    </table>
    
    <table width ="100%" style="text-align:center; line-height: 15px; font-size:7px">
        <tr align="center">
            <td width="45%"><b>Descripción</b></td>
            <td width="15%"><b>Cant</b></td>
            <td width="20%"><b>P. Unit</b></td>
            <td width="20%"><b>Total</b></td>
        </tr>
    </table>
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

// Variables para totales
$sumaTotal = 0;
$totalItems = 0;
$totalDescuentos = 0;

// Mostrar productos
foreach ($this->venta->Listar($id_venta) as $r) {
    $totalItems++;

    $precio_unitario = number_format($r->precio_venta, 0, ",", ".");
    $total_producto = ($r->precio_venta * $r->cantidad);
    $descuento_producto = ($total_producto * ($r->descuentov / 100));
    $total_con_descuento = $total_producto - $descuento_producto;

    // Cambiar para mostrar el total SIN descuento en la lista
    $total_formateado = number_format($total_producto, 0, ",", ".");

    // No cortar el nombre del producto, permitir que se ajuste
    $nombre_producto = $r->producto;

    $html_producto = <<<EOF
    <table width="100%" style="text-align:center; line-height: 8px; font-size:6px">
        <tr>
            <td width="45%" style="text-align: left; font-size:5px; word-wrap: break-word; vertical-align: top;">$nombre_producto</td>
            <td width="15%" style="text-align: center; vertical-align: top;">$r->cantidad</td>
            <td width="20%" style="text-align: right; vertical-align: top;">$precio_unitario</td>
            <td width="20%" style="text-align: right; vertical-align: top;"><b>$total_formateado</b></td>
        </tr>
    </table>
EOF;

    $pdf->writeHTML($html_producto, false, false, false, false, '');

    $sumaTotal += $total_producto;
    $sumaTotalConDescuento += $total_con_descuento;
    $totalDescuentos += $descuento_producto;
}

// Formatear totales
$sumaTotalFormateado = number_format($sumaTotal, 0, ",", ".");
$totalDescuentosFormateado = number_format($totalDescuentos, 0, ",", ".");

// Calcular porcentaje total de descuento
$porcentajeDescuento = 0;
if ($sumaTotal > 0) {
    $porcentajeDescuento = ($totalDescuentos / $sumaTotal) * 100;
}

$html_totales = <<<EOF
    <table>
        <tr><td>---------------------------------------------</td></tr>
    </table>
    
    <table width="100%" style="text-align:center; line-height: 6px; font-size:7px">
        <tr>
            <td width="30%">Items: $totalItems</td>
            <td width="40%" align="right" style="font-size:6px">TOTAL:</td>
            <td width="30%"><b>$sumaTotalFormateado Gs.</b></td>
        </tr>
EOF;

// Siempre mostrar la línea de descuento (incluso si es 0%)
$porcentajeFormateado = number_format($porcentajeDescuento, 1);
$html_totales .= <<<EOF
        <tr>
            <td width="30%"></td>
            <td width="40%" align="right" style="font-size:6px">DESC ($porcentajeFormateado%):</td>
            <td width="30%"><b>- $totalDescuentosFormateado Gs.</b></td>
        </tr>
EOF;

// Agregar línea de NETO (total final con descuento aplicado)
$totalNeto = $sumaTotal - $totalDescuentos;
$totalNetoFormateado = number_format($totalNeto, 0, ",", ".");

$html_totales .= <<<EOF
        <tr>
            <td width="30%"></td>
            <td width="40%" align="right" style="font-size:6px"><b>NETO:</b></td>
            <td width="30%"><b>$totalNetoFormateado Gs.</b></td>
        </tr>
    </table>
    
    <table>
        <tr><td>---------------------------------------------</td></tr>
    </table>
EOF;

// Solo mostrar la sección "PAGOS POR MONEDA" si hay más de una moneda
if (!empty($ingresosPorMoneda) && count($ingresosPorMoneda) > 1) {
    $html_totales .= <<<EOF
    
    <table width="100%" style="text-align:center; line-height: 6px; font-size:7px">
        <tr>
            <td width="100%" style="font-size:8px"><b>PAGOS POR MONEDA</b></td>
        </tr>
    </table>
    <br>
EOF;
}

$pdf->writeHTML($html_totales, false, false, false, false, '');

// Mostrar pagos por moneda solo si hay más de una moneda
if (!empty($ingresosPorMoneda) && count($ingresosPorMoneda) > 1) {
    foreach ($ingresosPorMoneda as $ingreso) {
        $monto_formateado = number_format($ingreso->total_monto, 0, ",", ".");
        $moneda_nombre = '';
        $cotizacion_texto = '';

        switch ($ingreso->moneda) {
            case 'PYG':
            case 'Gs':
            case 'GS':
            case null:
            case '':
                $moneda_nombre = 'Guaraníes';
                break;
            case 'USD':
            case '$':
            case 'Dolares':
                $moneda_nombre = 'Dólares';
                if ($cotizacionesUsadas && $cotizacionesUsadas->cot_usd && $cotizacionesUsadas->cot_usd > 0) {
                    $cotizacion_formateada = number_format($cotizacionesUsadas->cot_usd, 0, ",", ".");
                    $cotizacion_texto = "Cotización: $cotizacion_formateada";
                }
                break;
            case 'ARS':
            case 'Pesos':
                $moneda_nombre = 'Pesos Argentinos';
                break;
            case 'BRL':
            case 'Reales':
            case 'RS':  // Agregar este caso que faltaba
                $moneda_nombre = 'Reales';
                if ($cotizacionesUsadas && $cotizacionesUsadas->cot_rs && $cotizacionesUsadas->cot_rs > 0) {
                    $cotizacion_formateada = number_format($cotizacionesUsadas->cot_rs, 0, ",", ".");
                    $cotizacion_texto = "Cotización: $cotizacion_formateada";
                }
                break;
            default:
                $moneda_nombre = $ingreso->moneda;
        }

        $html_pago = <<<EOF
        <table width="100%" style="text-align:center; line-height: 10px; font-size:6px">
            <tr>
                <td width="60%" align="left" style="font-size:6px">$moneda_nombre: $monto_formateado</td>
                <td width="40%" align="right" style="font-size:6px">$cotizacion_texto</td>
            </tr>
        </table>
EOF;

        $pdf->writeHTML($html_pago, false, false, false, false, '');
    }
} else {
    // Si no hay datos de pago, mostrar como guaraníes por defecto con el NETO
    $html_pago_default = <<<EOF
    <table width="100%" style="text-align:center; line-height: 5px; font-size:6px">
        <tr>
            <td width="100%" align="left" style="font-size:6px">Guaraníes: $totalNetoFormateado</td>
        </tr>
    </table>
EOF;

    $pdf->writeHTML($html_pago_default, false, false, false, false, '');
}

$html_final = <<<EOF
    <br><br>
    <table width="100%" style="text-align:center; line-height: 8px; font-size:7px">
        <tr>
            <td>Gracias por su compra</td>
        </tr>
    </table>
    <br><br>
EOF;

$pdf->writeHTML($html_final, false, false, false, false, '');

// Output PDF
$pdf->Output('ticket.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

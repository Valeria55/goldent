<?php
// Establecer límite de tiempo de ejecución más alto
set_time_limit(300); // 5 minutos máximo
ini_set('memory_limit', '512M'); // Incrementar límite de memoria
ini_set('max_execution_time', 300);

require_once('plugins/tcpdf2/tcpdf.php');

$id_cierre = $_GET['id_cierre'];
$cierre = $this->model->Obtener($id_cierre);

// Verificar si el cierre existe
if (!$cierre) {
    die("Error: Cierre no encontrado");
}

$desde = $cierre->fecha_apertura;
$desdeV = date("d/m/Y H:i", strtotime($desde));
$desdeHora = date("H:i", strtotime($desde));
$hasta = $cierre->fecha_cierre;
$hastaV = date("d/m/Y H:i", strtotime($hasta));
$id_usuario = $cierre->id_usuario;


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$fechahoy = date("d/m/Y");
$horahoy = date("H:i");

$inicial = number_format($cierre->monto_apertura, 0, ",", ".");
$caja_inicial = $cierre->monto_apertura;
$real = number_format($cierre->cot_real, 0, ",", ".");
$dolar = number_format($cierre->cot_dolar, 0, ",", ".");
$espacio = "<h1>&nbsp;</h1>";
$html1 = <<<EOF
        $espacio
		<h1 align="center">Cierre nro $id_cierre</h1>
		<p>Desde $desdeV hasta $hastaV Generado a las $horahoy de la fecha $fechahoy</p>
		<div>
		<table width="100%">
		<tr>
		<td>
	
		</td>
		<td>
		
		</td>
		</tr>
		</table>
		</div>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$estilos = <<<EOF
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        .right {
            text-align: right;
        }
        .left{
            text-align: left;
        }
    </style>
EOF;

// Función para generar HTML de movimientos
function movimientos($movimientos_data, $metodo, $apertura = 0)
{
    // Si no hay movimientos, no generar nada
    if (empty($movimientos_data)) {
        return "";
    }

    // Para efectivo, usar el saldo total convertido que viene en los datos
    if (strtolower($metodo) === 'efectivo' && !empty($movimientos_data)) {
        $primer_movimiento = $movimientos_data[0];
        $saldo = isset($primer_movimiento->monto_apertura) ? $primer_movimiento->monto_apertura : $apertura;
    } else {
        $saldo = $apertura;
    }

    $sumaIngreso = 0;
    $sumaEgreso = 0;
    $saldoV = number_format($saldo, 0, ',', '.');

    // Obtener datos del primer movimiento para información del cierre
    $primer_movimiento = $movimientos_data[0];
    $desdeHora = isset($primer_movimiento->fecha_apertura) ? date('H:i', strtotime($primer_movimiento->fecha_apertura)) : '';

    // Determinar el texto de apertura según el método
    $texto_apertura = (strtolower($metodo) === 'efectivo') ? 'Apertura Total (Convertido a Gs.)' : 'Apertura';

    $aperturaHtml = ($saldo > 0) ? <<<EOF
        <tr>
            <td width="8%">$desdeHora</td>
            <td width="10%">Todas las cajas</td>
            <td width="30%">$texto_apertura</td>
            <td width="10%">-</td>
            <td width="13%" align="right">$saldoV</td>
            <td width="13%" align="right"></td>
            <td width="16%" align="right">$saldoV</td>
        </tr>
    EOF : "";

    $estilos = <<<EOF
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                font-size: 11px;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
                border: 1px solid #ddd;
                padding: 6px;
                text-align: left;
            }
            td {
                border: 1px solid #ddd;
                padding: 6px;
            }
            .right {
                text-align: right;
            }
            .left{
                text-align: left;
            }
        </style>
    EOF;

    $titulo_metodo = (strtolower($metodo) === 'efectivo') ? 'Movimientos de Efectivo (Convertido a Guaraníes)' : "Movimientos de $metodo";

    $html = <<<EOF
    $estilos
    <h3>$titulo_metodo</h3>
    <table>
        <thead>
            <tr>
                <th width="8%">Hora</th>
                <th width="10%">Caja</th>
                <th width="30%">Concepto</th>
                <th width="10%">ID</th>
                <th width="13%">Ingreso</th>
                <th width="13%">Egreso</th>
                <th width="16%">Saldo</th>
            </tr>
        </thead>
        <tbody>
        $aperturaHtml
    EOF;

    foreach ($movimientos_data as $mov) {
        $fecha = date('H:i', strtotime($mov->fecha));
        $concepto = htmlspecialchars($mov->concepto);
        $nombre_caja = htmlspecialchars($mov->nombre_caja);

        // Agregar información de moneda original si es efectivo convertido
        if (strtolower($metodo) === 'efectivo' && isset($mov->moneda) && !empty($mov->moneda) && $mov->moneda !== 'GS' && $mov->moneda !== 'Guaranies') {
            $concepto .= " (" . $mov->moneda . ")";
        }

        // Manejar ingresos y egresos
        $ingreso_valor = isset($mov->ingreso) ? $mov->ingreso : 0;
        $egreso_valor = isset($mov->egreso) ? $mov->egreso : 0;

        $ingreso = $ingreso_valor > 0 ? number_format($ingreso_valor, 0, ',', '.') : '';
        $egreso = $egreso_valor > 0 ? number_format($egreso_valor, 0, ',', '.') : '';

        // Determinar el ID a mostrar (id_venta para ingresos, id_compra para egresos)
        $id_transaccion = '-';
        if ($ingreso_valor > 0 && isset($mov->id_venta) && !empty($mov->id_venta)) {
            $id_transaccion = $mov->id_venta;
        } elseif ($egreso_valor > 0 && isset($mov->id_compra) && !empty($mov->id_compra)) {
            $id_transaccion = $mov->id_compra;
        }

        $sumaIngreso += $ingreso_valor;
        $sumaEgreso += $egreso_valor;
        $saldo += $ingreso_valor - $egreso_valor;
        $saldoV = number_format($saldo, 0, ',', '.');

        $html .= <<<EOF
            <tr>
                <td width="8%">$fecha</td>
                <td width="10%">$nombre_caja</td>
                <td width="30%">$concepto</td>
                <td width="10%">$id_transaccion</td>
                <td width="13%" align="right">$ingreso</td>
                <td width="13%" align="right">$egreso</td>
                <td width="16%" align="right">$saldoV</td>
            </tr>
        EOF;
    }

    $sumaIngresoV = number_format($sumaIngreso, 0, ',', '.');
    $sumaEgresoV = number_format($sumaEgreso, 0, ',', '.');

    $html .= <<<EOF
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right"><strong>Total:</strong></td>
                <td align="right"><strong>$sumaIngresoV</strong></td>
                <td align="right"><strong>$sumaEgresoV</strong></td>
                <td align="right"><strong>$saldoV</strong></td>
            </tr>
        </tfoot>
    </table>
    EOF;

    // Se eliminó completamente la sección de resumen individual
    return $html;
}

// Función para formatear números con decimales solo cuando es necesario
function formatearNumero($numero, $decimales_max)
{
    // Si es entero o no tiene decimales significativos, mostrar sin decimales
    if ($numero == intval($numero)) {
        return number_format($numero, 0, ',', '.');
    }

    // Si tiene decimales, formatear con el número máximo de decimales
    $formateado = number_format($numero, $decimales_max, ',', '.');

    // Remover ceros innecesarios al final
    if ($decimales_max > 0) {
        $formateado = rtrim($formateado, '0');
        $formateado = rtrim($formateado, ',');
    }

    return $formateado;
}

// Función específica para generar HTML de movimientos por moneda
function movimientosPorMoneda($movimientos_data, $metodo, $moneda, $apertura = 0, $mostrarResumen = true, $cierre_tipeado_moneda = null)
{
    // Si no hay movimientos y no hay apertura, no generar nada
    if (empty($movimientos_data) && $apertura == 0) {
        return "";
    }

    $saldo = $apertura;
    $sumaIngreso = 0;
    $sumaEgreso = 0;

    // Determinar el formato de número según la moneda
    $decimales = 0;
    if ($moneda === 'USD' || $moneda === 'RS') {
        $decimales = 3; // Máximo 3 decimales para USD y RS
    }

    $saldoV = formatearNumero($saldo, $decimales);

    // Obtener datos del primer movimiento para información del cierre
    $primer_movimiento = !empty($movimientos_data) ? $movimientos_data[0] : null;
    $desdeHora = ($primer_movimiento && isset($primer_movimiento->fecha_apertura)) ? date('H:i', strtotime($primer_movimiento->fecha_apertura)) : '';

    // Determinar el símbolo de la moneda
    $simbolo_moneda = '';
    switch ($moneda) {
        case 'GS':
        case 'Guaranies':
            $simbolo_moneda = 'Gs.';
            break;
        case 'RS':
        case 'Reales':
            $simbolo_moneda = 'R$';
            break;
        case 'USD':
        case 'Dolares':
            $simbolo_moneda = 'US$';
            break;
    }

    $aperturaHtml = ($apertura != 0) ? <<<EOF
        <tr>
            <td width="8%">$desdeHora</td>
            <td width="10%">Caja Principal</td>
            <td width="30%">Apertura - $moneda</td>
            <td width="10%">-</td>
            <td width="13%" align="right">$saldoV</td>
            <td width="13%" align="right"></td>
            <td width="16%" align="right">$saldoV</td>
        </tr>
    EOF : "";

    $estilos = <<<EOF
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                font-size: 11px;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
                border: 1px solid #ddd;
                padding: 6px;
                text-align: left;
            }
            td {
                border: 1px solid #ddd;
                padding: 6px;
            }
            .right {
                text-align: right;
            }
            .left{
                text-align: left;
            }
        </style>
    EOF;

    $html = <<<EOF
    $estilos
    <h3>$metodo - $moneda ($simbolo_moneda)</h3>
    <table>
        <thead>
            <tr>
                <th width="8%">Hora</th>
                <th width="10%">Caja</th>
                <th width="30%">Concepto</th>
                <th width="10%">ID</th>
                <th width="13%">Ingreso</th>
                <th width="13%">Egreso</th>
                <th width="16%">Saldo</th>
            </tr>
        </thead>
        <tbody>
        $aperturaHtml
    EOF;

    if (!empty($movimientos_data)) {
        foreach ($movimientos_data as $mov) {
            $fecha = date('H:i', strtotime($mov->fecha));
            $concepto = htmlspecialchars($mov->concepto);
            $nombre_caja = htmlspecialchars($mov->nombre_caja);

            // Manejar ingresos y egresos
            $ingreso_valor = isset($mov->ingreso) ? $mov->ingreso : 0;
            $egreso_valor = isset($mov->egreso) ? $mov->egreso : 0;

            // Formatear con decimales apropiados según la moneda
            $ingreso = $ingreso_valor > 0 ? formatearNumero($ingreso_valor, $decimales) : '';
            $egreso = $egreso_valor > 0 ? formatearNumero($egreso_valor, $decimales) : '';

            // Determinar el ID a mostrar (id_venta para ingresos, id_compra para egresos)
            $id_transaccion = '-';
            if ($ingreso_valor > 0 && isset($mov->id_venta) && !empty($mov->id_venta)) {
                $id_transaccion = $mov->id_venta;
            } elseif ($egreso_valor > 0 && isset($mov->id_compra) && !empty($mov->id_compra)) {
                $id_transaccion = $mov->id_compra;
            }

            $sumaIngreso += $ingreso_valor;
            $sumaEgreso += $egreso_valor;
            $saldo += $ingreso_valor - $egreso_valor;
            $saldoV = formatearNumero($saldo, $decimales);

            $html .= <<<EOF
                <tr>
                    <td width="8%">$fecha</td>
                    <td width="10%">$nombre_caja</td>
                    <td width="30%">$concepto</td>
                    <td width="10%">$id_transaccion</td>
                    <td width="13%" align="right">$ingreso</td>
                    <td width="13%" align="right">$egreso</td>
                    <td width="16%" align="right">$saldoV</td>
                </tr>
            EOF;
        }
    }

    $sumaIngresoV = formatearNumero($sumaIngreso, $decimales);
    $sumaEgresoV = formatearNumero($sumaEgreso, $decimales);

    $html .= <<<EOF
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right"><strong>Total:</strong></td>
                <td align="right"><strong>$sumaIngresoV</strong></td>
                <td align="right"><strong>$sumaEgresoV</strong></td>
                <td align="right"><strong>$saldoV</strong></td>
            </tr>
        </tfoot>
    </table>
    EOF;

    // Elimina el resumen individual si $mostrarResumen es false
    if ($mostrarResumen) {
        // Usar el valor de cierre específico por moneda si se proporciona
        $cierre_tipeado = $cierre_tipeado_moneda !== null ? $cierre_tipeado_moneda : $saldo;
        $cierre_tipeadoV = formatearNumero($cierre_tipeado, $decimales);
        $diferencia = formatearNumero(($cierre_tipeado - $saldo), $decimales);

        $resumen = <<<EOF
            <h4 align="center">Resumen - $moneda ($simbolo_moneda)</h4>
            <table>
                <tr>
                    <th width="20%">Entrada de caja</th>
                    <th width="20%">Salida de caja</th>
                    <th width="20%">Total sistema</th>
                    <th width="20%">Total tipeado</th>
                    <th width="20%">Diferencia</th>
                </tr>
                <tr align="right">
                    <td width="20%">$sumaIngresoV</td>
                    <td width="20%">$sumaEgresoV</td>
                    <td width="20%">$saldoV</td>
                    <td width="20%">$cierre_tipeadoV</td>
                    <td width="20%">$diferencia</td>
                </tr>
            </table>
        EOF;
        $html .= $resumen;
    }
    return $html;
}

// Agregar las nuevas secciones de Caja Principal por moneda
$monedas = ['GS', 'RS', 'USD']; // Las tres monedas a procesar
$montos_apertura = [
    'GS' => $caja_inicial,
    'RS' => $cierre->apertura_rs ?? 0,
    'USD' => $cierre->apertura_usd ?? 0
];

// Definir montos de cierre por moneda
$montos_cierre = [
    'GS' => $cierre->monto_cierre ?? 0,
    'RS' => $cierre->monto_cierre_rs ?? 0,
    'USD' => $cierre->monto_cierre_usd ?? 0
];

// 1. Guardar los datos de resumen de cada moneda para mostrar al final
$resumenes_monedas = [];

foreach ($monedas as $moneda) {
    $metodo_caja_principal = 'Caja Principal';
    try {
        error_log("Iniciando carga de movimientos de Caja Principal para cierre $id_cierre - Moneda: $moneda");
        $movimientos_caja_principal = $this->model->ListarMovimientosCajaPrincipalPorMoneda($id_cierre, $moneda);
        error_log("Movimientos de Caja Principal cargados para $moneda: " . count($movimientos_caja_principal));

        $apertura_moneda = $montos_apertura[$moneda];

        // Solo generar el reporte si hay movimientos o apertura diferente de cero
        if (!empty($movimientos_caja_principal) || $apertura_moneda != 0) {
            // --- NUEVO: Calcular resumen y guardar ---
            $sumaIngreso = 0;
            $sumaEgreso = 0;
            $saldo = $apertura_moneda;
            $primer_movimiento = !empty($movimientos_caja_principal) ? $movimientos_caja_principal[0] : null;
            foreach ($movimientos_caja_principal as $mov) {
                $ingreso_valor = isset($mov->ingreso) ? $mov->ingreso : 0;
                $egreso_valor = isset($mov->egreso) ? $mov->egreso : 0;
                $sumaIngreso += $ingreso_valor;
                $sumaEgreso += $egreso_valor;
                $saldo += $ingreso_valor - $egreso_valor;
            }
            // Usar el valor de cierre específico por moneda
            $cierre_tipeado = $montos_cierre[$moneda];
            $resumenes_monedas[$moneda] = [
                'sumaIngreso' => $sumaIngreso,
                'sumaEgreso' => $sumaEgreso,
                'saldo' => $saldo,
                'cierre_tipeado' => $cierre_tipeado,
                'diferencia' => $cierre_tipeado - $saldo
            ];
            // --- FIN NUEVO ---

            // Mostrar solo la tabla, sin resumen debajo
            $caja_principal_html = movimientosPorMoneda($movimientos_caja_principal, $metodo_caja_principal, $moneda, $apertura_moneda, false, $montos_cierre[$moneda]);
            if (!empty($caja_principal_html)) {
                $pdf->writeHTML($caja_principal_html, false, false, false, false, '');
                $pdf->writeHTML($espacio, false, false, false, false, '');
                error_log("HTML de Caja Principal para $moneda generado exitosamente");
            }
        } else {
            error_log("No hay movimientos ni apertura para $moneda - Saltando generación");
        }
    } catch (Exception $e) {
        error_log("Error en movimientos Caja Principal para $moneda: " . $e->getMessage());
    }
}

// 2. Mostrar resumen general de monedas después de las tablas
if (!empty($resumenes_monedas)) {
    // Primero mostrar las cotizaciones utilizadas
    $cotizaciones_html = <<<EOF
        <style>
            .tabla-cotizaciones {
                border-collapse: collapse;
                width: 100%;
                margin: 10px 0 20px 0;
                font-size: 11px;
                background-color: #f8f9fa;
            }
            .tabla-cotizaciones td {
                border: 1px solid #dee2e6;
                padding: 8px;
                text-align: center;
                font-weight: bold;
                color: #495057;
            }
            .titulo-cotizaciones {
                color: #6c3483;
                font-size: 12px;
                font-weight: bold;
                text-align: center;
                margin: 15px 0 5px 0;
                padding: 5px;
                background-color: #f4f1f8;
                border: 1px solid #d5c4e8;
                border-radius: 3px;
            }
        </style>
        
        <div class="titulo-cotizaciones">COTIZACIONES UTILIZADAS: R$ = {$real} Gs. | US$ = {$dolar} Gs. | Gs. = 1 (Base)</div>
    EOF;

    $simbolos = [
        'GS' => 'Gs.',
        'RS' => 'R$',
        'USD' => 'US$'
    ];

    $resumen_html = <<<EOF
        <style>
            .tabla-resumen {
                border-collapse: collapse;
                width: 100%;
                font-size: 12px;
                margin: 15px 0;
            }
            .tabla-resumen th {
                background-color: #34495e;
                color: white;
                font-weight: bold;
                border: 2px solid #2c3e50;
                padding: 12px 8px;
                text-align: center;
                font-size: 11px;
            }
            .tabla-resumen .th-moneda {
                background-color: #2980b9;
                border: 2px solid #1f5f8b;
            }
            .tabla-resumen td {
                border: 1px solid #bdc3c7;
                padding: 10px 8px;
                text-align: right;
            }
            .tabla-resumen .td-moneda {
                background-color: #ecf0f1;
                font-weight: bold;
                color: #2c3e50;
                text-align: center;
                border: 2px solid #bdc3c7;
                border-right: 3px solid #2980b9;
            }
            .tabla-resumen tr:nth-child(even) td:not(.td-moneda) {
                background-color: #f8f9fa;
            }
            .tabla-resumen tr:hover td {
                background-color: #e8f4f8;
            }
            .titulo-resumen {
                color: #2c3e50;
                font-size: 16px;
                font-weight: bold;
                text-align: center;
                margin: 25px 0 15px 0;
                padding: 10px;
                background-color: #ecf0f1;
                border: 2px solid #bdc3c7;
                border-radius: 5px;
            }
        </style>
        
        <div class="titulo-resumen">RESUMEN FINAL POR MONEDA</div>
        
        <table class="tabla-resumen">
            <thead>
                <tr>
                    <th class="th-moneda" width="18%">MONEDA</th>
                    <th width="16%">ENTRADA<br>DE CAJA</th>
                    <th width="16%">SALIDA<br>DE CAJA</th>
                    <th width="17%">TOTAL<br>SISTEMA</th>
                    <th width="17%">TOTAL<br>TIPEADO</th>
                    <th width="16%">DIFERENCIA</th>
                </tr>
            </thead>
            <tbody>
    EOF;

    foreach ($monedas as $moneda) {
        if (!isset($resumenes_monedas[$moneda])) continue;
        $simbolo = $simbolos[$moneda];
        $r = $resumenes_monedas[$moneda];

        // Determinar decimales según la moneda
        $decimales = ($moneda === 'USD' || $moneda === 'RS') ? 3 : 0;

        $sumaIngresoV = formatearNumero($r['sumaIngreso'], $decimales);
        $sumaEgresoV = formatearNumero($r['sumaEgreso'], $decimales);
        $saldoV = formatearNumero($r['saldo'], $decimales);
        $cierre_tipeadoV = formatearNumero($r['cierre_tipeado'], $decimales);
        $diferenciaV = formatearNumero($r['diferencia'], $decimales);

        // Determinar color de la diferencia
        $color_diferencia = '';
        if ($r['diferencia'] > 0) {
            $color_diferencia = 'color: #27ae60; font-weight: bold;'; // Verde para positivo
        } elseif ($r['diferencia'] < 0) {
            $color_diferencia = 'color: #e74c3c; font-weight: bold;'; // Rojo para negativo
        } else {
            $color_diferencia = 'color: #2c3e50; font-weight: bold;'; // Gris oscuro para cero
        }

        $resumen_html .= <<<EOF
                <tr>
                    <td class="td-moneda" width="18%">$moneda<br><small>($simbolo)</small></td>
                    <td width="16%">$sumaIngresoV</td>
                    <td width="16%">$sumaEgresoV</td>
                    <td width="17%">$saldoV</td>
                    <td width="17%">$cierre_tipeadoV</td>
                    <td width="16%" style="$color_diferencia">$diferenciaV</td>
                </tr>
        EOF;
    }

    $resumen_html .= <<<EOF
            </tbody>
        </table>
    EOF;

    // Escribir primero las cotizaciones y luego el resumen
    $pdf->writeHTML($cotizaciones_html, false, false, false, false, '');
    $pdf->writeHTML($resumen_html, false, false, false, false, '');
    $pdf->writeHTML($espacio, false, false, false, false, '');
}

$metodo = 'Efectivo';
try {
    error_log("Iniciando carga de movimientos de efectivo para cierre $id_cierre");
    $movimientos_efectivo = $this->model->ListarMovimientosMetodo($id_cierre, $metodo);
    error_log("Movimientos de efectivo cargados: " . count($movimientos_efectivo));

    $efectivo = movimientos($movimientos_efectivo, $metodo, $caja_inicial);
    if (!empty($efectivo)) {
        // Agregar salto de página y título antes de la sección de todas las cajas
        $pdf->AddPage('P', 'A4');
        $titulo_movimientos = '<h1 align="center">Movimientos del Usuario</h1>';
        $pdf->writeHTML($titulo_movimientos, false, false, false, false, '');
        $pdf->writeHTML($espacio, false, false, false, false, '');

        $pdf->writeHTML($efectivo, false, false, false, false, '');
        $pdf->writeHTML($espacio, false, false, false, false, '');
        error_log("HTML de efectivo generado exitosamente");
    } else {
        error_log("No hay movimientos de efectivo para mostrar");
    }
} catch (Exception $e) {
    error_log("Error en movimientos efectivo: " . $e->getMessage());
}

// $pdf->AddPage('P', 'A4'); // Comentar esta línea ya que se agregó arriba
$pdf->writeHTML($espacio, false, false, false, false, '');
// $titulo = '<h1 align="center">Otros Métodos de pago</h1>'; // Comentar esta línea
// $pdf->writeHTML($titulo, false, false, false, false, ''); // Comentar esta línea
$pdf->writeHTML($espacio, false, false, false, false, '');

$metodos = ""; // Inicializar la variable
try {
    error_log("Iniciando carga de otros métodos de pago");
    $lista_metodos = $this->metodo->Listar();
    error_log("Métodos encontrados: " . count($lista_metodos));

    foreach ($lista_metodos as $m) {
        if ($m->metodo == "Efectivo") continue;
        error_log("Procesando método: " . $m->metodo);

        $movimientos_metodo = $this->model->ListarMovimientosMetodo($id_cierre, $m->metodo);
        error_log("Movimientos para {$m->metodo}: " . count($movimientos_metodo));

        $metodo_html = movimientos($movimientos_metodo, $m->metodo);
        if (!empty($metodo_html)) {
            $metodos .= $metodo_html;
        }
    }

    // Solo escribir al PDF si hay contenido
    if (!empty($metodos)) {
        // Agregar título para otros métodos si hay contenido
        $titulo_otros = '<h3>Otros Métodos de Pago</h3>';
        $pdf->writeHTML($titulo_otros, false, false, false, false, '');
        $pdf->writeHTML($metodos, false, false, false, false, '');
        error_log("HTML de otros métodos generado exitosamente");
    } else {
        error_log("No hay otros métodos de pago para mostrar");
    }
} catch (Exception $e) {
    error_log("Error en otros métodos: " . $e->getMessage());
}

/*==============================================================
		VENTAS
================================================================*/


$html1 = <<<EOF
    $estilos
    $espacio
    <h1 align="center">Ventas</h1>

    <table>
        <thead>
            <tr>
                <th width="8%">Hora</th>
                <th width="8%">ID</th>
                <th width="48%">Cliente</th>
                <th width="12%">Vendedor</th>
                <th width="12%">Total</th>
                <th width="12%">Cobrado</th>
            </tr>
        </thead>
        <tbody>

EOF;

// $pdf->writeHTML($html1, false, false, false, false, '');

$totalCredito = 0;
$totalContado = 0;
$totalCosto = 0;
$subtotalVenta = 0;
$totalVenta = 0;
$totalDescuento = 0;
$totalContadoEfec = 0;

// Agregar logging detallado para las ventas
error_log("=== DEBUG VENTAS ===");
error_log("Desde: $desde");
error_log("Hasta: $hasta");
error_log("ID Usuario: $id_usuario");

try {
    // Verificar los parámetros antes de la consulta
    error_log("Parámetros antes de ListarRangoSinAnular:");
    error_log("- desde: '$desde' (tipo: " . gettype($desde) . ")");
    error_log("- hasta: '$hasta' (tipo: " . gettype($hasta) . ")");
    error_log("- id_usuario: '$id_usuario' (tipo: " . gettype($id_usuario) . ")");
    $ventas = $this->venta->ListarRangoSinAnularConCobrado($desde, $hasta, $id_usuario);
    error_log("Ventas encontradas: " . count($ventas));

    if (empty($ventas)) {
        error_log("No se encontraron ventas - Haciendo consulta de diagnóstico...");

        // Consulta de diagnóstico directa usando la conexión PDO del modelo
        try {
            // Primero verificar si existe el usuario
            error_log("Verificando si existe el usuario...");

            // Luego verificar ventas sin filtro de usuario
            error_log("Verificando ventas en el período sin filtro de usuario...");

            // Finalmente verificar la consulta exacta que está fallando
            error_log("Query que se ejecuta:");
            error_log("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM ventas v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE fecha_venta >= '$desde' AND fecha_venta <= '$hasta' AND v.anulado <> 1 AND id_vendedor = $id_usuario GROUP BY v.id_venta DESC");
        } catch (Exception $debug_e) {
            error_log("Error en diagnóstico: " . $debug_e->getMessage());
        }

        // Agregar mensaje informativo en el PDF
        $html1 .= <<<EOF
            <tr>
                <td colspan="6" align="center" style="color: #666; font-style: italic;">
                    No se encontraron ventas para este usuario en el período especificado
                </td>
            </tr>
EOF;
    }

    foreach ($ventas as $r):
        // Verificar que las propiedades necesarias existen
        if (!isset($r->subtotal) || !isset($r->total) || !isset($r->fecha_venta)) {
            error_log("Venta con propiedades faltantes: " . json_encode($r));
            continue;
        }
        $subtotal = number_format($r->subtotal, 0, ",", ".");
        $total = number_format($r->total, 0, ",", ".");

        // El campo cobrado ya viene calculado del nuevo método
        $cobrado = number_format($r->cobrado, 0, ",", ".");

        $descuento = $r->subtotal - $r->total;
        $descuentoV = number_format($descuento, 0, ",", ".");
        $costo = number_format(($r->costo ?? 0), 0, ",", ".");
        $ganancia = number_format((($r->total ?? 0) - ($r->costo ?? 0)), 0, ",", ".");
        $hora = date("H:i", strtotime($r->fecha_venta));

        // Manejar nombre de cliente que puede estar vacío
        $nombre_cliente = isset($r->nombre_cli) && !empty($r->nombre_cli) ? $r->nombre_cli : 'Cliente no especificado';
        $vendedor = isset($r->vendedor) && !empty($r->vendedor) ? $r->vendedor : 'N/A';

        // Agregar logging para cada venta procesada
        error_log("Procesando venta ID: {$r->id_venta}, Total: {$r->total}, Cliente: $nombre_cliente");

        $html1 .= <<<EOF
		
			<tr align="right">
				<td width="8%">$hora</td>
				<td width="8%">{$r->id_venta}</td>
				<td width="48%" align="left">{$r->nombre_cli}</td>
				<td width="12%" align="left">{$r->vendedor}</td>
				<td width="12%">$total</td>
				<td width="12%">$cobrado</td>
			</tr>

EOF;
        $totalCosto += ($r->costo ?? 0);
        $totalVenta += ($r->total ?? 0);
        $totalCobrado += ($r->cobrado ?? 0);
        $totalDescuento += $descuento;
        $subtotalVenta += ($r->subtotal ?? 0);

        // Nueva lógica para clasificar correctamente contado vs crédito
        if (($r->contado ?? '') == 'Contado') {
            // Para ventas al contado, todo se considera cobrado
            $totalContado += ($r->total ?? 0);
            if (($r->metodo ?? '') == "efectivo") {
                $totalContadoEfec += ($r->total ?? 0);
            }
        } else {
            // Para ventas a crédito, el monto cobrado va a contado y el resto a crédito
            $monto_cobrado = ($r->cobrado ?? 0);
            $monto_total = ($r->total ?? 0);

            $totalContado += $monto_cobrado;
            if (($r->metodo ?? '') == "efectivo") {
                $totalContadoEfec += $monto_cobrado;
            }

            // El saldo pendiente se considera crédito
            $saldo_pendiente = $monto_total - $monto_cobrado;
            $totalCredito += $saldo_pendiente;
        }



    endforeach;

    $totalVentaV = number_format($totalVenta, 0, ",", ".");
    $totalCobradoV = number_format($totalCobrado, 0, ",", ".");
    $totalCreditoV = number_format($totalCredito, 0, ",", ".");

    $html1 .= <<<EOF
		
		</tbody>
        <tfoot>
            <tr align="right">
				<td colspan="5" >Total cobrado: </td>
				<td>$totalCobradoV</td>
			</tr>
            <tr align="right">
				<td colspan="5" >Total crédito: </td>
				<td>$totalCreditoV</td>
			</tr>
            <tr align="right" style="font-weight: bold;">
				<td colspan="5">Total venta: </td>
				<td>$totalVentaV</td>
			</tr>        </tfoot>
    </table>
EOF;
} catch (Exception $e) {
    error_log("Error en sección de ventas: " . $e->getMessage());

    // Agregar mensaje de error al PDF
    $html1 .= <<<EOF
            <tr>
                <td colspan="6" align="center" style="color: #cc0000; font-weight: bold;">
                    Error al procesar las ventas: {$e->getMessage()}
                </td>
            </tr>
        </tbody>
    </table>
EOF;
}

$pdf->writeHTML($html1, false, false, false, false, '');

// Agregar página explicativa al final
$pdf->AddPage('P', 'A4');

$explicacion = <<<EOF
    <style>
        .titulo-explicacion {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .seccion-explicacion {
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
            padding-left: 15px;
        }
        .seccion-titulo {
            color: #2980b9;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .seccion-texto {
            color: #34495e;
            font-size: 11px;
            line-height: 1.4;
            text-align: justify;
        }
        .importante {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .lista {
            margin-left: 15px;
            font-size: 11px;
        }
    </style>
    
    <h1 class="titulo-explicacion" align="center">GUÍA DE INTERPRETACIÓN DEL INFORME</h1>
    
    <div class="seccion-explicacion">
        <div class="seccion-titulo">1. ENCABEZADO DEL CIERRE</div>
        <div class="seccion-texto">
            Muestra el número de cierre, las fechas y horas de apertura y cierre de caja, así como la fecha y hora de generación del reporte. 
            Esta información permite identificar el período exacto que cubre el informe.
        </div>
    </div>
      <div class="seccion-explicacion">
        <div class="seccion-titulo">2. MOVIMIENTOS DE CAJA PRINCIPAL POR MONEDA</div>
        <div class="seccion-texto">
            <strong>NOVEDAD:</strong> Ahora los movimientos de la caja principal se muestran separados por moneda:
            <div class="lista">
                • <strong>Guaraníes (Gs.):</strong> Movimientos en moneda local paraguaya<br>
                • <strong>Reales (R$):</strong> Movimientos en reales brasileños<br>
                • <strong>Dólares (US$):</strong> Movimientos en dólares estadounidenses
            </div>
            <br>
            Cada moneda tiene su propio seguimiento independiente:
            <div class="lista">
                • <strong>Apertura específica:</strong> Monto inicial por cada moneda<br>
                • <strong>Ingresos y egresos:</strong> Separados por moneda<br>
                • <strong>Saldo acumulado:</strong> Balance independiente por moneda<br>
                • <strong>Resumen individual:</strong> Totales y diferencias por cada moneda
            </div>
            <br>
            <div class="importante">
                <strong>IMPORTANTE:</strong> Solo se muestran las monedas que tienen movimientos o monto de apertura diferente de cero.
                Esto permite un control más preciso del efectivo multimoneda.
            </div>
        </div>
    </div>
    
    <div class="seccion-explicacion">
        <div class="seccion-titulo">3. MOVIMIENTOS DEL USUARIO</div>
        <div class="seccion-texto">
            Detalla todas las transacciones realizadas por el usuario en todas las cajas disponibles, 
            incluyendo tanto ingresos como egresos. Permite ver el flujo completo de efectivo manejado por el empleado.
            <br><br>
            <div class="importante">
                <strong>DIFERENCIA CLAVE:</strong> A diferencia de la sección "Movimientos de Caja Principal" que solo muestra 
                el movimiento efectivo que entró y salió de la caja específica del usuario, en esta sección de 
                "Movimientos del Usuario" también se incluyen los pagos y cobros con efectivo que fueron dirigidos 
                a otras cajas del sistema, como por ejemplo la tesorería central.
            </div>
        </div>
    </div>
    
    <div class="seccion-explicacion">
        <div class="seccion-titulo">4. OTROS MÉTODOS DE PAGO</div>
        <div class="seccion-texto">
            Registra las transacciones realizadas con medios de pago diferentes al efectivo:
            <div class="lista">
                • Tarjetas de crédito y débito<br>
                • Transferencias bancarias<br>
                • Pagos digitales<br>
                • Cheques<br>
                • Otros métodos configurados en el sistema
            </div>
        </div>
    </div>
    
    <div class="seccion-explicacion">
        <div class="seccion-titulo">5. REGISTRO DE VENTAS</div>
        <div class="seccion-texto">
            Lista detallada de todas las ventas realizadas durante el turno, mostrando:
            <div class="lista">
                • <strong>Hora:</strong> Momento exacto de la venta<br>
                • <strong>Cliente:</strong> Nombre del cliente (si se registró)<br>
                • <strong>Vendedor:</strong> Usuario que realizó la venta<br>
                • <strong>Total:</strong> Valor total de la venta<br>
                • <strong>Cobrado:</strong> Monto efectivamente recibido
            </div>
        </div>
    </div>
    
    <div class="importante">
        <strong>INTERPRETACIÓN DE TOTALES EN VENTAS:</strong><br>
        • <strong>Total Cobrado:</strong> Dinero efectivamente recibido de las ventas<br>
        • <strong>Total Crédito:</strong> Ventas pendientes de cobro<br>
        • <strong>Total Venta:</strong> Suma de cobrado + crédito (facturación total)
    </div>
      <div class="seccion-explicacion">
        <div class="seccion-titulo">DIFERENCIAS EN EL RESUMEN POR MONEDA</div>
        <div class="seccion-texto">
            Cada moneda tiene su propio resumen y cálculo de diferencias:
            <div class="lista">
                • <strong>Entrada de caja:</strong> Total de ingresos en esa moneda específica<br>
                • <strong>Salida de caja:</strong> Total de egresos en esa moneda específica<br>
                • <strong>Total sistema:</strong> Saldo calculado automáticamente por el sistema<br>
                • <strong>Total tipeado:</strong> Monto declarado por el usuario al cerrar<br>
                • <strong>Diferencia:</strong> Total tipeado - Total sistema
            </div>
            <br>
            <strong>Interpretación de las diferencias por moneda:</strong>
            <div class="lista">
                • <strong>Positiva (+):</strong> El usuario declaró más dinero del que indica el sistema en esa moneda<br>
                • <strong>Negativa (-):</strong> El usuario declaró menos dinero del que indica el sistema en esa moneda<br>
                • <strong>Cero (0):</strong> Los montos coinciden perfectamente en esa moneda
            </div>
            <br>
            <div class="importante">
                <strong>CONTROL MULTIMONEDA:</strong> Es fundamental verificar cada moneda por separado, 
                ya que pueden existir diferencias en una moneda mientras las otras están balanceadas correctamente.
            </div>
        </div>
    </div>
    
    <div class="importante">
        <strong>NOTA IMPORTANTE:</strong> Este informe es una herramienta de control administrativo que permite 
        supervisar el manejo de caja de cada usuario. Todas las transacciones mostradas corresponden 
        únicamente al período y usuario especificados en el encabezado.
    </div>
EOF;

$pdf->writeHTML($explicacion, false, false, false, false, '');

ob_end_clean();
$pdf->Output('cierre.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

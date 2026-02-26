<?php
// Establecer límite de tiempo de ejecución más alto
set_time_limit(300); // 5 minutos máximo
ini_set('memory_limit', '512M'); // Incrementar límite de memoria
ini_set('max_execution_time', 300);

require_once('plugins/tcpdf2/tcpdf.php');
require_once('model/deuda.php');

class CierreCajaPDF extends TCPDF
{
    public $fechaEmision = '';

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);

        $fecha = $this->fechaEmision ?: date('d/m/Y H:i');
        $texto = 'Emitido: ' . $fecha . ' | Pág. ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages();

        $this->Cell(0, 10, $texto, 0, 0, 'C');
    }
}

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


$pdf = new CierreCajaPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

// Footer global: fecha de emisión + paginación
$pdf->fechaEmision = date('d/m/Y H:i');
$pdf->setPrintFooter(true);
$pdf->SetFooterMargin(12);

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
		<p>Desde $desdeV hasta $hastaV </p>
		

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

/*==============================================================
		REPORTE NUEVO (Ventas + Ingresos)
		Solicitado: 20/02/2026
==============================================================*/

// Helpers locales (evitar dependencias externas)
$h = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
$fmtGs = function ($value) {
    return number_format((float)$value, 0, ',', '.');
};
$truncate = function ($text, $maxLen = 80) {
    $text = (string)$text;
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $maxLen) return $text;
        return mb_substr($text, 0, $maxLen - 3, 'UTF-8') . '...';
    }

    if (strlen($text) <= $maxLen) return $text;
    return substr($text, 0, $maxLen - 3) . '...';
};

// 1) Obtener ventas del cierre (rango del cierre / usuario)
$ventas = [];
$errorVentas = null;
try {
    $ventas = $this->venta->ListarRangoSinAnularConCobrado($desde, $hasta, $id_usuario);
} catch (Exception $e) {
    $errorVentas = $e->getMessage();
    error_log("Error obteniendo ventas para cierre PDF: " . $errorVentas);
}

// Ordenar ventas por fecha (desc) y como desempate por id_venta (desc)
if (!empty($ventas)) {
    usort($ventas, function ($a, $b) {
        $aTs = isset($a->fecha_venta) ? strtotime((string)$a->fecha_venta) : 0;
        $bTs = isset($b->fecha_venta) ? strtotime((string)$b->fecha_venta) : 0;
        if ($aTs === $bTs) {
            $aId = (int)($a->id_venta ?? 0);
            $bId = (int)($b->id_venta ?? 0);
            return $bId <=> $aId;
        }
        return $bTs <=> $aTs;
    });
}

$renderVentasTablaUnificada = function ($titulo, $ventasLista) use ($estilos, $h, $fmtGs, $truncate) {
    $totalMonto = 0;
    $cantidad = 0;
    $montoContado = 0;
    $montoCredito = 0;

    $html = <<<EOF
        $estilos
        <h1 align="center">$titulo</h1>
        <table>
            <thead>
                <tr>
                    <th width="12%">Nro Venta</th>
                    <th width="21%">Doctor</th>
                    <th width="44%">Concepto</th>
                    <th width="8%">Condición</th>
                    <th width="15%" class="right">Monto</th>
                </tr>
            </thead>
            <tbody>
    EOF;

    if (empty($ventasLista)) {
        $html .= <<<EOF
                <tr>
                    <td colspan="5" align="center" style="color:#666; font-style: italic;">Sin registros</td>
                </tr>
        EOF;
    } else {
        foreach ($ventasLista as $venta) {
            $cantidad++;
            $total = (float)($venta->total ?? 0);
            $totalMonto += $total;

            $contadoRaw = trim((string)($venta->contado ?? ''));
            $esContado = (strcasecmp($contadoRaw, 'Contado') === 0) || (strcasecmp($contadoRaw, 'contado') === 0);
            if ($esContado) {
                $montoContado += $total;
            } else {
                $montoCredito += $total;
            }

            $doctor = $h($venta->nombre_cli ?? '');
            $comprobante = $h($venta->nro_comprobante ?? ($venta->id_venta ?? ''));

            $concepto = (string)($venta->concepto ?? '');
            if (trim($concepto) === '') {
                $concepto = (string)($venta->producto ?? '');
            }
            $concepto = trim($concepto) !== '' ? $concepto : '-';
            $concepto = $h($truncate($concepto, 110));

            $montoV = $fmtGs($total);

            $html .= <<<EOF
                <tr>
                    <td width="12%" style="font-size: 7px;">$comprobante</td>
                    <td width="21%" style="font-size: 7px;">$doctor</td>
                    <td width="44%" style="font-size: 7px;">$concepto</td>
                    <td width="8%" style="font-size: 8px;">$venta->condicion_factura</td>
                    <td width="15%" align="right" style="font-size: 10px;">$montoV</td>
                </tr>
            EOF;
        }
    }

    $totalMontoV = $fmtGs($totalMonto);
    $montoContadoV = $fmtGs($montoContado);
    $montoCreditoV = $fmtGs($montoCredito);
    $html .= <<<EOF
            </tbody>
        </table>
        <br>
        <table>
            <tbody>
                <tr>
                    <td width="70%" align="right"><strong>Total de ventas:</strong></td>
                    <td width="30%" align="right"><strong>$cantidad</strong></td>
                </tr>
                <tr>
                    <td width="70%" align="right"><strong>Monto total (Gs.):</strong></td>
                    <td width="30%" align="right"><strong>$totalMontoV</strong></td>
                </tr>
                <tr>
                    <td width="70%" align="right"><strong>Monto contado (Gs.):</strong></td>
                    <td width="30%" align="right"><strong>$montoContadoV</strong></td>
                </tr>
                <tr>
                    <td width="70%" align="right"><strong>Monto crédito (Gs.):</strong></td>
                    <td width="30%" align="right"><strong>$montoCreditoV</strong></td>
                </tr>
            </tbody>
        </table>
    EOF;

    return $html;
};

// Hoja 1: Ventas (contado + crédito) en un único bloque ordenado
if ($errorVentas) {
    $pdf->writeHTML($estilos . '<h1 align="center">Ventas</h1><p style="color:#cc0000; font-weight:bold;">Error al obtener ventas: ' . $h($errorVentas) . '</p>', false, false, false, false, '');
} else {
    $pdf->writeHTML($renderVentasTablaUnificada('Ventas (Contado + Crédito)', $ventas), false, false, false, false, '');
}

// 2) Hoja 2: Ingresos
$pdf->AddPage('P', 'A4');

$ingresos = [];
$errorIngresos = null;
try {
    $ingresos = $this->model->ListarIngresosPorTipo($id_usuario, $desde, $hasta);
} catch (Exception $e) {
    $errorIngresos = $e->getMessage();
    error_log("Error obteniendo ingresos para cierre PDF: " . $errorIngresos);
}

$htmlIngresos = <<<EOF
    $estilos
    <h1 align="center">Ingresos</h1>
    <table>
        <thead>
            <tr>
                <th width="10%">Cod.</th>
                <th width="12%">Comp.</th>
                <th width="30%">Cliente</th>
                <th width="20%">Concepto</th>
                <th width="15%">Forma Pago</th>
                <th width="13%" class="right">Monto</th>
            </tr>
        </thead>
        <tbody>
EOF;

$totalIngresosGs = 0;
$totalesPorForma = [];

if ($errorIngresos) {
    $htmlIngresos .= '<tr><td colspan="6" align="center" style="color:#cc0000; font-weight:bold;">Error al obtener ingresos: ' . $h($errorIngresos) . '</td></tr>';
} elseif (empty($ingresos)) {
    $htmlIngresos .= '<tr><td colspan="6" align="center" style="color:#666; font-style: italic;">Sin registros</td></tr>';
} else {
    foreach ($ingresos as $ingreso) {
        $cod = $h($ingreso->cod ?? ($ingreso->id ?? ''));

        $cliente = $ingreso->cliente ?? 'Sin seleccionar';

        $comprobanteRaw = trim((string)($ingreso->comprobante ?? ''));
        if ($comprobanteRaw !== '') {
            $comprobanteRaw = preg_replace('/^\s*Factura\s*N(?:º|°|o)?\s*[:\-]?\s*/iu', '', $comprobanteRaw);
        }
        $comprobante = $comprobanteRaw !== '' ? $h($comprobanteRaw) : '-';

        $monto = (float)($ingreso->monto ?? 0);
        $cambio = (float)($ingreso->cambio ?? 1);
        if ($cambio == 0) $cambio = 1;
        $montoGs = $monto * $cambio;

        $formaPago = (string)($ingreso->forma_pago ?? '');
        $formaPagoKey = trim($formaPago) !== '' ? $formaPago : 'No especificado';
        $totalesPorForma[$formaPagoKey] = ($totalesPorForma[$formaPagoKey] ?? 0) + $montoGs;
        $totalIngresosGs += $montoGs;

        $montoV = $fmtGs($montoGs);
        $htmlIngresos .= <<<EOF
            <tr>
                <td width="10%">$cod</td>
                <td width="12%" style="font-size: 7px;">$comprobante</td>
                <td width="30%" style="font-size: 8px;">$cliente</td>
                <td width="20%">$ingreso->concepto</td>
                <td width="15%">{$h($formaPagoKey)}</td>
                <td width="13%" align="right">$montoV</td>
            </tr>
        EOF;
    }
}

$totalIngresosV = $fmtGs($totalIngresosGs);
$htmlIngresos .= <<<EOF
        </tbody>
    </table>
    <br>
    <table>
        <tbody>
            <tr>
                <td width="70%" align="right"><strong>Total ingresos (Gs.):</strong></td>
                <td width="30%" align="right"><strong>$totalIngresosV</strong></td>
            </tr>
        </tbody>
    </table>
    <br>
    <h3>Valor total por forma de pago</h3>
EOF;

// Resumen por forma de pago
if (empty($totalesPorForma)) {
    $htmlIngresos .= '<p style="color:#666; font-style: italic;">Sin datos para resumir</p>';
} else {
    arsort($totalesPorForma);

    $htmlIngresos .= <<<EOF
        <table>
            <thead>
                <tr>
                    <th width="70%">Forma de Pago</th>
                    <th width="30%" class="right">Total (Gs.)</th>
                </tr>
            </thead>
            <tbody>
    EOF;

    foreach ($totalesPorForma as $forma => $montoFormaGs) {
        $formaH = $h($forma);
        $montoFormaV = $fmtGs($montoFormaGs);
        $htmlIngresos .= <<<EOF
                <tr>
                    <td width="70%">$formaH</td>
                    <td width="30%" align="right">$montoFormaV</td>
                </tr>
        EOF;
    }

    $htmlIngresos .= <<<EOF
            </tbody>
        </table>
    EOF;
}

/*==============================================================
		EGRESOS (tabla igual a ingresos)
==============================================================*/

$egresos = [];
$errorEgresos = null;
try {
    $egresos = $this->model->ListarEgresosPorTipo($id_usuario, $desde, $hasta);
} catch (Exception $e) {
    $errorEgresos = $e->getMessage();
    error_log("Error obteniendo egresos para cierre PDF: " . $errorEgresos);
}

$htmlEgresos = <<<EOF
    <br>
    <h1 align="center">Egresos</h1>
    <table>
        <thead>
            <tr>
                <th width="10%">Cod.</th>
                <th width="12%">Comprobante</th>
                <th width="38%">Cliente</th>
                <th width="20%">Concepto</th>
                <th width="10%">Forma Pago</th>
                <th width="10%" class="right">Monto</th>
            </tr>
        </thead>
        <tbody>
EOF;

$totalEgresosGs = 0;
$totalesEgresosPorForma = [];

if ($errorEgresos) {
    $htmlEgresos .= '<tr><td colspan="6" align="center" style="color:#cc0000; font-weight:bold;">Error al obtener egresos: ' . $h($errorEgresos) . '</td></tr>';
} elseif (empty($egresos)) {
    $htmlEgresos .= '<tr><td colspan="6" align="center" style="color:#666; font-style: italic;">Sin registros</td></tr>';
} else {
    foreach ($egresos as $egreso) {
        $cod = $h($egreso->cod ?? ($egreso->id ?? ''));

        $clienteRaw = trim((string)($egreso->cliente ?? ''));
        $cliente = $clienteRaw !== '' ? $h($truncate($clienteRaw, 35)) : '-';

        $comprobanteRaw = trim((string)($egreso->comprobante ?? ''));
        if ($comprobanteRaw !== '') {
            $comprobanteRaw = preg_replace('/^\s*Factura\s*N(?:º|°|o)?\s*[:\-]?\s*/iu', '', $comprobanteRaw);
        }
        $comprobante = $comprobanteRaw !== '' ? $h($comprobanteRaw) : '-';

        $conceptoRaw = trim((string)($egreso->concepto ?? ''));
        $concepto = $conceptoRaw !== '' ? $h($truncate($conceptoRaw, 60)) : '-';

        $monto = (float)($egreso->monto ?? 0);
        $cambio = (float)($egreso->cambio ?? 1);
        if ($cambio == 0) $cambio = 1;
        $montoGs = $monto * $cambio;

        $formaPago = (string)($egreso->forma_pago ?? '');
        $formaPagoKey = trim($formaPago) !== '' ? $formaPago : 'No especificado';
        $totalesEgresosPorForma[$formaPagoKey] = ($totalesEgresosPorForma[$formaPagoKey] ?? 0) + $montoGs;
        $totalEgresosGs += $montoGs;

        $montoV = $fmtGs($montoGs);
        $htmlEgresos .= <<<EOF
            <tr>
                <td width="10%">$cod</td>
                <td width="12%" style="font-size: 7px;">$comprobante</td>
                <td width="38%" style="font-size: 10px;">$cliente</td>
                <td width="20%">$concepto</td>
                <td width="10%">{$h($formaPagoKey)}</td>
                <td width="10%" align="right">$montoV</td>
            </tr>
EOF;
    }
}

$totalEgresosV = $fmtGs($totalEgresosGs);
$htmlEgresos .= <<<EOF
        </tbody>
    </table>
    <br>
    <table>
        <tbody>
            <tr>
                <td width="70%" align="right"><strong>Total egresos (Gs.):</strong></td>
                <td width="30%" align="right"><strong>$totalEgresosV</strong></td>
            </tr>
        </tbody>
    </table>
    <br>
    <h3>Valor total por forma de pago (Egresos)</h3>
EOF;

if (empty($totalesEgresosPorForma)) {
    $htmlEgresos .= '<p style="color:#666; font-style: italic;">Sin datos para resumir</p>';
} else {
    arsort($totalesEgresosPorForma);
    $htmlEgresos .= <<<EOF
        <table>
            <thead>
                <tr>
                    <th width="70%">Forma de Pago</th>
                    <th width="30%" class="right">Total (Gs.)</th>
                </tr>
            </thead>
            <tbody>
EOF;

    foreach ($totalesEgresosPorForma as $forma => $montoFormaGs) {
        $formaH = $h($forma);
        $montoFormaV = $fmtGs($montoFormaGs);
        $htmlEgresos .= <<<EOF
                <tr>
                    <td width="70%">$formaH</td>
                    <td width="30%" align="right">$montoFormaV</td>
                </tr>
EOF;
    }

    $htmlEgresos .= <<<EOF
            </tbody>
        </table>
EOF;
}

$htmlIngresos .= $htmlEgresos;

// Resumen de caja para efectivo (caja 1) para detectar faltante/sobrante
$aperturaCajaGs = (float)($cierre->monto_apertura ?? 0);
$cierreTipeadoGs = (float)($cierre->monto_cierre ?? 0);

$ingresoEfectivoCajaGs = 0.0;
if (!$errorIngresos && !empty($ingresos)) {
    foreach ($ingresos as $ingreso) {
        $forma = trim((string)($ingreso->forma_pago ?? ''));
        $idCaja = (int)($ingreso->id_caja ?? 0);
        if (strcasecmp($forma, 'Efectivo') !== 0) continue;
        if ($idCaja !== 1) continue;

        $monto = (float)($ingreso->monto ?? 0);
        $cambio = (float)($ingreso->cambio ?? 1);
        if ($cambio == 0) $cambio = 1;
        $ingresoEfectivoCajaGs += ($monto * $cambio);
    }
}

$egresoEfectivoCajaGs = 0.0;
try {
    $egresoEfectivoCajaGs = (float)$this->model->SumarEgresosEfectivo($id_usuario, $desde, $hasta, 1);
} catch (Exception $e) {
    error_log('Error sumando egresos efectivo para cierre PDF: ' . $e->getMessage());
    $egresoEfectivoCajaGs = 0.0;
}

$cierreSistemaGs = $aperturaCajaGs + $ingresoEfectivoCajaGs - $egresoEfectivoCajaGs;
$diferenciaGs = $cierreTipeadoGs - $cierreSistemaGs;
$tipoDif = $diferenciaGs >= 0 ? 'Sobrante' : 'Faltante';

$aperturaCajaV = $fmtGs($aperturaCajaGs);
$ingresoEfectivoCajaV = $fmtGs($ingresoEfectivoCajaGs);
$egresoEfectivoCajaV = $fmtGs($egresoEfectivoCajaGs);
$cierreSistemaV = $fmtGs($cierreSistemaGs);
$cierreTipeadoV = $fmtGs($cierreTipeadoGs);
$diferenciaConSigno = ($diferenciaGs < 0 ? '-' : '') . $fmtGs(abs($diferenciaGs));

$htmlIngresos .= <<<EOF
    <br>
    <h3>Resumen Caja (Efectivo)</h3>
    <table>
        <tbody>
            <tr>
                <td width="70%" align="right"><strong>Apertura (Gs.):</strong></td>
                <td width="30%" align="right"><strong>$aperturaCajaV</strong></td>
            </tr>
            <tr>
                <td width="70%" align="right"><strong>Ingreso en efectivo (Gs.):</strong></td>
                <td width="30%" align="right"><strong>$ingresoEfectivoCajaV</strong></td>
            </tr>
            <tr>
                <td width="70%" align="right"><strong>Egreso en efectivo (Gs.):</strong></td>
                <td width="30%" align="right"><strong>$egresoEfectivoCajaV</strong></td>
            </tr>
            <tr>
                <td width="70%" align="right"><strong>Cierre sistema (Gs.):</strong></td>
                <td width="30%" align="right"><strong>$cierreSistemaV</strong></td>
            </tr>
            <tr>
                <td width="70%" align="right"><strong>Cierre caja (tipeado) (Gs.):</strong></td>
                <td width="30%" align="right"><strong>$cierreTipeadoV</strong></td>
            </tr>
            <tr>
                <td width="70%" align="right"><strong>Diferencia ($tipoDif) (Gs.):</strong></td>
                <td width="30%" align="right"><strong>$diferenciaConSigno</strong></td>
            </tr>
        </tbody>
    </table>
EOF;

$pdf->writeHTML($htmlIngresos, false, false, false, false, '');

/*==============================================================
        DEUDAS AGRUPADAS POR CLIENTE (otra hoja)
==============================================================*/

$deudasAgrupadas = [];
$errorDeudas = null;
try {
    $deudaModel = new deuda();
    $deudasAgrupadas = $deudaModel->ListarAgrupadoCliente();
} catch (Exception $e) {
    $errorDeudas = $e->getMessage();
    error_log('Error obteniendo deudas agrupadas por cliente para cierre PDF: ' . $errorDeudas);
}

$pdf->AddPage('P', 'A4');

$htmlDeudas = <<<EOF
    $estilos
    <h1 align="center">Deudas por cliente</h1>
    <table>
        <thead>
            <tr>
                <th width="10%">Item</th>
                <th width="70%">Cliente</th>
                <th width="20%" class="right">Monto</th>
            </tr>
        </thead>
        <tbody>
EOF;

$totalDeudasPendiente = 0.0;

if ($errorDeudas) {
    $htmlDeudas .= '<tr><td colspan="3" align="center" style="color:#cc0000; font-weight:bold;">Error al obtener deudas: ' . $h($errorDeudas) . '</td></tr>';
} elseif (empty($deudasAgrupadas)) {
    $htmlDeudas .= '<tr><td colspan="3" align="center" style="color:#666; font-style: italic;">Sin registros</td></tr>';
} else {
    $item = 1;
    foreach ($deudasAgrupadas as $deudaAgr) {
        $clienteRaw = trim((string)($deudaAgr->nombre ?? ''));
        $cliente = $clienteRaw !== '' ? $h($truncate($clienteRaw, 80)) : '-';

        // Se usa saldo como deuda pendiente total por cliente
        $montoPendiente = (float)($deudaAgr->saldo ?? 0);
        $totalDeudasPendiente += $montoPendiente;
        $montoPendienteV = $fmtGs($montoPendiente);

        $itemV = $h($item);
        $htmlDeudas .= <<<EOF
            <tr>
                <td width="10%">$itemV</td>
                <td width="70%">$cliente</td>
                <td width="20%" align="right">$montoPendienteV</td>
            </tr>
EOF;
        $item++;
    }
}

$htmlDeudas .= <<<EOF
        </tbody>
    </table>
EOF;

$totalDeudasPendienteV = $fmtGs($totalDeudasPendiente);
$htmlDeudas .= <<<EOF
    <br>
    <table>
        <tbody>
            <tr>
                <td width="80%" align="right"><strong>Total deudas por cliente (Gs.):</strong></td>
                <td width="20%" align="right"><strong>$totalDeudasPendienteV</strong></td>
            </tr>
        </tbody>
    </table>
EOF;

$pdf->writeHTML($htmlDeudas, false, false, false, false, '');

ob_end_clean();
$pdf->Output('cierre.pdf', 'I');
exit;

// Función para generar HTML de movimientos
function movimientos($movimientos_data, $metodo, $apertura = 0)
{
    // Si no hay movimientos, no generar nada
    if (empty($movimientos_data)) {
        return "";
    }

    // Para efectivo, usar el saldo total convertido que viene en los datos
    if (strtolower($metodo) === 'Efectivo' && !empty($movimientos_data)) {
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
    $texto_apertura = (strtolower($metodo) === 'Efectivo') ? 'Apertura Total (Convertido a Gs.)' : 'Apertura';

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

    $titulo_metodo = (strtolower($metodo) === 'Efectivo') ? 'Movimientos de Efectivo (Convertido a Guaraníes)' : "Movimientos de $metodo";

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
        if (strtolower($metodo) === 'Efectivo' && isset($mov->moneda) && !empty($mov->moneda) && $mov->moneda !== 'GS' && $mov->moneda !== 'Guaranies') {
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
       // $pdf->writeHTML($titulo_movimientos, false, false, false, false, '');
        //$pdf->writeHTML($espacio, false, false, false, false, '');

       // $pdf->writeHTML($efectivo, false, false, false, false, '');
       // $pdf->writeHTML($espacio, false, false, false, false, '');
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
            if (($r->metodo ?? '') == "Efectivo") {
                $totalContadoEfec += ($r->total ?? 0);
            }
        } else {
            // Para ventas a crédito, el monto cobrado va a contado y el resto a crédito
            $monto_cobrado = ($r->cobrado ?? 0);
            $monto_total = ($r->total ?? 0);

            $totalContado += $monto_cobrado;
            if (($r->metodo ?? '') == "Efectivo") {
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

//$pdf->writeHTML($html1, false, false, false, false, '');

/*==============================================================
		ANÁLISIS DE INGRESOS POR TIPO
================================================================*/

$html_ingresos = <<<EOF
    $estilos
    $espacio
    <h1 align="center">Análisis de Ingresos por Tipo</h1>

    <table>
        <thead>
            <tr>
                <th width="10%">Hora</th>
                <th width="12%">Forma Pago</th>
                <th width="10%">Moneda</th>
                <th width="38%">Concepto</th>
                <th width="15%">Monto Original</th>
                <th width="15%">Monto (Gs.)</th>
            </tr>
        </thead>
        <tbody>

EOF;

$totalCobrosDeuda = 0;
$totalPagosContado = 0;
$totalCobrosDeudaGs = 0;
$totalPagosContadoGs = 0;

// Separar en dos secciones: Cobros de Deuda y Pagos al Contado
try {
    error_log("=== DEBUG ANÁLISIS DE INGRESOS ===");
    error_log("Consultando ingresos del cierre $id_cierre");
    
    // Obtener todos los ingresos del período del cierre
    $ingresos = $this->model->ListarIngresosPorTipo($id_usuario, $desde, $hasta);
    error_log("Ingresos encontrados: " . count($ingresos));

    if (!empty($ingresos)) {
        // Sección 1: Cobros de Deudas
        $html_ingresos .= <<<EOF
            <tr style="background-color: #e8f5e8;">
                <td colspan="6" align="center"><strong>COBROS DE DEUDAS</strong></td>
            </tr>
EOF;

        $hayCobroDeuda = false;
        foreach ($ingresos as $ingreso) {
            if (!empty($ingreso->pago_deuda)) {
                $hayCobroDeuda = true;
                $hora = date("H:i", strtotime($ingreso->fecha));
                $concepto = htmlspecialchars($ingreso->concepto);
                $forma_pago = htmlspecialchars($ingreso->forma_pago);
                $moneda = $ingreso->moneda ?? 'GS';
                $monto_original = number_format($ingreso->monto, ($moneda == 'GS' ? 0 : 2), ",", ".");
                $monto_gs = number_format($ingreso->monto * $ingreso->cambio, 0, ",", ".");
                
                $totalCobrosDeuda += $ingreso->monto;
                $totalCobrosDeudaGs += ($ingreso->monto * $ingreso->cambio);

                $html_ingresos .= <<<EOF
                    <tr>
                        <td width="10%">$hora</td>
                        <td width="12%">$forma_pago</td>
                        <td width="10%">$moneda</td>
                        <td width="38%">$concepto</td>
                        <td width="15%" align="right">$monto_original</td>
                        <td width="15%" align="right">$monto_gs</td>
                    </tr>
EOF;
            }
        }

        if (!$hayCobroDeuda) {
            $html_ingresos .= <<<EOF
                <tr>
                    <td colspan="6" align="center" style="color: #666; font-style: italic;">
                        No se registraron cobros de deudas en este período
                    </td>
                </tr>
EOF;
        }

        // Subtotal Cobros de Deudas
        $totalCobrosDeudaGsV = number_format($totalCobrosDeudaGs, 0, ",", ".");
        $html_ingresos .= <<<EOF
            <tr style="background-color: #d4edda;">
                <td colspan="5" align="right"><strong>Subtotal Cobros de Deudas:</strong></td>
                <td align="right"><strong>$totalCobrosDeudaGsV</strong></td>
            </tr>
EOF;

        // Sección 2: Pagos al Contado
        $html_ingresos .= <<<EOF
            <tr style="background-color: #fff3cd;">
                <td colspan="6" align="center"><strong>PAGOS AL CONTADO (Ventas y Otros)</strong></td>
            </tr>
EOF;

        $hayPagoContado = false;
        foreach ($ingresos as $ingreso) {
            if (empty($ingreso->pago_deuda)) {
                $hayPagoContado = true;
                $hora = date("H:i", strtotime($ingreso->fecha));
                $concepto = htmlspecialchars($ingreso->concepto);
                $forma_pago = htmlspecialchars($ingreso->forma_pago);
                $moneda = $ingreso->moneda ?? 'GS';
                $monto_original = number_format($ingreso->monto, ($moneda == 'GS' ? 0 : 2), ",", ".");
                $monto_gs = number_format($ingreso->monto * $ingreso->cambio, 0, ",", ".");
                
                $totalPagosContado += $ingreso->monto;
                $totalPagosContadoGs += ($ingreso->monto * $ingreso->cambio);

                $html_ingresos .= <<<EOF
                    <tr>
                        <td width="10%">$hora</td>
                        <td width="12%">$forma_pago</td>
                        <td width="10%">$moneda</td>
                        <td width="38%">$concepto</td>
                        <td width="15%" align="right">$monto_original</td>
                        <td width="15%" align="right">$monto_gs</td>
                    </tr>
EOF;
            }
        }

        if (!$hayPagoContado) {
            $html_ingresos .= <<<EOF
                <tr>
                    <td colspan="6" align="center" style="color: #666; font-style: italic;">
                        No se registraron pagos al contado en este período
                    </td>
                </tr>
EOF;
        }

        // Subtotal Pagos al Contado
        $totalPagosContadoGsV = number_format($totalPagosContadoGs, 0, ",", ".");
        $html_ingresos .= <<<EOF
            <tr style="background-color: #fff3cd;">
                <td colspan="5" align="right"><strong>Subtotal Pagos al Contado:</strong></td>
                <td align="right"><strong>$totalPagosContadoGsV</strong></td>
            </tr>
EOF;

        // Total General
        $totalGeneralIngresos = $totalCobrosDeudaGs + $totalPagosContadoGs;
        $totalGeneralIngresosV = number_format($totalGeneralIngresos, 0, ",", ".");
        $html_ingresos .= <<<EOF
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="5" align="right"><strong>TOTAL GENERAL INGRESOS:</strong></td>
                <td align="right"><strong>$totalGeneralIngresosV</strong></td>
            </tr>
EOF;

    } else {
        $html_ingresos .= <<<EOF
            <tr>
                <td colspan="6" align="center" style="color: #666; font-style: italic;">
                    No se encontraron ingresos para este período
                </td>
            </tr>
EOF;
    }

} catch (Exception $e) {
    error_log("Error en análisis de ingresos: " . $e->getMessage());
    
    $html_ingresos .= <<<EOF
            <tr>
                <td colspan="6" align="center" style="color: #cc0000; font-weight: bold;">
                    Error al procesar los ingresos: {$e->getMessage()}
                </td>
            </tr>
EOF;
}

$html_ingresos .= <<<EOF
        </tbody>
    </table>
    
    $espacio
    
    <h3>Resumen del Análisis de Ingresos</h3>
    <table>
        <thead>
            <tr>
                <th width="60%">Tipo de Ingreso</th>
                <th width="20%">Cantidad</th>
                <th width="20%">Total (Gs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="60%">Cobros de Deudas</td>
                <td width="20%" align="center">-</td>
                <td width="20%" align="right">$totalCobrosDeudaGsV</td>
            </tr>
            <tr>
                <td width="60%">Pagos al Contado (Ventas y Otros)</td>
                <td width="20%" align="center">-</td>
                <td width="20%" align="right">$totalPagosContadoGsV</td>
            </tr>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td width="60%">TOTAL INGRESOS</td>
                <td width="20%" align="center">-</td>
                <td width="20%" align="right">$totalGeneralIngresosV</td>
            </tr>
        </tbody>
    </table>
EOF;

$pdf->writeHTML($html_ingresos, false, false, false, false, '');

/*==============================================================
		ANÁLISIS DE VENTAS POR TIPO DE PAGO
================================================================*/

$html_ventas_tipo = <<<EOF
    $estilos
    $espacio
    <h1 align="center">Análisis de Ventas por Tipo de Pago</h1>

    <table>
        <thead>
            <tr>
                <th width="6%">Hora</th>
                <th width="6%">ID</th>
                <th width="8%">Nro. Fact.</th>
                <th width="25%">Cliente</th>
                <th width="12%">Vendedor</th>
                <th width="12%">Tipo Pago</th>
                <th width="15%">Total</th>
                <th width="16%">Estado</th>
            </tr>
        </thead>
        <tbody>

EOF;

$totalVentasContado = 0;
$totalVentasCredito = 0;
$contadorVentasContado = 0;
$contadorVentasCredito = 0;

// Listar todas las ventas juntas en orden y al final solo resumir cantidades (contado/crédito)
try {
    error_log("=== DEBUG ANÁLISIS DE VENTAS POR TIPO ===");
    error_log("Consultando ventas del cierre $id_cierre");
    
    if (!empty($ventas)) {
        // Asegurar orden por fecha (desc) para el análisis
        $ventasOrdenadas = $ventas;
        usort($ventasOrdenadas, function ($a, $b) {
            $aTs = isset($a->fecha_venta) ? strtotime((string)$a->fecha_venta) : 0;
            $bTs = isset($b->fecha_venta) ? strtotime((string)$b->fecha_venta) : 0;
            if ($aTs === $bTs) {
                $aId = (int)($a->id_venta ?? 0);
                $bId = (int)($b->id_venta ?? 0);
                return $bId <=> $aId;
            }
            return $bTs <=> $aTs;
        });

        foreach ($ventasOrdenadas as $venta) {
            $esContado = (($venta->contado ?? '') == 'Contado');

            $hora = date("H:i", strtotime($venta->fecha_venta));
            $nombre_cliente = isset($venta->nombre_cli) && !empty($venta->nombre_cli) ? htmlspecialchars($venta->nombre_cli) : 'Cliente no especificado';
            $vendedor = isset($venta->vendedor) && !empty($venta->vendedor) ? htmlspecialchars($venta->vendedor) : 'N/A';
            $nro_comprobante = isset($venta->nro_comprobante) && !empty($venta->nro_comprobante) ? htmlspecialchars($venta->nro_comprobante) : '-';
            $total = number_format($venta->total, 0, ",", ".");

            $tipoPago = '';
            $estado = '';
            if ($esContado) {
                $tipoPago = isset($venta->metodo) && !empty($venta->metodo) ? htmlspecialchars($venta->metodo) : 'No especificado';
                $estado = 'Pagado';
                $totalVentasContado += ($venta->total ?? 0);
                $contadorVentasContado++;
            } else {
                $tipoPago = 'Crédito';
                $monto_cobrado = $venta->cobrado ?? 0;
                if ($monto_cobrado >= ($venta->total ?? 0)) {
                    $estado = 'Cobrado';
                } elseif ($monto_cobrado > 0) {
                    $estado = 'Parcial';
                } else {
                    $estado = 'Pendiente';
                }
                $totalVentasCredito += ($venta->total ?? 0);
                $contadorVentasCredito++;
            }

            $html_ventas_tipo .= <<<EOF
                <tr>
                    <td width="6%">$hora</td>
                    <td width="6%">{$venta->id_venta}</td>
                    <td width="8%">$nro_comprobante</td>
                    <td width="25%">$nombre_cliente</td>
                    <td width="12%">$vendedor</td>
                    <td width="12%">$tipoPago</td>
                    <td width="15%" align="right">$total</td>
                    <td width="16%" align="center">$estado</td>
                </tr>
EOF;
        }

        $totalGeneralVentas = $totalVentasContado + $totalVentasCredito;
        $totalGeneralVentasV = number_format($totalGeneralVentas, 0, ",", ".");
        $totalCantidadVentas = $contadorVentasContado + $contadorVentasCredito;

        $totalVentasContadoV = number_format($totalVentasContado, 0, ",", ".");
        $totalVentasCreditoV = number_format($totalVentasCredito, 0, ",", ".");
        $html_ventas_tipo .= <<<EOF
            <tr style="background-color: #e9ecef; font-weight: bold; font-size: 12px;">
                <td colspan="6" align="right"><strong>TOTAL ($totalCantidadVentas):</strong></td>
                <td align="right"><strong>$totalGeneralVentasV</strong></td>
                <td align="center"><strong>-</strong></td>
            </tr>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="6" align="right"><strong>Total contado (Gs.):</strong></td>
                <td align="right"><strong>$totalVentasContadoV</strong></td>
                <td align="center"><strong>-</strong></td>
            </tr>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="6" align="right"><strong>Total crédito (Gs.):</strong></td>
                <td align="right"><strong>$totalVentasCreditoV</strong></td>
                <td align="center"><strong>-</strong></td>
            </tr>
EOF;
    } else {
        $html_ventas_tipo .= <<<EOF
            <tr>
                <td colspan="8" align="center" style="color: #666; font-style: italic;">
                    No se encontraron ventas para este período
                </td>
            </tr>
EOF;
    }

} catch (Exception $e) {
    error_log("Error en análisis de ventas por tipo: " . $e->getMessage());
    
    $html_ventas_tipo .= <<<EOF
            <tr>
                <td colspan="8" align="center" style="color: #cc0000; font-weight: bold;">
                    Error al procesar las ventas: {$e->getMessage()}
                </td>
            </tr>
EOF;
}

$html_ventas_tipo .= <<<EOF
        </tbody>
    </table>
    
    $espacio
    
    <h3>Resumen de Ventas por Tipo de Pago</h3>
    <table>
        <thead>
            <tr>
                <th width="50%">Tipo de Venta</th>
                <th width="20%">Cantidad</th>
                <th width="30%">Total (Gs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="50%">Contado</td>
                <td width="20%" align="center">$contadorVentasContado</td>
                <td width="30%" align="right">$totalVentasContadoV</td>
            </tr>
            <tr>
                <td width="50%">Crédito</td>
                <td width="20%" align="center">$contadorVentasCredito</td>
                <td width="30%" align="right">$totalVentasCreditoV</td>
            </tr>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td width="50%">TOTAL</td>
                <td width="20%" align="center">$totalCantidadVentas</td>
                <td width="30%" align="right">$totalGeneralVentasV</td>
            </tr>
        </tbody>
    </table>
EOF;

$pdf->writeHTML($html_ventas_tipo, false, false, false, false, '');

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
        <div class="seccion-titulo">5. ANÁLISIS DE INGRESOS POR TIPO</div>
        <div class="seccion-texto">
            <strong>NUEVA SECCIÓN:</strong> Clasifica todos los ingresos en dos categorías principales:
            <div class="lista">
                • <strong>Cobros de Deudas:</strong> Ingresos generados por el cobro de deudas pendientes de clientes.
                  Estos registros tienen el campo 'pago_deuda' completado, indicando que provienen del módulo de cobranzas.<br>
                • <strong>Pagos al Contado:</strong> Ingresos por ventas directas, servicios u otros conceptos que no están 
                  relacionados con el cobro de deudas preexistentes.
            </div>
            <br>
            <strong>Información mostrada por cada ingreso:</strong>
            <div class="lista">
                • <strong>Hora:</strong> Momento exacto del ingreso<br>
                • <strong>Forma de Pago:</strong> Efectivo, transferencia, tarjeta, etc.<br>
                • <strong>Moneda:</strong> GS (Guaraníes), USD (Dólares), RS (Reales)<br>
                • <strong>Concepto:</strong> Descripción detallada del ingreso<br>
                • <strong>Monto Original:</strong> Valor en la moneda original<br>
                • <strong>Monto (Gs.):</strong> Valor convertido a guaraníes según la cotización del cierre
            </div>
            <br>
            <div class="importante">
                <strong>UTILIDAD:</strong> Esta clasificación permite identificar qué proporción de los ingresos 
                proviene de cobranzas versus ventas nuevas, facilitando el análisis de flujo de efectivo y 
                el seguimiento de la gestión de cobranzas.
            </div>
        </div>
    </div>

    <div class="seccion-explicacion">
        <div class="seccion-titulo">6. ANÁLISIS DE VENTAS POR TIPO DE PAGO</div>
        <div class="seccion-texto">
            <strong>NUEVA SECCIÓN:</strong> Clasifica todas las ventas según su modalidad de pago:
            <div class="lista">
                • <strong>Ventas al Contado:</strong> Transacciones pagadas completamente al momento de la venta.
                  Incluye todos los métodos de pago inmediato (efectivo, tarjeta, transferencia, etc.)<br>
                • <strong>Ventas a Crédito:</strong> Transacciones donde el cliente queda con deuda pendiente.
                  Pueden tener pagos parciales o estar completamente pendientes.
            </div>
            <br>
            <strong>Información mostrada por cada venta:</strong>
            <div class="lista">
                • <strong>Hora:</strong> Momento exacto de la venta<br>
                • <strong>ID:</strong> Número único de identificación de la venta<br>
                • <strong>Nro. Fact.:</strong> Número de factura o comprobante<br>
                • <strong>Cliente:</strong> Nombre del cliente (si se registró)<br>
                • <strong>Vendedor:</strong> Usuario que realizó la venta<br>
                • <strong>Tipo Pago:</strong> Método utilizado o "Crédito" para ventas a plazo<br>
                • <strong>Total:</strong> Valor total de la venta<br>
                • <strong>Estado:</strong> Para créditos: Pendiente, Parcial o Cobrado
            </div>
            <br>
            <strong>Resumen incluye:</strong>
            <div class="lista">
                • <strong>Cantidad de ventas:</strong> Número total de transacciones por tipo<br>
                • <strong>Totales en Guaraníes:</strong> Suma de los valores de cada categoría<br>
                • <strong>Comparación:</strong> Permite analizar la proporción entre ventas al contado vs crédito
            </div>
            <br>
            <div class="importante">
                <strong>UTILIDAD:</strong> Esta clasificación permite evaluar la política de créditos, 
                identificar patrones de venta y analizar el flujo de efectivo inmediato versus diferido.
                Las secciones están claramente separadas para facilitar el análisis de cada tipo de venta.
            </div>
        </div>
    </div>

    <div class="seccion-explicacion">
        <div class="seccion-titulo">7. REGISTRO DE VENTAS</div>
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

//$pdf->writeHTML($explicacion, false, false, false, false, '');

ob_end_clean();
$pdf->Output('cierre.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

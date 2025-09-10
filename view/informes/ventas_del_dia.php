<?php

require_once('plugins/tcpdf2/tcpdf.php');

// Parámetros de fecha
$fecha = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : date('Y-m-d');
$desde = $fecha;
$hasta = $fecha;

// Obtener movimientos generales
$movimientos = $this->model->InformeGeneral($desde, $hasta);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');

$fechaInforme = date("d/m/Y", strtotime($fecha));
$horahoy = date("H:i");

// Estilos para centrar todas las tablas
$estiloTabla = "
    <style>
        table.informe {
            border-collapse: collapse;
            width: 100%;
            font-size: 11px;
            margin: 0 auto;
        }
        table.informe th, table.informe td {
            border: 1px solid #555;
            padding: 4px 2px;
        }
        table.informe th {
            background-color: #348993;
            color: white;
            font-weight: bold;
        }
        table.informe tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .tabla-centro {
            width: 100%;
            text-align: center;
        }
        .tabla-resumen {
            border-collapse: collapse;
            width: 60%;
            font-size: 13px;
            box-shadow: 0 2px 8px #ccc;
            margin: 0 auto;
        }
        .tabla-resumen th {
            background-color: #348993;
            color: #fff;
            border: 1px solid #348993;
            padding: 10px;
            text-align: left;
        }
        .tabla-resumen td {
            border: 1px solid #348993;
            padding: 8px;
        }
        .tabla-resumen tr:nth-child(even) {
            background-color: #eaf6f8;
        }
        .tabla-resumen tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        .tabla-formas-pago {
            border-collapse: collapse;
            width: 60%;
            font-size: 13px;
            box-shadow: 0 2px 8px #ccc;
            margin: 30px auto;
        }
        .tabla-formas-pago th {
            background-color: #348993;
            color: #fff;
            border: 1px solid #348993;
            padding: 10px;
        }
        .tabla-formas-pago td {
            border: 1px solid #348993;
            padding: 8px;
            background-color: #f9f9f9;
        }
        .tabla-monedas {
            border-collapse: collapse;
            width: 40%;
            font-size: 13px;
            margin: 20px auto 25px auto;
            box-shadow: 0 2px 8px #ccc;
        }
        .tabla-monedas th {
            background-color: #348993;
            color: #fff;
            border: 1px solid #348993;
            padding: 10px;
        }
        .tabla-monedas td {
            border: 1px solid #348993;
            padding: 8px;
            background-color: #f9f9f9;
        }
    </style>
";

$html = <<<EOF
    $estiloTabla
    <div class="tabla-centro">
    <h1 align="center" style="font-size:15px;">Informe General del Día $fechaInforme</h1>
    <p align="center" style="font-size:10px;">Generado a las $horahoy</p>
    <br>
    <table class="informe">
        <tr align="center" style="background-color: #348993; color: white; font-weight: bold;">
            <th style="width:10%;">ID</th>
            <th style="width:10%;">Hora</th>
            <th style="width:30%;">Concepto</th>
            <th style="width:12%;">Monto</th> <!-- Monto en moneda -->
            <th style="width:12%;">Ingreso (Gs)</th>
            <th style="width:12%;">Egreso (Gs)</th>
            <th style="width:14%;">Forma de Pago</th>
        </tr>
EOF;

$total_ingresos = 0;
$total_egresos = 0;
$total_credito = 0;
$total_contado = 0;
$total_cobros_deuda = 0;
$total_compras = 0;
$total_otros_egresos = 0;

foreach ($movimientos as $m) {
    // ID: si es venta a crédito, mostrar id_venta, si no, dejar vacío
    $id = '';
    if (!empty($m->tipo) && $m->tipo == 'Venta Crédito' && isset($m->id)) {
        $id = $m->id;
    } elseif (isset($m->id)) {
        $id = $m->id;
    }

    $hora = date('H:i', strtotime($m->fecha));
    $concepto = !empty($m->concepto) ? $m->concepto : (!empty($m->categoria) ? $m->categoria : '');
    $forma_pago = !empty($m->forma_pago) ? $m->forma_pago : '';
    $ingreso = '';
    $egreso = '';

    // NUEVO: Monto + moneda
    $monto_moneda = '';
    if (isset($m->monto) && isset($m->moneda)) {
        $monto_moneda = number_format($m->monto, 2, ",", ".") . ' ' . $m->moneda;
    }

    // Clasificación para resumen
    if ($m->tipo == 'Ingreso' && strtolower($m->categoria) == 'venta') {
        $total_contado += $m->monto_guaranies;
    }
    if ($m->tipo == 'Venta Crédito') {
        $total_credito += $m->monto_guaranies;
    }
    if ($m->tipo == 'Ingreso' && strtolower($m->categoria) == 'cobro de deuda') {
        $total_cobros_deuda += $m->monto_guaranies;
    }
    // Compras: egreso con id_compra (en InformeGeneral, id es id_compra)
    if ($m->tipo == 'Egreso' && !empty($m->id)) {
        $total_compras += $m->monto_guaranies;
    }
    // Otros egresos: egreso sin id_compra
    if ($m->tipo == 'Egreso' && empty($m->id)) {
        $total_otros_egresos += $m->monto_guaranies;
    }

    // Para la tabla de movimientos
    if ($m->tipo == 'Ingreso' || $m->tipo == 'Venta Crédito') {
        $ingreso = number_format($m->monto_guaranies, 0, ",", ".");
    }
    if ($m->tipo == 'Egreso') {
        $egreso = number_format($m->monto_guaranies, 0, ",", ".");
    }

    $html .= "<tr align='center'>";
    $html .= "<td>{$id}</td>";
    $html .= "<td>{$hora}</td>";
    $html .= "<td>{$concepto}</td>";
    $html .= "<td>{$monto_moneda}</td>"; // NUEVA COLUMNA
    $html .= "<td align='right'>{$ingreso}</td>";
    $html .= "<td align='right'>{$egreso}</td>";
    $html .= "<td>{$forma_pago}</td>";
    $html .= "</tr>";
}

$total_contado_formatted = number_format($total_contado, 0, ",", ".");
$total_credito_formatted = number_format($total_credito, 0, ",", ".");
$total_cobros_deuda_formatted = number_format($total_cobros_deuda, 0, ",", ".");
$total_compras_formatted = number_format($total_compras, 0, ",", ".");
$total_otros_egresos_formatted = number_format($total_otros_egresos, 0, ",", ".");

$html .= "</table><br><br></div>";

// Tabla resumen centrada
$html .= <<<EOF
    <table class="tabla-resumen">
        <tr>
            <th colspan="2">Resumen del Día</th>
        </tr>
        <tr>
            <td>Total Ventas al Contado (Gs):</td>
            <td style="text-align:right;"><b>{$total_contado_formatted}</b></td>
        </tr>
        <tr>
            <td>Total Ventas a Crédito (Gs):</td>
            <td style="text-align:right;"><b>{$total_credito_formatted}</b></td>
        </tr>
        <tr>
            <td>Total Cobros de Deudas (Gs):</td>
            <td style="text-align:right;"><b>{$total_cobros_deuda_formatted}</b></td>
        </tr>
        <tr>
            <td>Compras (Gs):</td>
            <td style="text-align:right;"><b>{$total_compras_formatted}</b></td>
        </tr>
        <tr>
            <td>Otros Egresos (Gs):</td>
            <td style="text-align:right;"><b>{$total_otros_egresos_formatted}</b></td>
        </tr>
    </table>
EOF;

// Espacio entre tabla resumen y formas de pago
$html .= '<div style="height:30px;"></div>';

// --- tabla de sumatoria por forma de pago centrada ---
$formas_pago_sum = [];
foreach ($movimientos as $m) {
    // Solo sumar ingresos y ventas crédito
    if ($m->tipo == 'Ingreso' || $m->tipo == 'Venta Crédito') {
        $fp = !empty($m->forma_pago) ? $m->forma_pago : 'Sin especificar';
        if (!isset($formas_pago_sum[$fp])) $formas_pago_sum[$fp] = 0;
        $formas_pago_sum[$fp] += $m->monto_guaranies;
    }
}

// Generar tabla HTML centrada
$html .= '<table class="tabla-formas-pago">';
$html .= '<tr>
            <th>Forma de Pago</th>
            <th>Total (Gs)</th>
          </tr>';
foreach ($formas_pago_sum as $fp => $monto) {
    $html .= '<tr>
                <td>' . htmlspecialchars($fp) . '</td>
                <td style="text-align:right;"><b>' . number_format($monto, 0, ",", ".") . '</b></td>
              </tr>';
}
$html .= '</table>';

// Espacio entre tabla de formas de pago y resumen por moneda
$html .= '<div style="height:30px;"></div>';

// --- tabla de resumen por moneda centrada (INGRESOS y EGRESOS por moneda) ---
$monedas_sum = [];
foreach ($movimientos as $m) {
    $moneda = !empty($m->moneda) ? $m->moneda : 'Sin especificar';
    if (!isset($monedas_sum[$moneda])) {
        $monedas_sum[$moneda] = ['ingreso' => 0, 'egreso' => 0];
    }
    if ($m->tipo == 'Ingreso' || $m->tipo == 'Venta Crédito') {
        $monedas_sum[$moneda]['ingreso'] += $m->monto;
    }
    if ($m->tipo == 'Egreso') {
        $monedas_sum[$moneda]['egreso'] += $m->monto;
    }
}

// Generar tabla HTML de resumen por moneda centrada
$html .= '<table class="tabla-monedas">';
$html .= '<tr>
            <th>Moneda</th>
            <th>Ingresos</th>
            <th>Egresos</th>
          </tr>';
foreach ($monedas_sum as $moneda => $montos) {
    $html .= '<tr>
                <td>' . htmlspecialchars($moneda) . '</td>
                <td style="text-align:right;"><b>' . number_format($montos['ingreso'], 2, ",", ".") . '</b></td>
                <td style="text-align:right;"><b>' . number_format($montos['egreso'], 2, ",", ".") . '</b></td>
              </tr>';
}
$html .= '</table>';

// Espacio antes de finalizar el documento
$html .= '<div style="height:30px;"></div>';

$pdf->writeHTML($html, false, false, false, false, '');

// --- Página de documentación para usuarios no técnicos ---
$pdf->AddPage('P', 'A4');
$docuHtml = <<<EOF
    <h2 style="color:#348993;">Guía de Lectura del Informe de Ventas del Día</h2>
    <p>
        Este informe presenta un resumen detallado de todas las operaciones de ventas, ingresos y egresos realizadas en la fecha seleccionada. A continuación, se explica el significado de cada sección y columna para facilitar su interpretación:
    </p>
    <ul>
        <li>
            <b>Tabla de Movimientos:</b>
            <ul>
                <li><b>ID:</b> Identificador de la operación o venta.</li>
                <li><b>Hora:</b> Hora en que se realizó la operación.</li>
                <li><b>Concepto:</b> Motivo o descripción de la operación (por ejemplo, venta, cobro de deuda, compra, etc.).</li>
                <li><b>Monto:</b> Importe de la operación y la moneda utilizada (por ejemplo, "150 USD" o "100.000 Gs").</li>
                <li><b>Ingreso (Gs):</b> Monto ingresado en guaraníes.</li>
                <li><b>Egreso (Gs):</b> Monto egresado en guaraníes.</li>
                <li><b>Forma de Pago:</b> Método utilizado para la operación (efectivo, tarjeta, transferencia, etc.).</li>
            </ul>
        </li>
        <li>
            <b>Resumen del Día:</b>
            <ul>
                <li><b>Total Ventas al Contado:</b> Suma de todas las ventas pagadas en el momento.</li>
                <li><b>Total Ventas a Crédito:</b> Suma de ventas realizadas a crédito (a pagar posteriormente).</li>
                <li><b>Total Cobros de Deudas:</b> Pagos recibidos por deudas de días anteriores.</li>
                <li><b>Compras:</b> Total de compras realizadas por la empresa en el día.</li>
                <li><b>Otros Egresos:</b> Gastos diversos no relacionados con compras.</li>
            </ul>
        </li>
        <li>
            <b>Totales por Forma de Pago:</b>
            <ul>
                <li>Muestra cuánto se recibió por cada método de pago (efectivo, tarjeta, etc.), siempre expresado en guaraníes.</li>
            </ul>
        </li>
        <li>
            <b>Totales por Moneda:</b>
            <ul>
                <li>Indica los ingresos y egresos clasificados según la moneda utilizada (por ejemplo, guaraníes, dólares, reales).</li>
                <li>Esto ayuda a visualizar cuánto dinero ingresó o salió en cada tipo de moneda.</li>
            </ul>
        </li>
    </ul>
    <p>
        <b>Nota:</b> Este informe está diseñado para brindar una visión clara y rápida del movimiento financiero diario, facilitando la toma de decisiones y el control de la gestión.
        Si tiene dudas sobre algún dato, consulte con el área administrativa o de sistemas.
    </p>
EOF;

$pdf->writeHTML($docuHtml, false, false, false, false, '');

ob_end_clean();
$pdf->Output('ventas_del_dia.pdf', 'I');
<?php
// ============================================================
// RECIBO DE DINERO – Doble por A4 (Original / Duplicado)
// ============================================================

/**
 * Clase NumeroALetras (la misma que pegaste).
 * Si YA la cargás en otro archivo, ELIMINÁ esta clase aquí
 * para evitar "Cannot redeclare class".
 */
class NumeroALetras
{
    private static $UNIDADES = [
        '',
        'UN ',
        'DOS ',
        'TRES ',
        'CUATRO ',
        'CINCO ',
        'SEIS ',
        'SIETE ',
        'OCHO ',
        'NUEVE ',
        'DIEZ ',
        'ONCE ',
        'DOCE ',
        'TRECE ',
        'CATORCE ',
        'QUINCE ',
        'DIECISEIS ',
        'DIECISIETE ',
        'DIECIOCHO ',
        'DIECINUEVE ',
        'VEINTE '
    ];

    private static $DECENAS = [
        'VENTI',
        'TREINTA ',
        'CUARENTA ',
        'CINCUENTA ',
        'SESENTA ',
        'SETENTA ',
        'OCHENTA ',
        'NOVENTA ',
        'CIEN '
    ];

    private static $CENTENAS = [
        'CIENTO ',
        'DOSCIENTOS ',
        'TRESCIENTOS ',
        'CUATROCIENTOS ',
        'QUINIENTOS ',
        'SEISCIENTOS ',
        'SETECIENTOS ',
        'OCHOCIENTOS ',
        'NOVECIENTOS '
    ];

    public static function convertir($number, $moneda = '', $centimos = '', $forzarCentimos = false)
    {
        $converted = '';
        $decimales = '';

        if (($number < 0) || ($number > 999999999)) {
            return 'No es posible convertir el numero a letras';
        }

        $div_decimales = explode('.', (string)$number);

        if(count($div_decimales) > 1){
            $number = $div_decimales[0];
            $decNumberStr = (string) $div_decimales[1];
            if(strlen($decNumberStr) == 2){
                $decNumberStrFill = str_pad($decNumberStr, 9, '0', STR_PAD_LEFT);
                $decCientos = substr($decNumberStrFill, 6);
                $decimales = self::convertGroup($decCientos);
            }
        } else if ($forzarCentimos){
            $decimales = 'CERO ';
        }

        $numberStr = (string) $number;
        $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);

        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'UN MILLON ';
            } else {
                $converted .= sprintf('%sMILLONES ', self::convertGroup($millones));
            }
        }

        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'MIL ';
            } else {
                $converted .= sprintf('%sMIL ', self::convertGroup($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'UN ';
            } else {
                $converted .= sprintf('%s ', self::convertGroup($cientos));
            }
        }

        if(empty($decimales)){
            $valor_convertido = $converted . strtoupper($moneda);
        } else {
            $valor_convertido = $converted . strtoupper($moneda) . ' CON ' . $decimales . ' ' . strtoupper($centimos);
        }

        return $valor_convertido;
    }

    private static function convertGroup($n)
    {
        $output = '';

        if ($n == '100') {
            $output = "CIEN ";
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

// ================= TCPDF =================
require_once('plugins/tcpdf/tcpdf.php');

// ===== Helpers de formato =====
function gs($n){ return number_format((float)$n, 0, ',', '.'); }
function fecha_larga($fecha) {
    $meses = ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
    $ts = strtotime($fecha ?: date('Y-m-d'));
    return 'Ciudad del Este, '.date('d',$ts).' de '.ucfirst($meses[(int)date('m',$ts)-1]).' de '.date('Y',$ts);
}
function monto_letras($monto){
    $txt = NumeroALetras::convertir((int)$monto, 'GUARANIES', '', false);
    return mb_strtoupper(str_replace('GUARANIES','GUARANÍES',$txt), 'UTF-8');
}

// ====== Datos desde TU modelo ======
$idRecibo = (int)($_GET['id'] ?? 0);
$r = $this->model->Obtener($idRecibo); // <- YA LO TENÍAS

// Empresa: usar método propio si existe; si no, tomar del recibo si trae esos campos
if (method_exists($this->model, 'datosEmpresa')) {
    $empresa = $this->model->datosEmpresa(); // ['razon','actividad','ruc','direccion']
} else {
    $empresa = [
        'razon'     => $r->empresa_razon   ?? '',
        'actividad' => $r->empresa_giro    ?? '',
        'ruc'       => $r->empresa_ruc     ?? '',
        'direccion' => $r->empresa_dir     ?? ''
    ];
}

// Número de recibo (varía por tu implementación)
$recibo_numero =
    ($r->nro_recibo ?? null)
    ?? (isset($r->establecimiento,$r->punto,$r->numero) ? sprintf('%03d-%03d %06d',$r->establecimiento,$r->punto,$r->numero) : '');

// Código de cliente
$cod_cliente = $r->cod_cliente ?? $r->cliente_id ?? '';

// Lugar y fecha
$lugar_fecha = fecha_larga($r->fecha ?? date('Y-m-d'));

// Importe total según tu lógica actual
$importe_total = (int)(($r->monto ?? 0) - ($r->descuento ?? 0) - ($r->ips ?? 0));
if (!$importe_total && isset($r->total)) $importe_total = (int)$r->total;
$importe_letras = monto_letras($importe_total);

// Concepto
$conceptoTxt = trim(($r->categoria ? $r->categoria.' – ' : '').($r->concepto ?? 'Pago según detalle'));

// Facturas del panel izquierdo
$facturas = [];
if (method_exists($this->model, 'facturasDelRecibo')) {
    // Debe devolver: [ ['numero'=>'001-...', 'fecha'=>'YYYY-mm-dd', 'importe'=>int], ... ]
    $facturas = $this->model->facturasDelRecibo($idRecibo);
} elseif (!empty($r->facturas)) {
    $raw = is_string($r->facturas) ? json_decode($r->facturas, true) : $r->facturas;
    if (is_array($raw)) {
        foreach ($raw as $f) {
            $facturas[] = [
                'numero'  => $f['numero'] ?? ($f['factura'] ?? ''),
                'fecha'   => $f['fecha']  ?? '',
                'importe' => (int)($f['importe'] ?? 0),
            ];
        }
    }
}
if (empty($facturas)) {
    // fallback mínimo (no rompe layout)
    $facturas = [[
        'numero'  => $r->factura_numero ?? '',
        'fecha'   => isset($r->fecha) ? date('Y-m-d', strtotime($r->fecha)) : date('Y-m-d'),
        'importe' => $importe_total
    ]];
}

// ====== TCPDF base ======
$pdf = new TCPDF('P','mm','A4', true, 'UTF-8', false);
$pdf->SetCreator('Trinity Technologies');
$pdf->SetAuthor('Trinity Technologies');
$pdf->SetTitle('Recibo de Dinero');
$pdf->SetMargins(10, 8, 10);
$pdf->SetAutoPageBreak(true, 8);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);

// Dimensiones
$bloqueH = 138; // alto de cada recibo
$gap     = 8;   // espacio entre ambos

// Render de un recibo (Original/Duplicado)
$render = function($etiquetaPie, $y) use($pdf,$empresa,$r,$facturas,$recibo_numero,$cod_cliente,$lugar_fecha,$importe_total,$importe_letras,$conceptoTxt){
    $x=10; $w=190;

    // Marco
    $pdf->SetLineStyle(['width'=>0.2, 'color'=>[120,120,120]]);
    $pdf->Rect($x,$y,$w,138);

    // Encabezado Empresa
    $pdf->SetFont('helvetica','B',12);
    $pdf->SetXY($x, $y+3);
    $pdf->Cell($w, 6, trim($empresa['razon']), 0, 1, 'C');

    $pdf->SetFont('helvetica','',8.5);
    if (!empty($empresa['actividad'])) { $pdf->SetX($x); $pdf->Cell($w, 4.2, $empresa['actividad'], 0, 1, 'C'); }
    if (!empty($empresa['ruc']))       { $pdf->SetX($x); $pdf->Cell($w, 4.2, 'RUC: '.$empresa['ruc'], 0, 1, 'C'); }
    if (!empty($empresa['direccion'])) { $pdf->SetX($x); $pdf->Cell($w, 4.2, $empresa['direccion'], 0, 1, 'C'); }

    // Separador
    $pdf->Line($x, $pdf->GetY()+2, $x+$w, $pdf->GetY()+2);

    // Columna izquierda: tabla de facturas
    $col1X = $x+5; $col1Y = $pdf->GetY()+5; $col1W=95;

    $html = '<table border="1" cellpadding="3" cellspacing="0" width="100%">
      <tr style="font-weight:bold; text-align:center;">
        <td width="45%">FACTURA N°</td>
        <td width="25%">FECHA</td>
        <td width="30%">IMPORTE</td>
      </tr>';
    $suma = 0;
    foreach($facturas as $f){
        $suma += (int)$f['importe'];
        $num = htmlspecialchars((string)($f['numero'] ?? ''), ENT_QUOTES, 'UTF-8');
        $fec = !empty($f['fecha']) ? date('d/m/Y', strtotime($f['fecha'])) : '';
        $imp = gs($f['importe'] ?? 0);
        $html .= '<tr>
          <td>'.$num.'</td>
          <td style="text-align:center">'.$fec.'</td>
          <td style="text-align:right">'.$imp.'</td>
        </tr>';
    }
    $html .= '<tr style="font-weight:bold;">
        <td colspan="2" style="text-align:right">TOTALES Gs.</td>
        <td style="text-align:right">'.gs($suma).'</td>
      </tr></table>';

    $pdf->SetXY($col1X,$col1Y);
    $pdf->writeHTMLCell($col1W, 0, $col1X, $col1Y, $html, 0, 0, false, true, 'L', true);

    // Columna derecha: Recibo
    $col2X = $x + 5 + $col1W + 5; $col2Y = $col1Y; $col2W = $w - ($col2X - $x) - 5;

    $pdf->SetFont('helvetica','B',13);
    $pdf->SetXY($col2X,$col2Y);
    $pdf->Cell($col2W, 6, 'RECIBO DE DINERO', 0, 2, 'L');

    $pdf->SetFont('helvetica','',10);
    if ($recibo_numero) $pdf->Cell($col2W, 6, 'N°: '.$recibo_numero, 0, 2, 'L');

    $pdf->SetFont('helvetica','',9.5);
    $pdf->Ln(2);
    if ($cod_cliente!=='') $pdf->Cell($col2W, 5, 'Cód. Cliente: '.$cod_cliente, 0, 2, 'L');
    $pdf->Cell($col2W, 5, $lugar_fecha, 0, 2, 'L');

    // Receptor
    $pdf->Ln(2);
    $pdf->MultiCell($col2W, 5, "Hemos recibido de:\n".mb_strtoupper($r->nombre ?? '', 'UTF-8'), 1, 'L', false, 2, $col2X, $pdf->GetY());

    // RUC / CI
    $pdf->SetXY($col2X, $pdf->GetY()+1.5);
    $pdf->MultiCell($col2W, 5, "R.U.C. / C.I.N°:  ".($r->ruc ?? ''), 1, 'L');

    // Importe en letras
    $pdf->SetXY($col2X, $pdf->GetY()+1.5);
    $pdf->MultiCell($col2W, 10, "La Cantidad de Guaraníes:\n".$importe_letras, 1, 'L');

    // Concepto
    $pdf->SetXY($col2X, $pdf->GetY()+1.5);
    $pdf->MultiCell($col2W, 10, "En concepto de pago de: ".$conceptoTxt, 1, 'L');

    // Importe numérico
    $pdf->SetXY($col2X, $pdf->GetY()+1.5);
    $pdf->SetFont('helvetica','B',11);
    $pdf->MultiCell($col2W, 8, 'Importe: Gs. '.gs($importe_total), 1, 'L');

    // Firma
    $pdf->SetFont('helvetica','',9.5);
    $firmaY = $pdf->GetY() + 16;
    $pdf->Line($col2X + 20, $firmaY, $col2X + $col2W - 20, $firmaY);
    $pdf->SetXY($col2X, $filaY = $firmaY + 1);
    $pdf->Cell($col2W, 5, 'FIRMA Y SELLO', 0, 2, 'C');

    // Pie: ORIGINAL/DUPLICADO
    $pdf->SetFont('helvetica','',8.5);
    $pdf->SetXY($x, $y + 138 - 6.5);
    $pdf->Cell($w, 5.5, $etiquetaPie, 0, 0, 'R');
};

// Pintar Original y Duplicado
$render('ORIGINAL: Cliente', 10);
$render('DUPLICADO: Archivo', 10 + $bloqueH + $gap);

// Línea de corte
$pdf->SetDrawColor(180,180,180);
$pdf->SetLineStyle(['dash'=>'3,2']);
$pdf->Line(10, 10 + $bloqueH + 4, 200, 10 + $bloqueH + 4);

// Salida
ob_end_clean();
$pdf->Output('recibo_dinero.pdf', 'I');

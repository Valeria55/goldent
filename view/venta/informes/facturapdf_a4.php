<?php


// FIN  PRUEBA 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// require_once('view/factura/informes/facturapdf.php');
require_once('plugins/tcpdf2/tcpdf.php');

// Crear PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$id = $_GET['id'] ?? 1;
$facturaArray = $this->venta->Listar($id);
// Datos ficticios o dinámicos
$cliente = $facturaArray[0]->nombre ?? "Cliente Ejemplo";
$ruc = $facturaArray[0]->ruc ?? "1111111-1";
$direccion = $facturaArray[0]->direccion ?? "Av. Ejemplo 123";
$telefono = $facturaArray[0]->telefono ?? "0972 123 135";
$fecha = date("d/m/Y", strtotime($facturaArray[0]->fecha_venta)) ?? date('d/m/Y');
$moneda = $facturaArray[0]->moneda ?? "Guaraníes";

$condicion_factura = "Contado";
$timbrado = $facturaArray[0]->timbrado;
$fecha_inicio = date("d/m/Y", strtotime($facturaArray[0]->fecha_inicio));
$fecha_fin = date("d/m/Y", strtotime($facturaArray[0]->fecha_fin));
$establecimiento = str_pad($facturaArray[0]->establecimiento, 3, '0', STR_PAD_LEFT);
$punto_expedicion = str_pad($facturaArray[0]->punto_expedicion, 3, '0', STR_PAD_LEFT);
$autoimpresor = str_pad($facturaArray[0]->autoimpresor, 7, '0', STR_PAD_LEFT);
$nro_factura = "{$establecimiento}-{$punto_expedicion}-{$autoimpresor}";
$moneda = $facturaArray[0]->moneda ?? "Guaraníes";


// HTML para TCPDF
$html = <<<EOF
<style>
    h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        color: #000;
    }
    .info, .totales, table {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        color: #111;
    }
    .info {
        border-left: 4px solid #9d0000;
        background-color: #fafafa;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
    }
    
    .totales {
        text-align: right;
        color: #9d0000;
    }
    .empresa {
        font-size: 10px;
        color: #444;
        line-height: 1.2;
    }
    .factura-titulo {
        font-size: 20px;
        font-weight: bold;
        margin: 0;
        padding: 0;
    }
</style>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="33%" style="vertical-align:middle;">
            <img src="https://trinitytech.com.py/TRINITYTECHNOLOGIESEAS.png" width="145">
        </td>
        <td width="33%" align="center">
            <br><br> <!-- Estos BRs ajustan la alineación visual -->
            <span class="factura-titulo">FACTURA</span>
        </td>
        <td width="33%" align="right" class="empresa">
            TRINITY TECHNOLOGIES E.A.S. <br>
            RUC: 80122004-1<br>
            Timbrado: $timbrado<br>
            Inicio: 06/05/2025 - Fin: 31/05/2026<br>
        </td>
    </tr>
</table>

<table width="100%" cellspacing="0">
    <tr>
        <td width="50%">
            <div class="info" style="background-color: #f5f5f5; border-left: 2px solid #ff0000;">
                <strong>RUC:</strong> {$ruc}<br>
                <strong>Cliente:</strong> {$cliente}<br>
                <strong>Dirección:</strong> {$direccion}<br>
            </div>
        </td>
        <td width="50%">
            <div class="info" style="background-color: #f5f5f5; border-left: 2px solid #ff0000;">
                <strong>Factura N°:</strong> {$nro_factura}<br>
                <strong>Fecha de emisión:</strong> {$fecha}<br>
                <strong>Condición:</strong> {$condicion_factura}<br>
                <strong>Moneda:</strong> {$moneda}
            </div>
        </td>
    </tr>
</table>

<br>

<table width="100%">
    <tr style="background-color:#f5f5f5;">
        <th align="left" width="40%">Servicio</th>
        <th align="right" width="12%">Cant.</th>
        <th align="right" width="12%">Precio</th>
        <th align="right" width="12%">Exentas</th>
        <th align="right" width="12%">IVA 5%</th>
        <th align="right" width="12%">IVA 10%</th>
    </tr>
    <tr>
        <td align="left" width="40%"></td>
        <td align="right" width="12%"></td>
        <td align="right" width="12%"></td>
        <td align="right" width="12%"></td>
        <td align="right" width="12%"></td>
        <td align="right" width="12%"></td>
    </tr>
</table>
EOF;

$subtotal = 0;
$exentas = 0;
$iva5 = 0;
$iva10 = 0;
$itemsHTML = '';
foreach ($facturaArray as $item) {
    $total = $item->cantidad * $item->precio_venta;
    $subtotal += $total;
    $item->iva = $item->iva ?? 10;
    // Calcular IVA y exentas
    if ($item->iva == 5) {
        $iva5 += $total; // IVA 5% = precio / 21
    } elseif ($item->iva == 10) {
        $iva10 += $total; // IVA 10% = precio / 11
    } else {
        $exentas += $total; // Exentas
    }
    $precio_fmt = number_format($item->precio_venta, 0, ',', '.');
    $total_fmt = number_format($total, 0, ',', '.');
    $html .= <<<EOF
        <table width="100%">
            <tr>
                <td align="left" width="40%" style="font-size:8px">{$item->producto}</td>
                <td align="right" width="12%">{$item->cantidad}</td>
                <td align="right" width="12%">{$precio_fmt}</td>
                <td align="right" width="12%">0</td>
                <td align="right" width="12%">0</td>
                <td align="right" width="12%">{$total_fmt}</td>
            </tr>
        </table>
    EOF;
}
$total = $subtotal;
$subtotal_fmt = number_format($subtotal, 0, ',', '.');
$iva = $subtotal / 11;
$iva_fmt = number_format($iva, 0, ',', '.');
$total_fmt = number_format($total, 0, ',', '.');

$html .= <<<EOF
    <table width="100%">
        <tr>
            <td align="left" width="40%"></td>
            <td align="right" width="12%"></td>
            <td align="right" width="12%"></td>
            <td align="right" width="12%"></td>
            <td align="right" width="12%"></td>
            <td align="right" width="12%"></td>
        </tr>
        <tr style="background-color:#f5f5f5;">
            <td align="left" width="40%"></td>
            <td align="right" width="12%"></td>
            <td align="right" width="12%"></td>
            <td align="right" width="12%">0</td>
            <td align="right" width="12%">0</td>
            <td align="right" width="12%">{$subtotal_fmt}</td>
        </tr>
    </table>
EOF;

$html .= <<<EOF

<br><br>

<table width="100%" style="font-size: 10px; line-height: 1.2; border-top: 1px dashed #ccc;border-bottom: 1px dashed #ccc;padding-top: 5px;">
    <tr>
        <th>Exentas (0%):</th>
        <th>IVA (5%)</th>
        <th>IVA (10%)</th>
        <th align="center" style="border-left: 1px dashed #ccc;">
            Total a pagar:
        </th>
    </tr>
    <tr>
        <td>0</td>
        <td>0</td>
        <td>{$iva_fmt}</td>
        <td align="center" style="border-left: 1px dashed #ccc;font-size: 13px;">
            {$total_fmt}
        </td>
    </tr>
</table>

<br><br>


<table width="100%" style="padding-top: 2px;" border="0" cellspacing="2" cellpadding="2">
    <tr>
        <td style="font-size:9px; color:#666;" width="55%" align="left">
            <strong>Observación:</strong><br>
            Esta es una factura autoimpresor, <br>  debe ser impresa para resguardar el comprobante. <br>
        </td>
        <td style="font-size:9px; color:#666;" width="35%" align="right">
            www.trinitytech.com.py <br>
            Tel: 0972 123 135 <br>
            Avda. Dr. Ignacio A. Pane, Ciudad del Este, Alto Paraná
        </td>
        <td style="text-align:right;" width="10%">
            <img src="https://trinitytech.com.py/qr_trinitytech.png" width="60px">
        </td>
    </tr>
</table>

EOF;

// Imprimir HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output($nro_factura.".pdf", 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
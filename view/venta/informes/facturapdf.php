<?php

require_once('plugins/tcpdf2/tcpdf.php');

$pdf = new TCPDF('P', 'mm', [80, 300], true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(2, 2, 2);
$pdf->AddPage();

$id = $_GET['id'] ?? 1;
$facturaArray = $this->venta->Listar($id);
$cliente = $facturaArray[0]->nombre_cli ?? "Cliente Ejemplo";
$ruc = $facturaArray[0]->ruc ?? "1111111-1";
$direccion = $facturaArray[0]->direccion ?? "Sin dirección";
$telefono = $facturaArray[0]->telefono ?? "N/D";
$fecha = date("d/m/Y", strtotime($facturaArray[0]->fecha_venta)) ?? date('d/m/Y');
$moneda = $facturaArray[0]->moneda ?? "Guaraníes";
$condicion_factura = $facturaArray[0]->contado ?? "Contado";

$timbrado = $facturaArray[0]->timbrado ?? "N/D";
$fecha_inicio = date("d/m/Y", strtotime($facturaArray[0]->fecha_inicio ?? '2025-05-06'));
$fecha_fin = date("d/m/Y", strtotime($facturaArray[0]->fecha_fin ?? '2026-05-31'));
$establecimiento = str_pad($facturaArray[0]->establecimiento ?? 1, 3, '0', STR_PAD_LEFT);
$punto_expedicion = str_pad($facturaArray[0]->punto_expedicion ?? 1, 3, '0', STR_PAD_LEFT);
$autoimpresor = str_pad($facturaArray[0]->autoimpresor ?? 1, 7, '0', STR_PAD_LEFT);
$nro_factura = "{$establecimiento}-{$punto_expedicion}-{$autoimpresor}";

$html = <<<HTML
<style>
    * { font-size: 8px; font-family: helvetica; }
    .center { text-align: center; margin: 0; padding: 0; line-height: 1.1; }
    .right { text-align: right; }
    .bold { font-weight: bold; }
    table { width: 100%; }
    th, td { padding: 1px; }
    hr { border: 0; border-top: 1px dashed #000; margin: 4px 0; }
    .tight { margin: 0; padding: 0; line-height: 1.1; }
</style>
<table>
    <tr>
        <td class="center" colspan="2" style="margin-bottom:2px;">
            <img src="assets/img/SCORECARLOGO.png" width="150" style="margin-bottom:2px;">
        </td>
    </tr>
    <tr>
        <td class="center tight" colspan="2" style="font-size:12px;"></td>
    </tr>
    <tr>
        <td class="center tight" colspan="2"></td>
    </tr>
    <tr>
        <td class="center tight" colspan="2" style="font-size:6px;">de EIRL, BAEZ ENCISO EDISON MANUEL</td>
    </tr>
    <tr>
        <td class="center tight" colspan="2">RUC: 80098689-0</td>
    </tr>
    <tr>
        <td class="center tight" colspan="2">Timbrado: {$timbrado}</td>
    </tr>
    <tr>
        <td class="center tight" colspan="2">Inicio: {$fecha_inicio} - Fin: {$fecha_fin}</td>
    </tr>
</table>
<br>
<br>
<table>
    <tr>
        <td class="center tight" colspan="2">FACTURA N°: {$nro_factura}</td>
    </tr>
    <tr>
        <td class="center tight" colspan="2"></td>
    </tr>
    <tr>
        <td><strong>RUC:</strong></td>
        <td>{$ruc}</td>
    </tr>
    <tr>
        <td><strong>Cliente:</strong></td>
        <td>{$cliente}</td>
    </tr>
    <tr>
        <td><strong>Fecha de emisión:</strong></td>
        <td>{$fecha}</td>
    </tr>
    <tr>
        <td><strong>Condición:</strong></td>
        <td>{$condicion_factura}</td>
    </tr>
    <tr>
        <td><strong>Moneda:</strong></td>
        <td>{$moneda}</td>
    </tr>
    <tr>
        <td><strong>Dirección:</strong></td>
        <td>{$direccion}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
</table>
<hr>
<table>
    <tr>
        <th width="50%"><b>Servicio</b></th>
       <th class="right" width="20%"><b>Precio U.</b></th>
        <th class="right" width="12%"><b>Cant</b></th>
        <th class="right" width="18%"><b>Precio</b></th>
    </tr>
</table>
<hr>
<table>
    <tr>
        <th></th>
    </tr>
</table>
HTML;

$subtotal = 0;
$iva5 = 0;
$iva10 = 0;
$exentas = 0;

foreach ($facturaArray as $item) {
    $precio_con_descuento = ROUND($item->precio_venta - ($item->precio_venta * $item->descuento / 100),0);
    $precio_sin_descuento = $item->precio_venta * $item->cantidad;
    $total = $item->cantidad * $precio_con_descuento;
    $totalSinDescuento = $item->cantidad * $item->precio_venta;
    $subtotal += $total;
    $subtotalSinDescuento += $totalSinDescuento;
    
    $iva = $item->iva ?? 10;

    $precio_fmt = number_format($item->precio_venta, 0, ',', '.');
    $total_fmt = number_format($precio_sin_descuento, 0, ',', '.');

    $ex = $iva == 0 ? $total_fmt : '0';
    $i5 = $iva == 5 ? $total_fmt : '0';
    $i10 = $iva == 10 ? $total_fmt : '0';

    if ($iva == 0) $exentas += $total;
    elseif ($iva == 5) $iva5 += $total;
    elseif ($iva == 10) $iva10 += $total;

    $producto = htmlspecialchars($item->producto);
    $cantidad = $item->cantidad;

    $html .= <<<HTML
    <table>
        <tr>
            <td width="50%">{$producto}</td>
            <td class="right" width="20%">{$precio_fmt}</td>
            <td class="right" width="12%">{$cantidad}</td>
            <td class="right" width="18%">{$total_fmt}</td>
        </tr>
    HTML;
}

$iva5_calc = number_format($iva5 / 21, 0, ',', '.');
$iva10_calc = number_format($iva10 / 11, 0, ',', '.');
$total_fmt = number_format($subtotal, 0, ',', '.');

$descuentos = $subtotalSinDescuento - $subtotal;
$descuentos_fmt = number_format($descuentos, 0, ',', '.');
$subtotalSinDescuento_fmt = number_format($subtotalSinDescuento, 0, ',', '.');
$html .= <<<HTML
</table>
<table>
    <tr>
        <th> </th>
    </tr>
</table>
<hr>
<table>
    <tr><td class="bold">Sub Total:</td><td class="right bold">{$subtotalSinDescuento_fmt}</td></tr>
    <tr><td class="bold">Descuentos:</td><td class="right bold">{$descuentos_fmt}</td></tr>
    <tr><td class="bold">Total a pagar:</td><td class="right bold">{$total_fmt}</td></tr>
</table>
<hr>
<table>
    <tr>
        <th> </th>
    </tr>
</table>
<table>
    <tr><td>Exentas (0%):</td><td class="right">{$exentas}</td></tr>
    <tr><td>IVA (5%):</td><td class="right">{$iva5_calc}</td></tr>
    <tr><td>IVA (10%):</td><td class="right">{$iva10_calc}</td></tr>
</table>
<table>
    <tr>
        <th> </th>
    </tr>
</table>
<table border="0">
    <tr>
        <td style="font-size:8px;" width="78%"><b>Observación:</b><br>
            Esta es una factura autoimpresor, debe ser impresa para resguardar el comprobante.
        </td>
    </tr>
    <tr>
        <td colspan="2" class="center" style="font-size:8px; margin-top:4px;">
        </td>
    </tr>
</table>
HTML;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("factura_{$nro_factura}.pdf", 'I');


?>
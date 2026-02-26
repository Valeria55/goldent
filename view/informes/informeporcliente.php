<?php

require_once('plugins/tcpdf2/tcpdf.php');

$id_cliente = $_REQUEST['id'] ?? $_REQUEST['id_cliente'] ?? null;
$desde_req = $_REQUEST['desde'] ?? null;
$hasta_req = $_REQUEST['hasta'] ?? null;

if (!$id_cliente || !$desde_req || !$hasta_req) {
    die('Faltan parámetros: id (cliente), desde, hasta');
}

$desde = date('d/m/Y', strtotime($desde_req));
$hasta = date('d/m/Y', strtotime($hasta_req));

// En el controlador normalmente se define $cli; fallback por seguridad
if (!isset($cli)) {
    $cli = $this->cliente->Obtener($id_cliente);
}

$cliente_nombre = trim(($cli->nombre ?? ''));
$cliente_ruc = $cli->ruc ?? '';

class InformePorClientePDF extends TCPDF
{
    public $rango_desde;
    public $rango_hasta;
    public $cliente_nombre;
    public $cliente_ruc;

    public function Header()
    {
        $this->SetY(10);
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 6, 'GOLDENT S.A', 0, 1, 'L');

        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 5, 'Dirección: Calle Ernesto Baez y Los Rosales', 0, 1, 'L');
        $this->Cell(0, 5, 'Tel.: 061 571136', 0, 1, 'L');

        $this->Ln(1);
        $y = $this->GetY();
        $this->Line(10, $y, $this->getPageWidth() - 10, $y);
        $this->Ln(3);

        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(0, 5, 'Rango: ' . $this->rango_desde . ' a ' . $this->rango_hasta, 0, 1, 'L');

        $this->Ln(2);
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(16, 5, 'Cliente:', 0, 0, 'L');
        $this->SetFont('helvetica', '', 9);
        $this->Cell(95, 5, $this->cliente_nombre, 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(12, 5, 'R.U.C:', 0, 0, 'L');
        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 5, $this->cliente_ruc, 0, 1, 'L');

        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(16, 5, 'Moneda:', 0, 0, 'L');
        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 5, 'Guaraníes', 0, 1, 'L');

        $this->Ln(2);

        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(16, 10, 'LISTA DE VENTAS:', 0, 0, 'L');

    
    }

    public function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 8, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new InformePorClientePDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema Goldent');
$pdf->SetTitle('Informe de ventas por cliente');
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetMargins(10, 62, 10); // deja espacio para el encabezado
$pdf->SetAutoPageBreak(true, 12);

$pdf->rango_desde = $desde;
$pdf->rango_hasta = $hasta;
$pdf->cliente_nombre = $cliente_nombre;
$pdf->cliente_ruc = $cliente_ruc;

$pdf->AddPage('P', 'A4');

$header_table_style = 'border-top: .5px solid #333; border-bottom: 1px solid #333; font-size:9px; background-color: #eeeeee; color: #202020; font-weight: bold;';
$body_table_style = 'font-size:8px; border-bottom: .3px solid #ccc;';
$footer_table_style = 'font-size:8px; border-top: .5px solid #333; border-bottom: 1px solid #333; background-color: #f0f0f0; font-weight: bold;';

$html = <<<EOF
<table width="100%" cellpadding="3" cellspacing="0" style="$header_table_style">
	<tr align="center">
		<th width="12%">Fecha</th>
		<th width="13%">Comprobante</th>
		<th width="10%">N° venta</th>
		<th width="33%">Materiales/Servicios</th>
		<th width="8%">Cant.</th>
		<th width="12%">Precio</th>
		<th width="12%">Total</th>
	</tr>
</table>
EOF;

$pdf->writeHTML($html, false, false, false, false, '');

$items = $this->model->ListarDetallePorClienteRango($id_cliente, $desde_req, $hasta_req, 'DESC');

if (!$items || count($items) === 0) {
    $pdf->writeHTML('<p style="font-size:9px;">Sin registros en el rango seleccionado.</p>', false, false, false, false, '');
} else {
    $idx = 0;
    $venta_actual = null;
    $total_venta_actual = 0;
    $fecha_venta_actual = '';
    $comprobante_venta_actual = '';

    $imprimirFooterVenta = function () use ($pdf, $footer_table_style, &$venta_actual, &$total_venta_actual, &$fecha_venta_actual, &$comprobante_venta_actual) {
        if ($venta_actual === null) {
            return;
        }
        $total_formatted = number_format((float)$total_venta_actual, 0, ',', '.');
        $label = 'TOTAL VENTA N° ' . $venta_actual;
        $fecha = $fecha_venta_actual;
        $comp = htmlspecialchars((string)$comprobante_venta_actual, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $htmlFooter = <<<EOF
        <table width="100%" cellpadding="3" cellspacing="0" style="$footer_table_style">
            <tr>
                <td width="12%" align="center">$fecha</td>
                <td width="13%" align="center">$comp</td>
                <td width="63%" align="right">$label</td>
                <td width="12%" align="right">$total_formatted</td>
            </tr>
        </table>
EOF;
        $pdf->writeHTML($htmlFooter, false, false, false, false, '');
    };

    foreach ($items as $r) {
        $bg = ($idx % 2 === 0) ? 'background-color:#fafafa;' : '';
        $fecha = date('d/m/Y', strtotime($r->fecha_venta));
        $comprobante = $r->nro_comprobante ?? '';
        $nroVenta = $r->id_venta ?? '';
        $producto = $r->producto ?? '';
        $paciente = $r->paciente ?? null;
        $cantidad = $r->cantidad ?? 0;
        $precio = number_format((float)($r->precio_venta ?? 0), 0, ',', '.');
        $total = number_format((float)($r->total ?? 0), 0, ',', '.');

        // Si cambia la venta, imprimimos el footer de la venta anterior
        if ($venta_actual !== null && (string)$nroVenta !== (string)$venta_actual) {
            $imprimirFooterVenta();
            $total_venta_actual = 0;
        }

        if ($venta_actual === null || (string)$nroVenta !== (string)$venta_actual) {
            $venta_actual = $nroVenta;
            $fecha_venta_actual = $fecha;
            $comprobante_venta_actual = $comprobante;
        }

        $total_venta_actual += (float)($r->total ?? 0);

        $producto_safe = htmlspecialchars((string)$producto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $comprobante_safe = htmlspecialchars((string)$comprobante, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $paciente_safe = htmlspecialchars((string)$paciente, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $detalle_producto = $producto_safe;
        if ($paciente !== null && trim((string)$paciente) !== '') {
            $detalle_producto .= '<br><span style="font-size:7px;"><b>PACIENTE:</b> ' . $paciente_safe . '</span>';
        }

        $row = <<<EOF
        <table width="100%" cellpadding="3" cellspacing="0" style="$bg $body_table_style">
            <tr>
                <td width="12%" align="center">$fecha</td>
                <td width="13%" align="center">$comprobante_safe</td>
                <td width="10%" align="center">$nroVenta</td>
                <td width="33%" align="left" style="font-size:7px;">$detalle_producto</td>
                <td width="8%" align="center">$cantidad</td>
                <td width="12%" align="right">$precio</td>
                <td width="12%" align="right">$total</td>
            </tr>
        </table>
EOF;

        $pdf->writeHTML($row, false, false, false, false, '');
        $idx++;
    }

    // Footer de la última venta
    $imprimirFooterVenta();
}

$pdf->Output('ventas_por_cliente.pdf', 'I');

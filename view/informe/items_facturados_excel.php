<?php
$desde = isset($_REQUEST['desde']) ? date("d/m/Y", strtotime($_REQUEST["desde"])) : '';
$hasta = isset($_REQUEST['hasta']) ? date("d/m/Y", strtotime($_REQUEST["hasta"])) : '';
$comprobante_sel = isset($_REQUEST['comprobante']) ? $_REQUEST['comprobante'] : 'Todos';
$agrupado_sel = isset($_REQUEST['agrupado']) ? (int)$_REQUEST['agrupado'] : 0;
?>
<meta charset="utf-8">
<table border="1">
    <thead>
        <tr>
            <th colspan="<?php echo $agrupado_sel === 1 ? '4' : '10'; ?>" style="background-color: #348993; color: white; font-size: 16pt; font-weight: bold; text-align: center; height: 40px;">
                GOLDENT S.A - Informe de Ítems Facturados
            </th>
        </tr>
        <tr>
            <th colspan="<?php echo $agrupado_sel === 1 ? '4' : '10'; ?>" style="text-align: left; height: 30px; font-weight: bold;">
                Rango: <?php echo $desde; ?> al <?php echo $hasta; ?> | Comprobante: <?php echo htmlspecialchars($comprobante_sel); ?> | Agrupación: <?php echo $agrupado_sel === 1 ? 'Agrupado por Producto' : 'Detallado'; ?>
            </th>
        </tr>
        
        <?php if ($agrupado_sel === 1): ?>
            <!-- Vista Agrupada Excel -->
            <tr style="background-color: #efefef; font-weight: bold;">
                <th width="350">Item (Producto / Servicio)</th>
                <th width="150" style="text-align: center;">Cantidad Total</th>
                <th width="180" style="text-align: right;">Precio Venta Promedio</th>
                <th width="180" style="text-align: right;">Total Acumulado</th>
            </tr>
        <?php else: ?>
            <!-- Vista Desagrupada Excel -->
            <tr style="background-color: #efefef; font-weight: bold;">
                <th width="100" style="text-align: center;">Nº Venta</th>
                <th width="120" style="text-align: center;">Nº Orden</th>
                <th width="200" style="text-align: center;">Cliente</th>
                <th width="200" style="text-align: center;">Paciente</th>
                <th width="300">Item (Producto / Servicio)</th>
                <th width="150" style="text-align: center;">Fecha</th>
                <th width="150" style="text-align: center;">Comprobante</th>
                <th width="100" style="text-align: center;">Cant.</th>
                <th width="150" style="text-align: right;">Precio Unit.</th>
                <th width="150" style="text-align: right;">Total</th>
            </tr>
        <?php endif; ?>
    </thead>
    <tbody>
        <?php
        $totalCant = 0;
        $totalMonto = 0;
        
        foreach ($resultados as $r):
            $totalCant += $r->cantidad;
            $totalMonto += $r->total;
        ?>
            <?php if ($agrupado_sel === 1): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r->producto); ?></td>
                    <td style="text-align: center;"><?php echo number_format($r->cantidad, 0, ",", "."); ?></td>
                    <td style="text-align: right;"><?php echo number_format($r->precio_venta, 0, ",", "."); ?></td>
                    <td style="text-align: right; font-weight: bold; color: #27ae60;"><?php echo number_format($r->total, 0, ",", "."); ?></td>
                </tr>
            <?php else: 
                $compLabel = 'Sin Comprobante';
                if ($r->comprobante === 'Factura') {
                    $compLabel = 'Factura';
                } elseif ($r->comprobante === 'Ticket') {
                    $compLabel = 'Ticket';
                }
            ?>
                <tr>
                    <td style="text-align: center; font-weight: bold;"><?php echo htmlspecialchars($r->id_venta); ?></td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($r->id_presupuesto ?? '-'); ?></td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($r->cliente ?? '-'); ?></td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($r->paciente ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($r->producto); ?></td>
                    <td style="text-align: center;"><?php echo date('d/m/Y H:i', strtotime($r->fecha_venta)); ?></td>
                    <td style="text-align: center;"><?php echo $compLabel; ?></td>
                    <td style="text-align: center;"><?php echo number_format($r->cantidad, 0, ",", "."); ?></td>
                    <td style="text-align: right;"><?php echo number_format($r->precio_venta, 0, ",", "."); ?></td>
                    <td style="text-align: right; font-weight: bold; color: #27ae60;"><?php echo number_format($r->total, 0, ",", "."); ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <?php if (count($resultados) === 0): ?>
            <tr>
                <td colspan="<?php echo $agrupado_sel === 1 ? '4' : '10'; ?>" style="text-align: center; height: 50px;">
                    Sin registros para los filtros seleccionados.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
    <?php if (count($resultados) > 0): ?>
        <tfoot>
            <tr style="background-color: #eaeded; font-weight: bold;">
                <td colspan="<?php echo $agrupado_sel === 1 ? '1' : '7'; ?>" style="text-align: right; height: 30px;">
                    TOTAL GENERAL:
                </td>
                <td style="text-align: center;"><?php echo number_format($totalCant, 0, ",", "."); ?></td>
                <td></td>
                <td style="text-align: right; font-weight: bold; color: #27ae60;"><?php echo number_format($totalMonto, 0, ",", "."); ?></td>
            </tr>
        </tfoot>
    <?php endif; ?>
</table>

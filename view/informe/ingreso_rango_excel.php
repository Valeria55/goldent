<?php
$desde = date("d/m/Y", strtotime($_REQUEST["desde"]));
$hasta = date("d/m/Y", strtotime($_REQUEST["hasta"]));

// Obtener datos
$ingresos = $this->ingreso->Listar_rango($_REQUEST['desde'], $_REQUEST['hasta']);
?>
<meta charset="utf-8">
<table border="1">
    <thead>
        <tr>
            <th colspan="5" style="background-color: #348993; color: white; font-size: 16pt;">GOLDENT S.A - Informe de Ingresos</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: left;">Rango: <?php echo $desde; ?> a <?php echo $hasta; ?></th>
        </tr>
        <tr style="background-color: #efefef;">
            <th width="100">Fecha</th>
            <th width="300">Cliente</th>
            <th width="150">Categoría</th>
            <th width="150">Forma Pago</th>
            <th width="150">Monto (Gs)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalIngreso = 0;
        foreach ($ingresos as $r):
            if ($r->anulado) continue;
            $monto = $r->monto * ($r->cambio ?? 1);
            $totalIngreso += $monto;
        ?>
            <tr>
                <td style="text-align: center;"><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                <td><?php echo htmlspecialchars($r->cliente_nombre ?? 'General'); ?></td>
                <td style="text-align: center;"><?php echo htmlspecialchars($r->categoria); ?></td>
                <td style="text-align: center;"><?php echo htmlspecialchars($r->forma_pago); ?></td>
                <td style="text-align: right;"><?php echo number_format($monto, 0, ",", "."); ?></td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (count($ingresos) == 0): ?>
            <tr>
                <td colspan="5" style="text-align: center;">Sin registros en el rango seleccionado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f9f9f9;">
            <td colspan="4" style="text-align: right;">TOTAL GENERAL:</td>
            <td style="text-align: right;"><?php echo number_format($totalIngreso, 0, ",", "."); ?></td>
        </tr>
    </tfoot>
</table>

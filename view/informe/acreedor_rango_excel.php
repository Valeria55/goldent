<?php
$desde = date("d/m/Y", strtotime($_REQUEST["desde"]));
$hasta = date("d/m/Y", strtotime($_REQUEST["hasta"]));

// Obtener datos
$acreedores = $this->acreedor->ListarRango($_REQUEST['desde'], $_REQUEST['hasta']);
?>
<meta charset="utf-8">
<table border="1">
    <thead>
        <tr>
            <th colspan="5" style="background-color: #348993; color: white; font-size: 16pt;">GOLDENT S.A - Informe de Acreedores (Cuentas por Pagar)</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: left;">Rango: <?php echo $desde; ?> a <?php echo $hasta; ?></th>
        </tr>
        <tr style="background-color: #efefef;">
            <th width="100">Fecha</th>
            <th width="300">Proveedor</th>
            <th width="300">Concepto</th>
            <th width="150">Total</th>
            <th width="150">Saldo Pend.</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalAcreedor = 0;
        $totalSaldo = 0;
        foreach ($acreedores as $a):
            $totalAcreedor += $a->monto;
            $totalSaldo += $a->saldo;
        ?>
            <tr>
                <td style="text-align: center;"><?php echo date("d/m/Y", strtotime($a->fecha)); ?></td>
                <td><?php echo htmlspecialchars($a->nombre); ?></td>
                <td><?php echo htmlspecialchars($a->concepto); ?></td>
                <td style="text-align: right;"><?php echo number_format($a->monto, 0, ",", "."); ?></td>
                <td style="text-align: right; color: #c0392b; font-weight: bold;"><?php echo number_format($a->saldo, 0, ",", "."); ?></td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (count($acreedores) == 0): ?>
            <tr>
                <td colspan="5" style="text-align: center;">Sin registros en el rango seleccionado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f9f9f9;">
            <td colspan="3" style="text-align: right;">TOTALES:</td>
            <td style="text-align: right;"><?php echo number_format($totalAcreedor, 0, ",", "."); ?></td>
            <td style="text-align: right; color: #c0392b;"><?php echo number_format($totalSaldo, 0, ",", "."); ?></td>
        </tr>
    </tfoot>
</table>

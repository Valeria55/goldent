<?php
$desde = date("d/m/Y", strtotime($_REQUEST["desde"]));
$hasta = date("d/m/Y", strtotime($_REQUEST["hasta"]));

// Obtener datos
$egresos = $this->model->ListarSinCompraMes($_REQUEST['desde'], $_REQUEST['hasta']);
?>
<meta charset="utf-8">
<table border="1">
    <thead>
        <tr>
            <th colspan="3" style="background-color: #348993; color: white; font-size: 16pt;">GOLDENT S.A - Informe de Gastos (Egresos)</th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: left;">Rango: <?php echo $desde; ?> a <?php echo $hasta; ?></th>
        </tr>
        <tr style="background-color: #efefef;">
            <th width="100">Fecha</th>
            <th width="500">Concepto / Descripción</th>
            <th width="150">Monto (Gs)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalEgreso = 0;
        foreach ($egresos as $e):
            if ($e->categoria != "Transferencia" && $e->anulado == null):
                $monto = ($e->monto * ($e->cambio ?? 1));
                $totalEgreso += $monto;
        ?>
            <tr>
                <td style="text-align: center;"><?php echo date("d/m/Y", strtotime($e->fecha)); ?></td>
                <td><?php echo htmlspecialchars($e->concepto); ?></td>
                <td style="text-align: right;"><?php echo number_format($monto, 0, ",", "."); ?></td>
            </tr>
        <?php 
            endif;
        endforeach; ?>
        
        <?php if (count($egresos) == 0): ?>
            <tr>
                <td colspan="3" style="text-align: center;">Sin registros en el rango seleccionado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f9f9f9;">
            <td colspan="2" style="text-align: right;">TOTAL GASTOS:</td>
            <td style="text-align: right;"><?php echo number_format($totalEgreso, 0, ",", "."); ?></td>
        </tr>
    </tfoot>
</table>

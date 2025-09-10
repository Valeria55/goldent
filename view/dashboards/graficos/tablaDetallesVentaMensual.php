<?php
// Obtener los datos en función del mes actual
$controller = new dashboardActualController();
$datos = $controller->ctrlDetalleVentasMensual();
?>

<div class="container">
    <div class="table-responsive m-3" style="max-height: 400px; overflow-y: auto; padding: 0;">
        <table class="table table-hover text-center">
            <thead style="background-color: rgb(75, 192, 192);">
                <tr>
                    <th class="text-center">Fecha venta</th>
                    <th class="text-center">Id venta</th>
                    <th class="text-center">Id cliente</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Id producto</th>
                    <th class="text-center">Producto</th>
                    <th class="text-center">Id vendedor</th>
                    <th class="text-center">Monto venta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $venta): ?>
                    <tr>
                        <td><?= htmlspecialchars($venta['fecha_venta']) ?></td>
                        <td><?= htmlspecialchars($venta['id_venta']) ?></td>
                        <td><?= htmlspecialchars($venta['id_cliente']) ?></td>
                        <td><?= htmlspecialchars($venta['nombre']) ?></td>
                        <td><?= htmlspecialchars($venta['id_producto']) ?></td>
                        <td><?= htmlspecialchars($venta['producto']) ?></td>
                        <td><?= htmlspecialchars($venta['id_vendedor']) ?></td>
                        <td><?= number_format($venta['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
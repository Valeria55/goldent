<?php
// Obtener el año seleccionado (por defecto, el año actual)
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

// Obtener los datos en función del año seleccionado
$controller = new dashboardOperativoController();
$datos = $controller->ctrlAvisoDeStock($anio);

$stock = $datos['stock'];
$producto = $datos['producto'];
$codigo = $datos['codigo'];
$cantidad_ventas = $datos['cantidad_ventas'];
$total_ventas_gs = $datos['total_ventas_gs'];
$beneficios = $datos['beneficios'];

?>

<div class="container" id="tablaVentasProductos">
    <div class="table-responsive m-3" style="max-height: 400px; overflow-y: auto; padding:0px; ">
        <table class="table table-light table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Stock</th>
                    <th>Cantidad de Ventas</th>
                    <th>Ventas Totales (GS)</th>
                    <th>Beneficios (GS)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($codigo as $key => $cod) : ?>
                    <tr>
                        <td><?= htmlspecialchars($cod) ?></td>
                        <td><?= htmlspecialchars($producto[$key]) ?></td>
                        <td><?= htmlspecialchars($stock[$key]) ?></td>
                        <td><?= htmlspecialchars($cantidad_ventas[$key]) ?></td>
                        <td><?= number_format($total_ventas_gs[$key]) ?></td>
                        <td><?= number_format($beneficios[$key]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


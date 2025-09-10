<?php
// Obtener el año seleccionado (por defecto, el año actual)
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

// Obtener los datos en función del año seleccionado
$controller = new dashboardOperativoController();
$datos = $controller->ctrlVentasDeProductos($anio);

$codigo = $datos['codigo'];
$producto = $datos['producto'];
$marca = $datos['marca'];
$categoria = $datos['categoria'];
$precio_costo = $datos['precio_costo'];
$precio_unitario = $datos['precio_unitario'];
$ingreso_total = $datos['ingreso_total'];
$gasto_total = $datos['gasto_total'];
$diferencia = $datos['diferencia'];
?>

<!-- Llamada al Gráfico de Comparación de Ingresos y Egresos -->
<div>
    <canvas id="VentasProductosCategoria" width="700px" height="350px"></canvas>
</div>

<div class="row justify-content-center m-3">
    <button class="btn btn-outline-info" style="max-width: 30%;" onclick="toggleTable('tablaVentasProductos')">Mostrar/Ocultar Tabla de ventas de Productos</button>
</div>

<div class="container" style="display:none;" id="tablaVentasProductos">
    <div class="table-responsive m-3" style="max-height: 400px; overflow-y: auto; padding:0px; " >
        <table class="table table-light table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">Código</th>
                    <th class="text-center">Producto</th>
                    <th class="text-center">Marca</th>
                    <th class="text-center">Categoría</th>
                    <th class="text-center">Precio de Costo (GS)</th>
                    <th class="text-center">Precio de Venta Unitario (GS)</th>
                    <th class="text-center">Ingreso Total (GS)</th>
                    <th class="text-center">Gasto Total (GS)</th>
                    <th class="text-center">Diferencia (GS)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($codigo as $key => $cod) : ?>
                    <tr>
                        <td><?= htmlspecialchars($cod) ?></td>
                        <td><?= htmlspecialchars($producto[$key]) ?></td>
                        <td><?= htmlspecialchars($marca[$key]) ?></td>
                        <td><?= htmlspecialchars($categoria[$key]) ?></td>
                        <td><?= number_format($precio_costo[$key]) ?></td>
                        <td><?= number_format($precio_unitario[$key]) ?></td>
                        <td><?= number_format($ingreso_total[$key]) ?></td>
                        <td><?= number_format($gasto_total[$key]) ?></td>
                        <td><?= number_format($diferencia[$key]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('VentasProductosCategoria').getContext('2d');

        var graficoIngresosEgresos = new Chart(ctx, {
            type: 'bar',
            data: {

                labels: <?= json_encode($producto = array_slice($datos['producto'], 0, 10)) ?>,
                // Limitar los arrays a los primeros 10 elementos



                datasets: [{
                        label: 'Ingresos totales',
                        data: <?= json_encode($ingreso_total = array_slice($datos['ingreso_total'], 0, 10)) ?>,
                        backgroundColor: '#00C3FF',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Costo total',
                        data: <?= json_encode($gasto_total = array_slice($datos['gasto_total'], 0, 10)) ?>,
                        backgroundColor: '#EC7249',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Diferencia',
                        data: <?= json_encode($diferencia = array_slice($datos['diferencia'], 0, 10)) ?>,
                        backgroundColor: '#FFD43B',
                        borderWidth: 2,
                        fill: true
                    }
                ]
            },
            // Configuraciones del gráfico
            options: {
                // Configurar las escalas del gráfico
                scales: {
                    // Configuración del eje Y
                    y: {
                        // Comenzar el eje Y desde cero
                        beginAtZero: true,
                        // Título del eje Y
                        title: {
                            display: true,
                            text: 'Cantidad en GS', // Texto del título
                        }
                    },
                    // Configuración del eje X
                    x: {
                        // Título del eje X
                        title: {
                            display: true,
                            text: 'Productos', // Texto del título
                        },
                        // No mostrar la cuadrícula en el eje X
                        grid: {
                            display: false
                        },
                        // Configuración de las marcas en el eje X
                        ticks: {
                            // No omitir marcas en el eje X
                            autoSkip: false,
                        }
                    }
                },
                // Configuraciones de los plugins del gráfico
                plugins: {
                    // Configuración de la leyenda
                    legend: {
                        // Posición de la leyenda en la parte superior
                        position: 'top',
                    },
                    // Configuración del tooltip (cuadro de información que aparece al pasar el ratón)
                    tooltip: {
                        // Modo de los tooltips: 'index' significa que se mostrarán datos en la misma posición en el eje X
                        mode: 'index',
                        // No intersecar con las barras
                        intersect: false
                    }
                },
                // Hacer que el gráfico sea responsivo
                responsive: true,
                // Mantener la proporción del gráfico
                maintainAspectRatio: false,
            }
        });
    });
</script>

<script>
    // Función para mostrar u ocultar tablas
    function toggleTable(tableId) {
        var table = document.getElementById(tableId);
        if (table.style.display === "none") {
            table.style.display = "flex";
        } else {
            table.style.display = "none";
        }
    }
</script>
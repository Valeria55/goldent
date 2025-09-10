<?php
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
$filtroVentas = isset($_GET['filtroVentas']) ? $_GET['filtroVentas'] : 'categoria';

$controller = new dashboardOperativoController();
$datos = $controller->ctrlVRentabilidadProductosCategorias($anio, $filtroVentas);

$etiquetas = array_column($datos, 'etiqueta');
$ganancias = array_column($datos, 'ganancia');
$cantidad = array_column($datos, 'cantidad');
?>

<!-- Formulario de selección -->
<form method="GET" action="">
    <input type="hidden" name="c" value="dashboardOperativo"> <!-- Campo oculto para conservar el parámetro c -->
    <label for="anio">Seleccionar Año:</label>
    <select name="anio" id="anio">
        <?php foreach ($aniosDisponibles as $anioDisponible): ?>
            <option value="<?= $anioDisponible ?>" <?= $anioDisponible == $anio ? 'selected' : '' ?>>
                <?= $anioDisponible ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="filtroVentas">Filtrar por:</label>
    <select name="filtroVentas" id="filtroVentas">
        <option value="categoria" <?= $filtroVentas == 'categoria' ? 'selected' : '' ?>>Categoría</option>
        <option value="productos" <?= $filtroVentas == 'productos' ? 'selected' : '' ?>>Producto</option>
    </select>
    <button type="submit">Filtrar</button>
</form>

<!-- Gráfico de Chart.js -->
<div>
    <canvas id="VentasPorCategoria" width="700px" height="350px"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('VentasPorCategoria').getContext('2d');

        var graficoIngresosEgresos = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($etiquetas) ?>,
                datasets: [{
                    label: 'Beneficios obtenidos',
                    data: <?= json_encode($ganancias) ?>,
                    backgroundColor: '#FFD43B',
                    borderWidth: 2
                },
                {
                    label: 'Cantidad de ventas',
                    data: <?= json_encode($cantidad) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true, // Activa el apilado en el eje Y
                        title: {
                            display: true,
                            text: 'Monto en GS - Cantidad de ventas'
                        }
                    },
                    x: {
                        stacked: true // Activa el apilado en el eje X para asegurarse de que ambos ejes estén apilados
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    });
    
</script>



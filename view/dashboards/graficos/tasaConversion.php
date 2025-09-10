<?php
// Obtener el año seleccionado (por defecto, el año actual)
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

// Obtener los datos en función del año seleccionado
$controller = new dashboardClientesController();
$datos = $controller->ctrlTasaConversion($anio);

$tasaConversion = $datos['tasaConversion'];
$tasaNoConversion = $datos['tasaNoConversion'];
$total_presupuestos = $datos['total_presupuestos'];
$concretados = $datos['concretados'];
$no_concretados = $datos['no_concretados'];
?>

<!-- Gráfico de Chart.js -->
<div class="container">
    <div class="row">
        <div class="col-md">
            <canvas id="tasaConversion" width="700px" height="350px"></canvas>
        </div>
        <div class="col-md">
            <div class="table-responsive m-3" style="max-height: 400px; overflow-y: auto; padding:0px; ">
                <table class="table  table-hover text-center">
                    <thead style="background-color: rgba(75, 192, 192, 0.7);">
                        <tr>
                            <th class="text-center">Presupuestos Realizados</th>
                            <th class="text-center">Presupuestos Concretados</th>
                            <th class="text-center">Presupuestos No Concretados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format($total_presupuestos) ?></td>
                            <td><?= number_format($concretados) ?></td>
                            <td><?= number_format($no_concretados) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <?php include 'tablaInfoPresupuestosNoConcretados.php'; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('tasaConversion').getContext('2d');

        var graficoIngresosEgresos = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Tasa de Conversión', 'Tasa de No Conversión'], // Etiquetas para cada sector del gráfico
                datasets: [{
                    label: 'Tasa de Conversión',
                    data: [<?= json_encode($tasaConversion) ?>, <?= json_encode($tasaNoConversion) ?>], // Datos en un solo dataset
                    backgroundColor: ['#FFD43B', 'rgba(75, 192, 192, 0.7)'], // Colores para cada sector
                    borderWidth: 2
                }]
            },
            options: {
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
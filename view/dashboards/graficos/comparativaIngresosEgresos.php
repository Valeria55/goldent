<?php
// Obtener el año seleccionado (por defecto, el año actual)
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

// Obtener los datos en función del año seleccionado
$controller = new dashboardOperativoController();
$datos = $controller->ctrlComparativaIngresosEgresos($anio);

$meses = $datos['meses'];
$ingresosMensual = $datos['ingresosMensual'];
$egresosMensual = $datos['egresosMensual'];
?>

<style>
    select,
    button {
        padding: 10px 15px;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        outline: none;
    }


    button {
        background-color: #007bff;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
        border: 2px solid #007bff;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>

<!-- Llamada al Gráfico de Comparación de Ingresos y Egresos -->
<div>
    <canvas id="CompararIngresosEgresos" width="700px" height="350px"></canvas>
</div>

<script>
    // Se espera a que el contenido del documento esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener el contexto del lienzo donde se dibujará el gráfico
        var ctx = document.getElementById('CompararIngresosEgresos').getContext('2d');

        // Crear un nuevo gráfico utilizando Chart.js
        var graficoIngresosEgresos = new Chart(ctx, {
            // Definir el tipo de gráfico como 'bar' (gráfico de barras)
            type: 'bar',
            // Proporcionar los datos que se utilizarán para el gráfico
            data: {
                // Etiquetas para el eje X, que representan los meses
                labels: <?= json_encode($meses) ?>,
                datasets: [{
                        // Definir el primer conjunto de datos para los ingresos
                        label: 'Ingresos',
                        // Datos que se mostrarán en el gráfico para los ingresos
                        data: <?= json_encode($ingresosMensual) ?>,
                        // Color de fondo de las barras para los ingresos
                        backgroundColor: '#00C3FF',
                        // Ancho del borde de las barras
                        borderWidth: 2,
                        // Rellenar el área bajo las barras
                        fill: true
                    },
                    {
                        // Definir el segundo conjunto de datos para los egresos
                        label: 'Egresos',
                        // Datos que se mostrarán en el gráfico para los egresos
                        data: <?= json_encode($egresosMensual) ?>,
                        // Color de fondo de las barras para los egresos
                        backgroundColor: '#EC7249',
                        // Ancho del borde de las barras
                        borderWidth: 2,
                        // Rellenar el área bajo las barras
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
                            text: 'Meses', // Texto del título
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

</script>
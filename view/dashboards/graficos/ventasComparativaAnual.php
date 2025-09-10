<?php

$ctr = new dashboardOperativoController();

$datos_ventas = $ctr->obtenerDatosVentas();

$ventasActuales = isset($datos_ventas['ventas_actual']) ? $datos_ventas['ventas_actual'] : $datos_ventas['ventas_actual'];

$ventasAnteriores = isset($datos_ventas['ventas_anterior']) ? $datos_ventas['ventas_anterior'] : $datos_ventas['ventas_anterior'];
?>

<head>

    <style>
        .chart-container {
            width: 80%;
            height: 400px;
            margin: 30px auto 5px;
            padding-bottom: 50px;
        }
    </style>
</head>

<body>
    <div class="chart-container">
        <h2>Comparativa de Ventas <?php echo date('Y') - 1; ?> - <?php echo date('Y'); ?></h2>
        <canvas id="graficoVentas"></canvas>
    </div>

    <script>
        // Función para formatear números grandes
        // Función para formatear números con separadores de miles
        function formatNumber(number) {
            // Usar toLocaleString() para formatear con separadores de miles
            return number.toLocaleString('es-ES'); // 'es-ES' para usar puntos como separador de miles
        }

        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        // Crear el gráfico
        new Chart(document.getElementById('graficoVentas'), {
            type: 'bar',
            data: {
                labels: meses, // Etiquetas de los meses
                datasets: [{
                        label: 'Ventas ' + <?php echo date('Y'); ?>,
                        data: <?php echo json_encode(array_values($ventasActuales)); ?>, // Asegúrate de usar array_values
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Ventas ' + <?php echo date('Y') - 1; ?>,
                        data: <?php echo json_encode(array_values($ventasAnteriores)); ?>, // Asegúrate de usar array_values
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad en GS', // Texto del título
                        },
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value); // Formateo de los números con separadores de miles
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                return formatNumber(value) + ' GS';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
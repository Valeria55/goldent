<?php

$ctr = new dashboardClientesController();
$data = $ctr->getDashboardData();

?>

<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Rentabilidad por Cliente</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="row">
                            <div class="col-lg-8">
                                <canvas id="customerProfitabilityChart" class="m-3"></canvas>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Estadísticas de Clientes VIP</h5>
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Clientes VIP
                                                <span id="totalVIPClientes" class="badge bg-primary rounded-pill"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Rentabilidad Total VIP
                                                <span id="rentabilidadTotalVIP" class="badge bg-success rounded-pill">0 GS</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Rentabilidad Promedio VIP
                                                <span id="rentabilidadPromedioVIP" class="badge bg-info rounded-pill">0 GS</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón para mostrar/ocultar la tabla -->
                        <button id="toggleTableButton" class="btn btn-primary m-3">Ver detalles</button>

                        <!-- Tabla de clientes (oculta inicialmente) -->
                        <div id="customerTableContainer" class="table-responsive p-3" style="display: none;">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cliente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Ventas Totales</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Costo Total</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Rentabilidad</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">VIP</th>
                                    </tr>
                                </thead>
                                <tbody id="customerTableBody">
                                    <!-- Filas de clientes se insertarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos obtenidos del controlador PHP
            const customerData = <?php echo json_encode($data['customerSales']); ?>;
            const vipStats = <?php echo json_encode($data['vipStats']); ?>;

            // Convertir a número y aplicar separación de miles a estadísticas VIP
            document.getElementById('totalVIPClientes').textContent = Number(vipStats.total_clientes_vip).toLocaleString();
            document.getElementById('rentabilidadTotalVIP').textContent = Number(vipStats.rentabilidad_total_vip).toLocaleString() + ' GS';
            document.getElementById('rentabilidadPromedioVIP').textContent = Number(vipStats.promedio_rentabilidad_vip).toLocaleString() + ' GS';

            // Llenar tabla de clientes con separación de miles
            const tableBody = document.getElementById('customerTableBody');
            customerData.forEach(customer => {
                const row = `
                    <tr>
                        <td>${customer.nombre}</td>
                        <td>${Number(customer.total_ventas).toLocaleString()} Gs</td>
                        <td>${Number(customer.total_costo).toLocaleString()} Gs</td>
                        <td>${Number(customer.rentabilidad).toLocaleString()} Gs</td>
                        <td>${customer.es_cliente_vip === '1' ? '✓' : '—'}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });

            // Preparar datos para el gráfico
            const ctx = document.getElementById('customerProfitabilityChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: customerData.map(c => c.nombre),
                    datasets: [{
                        label: 'Rentabilidad (GS)',
                        data: customerData.map(c => Number(c.rentabilidad)),
                        backgroundColor: customerData.map(c =>
                            c.es_cliente_vip === '1' ? 'rgba(75, 192, 192, 0.8)' : 'rgba(54, 162, 235, 0.6)'
                        ),
                        borderColor: customerData.map(c =>
                            c.es_cliente_vip === '1' ? 'rgba(75, 192, 192, 1)' : 'rgba(54, 162, 235, 1)'
                        ),
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // Horizontal bar chart
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Rentabilidad por Cliente'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Rentabilidad (GS)'
                            }
                        }
                    }
                }
            });

            // Función para mostrar/ocultar la tabla
            const toggleTableButton = document.getElementById('toggleTableButton');
            const customerTableContainer = document.getElementById('customerTableContainer');

            toggleTableButton.addEventListener('click', () => {
                if (customerTableContainer.style.display === 'none') {
                    customerTableContainer.style.display = 'block';
                    toggleTableButton.textContent = 'Ocultar detalles';
                } else {
                    customerTableContainer.style.display = 'none';
                    toggleTableButton.textContent = 'Ver detalles';
                }
            });
        });
    </script>
</body>
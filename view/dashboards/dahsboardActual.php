<?php
$controller = new dashboardActualController();

$ventasDiarias = $controller->ctrlVentasDiariasPorcentajeCambio();
$ventasPorHoras = $controller->ctrlVentasPorHoras();


$ventasMensuales = $controller->ctrlVentasMensualesPorcentajeCambio();
$ventasDiariasMensual = $controller->ctrlVentasDiariasMesActual();



$lucroMensualPorcentaje = $controller->ctrlLucroMensualPorcentajeCambio();
$egresoMensualPorcentaje = $controller->ctrlEgresosMensualPorcentaje();
$ingresosSemanalDias = $controller->ctrlIngresosDiasSemanaActual();


$montoVentas = $controller->ctrlMontoventas();
$ventasRecientes = $controller->ctrlVentasRecientes();
$productosTopVentas = $controller->ctrlProductosTopVentas();
$comparativaMensual = $controller->ctrlComparativaMensual();
$lucroEgresosMensual = $controller->ctrlLucroEgresosMensual();

$cantidadVentasMensual = $controller->ctrlCantidadVentasMensual();
$metodosPagosMes = $controller->ctrlMetodosPagosMes();

$deudas = $controller->ctrlListarDeudas();

$totalDeudas=0;

foreach($deudas as $d){
    $totalDeudas += $d->saldo;
}


$totalVentasMes = 0;
foreach ($comparativaMensual as $ventaMes) {
    $totalVentasMes += $ventaMes['total'];
}

$labelsSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graficos Actual</title>
    <!-- Bootstrap 3 (por defecto en toda la página) -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Incluye Flatpickr CSS y JS en el encabezado de tu página -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <style>
        /* MODIFICANDO LOS ESTILOS GLOABLES PARA ENCAJAR CON LA PAGINA OPERACIONAL.PHP*/
        * {
            box-sizing: border-box;
            /* Incluye padding y borde en el tamaño total */
        }

        #content {
            margin: 0px;
            padding: 0px;
            width: 100%;
            flex-grow: 1;
        }

        /* CONTROLAR LOS ESTILOS DEL COMPONENTE HEADER */
        .navbar-nav {
            flex-direction: row;
        }

        .navbar-collapse {
            flex-basis: 0%;
        }

        .navbar-btn {
            padding: 3px;
        }

        #sidebardCollapse {
            background-color: #f5f5f5;
        }

        /* ESTILOS LOCALES */
        .container {
            width: 100%;
        }

        #bootstrap5-section {
            color: black;
            padding: 0 12px 0 0;
            width: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
        }

        select {
            color: black;
        }

        a {
            color: #ffffff;
        }

        .card {
            color: black;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Incrementar la sombra */
            background-color: white;
            /* Fondo blanco para resaltar sobre el fondo general */
            border-radius: 15px;
            /* Bordes redondeados */
        }

        .card-title {
            font-size: 1.25rem;
        }

        .card-body {
            padding: 20px;
        }

        h1,
        h2,
        h3,
        h5 {
            color: black;
        }

        #bootstrap5-section select {
            padding: 10px 15px;
            font-size: 16px;
            color: #000;
            background-color: #fff;
            border: none;
            border-radius: 5px;
        }

        #bootstrap5-section button {
            padding: 10px 15px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            /* Color azul típico de Bootstrap */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #bootstrap5-section button:hover {
            background-color: #0056b3;
            /* Color más oscuro en el hover */
        }

        .card {
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 1.5rem;
        }

        .icon-wrapper {
            position: relative;
            z-index: 1;
        }

        .badge {
            font-weight: 600;
            padding: 0.5em 0.75em;
            border-radius: 6px;
        }

        .z-1 {
            position: relative;
            z-index: 1;
        }

        .text-white {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            font-size: 1.75rem;
            letter-spacing: -0.5px;
        }

        /* Efecto de brillo en hover */
        .card:hover .card-body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), transparent);
            pointer-events: none;
        }

        /* Mejora de contraste para badges */
        .badge {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div id="bootstrap5-section" class="text-center p-3">

        <div class="row g-4">
            <div class="col-sm">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body position-relative" style="background: linear-gradient(45deg, #2C3E50, #3498db);">
                        <div class="d-flex justify-content-between align-items-center position-relative z-1">
                            <div>
                                <h6 class="card-title mb-0 text-white">Ventas Mensual</h6>
                                <h2 class="my-2 fw-bold text-white">
                                    <?php echo number_format($ventasMensuales['total_mes_actual'], 0, '', '.'); ?> Gs
                                </h2>
                                <!-- <p class="mb-0 d-flex align-items-center text-white">
                                    <?php if ($ventasMensuales['porcentaje_cambio'] >= 0): ?>
                                        <span class="badge bg-white text-success me-2">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            <?php echo number_format($ventasMensuales['porcentaje_cambio'], 2, '.', '.'); ?> %
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-white text-danger me-2">
                                            <i class="fas fa-arrow-down me-1"></i>
                                            <?php echo number_format($ventasMensuales['porcentaje_cambio'], 2, '.', '.'); ?> %
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-white">del mes anterior</span>
                                </p> -->
                                <i class="fa-solid fa-circle-info fa-sm"></i>
                                <button class="text-white p-0" data-id="2" style="background: none; color: rgba(255, 255, 255, 0.7);" onclick="toggleDescription(this)">Más información</button>
                                <p id="descripcion-2" class="text-white small mt-2" style="display: none;">
                                    Indica el monto obtenido a partir de ventas del mes junto <br>
                                    con el porcentaje de cambio respecto al mes anterior.
                                </p>
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-chart-line fa-3x text-white opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 bg-white">
                        <canvas id="ventasMesChart2" style="height: 100px; max-width:500px; display:none;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-sm">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body position-relative" style="background: linear-gradient(45deg, #4158D0, #C850C0);">
                        <div class="d-flex justify-content-between align-items-center position-relative z-1">
                            <div>
                                <h6 class="card-title mb-0 text-white">Utilidad Bruta Mensual</h6>
                                <h2 class="my-2 fw-bold text-white">
                                    <?php echo number_format($lucroEgresosMensual['utilidad_bruta'], 0, '', '.'); ?> Gs
                                </h2>
                               <!-- <p class="mb-0 d-flex align-items-center text-white">
                                    <?php if ($ventasDiarias['porcentaje_cambio'] >= 0): ?>
                                        <span class="badge bg-white text-success me-2">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            <?php echo number_format($ventasDiarias['porcentaje_cambio'], 2, '.', '.'); ?> %
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-white text-danger me-2">
                                            <i class="fas fa-arrow-down me-1"></i>
                                            <?php echo number_format($ventasDiarias['porcentaje_cambio'], 2, '.', '.'); ?> %
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-white">del día anterior</span>
                                </p> -->
                                <!-- Botón personalizado -->
                                <i class="fa-solid fa-circle-info fa-sm"></i>
                                <button id="detalles" class="btn btn-link text-white p-0 text-decoration-none" style="background: none; color: rgba(255, 255, 255, 0.7);" data-id="1" onclick="toggleDescription(this)">Más información</button>
                                <p id="descripcion-1" class="text-white small mt-2" style="display: none;">
                                    Se muestra el monto total de la utilidad bruta del mes actual.
                                </p>
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-shopping-cart fa-3x text-white opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 bg-white">
                        <canvas id="ventasHoyChart" style="height: 100px; max-width:500px; display:none;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-sm">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body position-relative" style="background: linear-gradient(45deg, #904e95, #e96443);">
                        <div class="d-flex justify-content-between align-items-center position-relative z-1">
                            <div>
                                <h6 class="card-title mb-0 text-white">Gastos Mensual</h6>
                                <h2 class="my-2 fw-bold text-white">
                                    <?php echo number_format($lucroEgresosMensual['gastos'], 0,'','.'); ?> Gs
                                </h2>
                                 <!-- <p class="mb-0 d-flex align-items-center text-white">
                                    <?php if ($egresoMensualPorcentaje['porcentaje_cambio'] >= 0): ?>
                                        <span class="badge bg-white text-success me-2">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            <?php echo number_format($egresoMensualPorcentaje['porcentaje_cambio'], 2, '.', '.'); ?> %
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-white text-danger me-2">
                                            <i class="fas fa-arrow-down me-1"></i>
                                            <?php echo number_format($egresoMensualPorcentaje['porcentaje_cambio'], 2, '.', '.'); ?> %
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-white">del mes anterior</span>
                                </p> -->
                                <i class="fa-solid fa-circle-info fa-sm"></i>
                                <button class="text-white p-0" style="background: none; color: rgba(255, 255, 255, 0.7);" data-id="4" onclick="toggleDescription(this)">Más información</button>
                                <p id="descripcion-4" class="text-white small mt-2" style="display: none;">
                                    Indica los gastos registrados de este mes, y el porcentaje de cambio respecto a los egresos al mes anterior.<br>
                                    .
                                </p>
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-file-invoice-dollar fa-3x text-white opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 bg-white">
                        <canvas id="egresosSemanaChart" style="height: 100px; max-width:500px; display:none"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-sm">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body position-relative" style="background: linear-gradient(45deg, #134E5E, #71B280);">
                        <div class="d-flex justify-content-between align-items-center position-relative z-1">
                            <div>
                                <h6 class="card-title mb-0 text-white">Lucro Mensual</h6>
                                <h2 class="my-2 fw-bold text-white">
                                    <?php echo number_format($lucroEgresosMensual['lucro'], 0, '', '.'); ?> Gs
                                </h2>
                                                               <!--  <p class="mb-0 d-flex align-items-center text-white">
                                    <?php if ($lucroMensualPorcentaje['porcentaje_cambio'] >= 0): ?>
                                        <span class="badge bg-white text-success me-2">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            <?php echo number_format($lucroMensualPorcentaje['porcentaje_cambio'], 2, '.', '.'); ?> %
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-white text-danger me-2">
                                            <i class="fas fa-arrow-down me-1"></i>
                                            <?php echo number_format($lucroMensualPorcentaje['porcentaje_cambio'], 2, '.', '.'); ?> %
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-white">del mes anterior</span>
                                </p> -->
                                <i class="fa-solid fa-circle-info fa-sm"></i>
                                <button class="text-white p-0" style="background: none; color: rgba(255, 255, 255, 0.7);" data-id="3" onclick="toggleDescription(this)">Más información</button>
                                <p id="descripcion-3" class="text-white small mt-2" style="display: none;">
                                    Indica el lucro registrado este mes, y el porcentaje de cambio respecto al lucro del mes anterior.<br>
                                    
                                </p>
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-wallet fa-3x text-white opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 bg-white">
                        <canvas id="ingresosSemanaChart" style="height: 100px; max-width:500px; display:none" ></canvas>
                    </div>
                </div>
            </div>
           

        </div>
        
        <div class="row g-4 mt-3">
            
            <div class="col-sm">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body position-relative" style="background: linear-gradient(45deg, #4158D0, #C850C0);">
                        <div class="d-flex justify-content-between align-items-center position-relative z-1">
                            <div>
                                <h6 class="card-title mb-0 text-white">Utilidad Bruta Mes Anterior</h6>
                                <h2 class="my-2 fw-bold text-white">
                                    <?php echo number_format($lucroMensualPorcentaje['utilidad_bruta_anterior'], 0, '', '.'); ?> Gs
                                </h2>
                                <!--<p class="mb-0 d-flex align-items-center text-white">-->
                                <!--    <?php if ($ventasDiarias['porcentaje_cambio'] >= 0): ?>-->
                                <!--        <span class="badge bg-white text-success me-2">-->
                                <!--            <i class="fas fa-arrow-up me-1"></i>-->
                                <!--            <?php echo number_format($ventasDiarias['porcentaje_cambio'], 2, '.', '.'); ?> %-->
                                <!--        </span>-->
                                <!--    <?php else: ?>-->
                                <!--        <span class="badge bg-white text-danger me-2">-->
                                <!--            <i class="fas fa-arrow-down me-1"></i>-->
                                <!--            <?php echo number_format($ventasDiarias['porcentaje_cambio'], 2, '.', '.'); ?> %-->
                                <!--        </span>-->
                                <!--    <?php endif; ?>-->
                                <!--    <span class="text-white">del día anterior</span>-->
                                <!--</p>-->
                                <!-- Botón personalizado -->
                                <!--<i class="fa-solid fa-circle-info fa-sm"></i>-->
                                <!--<button id="detalles" class="btn btn-link text-white p-0 text-decoration-none" style="background: none; color: rgba(255, 255, 255, 0.7);" data-id="1" onclick="toggleDescription(this)">Más información</button>-->
                                <!--<p id="descripcion-1" class="text-white small mt-2" style="display: none;">-->
                                <!--    Se muestra el monto total vendido en el día acompañado de un <br> gráfico-->
                                <!--    lineal de las ventas a lo largo del día con un intervalo de dos horas.-->
                                <!--</p>-->
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-shopping-cart fa-3x text-white opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 bg-white">
                        <canvas id="ventasHoyChart" style="height: 100px; max-width:500px; display:none;"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-sm">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body position-relative" style="background: linear-gradient(45deg, #904e95, #e96443);">
                        <div class="d-flex justify-content-between align-items-center position-relative z-1">
                            <div>
                                <h6 class="card-title mb-0 text-white">Gastos Mes Anterior</h6>
                                <h2 class="my-2 fw-bold text-white">
                                    <?php echo number_format($lucroMensualPorcentaje['gastos_mes_anterior'], 0,'','.'); ?> Gs
                                </h2>
                                <!-- <p class="mb-0 d-flex align-items-center text-white">-->
                                <!--    <?php if ($egresoMensualPorcentaje['porcentaje_cambio'] >= 0): ?>-->
                                <!--        <span class="badge bg-white text-success me-2">-->
                                <!--            <i class="fas fa-arrow-up me-1"></i>-->
                                <!--            <?php echo number_format($egresoMensualPorcentaje['porcentaje_cambio'], 2, '.', '.'); ?> %-->
                                <!--        </span>-->
                                <!--    <?php else: ?>-->
                                <!--        <span class="badge bg-white text-danger me-2">-->
                                <!--            <i class="fas fa-arrow-down me-1"></i>-->
                                <!--            <?php echo number_format($egresoMensualPorcentaje['porcentaje_cambio'], 2, '.', '.'); ?> %-->
                                <!--        </span>-->
                                <!--    <?php endif; ?>-->
                                <!--    <span class="text-white">del mes anterior</span>-->
                                <!--</p>-->
                                <!--<i class="fa-solid fa-circle-info fa-sm"></i>-->
                                <!--<button class="text-white p-0" style="background: none; color: rgba(255, 255, 255, 0.7);" data-id="4" onclick="toggleDescription(this)">Más información</button>-->
                                <!--<p id="descripcion-4" class="text-white small mt-2" style="display: none;">-->
                                <!--    Indica los egresos registrados este mes, y el porcentaje de cambio respecto a los egresos al mes anterior.<br>-->
                                <!--    .-->
                                <!--</p>-->
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-file-invoice-dollar fa-3x text-white opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 bg-white">
                        <canvas id="egresosSemanaChart" style="height: 100px; max-width:500px; display:none"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-sm">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body position-relative" style="background: linear-gradient(45deg, #134E5E, #71B280);">
                        <div class="d-flex justify-content-between align-items-center position-relative z-1">
                            <div>
                                <h6 class="card-title mb-0 text-white">Lucro Mes Anterior</h6>
                                <h2 class="my-2 fw-bold text-white">
                                    <?php echo number_format($lucroMensualPorcentaje['lucro_mes_anterior'], 0, '', '.'); ?> Gs
                                </h2>
                                <!--<p class="mb-0 d-flex align-items-center text-white">-->
                                <!--    <?php if ($lucroMensualPorcentaje['porcentaje_cambio'] >= 0): ?>-->
                                <!--        <span class="badge bg-white text-success me-2">-->
                                <!--            <i class="fas fa-arrow-up me-1"></i>-->
                                <!--            <?php echo number_format($lucroMensualPorcentaje['porcentaje_cambio'], 2, '.', '.'); ?> %-->
                                <!--        </span>-->
                                <!--    <?php else: ?>-->
                                <!--        <span class="badge bg-white text-danger me-2">-->
                                <!--            <i class="fas fa-arrow-down me-1"></i>-->
                                <!--            <?php echo number_format($lucroMensualPorcentaje['porcentaje_cambio'], 2, '.', '.'); ?> %-->
                                <!--        </span>-->
                                <!--    <?php endif; ?>-->
                                <!--    <span class="text-white">del mes anterior</span>-->
                                <!--</p>-->
                                <!--<i class="fa-solid fa-circle-info fa-sm"></i>-->
                                <!--<button class="text-white p-0" style="background: none; color: rgba(255, 255, 255, 0.7);" data-id="3" onclick="toggleDescription(this)">Más información</button>-->
                                <!--<p id="descripcion-3" class="text-white small mt-2" style="display: none;">-->
                                <!--    Indica el lucro registrado este mes, y el porcentaje de cambio respecto al lucro del mes anterior.<br>-->
                                    
                                <!--</p>-->
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-wallet fa-3x text-white opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 bg-white">
                        <canvas id="ingresosSemanaChart" style="height: 100px; max-width:500px; display:none" ></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-sm">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="card-body position-relative" style="background: linear-gradient(45deg, #2C3E50, #3498db);">
                        <div class="d-flex justify-content-between align-items-center position-relative z-1">
                            <div>
                                <h6 class="card-title mb-0 text-white">Total Deudas Pendientes</h6>
                                <h2 class="my-2 fw-bold text-white">
                                    <?php echo number_format($totalDeudas, 0, '', '.'); ?> Gs
                                </h2>
                                <!--<p class="mb-0 d-flex align-items-center text-white">-->
                                <!--    <?php if ($ventasMensuales['porcentaje_cambio'] >= 0): ?>-->
                                <!--        <span class="badge bg-white text-success me-2">-->
                                <!--            <i class="fas fa-arrow-up me-1"></i>-->
                                <!--            <?php echo number_format($ventasMensuales['porcentaje_cambio'], 2, '.', '.'); ?> %-->
                                <!--        </span>-->
                                <!--    <?php else: ?>-->
                                <!--        <span class="badge bg-white text-danger me-2">-->
                                <!--            <i class="fas fa-arrow-down me-1"></i>-->
                                <!--            <?php echo number_format($ventasMensuales['porcentaje_cambio'], 2, '.', '.'); ?> %-->
                                <!--        </span>-->
                                <!--    <?php endif; ?>-->
                                <!--    <span class="text-white">del mes anterior</span>-->
                                <!--</p>-->
                                <!--<i class="fa-solid fa-circle-info fa-sm"></i>-->
                                <!--<button class="text-white p-0" data-id="2" style="background: none; color: rgba(255, 255, 255, 0.7);" onclick="toggleDescription(this)">Más información</button>-->
                                <!--<p id="descripcion-2" class="text-white small mt-2" style="display: none;">-->
                                <!--    Indica el monto obtenido a partir de ventas en lo que va de mes junto <br>-->
                                <!--    con un gráfico del monto total cerrado a través de los días.-->
                                <!--</p>-->
                            </div>
                            <div class="icon-wrapper">
                                <i class="fas fa-chart-line fa-3x text-white opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 bg-white">
                        <canvas id="ventasMesChart2" style="height: 100px; max-width:500px; display:none;"></canvas>
                    </div>
                </div>
            </div>

            
            
        </div>
        <br>
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Análisis de Ventas Mensual</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasMesChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Formas de Pago del Mes</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="formasPagoChart" style="height: 300px;  max-width:500px"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Top Productos más lucrativo del Mes</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topProductosChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-md mt-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detalles de Venta Semanal</h5>
                </div>
                <div class="card-body">
                    <?php include 'graficos/tablaDetallesVentaSemanal.php'; ?>
                </div>
            </div>
        </div>

    </div>
    <script>
        // Cargar Bootstrap 5 y FontAwesome dinámicamente solo para la sección de gráficos
        function loadBootstrap5() {
            var linkBootstrap = document.createElement('link');
            linkBootstrap.rel = 'stylesheet';
            linkBootstrap.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css';

            var linkFontAwesome = document.createElement('link');
            linkFontAwesome.rel = 'stylesheet';
            linkFontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';

            document.head.appendChild(linkBootstrap);
            document.head.appendChild(linkFontAwesome);

            var scriptBootstrap = document.createElement('script');
            scriptBootstrap.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js';
            document.body.appendChild(scriptBootstrap);
        }

        // Llamar a la función para cargar Bootstrap 5 cuando sea necesario
        loadBootstrap5();

        function toggleDescription(button) {
            const descripcionId = 'descripcion-' + button.getAttribute('data-id');
            const descripcion = document.getElementById(descripcionId);
            descripcion.style.display = descripcion.style.display === 'none' ? 'block' : 'none';
        }

        // Función para crear gráficos de línea pequeños
        function createMiniLineChart(elementId, data) {
            const ctx = document.getElementById(elementId).getContext('2d');
            if (window[elementId + "Chart"]) {
                window[elementId + "Chart"].destroy();
            }
            window[elementId + "Chart"] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array(data.length).fill(''),
                    datasets: [{
                        data: data,
                        borderColor: 'rgba(75, 192, 192, 0.8)',
                        borderWidth: 2,
                        fill: false,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    },
                    elements: {
                        line: {
                            tension: 0.4
                        }
                    }
                }
            });
        }

        createMiniLineChart('ventasHoyChart', <?php echo json_encode($ventasPorHoras); ?>);
        // createMiniLineChart('ventasMesChart', <?php echo json_encode(array_values($ventasDiariasMensual)); ?>);
        createMiniLineChart('ingresosSemanaChart', <?php echo json_encode(array_values($ingresosSemanalDias)); ?>);
        createMiniLineChart('egresosSemanaChart', <?php echo json_encode($controller->ctrlEgresosSemanaActual()); ?>);

        // Gráfico de ventas de la semana
        // new Chart(document.getElementById('ventasSemanaChart'), {
        //     type: 'line',
        //     data: {
        //         labels: <?php echo json_encode(array_column($cantidadVentasSemanal, 'dia')); ?>,
        //         datasets: [{
        //             label: 'Ventas',
        //             data: <?php echo json_encode(array_column($cantidadVentasSemanal, 'cantidad_ventas')); ?>,
        //             borderColor: 'rgb(75, 192, 192)',
        //             tension: 0.1
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false
        //     }
        // });

        // Gráfico de ventas del mes
        new Chart(document.getElementById('ventasMesChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($cantidadVentasMensual, 'dia')); ?>,
                datasets: [{
                    label: 'Ventas',
                    data: <?php echo json_encode(array_column($cantidadVentasMensual, 'cantidad_ventas')); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Gráfico de formas de pago
        new Chart(document.getElementById('formasPagoChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($metodosPagosMes, 'metodo')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($metodosPagosMes, 'cantidad')); ?>,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Gráfico de top productos
        new Chart(document.getElementById('topProductosChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($productosTopVentas, 'producto')); ?>,
                datasets: [{
                    label: 'Ventas',
                    data: <?php echo json_encode(array_column($productosTopVentas, 'cantidad')); ?>,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Recargar la página dos veces al recargarla una vez
        window.addEventListener("load", function() {
            if (!sessionStorage.getItem("reloaded")) {
                sessionStorage.setItem("reloaded", "true");
                setTimeout(function() {
                    location.reload();
                }, 100);
            } else {
                sessionStorage.removeItem("reloaded");
            }
        });
    </script>
</body>

</html>
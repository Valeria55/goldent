<!-- Vista: dashboard.php -->
<?php
$tipo_filtro = isset($_GET['tipo_filtro']) ? $_GET['tipo_filtro'] : 'anual';

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

$controller = new dashboardOperativoController();
$aniosDisponibles = $controller->ctrlAniosConIngresosValidos()['anios'];
$params = ['year' => $year, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin];
$datos = $controller->ctrlObtenerDatos($tipo_filtro, $params);
?>

<div class="dashboard-container">
    <!-- Formulario de filtros -->
    <form method="GET" class="filters-form mb-3" onsubmit="guardarPosicionScroll(); mergeQueryParams(this); return false;">

        <input type="hidden" name="c" value="<?= htmlspecialchars($_GET['c'] ?? 'dashboardOperativo') ?>">

        <div class="filter-group">
            <label for="tipo_filtro">Tipo de Visualización:</label>
            <select id="tipo_filtro" name="tipo_filtro" onchange="actualizarFiltros(); this.form.submit();">
                <option value="anual" <?= $tipo_filtro === 'anual' ? 'selected' : '' ?>>Anual</option>
                <option value="mensual" <?= $tipo_filtro === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                <option value="semanal" <?= $tipo_filtro === 'semanal' ? 'selected' : '' ?>>Semanal</option>
            </select>
        </div>

        <!-- Filtros dinámicos -->
        <div id="filtros_adicionales">
            <?php if ($tipo_filtro === 'mensual'): ?>
                <div class="filter-group" id="filtro_year">
                    <label for="year">Año:</label>
                    <select name="year" id="year">
                    <?php foreach ($aniosDisponibles as $anioDisponible): ?>
                    <option value="<?= $anioDisponible ?>" <?= $anioDisponible == $anio ? 'selected' : '' ?>>
                        <?= $anioDisponible ?>
                    </option>
                    <?php endforeach; ?>
                    </select>
                </div>

            <?php elseif ($tipo_filtro === 'semanal'): ?>
                <div class="filter-group" id="filtro_fechas">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" max="<?= date('Y-m-d') ?>" required onchange="setEndDate();">

                    <label for="fecha_fin">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" max="<?= date('Y-m-d') ?>" readonly>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-actualizar mt-3">Actualizar Gráfico</button>
    </form>

    <!-- Contenedor del gráfico -->
    <div class="chart-container">
        <canvas id="financialChart"></canvas>
    </div>
</div>

<script>
    function guardarPosicionScroll() {
        localStorage.setItem('scrollPos', window.scrollY);
    }

    // Función para establecer automáticamente la fecha de fin al seleccionar una fecha de inicio
    function setEndDate() {
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaFinInput = document.getElementById('fecha_fin');

        if (fechaInicioInput.value) {
            const startDate = new Date(fechaInicioInput.value);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 7);

            const endDateString = endDate.toISOString().split('T')[0]; // Formato de fecha 'YYYY-MM-DD'
            fechaFinInput.value = endDateString;

            // Guardar el rango de fechas en localStorage
            localStorage.setItem('fechaInicio', fechaInicioInput.value);
            localStorage.setItem('fechaFin', endDateString);
        }
    }

    // Restaurar el rango de fechas desde localStorage al cargar la página
   

    // Restaura la URL almacenada en localStorage al cargar la página
    window.addEventListener('load', function() {
        const fechaInicio = localStorage.getItem('fechaInicio');
        const fechaFin = localStorage.getItem('fechaFin');
        if (fechaInicio && fechaFin) {
            document.getElementById('fecha_inicio').value = fechaInicio;
            document.getElementById('fecha_fin').value = fechaFin;
        }
    });

    function mergeQueryParams(form) {
    // Crear un objeto URL con la URL actual
    const url = new URL(window.location.href);

    // Obtener los datos del formulario
    const formData = new FormData(form);

    // Actualizar o añadir parámetros del formulario a la URL
    formData.forEach((value, key) => {
        url.searchParams.set(key, value);
    });

    // Navegar a la URL actualizada
    window.location.href = url.toString();
}


    const scrollPos = localStorage.getItem('scrollPos');
    if (scrollPos) {
        window.scrollTo(0, parseInt(scrollPos, 10));
        localStorage.removeItem('scrollPos');
    }

    // Función para actualizar los filtros según el tipo de filtro seleccionado y guardar la posición
    function actualizarFiltros() {
        guardarPosicionScroll();
        guardarURL();
        const tipoFiltro = document.getElementById('tipo_filtro').value;
        const filtrosAdicionales = document.getElementById('filtros_adicionales');

        let html = '';
        switch (tipoFiltro) {
            case 'mensual':
                html = `
                   <div class="filter-group" id="filtro_year">
                    <label for="year">Año:</label>
                    <select name="year" id="year">
                        <?php
                        $current_year = date('Y');
                        for ($y = $current_year; $y >= $current_year - 5; $y--) {
                            $selected = $y == $year ? 'selected' : '';
                            echo "<option value=\"$y\" $selected>$y</option>";
                        }
                        ?>
                    </select>
                </div>
        `;
                break;
            case 'semanal':
                html = ` 
        <div class="filter-group" id="filtro_fechas" >
        <label for="fecha_inicio"> Fecha Inicio: </label> 
        <input type="date" id ="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" max="<?= date('Y-m-d') ?>" required onchange="setEndDate();">
        <label for="fecha_fin"> Fecha Fin: </label> 
        <input type="date" id="fecha_fin" name="fecha_fin"
    value="<?= htmlspecialchars($fecha_fin) ?>"
    max="<?= date('Y-m-d') ?>"
    readonly >
        </div>
    `;
                break;
            default:
                html = '';
        }
        filtrosAdicionales.innerHTML = html;
    }

    // Código para inicializar el gráfico de datos financieros
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialChart').getContext('2d');
        const datos = <?= json_encode($datos) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: datos.periodo,
                datasets: [{
                        label: 'Ingresos',
                        data: datos.total_ingresos,
                        borderColor: '#00C3FF',
                        backgroundColor: 'rgba(0, 195, 255, 0.1)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Egresos',
                        data: datos.total_egresos,
                        borderColor: '#EC7249',
                        backgroundColor: 'rgba(236, 114, 73, 0.1)',
                        borderWidth: 2,
                        fill: true
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
                            text: 'Cantidad en GS'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Periodo'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
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

<style>
    /* Estilos CSS según tus necesidades */
</style>
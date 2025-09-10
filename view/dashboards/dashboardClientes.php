<?php
$controller = new dashboardClientesController();
//obtener los años con registros válidos
$aniosDisponibles = $controller->ctrlAniosConIngresosValidos()['anios'];

// Verifica si se ha seleccionado un año en el GET, por defecto 2024
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

// Traer los ingresos y egresos totales del año y el balance


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graficos Operacional</title>
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
    <div id="bootstrap5-section" class="text-center">
        <form method="GET" action="" class="mb-3">
            <input type="hidden" name="c" value="dashboardClientes"> <!-- Campo oculto para conservar el parámetro c -->
            <label for="anio">Seleccionar Año:</label>
            <select name="anio" id="anio">
                <?php foreach ($aniosDisponibles as $anioDisponible): ?>
                    <option value="<?= $anioDisponible ?>" <?= $anioDisponible == $anio ? 'selected' : '' ?>>
                        <?= $anioDisponible ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filtrar</button>
        </form>


        <div class="container mt-4">
            <div class="row">
                <div class="col">
                    <div class="card-body card h-100 shadow-sm" style="border-radius: 25px;">
                        <h2 class="text-center">Pedidos y Ventas por cliente</h2>
                        <h4>Muestra los clientes más rentables según los pedidos realizados, destacando los clientes VIP.</h4>
                        <?php include 'graficos/pedidosVentasCliente.php'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <div class="row">
                <div class="col">
                    <div class="card-body card h-100 shadow-sm" style="border-radius: 25px;">
                        <h2 class="text-center">Tasa de Conversión del año <?php echo $anio ?></h2>
                        <?php include 'graficos/tasaConversion.php'; ?>
                    </div>
                </div>
            </div>
        </div>



    </div>
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
    </script>
</body>

</html>
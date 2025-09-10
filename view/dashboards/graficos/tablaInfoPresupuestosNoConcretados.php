<?php
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'ingreso_total';
$orderDir = isset($_GET['orderDir']) ? $_GET['orderDir'] : 'DESC';

$controller = new dashboardClientesController();
$datos = $controller->ctrltablaInfoPresupuestosNoConcretados($anio);

$id_presupuesto = $datos['id_presupuesto'];
$id_cliente = $datos['id_cliente'];
$nombre_cliente = $datos['nombre_cliente'];
$ingreso_total = $datos['ingreso_total'];
$totalPagado = $datos['totalPagado'];
$fecha_presupuesto = $datos['fecha_presupuesto'];
$falta_por_pagar = $datos['falta_por_pagar'];

function generateUrl($newOrderBy, $currentOrderBy, $currentOrderDir)
{
    $params = [
        'c' => 'dashboardClientes',
        'anio' => $_GET['anio'] ?? date('Y'),
        'orderBy' => $newOrderBy,
        'orderDir' => ($newOrderBy === $currentOrderBy && $currentOrderDir === 'ASC') ? 'DESC' : 'ASC'
    ];
    return '?' . http_build_query($params);
}

function getSortIconClass($field, $currentOrderBy, $currentOrderDir)
{
    if ($field !== $currentOrderBy) {
        return 'fas fa-sort text-gray-400';
    }
    return $currentOrderDir === 'ASC' ? 'fas fa-sort-up text-primary' : 'fas fa-sort-down text-primary';
}
?>

<style>
    .sortable-header {
        cursor: pointer;
        transition: background-color 0.2s;
        position: relative;
        padding-right: 25px !important;
    }

    .sortable-header:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .sortable-header a {
        color: #2c3e50;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px;
    }

    .sortable-header a:hover {
        color: #000;
    }

    .sort-icon {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
    }

    .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #6c757d #f8f9fa;
    }

    .table-responsive::-webkit-scrollbar {
        width: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f8f9fa;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #6c757d;
        border-radius: 4px;
    }

    .sort-highlight {
        animation: highlightSort 0.5s ease-in-out;
    }

    @keyframes highlightSort {
        0% {
            background-color: rgba(0, 123, 255, 0.1);
        }

        100% {
            background-color: transparent;
        }
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md">
            <div class="table-responsive m-3" style="max-height: 400px; overflow-y: auto; padding:0px;">
                <table class="table table-hover text-center">
                    <thead style="background-color: rgba(75, 192, 192, 0.7);">
                        <tr>
                            <th class="text-center">Id_presupuesto</th>
                            <th class="text-center">ID Cliente</th>
                            <th class="text-center sortable-header">
                                <a data-href="<?= generateUrl('fecha_presupuesto', $orderBy, $orderDir) ?>"
                                    data-sort="fecha_presupuesto">
                                    Fecha del presupuesto
                                    <i class="sort-icon <?= getSortIconClass('fecha_presupuesto', $orderBy, $orderDir) ?>"></i>
                                </a>
                            </th>
                            <th class="text-center">Nombre Cliente</th>
                            <th class="text-center sortable-header">
                                <a data-href="<?= generateUrl('ingreso_total', $orderBy, $orderDir) ?>"
                                    data-sort="ingreso_total">
                                    Ingreso Presupuestado Gs
                                    <i class="sort-icon <?= getSortIconClass('ingreso_total', $orderBy, $orderDir) ?>"></i>
                                </a>
                            </th>
                            <th class="text-center">Total pagado Gs</th>
                            <th class="text-center sortable-header">
                                <a data-href="<?= generateUrl('falta_por_pagar', $orderBy, $orderDir) ?>"
                                    data-sort="falta_por_pagar">
                                    Falta por pagar Gs
                                    <i class="sort-icon <?= getSortIconClass('falta_por_pagar', $orderBy, $orderDir) ?>"></i>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($id_presupuesto as $key => $cod): ?>
                            <tr>
                                <td><?= number_format($cod) ?></td>
                                <td><?= number_format($id_cliente[$key]) ?></td>
                                <td><?= date('Y-m-d', strtotime($fecha_presupuesto[$key])) ?></td>
                                <td><?= htmlspecialchars($nombre_cliente[$key]) ?></td>
                                <td><?= number_format($ingreso_total[$key]) ?></td>
                                <td><?= number_format($totalPagado[$key]) ?></td>
                                <td><?= number_format($falta_por_pagar[$key]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Guardar la posición de desplazamiento antes de recargar
    document.querySelectorAll('.sortable-header a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            localStorage.setItem('scrollPosition', window.scrollY);
            window.location.href = this.getAttribute('data-href');
        });
    });

    // Restaurar la posición de desplazamiento después de cargar
    window.addEventListener('load', function() {
        if (localStorage.getItem('scrollPosition') !== null) {
            window.scrollTo(0, localStorage.getItem('scrollPosition'));
            localStorage.removeItem('scrollPosition');
        }
    });

    // Guardar la posición de desplazamiento de la tabla
    document.querySelector('.table-responsive').addEventListener('scroll', function() {
        localStorage.setItem('tableScrollPosition', this.scrollTop);
    });

    // Restaurar la posición de desplazamiento de la tabla
    window.addEventListener('load', function() {
        const tableResponsive = document.querySelector('.table-responsive');
        if (localStorage.getItem('tableScrollPosition') !== null) {
            tableResponsive.scrollTop = localStorage.getItem('tableScrollPosition');
            localStorage.removeItem('tableScrollPosition');
        }
    });
</script>
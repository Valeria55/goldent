<?php

require_once 'model/dashboardClientes.php';
require_once 'model/cierre.php';

class dashboardClientesController {
    private $model;
    private $cierre;

    public function __CONSTRUCT()
    {
        // Instanciar el modelo
        $this->model = new mdlDashboardClientes();
        $this->cierre = new cierre();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/dashboards/dashboardClientes.php';
        require_once 'view/footer.php';
    }

    public function ctrlAniosConIngresosValidos()
    {
        $datos = $this->model->mdlAniosConIngresosValidos();

        $anios = [];

        foreach ($datos as $fila) {
            $anios[] = $fila['anio'];
        }

        // Retornar los datos como array asociativo
        return [
            'anios' => $anios
        ];
    }

    public function getDashboardData()
    {
        try {
            // Obtener rendimiento de ventas
            $customerSales = $this->model->getCustomerSalesPerformance(15);

            // Obtener estadísticas de VIP
            $vipStats = $this->model->getVIPCustomerStats();

            return [
                'customerSales' => $customerSales,
                'vipStats' => $vipStats
            ];
        } catch (Exception $e) {
            // Manejar errores
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public function ctrlTasaConversion($anio)
    {
        $datos = $this->model->mdlTasaConversion($anio);
        $total_presupuestos = array_sum(array_column($datos, 'total_presupuestos'));
        $concretados = array_sum(array_column($datos, 'concretados'));
        $no_concretados = array_sum(array_column($datos, 'no_concretados'));

        // Verifica que $total_presupuestos no sea cero para evitar división por cero
        if ($total_presupuestos > 0) {
            $tasaConversion = ($concretados / $total_presupuestos) * 100;
        } else {
            $tasaConversion = 0;
        }

        $tasaNoConversion = 100 - $tasaConversion;

        return [
            'tasaConversion' => $tasaConversion,
            'tasaNoConversion' => $tasaNoConversion,
            'total_presupuestos' => $total_presupuestos,
            'concretados' => $concretados,
            'no_concretados' => $no_concretados
        ];
    }

    public function ctrltablaInfoPresupuestosNoConcretados($anio)
{
    // Obtener parámetros de ordenamiento de $_GET
    $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'ingreso_total';
    $orderDir = isset($_GET['orderDir']) ? $_GET['orderDir'] : 'DESC';

    // Validar campos permitidos
    $allowedFields = ['fecha_presupuesto', 'ingreso_total', 'nombre_cliente', 'falta_por_pagar'];
    $orderBy = in_array($orderBy, $allowedFields) ? $orderBy : 'ingreso_total';
    $orderDir = in_array(strtoupper($orderDir), ['ASC', 'DESC']) ? strtoupper($orderDir) : 'DESC';

    // Pasar los parámetros al modelo
    $datos = $this->model->mdltablaInfoPresupuestosNoConcretados($anio, $orderBy, $orderDir);

    // Debug - comenta estas líneas después
    error_log("Ordenando por: " . $orderBy . " " . $orderDir);

    $id_presupuesto = array_column($datos, 'id_presupuesto');
    $id_cliente = array_column($datos, 'id_cliente');
    $nombre_cliente = array_column($datos, 'nombre_cliente');
    $totalPagado = array_column($datos, 'total');
    $ingreso_total = array_column($datos, 'ingreso_total');
    $fecha_presupuesto = array_column($datos, 'fecha_presupuesto');
    $falta_por_pagar = array_column($datos, 'falta_por_pagar');

    return [
        'id_presupuesto' => $id_presupuesto,
        'id_cliente' => $id_cliente,
        'nombre_cliente' => $nombre_cliente,
        'totalPagado' => $totalPagado,
        'ingreso_total' => $ingreso_total,
        'fecha_presupuesto' => $fecha_presupuesto,
        'falta_por_pagar' => $falta_por_pagar
    ];
}
}
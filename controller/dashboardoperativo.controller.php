<?php

require_once 'model/dashboardOperativo.php';
require_once 'model/cierre.php';

class dashboardOperativoController
{
    private $model;
    private $cierre;

    public function __CONSTRUCT()
    {
        // Instanciar el modelo
        $this->model = new mdlDashboardOperativo();
        $this->cierre = new cierre();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/dashboards/dashboardOperativo.php';
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

    public function ctrlComparativaIngresosEgresos($anio)
    {
        $datos = $this->model->mdlComparativaIngresoEgreso($anio);

        // Procesar los datos para la vista

        $ingresosMensual = array_column($datos, 'total_ingresos'); // Ingresos por mes
        $egresosMensual = array_column($datos, 'total_egresos'); // Egresos por mes

        // Array de nombres de meses en español
        $nombresMeses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        // Obtener los números de los meses de la consulta SQL
        $mesesNumeros = array_column($datos, 'mes');

        // Convertir los números de los meses a sus nombres correspondientes
        $meses = array_map(function ($numeroMes) use ($nombresMeses) {
            return $nombresMeses[$numeroMes];
        }, $mesesNumeros);

        // Retornar los datos como array asociativo
        return [
            'meses' => $meses,
            'ingresosMensual' => $ingresosMensual,
            'egresosMensual' => $egresosMensual,
            'anio' => $anio,
        ];
    }

    public  function ctrlIngresosEgresosTotales($anio)
    {
        $datos =  $this->model->mdlIngresosEgresosTotalesDelAnio($anio);

        $totalIngresos = 0;
        $totalEgresos = 0;
        $balance = 0;

        $totalIngresos = $datos['total_ingresos'];
        $totalEgresos = $datos['total_egresos'];
        $balance = $datos['balance'];

        return [
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'balance' => $balance
        ];
    }

    public function obtenerDatosVentas()
    {
        try {
            $anio_actual = date('Y');
            $datos_ventas = $this->model->getVentasAnuales($anio_actual);


            $resultado = [
                'ventas_actual' => $datos_ventas['anio_actual'],
                'ventas_anterior' => $datos_ventas['anio_anterior'],

            ];



            return $resultado;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener los datos'
            ]);
        }
    }

    public function ctrlObtenerDatos($tipo_filtro, $params = [])
    {
        try {
            $datos = [];
            switch ($tipo_filtro) {
                case 'anual':
                    $datos = $this->model->mdlDatosAnuales();
                    break;

                case 'mensual':
                    if (!isset($params['year'])) {
                        throw new Exception("Faltan parámetros para el filtro mensual");
                    }
                    $datos = $this->model->mdlDatosMensuales($params['year']);
                    break;

                case 'semanal':
                    if (!isset($params['fecha_inicio']) || !isset($params['fecha_fin'])) {
                        throw new Exception("Faltan parámetros para el filtro semanal");
                    }
                    $datos = $this->model->mdlDatosDiarios($params['fecha_inicio'], $params['fecha_fin']);
                    break;

                default:
                    throw new Exception("Tipo de filtro no válido");
            }

            return [
                'periodo' => array_column($datos, 'periodo'),
                'total_ingresos' => array_column($datos, 'total_ingresos'),
                'total_egresos' => array_column($datos, 'total_egresos')
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function ctrlVentasDeProductos($anio)
    {
        $datos =  $this->model->mdlVentasProductosCategoria($anio);

        $codigo = array_column($datos, 'codigo');
        $producto = array_column($datos, 'producto');
        $marca = array_column($datos, 'marca');
        $categoria = array_column($datos, 'categoria');
        $precio_costo = array_column($datos, 'precio_costo');
        $precio_unitario = array_column($datos, 'precio_unitario');
        $ingreso_total = array_column($datos, 'ingreso_total');
        $gasto_total = array_column($datos, 'gasto_total');
        $diferencia = array_column($datos, 'diferencia');

        return [
            'producto' => $producto,
            'ingreso_total' => $ingreso_total,
            'gasto_total' => $gasto_total,
            'diferencia' => $diferencia,
            'codigo' => $codigo,
            'categoria' => $categoria,
            'marca' => $marca,
            'precio_costo' => $precio_costo,
            'precio_unitario' => $precio_unitario,
        ];
    }

    public function ctrlVRentabilidadProductosCategorias($anio, $filtro)
    {
        return $this->model->mdlRentabilidadProductosCategorias($anio, $filtro);
    }

    public function ctrlAvisoDeStock($anio)
    {
        $datos =  $this->model->mdlAvisoDeStock($anio);

        $stock = array_column($datos, 'stock');
        $producto = array_column($datos, 'producto');
        $codigo = array_column($datos, 'codigo');
        $cantidad_ventas = array_column($datos, 'cantidad_ventas');
        $total_ventas_gs = array_column($datos, 'total_ventas_gs');
        $beneficios = array_column($datos, 'beneficios');

        return [
            'stock' => $stock,
            'producto' => $producto,
            'codigo' => $codigo,
            'cantidad_ventas' => $cantidad_ventas,
            'total_ventas_gs' => $total_ventas_gs,
            'beneficios' => $beneficios
        ];
    }
}

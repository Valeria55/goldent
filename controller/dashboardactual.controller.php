<?php

require_once 'model/dashboardActual.php';
require_once 'model/cierre.php';
require_once 'model/deuda.php';

class dashboardActualController
{
    private $model;
    private $cierre;
    private $deuda;

    public function __CONSTRUCT()
    {
        // Instanciar el modelo
        $this->model = new mdlDashboardActual();
        $this->cierre = new cierre();
        $this->deuda = new deuda();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/dashboards/dahsboardActual.php';
        require_once 'view/footer.php';
    }

    public function ctrlMontoventas()
    {
        $datos = $this->model->mdlMontoventas();
        $monto_total = (float) $datos['monto_total'];
        return [
            'monto_total' => $monto_total
        ];
    }

    public function ctrlVentasRecientes($limit = 20)
    {
        $datos = $this->model->mdlVentasRecientes($limit);
        return $datos;
    }

    public function ctrlProductosTopVentas($limit = 5)
    {
        $datos = $this->model->mdlProductosTopVentas($limit);
        return $datos;
    }

    public function ctrlComparativaMensual($numMeses = 6)
    {
        $datos = $this->model->mdlComparativaMensual($numMeses);
        return $datos;
    }

    
    public function ctrlLucroEgresosMensual()
    {
        $datos = $this->model->LucroEgresoMensual();
        if ($datos) {
            return [
                'utilidad_bruta' => (float) $datos['utilidad_bruta'],
                'lucro' => (float) $datos['lucro'],
                'gastos' => (float) $datos['total_gastos']
            ];
        }
        return ['utilidad_bruta' => 0,'lucro' => 0, 'gastos' => 0];
    }

    public function ctrlLucroMensualPorcentajeCambio(){
        return $this->model->LucroMensualPorcentajeCambio();
    }


    public function ctrlEgresosMensualPorcentaje() {
        
        return $this->model->EgresosMensualesPorcentajeCambio();
    }

    public function ctrlCantidadVentasSemanal()
    {
        $datos = $this->model->mdlCantidadVentasSemanal();

        // Mapeo de nombres de días en inglés a español
        $dias_espanol = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];

        // Convertir los nombres de los días
        foreach ($datos as &$dato) {
            $dato['dia'] = $dias_espanol[$dato['dia']] ?? $dato['dia'];
        }

        return $datos;
    }
    
    public function ctrlCantidadVentasMensual()
    {
        $datos = $this->model->mdlCantidadVentasMensual();

        // Mapeo de nombres de días en inglés a español
        $dias_espanol = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];

        // Convertir los nombres de los días
        foreach ($datos as &$dato) {
            $dato['dia'] = $dias_espanol[$dato['dia']] ?? $dato['dia'];
        }

        return $datos;
    }

    public function ctrlMetodosPagosMes()
    {
        $datos = $this->model->mdlMetodosPagosMes();
        return $datos;
    }

    public function ctrlDetalleVentasSemanal()
    {
        $datos = $this->model->mdlDetalleVentasSemanal();
        return $datos;
    }

    public function ctrlDetalleVentasMensual()
    {
        $datos = $this->model->mdlDetalleVentasMensual();
        return $datos;
    }

    public function ctrlVentasDiariasPorcentajeCambio()
    {
        $datos = $this->model->mdlVentasDiariasPorcentajeCambio();

        if ($datos === false) {
            echo "Error al obtener las ventas diarias y el porcentaje de cambio.";
            return;
        }

        return $datos;
        
    }

    public function ctrlVentasPorHoras()
    {
        return $this->model->mdlVentasPorHorasHoy();
    }

    public function ctrlVentasMensualesPorcentajeCambio(){
        return $this->model->mdlVentasMensualesPorcentajeCambio();
    }

    public function ctrlVentasDiariasMesActual(){
        return $this->model->mdlVentasDiariasMesActual();
    }

   

    public function ctrlIngresosDiasSemanaActual(){
        return $this->model->mdlIngresosDiasSemanaActual();
    }

    public function ctrlEgresosSemanaActual() {
    $egresosDiasSemana = $this->model->obtenerEgresosPorDiaSemana();
    
    // Ensure all days are represented
    $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    $egresosOrdenados = array_fill(0, 7, 0);
    
    foreach ($egresosDiasSemana as $egreso) {
        $key = array_search($egreso['dia'], $diasSemana);
        if ($key !== false) {
            $egresosOrdenados[$key] = floatval($egreso['total_egresos']);
        }
    }
    
    return $egresosOrdenados;
}

public function ctrlListarDeudas(){
    return $this->deuda->Listar();
}



}



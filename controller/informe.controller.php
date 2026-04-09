<?php
require_once 'model/venta.php';
require_once 'model/egreso.php';
require_once 'model/ingreso.php';
require_once 'model/deuda.php';
require_once 'model/acreedor.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';

class informeController {
    
    private $venta;
    private $egreso;
    private $ingreso;
    private $deuda;
    private $acreedor;
    private $cliente;
    public $model; // Propiedad para compatibilidad con vistas heredadas
    private $cierre;

    public function __CONSTRUCT() {
        $this->venta = new venta();
        $this->egreso = new egreso();
        $this->ingreso = new ingreso();
        $this->deuda = new deuda();
        $this->acreedor = new acreedor();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
    }

    public function Index() {
        require_once 'view/header.php';
        require_once 'view/informe/index.php';
        require_once 'view/footer.php';
    }

    public function Generar() {
        $tipo = $_REQUEST['tipo'];
        $desde = $_REQUEST['desde'];
        $hasta = $_REQUEST['hasta'];

        switch ($tipo) {
            case 'ingreso':
                require_once 'view/informe/ingreso_rango_pdf.php';
                break;
            case 'egreso':
                $this->model = $this->egreso; 
                require_once 'view/informes/egresosrangopdf.php';
                break;
            case 'deuda':
                require_once 'view/informe/deuda_rango_pdf.php';
                break;
            case 'acreedor':
                require_once 'view/informe/acreedor_rango_pdf.php';
                break;
            case 'venta_factura':
                require_once 'view/informe/venta_factura_pdf.php';
                break;
            case 'venta_sin_factura':
                require_once 'view/informe/venta_sin_factura_pdf.php';
                break;
            case 'todos':
                require_once 'view/informe/todos_pdf.php';
                break;
            default:
                header('Location: index.php?c=informe');
                break;
        }
    }
    public function GenerarExcel(){
        $tipo = $_REQUEST['tipo'];
        $desde = $_REQUEST['desde'];
        $hasta = $_REQUEST['hasta'];

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=reporte_" . $tipo . "_" . date('Y-m-d') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        switch ($tipo) {
            case 'ingreso':
                require_once 'view/informe/ingreso_rango_excel.php';
                break;
            case 'egreso':
                $this->model = $this->egreso; 
                require_once 'view/informe/egreso_rango_excel.php';
                break;
            case 'deuda':
                require_once 'view/informe/deuda_rango_excel.php';
                break;
            case 'acreedor':
                require_once 'view/informe/acreedor_rango_excel.php';
                break;
            default:
                require_once 'view/informe/generico_excel.php';
                break;
        }
    }
}

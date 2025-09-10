<?php
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/cliente.php';
require_once 'model/venta.php';
require_once 'model/compra.php';
require_once 'model/ingreso.php';
require_once 'model/egreso.php';
require_once 'model/caja.php';
require_once 'model/metodo.php';


class cierreController
{

    private $model;
    private $cierre;
    private $usuario;
    private $cliente;
    private $venta;
    private $compra;
    private $ingreso;
    private $egreso;
    private $caja;
    private $metodo;


    public function __CONSTRUCT()
    {
        $this->model = new cierre();
        $this->cierre = new cierre();
        $this->usuario = new usuario();
        $this->cliente = new cliente();
        $this->venta = new venta();
        $this->compra = new compra();
        $this->ingreso = new ingreso();
        $this->egreso = new egreso();
        $this->caja = new caja();
        $this->metodo = new metodo();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/cierre/cierre.php';
        require_once 'view/footer.php';
    }

    public function Balance()
    {
        require_once 'view/header.php';
        require_once 'view/informes/balance.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/egreso/egreso.php';
    }

    public function Movimientos()
    {
        if (!isset($_SESSION)) session_start();
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {
            require_once 'view/cierre/movimientos.php';
        } else {
            require_once 'view/cierre/movimientos.php';
            //echo "<h1>Debe hacer apertura de caja</h1>";
        }
        require_once 'view/footer.php';
    }

    public function activas()
    {

        require_once 'view/header.php';
        require_once 'view/cierre/sesiones-activas.php';
        require_once 'view/footer.php';
    }

    public function detalles()
    {

        require_once 'view/header.php';
        require_once 'view/cierre/detalles_cierres.php';
        require_once 'view/footer.php';
    }
    public function CierrePDF()
    {
        $cierre = new cierre();

        $cierre_id = $this->model->Obtener($_REQUEST['id_cierre']);
        require_once 'view/informes/cierreCajaPDF_porMoneda.php';
    }

    public function Crud()
    {
        $egreso = new egreso();

        if (isset($_REQUEST['id'])) {
            $egreso = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/egreso/egreso-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $egreso = new egreso();

        if (isset($_REQUEST['id'])) {
            $egreso = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/egreso/egreso-editar.php';
    }


    public function Apertura()
    {

        $cierre = new cierre();

        if (!isset($_SESSION)) session_start();
        $cierre->fecha_apertura = date("Y-m-d H:i");
        $cierre->fecha_cierre = null;
        $cierre->id_usuario = $_SESSION['user_id'];
        $cierre->id_caja = $_REQUEST['id_caja'];
        $cierre->monto_apertura = $_REQUEST['monto_apertura']; // GS
        $cierre->apertura_rs = $_REQUEST['apertura_rs']; // RS
        $cierre->apertura_usd = $_REQUEST['apertura_usd']; // USD
        $cierre->monto_cierre = null;
        $cierre->cot_dolar = $_REQUEST['cot_dolar'];
        $cierre->cot_real = $_REQUEST['cot_real'];

        $this->model->Registrar($cierre);

        header('Location: index.php?c=venta_tmp');
    }    public function Cierre()
    {

        $cierre = new cierre();

        if (!isset($_SESSION)) session_start();
        $cierre->fecha_cierre = date("Y-m-d H:i");
        $cierre->monto_cierre = $_REQUEST['monto_cierre'];
        $cierre->monto_cierre_rs = isset($_REQUEST['monto_cierre_rs']) ? $_REQUEST['monto_cierre_rs'] : 0;
        $cierre->monto_cierre_usd = isset($_REQUEST['monto_cierre_usd']) ? $_REQUEST['monto_cierre_usd'] : 0;
        $cierre->id_usuario = $_SESSION['user_id'];

        $cierreV = new cierre();
        $cierreV = $this->model->Consultar($_SESSION['user_id']);

        $this->model->Cierre($cierre);

        session_destroy();

        //header('Location: index.php?c=cierre&a=CierrePDF&id_cierre='.$cierreV->id);
        header("Location:login.php?logout=1");
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=egreso');
    }

    public function EliminarPago()
    {

        $egreso = new egreso();
        $egreso = $this->model->Obtener($_REQUEST['id']);

        $acreedor = new acreedor();
        $acreedor->id = $egreso->id_acreedor;
        $acreedor->monto = $egreso->monto;

        $this->model->Eliminar($_REQUEST['id']);
        $this->acreedor->SumarSaldo($acreedor);
        header('Location: index.php?c=egreso');
    }

    public function obtenerCotizaciones()
    {
        if (!isset($_SESSION)) session_start();
        
        $cierre = $this->cierre->Consultar($_SESSION['user_id']);
        
        $cotizaciones = array(
            'usd' => $cierre->cotizacion_usd ?? 7500,
            'rs' => $cierre->cotizacion_rs ?? 1500
        );
        
        echo json_encode($cotizaciones);
    }

    public function actualizar()
    {
        try {
            $cierre = new cierre();
            
            // Asignar los valores del formulario
            $cierre->id = $_POST['id'];
            $cierre->monto_apertura = $_POST['monto_apertura'];
            $cierre->monto_cierre = $_POST['monto_cierre'];
            $cierre->apertura_rs = $_POST['apertura_rs'];
            $cierre->monto_cierre_rs = $_POST['monto_cierre_rs'];
            $cierre->apertura_usd = $_POST['apertura_usd'];
            $cierre->monto_cierre_usd = $_POST['monto_cierre_usd'];
            
            // Actualizar en la base de datos
            if ($this->model->ActualizarMontos($cierre)) {
                // Redirigir con mensaje de éxito
                header('Location: index.php?c=cierre&mensaje=actualizado');
            } else {
                // Redirigir con mensaje de error
                header('Location: index.php?c=cierre&error=1');
            }
        } catch (Exception $e) {
            // Redirigir con mensaje de error
            header('Location: index.php?c=cierre&error=1');
        }
    }
}

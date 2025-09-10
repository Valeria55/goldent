<?php
require_once 'model/salida_tmp.php';
require_once 'model/salida.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/caja.php';
require_once 'model/pago_tmp.php';
require_once 'model/compra.php';
require_once 'model/compra_tmp.php';
require_once 'model/metodo.php';
require_once 'model/gift_card.php';
require_once 'model/presupuesto.php';
require_once 'model/devolucion_ventas.php';

class salida_tmpController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new salida_tmp();
        $this->salida_tmp = new salida_tmp();
        $this->compra_tmp = new compra_tmp();
        $this->compra = new compra();
        $this->usuario = new usuario();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->pago_tmp = new pago_tmp();
        $this->metodo = new metodo();
        $this->gift_card = new gift_card();
        $this->presupuesto = new presupuesto();
        $this->devolucion_ventas = new devolucion_ventas();
    }

    public function Index()
    {
        $fecha = date('Y-m-d');
        require_once 'view/header.php';
        require_once 'view/salida/nueva-salida.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/venta/nueva-salida.php';
    }



    public function Crud()
    {
        $salida_tmp = new salida_tmp();

        if (isset($_REQUEST['id'])) {
            $salida_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/salida_tmp/salida_tmp.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $salida_tmp = new salida_tmp();

        if (isset($_REQUEST['id'])) {
            $salida_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/salida_tmp/salida_tmp.php';
    }

    public function ObtenerMoneda()
    {
        $salida_tmp = new salida_tmp();

        $salida_tmp = $this->model->ObtenerMoneda();
    }

    public function Guardar()
    {

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        if (!isset($_SESSION)) session_start();
        $salida_tmp = new salida_tmp();

        $salida_tmp->id = 0;
        $salida_tmp->id_venta = 1;
        $salida_tmp->id_vendedor = $_SESSION['user_id'];
        $salida_tmp->id_producto = $_REQUEST['id_producto'];
        $salida_tmp->precio_venta = ($_REQUEST['precio_venta']) ? $_REQUEST['precio_venta'] : $pro->precio_minorista;
        $salida_tmp->cantidad = $_REQUEST['cantidad'];
        $salida_tmp->descuento = $_REQUEST['descuento'];
        $salida_tmp->fecha_venta = date("Y-m-d H:i");


        $salida_tmp->id > 0
            ? $this->model->Actualizar($salida_tmp)
            : $this->model->Registrar($salida_tmp);

        //header('Location: index.php?c=salida_tmp');
        require_once "view/salida/nueva-salida.php";
    }

    public function CancelarVenta()
    {

        $this->model->Vaciar();
        header('Location: index.php?c=salida_tmp');
        //header('Location: index.php?c=salida_tmp');
    }

    public function Moneda()
    {

        $salida_tmp = new salida_tmp();

        $salida_tmp->id = 0;
        $salida_tmp->reales = $_REQUEST['reales'];
        $salida_tmp->dolares = $_REQUEST['dolares'];
        $salida_tmp->monto_inicial = $_REQUEST['monto_inicial'];

        $this->model->Moneda($salida_tmp);

        header('Location: index.php?c=salida_tmp');
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        require_once "view/salida/nueva-salida.php";
        //header('Location: index.php?c=salida_tmp');
    }
}

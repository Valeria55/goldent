<?php
require_once 'model/transferencia_tmp.php';
require_once 'model/transferencia.php';
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

class transferencia_tmpController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new transferencia_tmp();
        $this->transferencia_tmp = new transferencia_tmp();
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
        require_once 'view/transferencia/nueva-transferencia.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/venta/nueva-transferencia.php';
    }



    public function Crud()
    {
        $transferencia_tmp = new transferencia_tmp();

        if (isset($_REQUEST['id'])) {
            $transferencia_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/transferencia_tmp/transferencia_tmp.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $transferencia_tmp = new transferencia_tmp();

        if (isset($_REQUEST['id'])) {
            $transferencia_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/transferencia_tmp/transferencia_tmp.php';
    }

    public function ObtenerMoneda()
    {
        $transferencia_tmp = new transferencia_tmp();

        $transferencia_tmp = $this->model->ObtenerMoneda();
    }

    public function Guardar()
    {

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        if (!isset($_SESSION)) session_start();
        $transferencia_tmp = new transferencia_tmp();

        $transferencia_tmp->id = 0;
        $transferencia_tmp->id_venta = 1;
        $transferencia_tmp->id_vendedor = $_SESSION['user_id'];
        $transferencia_tmp->id_producto = $_REQUEST['id_producto'];
        $transferencia_tmp->precio_venta = ($_REQUEST['precio_venta']) ? $_REQUEST['precio_venta'] : $pro->precio_minorista;
        $transferencia_tmp->cantidad = $_REQUEST['cantidad'];
        $transferencia_tmp->descuento = $_REQUEST['descuento'];
        $transferencia_tmp->fecha_venta = date("Y-m-d H:i");


        $transferencia_tmp->id > 0
            ? $this->model->Actualizar($transferencia_tmp)
            : $this->model->Registrar($transferencia_tmp);

        //header('Location: index.php?c=transferencia_tmp');
        require_once "view/transferencia/nueva-transferencia.php";
    }

    public function CancelarVenta()
    {

        $this->model->Vaciar();
        header('Location: index.php?c=transferencia_tmp');
        //header('Location: index.php?c=transferencia_tmp');
    }

    public function Moneda()
    {

        $transferencia_tmp = new transferencia_tmp();

        $transferencia_tmp->id = 0;
        $transferencia_tmp->reales = $_REQUEST['reales'];
        $transferencia_tmp->dolares = $_REQUEST['dolares'];
        $transferencia_tmp->monto_inicial = $_REQUEST['monto_inicial'];

        $this->model->Moneda($transferencia_tmp);

        header('Location: index.php?c=transferencia_tmp');
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        require_once "view/transferencia/nueva-transferencia.php";
        //header('Location: index.php?c=transferencia_tmp');
    }
}

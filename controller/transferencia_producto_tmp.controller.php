<?php
require_once 'model/transferencia_producto_tmp.php';
require_once 'model/transferencia_producto.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/caja.php';
require_once 'model/pago_tmp.php';
require_once 'model/metodo.php';
require_once 'model/gift_card.php';
class transferencia_producto_tmpController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new transferencia_producto_tmp();
        $this->transferencia_producto_tmp = new transferencia_producto_tmp();
        $this->transferencia_producto = new transferencia_producto();
        $this->usuario = new usuario();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->pago_tmp = new pago_tmp();
        $this->metodo = new metodo();
        $this->gift_card = new gift_card();
    }

    public function Index()
    {
        $fecha = date('Y-m-d');
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/nuevo-transferencia_producto.php';
        require_once 'view/footer.php';
    }
    public function Mayorista()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/nueva-transferencia_productomayor.php';
        require_once 'view/footer.php';
    }
    public function Editar()
    {

        $transferencia_producto = new transferencia_producto();

        if (isset($_REQUEST['id'])) {
            $transferencia_producto = $this->transferencia_producto->ObtenerUno($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_producto-editar.php';
        require_once 'view/footer.php';
    }


    public function Listar()
    {
        require_once 'view/transferencia_producto/nuevo-transferencia_producto.php';
    }



    public function Crud()
    {
        $transferencia_producto_tmp = new transferencia_producto_tmp();

        if (isset($_REQUEST['id'])) {
            $transferencia_producto_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/transferencia_producto_tmp/transferencia_producto_tmp-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $transferencia_producto_tmp = new transferencia_producto_tmp();

        if (isset($_REQUEST['id'])) {
            $transferencia_producto_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/transferencia_producto_tmp/transferencia_producto_tmp-editar.php';
    }

    public function ObtenerMoneda()
    {
        $transferencia_producto_tmp = new transferencia_producto_tmp();

        $transferencia_producto_tmp = $this->model->ObtenerMoneda();
    }

    public function Guardar()
    {

        //$producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        if (!isset($_SESSION)) session_start();
        $transferencia_producto_tmp = new transferencia_producto_tmp();

        $transferencia_producto_tmp->id = 0;
        $transferencia_producto_tmp->id_transferencia_producto = 1;
        $transferencia_producto_tmp->id_vendedor = $_SESSION['user_id'];
        $transferencia_producto_tmp->id_producto = $_REQUEST['id_producto'];
        $transferencia_producto_tmp->precio_venta = ($_REQUEST['precio_venta']) ? $_REQUEST['precio_venta'] : $pro->precio_minorista;
        $transferencia_producto_tmp->cantidad = $_REQUEST['cantidad'];
        $transferencia_producto_tmp->descuento = $_REQUEST['descuento'] ?? 0;
        $transferencia_producto_tmp->fecha_transferencia_producto = date("Y-m-d H:i");
        $transferencia_producto_tmp->codigo = $_REQUEST['codigo_producto'];

        $transferencia_producto_tmp->id > 0
            ? $this->model->Actualizar($transferencia_producto_tmp)
            : $this->model->Registrar($transferencia_producto_tmp);

        //header('Location: index.php?c=transferencia_producto_tmp');
        require_once "view/transferencia_producto/tabla_transferencia_producto.php";
    }
    public function Cancelartransferencia_producto()
    {

        $this->model->Vaciar();
        header('Location: index.php?c=transferencia_producto_tmp');
        //header('Location: index.php?c=venta_tmp');
    }
    public function GuardarMayorista()
    {

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        if (!isset($_SESSION)) session_start();
        $transferencia_producto_tmp = new transferencia_producto_tmp();

        $transferencia_producto_tmp->id = 0;
        $transferencia_producto_tmp->id_transferencia_producto = 1;
        $transferencia_producto_tmp->id_vendedor = $_SESSION['user_id'];
        $transferencia_producto_tmp->id_producto = $_REQUEST['id_producto'];
        $transferencia_producto_tmp->precio_venta = $_REQUEST['precio_venta'];
        $transferencia_producto_tmp->cantidad = $_REQUEST['cantidad'];
        $transferencia_producto_tmp->descuento = $_REQUEST['descuento'];
        $transferencia_producto_tmp->fecha_transferencia_producto = date("Y-m-d H:i");


        $transferencia_producto_tmp->id > 0
            ? $this->model->Actualizar($transferencia_producto_tmp)
            : $this->model->Registrar($transferencia_producto_tmp);

        header('Location: index.php?c=transferencia_producto_tmp&a=mayorista');
    }

    public function GuardarUno()
    {


        $transferencia_producto = new transferencia_producto();

        $costo = $_REQUEST['precio_costo'];
        $transferencia_producto = $_REQUEST['precio_venta'];

        $transferencia_producto->id = 0;
        $transferencia_producto->id_transferencia_producto = 1;
        $transferencia_producto->id_cliente = $_REQUEST['id_cliente'];
        $transferencia_producto->id_vendedor = $_REQUEST['id_transferencia_producto'];
        $transferencia_producto->id_producto = $_REQUEST['codigo'];
        $transferencia_producto->precio_costo = $_REQUEST['precio_costo'];
        $transferencia_producto->precio_venta = $_REQUEST['precio_venta'];
        $transferencia_producto->subtotal = $_REQUEST['subtotal'];
        $transferencia_producto->descuento = 0;
        $transferencia_producto->iva = 0;
        $transferencia_producto->total = $_REQUEST['total'];
        $transferencia_producto->comprobante = $_REQUEST['comprobante'];
        $transferencia_producto->nro_comprobante = $_REQUEST['nro_comprobante'];
        $transferencia_producto->cantidad = $_REQUEST['cantidad'];
        $transferencia_producto->fecha_transferencia_producto = $_REQUEST['fecha_transferencia_producto'];
        $transferencia_producto->metodo = $_REQUEST['metodo'];
        $transferencia_producto->banco = $_REQUEST['banco'];
        $transferencia_producto->contado = $_REQUEST['contado'];


        $transferencia_producto->id > 0
            ? $this->transferencia_producto->Actualizar($transferencia_producto_tmp)
            : $this->transferencia_producto->Registrar($transferencia_producto_tmp);


        header('Location: index.php?c=transferencia_producto_tmp&a=editar&id=' . $transferencia_producto->id_transferencia_producto);
    }

    public function Moneda()
    {

        $transferencia_producto_tmp = new transferencia_producto_tmp();

        $transferencia_producto_tmp->id = 0;
        $transferencia_producto_tmp->reales = $_REQUEST['reales'];
        $transferencia_producto_tmp->dolares = $_REQUEST['dolares'];
        $transferencia_producto_tmp->monto_inicial = $_REQUEST['monto_inicial'];

        $this->model->Moneda($transferencia_producto_tmp);

        header('Location: index.php?c=transferencia_producto_tmp');
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        require_once "view/transferencia_producto/tabla_transferencia_producto.php";
        //header('Location: index.php?c=transferencia_producto_tmp');
    }
}

<?php
require_once 'model/factura_tmp.php';
require_once 'model/factura.php';
require_once 'model/vendedor.php';
require_once 'model/venta.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/caja.php';
require_once 'model/pago_tmp.php';
require_once 'model/metodo.php';
require_once 'model/gift_card.php';
class factura_tmpController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new factura_tmp();
        $this->factura_tmp = new factura_tmp();
        $this->factura = new factura();
        $this->usuario = new usuario();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->pago_tmp = new pago_tmp();
        $this->metodo = new metodo();
        $this->gift_card = new gift_card();
        $this->venta = new venta();
    }

    public function Index()
    {
        $fecha = date('Y-m-d');
        require_once 'view/header.php';
        require_once 'view/factura/nuevo-factura.php';
        require_once 'view/footer.php';
    }
    public function Mayorista()
    {
        require_once 'view/header.php';
        require_once 'view/factura/nueva-facturamayor.php';
        require_once 'view/footer.php';
    }
    public function Editar()
    {

        $factura = new factura();

        if (isset($_REQUEST['id'])) {
            $factura = $this->factura->ObtenerUno($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/factura/factura-editar.php';
        require_once 'view/footer.php';
    }


    public function Listar()
    {
        require_once 'view/factura/nuevo-factura.php';
    }



    public function Crud()
    {
        $factura_tmp = new factura_tmp();

        if (isset($_REQUEST['id'])) {
            $factura_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/factura_tmp/factura_tmp-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $factura_tmp = new factura_tmp();

        if (isset($_REQUEST['id'])) {
            $factura_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/factura_tmp/factura_tmp-editar.php';
    }

    public function ObtenerMoneda()
    {
        $factura_tmp = new factura_tmp();

        $factura_tmp = $this->model->ObtenerMoneda();
    }

    public function Guardar()
    {

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        session_start();
        $factura_tmp = new factura_tmp();

        $factura_tmp->id = 0;
        $factura_tmp->id_factura = 1;
        $factura_tmp->id_vendedor = $_SESSION['user_id'];
        $factura_tmp->id_producto = $_REQUEST['id_producto'];
        $factura_tmp->precio_venta = $_REQUEST['precio_venta'];

        // if ($_REQUEST['precio_venta'] == $pro->precio_minorista){
        //     $factura_tmp->precio_venta = $pro->precio_minorista;
        // }else if ($_REQUEST['precio_venta'] == $pro->precio_mayorista){
        //     $factura_tmp->precio_venta = $pro->precio_mayorista;
        // }else{
        //     $factura_tmp->precio_venta = $pro->precio_turista;
        // }

        $factura_tmp->cantidad = $_REQUEST['cantidad'];
        $factura_tmp->descuento = $_REQUEST['descuento'];
        $factura_tmp->fecha_factura = date("Y-m-d H:i");


        $factura_tmp->id > 0
            ? $this->model->Actualizar($factura_tmp)
            : $this->model->Registrar($factura_tmp);

        //header('Location: index.php?c=factura_tmp');
        require_once "view/factura/tabla_factura.php";
    }
    public function Cancelarfactura()
    {

        $this->model->Vaciar();
        header('Location: index.php?c=factura_tmp');
        //header('Location: index.php?c=venta_tmp');
    }
    public function GuardarMayorista()
    {

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        session_start();
        $factura_tmp = new factura_tmp();

        $factura_tmp->id = 0;
        $factura_tmp->id_factura = 1;
        $factura_tmp->id_vendedor = $_SESSION['user_id'];
        $factura_tmp->id_producto = $_REQUEST['id_producto'];
        $factura_tmp->precio_venta = $_REQUEST['precio_venta'];
        $factura_tmp->cantidad = $_REQUEST['cantidad'];
        $factura_tmp->descuento = $_REQUEST['descuento'];
        $factura_tmp->fecha_factura = date("Y-m-d H:i");


        $factura_tmp->id > 0
            ? $this->model->Actualizar($factura_tmp)
            : $this->model->Registrar($factura_tmp);

        header('Location: index.php?c=factura_tmp&a=mayorista');
    }

    public function GuardarUno()
    {


        $factura = new factura();

        $costo = $_REQUEST['precio_costo'];
        $factura = $_REQUEST['precio_venta'];

        $factura->id = 0;
        $factura->id_factura = 1;
        $factura->id_cliente = $_REQUEST['id_cliente'];
        $factura->id_vendedor = $_REQUEST['id_factura'];
        $factura->id_producto = $_REQUEST['codigo'];
        $factura->precio_costo = $_REQUEST['precio_costo'];
        $factura->precio_venta = $_REQUEST['precio_venta'];
        $factura->subtotal = $_REQUEST['subtotal'];
        $factura->descuento = 0;
        $factura->iva = 0;
        $factura->total = $_REQUEST['total'];
        $factura->comprobante = $_REQUEST['comprobante'];
        $factura->nro_comprobante = $_REQUEST['nro_comprobante'];
        $factura->cantidad = $_REQUEST['cantidad'];
        $factura->fecha_factura = $_REQUEST['fecha_factura'];
        $factura->metodo = $_REQUEST['metodo'];
        $factura->banco = $_REQUEST['banco'];
        $factura->contado = $_REQUEST['contado'];


        $factura->id > 0
            ? $this->factura->Actualizar($factura_tmp)
            : $this->factura->Registrar($factura_tmp);


        header('Location: index.php?c=factura_tmp&a=editar&id=' . $factura->id_factura);
    }

    public function Moneda()
    {

        $factura_tmp = new factura_tmp();

        $factura_tmp->id = 0;
        $factura_tmp->reales = $_REQUEST['reales'];
        $factura_tmp->dolares = $_REQUEST['dolares'];
        $factura_tmp->monto_inicial = $_REQUEST['monto_inicial'];

        $this->model->Moneda($factura_tmp);

        header('Location: index.php?c=factura_tmp');
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        require_once "view/factura/tabla_factura.php";
        //header('Location: index.php?c=factura_tmp');
    }
}

<?php
require_once 'model/devolucion_tmpventas.php';
require_once 'model/venta.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/caja.php';
require_once 'model/metodo.php';

class devolucion_tmpventasController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new devolucion_tmpventas();
        $this->venta = new venta();
        $this->usuario = new usuario();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->metodo = new metodo();
    }

    public function Index()
    {
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {
            require_once 'view/devolucion_ventas/nueva-devolucion.php';
        } else {
            require_once 'view/venta/apertura.php';
        }
        require_once 'view/footer.php';
    }

    public function Devolucion_ventas()
    {
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {
            require_once 'view/devolucion_ventas/nueva-devolucion.php';
        } else {
            require_once 'view/venta/apertura.php';
        }
        require_once 'view/footer.php';
    }

    public function Mayorista()
    {
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {
            require_once 'view/venta/nueva-ventamayor.php';
        } else {
            require_once 'view/venta/apertura.php';
        }
        require_once 'view/footer.php';
    }

    public function Editar()
    {

        $venta = new venta();

        if (isset($_REQUEST['id'])) {
            $venta = $this->venta->ObtenerUno($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/venta/venta-editar.php';
        require_once 'view/footer.php';
    }


    public function Listar()
    {
        require_once 'view/venta/nueva-venta.php';
    }



    public function Crud()
    {
        $devolucion_tmpventas = new devolucion_tmpventas();

        if (isset($_REQUEST['id'])) {
            $devolucion_tmpventas = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/devolucion_tmpventas/devolucion_tmpventas-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $devolucion_tmpventas = new devolucion_tmpventas();

        if (isset($_REQUEST['id'])) {
            $devolucion_tmpventas = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/devolucion_tmpventas/devolucion_tmpventas-editar.php';
    }

    public function ObtenerMoneda()
    {
        $devolucion_tmpventas = new devolucion_tmpventas();

        $devolucion_tmpventas = $this->model->ObtenerMoneda();
    }

    public function Guardar()
    {

        $id_venta = $_REQUEST["id_venta"];
        $producto = $this->producto->Obtener($_REQUEST['id_producto']);
        if (!isset($_SESSION)) session_start();
        $devolucion_tmpventas = new devolucion_tmpventas();

        $devolucion_tmpventas->id = 0;
        $devolucion_tmpventas->id_venta = 1;
        $devolucion_tmpventas->id_vendedor = $_SESSION['user_id'];
        $devolucion_tmpventas->id_producto = $_REQUEST['id_producto'];
        $devolucion_tmpventas->precio_venta = $_REQUEST['precio_venta'];
        $devolucion_tmpventas->cantidad = $_REQUEST['cantidad']*(-1) ;
        $devolucion_tmpventas->descuento = $_REQUEST['descuento'];
        $devolucion_tmpventas->fecha_venta = date("Y-m-d H:i");


        $devolucion_tmpventas->id > 0
            ? $this->model->Actualizar($devolucion_tmpventas)
            : $this->model->Registrar($devolucion_tmpventas);

        header('Location:' . getenv('HTTP_REFERER'));
    }


    public function GuardarMayorista()
    {

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        if (!isset($_SESSION)) session_start();
        $devolucion_tmpventas = new devolucion_tmpventas();

        $devolucion_tmpventas->id = 0;
        $devolucion_tmpventas->id_venta = 1;
        $devolucion_tmpventas->id_vendedor = $_SESSION['user_id'];
        $devolucion_tmpventas->id_producto = $_REQUEST['id_producto'];
        $devolucion_tmpventas->precio_venta = $_REQUEST['precio_venta'];
        $devolucion_tmpventas->cantidad = $_REQUEST['cantidad'];
        $devolucion_tmpventas->descuento = $_REQUEST['descuento'];
        $devolucion_tmpventas->fecha_venta = date("Y-m-d H:i");


        $devolucion_tmpventas->id > 0
            ? $this->model->Actualizar($devolucion_tmpventas)
            : $this->model->Registrar($devolucion_tmpventas);

        header('Location: index.php?c=devolucion_tmpventas&a=mayorista');
    }

    public function GuardarUno()
    {


        $venta = new venta();

        $costo = $_REQUEST['precio_costo'];
        $venta = $_REQUEST['precio_venta'];

        $venta->id = 0;
        $venta->id_venta = 1;
        $venta->id_cliente = $_REQUEST['id_cliente'];
        $venta->id_vendedor = $_REQUEST['id_venta'];
        $venta->id_producto = $_REQUEST['codigo'];
        $venta->precio_costo = $_REQUEST['precio_costo'];
        $venta->precio_venta = $_REQUEST['precio_venta'];
        $venta->subtotal = $_REQUEST['subtotal'];
        $venta->descuento = 0;
        $venta->iva = 0;
        $venta->total = $_REQUEST['total'];
        $venta->comprobante = $_REQUEST['comprobante'];
        $venta->nro_comprobante = $_REQUEST['nro_comprobante'];
        $venta->cantidad = $_REQUEST['cantidad'];
        $venta->margen_ganancia = round(((($venta - $costo) * 100) / $costo), 2);
        $venta->fecha_venta = $_REQUEST['fecha_venta'];
        $venta->metodo = $_REQUEST['metodo'];
        $venta->banco = $_REQUEST['banco'];
        $venta->contado = $_REQUEST['contado'];


        $venta->id > 0
            ? $this->venta->Actualizar($devolucion_tmpventas)
            : $this->venta->Registrar($devolucion_tmpventas);

        if ($venta->contado == 'Cuota')
            $deuda = $this->deuda->EditarMonto($venta->id_venta, $venta->total);

        if ($venta->contado == 'Contado')
            $deuda = $this->ingreso->EditarMonto($venta->id_venta, $venta->total);

        header('Location: index.php?c=devolucion_tmpventas&a=editar&id=' . $venta->id_venta);
    }

    public function Moneda()
    {

        $devolucion_tmpventas = new devolucion_tmpventas();

        $devolucion_tmpventas->id = 0;
        $devolucion_tmpventas->reales = $_REQUEST['reales'];
        $devolucion_tmpventas->dolares = $_REQUEST['dolares'];
        $devolucion_tmpventas->monto_inicial = $_REQUEST['monto_inicial'];

        $this->model->Moneda($devolucion_tmpventas);

        header('Location: index.php?c=devolucion_tmpventas');
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location:' . getenv('HTTP_REFERER'));
    }
}

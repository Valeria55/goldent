<?php
require_once 'model/devolucion.php';
require_once 'model/venta.php';
require_once 'model/usuario.php';
require_once 'model/compra.php';
require_once 'model/devolucion_tmp.php';
require_once 'model/producto.php';
require_once 'model/ingreso.php';
require_once 'model/egreso.php';
require_once 'model/deuda.php';
require_once 'model/acreedor.php';
require_once 'model/egreso.php';
require_once 'model/cierre.php';
require_once 'model/caja.php';
require_once 'model/cliente.php';


class devolucionController
{

    private $model;
    private $venta;
    private $usuario;
    private $compra;
    private $devolucion_tmp;
    private $producto;
    private $ingreso;
    private $egreso;
    private $deuda;
    private $acreedor;
    private $cierre;
    private $caja;
    private $cliente;


    public function __CONSTRUCT()
    {
        $this->model = new devolucion();
        $this->venta = new venta();
        $this->usuario = new usuario();
        $this->compra = new compra();
        $this->devolucion_tmp = new devolucion_tmp();
        $this->producto = new producto();
        $this->ingreso = new ingreso();
        $this->egreso = new egreso();
        $this->deuda = new deuda();
        $this->acreedor = new acreedor();
        $this->egreso = new egreso();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->cliente = new cliente();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/devolucion/devolucion.php';
        require_once 'view/footer.php';
    }

    public function Sesion()
    {
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {
            require_once 'view/venta/venta-sesion.php';
        } else {
            echo "<h1>Debe hacer apertura de caja</h1>";
        }
        require_once 'view/footer.php';
    }

    public function NuevaVenta()
    {
        require_once 'view/header.php';
        require_once 'view/venta/nueva-venta.php';
        require_once 'view/footer.php';
    }


    public function Listar()
    {
        require_once 'view/venta/venta.php';
    }

    public function ListarCliente()
    {
        require_once 'view/header.php';
        require_once 'view/venta/ventacliente.php';
        require_once 'view/footer.php';
    }

    public function ListarUsuario()
    {
        require_once 'view/header.php';
        require_once 'view/venta/ventausuario.php';
        require_once 'view/footer.php';
    }

    public function ListarProducto()
    {
        require_once 'view/header.php';
        require_once 'view/venta/ventaproducto.php';
        require_once 'view/footer.php';
    }


    public function detalles()
    {
        require_once 'view/devolucion/devolucion_detalles.php';
    }


    public function ListarDia()
    {

        require_once 'view/header.php';
        require_once 'view/venta/ventadia.php';
        require_once 'view/footer.php';
    }

    public function Cambiar()
    {

        $venta = new venta();

        $id_item = $_REQUEST['id_item'];
        $id_venta = $_REQUEST['id_venta'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $cantidad_ant = $_REQUEST['cantidad_ant'];

        $cant = $cantidad_ant - $cantidad;

        if ($cantidad > 0) {
            $venta = $this->model->Cantidad($id_item, $id_venta, $cantidad);

            if ($venta->contado == 'Cuota')
                $deuda = $this->deuda->EditarMonto($id_venta, $venta->total_venta);

            if ($venta->contado == 'Contado')
                $deuda = $this->ingreso->EditarMonto($id_venta, $venta->total_venta);


            $this->producto->Sumar($codigo, $cant);
        }

        echo json_encode($venta);
    }

    public function Cancelar()
    {

        $id_item = $_REQUEST['id_item'];
        $id_venta = $_REQUEST['id_venta'];
        $codigo = $_REQUEST['codigo'];
        $cantidad = $_REQUEST['cantidad_item'];


        $venta = $this->model->Cantidad($id_item, $id_venta, 0);

        if ($venta->contado == 'Cuota')
            $deuda = $this->deuda->EditarMonto($id_venta, $venta->total_venta);

        if ($venta->contado == 'Contado')
            $deuda = $this->ingreso->EditarMonto($id_venta, $venta->total_venta);

        $venta = $this->model->CancelarItem($id_item);
        $this->producto->Sumar($codigo, $cantidad);
        header('location: ?c=venta_tmp&a=editar&id=' . $id_venta);
    }



    public function Crud()
    {
        $venta = new venta();

        if (isset($_REQUEST['id'])) {
            $venta = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/venta/venta-editar.php';
        require_once 'view/footer.php';
    }

    public function Cierre()
    {

        require_once 'view/informes/cierrepdf.php';
    }

    public function InformeDiario()
    {

        require_once 'view/informes/ventadiapdf.php';
    }

    public function InformeRango()
    {

        require_once 'view/informes/ventarangopdf.php';
    }

    public function InformeUsados()
    {

        require_once 'view/informes/productosusadospdf.php';
    }

    public function CierreMes()
    {
        $venta = new venta();

        if (isset($_REQUEST['fecha'])) {
            $venta = $this->model->ListarMes($_REQUEST['fecha']);
        }
        require_once 'view/informes/cierremesnewpdf.php';
    }

    public function Factura()
    {

        require_once 'view/informes/facturapdf.php';
    }

    public function Ticket()
    {

        require_once 'view/informes/ticketpdf_ajuste.php';
    }

    public function Obtener()
    {
        $venta = new venta();

        if (isset($_REQUEST['id'])) {
            $venta = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/venta/venta-editar.php';
    }

    public function ObtenerProducto()
    {
        $venta = new venta();

        $venta = $this->model->ObtenerProducto($_REQUEST['id_venta'], $_REQUEST['id_producto']);

        echo json_encode($venta);
    }

    public function GuardarUno()
    {


        $venta = new venta();

        $costo = $_REQUEST['precio_costo'] * $_REQUEST['cantidad'];
        $p_venta = $_REQUEST['precio_venta'] * $_REQUEST['cantidad'];

        $venta->id = 0;
        $venta->id_venta = $_REQUEST['id_venta'];
        $venta->id_cliente = $_REQUEST['id_cliente'];
        $venta->id_vendedor = $_REQUEST['id_venta'];
        $venta->id_producto = $_REQUEST['id_producto'];
        $venta->id_res = 0;
        $venta->precio_costo = $_REQUEST['precio_costo'];
        $venta->precio_venta = $_REQUEST['precio_venta'];
        $venta->subtotal = $p_venta;
        $venta->descuento = 0;
        $venta->iva = 0;
        $venta->total = $p_venta;
        $venta->comprobante = $_REQUEST['comprobante'];
        $venta->nro_comprobante = $_REQUEST['nro_comprobante'];
        $venta->cantidad = $_REQUEST['cantidad'];
        $venta->margen_ganancia = round(((($p_venta - $costo) * 100) / $costo), 2);
        $venta->fecha_venta = $_REQUEST['fecha_venta'];
        $venta->metodo = $_REQUEST['metodo'];
        $venta->banco = $_REQUEST['banco'];
        $venta->contado = $_REQUEST['contado'];


        $this->producto->Restar($venta);


        $venta->id > 0
            ? $this->model->Actualizar($venta)
            : $this->model->Registrar($venta);



        header('Location: index.php?c=venta_tmp&a=editar&id=' . $venta->id_venta);
    }

    public function Guardar()
    {

        $ven = new venta();
        $ven = $this->model->Ultimo();
        $sumaTotal = 0;
        if (!isset($_SESSION)) session_start();

        // Obtener el id_user del formulario modal
        $id_user = isset($_POST['id_user']) ? $_POST['id_user'] : null;

        foreach ($this->devolucion_tmp->Listar() as $v) {

            $devolucion = new devolucion();

            $devolucion->id = 0;
            $devolucion->id_venta = ($ven->id_venta + 1);
            $devolucion->venta = $_POST['venta'] ;
            $devolucion->id_cliente = 0;
            $devolucion->id_vendedor = $_SESSION['user_id'];
            $devolucion->vendedor_salon = 0;
            $devolucion->id_producto = $v->id_producto;
            $devolucion->precio_costo = $v->precio_costo;
            $devolucion->precio_venta = $v->precio_venta;
            $subtotal = $v->precio_venta * $v->cantidad;
            $devolucion->subtotal = $v->cantidad<0 ? $subtotal * -1 : $subtotal;
            $devolucion->descuento = $v->descuento;
            $devolucion->iva = 0;
            $devolucion->total = $devolucion->subtotal;
            // Guardar la observación en la columna comprobante
            $devolucion->comprobante = isset($_POST['observacion']) ? $_POST['observacion'] : '';
            $devolucion->nro_comprobante = 0;
            $devolucion->cantidad = $v->cantidad;
            $devolucion->margen_ganancia = 0;
            $devolucion->fecha_venta = date("Y-m-d H:i");
            $devolucion->metodo = 0;
            $devolucion->contado = $_REQUEST['contado'];
            $devolucion->banco = 0;
            $devolucion->id_user = $id_user;

            //Registrar venta
            $this->model->Registrar($devolucion);
            //Sumar Stock
            $this->producto->SumarProducto($devolucion);

            $sumaTotal += $devolucion->total;
        }

        /*
            if($_REQUEST['contado']=="Efectivo"){

                $egreso = new egreso();
            
                if (!isset($_SESSION)) session_start();
                $cierre = $this->cierre->Consultar($_SESSION['user_id']);
                $egreso->id_caja = ($cierre->id_caja)? $cierre->id_caja:$_SESSION['id_caja'];
                $egreso->id_cliente = $devolucion->id_cliente;
                $egreso->id_compra = 0;
                $egreso->fecha = date("Y-m-d H:i");
                $egreso->categoria = 'Devoluciónes';
                $egreso->concepto = 'Devolución de venta segun '.$_REQUEST['comprobante'].' N° '.$_REQUEST['nro_comprobante'];
                $egreso->comprobante = $devolucion->comprobante.' N° '.$devolucion->nro_comprobante;
                $egreso->forma_pago = "Efectivo";
                $egreso->monto = $sumaTotal;
                $egreso->sucursal = 0;
                $this->egreso->Registrar($egreso);

            }elseif($_REQUEST['contado']=="Credito"){

                if (!isset($_SESSION)) session_start();
                $acreedor = new acreedor();
                $acreedor->id_cliente = $devolucion->id_cliente;
                $acreedor->id_compra = 0;
                $acreedor->fecha = date("Y-m-d H:i");
                $acreedor->concepto = "Crédito por devolución";
                $acreedor->monto = $sumaTotal;
                $acreedor->saldo = $sumaTotal;  
                $acreedor->sucursal = $_SESSION['sucursal']; 
                $this->acreedor->Registrar($acreedor);
            }else{
                $this->egreso->Registrar($egreso);
            }*/

        $this->devolucion_tmp->Vaciar();

       // if (false) {
            header('refresh:0;index.php?c=devolucion&a=ticket&id=' . $devolucion->id_venta);
        //} else {
            //header('Location: index.php?c=venta&a=sesion');
            //header('Location:' . getenv('HTTP_REFERER'));
       // }
        //header('Location: index.php?c=venta&a=sesion');
    }

    public function Eliminar()
    {

        foreach ($this->model->Listar($_REQUEST['id']) as $v) {
            $venta = new venta();
            $venta->id_producto = $v->codigo;
            $venta->cantidad = $v->cantidad;
            $this->producto->Sumar($venta);
        }
        $this->ingreso->EliminarVenta($_REQUEST['id']);
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=venta');
    }

    public function Anular()
    {
        foreach ($this->model->Listar($_REQUEST['id']) as $v) {
            $devolucion = new devolucion();
            $devolucion->id_producto = $v->id_producto;
            $devolucion->cantidad = $v->cantidad;
            $this->producto->Restar($devolucion);
        }
        $this->model->Anular($_REQUEST['id']);
        header('Location:' . getenv('HTTP_REFERER'));
    }
}

<?php
require_once 'model/devolucion_ventas.php';
require_once 'model/venta.php';
require_once 'model/usuario.php';
require_once 'model/compra.php';
require_once 'model/devolucion_tmpventas.php';
require_once 'model/producto.php';
require_once 'model/ingreso.php';
require_once 'model/egreso.php';
require_once 'model/deuda.php';
require_once 'model/acreedor.php';
require_once 'model/egreso.php';
require_once 'model/cierre.php';
require_once 'model/caja.php';
require_once 'model/cliente.php';
require_once 'model/metodo.php';


class devolucion_ventasController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new devolucion_ventas();
        $this->venta = new venta();
        $this->usuario = new usuario();
        $this->compra = new compra();
        $this->devolucion_tmpventas = new devolucion_tmpventas();
        $this->producto = new producto();
        $this->ingreso = new ingreso();
        $this->egreso = new egreso();
        $this->deuda = new deuda();
        $this->acreedor = new acreedor();
        $this->egreso = new egreso();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->cliente = new cliente();
        $this->metodo = new metodo();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/devolucion_ventas/devolucion_ventas.php';
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
    public function DevolucionTicket()
    {
        require_once 'view/informes/ticket-devolucion.php';
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
        require_once 'view/devolucion_ventas/devolucion_detalles.php';
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

        require_once 'view/informes/ticketpdf.php';
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
        $venta = $this->venta->ObtenerVenta($_REQUEST['id_venta']);
        $sumaTotal = 0;
        if (!isset($_SESSION)) session_start();

        foreach ($this->devolucion_tmpventas->Listar() as $v) {

            $devolucion_ventas = new devolucion_ventas();

            $devolucion_ventas->id = 0;
            $devolucion_ventas->id_venta = $_REQUEST['id_venta'];
            $devolucion_ventas->id_cliente = $venta->id_cliente;
            $devolucion_ventas->id_vendedor = $_SESSION['user_id'];
            $devolucion_ventas->vendedor_salon = 0;
            $devolucion_ventas->id_producto = $v->id_producto;
            $devolucion_ventas->precio_costo = $v->precio_costo;
            $devolucion_ventas->precio_venta = $v->precio_venta;
            $devolucion_ventas->subtotal = $v->precio_venta * ($v->cantidad)*(-1);
            $devolucion_ventas->descuento = $v->descuento;
            $devolucion_ventas->iva = 0;
            $devolucion_ventas->total = $devolucion_ventas->subtotal - ($devolucion_ventas->subtotal * ($devolucion_ventas->descuento / 100));
            $devolucion_ventas->comprobante = $venta->comprobante;
            $devolucion_ventas->nro_comprobante = $venta->nro_comprobante;
            $devolucion_ventas->cantidad = $v->cantidad;
            $devolucion_ventas->margen_ganancia = 0;
            $devolucion_ventas->fecha_venta = $venta->fecha_venta; //date("Y-m-d H:i");
            $devolucion_ventas->metodo = $venta->metodo;
            $devolucion_ventas->banco = 0;
            $devolucion_ventas->contado = $_REQUEST['contado'];
            $devolucion_ventas->motivo = $_REQUEST['motivo'];

            //Registrar venta
            $this->model->Registrar($devolucion_ventas);
            //Sumar Stock
            $this->producto->SumarDevolucion($devolucion_ventas);

            $sumaTotal += $devolucion_ventas->total;
        }
        $e = $this->model->Ultimo();
        //$e = $this->model->devolucionid();

        //var_dump($e);

        if ($_REQUEST['contado'] == "Efectivo") {

            $egreso = new egreso();

            if (!isset($_SESSION)) session_start();
            $cierre = $this->cierre->Consultar($_SESSION['user_id']);
            $egreso->id_devolucion = $e->id;
            //$egreso->id_caja = ($cierre->id_caja) ? $cierre->id_caja : $_SESSION['id_caja'];
            $egreso->id_cliente = $devolucion_ventas->id_cliente;
            $egreso->id_compra = 0;
            $egreso->fecha = date("Y-m-d H:i");
            $egreso->categoria = 'Devolución';
            $egreso->concepto = 'Devolución de venta N° ' . $devolucion_ventas->id_venta;
            $egreso->comprobante = $devolucion_ventas->comprobante . ' N° ' . $devolucion_ventas->nro_comprobante;
            
            if ($_REQUEST['forma_pago'] == "Efectivo") {
                $egreso->id_caja = 1; // caja chica
            } else {
                $egreso->id_caja = 2; // banco
            }
            $egreso->forma_pago = $_REQUEST['forma_pago'];
            $egreso->sucursal = 0;
            
            // Agregar campos de moneda y cambio
            $egreso->moneda = isset($_REQUEST['moneda']) ? $_REQUEST['moneda'] : 'GS';
            $egreso->cambio = isset($_REQUEST['cambio']) ? floatval($_REQUEST['cambio']) : 1;
            
            // Calcular monto según la moneda seleccionada
            if ($egreso->moneda == 'GS') {
                // Si es Guaraníes, usar el monto base (suma total en guaraníes)
                $egreso->monto = $sumaTotal;
            } else {
                // Si es otra moneda, convertir a esa moneda dividiendo por el cambio
                $egreso->monto = $sumaTotal / $egreso->cambio;
            }
            
            if ($egreso->monto != 0) {
                $this->egreso->Registrar($egreso);
            }
            
        } elseif ($_REQUEST['contado'] == "Credito") {

            if (!isset($_SESSION)) session_start();
            $acreedor = new acreedor();
            $acreedor->id_cliente = $devolucion_ventas->id_cliente;
            $acreedor->id_compra = 0;
            $acreedor->fecha = date("Y-m-d H:i");
            $acreedor->concepto = "Crédito por devolución";
            $acreedor->monto = $sumaTotal;
            $acreedor->saldo = $sumaTotal;
            $acreedor->sucursal = $_SESSION['sucursal'];
            $this->acreedor->Registrar($acreedor);
        } else {
            $this->egreso->Registrar($acreedor);
        }

        $this->devolucion_tmpventas->Vaciar();

        if (false) {
            header('refresh:0;index.php?c=venta&a=ticket&id=$id');
        } else {
            //header('Location: index.php?c=venta&a=sesion');
            header('Location:' . getenv('HTTP_REFERER'));
        }
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
        // $this->ingreso->EliminarVenta($_REQUEST['id']);
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=venta');
    }

    public function Anular()
    {
        foreach ($this->model->Listar($_REQUEST['id']) as $v) {
            $devolucion_ventas = new devolucion_ventas();
            $devolucion_ventas->id_producto = $v->id_producto;
            $devolucion_ventas->cantidad = $v->cantidad;
            $this->producto->RestarDevolucion($devolucion_ventas);
        }
        $this->model->Anular($_REQUEST['id']);
        $this->egreso->EliminarDevolucion($_REQUEST['id']);

        header('Location:' . getenv('HTTP_REFERER'));
    }
}

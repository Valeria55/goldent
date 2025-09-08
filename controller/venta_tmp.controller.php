<?php
require_once 'model/venta_tmp.php';
require_once 'model/venta.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/caja.php';
require_once 'model/pago_tmp.php';
require_once 'model/metodo.php';
require_once 'model/gift_card.php';
require_once 'model/presupuesto.php';
require_once 'model/sucursal.php';
require_once 'model/devolucion_ventas.php';

class venta_tmpController
{

    private $model;
    private $venta_tmp;
    private $venta ;
    private $usuario;
    private $vendedor;
    private $producto;
    private $cliente;
    private $cierre;
    private $caja;
    private $pago_tmp;
    private $metodo;
    private $sucursal;
    private $gift_card;
    private $presupuesto;
    private $devolucion_ventas;

    public function __CONSTRUCT()
    {
        $this->model = new venta_tmp();
        $this->venta_tmp = new venta_tmp();
        $this->venta = new venta();
        $this->usuario = new usuario();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->pago_tmp = new pago_tmp();
        $this->metodo = new metodo();
        $this->sucursal = new sucursal();
        $this->gift_card = new gift_card();
        $this->presupuesto = new presupuesto();
        $this->devolucion_ventas = new devolucion_ventas();
    }
    

    public function Index()
    {
        $fecha = date('Y-m-d');
        require_once 'view/header.php';
        session_start();
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {
            session_start();
            if ($this->cierre->ConsultarCierre($_SESSION['user_id'], $fecha)) {
                echo '<form method="get">
                   <h1>Generar cierre de caja</h1>
                    <input type="hidden" name="c" value="cierre">
                    <input type="hidden" name="a" value="cierre">
                    <div class="form-group">
                        <label>Monto Efectivo(GS)</label>
                        <input type="number" value="0" name="monto_cierre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Monto Efectivo(USD)</label>
                        <input type="number" value="0" name="monto_dolares" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Monto Efectivo(RS)</label>
                        <input type="number" value="0" name="monto_reales" class="form-control" required>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary">Generar</button>
                    </div>
                             
                               

                </form>';
            } else {
                require_once 'view/venta/nueva-venta.php';
            }
        } else {
            if ($_SESSION['nivel'] == 3) {
                require_once 'view/presupuesto/presupuesto.php';
            } else {
                require_once 'view/venta/apertura.php';
            }
        }
        require_once 'view/footer.php';
    }

    /* public function Index(){
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {     
            require_once 'view/venta/nueva-venta.php';
        }else{
            require_once 'view/venta/apertura.php';
        }
        require_once 'view/footer.php';
       
    }*/

    public function Mayorista()
    {
        require_once 'view/header.php';
        session_start();
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
        $venta_tmp = new venta_tmp();

        if (isset($_REQUEST['id'])) {
            $venta_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/venta_tmp/venta_tmp-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $venta_tmp = new venta_tmp();

        if (isset($_REQUEST['id'])) {
            $venta_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/venta_tmp/venta_tmp-editar.php';
    }

    public function ObtenerMoneda()
    {
        $venta_tmp = new venta_tmp();

        $venta_tmp = $this->model->ObtenerMoneda();
    }
    public function ProductoFactura()
    {

        $venta_tmp = new venta_tmp();

        $venta_tmp->id = $_REQUEST['id'];
        $venta_tmp->prod_factura = $_REQUEST['prod_factura'];
        //var_dump($venta_tmp->prod_facura);
        $insert = $this->model->ProductoFactura($venta_tmp);
        //var_dump($insert);


    }
    public function CantidadFactura()
    {

        $venta_tmp = new venta_tmp();
        $p = $this->model->ObtenerProdFactura($_REQUEST['id']);

        $venta_tmp->prod_factura = $p->prod_factura;
        $venta_tmp->id = $_REQUEST['id'];
        $venta_tmp->can_factura = $_REQUEST['can_factura'];
        //var_dump($venta_tmp->prod_facura);

        $insert = $this->model->CantidadFactura($venta_tmp);
        $this->producto->CantidadFactura($venta_tmp);
        //var_dump($insert);


    }
    public function Guardar()
    {

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);

        session_start();
        $venta_tmp = new venta_tmp();

        $venta_tmp->id = 0;
        $venta_tmp->id_venta = 1;
        $venta_tmp->id_vendedor = $_SESSION['user_id'];
        $venta_tmp->id_producto = $_REQUEST['id_producto'];
        $venta_tmp->precio_venta = $_REQUEST['precio_venta'];

        // if ($_REQUEST['precio_venta'] == $pro->precio_minorista) {
        //     $venta_tmp->precio_venta = $pro->precio_minorista;
        // } else if ($_REQUEST['precio_venta'] == $pro->precio_mayorista) {
        //     $venta_tmp->precio_venta = $pro->precio_mayorista;
        // } else {
        //     $venta_tmp->precio_venta = $pro->precio_turista;
        // }

        $venta_tmp->id_sucursal = $_REQUEST['id_sucursal'];
        $venta_tmp->cantidad = $_REQUEST['cantidad'];
        $venta_tmp->descuento = $_REQUEST['descuento'];
        $venta_tmp->fecha_venta = date("Y-m-d H:i");


        $venta_tmp->id > 0
            ? $this->model->Actualizar($venta_tmp)
            : $this->model->Registrar($venta_tmp);

        //header('Location: index.php?c=venta_tmp');
        require_once "view/venta/tabla_venta.php";
    }

    public function CancelarVenta()
    {

        $this->model->Vaciar();
        header('Location: index.php?c=venta_tmp');
        //header('Location: index.php?c=venta_tmp');
    }

    public function GuardarMayorista()
    {

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        session_start();
        $venta_tmp = new venta_tmp();

        $venta_tmp->id = 0;
        $venta_tmp->id_venta = 1;
        $venta_tmp->id_vendedor = $_SESSION['user_id'];
        $venta_tmp->id_producto = $_REQUEST['id_producto'];
        $venta_tmp->precio_venta = $_REQUEST['precio_venta'];
        $venta_tmp->cantidad = $_REQUEST['cantidad'];
        $venta_tmp->descuento = $_REQUEST['descuento'];
        $venta_tmp->fecha_venta = date("Y-m-d H:i");


        $venta_tmp->id > 0
            ? $this->model->Actualizar($venta_tmp)
            : $this->model->Registrar($venta_tmp);

        header('Location: index.php?c=venta_tmp&a=mayorista');
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
            ? $this->venta->Actualizar($venta)
            : $this->venta->Registrar($venta);

        if ($venta->contado == 'Cuota')
            $deuda = $this->deuda->EditarMonto($venta->id_venta, $venta->total);

        if ($venta->contado == 'Contado')
            $deuda = $this->ingreso->EditarMonto($venta->id_venta, $venta->total);

        header('Location: index.php?c=venta_tmp&a=editar&id=' . $venta->id_venta);
    }

    public function Moneda()
    {

        $venta_tmp = new venta_tmp();

        $venta_tmp->id = 0;
        $venta_tmp->reales = $_REQUEST['reales'];
        $venta_tmp->dolares = $_REQUEST['dolares'];
        $venta_tmp->monto_inicial = $_REQUEST['monto_inicial'];

        $this->model->Moneda($venta_tmp);

        header('Location: index.php?c=venta_tmp');
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        require_once "view/venta/tabla_venta.php";
        //header('Location: index.php?c=venta_tmp');
    }
}

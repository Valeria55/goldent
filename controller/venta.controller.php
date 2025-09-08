<?php
require_once 'model/venta.php';
require_once 'model/usuario.php';
require_once 'model/compra.php';
require_once 'model/venta_tmp.php';
require_once 'model/producto.php';
require_once 'model/ingreso.php';
require_once 'model/deuda.php';
require_once 'model/acreedor.php';
require_once 'model/egreso.php';
require_once 'model/cierre.php';
require_once 'model/caja.php';
require_once 'model/sucursal.php';
require_once 'model/cliente.php';
require_once 'model/pago_tmp.php';
require_once 'model/gift_card.php';
require_once 'model/metodo.php';
require_once 'model/presupuesto.php';
require_once 'model/devolucion_ventas.php';
require_once 'model/devolucion.php';
require_once 'model/inventario.php';


class ventaController
{

    private $model;
    private $venta;
    private $usuario;
    private $compra;
    private $venta_tmp;
    private $producto;
    private $ingreso;
    private $deuda;
    private $acreedor;
    private $egreso;
    private $cierre;
    private $caja;    
    private $sucursal;
    private $cliente;
    private $pago_tmp;
    private $gift_card;
    private $metodo;
    private $presupuesto;
    private $devolucion_ventas;

    public function __CONSTRUCT()
    {
        $this->model = new venta();
        $this->venta = new venta();
        $this->usuario = new usuario();
        $this->compra = new compra();
        $this->venta_tmp = new venta_tmp();
        $this->producto = new producto();
        $this->ingreso = new ingreso();
        $this->deuda = new deuda();
        $this->acreedor = new acreedor();
        $this->egreso = new egreso();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->sucursal = new sucursal();
        $this->cliente = new cliente();
        $this->pago_tmp = new pago_tmp();
        $this->gift_card = new gift_card();
        $this->metodo = new metodo();
        $this->presupuesto = new presupuesto();
        $this->devolucion_ventas = new devolucion_ventas();
        $this->devolucion = new devolucion();
        $this->inventario = new inventario();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/venta/venta.php';
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

    public function EstadoResultado()
    {
        require_once 'view/header.php';
        require_once 'view/informes/estado_resultado.php';
        require_once 'view/footer.php';
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

    public function ListarProductoCat()
    {
        require_once 'view/header.php';
        require_once 'view/venta/ventaproductocat.php';
        require_once 'view/footer.php';
    }

    public function detalles()
    {
        require_once 'view/venta/venta_detalles.php';
    }


    public function ListarDia()
    {

        require_once 'view/header.php';
        require_once 'view/venta/ventadia.php';
        require_once 'view/footer.php';
    }

    public function ListarAjax()
    {
        $venta = $this->model->Listar(0);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }
    public function ListarFiltros()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $venta = $this->model->ListarFiltros($desde, $hasta);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }
     public function ListarProductoVenta()
    {

        require_once 'view/header.php';
        require_once 'view/venta/total_ventaproducto.php';
        require_once 'view/footer.php';
    }
     public function ListarPorItem()
    {
        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $venta = $this->model->ListarPorItem($desde, $hasta);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
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

        // require_once 'view/informes/cierremesnewpdf.php';
        if($_REQUEST['balance']=='extracto'){
            require_once 'view/informes/extracto_balance.php';
        }else{
            require_once 'view/informes/cierremesnew_pdf.php';
        }
        
    }
    public function ExtractoBalance()
    {

        // require_once 'view/informes/cierremesnewpdf.php';
        require_once 'view/informes/extracto_balance.php';
    }

    public function Factura()
    {

        require_once 'view/informes/facturapdf.php';
    }

    public function Recibopdf()
    {

        require_once 'view/informes/recibopdf.php';
    }

    public function Recibodev()
    {

        require_once 'view/informes/recibodev.php';
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

    public function ObtenerCantMaxima()
    {
        $venta = new venta();

        $venta = $this->model->ObtenerCantMaxima($_REQUEST['id_venta'], $_REQUEST['id_producto']);

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


        foreach ($this->venta_tmp->Listar() as $v) {

            $venta = new venta();

            $venta->id = 0;
            $venta->id_venta = $ven->id_venta + 1;
            $venta->id_cliente = $_REQUEST['id_cliente'];
            $venta->id_vendedor = $v->id_vendedor;
            $venta->id_presupuesto = $v->id_presupuesto;
            $venta->id_sucursal = $v->id_sucursal;
            $venta->vendedor_salon = 0;
            $venta->id_producto = $v->id_producto;
            $venta->precio_costo = $v->precio_costo;
            $venta->precio_venta = $v->precio_venta;
            $venta->subtotal = $v->precio_venta * $v->cantidad;
            $venta->descuento = $v->descuento;
            $venta->iva = $_REQUEST['ivaval'];
            $venta->total = ($v->precio_venta-$venta->descuento)*$v->cantidad;
            $venta->comprobante = $_REQUEST['comprobante'];
            $venta->nro_comprobante = $_REQUEST['nro_comprobante'];
            $venta->cantidad = $v->cantidad;
            $venta->can_factura = $v->can_factura;
            $venta->prod_factura = $v->prod_factura;
            $venta->margen_ganancia = ((($venta->precio_venta - ($venta->descuento)) - $venta->precio_costo) / ($venta->precio_costo)) * 100;
            $venta->fecha_venta = $_REQUEST["fecha_venta"]; //date("Y-m-d H:i");
            $venta->metodo = $_REQUEST['forma_pago'];
            $venta->contado = $_REQUEST['contado'];
            $venta->banco = $_REQUEST['banco'];
            
            $venta->id_devolucion = $_REQUEST['id_devolucion'];
            if ($_REQUEST['id_gift'] != '') {
                $venta->id_gift = $_REQUEST['id_gift'];
            }
            $venta->fecha = $_REQUEST["fecha_venta"]; //date("Y-m-d H:i");
            $venta->categoria = "Venta";
            $producto = $this->producto->Obtener($v->id_producto);
            $venta->concepto = $v->cantidad . " Kg - " . $producto->producto;
            $venta->monto = $venta->total;
            $venta->fecha_emision = $_REQUEST["fecha_venta"]; //date("Y-m-d H:i");
            $venta->fecha_vencimiento = "2020-08-31";
            $venta->moneda = "GS";
            $venta->cot_dolar = $_REQUEST['cot_dolar'];
            $venta->cot_real = $_REQUEST['cot_real'];

            // //Registrar venta
            $this->model->Registrar($venta);

            $this->presupuesto->CambiarEstado($venta);

            if ($_REQUEST['id_gift'] != '') {
                $this->gift_card->Retirado($venta->id_gift);
            }

            //Restar Stock
            $this->producto->RestarStock($venta);

            if ($venta->prod_factura == null) {
                $venta->prod_factura = 0;
            }
            if ($venta->prod_factura > 0 && $venta->comprobante == 'Factura') {

                //$this->producto->RestarConFactura($venta);
                //$this->producto->RestarSinFactura($venta);
            } else {
                $venta->can_factura = $v->cantidad;
               $venta->prod_factura = $v->id_producto;

                //$this->producto->RestarConFactura($venta);
            }

            $sumaTotal += $venta->total;
        }

        $error = 0;

        if ($venta->contado == 'Credito') {

            $deuda = new deuda();

            $deuda->id_cliente =  $venta->id_cliente;
            $deuda->id_venta = $venta->id_venta;
            $deuda->fecha = date("Y-m-d H:i");
            //$deuda->vencimiento = $_REQUEST['vencimiento'];
            $deuda->concepto = 'Venta a crédito';
            $deuda->monto = $sumaTotal;
            $deuda->moneda = "GS";
            $deuda->cambio = $_REQUEST['cambio'];

            $cambio = $_REQUEST['cambio'];

            if ($_REQUEST['entrega'] > 0) {

                $entrega = ($_REQUEST['entrega'] * $_REQUEST['cambio']);
                // echo '<pre>'; var_dump(  $entrega); echo '</pre>';

                $deuda->saldo = $sumaTotal - $entrega;
            } else {
                $deuda->saldo = $sumaTotal;
            }
            // die();

            $deuda->sucursal = $_SESSION['sucursal'];

            $this->deuda->Registrar($deuda);

            if ($_REQUEST['entrega'] > 0) {

                $ingreso = new ingreso();

                $ingreso->id_cliente = $venta->id_cliente;
                session_start();

                $de = $this->deuda->Ultimo();
       
                $cli = $this->cliente->Obtener($venta->id_cliente);
                $cierre = $this->cierre->Consultar($_SESSION['user_id']);

                if ($venta->metodo == "Efectivo") {
                    $ingreso->id_caja = 1; //llevar a caja chica
                } else {
                    $ingreso->id_caja = 2; // llevar a banco
                }

                $ingreso->id_venta = $venta->id_venta;
                $ingreso->id_deuda = $de->id;
                $ingreso->fecha =$_REQUEST["fecha_venta"];
                $ingreso->categoria = 'Entrega';
                $ingreso->concepto = 'Venta a credito a ' . $cli->nombre;
                $ingreso->comprobante = $_REQUEST['comprobante'] . ' N° ' . $_REQUEST['nro_comprobante'];
                $ingreso->monto = $_REQUEST['entrega'];
                $ingreso->moneda = $_REQUEST['moneda'];
                $ingreso->cambio = $_REQUEST['cambio'];
                $ingreso->forma_pago = $_REQUEST['forma_pago'];
                $ingreso->sucursal = 0;

                $this->ingreso->Registrar($ingreso);
            }

            $this->pago_tmp->Vaciar();
            $this->venta_tmp->Vaciar();
            $id = $ven->id_venta + 1;
            if ($_REQUEST['comprobante'] == "Ticket") {
                header("Location: index.php?c=venta&a=ticket&id=$id");
            } elseif ($_REQUEST['comprobante'] == "Factura") {
                header("Location: index.php?c=factura_tmp");
                // header("Location: index.php?c=venta&a=factura&id=$id");
            } elseif ($_REQUEST['comprobante'] == "Recibo") {
                header("Location: index.php?c=venta&a=recibopdf&id=$id");
            } else {
                header('Location: index.php?c=venta_tmp');                 //header("refresh:0;index.php?c=venta&a=factura&id=$id");
            }
        } else {
            $suma = 0;
            $sumaBd = 0;
            foreach ($this->pago_tmp->Listar() as $r) {

                $ingreso = new ingreso();
                session_start();
                $cierre = $this->cierre->Consultar($_SESSION['user_id']);

                if ($venta->metodo == "Efectivo") {
                    $ingreso->id_caja = 1; //llevar a caja chica
                } else {
                    $ingreso->id_caja = 2; // llevar a banco
                }

                $ingreso->id_cliente = $venta->id_cliente;
                $ingreso->id_venta = $ven->id_venta + 1;
                $ingreso->fecha =$_REQUEST["fecha_venta"];
                // $ingreso->vencimiento = date("Y-m-d H:i", strtotime("+$cuotas MONTH"));
                $ingreso->categoria = 'Venta';
                $ingreso->concepto = 'Venta al contado';
                $ingreso->comprobante = $_REQUEST['comprobante'];
                $ingreso->nro_comprobante = $_REQUEST['nro_comprobante'];
                $ingreso->forma_pago = $r->pago;
                $ingreso->monto = $r->monto;
                $ingreso->saldo = $r->monto;
                $ingreso->moneda = $r->moneda;
                $ingreso->tarjeta = $r->tarjeta;
                if ($ingreso->moneda == 'GS') {
                    $ingreso->cambio = 1;
                } elseif ($ingreso->moneda == 'RS') {
                    $ingreso->cambio = $cierre->cot_real_tmp;
                } elseif ($ingreso->moneda == 'USD') {
                    $ingreso->cambio = $cierre->cot_dolar_tmp;
                }
                $ingreso->sucursal = 0;

                $suma += $r->monto * $ingreso->cambio;

                $this->ingreso->Registrar($ingreso);
            }

            $suma = round($suma, 2);

            $id = $ven->id_venta + 1;
            $sumaBd = $this->ingreso->consultarVenta($id)->total;
            $sumaBd = round($sumaBd, 2);

            if ($sumaBd != $suma || $suma == 0) {
                $error = 1;
            }

            if ($error == 0) {
                $this->pago_tmp->Vaciar();
                $this->venta_tmp->Vaciar();
                if ($_REQUEST['comprobante'] == "Ticket") {
                    header("Location: index.php?c=venta&a=ticket&id=$id");
                } elseif ($_REQUEST['comprobante'] == "Factura") {
                    header("Location: index.php?c=factura_tmp");
                    // header("Location: index.php?c=venta&a=factura&id=$id");
                } elseif ($_REQUEST['comprobante'] == "Recibo") {
                    header("Location: index.php?c=venta&a=ticket&id=$id");
                } else {
                    header('Location: index.php?c=venta_tmp');                 //header("refresh:0;index.php?c=venta&a=factura&id=$id");
                }
            } else {
                require_once 'view/header.php';
                echo
                "<script>
                Swal.fire({
                    title: 'HUBO UN ERROR ',
                    text: 'LA VENTA NO PUDO REALIZARSE  VUELVA A REINTENTAR !!!',
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK!'
                }).then((result) => {
                    if (result.value) {
                    
                    window.history.back();
                    
                    }
                });
                </script>";
                require_once 'view/footer.php';

                //restaurar stock

                foreach ($this->model->Listar($ven->id_venta + 1) as $v) {

                    $producto = new producto();
                    $producto->id_producto = $v->id_producto;
                    $producto->cantidad = $v->cantidad;

                   // $this->producto->Sumar($producto);
                }
                //Eliminar ingreso
                //$this->ingreso->EliminarVenta($ven->id_venta + 1);
                //eliminar venta 
                //$this->model->Eliminar($ven->id_venta + 1);
                //Cambiar el estado del presupuesto VentaAnulada
                //$this->presupuesto->VentaAnulada($venta);
                $this->pago_tmp->Vaciar();
                $this->venta_tmp->Vaciar();

                header('Location: index.php?c=presupuesto');
            }
        }
    }

    public function Eliminar()
    {

        foreach ($this->model->Listar($_REQUEST['id']) as $v) {
            $venta = new venta();
            $venta->id_producto = $v->id_producto;
            $venta->cantidad = $v->cantidad;
            $this->producto->Sumar($venta);
        }
        $this->ingreso->EliminarVenta($_REQUEST['id']);
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=venta');
    }

    public function Anular()
    {

        foreach ($this->model->Listar($_REQUEST['id']) as $v){
            $venta = new venta();

            if ($venta->prod_factura > 0) {
                $venta->id_producto = $v->id_producto;
                $venta->cantidad = $v->cantidad;
                $venta->prod_factura = $v->prod_factura;
                $venta->can_factura = $v->can_factura;
                $this->producto->Sumar($venta);
               // $this->producto->SumarConfactura($venta);
              //  $this->producto->SumarSinfactura($venta);
            }else {
                $venta->prod_factura = $v->id_producto;
                $venta->can_factura = $v->cantidad;
                $venta->id_producto = $v->id_producto;
                $venta->cantidad = $v->cantidad;
                $this->producto->Sumar($venta);
                //$this->producto->SumarConfactura($venta);
            }
             $venta->id_presupuesto = $v->id_presupuesto;
        }
        //var_dump($venta->id_presupuesto);
        $this->presupuesto->VentaAnulada($venta);
        $this->ingreso->AnularVenta($_REQUEST['id']);
        $this->deuda->AnularVenta($_REQUEST['id']);
        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?c=venta');
    }
}

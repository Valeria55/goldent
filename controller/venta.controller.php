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
require_once 'model/cliente.php';
require_once 'model/pago_tmp.php';
require_once 'model/gift_card.php';
require_once 'model/metodo.php';
require_once 'model/presupuesto.php';
require_once 'model/devolucion_ventas.php';
require_once 'model/devolucion_compras.php';
require_once 'model/inventario.php';
require_once 'model/devolucion.php';
require_once 'model/transferencia_producto.php';


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
    private $cliente;
    private $pago_tmp;
    private $gift_card;
    private $metodo;
    private $presupuesto;
    private $devolucion_ventas;
    private $devolucion_compras;
    private $inventario;
    private $devolucion;
    private $transferencia_producto;

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
        $this->cliente = new cliente();
        $this->pago_tmp = new pago_tmp();
        $this->gift_card = new gift_card();
        $this->metodo = new metodo();
        $this->presupuesto = new presupuesto();
        $this->devolucion_ventas = new devolucion_ventas();
        $this->devolucion_compras = new devolucion_compras();
        $this->inventario = new inventario();
        $this->devolucion = new devolucion();
        $this->transferencia_producto = new transferencia_producto();
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

    //LISTADO DE VENTAS AL CONTADO, A APROBAR, Y APROBADO SIN FILTRO 
    public function ListarAjax()
    {
        //$venta = $this->model->Listar(0);
        //metodo para listar por 30 días
        $venta = $this->model->ListarUltimos30Dias(0);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }
    public function ListarAjaxAprobar()
    {
        //$venta = $this->model->ListarAprobar(0);
        //metodo para listar por 30 días
        $venta = $this->model->ListarAprobarultimos30dias(0);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }
    public function ListarAjaxAprobado()
    {
        //$venta = $this->model->ListarAprobado(0);
        //metodo para listar por 30 días
        $venta = $this->model->ListarAprobadoUltimos30dias(0);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }

    //FILTROS POR VENTAS CONTADO, A APROBAR, APROBADOS
    public function ListarFiltros()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $venta = $this->model->ListarFiltros($desde, $hasta);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }
    public function ListarFiltrosAprobar()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $venta = $this->model->ListarFiltrosAprobar($desde, $hasta);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }
    public function ListarFiltrosAprobado()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $venta = $this->model->ListarFiltrosAprobado($desde, $hasta);
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

        //require_once 'view/informes/cierrepdf.php';
        require_once 'view/informes/ventas_del_dia.php';
    }

    public function InformeDiario()
    {

        require_once 'view/informes/ventadiapdf.php';
    }
    public function Pagare()
    {

        require_once 'view/informes/pagare_tcpdf.php';
    }
    public function OrdenDelivery()
    {
        $id_venta = $_GET['id'];
        $items = $this->model->Listar($id_venta);

        require_once 'view/informes/orden_entrega_tcpdf.php';
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
        require_once 'view/informes/cierremesnew_pdf.php';
    }
    public function ventasPorVendedor()
    {

        require_once 'view/informes/informevendedorpdf.php';
    }

    public function Factura()
    {

        // require_once 'view/venta/informes/facturapdf.php';
           require_once 'view/informes/facturapdf.php';
        // require_once 'view/informes/facturapdf_dani_8-3-23.php';
    }

    public function Ticket()
    {

        require_once 'view/informes/ticketpdf_nuevo.php';
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
    public function Editar()
    {


        $venta = new venta();

        $venta->id_venta = $_REQUEST['id_venta'];
        $venta->id_cliente = $_REQUEST['id_cliente'];
        $venta->comprobante = $_REQUEST['comprobante'];
        $venta->nro_comprobante = $_REQUEST['nro_comprobante'];
        $venta->pagare = (isset($_REQUEST['pagare']) && $_REQUEST['pagare'] != '') ? $_REQUEST['pagare'] : null;

        $id = $_REQUEST['id_venta'];
        $this->model->Editar($venta);

        if (isset($_REQUEST['solo_guardar']) && $_REQUEST['solo_guardar'] == 1) {
             header('Location: index.php?c=venta');
        } elseif ($_REQUEST['comprobante'] == "Ticket") {
             header("Location: index.php?c=venta&a=ticket&id=$id");
        } elseif ($_REQUEST['comprobante'] == "Factura") {
             header("Location: index.php?c=venta&a=factura&id=$id");
        } else {
             header('Location: index.php?c=venta_tmp');
        }
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
        $autoimpresor = ($_REQUEST['comprobante'] == "Factura") ? $this->model->UltimoAutoimpresor()->autoimpresor + 1 : 0;

        // Obtener las cotizaciones del cierre actual
        if (!isset($_SESSION)) session_start();
        $cierre_actual = $this->cierre->Consultar($_SESSION['user_id']);
        $cot_dolar = $cierre_actual ? $cierre_actual->cot_dolar : 0;
        $cot_real = $cierre_actual ? $cierre_actual->cot_real : 0;

        foreach ($this->venta_tmp->Listar() as $v) {

            $venta = new venta();

            $venta->id = 0;
            $venta->id_venta = $ven->id_venta + 1;
            //guardar cliente, sino guardar id 81 que es el ocasional
            $venta->id_cliente = $_REQUEST['id_cliente'] ? $_REQUEST['id_cliente'] : '';
            $venta->paciente = $v->paciente ?? '';
            $venta->id_vendedor = $v->id_vendedor;
            $venta->id_presupuesto = $v->id_presupuesto;
            $venta->vendedor_salon = 0;
            $venta->id_producto = $v->id_producto;
            $venta->precio_costo = $v->precio_costo;
            $venta->precio_venta = $v->precio_venta;
            $venta->subtotal = $v->precio_venta * $v->cantidad;
            $venta->descuento = (!($_REQUEST['descuento_final'] == '')) ?  $_REQUEST['descuento_final'] : $v->descuento;
            $venta->iva = $_REQUEST['ivaval'];
            $venta->total = $venta->subtotal - ($venta->subtotal * ($venta->descuento / 100));
            $venta->comprobante = $_REQUEST['comprobante'];
            $venta->nro_comprobante = $_REQUEST['comprobante'] == "Factura" ? $_REQUEST['nro_comprobante'] : "";
            $venta->id_timbrado = $_REQUEST['id_timbrado'] ?? 1;
            $venta->autoimpresor = $_REQUEST['comprobante'] == "Factura" ? $autoimpresor : 0;
            $venta->cantidad = $v->cantidad;
            $venta->margen_ganancia = $venta->precio_costo > 0 ? ((($venta->precio_venta - ($venta->precio_venta * ($venta->descuento / 100))) - $venta->precio_costo) / ($venta->precio_costo)) * 100 : 0;
            $venta->fecha_venta = $_REQUEST["fecha_venta"]; //date("Y-m-d H:i");
            $venta->metodo = $_REQUEST['forma_pago'];
            $venta->contado = $_REQUEST['contado'];
            $venta->condicion_factura = $_REQUEST['condicion_factura'];
            $venta->banco = $_REQUEST['banco'];
            $venta->id_devolucion = $_REQUEST['id_devolucion'] ?? 0;
            $venta->pagare = (isset($_REQUEST['pagare']) && $_REQUEST['pagare'] != '') ? $_REQUEST['pagare'] : null;
            $venta->sucursal = 1;
            $venta->id_sucursal = 1;

            // Guardar las cotizaciones del cierre actual
            $venta->cot_usd = $cot_dolar;
            $venta->cot_rs = $cot_real;
            //por defecto en guarani
            $venta->moneda = 'Gs';

            if ($_REQUEST['id_gift'] != '') {
                $venta->id_gift = $_REQUEST['id_gift'];
            }
            $producto = $this->producto->Obtener($v->id_producto);
            $venta->concepto = $v->cantidad . " Kg - " . $producto->producto;
            $venta->monto = $venta->total;


            if ($venta->contado == 'Credito') {
                $venta->estado = 'APROBADO';
            } else {
                $venta->estado = 'APROBADO';
            }


            //Registrar venta
            $this->model->Registrar($venta);
            $this->presupuesto->CambiarEstado($venta);

            if ($_REQUEST['id_gift'] != '') {
                $this->gift_card->Retirado($venta->id_gift);
            }

            // //Restar Stock
            // if ($venta->contado != 'Credito') {
            //     $this->producto->Restar($venta);
            // }
            $sumaTotal += $venta->total;
        }
        $error = 0;
        if ($venta->contado == 'Credito') {

            $deuda = new deuda();

            $deuda->id_cliente =  $venta->id_cliente;
            $deuda->id_venta = $venta->id_venta;
            $deuda->fecha = $_REQUEST["fecha_venta"];
            //$deuda->vencimiento = $_REQUEST['vencimiento'];
            $deuda->concepto = 'Venta a crédito';
            $deuda->monto = $sumaTotal;
            $deuda->saldo = $sumaTotal - $_REQUEST['entrega'];
            $deuda->sucursal = 1;

            $this->deuda->Registrar($deuda);

            if ($_REQUEST['entrega'] > 0) {

                $ingreso = new ingreso();

                $ingreso->id_cliente = $venta->id_cliente;
                if (!isset($_SESSION)) session_start();

                $de = $this->deuda->Ultimo();
                $cli = $this->cliente->Obtener($venta->id_cliente);
                $cierre = $this->cierre->Consultar($_SESSION['user_id']);

                if ($_REQUEST['forma_pago'] == "Efectivo") {
                    $ingreso->id_caja = 1; // caja chica
                } else {
                    $ingreso->id_caja = 2; // banco
                }

                $ingreso->id_venta = $venta->id_venta;
                $ingreso->id_deuda = $de->id;
                $ingreso->fecha = $_REQUEST["fecha_venta"];
                $ingreso->categoria = 'Entrega';
                $ingreso->concepto = 'Venta a credito a ' . $cli->nombre;
                $ingreso->comprobante = $_REQUEST['comprobante'];
                $ingreso->monto = $_REQUEST['entrega'];
                $ingreso->forma_pago = $_REQUEST['forma_pago'];
                $ingreso->sucursal = 1;

                $this->ingreso->Registrar($ingreso);
            }
        }
        if ($venta->contado == 'Contado') {

            $suma = 0;
            $sumaBd = 0;
            foreach ($this->pago_tmp->Listar() as $r) {
                $suma += $r->monto;
                $ingreso = new ingreso();
                if (!isset($_SESSION)) session_start();
                $cierre = $this->cierre->Consultar($_SESSION['user_id']);

                if ($r->pago == "Efectivo") {
                    $ingreso->id_caja = 1; // caja chica
                } else {
                    $ingreso->id_caja = 2; // banco
                }
                $ingreso->id_cliente = $_REQUEST['id_cliente'];
                $ingreso->id_venta = $ven->id_venta + 1;
                $ingreso->fecha = $_REQUEST["fecha_venta"];
                $ingreso->categoria = 'Venta';
                $ingreso->concepto = 'Venta al contado';
                $ingreso->comprobante = $_REQUEST['comprobante'] . ' N° ' . $_REQUEST['nro_comprobante'];
                $ingreso->forma_pago = $r->pago;
                $ingreso->monto = $r->monto;
                $ingreso->moneda = $r->moneda;
                $ingreso->cambio = $r->cambio;
                $ingreso->sucursal = 1;

                $this->ingreso->Registrar($ingreso);
            }
        }
        $idd = $ven->id_venta + 1;
        $this->pago_tmp->Vaciar();
        $this->venta_tmp->Vaciar();
        if ($_REQUEST['comprobante'] == "Ticket") {
            // header("Location: index.php?c=venta&a=OrdenDelivery&id=$idd");
            header('Location: index.php?c=venta_tmp'); 
        } elseif ($_REQUEST['comprobante'] == "Factura") {
            header("Location: index.php?c=venta&a=factura&id=$idd");
        } else {
            header('Location: index.php?c=venta_tmp');                 //header("refresh:0;index.php?c=venta&a=factura&id=$id");
        }

        // $id = $ven->id_venta + 1;
        // $sumaBd = $this->ingreso->consultarVenta($id)->total;
        // if ($sumaBd != $suma || $suma == 0) {
        //     $error = 1;
        // }
        // // echo "sumaBd = $sumaBd; suma = $suma; error = $error";
        // // if ($error == 0) {
        //     $this->pago_tmp->Vaciar();
        //     $this->venta_tmp->Vaciar();
        //     if ($_REQUEST['comprobante'] == "Ticket") {
        //         header("Location: index.php?c=venta&a=ticket&id=$id");
        //     } elseif ($_REQUEST['comprobante'] == "Factura") {
        //         header("Location: index.php?c=venta&a=factura&id=$id");
        //     } else {
        //         header('Location: index.php?c=venta_tmp');                 //header("refresh:0;index.php?c=venta&a=factura&id=$id");
        //     }
        // } else {
        //     require_once 'view/header.php';
        //     echo
        //     "<script>
        //         Swal.fire({
        //             title: 'HUBO UN ERROR ',
        //             text: 'LA VENTA NO PUDO REALIZARSE  VUELVA A INTENTAR !!!',
        //             icon: 'error',
        //             showCancelButton: false,
        //             confirmButtonColor: '#3085d6',
        //             cancelButtonColor: '#d33',
        //             confirmButtonText: 'OK!'
        //         }).then((result) => {
        //             if (result.value) {
        //                 window.history.back();
        //             }
        //         });
        //     </script>";
        //     require_once 'view/footer.php';

        //eliminar venta 

        // foreach ($this->model->Listar($ven->id_venta + 1) as $v) {

        //     $producto = new producto();
        //     $producto->id_producto = $v->id_producto;
        //     $producto->cantidad = $v->cantidad;

        //     $this->producto->Sumar($producto);
        // }

        // $this->ingreso->EliminarVenta($ven->id_venta + 1);
        // $this->model->Eliminar($ven->id_venta + 1);
        // header('Location: index.php?c=venta_tmp'); 

        //restaurar stock
        // }


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

    public function aprobarVenta()
    {
        $this->model->aprobarVenta($_REQUEST['id_venta']);
        $sumaTotal = 0;

        foreach ($this->model->ListarAprobado($_REQUEST['id_venta'])  as $v) {
            $id_cliente = $v->id_cliente;
            $id_venta = $v->id_venta;
            $sumaTotal += $v->total;
            $fecha_venta = $v->fecha_venta;
            $this->producto->Restar($v);
        }
        $deuda = new deuda();

        $deuda->id_cliente = $id_cliente;
        $deuda->id_venta = $id_venta;
        $deuda->fecha = $fecha_venta;
        //$deuda->vencimiento = $_REQUEST['vencimiento'];
        $deuda->concepto = 'Venta a crédito';
        $deuda->monto = $sumaTotal;
        $deuda->saldo = $sumaTotal;
        $deuda->sucursal = $_SESSION['sucursal'];

        $this->deuda->Registrar($deuda);

        header('Location: index.php?c=venta');
    }

    public function Anular()
    {

        foreach ($this->model->Listar($_REQUEST['id']) as $v) {
            $venta = new venta();
            $venta->id_producto = $v->id_producto;
            $venta->cantidad = $v->cantidad;
            $this->producto->Sumar($venta);
            $venta->id_presupuesto = $v->id_presupuesto;
        }
        $this->presupuesto->VentaAnulada($venta);
        $this->ingreso->AnularVenta($_REQUEST['id']);
        $this->deuda->AnularVenta($_REQUEST['id']);
        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?c=venta');
    }
}

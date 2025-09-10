<?php
require_once 'model/compra.php';
require_once 'model/compra_tmp.php';
require_once 'model/producto.php';
require_once 'model/egreso.php';
require_once 'model/acreedor.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';
require_once 'model/metodo.php';
require_once 'model/pago_tmp.php';
require_once 'model/venta_tmp.php';
require_once 'model/venta.php';
require_once 'model/deuda.php';
require_once 'model/ingreso.php';
require_once 'model/devolucion_compras.php';
class compraController
{

    private $model;
    private $pago_tmp;
    private $deuda;
    private $egreso;
    private $ingreso;
    private $compra_tmp;
    private $acreedor;
    private $compra;
    private $cierre;
    private $producto;
    private $metodo;
    private $cliente;
    private $venta_tmp;
    private $venta;
    private $devolucion_compras;

    public function __CONSTRUCT()
    {
        $this->model = new compra();
        $this->compra = new compra();
        $this->cierre = new cierre();
        $this->compra_tmp = new compra_tmp();
        $this->producto = new producto();
        $this->egreso = new egreso();
        $this->acreedor = new acreedor();
        $this->egreso = new egreso();
        $this->cliente = new cliente();
        $this->venta = new venta();
        $this->venta_tmp = new venta_tmp();
        $this->pago_tmp = new pago_tmp();
        $this->deuda = new deuda();
        $this->metodo = new metodo();
        $this->ingreso = new ingreso();
        $this->devolucion_compras = new devolucion_compras();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/compra/compra.php';
        require_once 'view/footer.php';
    }

    public function Nuevacompra()
    {
        require_once 'view/header.php';
        require_once 'view/compra/nueva-compra.php';
        require_once 'view/footer.php';
    }

    public function Finalizar()
    {
        $compra = new compra();

        if (isset($_REQUEST['id'])) {
            $compra = $this->model->Obtener($_REQUEST['id']);
        }
        require_once 'view/compra/finalizar-step.php';
    }

    public function finalizar_compra()
    {
        require_once 'view/compra/finalizar-step.php';
    }

    public function Editar()
    {
        require_once 'view/header.php';
        require_once 'view/compra/editar-compra.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/compra/compra.php';
    }

    public function CompraDia()
    {
        require_once 'view/informes/compradiapdf.php';
    }

    public function CompraMes()
    {
        require_once 'view/informes/compramespdf.php';
    }

    public function detalles()
    {
        require_once 'view/compra/compra_detalles.php';
    }


    public function ListarDia()
    {

        require_once 'view/header.php';
        require_once 'view/compra/compradia.php';
        require_once 'view/footer.php';
    }

    public function ObtenerProducto()
    {
        $compra = new compra();

        $compra = $this->model->ObtenerProducto($_REQUEST['id_compra'], $_REQUEST['id_producto']);

        echo json_encode($compra);
    }

    public function Cambiar()
    {

        $compra = new compra();

        $id_item = $_REQUEST['id_item'];
        $id_compra = $_REQUEST['id_compra'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $cantidad_ant = $_REQUEST['cantidad_ant'];

        $cant = $cantidad_ant - $cantidad;

        if ($cantidad > 0) {
            $compra = $this->model->Cantidad($id_item, $id_compra, $cantidad);

            if ($compra->contado == 'Cuota')
                $acreedor = $this->acreedor->EditarMonto($id_compra, $compra->total_compra);

            if ($compra->contado == 'Contado')
                $acreedor = $this->egreso->EditarMonto($id_compra, $compra->total_compra);


            $this->producto->Sumar($codigo, $cant);
        }

        echo json_encode($compra);
    }

    public function Cancelar()
    {

        $id_item = $_REQUEST['id_item'];
        $id_compra = $_REQUEST['id_compra'];
        $codigo = $_REQUEST['codigo'];
        $cantidad = $_REQUEST['cantidad_item'];


        $compra = $this->model->Cantidad($id_item, $id_compra, 0);

        if ($compra->contado == 'Cuota')
            $acreedor = $this->acreedor->EditarMonto($id_compra, $compra->total_compra);

        if ($compra->contado == 'Contado')
            $acreedor = $this->egreso->EditarMonto($id_compra, $compra->total_compra);

        $compra = $this->model->CancelarItem($id_item);
        $this->producto->Sumar($codigo, $cantidad);
        header('location: ?c=compra_tmp&a=editar&id=' . $id_compra);
    }



    public function Crud()
    {
        $compra = new compra();

        if (isset($_REQUEST['id'])) {
            $compra = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/compra/compra-editar.php';
        require_once 'view/footer.php';
    }

    public function Cierre()
    {
        $compra = new compra();

        if (isset($_REQUEST['fecha'])) {
            $compra = $this->model->ListarDiaContado($_REQUEST['fecha']);
        }
        require_once 'view/informes/cierrepdf.php';
    }

    public function CierreMes()
    {
        $compra = new compra();

        if (isset($_REQUEST['fecha'])) {
            $compra = $this->model->ListarMes($_REQUEST['fecha']);
        }
        require_once 'view/informes/cierremespdf.php';
    }
    public function Obtener()
    {
        $compra = new compra();

        if (isset($_REQUEST['id'])) {
            $compra = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/compra/compra-editar.php';
    }

    public function MandarId()
    {
        if (isset($_POST['id_cliente'])) {
            $idCliente = $_POST['id_cliente'];
            $response = array('id_cliente' => $idCliente);
            //echo json_encode($response);
        }
        include('view/pago_tmp/pago_compra_tmp.php');
    }

    public function ObtenerSaldo()
    {
        $idDeuda = $_POST['id_deuda'];
        $pago_tmp = $this->pago_tmp->ObtenerTodo();
        $deuda = $this->deuda->Obtener($idDeuda);
        $saldo = $deuda->saldo;
        if (!empty($pago_tmp)) {
            foreach ($pago_tmp as $key => $value) {
                if ($value->id_deuda == $deuda->id) {
                    $saldo -= $value->monto;
                }
            }
        }
        echo json_encode($saldo);
    }

    public function AgregarItem()
    {


        $compra = new compra();

        $c = $this->model->Obtener($_REQUEST['id_compra']);

        $compra->id = 0;
        $compra->id_compra = $_REQUEST['id_compra'];
        $compra->id_cliente = $c->id_cliente;
        $compra->id_vendedor = $c->id_vendedor;
        $compra->id_producto = $_REQUEST['id_producto'];
        $compra->precio_compra = $_REQUEST['precio_compra'];
        $compra->precio_min = $_REQUEST['precio_min'];
        $compra->precio_may = $_REQUEST['precio_may'];
        $compra->subtotal = $_REQUEST['precio_compra'] * $_REQUEST['cantidad'];
        $compra->descuento = 0;
        $compra->iva = 10;
        $compra->total = $compra->subtotal - ($compra->subtotal * ($compra->descuento / 100));
        $compra->comprobante = $c->comprobante;
        $compra->nro_comprobante = $c->nro_comprobante;
        $compra->cantidad = $_REQUEST['cantidad'];
        $compra->margen_ganancia = 0; // costo nuevo
        $compra->fecha_compra = $c->fecha_compra;
        $compra->metodo = $c->metodo;
        $compra->contado = $c->contado;
        $compra->banco = $c->banco;


        //Registrar compra
        $this->model->Registrar($compra);
        //Sumar Stock
        $this->producto->Compra($compra);

        $this->egreso->ActualizarCompra($_REQUEST['id_compra']);

        header('Location:' . getenv('HTTP_REFERER'));
    }

    public function EliminarItem()
    {

        $p = $this->model->ObtenerItem($_REQUEST['id']);

        $compra = new compra();
        $compra->id_producto = $p->id_producto;
        $compra->cantidad = $p->cantidad;
        $this->producto->Restar($compra);

        $this->model->CancelarItem($_REQUEST['id']);
        $this->egreso->ActualizarCompra($p->id_compra);

        header('Location:' . getenv('HTTP_REFERER'));
    }

    public function GuardarUno()
    {


        $compra = new compra();

        $costo = $_REQUEST['precio_costo'] * $_REQUEST['cantidad'];
        $p_compra = $_REQUEST['precio_compra'] * $_REQUEST['cantidad'];

        $compra->id = 0;
        $compra->id_compra = $_REQUEST['id_compra'];
        $compra->id_cliente = $_REQUEST['id_cliente'];
        $compra->id_vendedor = $_REQUEST['id_compra'];
        $compra->id_producto = $_REQUEST['id_producto'];
        $compra->id_res = 0;
        $compra->precio_costo = $_REQUEST['precio_costo'];
        $compra->precio_compra = $_REQUEST['precio_compra'];
        $compra->subtotal = $p_compra;
        $compra->descuento = 0;
        $compra->iva = 0;
        $compra->total = $p_compra;
        $compra->comprobante = $_REQUEST['comprobante'];
        $compra->nro_comprobante = $_REQUEST['nro_comprobante'];
        $compra->cantidad = $_REQUEST['cantidad'];
        $compra->margen_ganancia = round(((($p_compra - $costo) * 100) / $costo), 2);
        $compra->fecha_compra = $_REQUEST['fecha_compra'];
        $compra->metodo = $_REQUEST['metodo'];
        $compra->banco = $_REQUEST['banco'];
        $compra->contado = $_REQUEST['contado'];


        $this->producto->Restar($compra);


        $compra->id > 0
            ? $this->model->Actualizar($compra)
            : $this->model->Registrar($compra);



        header('Location: index.php?c=compra_tmp&a=editar&id=' . $compra->id_compra);
    }

    public function Guardar()
    {

        $com = new compra();
        $com = $this->model->Ultimo();
        $sumaTotal = 0;

        // Obtener las cotizaciones del cierre actual
        if (!isset($_SESSION)) session_start();
        $cierre_actual = $this->cierre->Consultar($_SESSION['user_id']);
        $cot_dolar = $cierre_actual ? $cierre_actual->cot_dolar : 0;
        $cot_real = $cierre_actual ? $cierre_actual->cot_real : 0;

        foreach ($this->compra_tmp->Listar() as $c) {

            $compra = new compra();

            $compra->id = 0;
            $compra->id_compra = $com->id_compra + 1;
            $compra->id_cliente = $_REQUEST['id_cliente'];
            $compra->id_vendedor = $c->id_vendedor;
            $compra->id_producto = $c->id_producto;
            $compra->precio_compra = $c->precio_compra;
            $compra->precio_min = $c->precio_min;
            $compra->precio_may = $c->precio_may;
            $compra->subtotal = $c->precio_compra * $c->cantidad;
            $compra->descuento = 0;
            $compra->iva = $_REQUEST['ivaval'];
            $compra->total = $compra->subtotal - ($compra->subtotal * ($compra->descuento / 100));
            $compra->comprobante = $_REQUEST['comprobante'];
            $compra->nro_comprobante = $_REQUEST['nro_comprobante'];
            $compra->cantidad = $c->cantidad;
            $compra->margen_ganancia = 0; // costo nuevo
            $compra->fecha_compra = $_REQUEST['fecha_compra'];
            $compra->metodo = $_REQUEST['pago'];
            $compra->contado = $_REQUEST['contado'];
            $compra->banco = $_REQUEST['banco'];

            $compra->fecha = date("Y-m-d H:i");
            $compra->categoria = "compra";
            $producto = $this->producto->Codigo($c->id_producto);
            $compra->concepto = $c->cantidad . " Kg - " . $producto->producto;
            $compra->monto = $compra->total;

            $compra->fecha_emision = date("Y-m-d H:i");
            $compra->fecha_vencimiento = "2020-08-31";

            // Guardar las cotizaciones del cierre actual
            $compra->cot_usd = $cot_dolar;
            $compra->cot_rs = $cot_real;



            //Registrar compra
            $this->model->Registrar($compra);
            //Restar Stock
            //$this->producto->Compra($compra);
            //$precio_costo = (($precio_anterior * $cantidad_disponible) + ($precio_compra * $cantidad_compra))/($cantidad_disponible+$cantidad_compra);
            $this->producto->Compra($compra);

            $sumaTotal += $compra->total;
        }
        $suma = 0;
        $deuda = new deuda();
        $ingreso = new ingreso();
        $egreso = new egreso();
        foreach ($this->pago_tmp->Listar() as $r) {

            $suma += $r->monto;
            if (!isset($_SESSION)) session_start();
            //$cierre = $this->cierre->Consultar($_SESSION['user_id']);

            //PAGO AL CONTADO 
            if ($r->pago == "Efectivo") {
                $egreso->id_caja = $_REQUEST['id_caja'] ?? 1; // caja chica
            } else { //elseif ($r->pago == "Tarjeta" || $r->pago == "Transferencia" || $r->pago == "Giro" || $r->pago == "Giros Tigo" || $r->pago == "Vision Banco") {
                $egreso->id_caja = 2; // banco
            }

            $egreso->forma_pago = $r->pago;
            $egreso->id_cliente = $compra->id_cliente;
            $egreso->id_compra = $com->id_compra + 1;
            $egreso->fecha = date("Y-m-d H:i:s");
            $egreso->categoria = 'compra';
            $egreso->concepto = $r->id_deuda != 0 ? 'compra con nota de credito' : 'compra en efectivo';
            $egreso->comprobante = $_REQUEST['comprobante'] . ' N° ' . $_REQUEST['nro_comprobante'];
            $egreso->monto = $r->monto;
            //campo moneda y cambio
            $egreso->moneda = $r->moneda;
            $egreso->cambio = $r->cambio;
            $egreso->sucursal = 0;
            $this->egreso->Registrar($egreso);



            if ($r->id_deuda != 0 && $r->id_deuda != null && $r->id_deuda != '') {
                $ingreso->id_cliente = $compra->id_cliente;
                $ingreso->id_caja = $_REQUEST['id_caja'] ?? 1;
                $ingreso->id_deuda = $r->pago;
                $ingreso->fecha = date("Y-m-d H:i:s");
                $ingreso->categoria = 'compra';
                $ingreso->concepto = 'compra mediante nota de crédito';
                $ingreso->comprobante = $_REQUEST['comprobante'] . ' N° ' . $_REQUEST['nro_comprobante'];
                $ingreso->monto = $r->monto;
                $ingreso->forma_pago = 'Nota de Crédito';
                $ingreso->sucursal = 0;
                $this->ingreso->Registrar($ingreso);

                $deuda->id = $r->id_deuda;
                $deuda->monto = $r->monto;
                $this->deuda->Restar($deuda);
            }
        }

        if ($_REQUEST['contado'] == "Credito") {

            if (!isset($_SESSION)) session_start();
            $acreedor = new acreedor();
            $acreedor->id_cliente = $_REQUEST['id_cliente'];
            $acreedor->id_compra = $com->id_compra + 1;
            $acreedor->fecha = date("Y-m-d", strtotime($_REQUEST['fecha_compra']));
            $acreedor->concepto = "compra a crédito";
            $acreedor->monto = $sumaTotal;
            $acreedor->saldo = isset($_REQUEST['entrega']) ? $sumaTotal - $_REQUEST['entrega'] : $sumaTotal;
            $acreedor->sucursal = $_SESSION['sucursal'];
            $this->acreedor->Registrar($acreedor);

            if ($_REQUEST['entrega'] > 0) {
                $id_acreedor = $this->acreedor->UltimoID();
                $egreso->id_caja = 1;
                $egreso->categoria = 'compra';
                $egreso->id_cliente = $compra->id_cliente;
                $egreso->id_compra = $com->id_compra + 1;
                $egreso->id_acreedor = $id_acreedor->id;
                $egreso->fecha = date("Y-m-d H:i:s");
                $egreso->concepto = 'compra cobro parcial';
                $egreso->monto = $_REQUEST['entrega'];
                //campo moneda y cambio
                $egreso->moneda = $r->moneda;
                $egreso->cambio = $r->cambio;
                $egreso->sucursal = 0;
                $egreso->forma_pago = 'Efectivo';
                $this->egreso->Registrar($egreso);
            }
        }

        $this->compra_tmp->Vaciar();
        $this->pago_tmp->Vaciar();

        header('Location: index.php?c=compra&a=listardia');
    }

    public function Eliminar()
    {

        foreach ($this->model->Listar($_REQUEST['id']) as $v) {
            $compra = new compra();
            $compra->id_producto = $v->id_producto;
            $compra->cantidad = $v->cantidad;
            $this->producto->Restar($compra);
        }
        $this->egreso->Eliminarcompra($_REQUEST['id']);
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=compra');
    }

    public function Anular()
    {

        foreach ($this->model->Listar($_REQUEST['id']) as $v) {
            $compra = new compra();
            $compra->id_producto = $v->id_producto;
            $compra->cantidad = $v->cantidad;
            $this->producto->Restar($compra);
        }
        $this->egreso->Anularcompra($_REQUEST['id']);
        $this->acreedor->Anularcompra($_REQUEST['id']);
        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?c=compra');
    }

    public function editarCompraYPago()
    {
        // Vaciar la tabla compras_tmp antes de cargar la vista
        $this->compra_tmp->Vaciar();
        
        $id_compra = isset($_GET['id_compra']) ? $_GET['id_compra'] : 0;
        if ($id_compra) {
            $productos_tmp = $this->compra_tmp->Listar($id_compra);
            if (empty($productos_tmp)) {
                $productos = $this->model->Listar($id_compra);
                foreach ($productos as $producto) {
                    $tmp = new compra_tmp();
                    $tmp->id_compra = $id_compra;
                    $tmp->id_vendedor = $_SESSION['user_id'];
                    $tmp->id_producto = $producto->id_producto;
                    $tmp->precio_compra = $producto->precio_compra;
                    $tmp->precio_min = $producto->precio_min;
                    $tmp->precio_may = isset($producto->precio_may) ? $producto->precio_may : 0; // Ensure precio_may is set
                    $tmp->cantidad = $producto->cantidad;
                    $tmp->fecha_compra = $producto->fecha_compra;
                    $this->compra_tmp->Registrar($tmp);
                }
            }
        }
        require_once 'view/header.php';
        require_once 'view/compra/editar-compra-y-pago.php';
        require_once 'view/footer.php';
    }

    public function cargarCompraEnTmp()
    {
        // Esta función ya no redirige, se utiliza dentro de editarCompraYPago
    }

    public function ObtenerPreciosProducto() {
        $id_producto = isset($_GET['id_producto']) ? $_GET['id_producto'] : 0;
        if ($id_producto) {
            $producto = $this->producto->Obtener($id_producto);
            error_log(print_r($producto, true)); // Log para inspeccionar el objeto producto
            if ($producto) {
                echo json_encode([
                    'success' => true,
                    'precio_compra' => $producto->precio_compra,
                    'precio_min' => $producto->precio_min,
                    'precio_may' => $producto->precio_may,
                    'producto' => $producto->producto
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado']);
        }
    }

    public function finalizar_edicion()
    {
        // Cargar los datos de la compra temporal para el proceso de finalización
        $id_compra = isset($_GET['id_compra']) ? $_GET['id_compra'] : 0;
        
        if ($id_compra) {
            // Verificar que existan productos en compra_tmp para esta compra
            $productos_tmp = $this->compra_tmp->Listar($id_compra);
            if (empty($productos_tmp)) {
                // Si no hay productos en tmp, cargarlos desde la compra original
                $productos = $this->model->Listar($id_compra);
                foreach ($productos as $producto) {
                    $tmp = new compra_tmp();
                    $tmp->id_compra = $id_compra;
                    $tmp->id_vendedor = $_SESSION['user_id'];
                    $tmp->id_producto = $producto->id_producto;
                    $tmp->precio_compra = $producto->precio_compra;
                    $tmp->precio_min = $producto->precio_min;
                    $tmp->precio_may = isset($producto->precio_may) ? $producto->precio_may : 0;
                    $tmp->cantidad = $producto->cantidad;
                    $tmp->fecha_compra = $producto->fecha_compra;
                    $this->compra_tmp->Registrar($tmp);
                }
            }
        }
        
        // Cargar la vista de finalización de edición (similar a finalizar-step.php)
        require_once 'view/compra/finalizar-edicion-step.php';
    }

    public function actualizar_compra()
    {
        if (!isset($_SESSION)) session_start();
        
        $id_compra = $_REQUEST['id_compra'];
        
        // Obtener la compra existente para referencia
        $compra_existente = $this->model->Obtener($id_compra);
        
        // Primero, eliminar todos los items existentes de la compra
        $items_existentes = $this->model->Listar($id_compra);
        foreach ($items_existentes as $item) {
            // Restar del inventario antes de eliminar el item
            $producto_restar = new compra();
            $producto_restar->id_producto = $item->id_producto;
            $producto_restar->cantidad = $item->cantidad;
            $this->producto->Restar($producto_restar);
        }
        
        // Eliminar todos los items de la compra
        $this->model->Eliminar($id_compra);
        
        // Eliminar los egresos existentes relacionados con esta compra
        $this->egreso->Eliminarcompra($id_compra);
        
        // Eliminar registros de acreedores si existían (para compras a crédito)
        $this->acreedor->Anularcompra($id_compra);
        
        // Si es compra a crédito, limpiar pagos temporales (no se usan)
        if ($_REQUEST['contado'] == "Credito") {
            $this->pago_tmp->Vaciar();
        }
        
        // Obtener las cotizaciones del cierre actual
        $cierre_actual = $this->cierre->Consultar($_SESSION['user_id']);
        $cot_dolar = $cierre_actual ? $cierre_actual->cot_dolar : 0;
        $cot_real = $cierre_actual ? $cierre_actual->cot_real : 0;
        
        $sumaTotal = 0;
        
        // Agregar los productos desde compra_tmp como nuevos items
        $productos_tmp = $this->compra_tmp->Listar($id_compra);
        foreach ($productos_tmp as $producto_tmp) {
            $compra = new compra();
            
            $compra->id = 0; // Nuevo registro
            $compra->id_compra = $id_compra;
            $compra->id_cliente = $_REQUEST['id_cliente'];
            $compra->id_vendedor = $producto_tmp->id_vendedor;
            $compra->id_producto = $producto_tmp->id_producto;
            $compra->precio_compra = $producto_tmp->precio_compra;
            $compra->precio_min = $producto_tmp->precio_min;
            $compra->precio_may = $producto_tmp->precio_may;
            $compra->subtotal = $producto_tmp->precio_compra * $producto_tmp->cantidad;
            $compra->descuento = $_REQUEST['descuentoval'] ?? 0;
            $compra->iva = $_REQUEST['ivaval'] ?? 0;
            $compra->total = $compra->subtotal - ($compra->subtotal * ($compra->descuento / 100));
            $compra->comprobante = $_REQUEST['comprobante'];
            $compra->nro_comprobante = $_REQUEST['nro_comprobante'];
            $compra->cantidad = $producto_tmp->cantidad;
            $compra->margen_ganancia = 0;
            $compra->fecha_compra = $_REQUEST['fecha_compra'];
            $compra->metodo = isset($_REQUEST['pago']) ? $_REQUEST['pago'] : (isset($compra_existente->metodo) ? $compra_existente->metodo : '');
            $compra->banco = isset($compra_existente->banco) ? $compra_existente->banco : '';
            $compra->contado = $_REQUEST['contado'];
            
            // Guardar las cotizaciones del cierre actual
            $compra->cot_usd = $cot_dolar;
            $compra->cot_rs = $cot_real;
            
            // Registrar el nuevo item de compra
            $this->model->Registrar($compra);
            
            // Agregar al inventario
            $this->producto->Compra($compra);
            
            $sumaTotal += $compra->total;
        }
        
        // Procesar los pagos y generar egresos (igual que en el método Guardar)
        $suma = 0;
        $deuda = new deuda();
        $ingreso = new ingreso();
        $egreso = new egreso();
        
        foreach ($this->pago_tmp->Listar() as $r) {
            $suma += $r->monto;
            
            //PAGO AL CONTADO 
            if ($r->pago == "Efectivo") {
                $egreso->id_caja = $_REQUEST['id_caja'] ?? 1; // caja chica
            } else {
                $egreso->id_caja = 2; // banco
            }

            $egreso->forma_pago = $r->pago;
            $egreso->id_cliente = $_REQUEST['id_cliente'];
            $egreso->id_compra = $id_compra;
            $egreso->fecha = date("Y-m-d H:i:s");
            $egreso->categoria = 'compra';
            $egreso->concepto = $r->id_deuda != 0 ? 'compra con nota de credito (editada)' : 'compra en efectivo (editada)';
            $egreso->comprobante = $_REQUEST['comprobante'] . ' N° ' . $_REQUEST['nro_comprobante'];
            $egreso->monto = $r->monto;
            //campo moneda y cambio
            $egreso->moneda = $r->moneda;
            $egreso->cambio = $r->cambio;
            $egreso->sucursal = 0;
            $this->egreso->Registrar($egreso);

            if ($r->id_deuda != 0 && $r->id_deuda != null && $r->id_deuda != '') {
                $ingreso->id_cliente = $_REQUEST['id_cliente'];
                $ingreso->id_caja = $_REQUEST['id_caja'] ?? 1;
                $ingreso->id_deuda = $r->pago;
                $ingreso->fecha = date("Y-m-d H:i:s");
                $ingreso->categoria = 'compra';
                $ingreso->concepto = 'compra mediante nota de crédito (editada)';
                $ingreso->comprobante = $_REQUEST['comprobante'] . ' N° ' . $_REQUEST['nro_comprobante'];
                $ingreso->monto = $r->monto;
                $ingreso->forma_pago = 'Nota de Crédito';
                $ingreso->sucursal = 0;
                $this->ingreso->Registrar($ingreso);

                $deuda->id = $r->id_deuda;
                $deuda->monto = $r->monto;
                $this->deuda->Restar($deuda);
            }
        }

        // Manejar compra a crédito si aplica
        if ($_REQUEST['contado'] == "Credito") {
            $acreedor = new acreedor();
            $acreedor->id_cliente = $_REQUEST['id_cliente'];
            $acreedor->id_compra = $id_compra;
            $acreedor->fecha = date("Y-m-d", strtotime($_REQUEST['fecha_compra']));
            $acreedor->concepto = "compra a crédito (editada)";
            $acreedor->monto = $sumaTotal;
            $acreedor->saldo = isset($_REQUEST['entrega']) ? $sumaTotal - $_REQUEST['entrega'] : $sumaTotal;
            $acreedor->sucursal = $_SESSION['sucursal'];
            $this->acreedor->Registrar($acreedor);

            if ($_REQUEST['entrega'] > 0) {
                $id_acreedor = $this->acreedor->UltimoID();
                $egreso->id_caja = 1;
                $egreso->categoria = 'compra';
                $egreso->id_cliente = $_REQUEST['id_cliente'];
                $egreso->id_compra = $id_compra;
                $egreso->id_acreedor = $id_acreedor->id;
                $egreso->fecha = date("Y-m-d H:i:s");
                $egreso->concepto = 'compra cobro parcial (editada)';
                $egreso->monto = $_REQUEST['entrega'];
                $egreso->moneda = 'GS'; // Valor por defecto
                $egreso->cambio = 1; // Valor por defecto
                $egreso->sucursal = 0;
                $egreso->forma_pago = 'Efectivo';
                $this->egreso->Registrar($egreso);
            }
        }
        
        // Limpiar la tabla temporal
        $this->compra_tmp->VaciarPorCompra($id_compra);
        $this->pago_tmp->Vaciar();
        
        // Redirigir a la lista de compras
        header('Location: ?c=compra');
        exit();
    }
}

<?php
require_once 'model/compra_tmp.php';
require_once 'model/compra.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/egreso.php';
require_once 'model/cierre.php';
require_once 'model/pago_tmp.php';
require_once 'model/venta_tmp.php';
require_once 'model/venta.php';
require_once 'model/metodo.php';
require_once 'model/deuda.php';
class compra_tmpController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new compra_tmp();
        $this->compra_tmp = new compra_tmp();
        $this->cierre = new cierre();
        $this->compra = new compra();
        $this->venta = new venta();
        $this->venta_tmp = new venta_tmp();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->pago_tmp = new pago_tmp();
        $this->cliente = new cliente();
        $this->metodo = new metodo();
        $this->deuda = new deuda();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/compra/nueva-compra.php';
        require_once 'view/footer.php';
    }

    public function Editar()
    {

        $compra = new compra();

        if (isset($_REQUEST['id'])) {
            $compra = $this->compra->ObtenerUno($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/compra/compra-editar.php';
        require_once 'view/footer.php';
    }


    public function Listar()
    {
        require_once 'view/compra/nueva-compra.php';
    }



    public function Crud()
    {
        $compra_tmp = new compra_tmp();

        if (isset($_REQUEST['id'])) {
            $compra_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/compra_tmp/compra_tmp-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $compra_tmp = new compra_tmp();

        if (isset($_REQUEST['id'])) {
            $compra_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/compra_tmp/compra_tmp-editar.php';
    }

    public function ObtenerMoneda()
    {
        $compra_tmp = new compra_tmp();

        $compra_tmp = $this->model->ObtenerMoneda();
    }

    public function Guardar()
    {

        $producto = $this->producto->Obtener($_REQUEST['id_producto']);
        if (!isset($_SESSION)) session_start();
        $compra_tmp = new compra_tmp();

        $compra_tmp->id = 0;
        $compra_tmp->id_compra = isset($_REQUEST['id_compra']) ? $_REQUEST['id_compra'] : 1;
        $compra_tmp->id_vendedor = $_SESSION['user_id'];
        $compra_tmp->id_producto = $_REQUEST['id_producto'];
        $compra_tmp->precio_compra = $_REQUEST['precio_compra'];
        $compra_tmp->precio_min = $_REQUEST['precio_min'];
        $compra_tmp->precio_may = $_REQUEST['precio_may'];
        $compra_tmp->cantidad = $_REQUEST['cantidad'];
        $compra_tmp->fecha_compra = date("Y-m-d H:i");
        
        // Calcular subtotal y total
        $compra_tmp->subtotal = $compra_tmp->precio_compra * $compra_tmp->cantidad;
        $compra_tmp->total = $compra_tmp->subtotal; // No hay descuentos por ahora


        $compra_tmp->id > 0
            ? $this->model->Actualizar($compra_tmp)
            : $this->model->Registrar($compra_tmp);

        // Manejar la redirección con parámetros de retorno si están presentes
        if (isset($_REQUEST['return']) && isset($_REQUEST['return_action'])) {
            $return_controller = $_REQUEST['return'];
            $return_action = $_REQUEST['return_action'];
            $id_compra = isset($_REQUEST['id_compra']) ? $_REQUEST['id_compra'] : '';
            
            header("Location: index.php?c={$return_controller}&a={$return_action}&id_compra={$id_compra}");
        } else {
            header('Location: index.php?c=compra_tmp');
        }
    }

    public function GuardarUno()
    {


        $compra = new compra();

        $costo = $_REQUEST['precio_costo'];
        $compra = $_REQUEST['precio_compra'];

        $compra->id = 0;
        $compra->id_compra = $_REQUEST['id_compra'];
        $compra->id_cliente = $_REQUEST['id_cliente'];
        $compra->id_vendedor = $_REQUEST['id_compra'];
        $compra->id_producto = $_REQUEST['codigo'];
        $compra->precio_costo = $_REQUEST['precio_costo'];
        $compra->precio_compra = $_REQUEST['precio_compra'];
        $compra->subtotal = $_REQUEST['subtotal'];
        $compra->descuento = 0;
        $compra->iva = 0;
        $compra->total = $_REQUEST['total'];
        $compra->comprobante = $_REQUEST['comprobante'];
        $compra->nro_comprobante = $_REQUEST['nro_comprobante'];
        $compra->cantidad = $_REQUEST['cantidad'];
        $compra->margen_ganancia = round(((($compra - $costo) * 100) / $costo), 2);
        $compra->fecha_compra = $_REQUEST['fecha_compra'];
        $compra->metodo = $_REQUEST['metodo'];
        $compra->banco = $_REQUEST['banco'];
        $compra->contado = $_REQUEST['contado'];


        $compra->id > 0
            ? $this->compra->Actualizar($compra_tmp)
            : $this->compra->Registrar($compra_tmp);

        if ($compra->contado == 'Cuota')
            $deuda = $this->deuda->EditarMonto($compra->id_compra, $compra->total);

        if ($compra->contado == 'Contado')
            $deuda = $this->egreso->EditarMonto($compra->id_compra, $compra->total);

        header('Location: index.php?c=compra_tmp&a=editar&id=' . $compra->id_compra);
    }

    public function Moneda()
    {

        $compra_tmp = new compra_tmp();

        $compra_tmp->id = 0;
        $compra_tmp->reales = $_REQUEST['reales'];
        $compra_tmp->dolares = $_REQUEST['dolares'];
        $compra_tmp->monto_inicial = $_REQUEST['monto_inicial'];

        $this->model->Moneda($compra_tmp);

        header('Location: index.php?c=compra_tmp');
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        
        // Manejar la redirección con parámetros de retorno si están presentes
        if (isset($_REQUEST['return']) && isset($_REQUEST['return_action'])) {
            $return_controller = $_REQUEST['return'];
            $return_action = $_REQUEST['return_action'];
            $id_compra = isset($_REQUEST['id_compra']) ? $_REQUEST['id_compra'] : '';
            
            header("Location: index.php?c={$return_controller}&a={$return_action}&id_compra={$id_compra}");
        } else {
            header('Location: index.php?c=compra_tmp');
        }
    }
}

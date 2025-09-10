<?php
require_once 'model/ingreso.php';
require_once 'model/egreso.php';
require_once 'model/deuda.php';
require_once 'model/venta.php';
require_once 'model/venta_tmp.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/metodo.php';
require_once 'model/producto.php';
require_once 'model/acreedor.php';


class ingresoController
{

    private $model;
    private $egreso;
    private $deuda;
    private $acreedor;
    private $venta;
    private $cliente;
    private $venta_tmp;
    private $cierre;
    private $metodo;
    private $producto;

    public function __CONSTRUCT()
    {
        $this->model = new ingreso();
        $this->egreso = new egreso();
        $this->deuda = new deuda();
        $this->acreedor = new acreedor();
        $this->venta = new venta();
        $this->cliente = new cliente();
        $this->venta_tmp = new venta_tmp();
        $this->cierre = new cierre();
        $this->metodo = new metodo();
        $this->producto = new producto();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/ingreso/ingreso.php';
        require_once 'view/footer.php';
    }

    public function Balance()
    {
        require_once 'view/header.php';
        if (($_SESSION['nivel'] != 1)) {
            echo '<h3>No tienes permiso</h3>';
        } else {
            require_once 'view/informes/balance.php';
        }
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/ingreso/ingreso.php';
    }

    public function Deposito()
    {

        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {
            require_once 'view/ingreso/deposito.php';
        } else {
            echo "<h1>Debe hacer apertura de caja</h1>";
        }
        require_once 'view/footer.php';
    }


    public function detalles()
    {
        require_once 'view/deuda/pago_detalles.php';
    }

    public function BalanceMes()
    {
        require_once 'view/informes/balancemespdf.php';
    }

    public function Recibo()
    {
        require_once 'view/informes/recibo_ingreso.php';
    }

    public function Crud()
    {
        $ingreso = new ingreso();

        if (isset($_REQUEST['id'])) {
            $ingreso = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/ingreso/ingreso-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $ingreso = new ingreso();

        if (isset($_REQUEST['id'])) {
            $ingreso = $this->model->Obtener($_REQUEST['id']);
        }
        if (!isset($_SESSION['user_id'])) {
            if (!isset($_SESSION)) session_start();
        }

        if ($_SESSION['nivel'] > 1) {
            require_once 'view/ingreso/deposito-editar.php';
        } else {
            require_once 'view/ingreso/ingreso-editar.php';
        }
    }


    public function Guardar()
    {
        $ingreso = new ingreso();

        $ingreso->id = $_REQUEST['id'];
        $ingreso->id_cliente = $_REQUEST['id_cliente'];
        if (!isset($_SESSION)) session_start();
        $cierre = $this->cierre->Consultar($_SESSION['user_id']);

        if ($_REQUEST['forma_pago'] == "Efectivo") {
            if ($_SESSION['nivel'] == 4) { // es gerente, ir a tesoreria
                $ingreso->id_caja = 3;    //tesoreria
            } else {
                $ingreso->id_caja = 1; //caja chica
            }
        } else {
            $ingreso->id_caja = 2; // banco
        }

        $ingreso->id_venta = (isset($_REQUEST['id_venta'])) ? $_REQUEST['id_venta'] : 0;
        $ingreso->fecha = $_REQUEST['fecha'];
        $ingreso->categoria = $_REQUEST['categoria'];
        $ingreso->concepto = $_REQUEST['concepto'];
        $ingreso->comprobante = $_REQUEST['comprobante'];
        $ingreso->monto = $_REQUEST['monto'];
        $ingreso->forma_pago = $_REQUEST['forma_pago'];
        $ingreso->sucursal = $_REQUEST['sucursal'];

        // Agregar moneda y cotización
        $ingreso->moneda = isset($_REQUEST['moneda']) ? $_REQUEST['moneda'] : 'GS';
        
        // Si no es efectivo, forzar GS
        if ($_REQUEST['forma_pago'] !== "Efectivo") {
            $ingreso->moneda = 'GS';
        }
        
        // Obtener cotización actual si es necesario
        if ($ingreso->moneda !== 'GS') {
            $cambio_actual = $this->cierre->Ultimo();
            if ($ingreso->moneda == 'USD') {
                $ingreso->cambio = $cambio_actual->cot_dolar;
            } elseif ($ingreso->moneda == 'RS') {
                $ingreso->cambio = $cambio_actual->cot_real;
            }
        } else {
            $ingreso->cambio = 1;
        }

        $ingreso->id > 0
            ? $this->model->Actualizar($ingreso)
            : $this->model->Registrar($ingreso);

        if ($_SESSION['nivel'] > 1) {
            header('Location: index.php?c=ingreso&a=deposito');
        } else {
            header('Location: index.php?c=ingreso');
        }
    }

    public function entradaExterna()
    {
        $ingreso = new ingreso();

        $ingreso->id_cliente = 0;
        if (!isset($_SESSION)) session_start();
        $cierre = $this->cierre->Consultar($_SESSION['user_id']);

        $ingreso->id_caja = $_REQUEST['id_caja'];

        $ingreso->id_venta = 0;
        $ingreso->fecha = date("Y-m-d H:i");
        $ingreso->categoria = 'Inversión';
        $ingreso->concepto = 'Entrada externa por inversión';
        $ingreso->comprobante = '-';
        if ($ingreso->id_caja == 2) {
            $ingreso->forma_pago = 'Transferencia';
        } else {
            $ingreso->forma_pago = 'Efectivo';
        }
        $ingreso->monto = $_REQUEST['monto'];
        $ingreso->sucursal = 0;

        $this->model->Registrar($ingreso);
        header('Location:' . getenv('HTTP_REFERER'));
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=ingreso');
    }

    public function Anular()
    {
        $this->model->Eliminar($_REQUEST['id']);
        $ingreso = new ingreso();
        $ingreso = $this->model->Obtener($_REQUEST['id']);

        $deuda = new deuda();
        $deuda->id = $ingreso->id_deuda;
        $deuda->monto = $ingreso->monto;

        $this->model->Eliminar($_REQUEST['id']);
        $this->deuda->SumarSaldo($deuda);
        header('Location: index.php?c=ingreso&a=deposito');
    }

    public function EliminarPago()
    {

        $ingreso = new ingreso();
        $ingreso = $this->model->Obtener($_REQUEST['id']);

        $montoIngreso = $ingreso->monto;
        $cotizacionIngreso = $ingreso->cambio ?? 1; // Si no hay cotización, usar 1 como valor por defecto
        $montoADevolver = $montoIngreso * $cotizacionIngreso;

        $deuda = new deuda();
        $deuda->id = $ingreso->id_deuda;
        $deuda->monto = $montoADevolver;

        $this->model->Eliminar($_REQUEST['id']);
        $this->deuda->SumarSaldo($deuda);
        header('Location: index.php?c=ingreso');
    }
}

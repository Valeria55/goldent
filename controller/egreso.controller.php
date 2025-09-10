<?php
require_once 'model/egreso.php';
require_once 'model/acreedor.php';
require_once 'model/compra.php';
require_once 'model/compra_tmp.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/caja.php';
require_once 'model/metodo.php';

class egresoController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new egreso();
        $this->acreedor = new acreedor();
        $this->compra = new compra();
        $this->cliente = new cliente();
        $this->compra_tmp = new compra_tmp();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->metodo = new metodo();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/egreso/egreso.php';
        require_once 'view/footer.php';
    }

    public function Balance()
    {
        require_once 'view/header.php';
        require_once 'view/informes/balance.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/egreso/egreso.php';
    }

    public function EgresoRango()
    {

        require_once 'view/informes/egresosrangopdf.php';
    }

    public function ReciboEgreso()
    {

        require_once 'view/informes/recibo_egreso.php';
    }

    public function Extraccion()
    {
        require_once 'view/header.php';
        if ($this->cierre->Consultar($_SESSION['user_id'])) {
            require_once 'view/egreso/extraccion.php';
        } else {
            echo "<h1>Debe hacer apertura de caja</h1>";
        }
        require_once 'view/footer.php';
    }

    public function detalles()
    {
        require_once 'view/acreedor/pago_detalles.php';
    }

    public function Crud()
    {
        $egreso = new egreso();

        if (isset($_REQUEST['id'])) {
            $egreso = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/egreso/egreso-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $egreso = new egreso();

        if (isset($_REQUEST['id'])) {
            $egreso = $this->model->Obtener($_REQUEST['id']);
        }

        if (!isset($_SESSION['user_id'])) {
            if (!isset($_SESSION)) session_start();
        }

        if ($_SESSION['nivel'] > 1) {
            require_once 'view/egreso/egreso-editar.php';
            // require_once 'view/egreso/extraccion-editar.php';
        } else {
            require_once 'view/egreso/egreso-editar.php';
        }
    }


    public function Guardar()
    {
        $egreso = new egreso();

        $egreso->id = $_REQUEST['id'];
        $egreso->id_cliente = $_REQUEST['id_cliente'];
        if (!isset($_SESSION)) session_start();
        $cierre = $this->cierre->Consultar($_SESSION['user_id']);
        if ($_REQUEST['forma_pago'] == "Efectivo") {

            if ($_SESSION['nivel'] == 4) { // es gerente, ir a tesoreria
                $egreso->id_caja = 3;    //tesoreria
            } else {
                $egreso->id_caja = 1; //caja chica
            }
        } else {
            $egreso->id_caja = 2;
        }
        $egreso->id_venta = (isset($_REQUEST['id_venta'])) ? $_REQUEST['id_venta'] : 0;
        $egreso->fecha = $_REQUEST['fecha'];
        $egreso->categoria = $_REQUEST['categoria'];
        $egreso->concepto = $_REQUEST['concepto'];
        $egreso->comprobante = $_REQUEST['comprobante'];
        $egreso->monto = $_REQUEST['monto'];
        $egreso->forma_pago = $_REQUEST['forma_pago'];
        $egreso->sucursal = $_REQUEST['sucursal'];
        $egreso->nro_cheque = $_REQUEST['nro_cheque'];
        $egreso->plazo = $_REQUEST['plazo'] == '' ? null : $_REQUEST['plazo'];

        // Agregar moneda y cotización
        $egreso->moneda = isset($_REQUEST['moneda']) ? $_REQUEST['moneda'] : 'GS';

        // Si no es efectivo, forzar GS
        if ($_REQUEST['forma_pago'] !== "Efectivo") {
            $egreso->moneda = 'GS';
        }

        // Obtener cotización actual si es necesario
        if ($egreso->moneda !== 'GS') {
            $cambio_actual = $this->cierre->Ultimo();
            if ($egreso->moneda == 'USD') {
                $egreso->cambio = $cambio_actual->cot_dolar;
            } elseif ($egreso->moneda == 'RS') {
                $egreso->cambio = $cambio_actual->cot_real;
            }
        } else {
            $egreso->cambio = 1;
        }

        $egreso->id > 0
            ? $this->model->Actualizar($egreso)
            : $this->model->Registrar($egreso);

        if (!isset($_SESSION['user_id'])) {
            if (!isset($_SESSION)) session_start();
        }

        if ($_SESSION['nivel'] > 1) {
            header('Location: index.php?c=egreso&a=extraccion');
        } else {
            header('Location: index.php?c=egreso');
        }
    }

    public function Eliminar()
    {
        $egreso = $this->model->Obtener($_REQUEST['id']);
        if ($egreso->categoria == 'Transferencia') {
            $this->model->EliminarTransferencia($egreso->id_transferencia);
        } else {
            $this->model->Eliminar($_REQUEST['id']);
        }
        header('Location: index.php?c=egreso');
    }

    public function Anular()
    {
        $egreso = $this->model->Obtener($_REQUEST['id']);
        if ($egreso->categoria == 'Transferencia') {
            $this->model->EliminarTransferencia($egreso->id_transferencia);
        } else {
            $this->model->Eliminar($_REQUEST['id']);
        }
        header('Location: index.php?c=egreso&a=extraccion');
    }

    public function EliminarPago()
    {

        $egreso = new egreso();
        $egreso = $this->model->Obtener($_REQUEST['id']);

        $montoEgreso = $egreso->monto;
        $cotizacionEgreso = $egreso->cambio ?? 1; // Si no hay cotización, usar 1 como valor por defecto
        $montoADevolver = $montoEgreso * $cotizacionEgreso;

        $acreedor = new acreedor();
        $acreedor->id = $egreso->id_acreedor;
        //devolvemos en monto en guaranies
        $acreedor->monto = $montoADevolver;

        $this->model->Eliminar($_REQUEST['id']);
        $this->acreedor->SumarSaldo($acreedor);
        header('Location: index.php?c=egreso');
    }
}

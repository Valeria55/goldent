<?php
require_once 'model/compra.php';
require_once 'model/compra_tmp.php';
require_once 'model/producto.php';
require_once 'model/egreso.php';
require_once 'model/acreedor.php';
require_once 'model/egreso.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/metodo.php';

class acreedorController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new acreedor();
        $this->compra_tmp = new compra_tmp();
        $this->producto = new producto();
        $this->cierre = new cierre();
        $this->egreso = new egreso();
        $this->compra = new compra();
        $this->egreso = new egreso();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->metodo = new metodo();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/acreedor/acreedor.php';
        require_once 'view/footer.php';
    }



    public function Listar()
    {
        require_once 'view/acreedor/acreedor.php';
    }



    public function Crud()
    {
        $acreedor = new acreedor();

        if (isset($_REQUEST['id'])) {
            $acreedor = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/acreedor/acreedor-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $acreedor = new acreedor();

        if (isset($_REQUEST['id'])) {
            $acreedor = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/acreedor/acreedor-editar.php';
    }

    public function clientepdf()
    {
        $acreedor = new acreedor();

        require_once 'view/informes/extractoproveedorpdf.php';
    }

    public function PagarModal()
    {
        $acreedor = new acreedor();

        if (isset($_REQUEST['id'])) {
            $r = $this->model->Obtener($_REQUEST['id']);
        }

        // Obtener cotizaciones del cierre actual
        if (!isset($_SESSION)) session_start();
        $cierre_actual = $this->cierre->Consultar($_SESSION['user_id']);
        $cotizacion_usd = $cierre_actual->cot_dolar ?? 7500; // Valor por defecto si no hay cierre
        $cotizacion_rs = $cierre_actual->cot_real ?? 1500; //

        require_once 'view/acreedor/pagar-form.php';
    }

    public function Guardar()
    {
        $acreedor = new acreedor();

        if (!isset($_SESSION)) session_start();

        $acreedor->id = $_REQUEST['id'];
        $acreedor->id_cliente = $_REQUEST['id_cliente'];
        $acreedor->id_compra = $_REQUEST['id_compra'];
        $acreedor->fecha = $_REQUEST['fecha'];
        $acreedor->concepto = $_REQUEST['concepto'];
        $acreedor->monto = $_REQUEST['monto'];
        $acreedor->saldo = $_REQUEST['saldo'];
        $acreedor->sucursal = $_SESSION['sucursal'];

        $acreedor->id > 0
            ? $this->model->Actualizar($acreedor)
            : $this->model->Registrar($acreedor);

        header('Location: index.php?c=acreedor');
    }

    public function Pagar()
    {
        if (!isset($_SESSION)) session_start();
        $egreso = new egreso();

        $egreso->id_cliente = $_REQUEST['id_cliente'];

        if ($_REQUEST['forma_pago'] == "Efectivo") {
            if ($_SESSION['nivel'] == 4) { // es gerente, ir a tesoreria
                $egreso->id_caja = 3;    //tesoreria
            } else {
                $egreso->id_caja = 1; //caja chica
            }
        } else {
            $egreso->id_caja = 2; // banco
        }

        $egreso->id_compra = $_REQUEST['id_compra'];
        $egreso->id_acreedor = $_REQUEST['id'];
        $egreso->forma_pago = $_REQUEST['forma_pago'];
        $egreso->fecha = date("Y-m-d H:i");
        $egreso->categoria = 'Pago';
        $egreso->concepto = "Pago a proveedor " . $_REQUEST['cli'];
        $egreso->comprobante = $_REQUEST['comprobante'];

        // Obtener cotizaciones del cierre actual
        $cierre_actual = $this->cierre->Consultar($_SESSION['user_id']);
        $cot_dolar = $cierre_actual ? $cierre_actual->cot_dolar : 7500;
        $cot_real = $cierre_actual ? $cierre_actual->cot_real : 1500;

        // Manejar monedas y montos
        $moneda = $_REQUEST['moneda'] ?? 'Gs';
        $montoOriginal = floatval($_REQUEST['mon']);

        // El monto del egreso se guarda en la moneda original
        $egreso->monto = $montoOriginal;
        $egreso->moneda = $moneda;

        // Calcular el monto en guaraníes para descontar de la deuda
        $montoGs = $montoOriginal;
        if ($moneda === 'USD') {
            $montoGs = $montoOriginal * $cot_dolar;
            $egreso->cambio = $cot_dolar;
        } elseif ($moneda === 'RS') {
            $montoGs = $montoOriginal * $cot_real;
            $egreso->cambio = $cot_real;
        } else {
            // Para guaraníes
            $egreso->cambio = 1;
        }

        $egreso->sucursal = $_SESSION['sucursal'];

        $acreedor = new acreedor();
        $acreedor->id = $_REQUEST['id'];
        $acreedor->monto = $montoGs; // Descontar en guaraníes de la deuda

        // Debug para verificar los valores
        error_log("Moneda: " . $moneda);
        error_log("Monto original: " . $montoOriginal);
        error_log("Monto en GS: " . $montoGs);
        error_log("Cotización USD: " . $cot_dolar);
        error_log("Cotización RS: " . $cot_real);

        $this->egreso->Registrar($egreso);
        $this->model->Restar($acreedor);

        header('Location: index.php?c=acreedor');
    }

    public function PagosModal()
    {
        if (isset($_REQUEST['id'])) {
            $id_acreedor = $_REQUEST['id'];
            require_once 'view/acreedor/pago_detalles.php';
        }
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=acreedor');
    }
}

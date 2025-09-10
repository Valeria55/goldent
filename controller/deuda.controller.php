<?php
require_once 'model/venta.php';
require_once 'model/venta_tmp.php';
require_once 'model/producto.php';
require_once 'model/ingreso.php';
require_once 'model/deuda.php';
require_once 'model/egreso.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/metodo.php';

class deudaController
{

    private $model;
    private $venta_tmp;
    private $cierre;
    private $producto;
    private $ingreso;
    private $venta;
    private $egreso;
    private $cliente;
    private $metodo;


    public function __CONSTRUCT()
    {
        $this->model = new deuda();
        $this->venta_tmp = new venta_tmp();
        $this->cierre = new cierre();
        $this->producto = new producto();
        $this->ingreso = new ingreso();
        $this->venta = new venta();
        $this->egreso = new egreso();
        $this->cliente = new cliente();
        $this->metodo = new metodo();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/deuda/deuda.php';
        require_once 'view/footer.php';
    }



    public function Listar()
    {
        require_once 'view/deuda/deuda.php';
    }



    public function Crud()
    {
        $deuda = new deuda();

        if (isset($_REQUEST['id'])) {
            $deuda = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/deuda/deuda-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $deuda = new deuda();

        if (isset($_REQUEST['id'])) {
            $deuda = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/deuda/deuda-editar.php';
    }

    public function clientepdf()
    {
        $deuda = new deuda();
        $cli = $this->cliente->Obtener($_REQUEST['id']);
        require_once 'view/informes/extractoclientepdf.php';
    }

    public function CobrarModal()
    {
        $deuda = new deuda();

        if (isset($_REQUEST['id'])) {
            $r = $this->model->Obtener($_REQUEST['id']);
        }
        // Obtener cotizaciones del cierre actual
        if (!isset($_SESSION)) session_start();
        $cierre_actual = $this->cierre->Consultar($_SESSION['user_id']);
        $cotizacion_usd = $cierre_actual->cot_dolar ?? 7500; // Valor por defecto si no hay cierre
        $cotizacion_rs = $cierre_actual->cot_real ?? 1500; //

        require_once 'view/deuda/cobrar-form.php';
    }

    public function Guardar()
    {
        $deuda = new deuda();

        if (!isset($_SESSION)) session_start();

        $deuda->id = $_REQUEST['id'];
        $deuda->id_cliente = $_REQUEST['id_cliente'];
        $deuda->id_venta = $_REQUEST['id_venta'];
        $deuda->fecha = $_REQUEST['fecha'];
        $deuda->vencimiento = $_REQUEST['vencimiento'];
        $deuda->concepto = $_REQUEST['concepto'];
        $deuda->monto = $_REQUEST['monto'];
        $deuda->saldo = $_REQUEST['saldo'];
        $deuda->sucursal = $_SESSION['sucursal'];

        $deuda->id > 0
            ? $this->model->Actualizar($deuda)
            : $this->model->Registrar($deuda);

        header('Location: index.php?c=deuda');
    }

    public function Cobrar()
    {
        if (!isset($_SESSION)) session_start();
        $ingreso = new ingreso();

        $ingreso->id_cliente = $_REQUEST['id_cliente'];

        if ($_REQUEST['forma_pago'] == "Efectivo") {
            if ($_SESSION['nivel'] == 4) { // es gerente, ir a tesoreria
                $ingreso->id_caja = 3;    //tesoreria
            } else {
                $ingreso->id_caja = 1; //caja chica
            }
        } else {
            $ingreso->id_caja = 2; // banco
        }

        $ingreso->id_venta = $_REQUEST['id_venta'];
        $ingreso->id_deuda = $_REQUEST['id'];
        $ingreso->forma_pago = $_REQUEST['forma_pago'];
        $ingreso->fecha = date("Y-m-d H:i");
        $ingreso->categoria = 'Cobro de deuda';
        $ingreso->concepto = "Cobro de deuda a " . $_REQUEST['cli'];
        $ingreso->comprobante = $_REQUEST['comprobante'];

        // Obtener cotizaciones del cierre actual
        $cierre_actual = $this->cierre->Consultar($_SESSION['user_id']);
        $cot_dolar = $cierre_actual ? $cierre_actual->cot_dolar : 7500;
        $cot_real = $cierre_actual ? $cierre_actual->cot_real : 1500;

        // Manejar monedas y montos
        $moneda = $_REQUEST['moneda'] ?? 'Gs';
        $montoOriginal = floatval($_REQUEST['mon']);

        // El monto del ingreso se guarda en la moneda original
        $ingreso->monto = $montoOriginal;
        $ingreso->moneda = $moneda;

        // Calcular el monto en guaraníes para descontar de la deuda
        $montoGs = $montoOriginal;
        if ($moneda === 'USD') {
            $montoGs = $montoOriginal * $cot_dolar;
            $ingreso->cambio = $cot_dolar;
        } elseif ($moneda === 'RS') {
            $montoGs = $montoOriginal * $cot_real;
            $ingreso->cambio = $cot_real;
        } else {
            // Para guaraníes
            $ingreso->cambio = 1;
        }

        $ingreso->sucursal = $_SESSION['sucursal'];

        $deuda = new deuda();
        $deuda->id = $_REQUEST['id'];
        $deuda->monto = $montoGs; // Descontar en guaraníes de la deuda

        // Debug para verificar los valores
        error_log("Moneda: " . $moneda);
        error_log("Monto original: " . $montoOriginal);
        error_log("Monto en GS: " . $montoGs);
        error_log("Cotización USD: " . $cot_dolar);
        error_log("Cotización RS: " . $cot_real);

        $this->ingreso->Registrar($ingreso);
        $this->model->Restar($deuda);

        header('Location:' . getenv('HTTP_REFERER'));
    }
    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=deuda');
    }

    public function NotaCredito()
    {
        $nota = $this->model->listar_cliente_deuda($_REQUEST['id_cliente']);
        echo json_encode($nota);
    }


    public function RangoForm()
    {
        $deuda = new deuda();
        $cli = $this->cliente->Obtener($_REQUEST['id']);
        require_once 'view/deuda/rango-form.php';
    }
}

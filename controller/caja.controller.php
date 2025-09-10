<?php
require_once 'model/caja.php';
require_once 'model/sucursal.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';
require_once 'model/ingreso.php';
require_once 'model/egreso.php';
require_once 'model/usuario.php';


class cajaController
{

    private $model;
    private $caja;
    private $sucursal;
    private $cierre;
    private $cliente;
    private $ingreso;
    private $egreso;
    private $usuario;

    public function __CONSTRUCT()
    {
        $this->model = new caja();
        $this->caja = new caja();
        $this->sucursal = new sucursal();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
        $this->ingreso = new ingreso();
        $this->egreso = new egreso();
        $this->usuario = new usuario();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/caja/caja.php';
        require_once 'view/footer.php';
    }

    public function Movimientos()
    {
        require_once 'view/header.php';
        require_once 'view/caja/movimientos.php';
        require_once 'view/footer.php';
    }

    public function guiaPDF()
    {
        require_once 'view/caja/guia_cajas.php';
    }

    public function MovimientoCaja()
    {
        require_once 'view/header.php';
        require_once 'view/caja/movimientocaja.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/caja/caja.php';
    }

    public function InversionModal()
    {
        require_once 'view/caja/inversion-modal.php';
    }


    public function Crud()
    {
        $caja = new caja();

        if (isset($_REQUEST['id'])) {
            $caja = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/caja/caja-editar.php';
        require_once 'view/footer.php';
    }

    public function transferenciaModal()
    {
        $caja = new caja();

        require_once 'view/caja/transferencia-modal.php';
    }

    public function Obtener()
    {
        $caja = new caja();

        if (isset($_REQUEST['id'])) {
            $caja = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/caja/caja-editar.php';
    }

    public function Guardar()
    {
        $caja = new caja();
        if (!isset($_SESSION)) session_start();
        $caja->id = $_REQUEST['id'];
        $caja->id_usuario = ($_REQUEST['id_usuario']) ? $_REQUEST['id_usuario'] : $_SESSION["user_id"];
        $caja->fecha = ($_REQUEST['fecha']) ? $_REQUEST['fecha'] : date("Y-m-d");
        $caja->caja = $_REQUEST['caja'];
        $caja->monto = $_REQUEST['monto'] * $_REQUEST['movimiento'];
        $caja->comprobante = $_REQUEST['comprobante'];
        $caja->anulado = 0;

        $caja->id > 0
            ? $this->model->Actualizar($caja)
            : $this->model->Registrar($caja);

        $caja->id > 0
            ? $accion = "Modificado"
            : $accion = "Agregado";;

        header('Location: index.php?success=' . $accion . '&c=' . $_REQUEST['c']);
    }

    public function Transferencia()
    {
        $egreso = new egreso();

        $receptor = $this->model->Obtener($_REQUEST['id_receptor']);

        //el usuario cuyo id va al egreso siempre es el de la sesion, el que realizo la transferencia

        $egreso->id_cliente = 0;
        if (!isset($_SESSION)) session_start();
        $cierre = $this->cierre->Consultar($_SESSION['user_id']);
        $egreso->id_caja = $_REQUEST['id_emisor'];
        $egreso->fecha = date("Y-m-d H:i");
        // Manejo de moneda y tipo de cambio
        $moneda_transferencia = $_REQUEST['moneda_transferencia'] ?? 'GS';
        $egreso->monto = $_REQUEST['monto'];
        $egreso->moneda = $moneda_transferencia;

        // Usar las cotizaciones especificadas en la transferencia
        $cot_dolar_transferencia = $_REQUEST['cot_dolar'] ?? ($cierre->cot_dolar ?? 7500);
        $cot_real_transferencia = $_REQUEST['cot_real'] ?? ($cierre->cot_real ?? 1500);

        // Validar que las cotizaciones sean válidas
        if ($cot_dolar_transferencia <= 0) {
            die('<script>alert("La cotización del dólar debe ser mayor a 0."); window.history.back();</script>');
        }
        if ($cot_real_transferencia <= 0) {
            die('<script>alert("La cotización del real debe ser mayor a 0."); window.history.back();</script>');
        }

        // Establecer el tipo de cambio según la moneda usando las cotizaciones de transferencia
        if ($moneda_transferencia == 'USD') {
            $egreso->cambio = $cot_dolar_transferencia;
        } elseif ($moneda_transferencia == 'RS') {
            $egreso->cambio = $cot_real_transferencia;
        } else {
            $egreso->cambio = 1; // Guaraníes
        }

        $egreso->categoria = "Transferencia";
        $concepto_cotizacion = "";
        if ($moneda_transferencia == 'USD') {
            $concepto_cotizacion = " - Cotización USD: " . number_format($cot_dolar_transferencia, 0);
        } elseif ($moneda_transferencia == 'RS') {
            $concepto_cotizacion = " - Cotización RS: " . number_format($cot_real_transferencia, 0);
        }
        $egreso->concepto = "Transferencia enviada a " . $receptor->caja . " (" . $egreso->monto . " " . $moneda_transferencia . ")" . $concepto_cotizacion;

        if ($_REQUEST['id_receptor'] == 2) {
            $egreso->comprobante = $_REQUEST['comprobante'];
        } else {
            $egreso->comprobante = "";
        }

        if (!$egreso->monto > 0) die('<script>alert("Monto ingresado no válido."); window.history.back();</script>');

        // Validar que hay suficiente saldo en la moneda seleccionada
        $caja_origen = $this->model->ObtenerBalance($_REQUEST['id_emisor']);
        $saldo_disponible = 0;

        if ($moneda_transferencia == 'GS') {
            $saldo_disponible = ($caja_origen->ingresos_gs ?? 0) - ($caja_origen->egresos_gs ?? 0);
        } elseif ($moneda_transferencia == 'USD') {
            $saldo_disponible = ($caja_origen->ingresos_usd ?? 0) - ($caja_origen->egresos_usd ?? 0);
        } elseif ($moneda_transferencia == 'RS') {
            $saldo_disponible = ($caja_origen->ingresos_rs ?? 0) - ($caja_origen->egresos_rs ?? 0);
        }

        if ($egreso->monto > $saldo_disponible) {
            die('<script>alert("Saldo insuficiente en ' . $moneda_transferencia . '. Disponible: ' . number_format($saldo_disponible, 2) . '"); window.history.back();</script>');
        }

        if ($egreso->id_caja == 2) { // si es caja -Banco-
            $egreso->forma_pago = "Transferencia";
        } else {
            $egreso->forma_pago = "Efectivo";
        }
        $egreso->sucursal = 1;
        // NO sobrescribir moneda y cambio que ya se establecieron correctamente arriba

        $emisor = $this->model->Obtener($_REQUEST['id_emisor']);

        $ingreso = new ingreso();

        $ingreso->id_cliente = 0;
        $ingreso->id_caja = $_REQUEST['id_receptor'];
        $ingreso->id_usuario_transferencia = $_SESSION['user_id']; // usuario que envio esta transferencia
        $id_c = $_REQUEST['id_cajero'] ?? 0;

        //usuario que recibe la transferencia
        if ($ingreso->id_caja == 1) { //caja chica, se tiene que especificar cajero

            $ingreso->id_usuario = $id_c > 0 ? $id_c : die('<script>alert("Error al registrar cajero receptor."); window.history.back();</script>'); // usuario a quien le llega la transferencia

        } elseif ($ingreso->id_caja == 2) { // banco, llega directo a nombre de edison

            $ingreso->id_usuario = 15; //edison

        } else { // tesoreria, llega directo a nombre de yanina

            $ingreso->id_usuario = 14; // yanina

        }
        $ingreso->id_venta = 0;
        $ingreso->fecha = date("Y-m-d H:i");
        $ingreso->categoria = "Transferencia";
        $concepto_cotizacion_ingreso = "";
        if ($moneda_transferencia == 'USD') {
            $concepto_cotizacion_ingreso = " - Cotización USD: " . number_format($cot_dolar_transferencia, 0);
        } elseif ($moneda_transferencia == 'RS') {
            $concepto_cotizacion_ingreso = " - Cotización RS: " . number_format($cot_real_transferencia, 0);
        }
        $ingreso->concepto = "Transferencia recibida de " . $emisor->caja . " (" . $egreso->monto . " " . $moneda_transferencia . ")" . $concepto_cotizacion_ingreso;
        if ($ingreso->id_caja == 2) {
            $ingreso->comprobante = $_REQUEST['comprobante'];
        } else {
            $ingreso->comprobante = "";
        }

        // El ingreso tiene la misma moneda y tipo de cambio que el egreso (usando cotizaciones de transferencia)
        $ingreso->monto = $_REQUEST['monto'];
        $ingreso->moneda = $moneda_transferencia;
        
        // Usar las mismas cotizaciones de transferencia para el ingreso
        if ($moneda_transferencia == 'USD') {
            $ingreso->cambio = $cot_dolar_transferencia;
        } elseif ($moneda_transferencia == 'RS') {
            $ingreso->cambio = $cot_real_transferencia;
        } else {
            $ingreso->cambio = 1; // Guaraníes
        }

        $ingreso->forma_pago = ($ingreso->id_caja == 2) ? "Transferencia" : "Efectivo"; //si es caja Banco
        $ingreso->sucursal = 1;
        if (($ingreso->id_caja == 1 && !($id_c > 0))) {
            die('<script>alert("Error con el usuario seleccionado, intente nuevamente."); window.history.back();</script>');
        }
        if (
            !($egreso->id_caja > 0)
            || !($ingreso->id_caja > 0)
            || !($egreso->monto > 0)

        ) { //tratamiento de error antes de insertar cualquier registro en la BD
            die('<script>alert("Ocurrió  un error, intente nuevamente"); window.history.back();</script>');
        }

        //obtener el id_transferencia para ambos registros
        $ultimoIdTransferencia = $this->egreso->ObtenerUltimoIdTransferencia();
        $nuevoIdTransferencia = $ultimoIdTransferencia + 1;
        $egreso->id_transferencia = $nuevoIdTransferencia;
        $ingreso->id_transferencia = $nuevoIdTransferencia;

        $this->egreso->Registrar($egreso);
        $this->ingreso->Registrar($ingreso);

        $accion = "Guardado";
        if ($_SESSION['nivel'] == 1) {
            header('Location: index.php?success=' . $accion . '&c=' . $_REQUEST['c']);
        } else {
            header('Location: index.php?success=' . $accion . '&c=egreso&a=extraccion');
        }
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c=' . $_REQUEST['c']);
    }

    public function Anular()
    {
        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?success=Anulado&c=' . $_REQUEST['c']);
    }
}

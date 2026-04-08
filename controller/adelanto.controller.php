<?php
require_once 'model/adelanto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/ingreso.php';
require_once 'model/metodo.php';

class adelantoController
{
    private $model;
    private $cliente;
    private $cierre;
    private $ingreso;
    private $metodo;

    public function __CONSTRUCT()
    {
        $this->model = new adelanto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->ingreso = new ingreso();
        $this->metodo = new metodo();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/adelanto/adelanto.php';
        require_once 'view/footer.php';
    }

    public function Crud()
    {
        $adelanto = new adelanto();

        if (isset($_REQUEST['id'])) {
            $adelanto = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/adelanto/adelanto-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $adelanto = new adelanto();

        if (isset($_REQUEST['id'])) {
            $adelanto = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/adelanto/adelanto-editar.php';
    }

    public function Guardar()
    {
        $adelanto = new adelanto();

        $adelanto->id = $_REQUEST['id'];
        $adelanto->id_cliente = $_REQUEST['id_cliente'];
        $adelanto->monto = $_REQUEST['monto'];
        $adelanto->descripcion = $_REQUEST['descripcion'];
        $adelanto->forma_pago = $_REQUEST['forma_pago'];
        $adelanto->comprobante = $_REQUEST['comprobante'] ?? null;
        $adelanto->fecha = date('Y-m-d H:i:s', strtotime($_REQUEST['fecha']));
        
        if (!isset($_SESSION)) session_start();
        $adelanto->id_usuario = $_SESSION['user_id'];

        if ($adelanto->id > 0) {
            $this->model->Actualizar($adelanto);
            // Actualizar el ingreso vinculado al adelanto
            $this->ingreso->ActualizarPorAdelanto($adelanto);
        } else {
            $id_adelanto = $this->model->Registrar($adelanto);

            // Registrar ingreso automático por el adelanto
            $ing = new ingreso();
            $ing->id_cliente    = $adelanto->id_cliente;
            $ing->id_usuario    = $adelanto->id_usuario;
            $ing->id_caja       = ($adelanto->forma_pago === 'Efectivo') ? 1 : 2;
            $ing->id_venta      = 0;
            $ing->id_deuda      = null;
            $ing->fecha         = $adelanto->fecha;
            $ing->categoria     = 'ADELANTO';
            $ing->concepto      = 'Adelanto #' . $id_adelanto . ($adelanto->descripcion ? ' - ' . $adelanto->descripcion : '');
            $ing->comprobante   = $adelanto->comprobante;
            $ing->monto         = $adelanto->monto;
            $ing->moneda        = 'GS';
            $ing->cambio        = 1;
            $ing->forma_pago    = $adelanto->forma_pago;
            $ing->sucursal      = isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : null;
            $ing->id_gift       = null;
            $ing->id_usuario_transferencia = null;
            $ing->id_compra     = null;
            $ing->id_transferencia = null;
            $ing->id_adelanto   = $id_adelanto;
            $this->ingreso->RegistrarAdelanto($ing);
        }

        $accion = $adelanto->id > 0 ? "Modificado" : "Agregado";

        header('Location: index.php?c=adelanto&success=' . $accion);
    }

    public function Anular()
    {
        if (!isset($_SESSION)) session_start();
        $this->model->Anular($_REQUEST['id'], $_SESSION['user_id']);
        // Anular el ingreso vinculado
        $this->ingreso->AnularPorAdelanto($_REQUEST['id']);
        header('Location: index.php?c=adelanto&success=Anulado');
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        // Eliminar (anular) el ingreso vinculado
        $this->ingreso->EliminarPorAdelanto($_REQUEST['id']);
        header('Location: index.php?c=adelanto&success=Eliminado');
    }
    
    public function CambiarEstado()
    {
        $this->model->CambiarEstado($_REQUEST['id'], $_REQUEST['estado']);
        header('Location: index.php?c=adelanto&success=Estado Actualizado');
    }

    public function ListarPendientes()
    {
        $id_cliente = $_REQUEST['id_cliente'];
        $adelantos = $this->model->ListarPendientesPorCliente($id_cliente);
        echo json_encode($adelantos);
    }
}

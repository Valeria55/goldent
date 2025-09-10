<?php

require_once 'model/pago_tmp.php';
require_once 'model/venta_tmp.php';
require_once 'model/metodo.php';
require_once 'model/gift_card.php';
require_once 'model/presupuesto.php';
require_once 'model/devolucion_compras.php';
require_once 'model/compra_tmp.php';
require_once 'model/deuda.php';
require_once 'model/cierre.php';

class pago_tmpController
{
    private $model;
    private $pago_tmp;
    private $venta_tmp;
    private $metodo;
    private $gift_card;
    private $presupuesto;
    private $devolucion_compras;
    private $compra_tmp;
    private $deuda;
    private $cierre;

    public function __CONSTRUCT()
    {
        $this->model = new pago_tmp();
        $this->pago_tmp = new pago_tmp();
        $this->venta_tmp = new venta_tmp();
        $this->metodo = new metodo();
        $this->gift_card = new gift_card();
        $this->presupuesto = new presupuesto();
        $this->devolucion_compras = new devolucion_compras();
        $this->compra_tmp = new compra_tmp();
        $this->deuda = new deuda();
        $this->cierre = new cierre();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/pago_tmp/pago_tmp.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/pago_tmp/pago_tmp.php';
    }

    public function Crud()
    {
        $pago_tmp = new pago_tmp();

        if (isset($_REQUEST['id'])) {
            $pago_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/pago_tmp/pago_tmp-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $pago_tmp = new pago_tmp();

        if (isset($_REQUEST['id'])) {
            $pago_tmp = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/pago_tmp/pago_tmp-editar.php';
    }

    public function Guardar()
    {
        $pago_tmp = new pago_tmp();
        if (!isset($_SESSION)) session_start();

        // Obtener cotizaciones del cierre actual
        $cierre_actual = $this->cierre->Consultar($_SESSION['user_id']);
        $moneda = isset($_REQUEST['moneda']) ? $_REQUEST['moneda'] : 'GS';
        
        // Usar la cotización especificada por el usuario o la del cierre
        $cambio = 1; // Default para GS
        if (isset($_REQUEST['cambio']) && $_REQUEST['cambio'] != '') {
            // Usar la cotización especificada por el usuario
            $cambio = floatval($_REQUEST['cambio']);
        } else {
            // Usar la cotización del cierre actual
            if ($moneda == 'USD') {
                $cambio = $cierre_actual ? $cierre_actual->cot_dolar : 1;
            } elseif ($moneda == 'RS') {
                $cambio = $cierre_actual ? $cierre_actual->cot_real : 1;
            }
        }

        $pago_tmp->id_usuario = $_SESSION['user_id'];
        $pago_tmp->pago = $_REQUEST['pago'];
        $pago_tmp->monto = $_REQUEST['monto'];
        $pago_tmp->moneda = $moneda;
        $pago_tmp->cambio = $cambio;
        $pago_tmp->id_deuda = isset($_REQUEST['id_deuda']) ? $_REQUEST['id_deuda'] : 0;

        $this->model->Registrar($pago_tmp);

        if (isset($_REQUEST['bandera']) && $_REQUEST['bandera'] == 1) {
            require_once 'view/pago_tmp/pago_compra_tmp.php';
        } else {
            require_once 'view/pago_tmp/pago_tmp.php';
        }
    }


    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);

        if (isset($_REQUEST['bandera']) && $_REQUEST['bandera'] == 1) {
            require_once 'view/pago_tmp/pago_compra_tmp.php';
        } else {
            require_once 'view/pago_tmp/pago_tmp.php';
        }
    }

    public function VaciarPago()
    {
        $this->model->Vaciar();
    }

    public function SaldoReal()
    {

        $pago_tmp = $this->model->ObtenerTodo();

        $deuda = $_REQUEST['id_deuda'] != 0 ? $this->deuda->Obtener($_REQUEST['id_deuda']) : null;
        $saldo = $deuda && is_numeric($deuda->saldo) ? $deuda->saldo : 0;

        if (empty($pago_tmp)) {
            $saldo = $deuda->saldo;
        } else {
            foreach ($pago_tmp as $key => $value) {
                if ($value->id_deuda != 0) {
                    if ($deuda !== null && $value->id_deuda == $deuda->id) {
                        $saldo -= $value->monto;
                    }
                }
            }
        }
        echo json_encode($saldo);
    }

    public function BuscarNota()
    {

        $pago_tmp = $this->model->ObtenerTodo();
        $deuda = $this->deuda->Obtener($_REQUEST['id_deuda']);

        if (empty($pago_tmp)) {
            $bandera = 0;
        } else {
            foreach ($pago_tmp as $key => $value) {
                if ((intval($_REQUEST['id_deuda']) == $value->id_deuda) && ($_REQUEST['metodo'] == $value->pago)) {
                    $bandera = 1;
                    break;
                } else {
                    $bandera = 0;
                }
            }
        }
        echo json_encode($bandera);
    }

    public function ObtenerPago()
    {
        if (!isset($_SESSION)) session_start();
        
        // Determinar si es compra o venta basado en el parámetro bandera
        $bandera = isset($_REQUEST['bandera']) ? $_REQUEST['bandera'] : 0;

        if ($bandera == 1) {
            // Para compras
            $monto_total = $this->compra_tmp->ObtenerSinID();
            $pagos = $this->model->ListarPagos();
        } else {
            // Para ventas
            $monto_total = $this->venta_tmp->Obtener();
            $pagos = $this->model->Listar();
        }

        $pagototal_gs = 0;

        // Calcular el total pagado convirtiendo todo a GS usando la cotización de cada pago
        foreach ($pagos as $pago) {
            $pago_gs = 0;

            // Verificar si existe la propiedad moneda, si no, asumir GS
            $moneda = isset($pago->moneda) ? $pago->moneda : 'GS';
            $cambio = isset($pago->cambio) ? $pago->cambio : 1;

            if ($moneda == 'GS') {
                $pago_gs = $pago->monto;
            } else {
                // Usar la cotización específica del pago
                $pago_gs = $pago->monto * $cambio;
            }

            $pagototal_gs += $pago_gs;
        }

        // El monto total siempre está en GS
        $monto_total_gs = intval($monto_total->monto);

        // Calcular el saldo restante
        $saldo = $monto_total_gs - $pagototal_gs;

        // -----------------  ESTABLECER TOLERANCIA -----------------
        // Considerar pagado si el saldo es menor o igual a 100 GS (tolerancia para redondeos)
        $pagado = ($saldo <= 100) ? 1 : 0;

        // Para debug - comentar en producción
        // error_log("Debug ObtenerPago - Bandera: $bandera, Monto total: $monto_total_gs, Pago total GS: $pagototal_gs, Saldo: $saldo, Pagado: $pagado");

        echo json_encode($pagado);
    }

    public function cargar_pagos_compra()
    {
        $id_compra = isset($_REQUEST['id_compra']) ? $_REQUEST['id_compra'] : 0;
        
        // Si es una edición (id_compra > 0), cargar los egresos existentes
        if ($id_compra > 0) {
            $this->model->CargarEgresosDeCompra($id_compra);
        } else {
            // Si es una compra nueva, limpiar pagos temporales previos
            $this->model->Vaciar();
        }
        
        // Cargar la vista de pagos para compra
        require_once 'view/pago_tmp/pago_compra_tmp.php';
    }
}

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

    public function cargarPagados()
    {
        // Este método carga la tabla de pagados mediante AJAX
        require_once 'view/deuda/tabla-pagados.php';
    }

    public function cargarClientesConDeudas()
    {
        // Obtener clientes con deudas agrupados
        $clientes = $this->model->listarClientesConDeudas();
        
        if (empty($clientes)) {
            echo '<div class="alert alert-info">No hay clientes con deudas pendientes.</div>';
            return;
        }
        
        foreach ($clientes as $cliente) {
            echo '<div class="cliente-item list-group-item" data-id="' . $cliente->id_cliente . '" data-nombre="' . htmlspecialchars($cliente->nombre) . '" style="cursor: pointer; margin-bottom: 5px;">';
            echo '<div class="row">';
            echo '<div class="col-md-8">';
            echo '<strong>' . htmlspecialchars($cliente->nombre) . '</strong>';
            echo '<br><small class="text-muted">RUC: ' . ($cliente->ruc ?: 'No especificado') . '</small>';
            echo '</div>';
            echo '<div class="col-md-4 text-right">';
            echo '<span class="badge badge-danger">' . number_format($cliente->total_deuda, 0, ',', '.') . '</span>';
            echo '<br><small class="text-muted">' . $cliente->cantidad_deudas . ' deuda(s)</small>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }

    public function cargarDeudasCliente()
    {
        $id_cliente = $_GET['id_cliente'];
        $deudas = $this->model->listarDeudasPorCliente($id_cliente);
        
        $total = 0;
        $html = '<table class="table table-bordered table-striped">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f8f9fa;">';
        $html .= '<th>Concepto</th>';
        $html .= '<th>Comprobante</th>';
        $html .= '<th>Fecha</th>';
        $html .= '<th>Vencimiento</th>';
        $html .= '<th>Monto</th>';
        $html .= '<th>Saldo</th>';
        $html .= '<th>Acciones</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($deudas as $deuda) {
            $total += $deuda->saldo;
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($deuda->concepto) . '</td>';
            $html .= '<td>' . ($deuda->nro_comprobante ? htmlspecialchars($deuda->nro_comprobante) : '-') . '</td>';
            $html .= '<td>' . date("d/m/Y", strtotime($deuda->fecha)) . '</td>';
            $html .= '<td>' . (date("Y", strtotime($deuda->vencimiento)) > 2000 ? date("d/m/Y", strtotime($deuda->vencimiento)) : '') . '</td>';
            $html .= '<td class="text-right">' . number_format($deuda->monto, 0, ',', '.') . '</td>';
            $html .= '<td class="text-right">' . number_format($deuda->saldo, 0, ',', '.') . '</td>';
            $html .= '<td>';
            $html .= '<button class="btn btn-sm btn-success cobro-especifico-btn" ';
            $html .= 'data-id="' . $deuda->id . '" ';
            $html .= 'data-concepto="' . htmlspecialchars($deuda->concepto) . '" ';
            $html .= 'data-saldo="' . number_format($deuda->saldo, 0, ',', '.') . '">';
            $html .= 'Cobrar</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '<tfoot>';
        $html .= '<tr style="background-color: #f8f9fa; font-weight: bold;">';
        $html .= '<td colspan="4" class="text-right">TOTAL:</td>';
        $html .= '<td class="text-right">' . number_format($total, 0, ',', '.') . '</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        $html .= '</tfoot>';
        $html .= '</table>';
        
        $response = [
            'total' => $total,
            'total_formateado' => number_format($total, 0, ',', '.'),
            'tabla_html' => $html
        ];
        
        echo json_encode($response);
    }

    public function pagoTotal()
    {
        try {
            $id_cliente = $_POST['id_cliente'];
            $cantidad = floatval($_POST['cantidad']);
            
            if ($cantidad <= 0) {
                echo json_encode(['success' => false, 'message' => 'La cantidad debe ser mayor a 0']);
                return;
            }
            
            // Obtener deudas del cliente ordenadas por fecha (más vieja primero)
            $deudas = $this->model->listarDeudasPorClienteOrdenadas($id_cliente);
            
            if (empty($deudas)) {
                echo json_encode(['success' => false, 'message' => 'No se encontraron deudas para este cliente']);
                return;
            }
            
            $cantidad_restante = $cantidad;
            $deudas_procesadas = [];
            
            foreach ($deudas as $deuda) {
                if ($cantidad_restante <= 0) break;
                
                if ($deuda->saldo > 0) {
                    $pago_deuda = min($cantidad_restante, $deuda->saldo);
                    
                    // Registrar el pago
                    $this->model->registrarPago($deuda->id, $pago_deuda, 'Efectivo', 'Pago automático desde el más viejo');
                    
                    $deudas_procesadas[] = [
                        'id' => $deuda->id,
                        'concepto' => $deuda->concepto,
                        'pago' => $pago_deuda
                    ];
                    
                    $cantidad_restante -= $pago_deuda;
                }
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Pago procesado correctamente',
                'deudas_procesadas' => $deudas_procesadas,
                'cantidad_restante' => $cantidad_restante
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al procesar el pago: ' . $e->getMessage()]);
        }
    }

    public function cobroEspecifico()
    {
        try {
            $id_deuda = $_POST['id_deuda'];
            $cantidad = floatval($_POST['cantidad']);
            $metodo = $_POST['metodo'];
            $observaciones = $_POST['observaciones'];
            
            if ($cantidad <= 0) {
                echo json_encode(['success' => false, 'message' => 'La cantidad debe ser mayor a 0']);
                return;
            }
            
            // Obtener la deuda para validar el saldo
            $deuda = $this->model->Obtener($id_deuda);
            
            if (!$deuda || $cantidad > $deuda->saldo) {
                echo json_encode(['success' => false, 'message' => 'La cantidad excede el saldo de la deuda']);
                return;
            }
            
            // Registrar el pago
            $this->model->registrarPago($id_deuda, $cantidad, $metodo, $observaciones);
            
            echo json_encode(['success' => true, 'message' => 'Pago registrado correctamente']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }

    public function cobroEspecificoMultiple()
    {
        try {
            $id_deuda = $_POST['id_deuda'];
            $metodos_pago = json_decode($_POST['metodos_pago'], true);
            $total_pagar = floatval($_POST['total_pagar']);
            
            if (empty($metodos_pago)) {
                echo json_encode(['success' => false, 'message' => 'Debe proporcionar al menos un método de pago']);
                return;
            }
            
            if ($total_pagar <= 0) {
                echo json_encode(['success' => false, 'message' => 'El total a pagar debe ser mayor a 0']);
                return;
            }
            
            // Obtener la deuda para validar el saldo
            $deuda = $this->model->Obtener($id_deuda);
            
            if (!$deuda) {
                echo json_encode(['success' => false, 'message' => 'Deuda no encontrada']);
                return;
            }
            
            if ($total_pagar > $deuda->saldo) {
                echo json_encode(['success' => false, 'message' => 'El total a pagar excede el saldo de la deuda']);
                return;
            }
            
            // Obtener tipos de cambio del último cierre del usuario
            $tipos_cambio = $this->model->obtenerTiposCambioActuales($_SESSION['user_id']);
            
            // Registrar el pago múltiple
            $resultado = $this->model->registrarPagoMultiple($id_deuda, $metodos_pago, $tipos_cambio);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Pago registrado correctamente',
                'grupo_pago_id' => $resultado['grupo_pago_id']
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }

    public function pagoTotalMultiple()
    {
        try {
            $id_cliente = $_POST['id_cliente'];
            $metodos_pago = json_decode($_POST['metodos_pago'], true);
            $total_pagar = floatval($_POST['total_pagar']);
            
            if (empty($metodos_pago)) {
                echo json_encode(['success' => false, 'message' => 'Debe proporcionar al menos un método de pago']);
                return;
            }
            
            if ($total_pagar <= 0) {
                echo json_encode(['success' => false, 'message' => 'El total a pagar debe ser mayor a 0']);
                return;
            }
            
            // Obtener deudas del cliente ordenadas por fecha (más vieja primero)
            $deudas = $this->model->listarDeudasPorClienteOrdenadas($id_cliente);
            
            if (empty($deudas)) {
                echo json_encode(['success' => false, 'message' => 'No se encontraron deudas para este cliente']);
                return;
            }
            
            // Obtener tipos de cambio del último cierre del usuario
            $tipos_cambio = $this->model->obtenerTiposCambioActuales($_SESSION['user_id']);
            
            // Generar ID único para este grupo de pagos totales
            $grupo_pago_id = 'PT_' . date('YmdHis') . '_' . uniqid();
            
            // Agregar el grupo_pago_id a cada método de pago
            foreach ($metodos_pago as &$metodo) {
                $metodo['grupo_pago_id'] = $grupo_pago_id;
            }
            
            $cantidad_restante = $total_pagar;
            $deudas_procesadas = [];
            
            foreach ($deudas as $deuda) {
                if ($cantidad_restante <= 0) break;
                
                if ($deuda->saldo > 0) {
                    $pago_deuda = min($cantidad_restante, $deuda->saldo);
                    
                    // Registrar el pago múltiple para esta deuda específica
                    $this->model->registrarPagoMultipleDeuda($deuda->id, $metodos_pago, $tipos_cambio, $pago_deuda, $total_pagar);
                    
                    $deudas_procesadas[] = [
                        'id' => $deuda->id,
                        'concepto' => $deuda->concepto,
                        'pago' => $pago_deuda
                    ];
                    
                    $cantidad_restante -= $pago_deuda;
                }
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Pago procesado correctamente',
                'grupo_pago_id' => $grupo_pago_id,
                'deudas_procesadas' => $deudas_procesadas,
                'cantidad_restante' => $cantidad_restante
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al procesar el pago: ' . $e->getMessage()]);
        }
    }

    public function revertirPagoMultiple()
    {
        try {
            $grupo_pago_id = $_POST['grupo_pago_id'];
            
            if (empty($grupo_pago_id)) {
                echo json_encode(['success' => false, 'message' => 'ID de grupo de pago requerido']);
                return;
            }
            
            $resultado = $this->model->revertirPagoMultiple($grupo_pago_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Pago revertido correctamente',
                'detalles' => $resultado
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al revertir el pago: ' . $e->getMessage()]);
        }
    }

    public function listarPagosMultiples()
    {
        try {
            $id_cliente = $_GET['id_cliente'] ?? null;
            
            if ($id_cliente) {
                // Si se especifica cliente, obtener TODAS las deudas del cliente (incluidas saldadas) para buscar pagos múltiples
                $deudas_cliente = $this->model->listarTodasDeudasPorCliente($id_cliente);
                $ids_deudas = array_map(function($deuda) { return $deuda->id; }, $deudas_cliente);
                
                if (empty($ids_deudas)) {
                    echo json_encode(['success' => true, 'pagos' => [], 'debug' => 'No se encontraron deudas para el cliente']);
                    return;
                }
                
                // Obtener pagos múltiples que afecten a estas deudas
                $pagos = $this->model->obtenerPagosMultiplesPorCliente($ids_deudas);
            } else {
                $pagos = $this->model->obtenerPagosMultiples();
            }
            
            echo json_encode([
                'success' => true,
                'pagos' => $pagos,
                'debug' => [
                    'id_cliente' => $id_cliente,
                    'ids_deudas' => $ids_deudas ?? null,
                    'cantidad_pagos' => count($pagos)
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener pagos: ' . $e->getMessage()]);
        }
    }

    public function obtenerMetodosPago()
    {
        try {
            $metodos = $this->metodo->Listar();
            $metodos_array = [];
            
            foreach ($metodos as $metodo) {
                $metodos_array[] = [
                    'id' => $metodo->id,
                    'metodo' => $metodo->metodo
                ];
            }
            
            echo json_encode(['success' => true, 'metodos' => $metodos_array]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener métodos: ' . $e->getMessage()]);
        }
    }

    public function obtenerTiposCambio()
    {
        try {
            $tipos_cambio = $this->model->obtenerTiposCambioActuales($_SESSION['user_id']);
            echo json_encode(['success' => true, 'tipos_cambio' => $tipos_cambio]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener tipos de cambio: ' . $e->getMessage()]);
        }
    }

    public function obtenerDetalleRecibo()
    {
        try {
            $grupo_pago_id = $_POST['grupo_pago_id'];
            
            if (empty($grupo_pago_id)) {
                echo json_encode(['success' => false, 'message' => 'ID de grupo de pago requerido']);
                return;
            }

            $detalle = $this->model->obtenerDetalleRecibo($grupo_pago_id);
            echo json_encode(['success' => true, 'detalle' => $detalle]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener detalle del recibo: ' . $e->getMessage()]);
        }
    }

    public function obtenerRecibosCliente()
    {
        try {
            error_log("DEBUG: obtenerRecibosCliente llamado");
            $id_cliente = $_POST['id_cliente'];
            error_log("DEBUG: id_cliente recibido: " . $id_cliente);
            
            if (empty($id_cliente)) {
                echo json_encode(['success' => false, 'message' => 'ID de cliente requerido']);
                return;
            }

            $recibos = $this->model->obtenerRecibosCliente($id_cliente);
            error_log("DEBUG: recibos obtenidos: " . count($recibos));
            echo json_encode(['success' => true, 'recibos' => $recibos]);
            
        } catch (Exception $e) {
            error_log("DEBUG: Error en obtenerRecibosCliente: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al obtener recibos del cliente: ' . $e->getMessage()]);
        }
    }

    public function obtenerRecibosAnulados()
    {
        try {
            $id_cliente = $_POST['id_cliente'];
            
            if (empty($id_cliente)) {
                echo json_encode(['success' => false, 'message' => 'ID de cliente requerido']);
                return;
            }

            $recibos = $this->model->obtenerRecibosAnulados($id_cliente);
            echo json_encode(['success' => true, 'recibos' => $recibos]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener recibos anulados del cliente: ' . $e->getMessage()]);
        }
    }

    public function generarReciboPDF()
    {
        try {
            $grupo_pago_id = $_GET['grupo_pago_id'] ?? $_POST['grupo_pago_id'] ?? '';
            $download = $_GET['download'] ?? '';
            $anulado = $_GET['anulado'] ?? '';
            
            if (empty($grupo_pago_id)) {
                die('ID de grupo de pago requerido');
            }

            // Construir URL con parámetros
            $url = 'view/deuda/recibo_tcpdf.php?grupo_pago_id=' . urlencode($grupo_pago_id);
            if ($download) {
                $url .= '&download=1';
            }
            if ($anulado) {
                $url .= '&anulado=1';
            }
            
            // Redirigir al archivo TCPDF
            header('Location: ' . $url);
            exit;
            
        } catch (Exception $e) {
            die('Error al generar recibo PDF: ' . $e->getMessage());
        }
    }
}

<?php
require_once 'model/devolucion_compras.php';
require_once 'model/compra.php';
require_once 'model/usuario.php';
require_once 'model/compra.php';
require_once 'model/devolucion_tmpcompras.php';
require_once 'model/producto.php';
require_once 'model/ingreso.php';
require_once 'model/egreso.php';
require_once 'model/deuda.php';
require_once 'model/acreedor.php';
require_once 'model/egreso.php';
require_once 'model/cierre.php';
require_once 'model/caja.php';
require_once 'model/cliente.php';
require_once 'model/metodo.php';
require_once 'model/pago_tmp.php';
class devolucion_comprasController{ 
    
    private $model;
    private $compra;
    private $usuario;
    private $devolucion_tmpcompras;
    private $producto;
    private $ingreso;
    private $egreso;
    private $deuda;
    private $acreedor;
    private $cierre;
    private $caja;
    private $cliente;
    private $metodo;
    private $pago_tmp;
    
    public function __CONSTRUCT(){
        $this->model = new devolucion_compras();
        $this->compra = new compra();
        $this->usuario = new usuario();
        $this->devolucion_tmpcompras = new devolucion_tmpcompras();
        $this->producto = new producto();
        $this->ingreso = new ingreso();
        $this->deuda = new deuda();
        $this->acreedor = new acreedor();
        $this->egreso = new egreso();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->cliente = new cliente();
        $this->metodo = new metodo();
        $this->pago_tmp = new pago_tmp();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/devolucion_compras/devolucion_compras.php';
        require_once 'view/footer.php';
       
    }

    public function Sesion(){
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {          
            require_once 'view/compra/compra-sesion.php';
        }else{
            echo "<h1>Debe hacer apertura de caja</h1>";
        }
        require_once 'view/footer.php';
    }

    public function Nuevacompra(){
        require_once 'view/header.php';
        require_once 'view/compra/nueva-compra.php';
        require_once 'view/footer.php';
       
    }

    public function Listar(){
        require_once 'view/compra/compra.php';
    }
    public function DevolucionTicket(){
        require_once 'view/informes/ticket-devolucion.php';
    }
    
    public function ListarCliente(){
        require_once 'view/header.php';
        require_once 'view/compra/compracliente.php';
        require_once 'view/footer.php';
    }
    
    public function ListarUsuario(){
        require_once 'view/header.php';
        require_once 'view/compra/comprausuario.php';
        require_once 'view/footer.php';
    }
    
    public function ListarProducto(){
        require_once 'view/header.php';
        require_once 'view/compra/compraproducto.php';
        require_once 'view/footer.php';
    }
    

    public function detalles(){
        $id_compra = $_REQUEST['id'];
        require_once 'view/devolucion_compras/devolucion_detalles.php';
    }
    
    
    public function ListarDia(){
        
        require_once 'view/header.php';
        require_once 'view/compra/compradia.php';
        require_once 'view/footer.php';
    }

    public function Cambiar(){

        $compra = new compra();
        
        $id_item = $_REQUEST['id_item'];
        $id_compra = $_REQUEST['id_compra'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $cantidad_ant = $_REQUEST['cantidad_ant'];
        
        $cant = $cantidad_ant - $cantidad;

        if($cantidad>0){
            $compra = $this->model->Cantidad($id_item, $id_compra, $cantidad);
            
            if($compra->contado=='Cuota')
                $deuda = $this->deuda->EditarMonto($id_compra, $compra->total_compra);

            if($compra->contado=='Contado')
                $deuda = $this->ingreso->EditarMonto($id_compra, $compra->total_compra);

            
            $this->producto->Sumar($codigo, $cant);

        }
        
        echo json_encode($compra);
    }

    public function Cancelar(){
        
        $id_item = $_REQUEST['id_item'];
        $id_compra = $_REQUEST['id_compra'];
        $codigo = $_REQUEST['codigo'];
        $cantidad = $_REQUEST['cantidad_item'];


        $compra = $this->model->Cantidad($id_item, $id_compra, 0);
            
        if($compra->contado=='Cuota')
            $deuda = $this->deuda->EditarMonto($id_compra, $compra->total_compra);

        if($compra->contado=='Contado')
            $deuda = $this->ingreso->EditarMonto($id_compra, $compra->total_compra);

        $compra = $this->model->CancelarItem($id_item);
        $this->producto->Sumar($codigo, $cantidad);
        header('location: ?c=compra_tmp&a=editar&id='.$id_compra);
    }


    
    public function Crud(){
        $compra = new compra();
        
        if(isset($_REQUEST['id'])){
            $compra = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/compra/compra-editar.php';
        require_once 'view/footer.php';
    }

    public function Cierre(){
        
        require_once 'view/informes/cierrepdf.php';
        
    }
    
    public function InformeDiario(){
        
        require_once 'view/informes/compradiapdf.php';
        
    }
    
    public function InformeRango(){
        
        require_once 'view/informes/comprarangopdf.php';
        
    }
    
    public function InformeUsados(){
        
        require_once 'view/informes/productosusadospdf.php';
        
    }
    
    public function CierreMes(){
        $compra = new compra();
        
        if(isset($_REQUEST['fecha'])){
            $compra = $this->model->ListarMes($_REQUEST['fecha']);
        }
        require_once 'view/informes/cierremesnewpdf.php';
        
    }
        
    public function Factura(){
        
        require_once 'view/informes/facturapdf.php';
        
    }
    
    public function Ticket(){
        
        require_once 'view/informes/ticketpdf.php';
        
    }
    
    public function Obtener(){
        $compra = new compra();
        
        if(isset($_REQUEST['id'])){
            $compra = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/compra/compra-editar.php';
        
    }

    public function ObtenerProducto(){
        $compra = new compra();
        
        $compra = $this->model->ObtenerProducto($_REQUEST['id_compra'], $_REQUEST['id_producto']);
        
        echo json_encode($compra);
        
    }

    public function ObtenerProveedor(){
        
        $devolucion_compras = $this->deuda->ObtenerCliente($_REQUEST['id_cliente']);

        echo json_encode($devolucion_compras);
    }

    public function GuardarUno(){
         

        $compra = new compra();

        $costo = $_REQUEST['precio_costo']*$_REQUEST['cantidad'];
        $p_compra = $_REQUEST['precio_compra']*$_REQUEST['cantidad'];
        
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
        $compra->margen_ganancia = round(((($p_compra - $costo)*100)/$costo),2);
        $compra->fecha_compra = $_REQUEST['fecha_compra'];
        $compra->metodo = $_REQUEST['metodo'];
        $compra->banco = $_REQUEST['banco'];
        $compra->contado = $_REQUEST['contado'];


        $this->producto->Restar($compra);
        

        $compra->id > 0 
            ? $this->model->Actualizar($compra)
            : $this->model->Registrar($compra);



        header('Location: index.php?c=compra_tmp&a=editar&id='.$compra->id_compra);
    }
    
    public function Guardar(){

        $ven = new compra();
        $deuda = new deuda();
        $compra = $this->compra->Obtenercompra($_REQUEST['id_compra']);

        if($_REQUEST['sucursal'] == 'taller'){
            $ruc = $this->cliente->Obtener($compra->id_cliente)->ruc; //Obtenemos el ruc del cliente
            $cliente = $this->cliente->ObtenerTaller($ruc)->id; //Mediante el ruc obtenemos el id correpondiente a la otra base de datos de la sucursal
        }else{
            $cliente = 0;
        }
        

        $sumaTotal = 0;
        if(!isset($_SESSION)) session_start();
        $id_deuda = $this->deuda->Ultimo();

        if($cliente != null){

            foreach($this->devolucion_tmpcompras->Listar() as $v){

                $devolucion_compras = new devolucion_compras();

                $devolucion_compras->id = 0;
                $devolucion_compras->id_compra = $_REQUEST['id_compra'];
                $devolucion_compras->id_cliente = $compra->id_cliente;
                $devolucion_compras->id_vendedor = $_SESSION['user_id'];
                $devolucion_compras->vendedor_salon = 0;
                $devolucion_compras->id_producto = $v->id_producto;
                $devolucion_compras->id_deuda = $id_deuda->id + 1;
                $devolucion_compras->precio_costo = $v->precio_costo;
                $devolucion_compras->precio_compra = $v->precio_compra;
                $devolucion_compras->subtotal = $v->precio_compra*($v->cantidad);
                $devolucion_compras->descuento = $v->descuento;
                $devolucion_compras->iva = 0;
                $devolucion_compras->total = $devolucion_compras->subtotal-($devolucion_compras->subtotal*($devolucion_compras->descuento/100));
                $devolucion_compras->comprobante = $compra->comprobante;
                $devolucion_compras->nro_comprobante = $compra->nro_comprobante;
                $devolucion_compras->cantidad = $v->cantidad;
                $devolucion_compras->margen_ganancia = 0;
                $devolucion_compras->fecha_compra = date("Y-m-d H:i");
                $devolucion_compras->metodo = $_REQUEST['forma_pago'];
                $devolucion_compras->banco = 0;
                $devolucion_compras->contado = $_REQUEST['contado'];
                $devolucion_compras->motivo = $_REQUEST['motivo'];

                //Registrar compra
                $this->model->Registrar($devolucion_compras);
                //restar Stock
                $this->producto->Restar($devolucion_compras);
                $sumaTotal+=$devolucion_compras->total; 

            }

            if($_REQUEST['nota_credito'] == 1){            
                
                $deuda->devolucion = 1;
                $deuda->concepto = 'Devolución de compra N° '.$devolucion_compras->id_compra;
                $deuda->id_venta = 0;
                $deuda->fecha = date("Y-m-d H:i:s");
                $deuda->monto = $sumaTotal;
                $deuda->saldo = $sumaTotal;
                $deuda->sucursal = 0;
                
                if($_REQUEST['sucursal'] != 'taller'){
                    $deuda->id_cliente = $compra->id_cliente;
                    $this->deuda->Registrar($deuda);
                }else{
                    $deuda->id_cliente = $cliente;
                    $this->deuda->RegistrarTaller($deuda);
                }
            }else{
                $ingreso = new ingreso();

                if ($_REQUEST['forma_pago'] == "Efectivo") {
                    if($_SESSION['user_id'] == 16 || $_SESSION['user_id'] == 14){ // si user es Ovidio o Yanina
                        $ingreso->id_caja = 3; // tesoreria
                    }else{
                        $ingreso->id_caja = 1; // caja chica
                    }
                } else {
                    $ingreso->id_caja = 2; // banco
                }
                $ingreso->id_cliente = $compra->id_cliente;
                $ingreso->id_usuario = $_SESSION['user_id'];
                $ingreso->id_compra = $_REQUEST['id_compra'];
                $ingreso->fecha = date("Y-m-d H:i:s");
                $ingreso->categoria = 'Devolución de compra';
                $ingreso->comprobante = $compra->comprobante;
                $ingreso->concepto = 'Devolución de compra N° '.$devolucion_compras->id_compra;
                $ingreso->monto = $sumaTotal;
                $ingreso->forma_pago = $_REQUEST['forma_pago'];
                $ingreso->sucursal = 0;

                $this->ingreso->Registrar($ingreso);
            }

        }else{
            $_SESSION['cliente_error'] = true;
            // O usa un parámetro GET para la redirección
            header('Location: index.php?c=compra&error=cliente');
            exit;
        }

        $this->devolucion_tmpcompras->Vaciar();
        
        if(false){
            header('refresh:0;index.php?c=compra&a=ticket&id=$id');
        }else{
            header('Location: index.php?c=devolucion_compras');
        }
    }
    
    public function Eliminar(){
        
        foreach($this->model->Listar($_REQUEST['id']) as $v){
            $compra = new compra();
            $compra->id_producto = $v->codigo;
            $compra->cantidad = $v->cantidad;
            $this->producto->Sumar($compra);
        }
       // $this->ingreso->Eliminarcompra($_REQUEST['id']);
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=compra');
    }

    public function Anular(){
        foreach($this->model->Listar($_REQUEST['id_compra']) as $v){
            $devolucion_compras = new devolucion_compras();
            $devolucion_compras->id_producto = $v->id_producto;
            $devolucion_compras->cantidad = $v->cantidad;
            $this->producto->RestarDevolucion($devolucion_compras);
        }
        $this->model->Anular($_REQUEST['id_compra']);
        $this->egreso->EliminarDevolucion($_REQUEST['id']);
        
        header('Location:' . getenv('HTTP_REFERER'));
    }
}
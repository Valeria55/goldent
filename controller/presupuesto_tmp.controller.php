<?php
require_once 'model/presupuesto_tmp.php';
require_once 'model/presupuesto.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/caja.php';
require_once 'model/pago_tmp.php';
require_once 'model/metodo.php';
require_once 'model/sucursal.php';
require_once 'model/gift_card.php';
class presupuesto_tmpController{
    
    private $model;
    private $presupuesto_tmp;
    private $presupuesto;
    private $usuario;
    private $vendedor;
    private $producto;
    private $cliente;
    private $cierre;
    private $caja;
    private $pago_tmp;
    private $metodo;
    private $sucursal;
    private $gift_card;
    
    public function __CONSTRUCT(){
        $this->model = new presupuesto_tmp();
        $this->presupuesto_tmp = new presupuesto_tmp();
        $this->presupuesto = new presupuesto();
        $this->usuario = new usuario();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->pago_tmp = new pago_tmp();
        $this->metodo = new metodo();
        $this->sucursal = new sucursal();
        $this->gift_card = new gift_card();
    }
    
    public function Index(){
        $fecha = date ('Y-m-d');
        require_once 'view/header.php';
        require_once 'view/presupuesto/nuevo-presupuesto.php';
        require_once 'view/footer.php';
       
    }
    public function Mayorista(){
        require_once 'view/header.php';
        require_once 'view/presupuesto/nueva-presupuestomayor.php';
        require_once 'view/footer.php';
       
    }
    public function Editar(){

        $presupuesto = new presupuesto();
        
        if(isset($_REQUEST['id'])){
            $presupuesto = $this->presupuesto->ObtenerUno($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuesto-editar.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/presupuesto/nuevo-presupuesto.php';
    }


    
    public function Crud(){
        $presupuesto_tmp = new presupuesto_tmp();
        
        if(isset($_REQUEST['id'])){
            $presupuesto_tmp = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/presupuesto_tmp/presupuesto_tmp-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $presupuesto_tmp = new presupuesto_tmp();
        
        if(isset($_REQUEST['id'])){
            $presupuesto_tmp = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/presupuesto_tmp/presupuesto_tmp-editar.php';
        
    }

    public function ObtenerMoneda(){
        $presupuesto_tmp = new presupuesto_tmp();
        
        $presupuesto_tmp = $this->model->ObtenerMoneda();
        
        
    }
    
    public function Guardar(){

        // $producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        session_start();
        $presupuesto_tmp = new presupuesto_tmp();
        
        $presupuesto_tmp->id = 0;
        $presupuesto_tmp->id_presupuesto = 1;
        $presupuesto_tmp->id_vendedor = $_SESSION['user_id'];
        $presupuesto_tmp->id_producto = $_REQUEST['id_producto'];
        $presupuesto_tmp->id_sucursal = $_REQUEST['id_sucursal'];

        // if ($_REQUEST['precio_venta'] == $pro->precio_minorista){
        //     $presupuesto_tmp->precio_venta = $pro->precio_minorista;
        // }else if ($_REQUEST['precio_venta'] == $pro->precio_mayorista){
        //     $presupuesto_tmp->precio_venta = $pro->precio_mayorista;
        // }else{
        //     $presupuesto_tmp->precio_venta = $pro->precio_turista;
        // }
        $presupuesto_tmp->precio_venta =$_REQUEST['precio_venta'];

        $presupuesto_tmp->cantidad = $_REQUEST['cantidad'];
        $presupuesto_tmp->descuento = $_REQUEST['descuento'];
        $presupuesto_tmp->fecha_presupuesto = date("Y-m-d H:i");
        

        $presupuesto_tmp->id > 0 
            ? $this->model->Actualizar($presupuesto_tmp)
            : $this->model->Registrar($presupuesto_tmp);
        
        //header('Location: index.php?c=presupuesto_tmp');
        require_once "view/presupuesto/tabla_presupuesto.php";
    }

    public function CancelarPresupuesto(){
       
        $this->model->Vaciar();
        header('Location: index.php?c=presupuesto_tmp');
        //header('Location: index.php?c=venta_tmp');
    }

    public function GuardarMayorista(){

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        session_start();
        $presupuesto_tmp = new presupuesto_tmp();
        
        $presupuesto_tmp->id = 0;
        $presupuesto_tmp->id_presupuesto = 1;
        $presupuesto_tmp->id_vendedor = $_SESSION['user_id'];
        $presupuesto_tmp->id_producto = $_REQUEST['id_producto'];
        $presupuesto_tmp->precio_venta = $_REQUEST['precio_venta'];
        $presupuesto_tmp->cantidad = $_REQUEST['cantidad'];
        $presupuesto_tmp->descuento = $_REQUEST['descuento'];
        $presupuesto_tmp->fecha_presupuesto = date("Y-m-d H:i");
        

        $presupuesto_tmp->id > 0 
            ? $this->model->Actualizar($presupuesto_tmp)
            : $this->model->Registrar($presupuesto_tmp);
        
        header('Location: index.php?c=presupuesto_tmp&a=mayorista');
    }

    public function GuardarUno(){
         

        $presupuesto = new presupuesto();

        $costo = $_REQUEST['precio_costo'];
        $presupuesto = $_REQUEST['precio_venta'];
        
        $presupuesto->id = 0;
        $presupuesto->id_presupuesto = 1;
        $presupuesto->id_cliente = $_REQUEST['id_cliente'];
        $presupuesto->id_vendedor = $_REQUEST['id_presupuesto'];
        $presupuesto->id_producto = $_REQUEST['codigo'];
        $presupuesto->precio_costo = $_REQUEST['precio_costo'];
        $presupuesto->precio_venta = $_REQUEST['precio_venta'];
        $presupuesto->subtotal = $_REQUEST['subtotal'];
        $presupuesto->descuento = 0;
        $presupuesto->iva = 0;
        $presupuesto->total = $_REQUEST['total'];
        $presupuesto->comprobante = $_REQUEST['comprobante'];
        $presupuesto->nro_comprobante = $_REQUEST['nro_comprobante'];
        $presupuesto->cantidad = $_REQUEST['cantidad'];
        $presupuesto->fecha_presupuesto = $_REQUEST['fecha_presupuesto'];
        $presupuesto->metodo = $_REQUEST['metodo'];
        $presupuesto->banco = $_REQUEST['banco'];
        $presupuesto->contado = $_REQUEST['contado'];
        

        $presupuesto->id > 0 
            ? $this->presupuesto->Actualizar($presupuesto_tmp)
            : $this->presupuesto->Registrar($presupuesto_tmp);

       
        header('Location: index.php?c=presupuesto_tmp&a=editar&id='.$presupuesto->id_presupuesto);
    }

    public function Moneda(){

        $presupuesto_tmp = new presupuesto_tmp();
        
        $presupuesto_tmp->id = 0;
        $presupuesto_tmp->reales = $_REQUEST['reales'];
        $presupuesto_tmp->dolares = $_REQUEST['dolares'];
        $presupuesto_tmp->monto_inicial = $_REQUEST['monto_inicial'];
        
        $this->model->Moneda($presupuesto_tmp);
        
        header('Location: index.php?c=presupuesto_tmp');
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        require_once "view/presupuesto/tabla_presupuesto.php";
        //header('Location: index.php?c=presupuesto_tmp');
    }
}
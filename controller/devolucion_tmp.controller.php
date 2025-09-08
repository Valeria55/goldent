<?php
require_once 'model/devolucion_tmp.php';
require_once 'model/venta.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/caja.php';

class devolucion_tmpController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new devolucion_tmp();
        $this->venta = new venta();
        $this->usuario = new usuario();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->caja = new caja();
    }
    
    public function Index(){
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {     
            require_once 'view/devolucion/nueva-devolucion.php';
        }else{
            require_once 'view/venta/apertura.php';
        }
        require_once 'view/footer.php';
       
    }

    public function Devolucion(){
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {     
            require_once 'view/venta/nueva-devolucion.php';
        }else{
            require_once 'view/venta/apertura.php';
        }
        require_once 'view/footer.php';
       
    }
    
    public function Mayorista(){
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {     
            require_once 'view/venta/nueva-ventamayor.php';
        }else{
            require_once 'view/venta/apertura.php';
        }
        require_once 'view/footer.php';
       
    }

    public function Editar(){

        $venta = new venta();
        
        if(isset($_REQUEST['id'])){
            $venta = $this->venta->ObtenerUno($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/venta/venta-editar.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/venta/nueva-venta.php';
    }


    
    public function Crud(){
        $devolucion_tmp = new devolucion_tmp();
        
        if(isset($_REQUEST['id'])){
            $devolucion_tmp = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/devolucion_tmp/devolucion_tmp-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $devolucion_tmp = new devolucion_tmp();
        
        if(isset($_REQUEST['id'])){
            $devolucion_tmp = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/devolucion_tmp/devolucion_tmp-editar.php';
        
    }

    public function ObtenerMoneda(){
        $devolucion_tmp = new devolucion_tmp();
        
        $devolucion_tmp = $this->model->ObtenerMoneda();
        
        
    }
    
    public function Guardar(){

        $id_venta = $_REQUEST["id_venta"];
        $producto = $this->producto->Obtener($_REQUEST['id_producto']);
        session_start();
        $devolucion_tmp = new devolucion_tmp();
        
        $devolucion_tmp->id = 0;
        $devolucion_tmp->id_venta = 1;
        $devolucion_tmp->id_vendedor = $_SESSION['user_id'];
        $devolucion_tmp->id_producto = $_REQUEST['id_producto'];
        $devolucion_tmp->precio_venta = $_REQUEST['precio_venta'];
        $devolucion_tmp->cantidad = $_REQUEST['cantidad'];
        $devolucion_tmp->descuento = $_REQUEST['descuento'];
        $devolucion_tmp->fecha_venta = date("Y-m-d H:i");
        

        $devolucion_tmp->id > 0 
            ? $this->model->Actualizar($devolucion_tmp)
            : $this->model->Registrar($devolucion_tmp);
        
        header('Location:' . getenv('HTTP_REFERER'));
    }

    public function GuardarDevolucion(){

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        session_start();
        $devolucion_tmp = new devolucion_tmp();
        $id_venta = $_SESSION['id_venta'];
        $devolucion_tmp->id = 0;
        $devolucion_tmp->id_venta = 1;
        $devolucion_tmp->id_vendedor = $_SESSION['user_id'];
        $devolucion_tmp->id_producto = $_REQUEST['id_producto'];
        $devolucion_tmp->precio_venta = $_REQUEST['precio_venta'];
        $devolucion_tmp->cantidad = $_REQUEST['cantidad'];
        $devolucion_tmp->descuento = $_REQUEST['descuento'];
        $devolucion_tmp->fecha_venta = date("Y-m-d H:i");
        

        $devolucion_tmp->id > 0 
            ? $this->model->Actualizar($devolucion_tmp)
            : $this->model->Registrar($devolucion_tmp);
        
        header("Location: index.php?c=devolucion_tmp&a=devolucion&id_venta=$id_venta");
    }
    
    public function GuardarMayorista(){

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        session_start();
        $devolucion_tmp = new devolucion_tmp();
        
        $devolucion_tmp->id = 0;
        $devolucion_tmp->id_venta = 1;
        $devolucion_tmp->id_vendedor = $_SESSION['user_id'];
        $devolucion_tmp->id_producto = $_REQUEST['id_producto'];
        $devolucion_tmp->precio_venta = $_REQUEST['precio_venta'];
        $devolucion_tmp->cantidad = $_REQUEST['cantidad'];
        $devolucion_tmp->descuento = $_REQUEST['descuento'];
        $devolucion_tmp->fecha_venta = date("Y-m-d H:i");
        

        $devolucion_tmp->id > 0 
            ? $this->model->Actualizar($devolucion_tmp)
            : $this->model->Registrar($devolucion_tmp);
        
        header('Location: index.php?c=devolucion_tmp&a=mayorista');
    }

    public function GuardarUno(){
         

        $venta = new venta();

        $costo = $_REQUEST['precio_costo'];
        $venta = $_REQUEST['precio_venta'];
        
        $venta->id = 0;
        $venta->id_venta = 1;
        $venta->id_cliente = $_REQUEST['id_cliente'];
        $venta->id_vendedor = $_REQUEST['id_venta'];
        $venta->id_producto = $_REQUEST['codigo'];
        $venta->precio_costo = $_REQUEST['precio_costo'];
        $venta->precio_venta = $_REQUEST['precio_venta'];
        $venta->subtotal = $_REQUEST['subtotal'];
        $venta->descuento = 0;
        $venta->iva = 0;
        $venta->total = $_REQUEST['total'];
        $venta->comprobante = $_REQUEST['comprobante'];
        $venta->nro_comprobante = $_REQUEST['nro_comprobante'];
        $venta->cantidad = $_REQUEST['cantidad'];
        $venta->margen_ganancia = round(((($venta - $costo)*100)/$costo),2);
        $venta->fecha_venta = $_REQUEST['fecha_venta'];
        $venta->metodo = $_REQUEST['metodo'];
        $venta->banco = $_REQUEST['banco'];
        $venta->contado = $_REQUEST['contado'];
        

        $venta->id > 0 
            ? $this->venta->Actualizar($devolucion_tmp)
            : $this->venta->Registrar($devolucion_tmp);

        if($venta->contado=='Cuota')
            $deuda = $this->deuda->EditarMonto($venta->id_venta, $venta->total);

        if($venta->contado=='Contado')
            $deuda = $this->ingreso->EditarMonto($venta->id_venta, $venta->total);

        header('Location: index.php?c=devolucion_tmp&a=editar&id='.$venta->id_venta);
    }

    public function Moneda(){

        $devolucion_tmp = new devolucion_tmp();
        
        $devolucion_tmp->id = 0;
        $devolucion_tmp->reales = $_REQUEST['reales'];
        $devolucion_tmp->dolares = $_REQUEST['dolares'];
        $devolucion_tmp->monto_inicial = $_REQUEST['monto_inicial'];
        
        $this->model->Moneda($devolucion_tmp);
        
        header('Location: index.php?c=devolucion_tmp');
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location:' . getenv('HTTP_REFERER'));
    }
}
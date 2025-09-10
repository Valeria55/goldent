<?php
require_once 'model/devolucion_tmpcompras.php';
require_once 'model/compra.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/caja.php';
require_once 'model/metodo.php';

class devolucion_tmpcomprasController{
    
    private $model;
    private $compra;
    private $usuario;
    private $vendedor;
    private $producto;
    private $cliente;
    private $cierre;
    private $caja;
    private $metodo;
    
    public function __CONSTRUCT(){
        $this->model = new devolucion_tmpcompras();
        $this->compra = new compra();
        $this->usuario = new usuario();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->metodo = new metodo();
    }
    
    public function Index(){
        require_once 'view/header.php';
        // if (!isset($_SESSION)) session_start();
        // if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {     
            require_once 'view/devolucion_compras/nueva-devolucion.php';
        // }
        require_once 'view/footer.php';
       
    }
       public function Devolucion_compras(){
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {     
            require_once 'view/devolucion_compras/nueva-devolucion.php';
        }else{
            require_once 'view/compra/apertura.php';
        }
        require_once 'view/footer.php';
       
    }
    
    public function Mayorista(){
        require_once 'view/header.php';
        if ($cierre = $this->cierre->Consultar($_SESSION['user_id'])) {     
            require_once 'view/compra/nueva-compramayor.php';
        }else{
            require_once 'view/compra/apertura.php';
        }
        require_once 'view/footer.php';
       
    }

    public function Editar(){

        $compra = new compra();
        
        if(isset($_REQUEST['id'])){
            $compra = $this->compra->ObtenerUno($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/compra/compra-editar.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/compra/nueva-compra.php';
    }


    
    public function Crud(){
        $devolucion_tmpcompras = new devolucion_tmpcompras();
        
        if(isset($_REQUEST['id'])){
            $devolucion_tmpcompras = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/devolucion_tmpcompras/devolucion_tmpcompras-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $devolucion_tmpcompras = new devolucion_tmpcompras();
        
        if(isset($_REQUEST['id'])){
            $devolucion_tmpcompras = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/devolucion_tmpcompras/devolucion_tmpcompras-editar.php';
        
    }

    public function ObtenerMoneda(){
        $devolucion_tmpcompras = new devolucion_tmpcompras();
        
        $devolucion_tmpcompras = $this->model->ObtenerMoneda();
        
        
    }
    
    public function Guardar(){

        $id_compra = $_REQUEST["id_compra"];
        $producto = $this->producto->Obtener($_REQUEST['id_producto']);
        session_start();
        $devolucion_tmpcompras = new devolucion_tmpcompras();
        
        $devolucion_tmpcompras->id = 0;
        $devolucion_tmpcompras->id_compra = 1;
        $devolucion_tmpcompras->id_vendedor = $_SESSION['user_id'];
        $devolucion_tmpcompras->id_producto = $_REQUEST['id_producto'];
        $devolucion_tmpcompras->precio_compra = $_REQUEST['precio_compra'];
        $devolucion_tmpcompras->cantidad = $_REQUEST['cantidad'];
        $devolucion_tmpcompras->descuento = $_REQUEST['descuento'];
        $devolucion_tmpcompras->fecha_compra = date("Y-m-d H:i");
        

        $devolucion_tmpcompras->id > 0 
            ? $this->model->Actualizar($devolucion_tmpcompras)
            : $this->model->Registrar($devolucion_tmpcompras);
        
        header('Location:' . getenv('HTTP_REFERER'));
    }

    
    public function GuardarMayorista(){

        $producto = $this->producto->Codigo($_REQUEST['codigo']);
        session_start();
        $devolucion_tmpcompras = new devolucion_tmpcompras();
        
        $devolucion_tmpcompras->id = 0;
        $devolucion_tmpcompras->id_compra = 1;
        $devolucion_tmpcompras->id_vendedor = $_SESSION['user_id'];
        $devolucion_tmpcompras->id_producto = $_REQUEST['id_producto'];
        $devolucion_tmpcompras->precio_compra = $_REQUEST['precio_compra'];
        $devolucion_tmpcompras->cantidad = $_REQUEST['cantidad'];
        $devolucion_tmpcompras->descuento = $_REQUEST['descuento'];
        $devolucion_tmpcompras->fecha_compra = date("Y-m-d H:i");
        

        $devolucion_tmpcompras->id > 0 
            ? $this->model->Actualizar($devolucion_tmpcompras)
            : $this->model->Registrar($devolucion_tmpcompras);
        
        header('Location: index.php?c=devolucion_tmpcompras&a=mayorista');
    }

    public function GuardarUno(){
         

        $compra = new compra();

        $costo = $_REQUEST['precio_costo'];
        $compra = $_REQUEST['precio_compra'];
        
        $compra->id = 0;
        $compra->id_compra = 1;
        $compra->id_cliente = $_REQUEST['id_cliente'];
        $compra->id_vendedor = $_REQUEST['id_compra'];
        $compra->id_producto = $_REQUEST['codigo'];
        $compra->precio_costo = $_REQUEST['precio_costo'];
        $compra->precio_compra = $_REQUEST['precio_compra'];
        $compra->subtotal = $_REQUEST['subtotal'];
        $compra->descuento = 0;
        $compra->iva = 0;
        $compra->total = $_REQUEST['total'];
        $compra->comprobante = $_REQUEST['comprobante'];
        $compra->nro_comprobante = $_REQUEST['nro_comprobante'];
        $compra->cantidad = $_REQUEST['cantidad'];
        $compra->margen_ganancia = round(((($compra - $costo)*100)/$costo),2);
        $compra->fecha_compra = $_REQUEST['fecha_compra'];
        $compra->metodo = $_REQUEST['metodo'];
        $compra->banco = $_REQUEST['banco'];
        $compra->contado = $_REQUEST['contado'];
        

        $compra->id > 0 
            ? $this->compra->Actualizar($devolucion_tmpcompras)
            : $this->compra->Registrar($devolucion_tmpcompras);

        if($compra->contado=='Cuota')
            $deuda = $this->deuda->EditarMonto($compra->id_compra, $compra->total);

        if($compra->contado=='Contado')
            $deuda = $this->ingreso->EditarMonto($compra->id_compra, $compra->total);

        header('Location: index.php?c=devolucion_tmpcompras&a=editar&id='.$compra->id_compra);
    }

    public function Moneda(){

        $devolucion_tmpcompras = new devolucion_tmpcompras();
        
        $devolucion_tmpcompras->id = 0;
        $devolucion_tmpcompras->reales = $_REQUEST['reales'];
        $devolucion_tmpcompras->dolares = $_REQUEST['dolares'];
        $devolucion_tmpcompras->monto_inicial = $_REQUEST['monto_inicial'];
        
        $this->model->Moneda($devolucion_tmpcompras);
        
        header('Location: index.php?c=devolucion_tmpcompras');
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location:' . getenv('HTTP_REFERER'));
    }
}
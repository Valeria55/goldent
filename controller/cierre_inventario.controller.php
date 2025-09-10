<?php
require_once 'model/cierre.php';
require_once 'model/cierre_inventario.php';
require_once 'model/usuario.php';
require_once 'model/producto.php';
require_once 'model/inventario.php';


class cierre_inventarioController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new cierre_inventario();
        $this->usuario = new usuario();
        $this->cierre = new cierre();
        $this->producto = new producto();
         $this->inventario = new inventario();

    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/inventario/cierre-inventario.php';
        require_once 'view/footer.php';
       
    }

      public function Cierreinventario(){
        require_once 'view/header.php';
        require_once 'view/inventario/cierre-inventario.php';
        require_once 'view/footer.php';
       
    }

    public function detalles(){
        
        require_once 'view/header.php';
        require_once 'view/inventario/detalles_modal.php';
        require_once 'view/footer.php';
        
    }

    public function Crud(){
        $cierre_inventario = new cierre_inventario();
        
        if(isset($_REQUEST['id'])){
            $cierre_inventario = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/inventario/cierre-inventario.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $cierre_inventario = new cierre_inventario();
        
        if(isset($_REQUEST['id'])){
            $cierre_inventario = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/inventario/cierre-inventario.php';
        
    }

    
    /*public function Apertura(){

        $cierre = new cierre();
        
        session_start();
        $cierre->fecha_apertura = date("Y-m-d H:i");
        $cierre->fecha_cierre = null;
        $cierre->usuario_inicial = $_SESSION['user_id'];
      
        $this->model->Registrar($cierre);
       // $this->model->CierreApertura($cierre);
       sirve para ver los errores
       var_dump(Apertura);
        
        header('Location:' . getenv('HTTP_REFERER'));
    }
*/

    public function Cierre(){

        $cierre_inventario = new cierre_inventario();
        
        session_start();
        $cierre_inventario->fecha_cierre = date("Y-m-d H:i");
        // $cierre_inventario->usuario_inicial = $_SESSION['user_id'];
        $cierre_inventario->usuario_final = $_SESSION['user_id'];
        $cierre_inventario->motivo = $_REQUEST['motivo'];
        $cierre_inventario->sobrante_caja = $_REQUEST['sobrante_caja'];
        $cierre_inventario->id = $_REQUEST['id_c'];

        $this->model->Cierre($cierre_inventario);
        //$this->model->Registrar($cierre);
        $fecha = date("Y-m-d");

        // carga el faltante de los productos no cargados
        $this->inventario->SetFaltanteNoCargado($cierre_inventario);
        //actualiza los productos con su nuevo stock segun inventario
        $this->inventario->StockDeInventario($cierre_inventario);
        // listar  por id
        // foreach($this->inventario->Listar($fecha) as $i){

        //     $producto = new producto();

        //     $producto->id = $i->id_producto;
        //     $producto->stock = $i->stock_real;
        //     $this->producto->GuardarStock($producto);
        // }  


        //session_destroy();        
        header('Location: index.php?c=cierre_inventario&a=Cierreinventario');
    }

    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=cierre_inventario');
    }

}
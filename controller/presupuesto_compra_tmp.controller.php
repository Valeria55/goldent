<?php
require_once 'model/presupuesto_compra.php';
require_once 'model/presupuesto_compra_tmp.php';
require_once 'model/compra_tmp.php';
require_once 'model/compra.php';
require_once 'model/vendedor.php';
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/egreso.php';
require_once 'model/cierre.php';
require_once 'model/metodo.php';

class presupuesto_compra_tmpController{
    
    private $model;

    
    public function __CONSTRUCT(){
        $this->model = new presupuesto_compra_tmp();
        $this->presupuesto_compra = new presupuesto_compra();
        $this->cierre = new cierre();
        $this->compra = new compra();
        $this->vendedor = new vendedor();
        $this->producto = new producto();
        $this->cliente = new cliente();
        $this->metodo = new metodo();

    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/presupuesto_compra/nuevo-presupuesto-compra.php';
        require_once 'view/footer.php';
       
    }

    // public function Editar(){

    //     $presupuesto_compra = new presupuesto_compra();
        
    //     if(isset($_REQUEST['id'])){
    //         $presupuesto_compra = $this->presupuesto_compra->ObtenerUno($_REQUEST['id']);
    //     }

    //     require_once 'view/header.php';
    //     require_once 'view/presupuesto_compra/compra-editar.php';
    //     require_once 'view/footer.php';
       
    // }


    // public function Crud(){
    //     $presupuesto_compra_tmp = new presupuesto_compra_tmp();
        
    //     if(isset($_REQUEST['id'])){
    //         $presupuesto_compra_tmp = $this->model->Obtener($_REQUEST['id']);
    //     }
        
    //     require_once 'view/header.php';
    //     require_once 'view/compra_tmp/compra_tmp-editar.php';
    //     require_once 'view/footer.php';
    // }
    
    // public function Obtener(){
    //     $presupuesto_compra_tmp = new presupuesto_compra_tmp();
        
    //     if(isset($_REQUEST['id'])){
    //         $presupuesto_compra_tmp = $this->model->Obtener($_REQUEST['id']);
    //     }
        
    //     require_once 'view/compra_tmp/compra_tmp-editar.php';
        
    // }

    
    public function Guardar(){
        
        // $producto = $this->producto->Codigo($_REQUEST['codigo']);
        $producto = $this->producto->Obtener($_REQUEST['id_producto']);
        session_start();
        $presupuesto_compra_tmp = new presupuesto_compra_tmp();
        
        $presupuesto_compra_tmp->id = 0;
        $presupuesto_compra_tmp->id_presupuesto = 1;
        $presupuesto_compra_tmp->id_vendedor = $_SESSION['user_id'];
        $presupuesto_compra_tmp->id_producto = $_REQUEST['id_producto'];
        $presupuesto_compra_tmp->precio_compra = $_REQUEST['precio_compra'];
        $presupuesto_compra_tmp->precio_min = $_REQUEST['precio_min'];
        $presupuesto_compra_tmp->precio_may = $_REQUEST['precio_may'];
        // $presupuesto_compra_tmp->precio_brasil = $_REQUEST['precio_brasil'];
        $presupuesto_compra_tmp->precio_intermedio = $_REQUEST['precio_intermedio'];
        $presupuesto_compra_tmp->cantidad = $_REQUEST['cantidad'];
        $presupuesto_compra_tmp->fecha_compra = date("Y-m-d H:i");
        
        if($this->model->ObtenerPorProductoUsuario($presupuesto_compra_tmp)){
            $dupl = '&dupl=1';
        }elseif(!($presupuesto_compra_tmp->cantidad > 0)){
            $dupl = '&cant=1';
        }else{
            $dupl = '';
            $presupuesto_compra_tmp->id > 0 
                ? $this->model->Actualizar($presupuesto_compra_tmp)
                : $this->model->Registrar($presupuesto_compra_tmp);
        }
        
        header('Location: index.php?c=presupuesto_compra_tmp'.$dupl);
    }

   
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=presupuesto_compra_tmp');     
    }

    public function CancelarPresupuesto(){
       
        $this->model->Vaciar();
        header('Location: index.php?c=presupuesto_compra_tmp');

    }




}
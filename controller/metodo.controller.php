<?php

require_once 'model/metodo.php';
require_once 'model/venta_tmp.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';

class metodoController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new metodo();
        $this->metodo = new metodo();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/metodo/metodo.php';
        require_once 'view/footer.php';
       
    }

    public function Movimientos(){
        require_once 'view/header.php';
        if($_REQUEST['metodo']!='Efectivo'){
            require_once 'view/metodo/movimientos.php';
        }else{
             require_once 'view/metodo/movimiento_efectivo.php';
        }
       
        require_once 'view/footer.php';
    }


    public function Listar(){
        require_once 'view/metodo/metodo.php';
    }

    public function informeMetodo(){
        require_once 'view/informes/metodo_pago.php';
    }
    
    public function Crud(){
        $metodo = new metodo();
        
        if(isset($_REQUEST['id'])){
            $metodo = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/metodo/metodo-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $metodo = new metodo();
        
        if(isset($_REQUEST['id'])){
            $metodo = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/metodo/metodo-editar.php';
        
    }
    
    public function Guardar(){
        
        $metodo = new metodo();

        $metodo->id = $_REQUEST['id'];
        $metodo->metodo = $_REQUEST['metodo'];
        $metodo->saldo_inicial = $_REQUEST['saldo_inicial'];
        $metodo->porcentaje = $_REQUEST['porcentaje'];
        $metodo->fecha_inicio = $_REQUEST['fecha_inicio'];

        
        $metodo->id > 0 
            ? $this->model->Actualizar($metodo)
            : $this->model->Registrar($metodo);
        //require_once 'view/metodo/metodo.php';
        //header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c'].'&id_prod='.$_REQUEST['id_producto'].'&prod='.$_REQUEST['prod']);
        header('Location:' . getenv('HTTP_REFERER'));

    }
    
    public function Eliminar(){
        $this->model->Anular($_REQUEST['id']);
        header('Location:' . getenv('HTTP_REFERER'));
        //header('Location: index.php?success=Eliminado&c='.$_REQUEST['c'].'&id_prod='.$_REQUEST['id_producto'].'&prod='.$_REQUEST['prod']);
    }
}
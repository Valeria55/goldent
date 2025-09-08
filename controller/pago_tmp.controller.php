<?php

require_once 'model/pago_tmp.php';
require_once 'model/venta_tmp.php';
require_once 'model/metodo.php';
require_once 'model/gift_card.php';
require_once 'model/cierre.php';

class pago_tmpController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new pago_tmp();
        $this->pago_tmp = new pago_tmp();
        $this->venta_tmp = new venta_tmp();
        $this->metodo = new metodo();
        $this->gift_card = new gift_card();
        $this->cierre = new cierre();
    }
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/pago_tmp/pago_tmp.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/pago_tmp/pago_tmp.php';
    }


    
    public function Crud(){
        $pago_tmp = new pago_tmp();
        
        if(isset($_REQUEST['id'])){
            $pago_tmp = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/pago_tmp/pago_tmp-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $pago_tmp = new pago_tmp();
        
        if(isset($_REQUEST['id'])){
            $pago_tmp = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/pago_tmp/pago_tmp-editar.php';
        
    }
    
    public function Guardar(){
        
        $pago_tmp = new pago_tmp();

        session_start();
        
        $pago_tmp->id_usuario = $_SESSION['user_id'];
        $pago_tmp->pago = $_REQUEST['pago'];
        $pago_tmp->monto = $_REQUEST['monto'];
        $pago_tmp->moneda = $_REQUEST['moneda'];
        $pago_tmp->tarjeta = $_REQUEST['tarjeta'];


        $this->model->Registrar($pago_tmp);
        require_once 'view/pago_tmp/pago_tmp.php';
        //header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c'].'&id_prod='.$_REQUEST['id_producto'].'&prod='.$_REQUEST['prod']);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        require_once 'view/pago_tmp/pago_tmp.php';
        //header('Location: index.php?success=Eliminado&c='.$_REQUEST['c'].'&id_prod='.$_REQUEST['id_producto'].'&prod='.$_REQUEST['prod']);
    }
}
<?php
require_once 'model/sucursal.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';

class sucursalController{
    
    private $model;
    private $cierre;
    private $cliente;
    
    public function __CONSTRUCT(){
        $this->model = new sucursal();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/sucursal/sucursal.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/sucursal/sucursal.php';
    }


    
    public function Crud(){
        $sucursal = new sucursal();
        
        if(isset($_REQUEST['id'])){
            $sucursal = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/sucursal/sucursal-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $sucursal = new sucursal();
        
        if(isset($_REQUEST['id'])){
            $sucursal = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/sucursal/sucursal-editar.php';
        
    }
    
    public function Guardar(){
        $sucursal = new sucursal();
        
        $sucursal->id = $_REQUEST['id'];
        $sucursal->sucursal = $_REQUEST['sucursal'];


      

        $sucursal->id > 0 
            ? $this->model->Actualizar($sucursal)
            : $this->model->Registrar($sucursal);
            
        $sucursal->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
        
        header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
}
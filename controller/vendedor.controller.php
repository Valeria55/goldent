<?php
require_once 'model/vendedor.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';

class vendedorController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new vendedor();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/vendedor/vendedor.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/vendedor/vendedor.php';
    }


    
    public function Crud(){
        $vendedor = new vendedor();
        
        if(isset($_REQUEST['id'])){
            $vendedor = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/vendedor/vendedor-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $vendedor = new vendedor();
        
        if(isset($_REQUEST['id'])){
            $vendedor = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/vendedor/vendedor-editar.php';
        
    }
    
    public function Guardar(){
        $vendedor = new vendedor();
        
        $vendedor->id = $_REQUEST['id'];
        $vendedor->nombre = $_REQUEST['nombre'];
        $vendedor->porcentaje = $_REQUEST['porcentaje'];   
      

        $vendedor->id > 0 
            ? $this->model->Actualizar($vendedor)
            : $this->model->Registrar($vendedor);
        
        header('Location: index.php?c=vendedor');
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=vendedor');
    }
}
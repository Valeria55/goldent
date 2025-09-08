<?php
require_once 'model/marca.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';



class marcaController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new marca();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/marca/marca.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/marca/marca.php';
    }


    
    public function Crud(){
        $marca = new marca();
        
        if(isset($_REQUEST['id'])){
            $marca = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/marca/marca-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $marca = new marca();
        
        if(isset($_REQUEST['id'])){
            $marca = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/marca/marca-editar.php';
        
    }
    
    public function Guardar(){
        $marca = new marca();
        
        $marca->id = $_REQUEST['id'];
        $marca->marca = $_REQUEST['marca'];


      

        $marca->id > 0 
            ? $this->model->Actualizar($marca)
            : $this->model->Registrar($marca);
            
        $marca->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
        
        header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
}
<?php
require_once 'model/categoria.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';

class categoriaController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new categoria();
        $this->cierre = new cierre();
        $this->cliente = new cliente();

    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/categoria/categoria.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/categoria/categoria.php';
    }


    
    public function Crud(){
        $categoria = new categoria();
        
        if(isset($_REQUEST['id'])){
            $categoria = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/categoria/categoria-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $categoria = new categoria();
        
        if(isset($_REQUEST['id'])){
            $categoria = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/categoria/categoria-editar.php';
        
    }
    
    public function Guardar(){
        $categoria = new categoria();
        
        $categoria->id = $_REQUEST['id'];
        $categoria->id_padre = $_REQUEST['id_padre'];
        $categoria->categoria = $_REQUEST['categoria'];
        $categoria->producto = $_REQUEST['producto'];

        $categoria->id > 0 
            ? $this->model->Actualizar($categoria)
            : $this->model->Registrar($categoria);
            
        $categoria->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
            
        $id= $this->model->Ultimo();
        var_dump($id->id);
        
          if($_REQUEST['id_padre'] == 0)  {
            
         $this->model->GuardarPadre($id->id);
                   
               
          }
        
        header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
}
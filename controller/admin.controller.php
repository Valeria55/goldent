<?php
require_once 'model/producto.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';


class adminController{
    
    private $model;
   
    
    public function __CONSTRUCT(){
        $this->model = new producto();
        $this->cliente = new cliente();
        $this->cierre = new cierre();
    } 

    public function Index(){

        require_once 'view/header.php';
        require_once 'view/producto/producto.php';
        require_once 'view/footer.php';
       
    }

    public function Filtrado(){
        
        require_once 'view/pagina/head.php';
        require_once 'view/pagina/header.php';
        require_once 'view/pagina/filtrado.php';
        require_once 'view/pagina/relacionados.php';
        require_once 'view/pagina/footer.php';
       
    }



    public function Listar(){
        require_once 'view/pagina/pagina.php';
    }


    
    public function Crud(){
        $pagina = new pagina();
        
        if(isset($_REQUEST['id'])){
            $pagina = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/pagina/pagina-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $pagina = new pagina();
        
        if(isset($_REQUEST['id'])){
            $pagina = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/pagina/pagina-editar.php';
        
    }
    
    public function Guardar(){
        $pagina = new pagina();
        
        $pagina->id = $_REQUEST['id'];
        $pagina->user = $_REQUEST['user'];
        $pagina->pass = $_REQUEST['pass'];
        $pagina->nivel = $_REQUEST['nivel'];   
      

        $pagina->id > 0 
            ? $this->model->Actualizar($pagina)
            : $this->model->Registrar($pagina);
            
        $pagina->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
        
        header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
}
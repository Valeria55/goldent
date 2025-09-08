<?php
require_once 'model/slider.php';
require_once 'model/categoria.php';
require_once 'model/producto.php';
require_once 'model/imagen.php';
require_once 'model/cliente.php';

class paginaController{
    
    private $model;
   
    
    public function __CONSTRUCT(){
        $this->slider = new slider();
        $this->categoria = new categoria();
        $this->producto = new producto();
        $this->imagen = new imagen();
        $this->cliente = new cliente();
    }
    
    public function Index(){

        require_once 'view/pagina/index.php';
       
    }
    
    public function Contacto(){

        require_once 'view/pagina/contact.php';
       
    }
    
    public function QuienesSomos(){

        require_once 'view/pagina/quienesomos.php';
       
    }
    
    
    public function Cart(){

        require_once 'view/pagina/cart.php';
       
    }


    public function Categorias(){

        require_once 'view/pagina/categorias.php';
       
    }
    
    public function Buscar(){

        require_once 'view/pagina/buscar.php';
       
    }


    public function Detalles(){

        if(isset($_REQUEST['id'])){
            $producto = $this->producto->Obtener($_REQUEST['id']);
            $imagen = $this->imagen->UnaImagen($_REQUEST['id']);
        }

        require_once 'view/pagina/single-product.php';
       
    }

    public function DetallesQuick(){

        if(isset($_REQUEST['id'])){
            $producto = $this->producto->Obtener($_REQUEST['id']);
            $imagen = $this->imagen->UnaImagen($_REQUEST['id']);
        }

        require_once 'view/pagina/head.php';
        require_once 'view/pagina/detalles.php';
       
    }


    public function Ofertas(){

        if(isset($_REQUEST['id'])){
            $producto = $this->producto->Obtener($_REQUEST['id']);
            $imagen = $this->imagen->UnaImagen($_REQUEST['id']);
        }

        require_once 'view/pagina/head.php';
        require_once 'view/pagina/header.php';
        require_once 'view/pagina/ofertas.php';
        require_once 'view/pagina/footer.php';
       
    }

    public function Filtrado(){
        
        require_once 'view/pagina/head.php';
        require_once 'view/pagina/header.php';
        require_once 'view/pagina/filtrado.php';
        require_once 'view/pagina/relacionados.php';
        require_once 'view/pagina/footer.php';
       
    }

    public function Admin(){
        
        require_once 'view/header.php';
        require_once 'view/pagina/index.php';
        require_once 'view/footer.php';
       
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
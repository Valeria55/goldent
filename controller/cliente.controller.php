<?php
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/cuenta.php';


class clienteController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new cliente();
        $this->cuenta = new cuenta();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/cliente/cliente.php';
        require_once 'view/footer.php';
       
    }

    public function Cumple(){
        require_once 'view/header.php';
        require_once 'view/cliente/cliente-cumple.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/cliente/cliente.php';
    }

    public function ModalCliente(){
         require_once 'view/cliente/cliente-editar.php';
    }


    
    public function Crud(){
        $cliente = new cliente();
        
        if(isset($_REQUEST['id'])){
            $cliente = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/cliente/cliente-editar.php';
        require_once 'view/footer.php';
    }

     public function detalles(){
        $cliente = new cliente();
        
        if(isset($_REQUEST['id'])){
            $cliente = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/cliente/cliente-detalle.php';
        require_once 'view/footer.php';
    }

    public function ListarAJAX(){

        $cliente = new cliente();
        
        $cliente = $this->model->ListarAJAX();
        echo json_encode($cliente, JSON_UNESCAPED_UNICODE);

    }
    
    public function Obtener(){
        $cliente = new cliente();
        
        if(isset($_REQUEST['id'])){
            $cliente = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/cliente/cliente-editar.php';
        
    }

    public function Buscar(){
        $cliente = new cliente();
        
        if(isset($_REQUEST['id'])){
            $cliente = $this->model->Obtener($_REQUEST['id']);
        }
        
        echo json_encode($cliente);
        
    }

     public function guardarCliente(){
        $cliente = new cliente();

        //Subida de imágenes

        
           //Recogemos el archivo enviado por el formulario
           $archivo = $_FILES['foto_perfil']['name'];
           //Si el archivo contiene algo y es diferente de vacio
           if (isset($archivo) && $archivo != "") {
                //Obtenemos algunos datos necesarios sobre el archivo
                $tipo = $_FILES['foto_perfil']['type'];
                $tamano = $_FILES['foto_perfil']['size'];
                $temp = $_FILES['foto_perfil']['tmp_name'];
                //Se comprueba si el archivo a cargar es correcto observando su extensión y tamaño
                 if (!((strpos($tipo, "gif") || strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "png")) && ($tamano < 200000000))) {
                    echo '<div><b>Error. La extensión o el tamaño de los archivos no es correcta.<br/>
                    - Se permiten archivos .gif, .jpg, .png. y de 200 kb como máximo.</b></div>';
                 }
                 else {
                    //Si la imagen es correcta en tamaño y tipo
                    //Se intenta subir al servidor
                    if (move_uploaded_file($temp, 'assets/img/'.$archivo)) {
                        //Cambiamos los permisos del archivo a 777 para poder modificarlo posteriormente
                        chmod('assets/img/'.$archivo, 0777);
                            
                        
                    }
                    else {
                       //Si no se ha podido subir la imagen, mostramos un mensaje de error
                       echo '<div><b>Ocurrió algún error al subir el fichero. No pudo guardarse.</b></div>';
                    }
                  }
           }else{
             $archivo = "";
           }

           $cliente->id = $_REQUEST['id'];
           $cliente->ruc = $_REQUEST['ruc'];
           $cliente->nombre = $_REQUEST['nombre'];
           $cliente->nick = $_REQUEST['nick'];
           $cliente->correo = $_REQUEST['correo'];
           $cliente->pass = $_REQUEST['pass'];
           $cliente->telefono = $_REQUEST['telefono']; 
           $cliente->cumple = $_REQUEST['cumple']; 
           $cliente->direccion = $_REQUEST['direccion']; 
           $cliente->fecha_registro = date("Y-m-d"); 
           $cliente->foto_perfil = $archivo; 
           $cliente->sucursal = $_REQUEST['sucursal'];
           $cliente->puntos = $_REQUEST['puntos'];
           $cliente->gastado = ($_REQUEST['cambiar']!='')? $_REQUEST['cambiar']:$_REQUEST['gastado'];
           $cliente->mayorista = $_REQUEST['mayorista'];
           $cliente->cliente = $_REQUEST['cliente'];
           $cliente->proveedor = $_REQUEST['proveedor'];
            
            $cliente->id > 0 
                ? $this->model->Actualizar($cliente)
                : $this->model->Registrar($cliente);

            $cliente->id > 0 
                ? $accion = "Modificado"
                : $accion = "Agregado";
            
            header('Location:' . getenv('HTTP_REFERER'));
        
    }
    
    public function Guardar(){
        $cliente = new cliente();

        //Subida de imágenes

        
           //Recogemos el archivo enviado por el formulario
           $archivo = $_FILES['foto_perfil']['name'];
           //Si el archivo contiene algo y es diferente de vacio
           if (isset($archivo) && $archivo != "") {
                //Obtenemos algunos datos necesarios sobre el archivo
                $tipo = $_FILES['foto_perfil']['type'];
                $tamano = $_FILES['foto_perfil']['size'];
                $temp = $_FILES['foto_perfil']['tmp_name'];
                //Se comprueba si el archivo a cargar es correcto observando su extensión y tamaño
                 if (!((strpos($tipo, "gif") || strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "png")) && ($tamano < 200000000))) {
                    echo '<div><b>Error. La extensión o el tamaño de los archivos no es correcta.<br/>
                    - Se permiten archivos .gif, .jpg, .png. y de 200 kb como máximo.</b></div>';
                 }
                 else {
                    //Si la imagen es correcta en tamaño y tipo
                    //Se intenta subir al servidor
                    if (move_uploaded_file($temp, 'assets/img/'.$archivo)) {
                        //Cambiamos los permisos del archivo a 777 para poder modificarlo posteriormente
                        chmod('assets/img/'.$archivo, 0777);
                            
                        
                    }
                    else {
                       //Si no se ha podido subir la imagen, mostramos un mensaje de error
                       echo '<div><b>Ocurrió algún error al subir el fichero. No pudo guardarse.</b></div>';
                    }
                  }
           }else{
             $archivo = "";
           }

           $cliente->id = $_REQUEST['id'];
           $cliente->ruc = $_REQUEST['ruc'];
           $cliente->nombre = $_REQUEST['nombre'];
           $cliente->nick = $_REQUEST['nick'];
           $cliente->correo = $_REQUEST['correo'];
           $cliente->pass = $_REQUEST['pass'];
           $cliente->telefono = $_REQUEST['telefono']; 
           $cliente->cumple = $_REQUEST['cumple']; 
           $cliente->direccion = $_REQUEST['direccion']; 
           $cliente->fecha_registro = date("Y-m-d"); 
           $cliente->foto_perfil = $archivo; 
           $cliente->sucursal = $_REQUEST['sucursal'];
           $cliente->puntos = $_REQUEST['puntos'];
           $cliente->gastado = ($_REQUEST['cambiar']!='')? $_REQUEST['cambiar']:$_REQUEST['gastado'];
           $cliente->mayorista = $_REQUEST['mayorista'];
           $cliente->cliente = $_REQUEST['cliente'];
           $cliente->proveedor = $_REQUEST['proveedor'];
            
            $cliente->id > 0 
                ? $this->model->Actualizar($cliente)
                : $this->model->Registrar($cliente);

            $cliente->id > 0 
                ? $accion = "Modificado"
                : $accion = "Agregado";
            
            header('Location:' . getenv('HTTP_REFERER'));
        
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
}
<?php
require_once 'model/producto.php';
require_once 'model/categoria.php';
require_once 'model/marca.php';
require_once 'model/imagen.php';
require_once 'model/sucursal.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';
class productoController{
    
    private $model;
    private $categoria;
    private $cierre;
    private $marca; 
    private $imagen;
    private $sucursal;
    private $cliente;
    
    public function __CONSTRUCT(){
        $this->model = new producto();
        $this->categoria = new categoria();
        $this->cierre = new cierre();
        $this->marca = new marca();
        $this->imagen = new imagen();
        $this->sucursal = new sucursal();
        $this->cliente = new cliente();

    }

    public function Index(){
        session_start();
        
        if($_SESSION['nivel']==1 || $_SESSION['nivel']==4){
        require_once 'view/header.php';
        require_once 'view/producto/producto.php';
        require_once 'view/footer.php';
        }else{
            echo '<h1><center>NO TIENES ACCESO</center></h1>';
        }
        
          
        // require_once 'view/header.php';
        // require_once 'view/producto/producto.php';
        // require_once 'view/footer.php';
        
       
    }

    public function Listar(){
        require_once 'view/producto/producto.php';
    }
    
    public function ListarAjax(){
     
        $producto = $this->model->ListarAjax();
        echo json_encode($producto, JSON_UNESCAPED_UNICODE);
     }
     
      public function ListarFiltros()
    {

        $marca = $_REQUEST["marca"];
      
        $producto = $this->model->ListarMarca($marca);
        echo json_encode($producto, JSON_UNESCAPED_UNICODE);
    }

    public function Crud(){
        $producto = new producto();
        
        if(isset($_REQUEST['id'])){
            $producto = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/producto/producto-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $producto = new producto();
        
        if(isset($_REQUEST['id'])){
            $producto = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/producto/producto-editar.php';
        
    }

    public function Balance(){
        require_once 'view/header.php';
        require_once 'view/informes/balance.php';
        require_once 'view/footer.php';
       
    }

    public function Buscar(){
        
        if(isset($_REQUEST['id'])){
            $producto = $this->model->Obtener($_REQUEST['id']);
        }
        echo json_encode($producto);
        
    }
    public function BuscarCodigo(){

        $gift = $this->model->BuscarCodigo($_REQUEST['codigo']);
      
    
      if ($gift->codigo == $_REQUEST["codigo"]) {
         
             echo "true";
         }
         if($gift->codigo != $_REQUEST["codigo"]){
 
             echo "false";
        
         //header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
         }
     }
    
    public function Guardar(){
        $producto = new producto();
        
        $producto->id = $_REQUEST['id'];
        $producto->codigo = $_REQUEST['codigo'];
        $producto->id_categoria = $_REQUEST['id_categoria'];
        $producto->producto = $_REQUEST['producto'];
        $producto->marca = $_REQUEST['marca'];
        $producto->descripcion = $_REQUEST['descripcion'];
        $producto->precio_costo = $_REQUEST['precio_costo'];
        $producto->ultimo_precio = $_REQUEST['ultimo_precio'];
        // $producto->precio_brasil = $_REQUEST['precio_brasil'];
        $producto->precio_minorista = $_REQUEST['precio_minorista'];
        $producto->precio_mayorista = $_REQUEST['precio_mayorista'];
        $producto->precio_intermedio = $_REQUEST['precio_intermedio'];
        $producto->apartir = $_REQUEST['apartir'];
        $producto->fardo = $_REQUEST['fardo'];
        $producto->preciob = $_REQUEST['precio_intermedio'];
        $producto->precio_promo = $_REQUEST['precio_promo'];
        $producto->desde = $_REQUEST['desde'];
        $producto->hasta = $_REQUEST['hasta'];
        $producto->stock_s1 = $_REQUEST['stock_s1'];
        $producto->stock_s2 = $_REQUEST['stock_s2'];
        $producto->stock = $_REQUEST['stock_s1'];
        $producto->stock_minimo = $_REQUEST['stock_minimo'];
        $producto->descuento_max = $_REQUEST['descuento_max'];
        $producto->importado = $_REQUEST['importado'];
        $producto->iva = $_REQUEST['iva'];
        $producto->sucursal = 1;
        $producto->sinfactura = $_REQUEST['sinfactura'];
        $producto->confactura = $_REQUEST['confactura'];

        $ultimo = $this->model->Ultimo();
        $ultimo_id = $ultimo->id +1;

        $imagen = new imagen();

        $imagen->id_producto = $ultimo_id;

        if (isset($_FILES["imagen"]) && count($_FILES["imagen"]["tmp_name"])>0 && $_FILES["imagen"]["name"][0]!=""){

            $id_pedido = $_POST['id'];
            $reporte = null;

            for($x=0; $x<count($_FILES["imagen"]["name"]); $x++){

                $file = $_FILES["imagen"];
                $nombre = $new_id = rand(1000, 1000000).$file["name"][$x];
                $tipo = $file["type"][$x];
                $ruta_provisional = $file["tmp_name"][$x];
                $size = $file["size"][$x];
                $dimensiones = getimagesize($ruta_provisional);
                $width = $dimensiones[0];
                $height = $dimensiones[1];
                $carpeta = "assets/img/";

                if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'image/gif'){
                    $reporte .= "<p style='color: red'>Error $nombre, el archivo no es una imagen.</p>";
                }elseif($size > 11024*11024){
                    $reporte .= "<p style='color: red'>Error $nombre, el tamaño máximo permitido es 1mb</p>";
                }else{
                    $src = $carpeta.$nombre;
                    //Caragamos imagenes al servidor
                    move_uploaded_file($ruta_provisional, $src);
                    
                    $imagen->imagen = $nombre;
                    $this->imagen->Registrar($imagen);
                }
            }
            echo $reporte;
        }
      

        $producto->id > 0 
            ? $this->model->Actualizar($producto)
            : $this->model->Registrar($producto);
            
        $producto->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
        
        header('Location:' . getenv('HTTP_REFERER'));
        //header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
}
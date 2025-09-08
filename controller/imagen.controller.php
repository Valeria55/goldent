<?php

require_once 'model/imagen.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';

class imagenController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new imagen();
        $this->venta_tmp = new venta_tmp();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/imagen/imagen.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/imagen/imagen.php';
    }
    
     public function ListarAjax(){
        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);
        
        
        $ingreso = $this->model->ListarAjax($desde,$hasta);
        
        echo json_encode($ingreso, JSON_UNESCAPED_UNICODE);
    }
    public function Crud(){
        $imagen = new imagen();
        
        if(isset($_REQUEST['id'])){
            $imagen = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/imagen/imagen-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $imagen = new imagen();
        
        if(isset($_REQUEST['id'])){
            $imagen = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/imagen/imagen-editar.php';
        
    }
    
    public function Guardar(){
        
        $imagen = new imagen();

        $imagen->id = $_REQUEST['id'];
        $imagen->id_producto = $_REQUEST['id_producto'];

        if (isset($_FILES["imagen"]) && count($_FILES["imagen"]["tmp_name"])>0 && $_FILES["imagen"]["name"][0]!=""){

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
                    $imagen->id > 0 
                    ? $this->model->Actualizar($imagen)
                    : $this->model->Registrar($imagen);
                }
            }
            echo $reporte;
        }



      

        $imagen->id > 0 
            ? $this->model->Actualizar($imagen)
            : $this->model->Registrar($imagen);
            
        $imagen->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
        
        header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c'].'&id_prod='.$_REQUEST['id_producto'].'&prod='.$_REQUEST['prod']);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c'].'&id_prod='.$_REQUEST['id_producto'].'&prod='.$_REQUEST['prod']);
    }
}
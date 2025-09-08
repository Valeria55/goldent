<?php
require_once 'model/transferencia.php';
require_once 'model/acreedor.php';
require_once 'model/compra.php';
require_once 'model/compra_tmp.php';
require_once 'model/cliente.php';
require_once 'model/producto.php';
require_once 'model/sucursal.php';
require_once 'model/cierre.php';

class transferenciaController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new transferencia();
        $this->acreedor = new acreedor();
        $this->compra = new compra();
        $this->cliente = new cliente();
        $this->compra_tmp = new compra_tmp();
        $this->producto = new producto();
        $this->sucursal = new sucursal();
        $this->cierre = new cierre();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/transferencia/transferencia.php';
        require_once 'view/footer.php';
       
    }

    public function Balance(){
        require_once 'view/header.php';
        require_once 'view/informes/balance.php';
        require_once 'view/footer.php';
       
    }

    public function Listar(){
        require_once 'view/egreso/egreso.php';
    }

    public function Varios(){
       require_once 'view/header.php';
       require_once 'view/transferencia/nueva-transferencia.php';
       require_once 'view/footer.php';
    }

    public function detalles(){
        require_once 'view/acreedor/pago_detalles.php';
    }
    
    public function Crud(){
        $egreso = new egreso();
        
        if(isset($_REQUEST['id'])){
            $egreso = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/egreso/egreso-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $transferencia = new transferencia();
        
        if(isset($_REQUEST['id'])){
            $transferencia = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/transferencia/transferencia-editar.php';
        
    }

    
    public function Guardar(){

        $producto = new producto();
        $producto = $this->producto->Obtener($_REQUEST['id_producto']);
        $transferencia = new transferencia();
        
        $transferencia->id = $_REQUEST['id'];
        $transferencia->usuario_emisor = $_REQUEST['usuario_emisor'];
        $transferencia->usuario_receptor = $_REQUEST['usuario_receptor'];
        $transferencia->local_emisor = $_REQUEST['local_emisor'];
        $transferencia->local_receptor = ($_REQUEST['tipo']=="transferencia")? $_REQUEST['sucursal']:$producto->sucursal;
        $transferencia->id_producto = $_REQUEST['id_producto'];
        $transferencia->cantidad = $_REQUEST['cantidad'];
        $transferencia->tipo = $_REQUEST['tipo'];
        $transferencia->fecha_solicitada = $_REQUEST['fecha_solicitada']; 
        $transferencia->fecha_aceptada = $_REQUEST['fecha_aceptada'];  
        $transferencia->observacion = $_REQUEST['observacion'];     
        $transferencia->estado = $_REQUEST['estado'];

        $transferencia->id > 0 
            ? $this->model->Actualizar($transferencia)
            : $this->model->Registrar($transferencia);
        
        //var_dump($transferencia);

        header('Location: index.php?c=transferencia');
    }

    public function GuardarVarios(){

        $producto = new producto();
        $producto = $this->producto->Obtener($_REQUEST['id_producto']);
        $transferencia = new transferencia();
        
        $transferencia->id = $_REQUEST['id'];
        $transferencia->usuario_emisor = $_REQUEST['usuario_emisor'];
        $transferencia->usuario_receptor = $_REQUEST['usuario_receptor'];
        $transferencia->local_emisor = $_REQUEST['local_emisor'];
        $transferencia->local_receptor = ($_REQUEST['tipo']=="transferencia")? $_REQUEST['sucursal']:$producto->sucursal;
        $transferencia->id_producto = $_REQUEST['id_producto'];
        $transferencia->cantidad = $_REQUEST['cantidad'];
        $transferencia->tipo = $_REQUEST['tipo'];
        $transferencia->fecha_solicitada = $_REQUEST['fecha_solicitada']; 
        $transferencia->fecha_aceptada = $_REQUEST['fecha_aceptada'];  
        $transferencia->observacion = $_REQUEST['observacion'];     
        $transferencia->estado = $_REQUEST['estado'];

        $transferencia->id > 0 
            ? $this->model->Actualizar($transferencia)
            : $this->model->Registrar($transferencia);
        
        //var_dump($transferencia);

        header('Location: index.php?c=transferencia&a=varios&tipo='.$_REQUEST["tipo"].'&suc='.$_REQUEST['sucursal']);
    }

    public function FinalizarCarga(){
        $this->model->FinalizarCarga($_REQUEST['id']);
        header('Location: index.php?c=transferencia');
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=egreso');
    }

    public function Aceptar(){
        if(isset($_SESSION["user_id"])){
            session_start();
        }
        $fecha_aceptada = date("Y-m-d");
        $transferencia = new transferencia();
        $transferencia = $this->model->Obtener($_REQUEST['id']);
        $producto = $this->producto->ObtenerLimpio($transferencia->id_producto);

        $this->producto->RestarId($producto->id, $transferencia->cantidad);

        if($transferencia->tipo=="solicitud"){
            $prodReceptor = $this->producto->ObtenerSucursal($producto->codigo, $transferencia->local_emisor);
            if($prodReceptor){
                $this->producto->SumarId($prodReceptor->id, $transferencia->cantidad);    
            }else{
                $producto->id=null;
                $producto->stock = $transferencia->cantidad;
                $producto->sucursal = $transferencia->local_emisor;
                $this->producto->Insertar($producto);
            }
            

        }else{
            $prodReceptor = $this->producto->ObtenerSucursal($producto->codigo, $transferencia->local_receptor);
            if($prodReceptor){
                $this->producto->SumarId($prodReceptor->id, $transferencia->cantidad);    
            }else{
                $producto->id=null;
                $producto->stock = $transferencia->cantidad;
                $producto->sucursal = $transferencia->local_receptor;
                $this->producto->Insertar($producto);
            }
        }        $this->model->Aceptar($_REQUEST['id'], $_REQUEST['receptor'], $fecha_aceptada);
        header('Location: index.php?c=transferencia');
    }

    public function Cancelar(){
        $this->model->Cancelar($_REQUEST['id'], $_REQUEST['estado']);
        header('Location: index.php?c=transferencia');
    }
    
    public function Borrar(){
        $this->model->Borrar($_REQUEST['id']);
        header('Location: index.php?c=transferencia&a=varios');
    }

    public function EliminarPago(){

        $egreso = new egreso();
        $egreso = $this->model->Obtener($_REQUEST['id']);

        $acreedor = new acreedor();
        $acreedor->id=$egreso->id_acreedor;
        $acreedor->monto=$egreso->monto;

        $this->model->Eliminar($_REQUEST['id']);
        $this->acreedor->SumarSaldo($acreedor);
        header('Location: index.php?c=egreso');
        
    }

}
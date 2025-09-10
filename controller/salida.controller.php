<?php
require_once 'model/salida.php';
require_once 'model/usuario.php';
require_once 'model/salida_tmp.php';
require_once 'model/producto.php';
require_once 'model/cierre.php';
require_once 'model/caja.php';
require_once 'model/cliente.php';


class salidaController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new salida();
        $this->salida = new salida();
        $this->usuario = new usuario();
        $this->salida_tmp = new salida_tmp();
        $this->producto = new producto();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->cliente = new cliente();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/salida/salida.php';
        require_once 'view/footer.php';
       
    }

    public function NuevaVenta(){
        require_once 'view/header.php';
        require_once 'view/venta/nueva-salida.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/salida/salida.php';
    }
    

    public function detalles(){
        require_once 'view/salida/salida_detalles.php';
    }
    
     public function ListarAjax(){
        $venta = $this->model->Listar(0);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }
    public function ListarFiltros(){
        
        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);
        
        $venta = $this->model->ListarFiltros($desde,$hasta);
        echo json_encode($venta, JSON_UNESCAPED_UNICODE);
    }
   
    public function Crud(){
        $venta = new venta();
        
        if(isset($_REQUEST['id'])){
            $venta = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/salida/salida-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $venta = new venta();
        
        if(isset($_REQUEST['id'])){
            $venta = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/salida/salida-editar.php';
        
    }
   
   public function Guardar(){

        $ven = new venta();
        $ven = $this->model->Ultimo();
        $sumaTotal = 0;


        foreach($this->salida_tmp->Listar() as $v){

            $venta = new venta();

            $venta->id = 0;
            $venta->id_venta = $ven->id_venta+1;
            $venta->id_cliente = $_REQUEST['id_cliente'];
            $venta->id_vendedor = $v->id_vendedor;
            $venta->id_producto = $v->id_producto;
            $venta->precio_costo = $v->precio_costo;
            $venta->precio_venta = $v->precio_venta;
            $venta->subtotal = $v->precio_venta*$v->cantidad;
            $venta->descuento = $v->descuento;
            $venta->iva = $_REQUEST['ivaval'];
            $venta->total = $venta->subtotal-($venta->subtotal*($venta->descuento/100));
            $venta->comprobante = $_REQUEST['comprobante'];
            $venta->nro_comprobante = $_REQUEST['nro_comprobante'];
            $venta->cantidad = $v->cantidad;
            $venta->margen_ganancia = ((($venta->precio_venta-($venta->precio_venta*($venta->descuento/100)))-$venta->precio_costo)/($venta->precio_costo))*100 ;
            $venta->fecha_venta = $_REQUEST["fecha_venta"];//date("Y-m-d H:i");
            $venta->fecha = $_REQUEST["fecha_venta"];//date("Y-m-d H:i");
            $venta->categoria = "Venta";
            $producto = $this->producto->Obtener($v->id_producto);
            $venta->concepto = $v->cantidad." Kg - ".$producto->producto; 
            $venta->monto = $venta->total;

            $venta->fecha_emision = $_REQUEST["fecha_venta"];//date("Y-m-d H:i");
            $venta->fecha_vencimiento = "2020-08-31";

            

            //Registrar venta
            $this->model->Registrar($venta);
            
            //Restar Stock
            $this->producto->Restar($venta);

            $sumaTotal+=$venta->total; 

        }
        
          $idd = $ven->id_venta+1;
          $this->salida_tmp->Vaciar();
         
                header('Location: index.php?c=salida_tmp');                 //header("refresh:0;index.php?c=venta&a=factura&id=$id");
             
       

    }
    public function Eliminar(){
        
        foreach($this->model->Listar($_REQUEST['id']) as $v){
            $venta = new venta();
            $venta->id_producto = $v->id_producto;
            $venta->cantidad = $v->cantidad;
            $this->producto->Sumar($venta);
        }
        $this->ingreso->EliminarVenta($_REQUEST['id']);
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=venta');
        
    }

    public function Anular(){
        
        foreach($this->model->Listar($_REQUEST['id']) as $v){
            $venta = new venta();
            $venta->id_producto = $v->id_producto;
            $venta->cantidad = $v->cantidad;
            $this->producto->Sumar($venta);
        }
        
        $this->ingreso->AnularVenta($_REQUEST['id']);
        $this->deuda->AnularVenta($_REQUEST['id']);
        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?c=venta');
    }
}
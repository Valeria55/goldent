<?php
require_once 'model/caja.php';
require_once 'model/sucursal.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';
require_once 'model/ingreso.php';
require_once 'model/egreso.php';


class cajaController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new caja();
        $this->caja = new caja();
        $this->sucursal = new sucursal();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
        $this->ingreso = new ingreso();
        $this->egreso = new egreso();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/caja/caja.php';
        require_once 'view/footer.php';
       
    }

    public function Movimientos(){
        require_once 'view/header.php';
        require_once 'view/caja/movimientos.php';
        require_once 'view/footer.php';
       
    }

    public function Listar(){
        require_once 'view/caja/caja.php';
    }


    
    public function Crud(){
        $caja = new caja();
        
        if(isset($_REQUEST['id'])){
            $caja = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/caja/caja-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $caja = new caja();
        
        if(isset($_REQUEST['id'])){
            $caja = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/caja/caja-editar.php';
        
    }
    
    public function Guardar(){
        $caja = new caja();
        session_start();
        $caja->id = $_REQUEST['id'];
        $caja->id_usuario = ($_REQUEST['id_usuario'])? $_REQUEST['id_usuario']:$_SESSION["user_id"];
        $caja->fecha = ($_REQUEST['fecha'])?$_REQUEST['fecha']:date("Y-m-d");
        $caja->caja = $_REQUEST['caja'];
        $caja->monto = $_REQUEST['monto']*$_REQUEST['movimiento'];
        $caja->comprobante = $_REQUEST['comprobante'];  
        $caja->anulado = 0;

        $caja->id > 0 
            ? $this->model->Actualizar($caja)
            : $this->model->Registrar($caja);
            
        $caja->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
        
        header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }

    public function Transferencia(){
        $egreso = new egreso();


        $receptor = $this->model->Obtener($_REQUEST['id_receptor']);
        
        $egreso->id_cliente = 0;
        session_start();
        $cierre = $this->cierre->Consultar($_SESSION['user_id']);
        $egreso->id_caja = $_REQUEST['id_emisor'];
        $egreso->id_venta = (isset($_REQUEST['id_venta']))? $_REQUEST['id_venta']:0;
        $egreso->fecha = date("Y-m-d H:i");
        $egreso->categoria = "Transferencia";
        $egreso->concepto = "Transferencia enviada a ".$receptor->caja;
        $egreso->comprobante = "";
        $egreso->monto = $_REQUEST['monto'];
        $egreso->forma_pago = "Efectivo";   
        $egreso->sucursal = 1;   
      

        $this->egreso->Registrar($egreso);

        $emisor = $this->model->Obtener($_REQUEST['id_emisor']);

        $ingreso = new ingreso();
        
        $ingreso->id_cliente = 0;
        $ingreso->id_caja = $_REQUEST['id_receptor'];
        $ingreso->id_usuario = $receptor->id_usuario;
        $ingreso->id_venta = (isset($_REQUEST['id_venta']))? $_REQUEST['id_venta']:0;
        $ingreso->fecha = date("Y-m-d H:i");
        $ingreso->categoria ="Transferencia";
        $ingreso->concepto = "Transferencia recibida de ".$emisor->caja;
        $ingreso->comprobante = "";
        $ingreso->monto = $_REQUEST['monto'];
        $ingreso->forma_pago = "Efectivo";   
        $ingreso->sucursal = 1;   
      

        $this->ingreso->Registrar($ingreso);

        $accion = "Transferencia exitósa"; 
        if($_SESSION['nivel']==1){
            header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
        }else{
            header('Location: index.php?success='.$accion.'&c=egreso&a=extraccion');
        }
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }

    public function Anular(){
        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?success=Anulado&c='.$_REQUEST['c']);
    }
}
<?php
require_once 'model/cierre.php';
require_once 'model/usuario.php';
require_once 'model/cliente.php';
require_once 'model/venta.php';
require_once 'model/compra.php';
require_once 'model/ingreso.php';
require_once 'model/egreso.php';

class cierreController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new cierre();
        $this->cierre = new cierre();
        $this->usuario = new usuario();
        $this->cliente = new cliente();
        $this->venta = new venta();
        $this->compra = new compra();
        $this->ingreso = new ingreso();
        $this->egreso = new egreso();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/cierre/cierre.php';
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

    public function CierrePDF(){
        
        require_once 'view/informes/cierrecajapdf.php';
        
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
        $egreso = new egreso();
        
        if(isset($_REQUEST['id'])){
            $egreso = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/egreso/egreso-editar.php';
        
    }

    
    public function Apertura(){

        $cierre = new cierre();
        
        session_start();
        $cierre->fecha_apertura = date("Y-m-d H:i");
        $cierre->fecha_cierre = null;
        $cierre->id_usuario = $_SESSION['user_id'];
        $cierre->monto_apertura = $_REQUEST['monto_apertura'];
        $cierre->monto_cierre = null;
        $cierre->cot_dolar = $_REQUEST['cot_dolar'];   
        $cierre->cot_real = $_REQUEST['cot_real'];   
      
        $this->model->Registrar($cierre);
        
        header('Location: index.php?c=venta_tmp');
    }

    public function Cierre(){

        $cierre = new cierre();
        
        session_start();
        $cierre->fecha_cierre = date("Y-m-d H:i");
        $cierre->monto_cierre = $_REQUEST['monto_cierre'];
        $cierre->id_usuario = $_SESSION['user_id'];

        $cierreV = new cierre();
        $cierreV = $this->model->Consultar($_SESSION['user_id']);

        $this->model->Cierre($cierre);

        session_destroy();
        
        header('Location: index.php?c=cierre&a=CierrePDF&id_cierre='.$cierreV->id);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=egreso');
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
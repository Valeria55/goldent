<?php
require_once 'model/gift_card.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';
require_once 'model/venta.php';
require_once 'model/ingreso.php';
require_once 'model/metodo.php';



class gift_cardController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new gift_card();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
        $this->venta = new venta();
        $this->ingreso = new ingreso();
        $this->metodo = new metodo();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/gift_card/gift_card.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/gift_card/gift_card.php';
    }
    
    public function Buscar(){
        
        if(isset($_REQUEST['id'])){
            $gift_card = $this->model->Obtener($_REQUEST['id']);
        }
        echo json_encode($gift_card);
        
    }
    public function Crud(){
        $gift_card = new gift_card();
        
        if(isset($_REQUEST['id'])){
            $gift_card = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/gift_card/gift_card-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $gift_card = new gift_card();
        
        if(isset($_REQUEST['id'])){
            $gift_card = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/gift_card/gift_card-editar.php';
        
    }
    
    public function Guardar(){
        $gift_card = new gift_card();
        
        session_start();
		$id_usuario = $_SESSION["user_id"];
		
        $gift_card->id = $_REQUEST['id'];
        $gift_card->id_funcionario = $id_usuario;
        $gift_card->id_cliente = $_REQUEST['id_cliente'];
        $gift_card->nro_tarjeta = $_REQUEST['nro_tarjeta'];
        $gift_card->monto = $_REQUEST['monto'];
        $gift_card->fecha = date('Y-m-d');
        $gift_card->forma_pago =$_REQUEST['forma_pago'];
        $gift_card->comprobante =$_REQUEST['comprobante'];

       $gift_card->id > 0 
            ? $this->model->Actualizar($gift_card)
            : $this->model->Registrar($gift_card);
            
        $gift_card->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
         
         $ingreso = new ingreso();
         
        if($_REQUEST['id']>0){
             $i=$this->ingreso->ObtenerIngreso($_REQUEST['id']);
            
          if($_REQUEST['forma_pago']=="Efectivo"){
            $ingreso->id_caja = 3;
        }else{
            $ingreso->id_caja = 2;
        }
        $ingreso->id = $i->id;
        $ingreso->id_cliente = $_REQUEST['id_cliente'];
        //$ingreso->id_venta = $ven->id_venta+1;
        $ingreso->fecha = $i->fecha;
        //$ingreso->vencimiento = date("Y-m-d H:i", strtotime("+$cuotas MONTH"));
        $ingreso->categoria = 'Gift Card';
        $ingreso->concepto = 'Venta de Gift Card';
        $ingreso->comprobante = $_REQUEST['comprobante'].' N° '.$_REQUEST['nro_comprobante'];
        $ingreso->forma_pago =$_REQUEST['forma_pago'];
        $ingreso->monto = $_REQUEST['monto'];
        $ingreso->id_gift = $i->id_gift;
       // $ingreso->saldo = $r->monto;
        $ingreso->sucursal = 0;
            
             $this->ingreso->Actualizar($ingreso);   
             
        }else{
         $g=$this->model->Ultimo();

          if($_REQUEST['forma_pago']=="Efectivo"){
            $ingreso->id_caja = 3;
        }else{
            $ingreso->id_caja = 2;
        }
            
        $ingreso->id_cliente = $_REQUEST['id_cliente'];
        //$ingreso->id_venta = $ven->id_venta+1;
        $ingreso->fecha = date("Y-m-d H:i");
        //$ingreso->vencimiento = date("Y-m-d H:i", strtotime("+$cuotas MONTH"));
        $ingreso->categoria = 'Gift Card';
        $ingreso->concepto = 'Venta de Gift Card';
        $ingreso->comprobante = $_REQUEST['comprobante'].' N° '.$_REQUEST['nro_comprobante'];
        $ingreso->forma_pago =$_REQUEST['forma_pago'];
        $ingreso->monto = $_REQUEST['monto'];
        $ingreso->id_gift = $g->id;
       // $ingreso->saldo = $r->monto;
        $ingreso->sucursal = 0;
            
             $this->ingreso->Registrar($ingreso);   
        }
      /*$gift_card->id > 0 
            ? $this->model->Actualizar($gift_card)
            : $this->model->Registrar($gift_card);
            
        $gift_card->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;*/


        
        header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }
    
    public function BuscarTarjeta(){

       $gift = $this->model->BuscarTarjeta($_REQUEST['numero']);
     
   
     if ($gift->nro_tarjeta == $_REQUEST["numero"]) {
        
            echo "true";
        }
        if($gift->nro_tarjeta != $_REQUEST["numero"]){

            echo "false";
       
        //header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
        }
    }
    
     public function Anular(){
        
        $this->model->Anular($_REQUEST['id']);
        $this->ingreso->AnularGift($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
}
<?php
require_once 'model/venta.php';
require_once 'model/venta_tmp.php';
require_once 'model/producto.php';
require_once 'model/ingreso.php';
require_once 'model/deuda.php';
require_once 'model/egreso.php';
require_once 'model/cliente.php';
require_once 'model/cierre.php';
require_once 'model/metodo.php';

class deudaController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new deuda();
        $this->venta_tmp = new venta_tmp();
        $this->cierre = new cierre();
        $this->producto = new producto();
        $this->ingreso = new ingreso();
        $this->venta = new venta();
        $this->egreso = new egreso();
        $this->cliente = new cliente();
        $this->metodo = new metodo();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/deuda/deuda.php';
        require_once 'view/footer.php';
       
    }



    public function Listar(){
        require_once 'view/deuda/deuda.php';
    }


    
    public function Crud(){
        $deuda = new deuda();
        
        if(isset($_REQUEST['id'])){
            $deuda = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/deuda/deuda-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $deuda = new deuda();
        
        if(isset($_REQUEST['id'])){
            $deuda = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/deuda/deuda-editar.php';
        
    }

    public function RangoForm(){
        $deuda = new deuda();
          $cli = $this->cliente->Obtener($_REQUEST['id']);
        require_once 'view/deuda/rango-form.php';
        
    }

    public function clientepdf(){
        $deuda = new deuda();
        $cli = $this->cliente->Obtener($_REQUEST['id']);
        require_once 'view/informes/extractoclientepdf.php';
        
    }

    public function CobrarModal(){
        $deuda = new deuda();
        
        if(isset($_REQUEST['id'])){
            $r = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/deuda/cobrar-form.php';
        
    }
    
    public function Guardar(){
        $deuda = new deuda();

        session_start();
        
        $deuda->id = $_REQUEST['id'];
        $deuda->id_cliente = $_REQUEST['id_cliente'];
        $deuda->id_venta = $_REQUEST['id_venta'];
        $deuda->fecha = $_REQUEST['fecha'];
        $deuda->vencimiento = $_REQUEST['vencimiento'];
        $deuda->concepto = $_REQUEST['concepto'];
        $deuda->monto = $_REQUEST['monto'];
        $deuda->moneda = 'GS';
        $deuda->cambio = 1;
        $deuda->saldo = $_REQUEST['saldo'];  
        $deuda->sucursal = $_SESSION['sucursal'];      

        $deuda->id > 0 
            ? $this->model->Actualizar($deuda)
            : $this->model->Registrar($deuda);
        
        header('Location: index.php?c=deuda');

    }


    public function Cobrar(){
       
            session_start();
            $ingreso = new ingreso();
            
            $ingreso->id_cliente  = $_REQUEST['id_cliente'];
            
            if($_REQUEST['forma_pago']=="Efectivo"){
                $ingreso->id_caja = 1;
            }else{
                $ingreso->id_caja = 2;
            }

        $ingreso->id_venta  = $_REQUEST['id_venta'];
        $ingreso->id_deuda  = $_REQUEST['id'];
        $ingreso->forma_pago  = $_REQUEST['forma_pago'];
        $ingreso->fecha = date("Y-m-d H:i");
        $ingreso->categoria = 'Cobro de deuda';
        $ingreso->concepto = "Cobro de deuda a ".$_REQUEST['cli'];
        $ingreso->comprobante  = $_REQUEST['comprobante'];
        $ingreso->sucursal = $_SESSION['sucursal'];
        $ingreso->monto  = $_REQUEST['mon'];
        $ingreso->moneda = $_REQUEST['moneda'];
        $ingreso->cambio = $_REQUEST['cambio'];


        $deuda = new deuda();

        $deuda->id = $_REQUEST['id'];
        $deuda->monto = $_REQUEST['mon'];
        $deuda->moneda = $_REQUEST['moneda'];
        $deuda->cambio = $_REQUEST['cambio'];
       
       
    //    Ver en que moneda está la deuda para hacer el cambio
        $monto  = $_REQUEST['mon']*$_REQUEST['cambio'];
        // $monto = round($monto, 2);
        // echo '<pre>'; var_ .                                                                                                                                                                                                                                                                                          dump($monto); echo '</pre>';
        // die();
        // $monto  = $_REQUEST['mon']/$_REQUEST['cambio'];
        // $monto = round($monto, 2);
        
        foreach($this->model->ObtenerDeudaCliente($_REQUEST['id_cliente']) as $v){
         
            if($monto <= 0) break;

            if($monto >= $v->saldo){

                $monto -= $v->saldo;
                $id_deuda = $v->id;
                $nuevo_saldo = 0;

                $this->model->Restar($nuevo_saldo, $id_deuda);
            }else{

                $id_deuda = $v->id;
                $nuevo_saldo = $v->saldo - $monto;
                $nuevo_saldo = round($nuevo_saldo, 2);
                $monto = 0;

                $this->model->Restar($nuevo_saldo, $id_deuda);
            }


        }

        
        $this->ingreso->Registrar($ingreso);
        
        // $ingresoID = $this->ingreso->UltimoID();
        //header('Location: index.php?c=ingreso&a=recibo&id='.$ingresoID->id);
        
        header('Location:' . getenv('HTTP_REFERER'));
        
    }

    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=deuda');
    }
}
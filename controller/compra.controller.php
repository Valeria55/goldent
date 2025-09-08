<?php
require_once 'model/compra.php';
require_once 'model/compra_tmp.php';
require_once 'model/producto.php';
require_once 'model/egreso.php';
require_once 'model/acreedor.php';
require_once 'model/egreso.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';
require_once 'model/presupuesto_compra.php';



class compraController{
    
    private $model;
    private $compra;
    private $cierre;
    private $compra_tmp;
    private $producto;
    private $egreso;
    private $acreedor;
    private $cliente;
    private $presupuesto_compra;

    public function __CONSTRUCT(){
        $this->model = new compra();
        $this->compra = new compra();
        $this->cierre = new cierre();
        $this->compra_tmp = new compra_tmp();
        $this->producto = new producto();
        $this->egreso = new egreso();
        $this->acreedor = new acreedor();
        $this->cliente = new cliente();
        $this->presupuesto_compra = new presupuesto_compra();

    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/compra/compra.php';
        require_once 'view/footer.php';
       
    }

    public function Nuevacompra(){
        require_once 'view/header.php';
        require_once 'view/compra/nueva-compra.php';
        require_once 'view/footer.php';
       
    }
    
    public function Editar(){
        require_once 'view/header.php';
        require_once 'view/compra/editar-compra.php';
        require_once 'view/footer.php';
       
    }

    public function Listar(){
        require_once 'view/compra/compra.php';
    }
    
    public function CompraDia(){
        require_once 'view/informes/compradiapdf.php';
    }
    
    public function CompraMes(){
        require_once 'view/informes/compramespdf.php';
    }

    public function detalles(){
        require_once 'view/compra/compra_detalles.php';
    }
    
    
    public function ListarDia(){
        
        require_once 'view/header.php';
        require_once 'view/compra/compradia.php';
        require_once 'view/footer.php';
    }

    public function Cambiar(){

        $compra = new compra();
        
        $id_item = $_REQUEST['id_item'];
        $id_compra = $_REQUEST['id_compra'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $cantidad_ant = $_REQUEST['cantidad_ant'];
        
        $cant = $cantidad_ant - $cantidad;

        if($cantidad>0){
            $compra = $this->model->Cantidad($id_item, $id_compra, $cantidad);
            
            if($compra->contado=='Cuota')
                $acreedor = $this->acreedor->EditarMonto($id_compra, $compra->total_compra);

            if($compra->contado=='Contado')
                $acreedor = $this->egreso->EditarMonto($id_compra, $compra->total_compra);

            
            $this->producto->Sumar($codigo, $cant);

        }
        
        echo json_encode($compra);
    }

    public function Cancelar(){
        
        $id_item = $_REQUEST['id_item'];
        $id_compra = $_REQUEST['id_compra'];
        $codigo = $_REQUEST['codigo'];
        $cantidad = $_REQUEST['cantidad_item'];


        $compra = $this->model->Cantidad($id_item, $id_compra, 0);
            
        if($compra->contado=='Cuota')
            $acreedor = $this->acreedor->EditarMonto($id_compra, $compra->total_compra);

        if($compra->contado=='Contado')
            $acreedor = $this->egreso->EditarMonto($id_compra, $compra->total_compra);

        $compra = $this->model->CancelarItem($id_item);
        $this->producto->Sumar($codigo, $cantidad);
        header('location: ?c=compra_tmp&a=editar&id='.$id_compra);
    }


    
    public function Crud(){
        $compra = new compra();
        
        if(isset($_REQUEST['id'])){
            $compra = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/compra/compra-editar.php';
        require_once 'view/footer.php';
    }

    public function Cierre(){
        $compra = new compra();
        
        if(isset($_REQUEST['fecha'])){
            $compra = $this->model->ListarDiaContado($_REQUEST['fecha']);
        }
        require_once 'view/informes/cierrepdf.php';
        
    }
    
    public function CierreMes(){
        $compra = new compra();
        
        if(isset($_REQUEST['fecha'])){
            $compra = $this->model->ListarMes($_REQUEST['fecha']);
        }
        require_once 'view/informes/cierremespdf.php';
        
    }
    public function Obtener(){
        $compra = new compra();
        
        if(isset($_REQUEST['id'])){
            $compra = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/compra/compra-editar.php';
        
    }
    
    public function AgregarItem(){
         

        $compra = new compra();
        
        $c = $this->model->Obtener($_REQUEST['id_compra']);
        
        $compra->id = 0;
        $compra->id_compra = $_REQUEST['id_compra'];
        $compra->id_cliente = $c->id_cliente;
        $compra->id_vendedor = $c->id_vendedor;
        $compra->id_producto = $_REQUEST['id_producto'];
        $compra->precio_compra = $_REQUEST['precio_compra'];
        $compra->precio_min = $_REQUEST['precio_min'];
        $compra->precio_may = $_REQUEST['precio_may'];
        $compra->subtotal = $_REQUEST['precio_compra']*$_REQUEST['cantidad'];
        $compra->descuento = 0;
        $compra->iva = 10;
        $compra->total = $compra->subtotal-(($compra->descuento));
        $compra->comprobante = $c->comprobante;
        $compra->nro_comprobante = $c->nro_comprobante;
        $compra->cantidad = $_REQUEST['cantidad'];
        $compra->margen_ganancia = 0; // costo nuevo
        $compra->fecha_compra = $c->fecha_compra;
        $compra->metodo = $c->metodo;
        $compra->contado = $c->contado;
        $compra->banco = $c->banco;
        $compra->moneda = $c->moneda;
        $compra->cambio = $c->cambio;
        $compra->facturable = $c->facturable;
        

        //Registrar compra
        $this->model->Registrar($compra);
        //Sumar Stock
        $this->producto->Compra($compra);
        
        $this->egreso->ActualizarCompra($_REQUEST['id_compra']);

        header('Location:' . getenv('HTTP_REFERER'));
        
    }
    
    public function EliminarItem(){
        
        $p = $this->model->ObtenerItem($_REQUEST['id']);
        
        $compra = new compra();
        $compra->id_producto = $p->id_producto;
        $compra->cantidad = $p->cantidad;
        $this->producto->Restar($compra);
            
        $this->model->CancelarItem($_REQUEST['id']);
        $this->egreso->ActualizarCompra($p->id_compra);
        
        header('Location:' . getenv('HTTP_REFERER'));
        
    }

    public function GuardarUno(){
         

        $compra = new compra();

        $costo = $_REQUEST['precio_costo']*$_REQUEST['cantidad'];
        $p_compra = $_REQUEST['precio_compra']*$_REQUEST['cantidad'];
        
        $compra->id = 0;
        $compra->id_compra = $_REQUEST['id_compra'];
        $compra->id_cliente = $_REQUEST['id_cliente'];
        $compra->id_vendedor = $_REQUEST['id_compra'];
        $compra->id_producto = $_REQUEST['id_producto'];
        $compra->id_res = 0;
        $compra->precio_costo = $_REQUEST['precio_costo'];
        $compra->precio_compra = $_REQUEST['precio_compra'];
        $compra->subtotal = $p_compra;
        $compra->descuento = 0;
        $compra->iva = 0;
        $compra->total = $p_compra;
        $compra->comprobante = $_REQUEST['comprobante'];
        $compra->nro_comprobante = $_REQUEST['nro_comprobante'];
        $compra->cantidad = $_REQUEST['cantidad'];
        $compra->margen_ganancia = round(((($p_compra - $costo)*100)/$costo),2);
        $compra->fecha_compra = $_REQUEST['fecha_compra'];
        $compra->metodo = $_REQUEST['metodo'];
        $compra->banco = $_REQUEST['banco'];
        $compra->contado = $_REQUEST['contado'];


        $this->producto->Sumar($compra);
        

        $compra->id > 0 
            ? $this->model->Actualizar($compra)
            : $this->model->Registrar($compra);



        header('Location: index.php?c=compra_tmp&a=editar&id='.$compra->id_compra);
    }
    
    public function Guardar(){

        $com = new compra();
        $com = $this->model->Ultimo();
        $sumaTotal = 0;
        $cant = 0;


        foreach($this->compra_tmp->Listar() as $c){

            $cant = $cant + $c->cantidad;
            // var_dump($c->id_producto);
            
            $compra = new compra();

            $compra->id = 0;
            $compra->id_compra = $com->id_compra+1;
            $compra->id_cliente = $_REQUEST['id_cliente'];
            $compra->id_vendedor = $c->id_vendedor;
            $compra->id_producto = $c->id_producto;
            $compra->precio_compra = $c->precio_compra;
            
            $compra->precio_min = $c->precio_min;
            $compra->precio_may = $c->precio_may;
            $compra->precio_intermedio = $c->precio_intermedio;
            
            $compra->subtotal = $c->precio_compra*$c->cantidad;
            $compra->descuento = 0;
            $compra->iva = $_REQUEST['ivaval'];
            $compra->total = $compra->subtotal-($compra->descuento);
            $compra->comprobante = $_REQUEST['comprobante'];
           

            $compra->nro_comprobante = $_REQUEST['nro_comprobante'];
            $compra->cantidad = $c->cantidad;
            $compra->margen_ganancia = 0; // costo nuevo
            $compra->fecha_compra = $_REQUEST['fecha_compra'];
            $compra->metodo = $_REQUEST['pago'];
            $compra->moneda =  $_REQUEST['monedas'];
            $compra->cambio =  $_REQUEST['cambios'];
            $compra->contado = $_REQUEST['contado'];
            $compra->banco = $_REQUEST['banco'];
            $compra->facturable = $_REQUEST['facturable'];
            $compra->fecha = date("Y-m-d H:i");
            $compra->categoria = "compra";
            $producto = $this->producto->Codigo($c->id_producto);
            // $compra->concepto = $c->cantidad." Kg - ".$producto->producto; 
            $compra->monto = $compra->total;
            $compra->cajas =  $_REQUEST['cajas'];

            $compra->fecha_emision = date("Y-m-d H:i");
            $compra->fecha_vencimiento = "2020-08-31";
            $compra->id_presupuesto = $c->id_presupuesto; //para guardar el id del presupuesto de compra

           // $precio_costo = (($precio_anterior * $cantidad_disponible) + ($precio_compra * $cantidad_compra))/($cantidad_disponible+$cantidad_compra);
            
        //    var_dump($compra->id_presupuesto);
        //    die();

            //Registrar compra
            $this->model->Registrar($compra);

            $this->presupuesto_compra->CambiarEstado($compra);

            //precios de venta
            $this->producto->Compra($compra);
            
            if($_REQUEST['facturable']=='sin factura'){
                $this->producto->SumarSinfactura($compra); //Sumar Stock sin fcatura
            }else{
                $compra->prod_factura = $c->id_producto;
                $compra->can_factura = $c->cantidad;
                $this->producto->SumarConfactura($compra);//Sumar Stock Con fcatura
            }
           

            $sumaTotal+=$compra->total; 
            // var_dump($compra);
            
        }

        
        $gasto = round((($_REQUEST ['monto_gasto'])* $_REQUEST ['cambio']),2);
        $div = round(($gasto/$cant), 2);
        

        foreach($this->compra_tmp->Listar() as $c){
            $id_producto = $c->id_producto;
            $old_precio = $this->producto->ListarProd($id_producto);
            
            $costo_nuevo = round(($c->precio_compra*$_REQUEST['cambios']), 2);
           
            $gasto_extra=$div;
            
            // $new_precio = $gasto_extra + $costo_nuevo;
            $a = (($costo_nuevo+$gasto_extra)*$c->cantidad) + ($old_precio->precio_costo*$old_precio->stock_s1);
            
            $pre_promed = round((($a)/($c->cantidad+$old_precio->stock_s1)), 2);
            $new_precio =   $pre_promed;
            
            $cantidad=intval($c->cantidad);
            
           // var_dump($new_precio,$a,$id_producto, $pre_promed, $cantidad);
            
            // var_dump('ID producto '.$id_producto);
            // var_dump('Gasto '.$gasto);
            // var_dump('Gasto dividido '.$div);
            // var_dump('Precio nuevo ingresado '.$c->precio_compra);
            // var_dump('Precio nuevo precio + gastos '.$new_precio);
            // var_dump('Precio anterior '.$old_precio->precio_costo);
            // var_dump('suma '.$a);
            // var_dump('Nuevo precio '.$pre_promed);

                //Actualizar precios 
           // $this->model->ActualizarPrecio($compra->id_compra, $id_producto, $new_precio);
            //$this->producto->ActualizarPrecio($cantidad, $id_producto ); 
            $producto = new producto();
            $this->producto->ActualizarCosto($cantidad, $id_producto, $new_precio);

        }

            
            $egreso = new egreso();

            if($_REQUEST['monto_gasto']>0){

                if($compra->metodo == "Efectivo" && $_REQUEST['caja']=1){
                    $egreso->id_caja = 1;
                }else{
                    $egreso->id_caja = 2;
                }

                $egreso->id_cliente = $_REQUEST['id_cliente'];
                $egreso->id_compra = $com->id_compra+1;
                $egreso->fecha = $_REQUEST['fecha_compra'];
                $egreso->categoria = 'Gastos por compra';
                $egreso->concepto = $_REQUEST['otro_gasto'];
                $egreso->forma_pago = $_REQUEST['pago_gasto'];
                $egreso->monto = $_REQUEST['monto_gasto'];
                $egreso->sucursal = 1;
                $egreso->id_usuario = $_REQUEST['id_usuario'];
                $egreso->moneda = $_REQUEST['moneda'];
                $egreso->cambio = $_REQUEST['cambio'];
                $egreso->tipo_egreso = 'GASTO VARIABLE';
                $this->egreso->Registrar($egreso);
            }

            if($_REQUEST['contado']=="Credito"){

                // $this->acreedor->Registrar($egreso);

                if($_REQUEST['entrega']>0){
                    
                    if($compra->metodo == "Efectivo" && $_REQUEST['caja']=1){
                        $egreso->id_caja = 1;
                    }else{
                        $egreso->id_caja = 2;
                    }

                    $egreso->id_cliente = $_REQUEST['id_cliente'];
                    $egreso->id_compra = $com->id_compra+1;
                    $egreso->categoria = 'entrega';
                    $egreso->concepto = 'compra cobro parcial';
                    $egreso->comprobante = $_REQUEST['comprobante'];
                    $egreso->fecha =$_REQUEST['fecha_compra'];
                    $egreso->nro_comprobante = $_REQUEST['nro_comprobante'];
                    $egreso->monto = $_REQUEST['entrega'];
                    $egreso->sucursal = 1;
                    $egreso->forma_pago = $_REQUEST['pago'];
                    $egreso->moneda =  $_REQUEST['moneda'];
                    $egreso->cambio = $_REQUEST['cambio'];
                    $egreso->tipo_egreso = 'COMPRA';
                    $egreso->id_usuario = $_SESSION['user_id'];

                    $this->egreso->Registrar($egreso);
                }

                session_start();
                $acreedor = new acreedor();

                $acreedor->id_cliente = $_REQUEST['id_cliente'];
                $acreedor->id_compra = $com->id_compra+1;
                $acreedor->fecha = date("Y-m-d",strtotime($_REQUEST['fecha_compra']));
                $acreedor->concepto = "compra a crédito";
                $acreedor->monto = $sumaTotal;
                $acreedor->saldo = $sumaTotal-$_REQUEST['entrega']; 
                $acreedor->moneda =  $_REQUEST['moneda']; 
                $acreedor->cambio = $_REQUEST['cambio'];
                // var_dump($acreedor->monto ); 
                $acreedor->sucursal = $_SESSION['sucursal']; 

                $this->acreedor->Registrar($acreedor);

            }else{
                session_start();
                $cierre = $this->cierre->Consultar($_SESSION['user_id']);
                if($compra->metodo == "Efectivo" && $_REQUEST['caja']=1){
                    $egreso->id_caja = 1;
                }else{
                    $egreso->id_caja = 2;
                }
                $egreso->id_cliente = $_REQUEST['id_cliente'];
                $egreso->id_compra = $com->id_compra+1;
                $egreso->fecha = $_REQUEST['fecha_compra'];
                $egreso->categoria = 'compra';
                $egreso->concepto = 'compra al contado';
                $egreso->comprobante = $_REQUEST['comprobante'];
                $egreso->nro_comprobante = $_REQUEST['nro_comprobante'];
                $egreso->forma_pago = $_REQUEST['pago'];
                $egreso->monto =  floatval(str_replace(".","",$_REQUEST['monto']));
                $egreso->saldo = floatval(str_replace(".","",$_REQUEST['monto'])) - $_REQUEST['entrega'];
                $egreso->sucursal = 1;
                $egreso->plazo = $_REQUEST['plazo'];
                $egreso->nro_cheque = $_REQUEST['nro_cheque'];
                $egreso->moneda =  $_REQUEST['monedas'];
                $egreso->cambio =  $_REQUEST['cambios'];
                $egreso->id_usuario = $_SESSION['user_id'];

                //$this->egreso->Registrar($egreso);
            }
            
            $this->compra_tmp->Vaciar();
            
            /*
            if($_REQUEST['comprobante'] == "Ticket" ){
            header("refresh:0;index.php?c=factura&a=recibo&id=$id");
            }else{
            header("refresh:0;index.php?c=factura&a=pdf&id=$id");
            }*/
            header('Location: index.php?c=compra&a=listardia');
    }
    
    public function Eliminar(){
        
        foreach($this->model->Listar($_REQUEST['id']) as $v){
            $compra = new compra();
            $compra->id_producto = $v->id_producto;
            $compra->cantidad = $v->cantidad;
            $this->producto->Restar($compra);
        }
        $this->egreso->Eliminarcompra($_REQUEST['id']);
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=compra');
    }

    public function Anular(){
        
        foreach($this->model->Listar($_REQUEST['id']) as $v){
            $compra = new compra();
            $compra->id_producto = $v->id_producto;
            $compra->cantidad = $v->cantidad;
            $this->producto->Restar($compra);
        }
        $this->egreso->Anularcompra($_REQUEST['id']);
        $this->acreedor->Anularcompra($_REQUEST['id']);
        $this->model->Anular($_REQUEST['id']);
        $id_presupuesto = $v->id_presupuesto;
     
        // Volver como presupuesto compra
        if ($v->id_presupuesto =! 0) {
            $this->presupuesto_compra->VolverEstado($id_presupuesto);
        }
     
        header('Location: index.php?c=compra');
    }
}
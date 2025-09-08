<?php
require_once 'model/presupuesto_compra.php';
require_once 'model/presupuesto_compra_tmp.php';
require_once 'model/compra.php';
require_once 'model/compra_tmp.php';
require_once 'model/producto.php';
require_once 'model/egreso.php';
require_once 'model/acreedor.php';
require_once 'model/egreso.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';


class presupuesto_compraController{

    private $model;

    public function __CONSTRUCT(){
        $this->model = new presupuesto_compra();
        $this->presupuesto_compra = new presupuesto_compra();
        $this->presupuesto_compra_tmp = new presupuesto_compra_tmp();
        $this->compra = new compra();
        $this->compra_tmp = new compra_tmp();
        $this->cierre = new cierre();
        $this->producto = new producto();
        $this->egreso = new egreso();
        $this->acreedor = new acreedor();
        $this->egreso = new egreso();
        $this->cliente = new cliente();
    }

    public function Index(){
        require_once 'view/header.php';
        // require_once 'view/presupuesto_compra/presupuesto-compra.php';
        require_once 'view/presupuesto_compra/presupuestos.php';
        require_once 'view/footer.php';

    }

    public function Editar(){

        $presupuesto_compra = new presupuesto_compra();
        
        if(isset($_REQUEST['id_presupuesto'])){
            $presupuesto_compra = $this->presupuesto_compra->ObtenerUno($_REQUEST['id_presupuesto']);
        }
 
        require_once 'view/header.php';
        require_once 'view/presupuesto_compra/editar-presupuesto.php';
        require_once 'view/footer.php';

    }

    public function ObtenerUno(){

        $presupuesto_compra = new presupuesto_compra();
        
        if(isset($_REQUEST['id_presupuesto'])){
            $presupuesto_compra = $this->presupuesto_compra->ObtenerUno($_REQUEST['id_presupuesto']);
        }

    }



    public function Listar(){
        require_once 'view/header.php';
        require_once 'view/presupuesto_compra/presupuestos.php';
        require_once 'view/footer.php';
    }

    public function ListarAjax(){
        $presupuesto_compra = $this->model->Listarr();
        echo json_encode($presupuesto_compra, JSON_UNESCAPED_UNICODE);
    }

    public function ListarFiltros(){
        
        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);
        
        $presupuesto_compra = $this->model->ListarFiltros($desde,$hasta);
        echo json_encode($presupuesto_compra, JSON_UNESCAPED_UNICODE);
    }



    public function detalles(){
        require_once 'view/presupuesto_compra/compra_detalles.php';
    }

    public function presupuestos(){
        require_once 'view/header.php';
        require_once 'view/presupuesto_compra/presupuesto-compra.php';
        require_once 'view/footer.php';
    }

    public function FinalizarPresupuesto(){
        require_once 'view/presupuesto_compra/compra_detalles.php';
    }


    public function Cambiar(){

        $presupuesto_compra = new presupuesto_compra();

        $id_item = $_REQUEST['id_item'];
        $id_presupuesto = $_REQUEST['id_presupuesto'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $cantidad_ant = $_REQUEST['cantidad_ant'];

        // $cant = $cantidad_ant - $cantidad;

        if($cantidad>0){
            $presupuesto_compra = $this->model->Cantidad($id_item, $id_presupuesto, $cantidad);
            // $this->producto->Sumar($codigo, $cant);
        }

        echo json_encode($presupuesto_compra);
    }

    public function CambiarTotal(){

        $presupuesto_compra = new presupuesto_compra();

        $id_item = $_REQUEST['id_item'];
        $id_presupuesto = $_REQUEST['id_presupuesto'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $precio_compra = $_REQUEST['precio_compra'];

        $presupuesto_compra = $this->model->PrecioCosto($id_item, $id_presupuesto, $precio_compra);

        echo json_encode($presupuesto_compra);
    }

    public function Cancelar(){

        $id_item = $_REQUEST['id_item'];
        $id_compra = $_REQUEST['id_compra'];
        $codigo = $_REQUEST['codigo'];
        $cantidad = $_REQUEST['cantidad_item'];


        $presupuesto_compra = $this->model->Cantidad($id_item, $id_compra, 0);

        if($presupuesto_compra->contado=='Cuota')
            $acreedor = $this->acreedor->EditarMonto($id_compra, $presupuesto_compra->total_compra);

        if($presupuesto_compra->contado=='Contado')
            $acreedor = $this->egreso->EditarMonto($id_compra, $presupuesto_compra->total_compra);

        $presupuesto_compra = $this->model->CancelarItem($id_item);
        $this->producto->Sumar($codigo, $cantidad);
        header('location: ?c=presupuesto_compra_tmp&a=editar&id='.$id_compra);
    }



    public function Crud(){
        $presupuesto_compra = new presupuesto_compra();

        if(isset($_REQUEST['id'])){
            $presupuesto_compra = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/compra/compra-editar.php';
        require_once 'view/footer.php';
    }


    public function Obtener(){
        $presupuesto_compra = new presupuesto_compra();

        if(isset($_REQUEST['id'])){
            $presupuesto_compra = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/presupuesto_compra/compra-editar.php';

    }

    public function GuardarUno(){


        $presupuesto_compra = new presupuesto_compra();

        $c = $this->model->Obtener($_REQUEST['id_presupuesto']);

        $presupuesto_compra->id = 0;
        $presupuesto_compra->id_presupuesto = $_REQUEST['id_presupuesto'];
        $presupuesto_compra->id_cliente = $c->id_cliente;
        $presupuesto_compra->id_vendedor = $c->id_vendedor;
        $presupuesto_compra->id_producto = $_REQUEST['id_producto'];
        $presupuesto_compra->precio_compra = $_REQUEST['precio_compra'];
        $presupuesto_compra->precio_min = $_REQUEST['precio_min'];
        $presupuesto_compra->precio_may = $_REQUEST['precio_may'];
        $presupuesto_compra->precio_intermedio =  $_REQUEST['precio_intermedio'];
        $presupuesto_compra->subtotal = $_REQUEST['precio_compra']*$_REQUEST['cantidad'];
        $presupuesto_compra->descuento = 0;
        $presupuesto_compra->iva = 10;
        $presupuesto_compra->total = $presupuesto_compra->subtotal-(($presupuesto_compra->descuento));
        $presupuesto_compra->comprobante = $c->comprobante;
        $presupuesto_compra->nro_comprobante = $c->nro_comprobante;
        $presupuesto_compra->cantidad = $_REQUEST['cantidad'];
        $presupuesto_compra->margen_ganancia = 0; // costo nuevo
        $presupuesto_compra->fecha_compra = $c->fecha_compra;
        $presupuesto_compra->metodo = $c->metodo;
        $presupuesto_compra->moneda = $c->moneda;
        // $presupuesto_compra->facturable = $c->facturable;

        if($this->model->ObtenerPorProductoUsuario($presupuesto_compra)){
            $dupl = '&dupl=1';
        }else{
            $dupl = '';
                $this->model->Registrar($presupuesto_compra);
        }

        header('Location: index.php?c=presupuesto_compra&a=editar&id_presupuesto='.$presupuesto_compra->id_presupuesto.$dupl);

        // $this->model->Registrar($presupuesto_compra);

    }

    public function EliminarItem(){

        $p = $this->model->ObtenerItem($_REQUEST['id']);

        $presupuesto_compra = new presupuesto_compra();
        $presupuesto_compra->id_producto = $p->id_producto;
        $presupuesto_compra->cantidad = $p->cantidad;
  
     
        $this->model->CancelarItem($_REQUEST['id']);

        // header('Location: index.php?c=presupuesto&a=editar&id='.$id_presupuesto);

        header('Location:' . getenv('HTTP_REFERER'));

    }

    
    public function Compra(){

        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        session_start();
        $this->compra_tmp->Vaciar(); 

        var_dump($_REQUEST['id_presupuesto']);

        foreach($this->presupuesto_compra->ListarDetalle($_REQUEST['id_presupuesto']) as $p){
    
        $compra_tmp = new compra_tmp();
        $compra_tmp->id = 0;
        $compra_tmp->id_compra = 1;
        $compra_tmp->id_vendedor = $_SESSION['user_id'];
        $compra_tmp->id_producto = $p->id_producto;
        $compra_tmp->id_presupuesto = $p->id_presupuesto;

        $compra_tmp->id_presupuesto = $_REQUEST['id_presupuesto'];
        // var_dump($compra_tmp->id_presupuesto);
        // die();
        $compra_tmp->precio_compra = $p->precio_compra;
        $compra_tmp->precio_min = $p->precio_min;
        $compra_tmp->precio_may = $p->precio_may;
        // $compra_tmp->precio_brasil = $p->precio_br;
        $compra_tmp->precio_intermedio = $p->precio_inter;

                
        // var_dump($compra_tmp->precio_mayorista);
        // var_dump($compra_tmp->precio_turista);
        //  die();

        $compra_tmp->cantidad = $p->cantidad;
        $compra_tmp->fecha_compra = $p->fecha_compra;
          
             $this->compra_tmp->Registrar($compra_tmp); 
    
        }
        
        header('Location: index.php?c=compra_tmp');

    }


       public function Guardar(){

        $pr = $this->model->Ultimo();
        $sumaTotal = 0;

        foreach($this->presupuesto_compra_tmp->Listar() as $p){

            $presupuesto_compra = new presupuesto_compra();

            $presupuesto_compra->id = 0;
            $presupuesto_compra->id_presupuesto = $pr->id_presupuesto+1;
            $presupuesto_compra->id_cliente = $_REQUEST["id_cliente"];
            $presupuesto_compra->id_vendedor = $p->id_vendedor;
            $presupuesto_compra->descuento = 0;
            $presupuesto_compra->id_producto = $p->id_producto;
            $presupuesto_compra->precio_compra = $p->precio_compra;
            $presupuesto_compra->precio_min = $p->precio_min;
            $presupuesto_compra->precio_may = $p->precio_may;
            $presupuesto_compra->precio_intermedio = $p->precio_intermedio;
            $presupuesto_compra->subtotal = $p->precio_compra*$p->cantidad;
            $presupuesto_compra->descuento = 0;
            $presupuesto_compra->cantidad = $p->cantidad;
            $presupuesto_compra->fecha_compra = $_REQUEST["fecha_presupuesto"];
            $presupuesto_compra->total = $presupuesto_compra->subtotal-($presupuesto_compra->descuento);
            $presupuesto_compra->comprobante = $_REQUEST["comprobante"];
            $presupuesto_compra->nro_comprobante = $_REQUEST["nro_comprobante"];
            //Registrar presupuesto de compra
            $this->model->Registrar($presupuesto_compra);

            $sumaTotal+=$presupuesto_compra->total;
        }
        $this->presupuesto_compra_tmp->Vaciar();
        // $id = $presupuesto_compra->id_presupuesto+1;

        header('Location: index.php?c=presupuesto_compra&a=listar');
}


    public function Anular(){

        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?c=presupuesto_compra');
    }
}
<?php
require_once 'model/factura.php';
require_once 'model/usuario.php';
require_once 'model/factura_tmp.php';
require_once 'model/venta_tmp.php';
require_once 'model/venta.php';
require_once 'model/producto.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';



class facturaController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new factura();
        $this->factura = new factura();
        $this->usuario = new usuario();
        $this->factura_tmp = new factura_tmp();
        $this->venta_tmp = new venta_tmp();
        $this->venta = new venta();
        $this->producto = new producto();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/factura/factura.php';
        require_once 'view/footer.php';
    }

    public function Sesion()
    {
        require_once 'view/header.php';
        require_once 'view/factura/factura-sesion.php';
        require_once 'view/footer.php';
    }

    public function Nuevofactura()
    {
        require_once 'view/header.php';
        require_once 'view/factura/nuevo-factura.php';
        require_once 'view/footer.php';
    }
    public function Listar()
    {
        require_once 'view/factura/factura.php';
    }

    public function facturapdf()
    {
        require_once 'view/informes/facturapdf.php';
    }

    public function EstadoResultado()
    {
        require_once 'view/header.php';
        require_once 'view/informes/estado_resultado.php';
        require_once 'view/footer.php';
    }

    public function ListarCliente()
    {
        require_once 'view/header.php';
        require_once 'view/factura/facturacliente.php';
        require_once 'view/footer.php';
    }

    public function ListarUsuario()
    {
        require_once 'view/header.php';
        require_once 'view/factura/facturausuario.php';
        require_once 'view/footer.php';
    }

    public function ListarProducto()
    {
        require_once 'view/header.php';
        require_once 'view/factura/facturaproducto.php';
        require_once 'view/footer.php';
    }

    public function ListarProductoCat()
    {
        require_once 'view/header.php';
        require_once 'view/factura/facturaproductocat.php';
        require_once 'view/footer.php';
    }

    public function detalles()
    {
        require_once 'view/factura/factura_detalles.php';
    }


    public function ListarDia()
    {

        require_once 'view/header.php';
        require_once 'view/factura/facturadia.php';
        require_once 'view/footer.php';
    }

    public function ListarAjax()
    {
        $factura = $this->model->Listar();
        echo json_encode($factura, JSON_UNESCAPED_UNICODE);
    }

    public function ListarFiltros()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $factura = $this->model->ListarFiltros($desde, $hasta);
        echo json_encode($factura, JSON_UNESCAPED_UNICODE);
    }


    public function Cambiar()
    {

        $factura = new factura();

        $id_item = $_REQUEST['id_item'];
        $id_factura = $_REQUEST['id_factura'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $cantidad_ant = $_REQUEST['cantidad_ant'];

        $cant = $cantidad_ant - $cantidad;


        echo json_encode($factura);
    }

    public function Cancelar()
    {

        $id_item = $_REQUEST['id_item'];
        $id_factura = $_REQUEST['id_factura'];
        $codigo = $_REQUEST['codigo'];
        $cantidad = $_REQUEST['cantidad_item'];


        $factura = $this->model->Cantidad($id_item, $id_factura, 0);


        $factura = $this->model->CancelarItem($id_item);

        header('location: ?c=factura_tmp&a=editar&id=' . $id_factura);
    }



    public function Crud()
    {
        $factura = new factura();

        if (isset($_REQUEST['id'])) {
            $factura = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/factura/factura-editar.php';
        require_once 'view/footer.php';
    }

    public function Cierre()
    {

        require_once 'view/informes/cierrepdf.php';
    }

    public function InformeDiario()
    {

        require_once 'view/informes/facturadiapdf.php';
    }

    public function InformeRango()
    {

        require_once 'view/informes/facturarangopdf.php';
    }

    public function InformeUsados()
    {

        require_once 'view/informes/productosusadospdf.php';
    }

    public function CierreMes()
    {

        require_once 'view/informes/cierremesnewpdf.php';
    }

    // public function Factura()
    // {

    //     require_once 'view/informes/facturapdf.php';
    // }

    public function Facturacion()
    {

        require_once 'view/informes/factura.php';
    }

    public function Ticket()
    {

        require_once 'view/informes/ticketpdf.php';
    }

    public function Obtener()
    {
        $factura = new factura();

        if (isset($_REQUEST['id'])) {
            $factura = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/factura/factura-editar.php';
    }

    public function ObtenerProducto()
    {
        $factura = new factura();

        $factura = $this->model->ObtenerProducto($_REQUEST['id_factura'], $_REQUEST['id_producto']);

        echo json_encode($factura);
    }

    public function GuardarUno()
    {


        $factura = new factura();

        $p_factura = $_REQUEST['precio_factura'] * $_REQUEST['cantidad'];

        $factura->id = 0;
        $factura->id_factura = $_REQUEST['id_factura'];
        $factura->id_cliente = $_REQUEST['id_cliente'];
        $factura->id_vendedor = $_REQUEST['id_factura'];
        $factura->id_producto = $_REQUEST['id_producto'];
        $factura->precio_venta = $_REQUEST['precio_venta'];
        $factura->descuento = 0;
        $factura->cantidad = $_REQUEST['cantidad'];
        $factura->fecha_factura = $_REQUEST['fecha_factura'];

        $factura->id > 0
            ? $this->model->Actualizar($factura)
            : $this->model->Registrar($factura);
        header('Location: index.php?c=factura_tmp&a=editar&id=' . $factura->id_factura);
    }


    public function Guardar()
    {

        $cierre = $this->cierre->UltimaCotCajero();
        // echo '<pre>'; var_dump($cierre->cot_dolar); echo '</pre>';
        // die();
        $ven = new factura();
        $ven = $this->model->Ultimo();
        $sumaTotal = 0;


        foreach ($this->factura_tmp->Listar() as $v) {

            $factura = new factura();

            $factura->id = 0;
            $factura->id_factura = $ven->id_factura + 1;
            $factura->id_cliente = $_REQUEST['id_cliente'];
            $factura->id_vendedor = $v->id_vendedor;
            $factura->id_producto = $v->id_producto;
            $factura->precio_venta = $v->precio_venta;
            $factura->descuento = $v->descuento;
            $factura->cantidad = $v->cantidad;
            $factura->fecha_factura = $_REQUEST["fecha_factura"]; //date("Y-m-d H:i");
            // $factura->estado = 0;
            $factura->cot_dolar = $cierre->cot_dolar;
            $factura->nro_comprobante = $_REQUEST["nro_comprobante"]; 
            // var_dump( $factura->nro_comprobante);
            // die();
            
            //Registrar factura
            $this->model->Registrar($factura);
            $this->producto->RestarFactura($factura);
            $sumaTotal += ($v->precio_venta * $v->cantidad);
        }


        $this->factura_tmp->Vaciar();
        $id = $ven->id_factura + 1;


        // header('Location: index.php?c=factura_tmp');
        header("refresh:0;index.php?c=factura&a=facturacion&id=$id");

        //header('Location: index.php?c=venta&a=sesion');
    }
    public function Venta()
    {

        //$producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        session_start();

        foreach ($this->factura->ListarDetalle($_REQUEST['id_factura']) as $p) {

            $venta_tmp = new venta_tmp();
            $venta_tmp->id = 0;
            $venta_tmp->id_venta = 1;
            $venta_tmp->id_factura = $p->id_factura;
            $venta_tmp->id_vendedor = $_SESSION['user_id'];
            $venta_tmp->id_producto = $p->id_producto;
            $venta_tmp->precio_venta = $p->precio_venta;
            $venta_tmp->cantidad = $p->cantidad;
            $venta_tmp->descuento = $p->descuento;
            $venta_tmp->fecha_venta = date("Y-m-d H:i");

            $this->venta_tmp->Registrar($venta_tmp);
        }
        header('Location: index.php?c=venta_tmp');
        //require_once "view/venta/tabla_venta.php";
    }

    public function Anular(){
        foreach ($this->factura->Listarfactura($_REQUEST['id']) as $p) {
            $factura = new factura();
            $factura->id_producto = $p->id_producto;
            $factura->cantidad = $p->cantidad;
            $this->producto->Restar($factura);
       

        }
        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?c=factura');
    }

}

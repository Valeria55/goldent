<?php
require_once 'model/transferencia_producto.php';
require_once 'model/usuario.php';
require_once 'model/transferencia_producto_tmp.php';
require_once 'model/venta_tmp.php';
require_once 'model/producto.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';
require_once 'model/marca.php';


class transferencia_productoController
{

    private $model;
    
    private $transferencia_producto;
    private $usuario;
    private $transferencia_producto_tmp;
    private $venta_tmp;
    private $producto;
    private $cierre;
    private $cliente;
    private $marca;
    

    public function __CONSTRUCT()
    {
        $this->model = new transferencia_producto();
        $this->transferencia_producto = new transferencia_producto();
        $this->usuario = new usuario();
        $this->transferencia_producto_tmp = new transferencia_producto_tmp();
        $this->venta_tmp = new venta_tmp();
        $this->producto = new producto();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
        $this->marca = new marca();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_producto.php';
        require_once 'view/footer.php';
    }

    public function Sesion()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_producto-sesion.php';
        require_once 'view/footer.php';
    }

    public function Nuevotransferencia_producto()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/nuevo-transferencia_producto.php';
        require_once 'view/footer.php';
    }
    public function Listar()
    {
        require_once 'view/transferencia_producto/transferencia_producto.php';
    }
    public function ListarAjaxRecibir()
    {
        $transferencia_producto = $this->model->ListarRecibir();
        echo json_encode($transferencia_producto, JSON_UNESCAPED_UNICODE);
    }

    public function transferencia_productopdf()
    {
        require_once 'view/informes/transferencia_productopdf.php';
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
        require_once 'view/transferencia_producto/transferencia_productocliente.php';
        require_once 'view/footer.php';
    }

    public function ListarUsuario()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_productousuario.php';
        require_once 'view/footer.php';
    }

    public function ListarProducto()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_productoproducto.php';
        require_once 'view/footer.php';
    }

    public function ListarProductoCat()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_productoproductocat.php';
        require_once 'view/footer.php';
    }

    public function detalles()
    {
        require_once 'view/transferencia_producto/transferencia_producto_detalles.php';
    }


    public function ListarDia()
    {

        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_productodia.php';
        require_once 'view/footer.php';
    }

    public function ListarAjax()
    {
        $transferencia_producto = $this->model->Listar(0);
        echo json_encode($transferencia_producto, JSON_UNESCAPED_UNICODE);
    }

    public function ListarFiltros()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $transferencia_producto = $this->model->ListarFiltros($desde, $hasta);
        echo json_encode($transferencia_producto, JSON_UNESCAPED_UNICODE);
    }

    public function ConfirmarTransferencia()
    {

        if (!isset($_SESSION)) session_start();

        $array_recibidos = $this->model->ListarDetalleRecibido($_REQUEST['id_transferencia_producto']);
        
        

        $transferencia_producto = new transferencia_producto();

        foreach ($array_recibidos as $producto) {

            $existencia_producto = $this->producto->ObtenerPorCodigo($producto->codigo);
            $existencia_marca = $producto->marca == NULL ? true : $this->marca->ObtenerMarca($producto->marca);
            // var_dump($existencia_marca);
            // die();
             $marca = new marca();
            if (!$existencia_marca) {
                $marca->id = 0;
                $marca->marca = $producto->marca;

                $this->marca->Registrar($marca);
                $m=$this->marca->Ultimo();
                
                $nueva_marca=$m->id;
            }else{
                $nueva_marca=$existencia_marca->id;
            }
            
            if (!$existencia_producto) {
                $nuevo_producto = $this->producto->ObtenerDeTallerPorCodigo($producto->codigo);
                
                $nuevo_producto->id_categoria = 0;
                $nuevo_producto->marca = $nueva_marca;
                $nuevo_producto->stock = 0;
                $nuevo_producto->servicio = null;
                $nuevo_producto->anulado = null;
                
                $this->producto->Registrar($nuevo_producto);
            }
            $ultimo_productoID = $this->producto->Ultimo();
            $transferencia_producto->id_transferencia_producto = $producto->id_transferencia_producto;
            $transferencia_producto->id_cliente = $producto->id_cliente;
            $transferencia_producto->id_vendedor = $producto->id_vendedor;
            $transferencia_producto->recibido_por = $_SESSION['user_id'];
            $transferencia_producto->id_producto = !$existencia_producto->id ?($ultimo_productoID->id + 1):$existencia_producto->id ;
            $transferencia_producto->codigo = $producto->codigo;
            $transferencia_producto->destino_transferencia = $producto->destino_transferencia;
            $transferencia_producto->precio_venta = $producto->precio_venta;
            $transferencia_producto->cantidad = $producto->cantidad;
            $transferencia_producto->fecha_transferencia_producto = $producto->fecha_transferencia_producto;
            $transferencia_producto->fecha_confirmacion = date('Y-m-d H:i:s');
            $transferencia_producto->descuento = $producto->descuento;
            $transferencia_producto->tipo_transferencia = $producto->tipo_transferencia;
            $transferencia_producto->estado = 'Recibido';

            $this->model->RegistrarRecibidos($transferencia_producto);

        }        

        foreach ($array_recibidos as $p) {

            if ($p->estado == 'cancelado') die('<script>alert("La transferencia fue cancelada desde el origen. No se pudo confirmar"); window.history.back();</script>');
            
            $producto = new producto();
            $producto->id_producto = $p->id_producto;
            $producto->cantidad = $p->cantidad;
            $producto->codigo = $p->codigo;
            $producto->precio_costo = $p->precio_costo;
            $producto->precio_minorista = $p->precio_minorista;
            $producto->precio_mayorista = $p->precio_mayorista;

            $this->producto->SumarPorCodigo($producto);
        }
        $this->model->FinalizarTransferenciaRecibida($_REQUEST['id_transferencia_producto']);
        header('Location: index.php?c=transferencia_producto&a=recibidos');
    }

    public function Cambiar()
    {

        $transferencia_producto = new transferencia_producto();

        $id_item = $_REQUEST['id_item'];
        $id_transferencia_producto = $_REQUEST['id_transferencia_producto'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $cantidad_ant = $_REQUEST['cantidad_ant'];

        $cant = $cantidad_ant - $cantidad;


        echo json_encode($transferencia_producto);
    }

    public function Cancelar()
    {

        $id_item = $_REQUEST['id_item'];
        $id_transferencia_producto = $_REQUEST['id_transferencia_producto'];
        $codigo = $_REQUEST['codigo'];
        $cantidad = $_REQUEST['cantidad_item'];


        $transferencia_producto = $this->model->Cantidad($id_item, $id_transferencia_producto, 0);


        $transferencia_producto = $this->model->CancelarItem($id_item);

        header('location: ?c=transferencia_producto_tmp&a=editar&id=' . $id_transferencia_producto);
    }



    public function Crud()
    {
        $transferencia_producto = new transferencia_producto();

        if (isset($_REQUEST['id'])) {
            $transferencia_producto = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_producto-editar.php';
        require_once 'view/footer.php';
    }

    public function Cierre()
    {

        require_once 'view/informes/cierrepdf.php';
    }
    public function TransferenciaPdf()
    {
        require_once 'view/informes/transferenciapdf.php';
    }
    public function recibidos()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_producto/transferencia_producto-recibir.php';
        require_once 'view/footer.php';
    }
    public function InformeDiario()
    {

        require_once 'view/informes/transferencia_productodiapdf.php';
    }

    public function InformeRango()
    {

        require_once 'view/informes/transferencia_productorangopdf.php';
    }

    public function InformeUsados()
    {

        require_once 'view/informes/productosusadospdf.php';
    }

    public function CierreMes()
    {

        require_once 'view/informes/cierremesnewpdf.php';
    }

    public function Factura()
    {

        require_once 'view/informes/facturapdf.php';
    }

    public function Ticket()
    {

        require_once 'view/informes/ticketpdf.php';
    }

    public function Obtener()
    {
        $transferencia_producto = new transferencia_producto();

        if (isset($_REQUEST['id'])) {
            $transferencia_producto = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/transferencia_producto/transferencia_producto-editar.php';
    }

    public function ObtenerProducto()
    {
        $transferencia_producto = new transferencia_producto();

        $transferencia_producto = $this->model->ObtenerProducto($_REQUEST['id_transferencia_producto'], $_REQUEST['id_producto']);

        echo json_encode($transferencia_producto);
    }

    public function GuardarUno()
    {


        $transferencia_producto = new transferencia_producto();

        $p_transferencia_producto = $_REQUEST['precio_transferencia_producto'] * $_REQUEST['cantidad'];

        $transferencia_producto->id = 0;
        $transferencia_producto->id_transferencia_producto = $_REQUEST['id_transferencia_producto'];
        $transferencia_producto->id_cliente = $_REQUEST['id_cliente'];
        $transferencia_producto->id_vendedor = $_REQUEST['id_transferencia_producto'];
        $transferencia_producto->id_producto = $_REQUEST['id_producto'];
        $transferencia_producto->precio_venta = $_REQUEST['precio_venta'];
        $transferencia_producto->descuento = 0;
        $transferencia_producto->cantidad = $_REQUEST['cantidad'];
        $transferencia_producto->fecha_transferencia_producto = $_REQUEST['fecha_transferencia_producto'];

        $transferencia_producto->id > 0
            ? $this->model->Actualizar($transferencia_producto)
            : $this->model->Registrar($transferencia_producto);
        header('Location: index.php?c=transferencia_producto_tmp&a=editar&id=' . $transferencia_producto->id_transferencia_producto);
    }


    public function Guardar()
    {

        $ven = new transferencia_producto();
        $ven = $this->model->Ultimo();
        $sumaTotal = 0;


        foreach ($this->transferencia_producto_tmp->Listar() as $v) {

            $transferencia_producto = new transferencia_producto();

            $transferencia_producto->id = 0;
            $transferencia_producto->id_transferencia_producto = $ven->id_transferencia_producto + 1;
            $transferencia_producto->id_cliente = $_REQUEST['id_cliente'] ?? 0;
            $transferencia_producto->destino_transferencia = $_REQUEST['destino_transferencia'] ?? 'c_inst_1';
            $transferencia_producto->id_vendedor = $v->id_vendedor;
            $transferencia_producto->id_producto = $v->id_producto;
            $transferencia_producto->codigo = $v->codigo;
            $transferencia_producto->precio_venta = $v->precio_venta;
            $transferencia_producto->tipo_transferencia = $_REQUEST['tipo_transferencia'] ?? "envio";
            $transferencia_producto->estado = "pendiente";

            $transferencia_producto->precio_venta = $v->precio_venta;
            $transferencia_producto->descuento = 0;
            $transferencia_producto->cantidad = $v->cantidad;
            $transferencia_producto->fecha_transferencia_producto = $_REQUEST["fecha_transferencia_producto"]; //date("Y-m-d H:i");

            //Restar Stock
            $this->producto->Restar($transferencia_producto);

            //Registrar transferencia_producto
            $this->model->Registrar($transferencia_producto);

            $sumaTotal += $transferencia_producto->transferencia_producto;
        }


        $this->transferencia_producto_tmp->Vaciar();
        $id = $ven->id_transferencia_producto + 1;


        header('Location: index.php?c=transferencia_producto');
        //header("refresh:0;index.php?c=venta&a=factura&id=$id");

        //header('Location: index.php?c=venta&a=sesion');
    }
    public function CancelarTransferencia()
    {

        //$producto = $this->producto->Codigo($_REQUEST['codigo']);
        // $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        if (!isset($_SESSION)) session_start();

        foreach ($this->model->ListarDetalle($_REQUEST['id_transferencia_producto']) as $p) {

            if ($p->estado == 'finalizado') die('<script>alert("La transferencia ya fue confirmada en el destino, no se puede cancelar"); window.history.back();</script>');

            $producto = new producto();
            $producto->id_producto = $p->id_producto;
            $producto->codigo = $p->codigo;
            $producto->cantidad = $p->cantidad;

            $this->producto->Sumar($producto);

            // $venta_tmp = new venta_tmp();
            // $venta_tmp->id = 0;
            // $venta_tmp->id_venta = 1;
            // $venta_tmp->id_transferencia_producto = $p->id_transferencia_producto;
            // $venta_tmp->id_vendedor = $_SESSION['user_id'];
            // $venta_tmp->id_producto = $p->id_producto;
            // $venta_tmp->precio_venta = $p->precio_venta;
            // $venta_tmp->cantidad = $p->cantidad;
            // $venta_tmp->descuento = $p->descuento;
            // $venta_tmp->fecha_venta = date("Y-m-d H:i");

            // $this->venta_tmp->Registrar($venta_tmp);
        }
        $this->model->CancelarTransferencia($_REQUEST['id_transferencia_producto']);
        header('Location: index.php?c=transferencia_producto');
        //require_once "view/venta/tabla_venta.php";
    }

    public function Venta()
    {

        //$producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        if (!isset($_SESSION)) session_start();

        foreach ($this->transferencia_producto->ListarDetalle($_REQUEST['id_transferencia_producto']) as $p) {


            $producto = new producto();
            $producto->id_producto = $p->id_producto;
            $producto->cantidad = $p->cantidad;

            $this->producto->Sumar($producto);

            $venta_tmp = new venta_tmp();
            $venta_tmp->id = 0;
            $venta_tmp->id_venta = 1;
            $venta_tmp->id_transferencia_producto = $p->id_transferencia_producto;
            $venta_tmp->id_vendedor = $_SESSION['user_id'];
            $venta_tmp->id_producto = $p->id_producto;
            $venta_tmp->precio_venta = $p->precio_venta;
            $venta_tmp->cantidad = $p->cantidad;
            $venta_tmp->descuento = $p->descuento;
            $venta_tmp->fecha_venta = date("Y-m-d H:i");

            $this->venta_tmp->Registrar($venta_tmp);
        }
        $this->model->FinalizarTransferenciaRecibida($_REQUEST['id_transferencia_producto']);
        header('Location: index.php?c=venta_tmp');
        //require_once "view/venta/tabla_venta.php";
    }

    public function Eliminar()
    {

        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=transferencia_producto');
    }

    public function Anular()
    {

        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?c=transferencia_producto');
    }
}

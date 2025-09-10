<?php
require_once 'model/presupuesto.php';
require_once 'model/usuario.php';
require_once 'model/presupuesto_tmp.php';
require_once 'model/venta_tmp.php';
require_once 'model/producto.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';


class presupuestoController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new presupuesto();
        $this->presupuesto = new presupuesto();
        $this->usuario = new usuario();
        $this->presupuesto_tmp = new presupuesto_tmp();
        $this->venta_tmp = new venta_tmp();
        $this->producto = new producto();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuesto.php';
        require_once 'view/footer.php';
    }

    public function Sesion()
    {
        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuesto-sesion.php';
        require_once 'view/footer.php';
    }

    public function NuevoPresupuesto()
    {
        require_once 'view/header.php';
        require_once 'view/presupuesto/nuevo-presupuesto.php';
        require_once 'view/footer.php';
    }
    public function Listar()
    {
        require_once 'view/presupuesto/presupuesto.php';
    }

    public function ListarAnulado()
    {
        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuesto_anulado.php';
        require_once 'view/footer.php';
    }

    public function ListarPendientes()
    {
        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuesto_pendientes.php';
        require_once 'view/footer.php';
    }

    public function Presupuestopdf()
    {
        require_once 'view/informes/presupuestopdf.php';
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
        require_once 'view/presupuesto/presupuestocliente.php';
        require_once 'view/footer.php';
    }

    public function ListarUsuario()
    {
        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuestousuario.php';
        require_once 'view/footer.php';
    }

    public function ListarProducto()
    {
        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuestoproducto.php';
        require_once 'view/footer.php';
    }

    public function ListarProductoCat()
    {
        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuestoproductocat.php';
        require_once 'view/footer.php';
    }

    public function detalles()
    {
        require_once 'view/presupuesto/presupuesto_detalles.php';
    }


    public function ListarDia()
    {

        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuestodia.php';
        require_once 'view/footer.php';
    }

    public function ListarAjax()
    {
        $presupuesto = $this->model->Listar(0);
        echo json_encode($presupuesto, JSON_UNESCAPED_UNICODE);
    }

    public function ListarAjaxAnulado()
    {
        $presupuesto = $this->model->ListarAnulado(0);
        echo json_encode($presupuesto, JSON_UNESCAPED_UNICODE);
    }

    public function ListarAjaxPendientes()
    {
        $presupuesto = $this->model->ListarPendientes(0);
        echo json_encode($presupuesto, JSON_UNESCAPED_UNICODE);
    }

    public function ListarFiltros()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $presupuesto = $this->model->ListarFiltros($desde, $hasta);
        echo json_encode($presupuesto, JSON_UNESCAPED_UNICODE);
    }
    public function ListarFiltrosAnulado()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $presupuesto = $this->model->ListarFiltrosAnulado($desde, $hasta);
        echo json_encode($presupuesto, JSON_UNESCAPED_UNICODE);
    }

    public function ListarPorEstado()
    {
        $estado = $_REQUEST["estado"];
        $desde = isset($_REQUEST["desde"]) ? $_REQUEST["desde"] : '';
        $hasta = isset($_REQUEST["hasta"]) ? $_REQUEST["hasta"] : '';
        
        $presupuesto = $this->model->ListarPorEstado($estado, $desde, $hasta);
        echo json_encode($presupuesto, JSON_UNESCAPED_UNICODE);
    }


    public function Cambiar()
    {

        $presupuesto = new presupuesto();

        $id_item = $_REQUEST['id_item'];
        $id_presupuesto = $_REQUEST['id_presupuesto'];
        $cantidad = $_REQUEST['cantidad'];
        $codigo = $_REQUEST['codigo'];
        $cantidad_ant = $_REQUEST['cantidad_ant'];

        $cant = $cantidad_ant - $cantidad;


        echo json_encode($presupuesto);
    }

    public function Cancelar()
    {

        $id_item = $_REQUEST['id_item'];
        $id_presupuesto = $_REQUEST['id_presupuesto'];
        $codigo = $_REQUEST['codigo'];
        $cantidad = $_REQUEST['cantidad_item'];


        $presupuesto = $this->model->Cantidad($id_item, $id_presupuesto, 0);


        $presupuesto = $this->model->CancelarItem($id_item);

        header('location: ?c=presupuesto_tmp&a=editar&id=' . $id_presupuesto);
    }



    public function Crud()
    {
        $presupuesto = new presupuesto();

        if (isset($_REQUEST['id'])) {
            $presupuesto = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/presupuesto/presupuesto-editar.php';
        require_once 'view/footer.php';
    }

    public function Cierre()
    {

        require_once 'view/informes/cierrepdf.php';
    }

    public function InformeDiario()
    {

        require_once 'view/informes/presupuestodiapdf.php';
    }

    public function InformeRango()
    {

        require_once 'view/informes/presupuestorangopdf.php';
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
        $presupuesto = new presupuesto();

        if (isset($_REQUEST['id'])) {
            $presupuesto = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/presupuesto/presupuesto-editar.php';
    }

    public function ObtenerProducto()
    {
        $presupuesto = new presupuesto();

        $presupuesto = $this->model->ObtenerProducto($_REQUEST['id_presupuesto'], $_REQUEST['id_producto']);

        echo json_encode($presupuesto);
    }

    public function GuardarUno()
    {


        $presupuesto = new presupuesto();

        $p_presupuesto = $_REQUEST['precio_presupuesto'] * $_REQUEST['cantidad'];

        $presupuesto->id = 0;
        $presupuesto->id_presupuesto = $_REQUEST['id_presupuesto'];
        $presupuesto->id_cliente = $_REQUEST['id_cliente'];
        $presupuesto->id_vendedor = $_REQUEST['id_presupuesto'];
        $presupuesto->id_producto = $_REQUEST['id_producto'];
        $presupuesto->precio_venta = $_REQUEST['precio_venta'];
        $presupuesto->descuento = 0;
        $presupuesto->cantidad = $_REQUEST['cantidad'];
        $presupuesto->fecha_presupuesto = $_REQUEST['fecha_presupuesto'];

        $presupuesto->id > 0
            ? $this->model->Actualizar($presupuesto)
            : $this->model->Registrar($presupuesto);
        header('Location: index.php?c=presupuesto_tmp&a=editar&id=' . $presupuesto->id_presupuesto);
    }


    public function Guardar()
    {

        $ven = new presupuesto();
        $ven = $this->model->Ultimo();
        $sumaTotal = 0;
        
        // Obtener descuento global del formulario
        $descuento_global = isset($_REQUEST['descuento_global']) ? floatval($_REQUEST['descuento_global']) : 0;
        
        // Determinar si requiere aprobación (descuento > 10%)
        $requiere_aprobacion = $descuento_global > 10;
        $aprobado = $requiere_aprobacion ? 'no' : 'si';
        $estado = $requiere_aprobacion ? 'Pendiente' : 'Aprobado';

        foreach ($this->presupuesto_tmp->Listar() as $v) {

            $presupuesto = new presupuesto();

            // Usar descuento global solo si es mayor a 0, sino usar el descuento individual
            $descuento_aplicar = ($descuento_global > 0) ? $descuento_global : $v->descuento;
            
            // Verificar aprobación también con descuentos individuales si no hay descuento global
            if ($descuento_global == 0 && $v->descuento > 10) {
                $requiere_aprobacion = true;
                $aprobado = 'no';
                $estado = 'Pendiente';
            }

            $presupuesto->id = 0;
            $presupuesto->id_presupuesto = $ven->id_presupuesto + 1;
            $presupuesto->id_cliente = $_REQUEST['id_cliente'];
            $presupuesto->id_vendedor = $v->id_vendedor;
            $presupuesto->id_producto = $v->id_producto;
            $presupuesto->precio_venta = $v->precio_venta;
            $presupuesto->descuento = $descuento_aplicar; // Usar descuento calculado
            $presupuesto->cantidad = $v->cantidad;
            $presupuesto->fecha_presupuesto = $_REQUEST["fecha_presupuesto"]; //date("Y-m-d H:i");
            $presupuesto->aprobado = $aprobado; // Nuevo campo para aprobación
            $presupuesto->estado = $estado; // Establecer estado

            //Registrar presupuesto
            $this->model->Registrar($presupuesto);

            $sumaTotal += $presupuesto->presupuesto;
        }


        $this->presupuesto_tmp->Vaciar();
        $id = $ven->id_presupuesto + 1;

        // Mostrar mensaje si requiere aprobación
        if ($requiere_aprobacion) {
            if (!isset($_SESSION)) session_start();
            $descuento_mostrar = $descuento_global > 0 ? $descuento_global : "individual";
            $_SESSION['mensaje'] = "Presupuesto guardado. Requiere aprobación debido al descuento aplicado ({$descuento_mostrar}% > 10%).";
        }

        header('Location: index.php?c=presupuesto_tmp');
        //header("refresh:0;index.php?c=venta&a=factura&id=$id");

        //header('Location: index.php?c=venta&a=sesion');
    }
    public function Venta()
    {
        $this->venta_tmp->Vaciar();
        //$producto = $this->producto->Codigo($_REQUEST['codigo']);
        $pro = $this->producto->Obtener($_REQUEST['id_producto']);
        if (!isset($_SESSION)) session_start();

        foreach ($this->presupuesto->ListarDetalle($_REQUEST['id_presupuesto']) as $p) {

            $venta_tmp = new venta_tmp();
            $venta_tmp->id = 0;
            $venta_tmp->id_venta = 1;
            $venta_tmp->id_presupuesto = $p->id_presupuesto;
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

    public function Eliminar()
    {

        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?c=presupuesto');
    }

    public function Anular()
    {

        $this->model->Anular($_REQUEST['id']);
        header('Location: index.php?c=presupuesto');
    }

    public function Aprobar()
    {
        $this->model->Aprobar($_REQUEST['id_presupuesto']);
        // También actualizar el estado a 'Aprobado'
        $this->model->ActualizarEstado($_REQUEST['id_presupuesto'], 'Aprobado');
        header('Location: index.php?c=presupuesto');
    }
}

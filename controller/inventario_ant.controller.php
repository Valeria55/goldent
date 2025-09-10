<?php
require_once 'model/cierre_inventario.php';
require_once 'model/cierre.php';
require_once 'model/inventario.php';
require_once 'model/producto.php';


class inventarioController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new inventario();
        $this->cierre_inventario = new cierre_inventario();
        $this->cierre = new cierre();
        $this->producto = new producto();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/inventario/inventario.php';
        require_once 'view/footer.php';
    }


    public function FechaApertura()
    {
        require_once 'view/header.php';
        require_once 'view/inventario/inventario.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/inventario/inventario.php';
    }


    public function ListarAjax()
    {
        $inventario = $this->model->Listar(0);
        echo json_encode($inventario, JSON_UNESCAPED_UNICODE);
    }

    public function ListarFiltros()
    {

        $desde = ($_REQUEST["desde"]);
        $hasta = ($_REQUEST["hasta"]);

        $inventario = $this->model->ListarFiltros($desde, $hasta);
        echo json_encode($inventario, JSON_UNESCAPED_UNICODE);
    }


    public function Crud()
    {

        $inventario = new inventario();

        if (isset($_REQUEST['id'])) {
            $inventario = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/inventario/inventario-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $inventario = new inventario();

        if (isset($_REQUEST['id'])) {
            $inventario = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/inventario/inventario-editar.php';
    }

    public function ObtenerJson()
    {
        $inventario = new inventario();

        if (isset($_REQUEST['id'])) {
            $inventario = $this->model->Obtener($_REQUEST['id']);
        }

        echo json_encode($inventario, JSON_UNESCAPED_UNICODE);
    }

    public function Guardar()
    {

        //Lista de la tabla de productos y registra en inventario.
        foreach ($this->producto->Listar() as $p) {

            $inventario = new inventario();

            $inventario->id_producto = $p->id;
            $inventario->id_usuario = $_SESSION['user_id'];
            $inventario->stock_actual = $p->stock;
            $inventario->fecha = date("Y-m-d");
            $this->model->Registrar($inventario);
        }

        $cierre_inventario = new cierre_inventario();

        if (!isset($_SESSION)) session_start();
        $cierre_inventario->fecha_apertura = date("Y-m-d H:i");
        $cierre_inventario->fecha_cierre = null;
        $cierre_inventario->usuario_inicial = $_SESSION['user_id'];

        $this->cierre_inventario->Registrar($cierre_inventario);


        header('Location:' . getenv('HTTP_REFERER'));
    }

    /*  public function GuardarStock(){

        foreach($this->inventario->Listar($fecha) as $i){

            $producto = new producto();
            $fecha = date('Y-m-d');
            $producto->id = $i->id_producto;
            $producto->stock = $i->stock_real;
            $this->model->GuardarStock($producto);
        }   
            
        
       header('Location:' . getenv('HTTP_REFERER'));
    }*/

    //Obtiene los datos a traves del id y resta los campos sin necesidad de recargar. Va a bd.
    //Una vez que se obtiene el id, se puede ingresar en cualquiera de los campos.
    public function StockReal()
    {

        $inventario = new inventario();

        $i = $this->model->Obtener($_REQUEST['id']);

        $inventario->id = $_REQUEST['id'];
        $inventario->stock_real = $_REQUEST['stock_real'];
        $inventario->faltante = $i->stock_actual - $inventario->stock_real;
        //Inserta en el modelo los datos de arriba.
        $insert = $this->model->StockReal($inventario);
        // var_dump($insert);

    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c=' . $_REQUEST['c']);
    }
}

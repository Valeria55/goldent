<?php
require_once 'model/cierre_inventario.php';
require_once 'model/cierre.php';
require_once 'model/inventario.php';
require_once 'model/producto.php';


class inventarioController
{

    private $model;
    private $cierre_inventario;
    private $cierre;
    private $producto;

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


    //  public function FechaApertura(){
    //     require_once 'view/header.php';
    //     require_once 'view/inventario/inventario.php';
    //     require_once 'view/footer.php';

    //    }
    public function ListarPorIdC()
    {
        require_once 'view/header.php';
        require_once 'view/inventario/inventario.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/inventario/inventario.php';
    }
    
    public function Inventario()
    {
        require_once 'view/header.php';
        require_once 'view/inventario/vista_inventario.php';
        require_once 'view/footer.php';
        
    }
    
     public function ListarInventario()
    {
        $inventario = new inventario();
        $inventario = $this->model->inventario($_REQUEST['id_c']);
        echo json_encode($inventario, JSON_UNESCAPED_UNICODE);
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


    public function InventarioPdf()
    {

        $id_c = $_REQUEST['id_c'];

        $cierre_inventario = $this->cierre_inventario->Obtener($id_c);

        // require_once 'view/informes/inventariopdf_dompdf.php';
        require_once 'view/informes/inventariopdf.php';
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
        $cierre_inventario = new cierre_inventario();

        session_start();
        $cierre_inventario->fecha_apertura = date("Y-m-d H:i");
        $cierre_inventario->fecha_cierre = null;
        $cierre_inventario->usuario_inicial = $_SESSION['user_id'];

        if ($this->cierre_inventario->CierrePendiente()){
            header('Location:' . getenv('HTTP_REFERER'));
            die;
        }

        $new_cierre_inv = $this->cierre_inventario->Registrar($cierre_inventario);

        $inventario = new inventario();
        $inventario->id_inventario = $new_cierre_inv->id;
        $inventario->id_usuario = $_SESSION['user_id'];
        $inventario->fecha = date("Y-m-d");
        $this->model->RegistrarInventario($inventario);


        header('Location: index.php?c=inventario&a=ListarPorIdC&id_c=' . $inventario->id_inventario);
    }

    public function ObtenerSumatorias()
    {
        $id_cierre = $_REQUEST['id_c'];
        $q = $_REQUEST['q'];

        $sumatorias = $this->model->ObtenerSumatorias($id_cierre, $q);

        $sumatorias->monto_faltante_formatted = number_format($sumatorias->monto_faltante, 0); 

        echo json_encode($sumatorias, JSON_UNESCAPED_UNICODE);
    }

    public function ListarSS()
    { //listar por id_cierre_inventario

        $table = 'inventario';
        $primaryKey = 'id';

        $columns = array(
            //campos
            array(
                'db' => 'rownum',  'dt' =>
                0,
                'formatter' => function ($d, $row) {
                    return "<span title='Id Producto: {$row['id_producto']}'>$d</span>";
                }
            ),
            //campos
            array(
                'db' => 'codigo',  'dt' => 1
            ),
            array(
                'db' => 'categoria',  'dt' =>
                2,
                'formatter' => function ($d, $row) {
                    return ($d) ?? "<small>(sin especificar)</small>";
                }
            ),
            array(
                'db' => 'producto',  'dt' =>
                3,
                'formatter' => function ($d, $row) {
                    return $d;
                }
            ),
            array(
                'db' => 'precio_costo',  'dt' =>
                4,
                'formatter' => function ($d, $row) {
                    return number_format($row['precio_costo']);
                }
            ),
            array(
                'db' => 'precio_minorista',  'dt' =>
                5,
                'formatter' => function ($d, $row) {
                    return number_format($d);
                }
            ),
            array(
                'db' => 'stock_actual',  'dt' =>
                6,
                'formatter' => function ($d, $row) {
                    return $d;
                }
            ),
            array(
                'db' => 'stock_productos_view',  'dt' =>
                7,
                'formatter' => function ($d, $row) {
                    return $d;
                }
            ),
            array(
                'db' => 'stock_real',  'dt' =>
                8,
                'formatter' => function ($d, $row) {
                    if (is_null($row['fecha_cierre'])) {
                        $disabled = '';
                        $css_bg = (!is_null($d))? "background-color : #E4FAE4;" : "";
                        $data = '<div class="form-group">
                        <input min="0" style="'. $css_bg . '" id_real="' . $row['id_i'] . '" stock_real="' . $row['stock_real'] . '"  name="stock_real" class="stock_real form-control" type="number" value="' . $row['stock_real'] . '" id="stock_real" onchange="setStockReal($(this))"' . $disabled . '>
                        </div>';
                    } else {
                        // $data = ( $d > 0 ) ? $d : $row['stock_inicial'];
                        if (is_null($d)) {
                            // $data = $row['stock_actual'];
                            $data = 0;
                        } else {
                            $data = $d;
                        }
                    }
                    return $data;
                }
            ),
            array(
                'db' => 'fecha_stock_real',  'dt' =>
                9,
                'formatter' => function ($d, $row) {
                    //fecha de carga del stock
                    if (is_null($row['fecha_stock_real'])) {
                        return '<small>sin cargar</small>';
                    } else {
                        return date('d/m/Y \a \l\a\s H:i:s', strtotime($row['fecha_stock_real']));
                    }
                }
            ),
            array( // PRODUCTOS SOBRANTES, COLUMNA SOBRANTE
                'db' => 'faltante',  'dt' =>
                10,
                'formatter' => function ($d, $row) {
                    //CANTIDAD DE PRODUCTOS FALTANTES
                    if (is_null($row['fecha_cierre'])) { // si inventario esta abierto
                        if (is_null($row['faltante'])){ //si todavia no se cargo el stock real
                            return '';
                        }else{ // si el stock ya se cargo
                            // solo mostrar en esta columna si faltante es negativo
                            return ($row['faltante'] < 0) ? number_format($row['faltante'] * -1) : '';
                        }
                    } else {
                        return (is_null($row['faltante'])) ? '0' : number_format($row['faltante'] * -1);
                    }
                }
            ),
            array(
                'db' => 'faltante',  'dt' =>
                11,
                'formatter' => function ($d, $row) {
                    //CANTIDAD DE PRODUCTOS FALTANTES
                    if (is_null($row['fecha_cierre'])) { // si inventario esta abierto
                        if (is_null($row['faltante'])){ //si todavia no se cargo el stock real
                            return '';
                        }else{ // si el stock ya se cargo
                            // solo mostrar en esta columna si faltante es positivo o 0
                            return ($row['faltante'] >= 0) ? number_format($row['faltante']) : '';
                        }
                    } else {
                        return (is_null($row['faltante'])) ? '0' : number_format($row['faltante']);
                    }
                }
            ),
            array(
                'db' => 'faltante',  'dt' =>
                12,
                'formatter' => function ($d, $row) {

                    // MONTO DE VENTA DE FALTANTES
                    if (is_null($row['fecha_cierre'])) {
                        return (is_null($row['faltante'])) ? '' : number_format($row['faltante'] * $row['precio_minorista']);
                    } else {
                        return (is_null($row['faltante'])) ? '0' : number_format($row['faltante'] * $row['precio_minorista']);
                    }
                    //return (is_null($row['faltante'])) ? '' : number_format($row['faltante'] * $row['precio_minorista']);
                }
            ),

        );

        $inventario = $this->model->ListarSS($_GET, $table, $primaryKey, $columns);
        //$inventario = utf8ize($inventario); //llamada a funcion que esta en index. para convertir a utf8

        // var_dump($inventario);
        

        echo json_encode($inventario, JSON_UNESCAPED_UNICODE);

        /*  ver error en json encode
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                echo ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                echo ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                echo ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                echo ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                echo ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                echo ' - Unknown error';
                break;
        }
        fin ver error en json encode
        */

        // var_dump($texto);


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

        $producto = $this->producto->Obtener($i->id_producto);

        if(!($inventario->id > 0) || (!(is_numeric($inventario->stock_real)) && ($inventario->stock_real != '')) ){
            die('Error al guardar los datos.');
        }
        if($inventario->stock_real < 0) $inventario->stock_real = 0;

        //calcular faltante con stock que hay en la tabla productos al cargar
        if ($producto->stock < 0) {
            $inventario->faltante = 0;
        } else {
            $inventario->faltante = $producto->stock - $inventario->stock_real;
        }
        $inventario->stock_tabla_productos = $producto->stock;
        $inventario->fecha_stock_real = date("Y-m-d H:i:s");
        
        //Inserta en el modelo los datos de arriba.
        $respuesta = $this->model->StockReal($inventario);
        echo $respuesta;
        // var_dump($insert);

    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c=' . $_REQUEST['c']);
    }
}

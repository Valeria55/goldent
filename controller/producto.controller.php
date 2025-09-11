
    
<?php
require_once 'model/producto.php';
require_once 'model/categoria.php';
require_once 'model/marca.php';
require_once 'model/imagen.php';
require_once 'model/sucursal.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';
class productoController
{

    private $model;
    private $categoria;
    private $cierre;
    private $marca;
    private $imagen;
    private $sucursal;
    private $cliente;

    public function __CONSTRUCT()
    {
        $this->model = new producto();
        $this->categoria = new categoria();
        $this->cierre = new cierre();
        $this->marca = new marca();
        $this->imagen = new imagen();
        $this->sucursal = new sucursal();
        $this->cliente = new cliente();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/producto/producto.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/producto/producto.php';
    }
    public function servicios()
    {
        require_once 'view/header.php';
        require_once 'view/producto/servicio.php';
        require_once 'view/footer.php';
    }
    public function ListarAjax()
    {
        $producto = $this->model->ListarAjax();
        echo json_encode($producto, JSON_UNESCAPED_UNICODE);
    }
    public function ListarServicios()
    {
        $producto = $this->model->ListarServicios();
        echo json_encode($producto, JSON_UNESCAPED_UNICODE);
    }
    public function ListarStockTiempo()
    {
        $producto = $this->model->ListarStockTiempo($_REQUEST['fecha']);
        echo json_encode($producto, JSON_UNESCAPED_UNICODE);
    }
    public function Stock()
    {
        require_once 'view/header.php';
        require_once 'view/producto/stock_tiempo.php';
        require_once 'view/footer.php';
    }
    public function Crud()
    {
        $producto = new producto();

        if (isset($_REQUEST['id'])) {
            $producto = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/producto/producto-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $producto = new producto();

        if (isset($_REQUEST['id'])) {
            $producto = $this->model->Obtener($_REQUEST['id']);
        }

        if (!isset($_SESSION)) session_start();
        require_once 'view/producto/producto-editar.php';
    }

    public function Balance()
    {
        require_once 'view/header.php';
        require_once 'view/informes/balance.php';
        require_once 'view/footer.php';
    }

    public function Buscar()
    {

        if (isset($_REQUEST['id'])) {
            $producto = $this->model->Obtener($_REQUEST['id']);
        }
        echo json_encode($producto);
    }

    public function Guardar()
    {
        $producto = new producto();

        $producto->id = $_REQUEST['id'];
        $producto->codigo = $_REQUEST['codigo'];
        $producto->id_categoria = $_REQUEST['id_categoria'];
        $producto->producto = $_REQUEST['producto'];
        $producto->marca = $_REQUEST['marca'];
        $producto->descripcion = $_REQUEST['descripcion'];
        $producto->precio_costo = $_REQUEST['precio_costo'];
        $producto->precio_minorista = $_REQUEST['precio_minorista'];
        // if (($producto->precio_costo * 1.05) > $producto->precio_minorista) {
        //     require_once 'view/header.php';
        //     require_once 'view/footer.php';
        //     die('<script>
        //             Swal.fire({
        //             title: "No se guardó el registro",
        //             icon: "warning",
        //             html: `<p>La ganancia no puede ser menor al 5%</p>
        //                     `,
        //             showCancelButton: false,
        //             focusCancel: true,
        //             customClass: "swal-lg",
        //             confirmButtonText: "OK",
        //             // timer: 3000, // para cerrar automaticamente
        //         }).then((result) => {
        //             if (result.isConfirmed) {
        //                 window.history.back ();
        //             } else if (result.isDenied) { // cancelado
        //                 window.history.back ();
        //             }
        //         })
        //         </script>');
        // }
        $producto->precio_mayorista = $_REQUEST['precio_mayorista'];
        $producto->precio_promo = $_REQUEST['precio_promo'];
        $producto->desde = $_REQUEST['desde'];
        $producto->hasta = $_REQUEST['hasta'];
        $producto->stock = $_REQUEST['stock'];
        $producto->stock_minimo = $_REQUEST['stock_minimo'];
        $producto->descuento_max = $_REQUEST['descuento_max'];
        $producto->importado = $_REQUEST['importado'];
        $producto->iva = $_REQUEST['iva'];
        $producto->sucursal = $_REQUEST['sucursal'];
        $producto->tipo = 'producto';

        $ultimo = $this->model->Ultimo();
        $ultimo_id = $ultimo->id + 1;

        $imagen = new imagen();

        $imagen->id_producto = $ultimo_id;

        if (isset($_FILES["imagen"]) && count($_FILES["imagen"]["tmp_name"]) > 0 && $_FILES["imagen"]["name"][0] != "") {

            $id_pedido = $_POST['id'];
            $reporte = null;

            for ($x = 0; $x < count($_FILES["imagen"]["name"]); $x++) {

                $file = $_FILES["imagen"];
                $nombre = $new_id = rand(1000, 1000000) . $file["name"][$x];
                $tipo = $file["type"][$x];
                $ruta_provisional = $file["tmp_name"][$x];
                $size = $file["size"][$x];
                $dimensiones = getimagesize($ruta_provisional);
                $width = $dimensiones[0];
                $height = $dimensiones[1];
                $carpeta = "assets/img/";

                if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'image/gif') {
                    $reporte .= "<p style='color: red'>Error $nombre, el archivo no es una imagen.</p>";
                } elseif ($size > 11024 * 11024) {
                    $reporte .= "<p style='color: red'>Error $nombre, el tamaño máximo permitido es 1mb</p>";
                } else {
                    $src = $carpeta . $nombre;
                    //Caragamos imagenes al servidor
                    move_uploaded_file($ruta_provisional, $src);

                    $imagen->imagen = $nombre;
                    $this->imagen->Registrar($imagen);
                }
            }
            echo $reporte;
        }


        $producto->id > 0
            ? $this->model->Actualizar($producto)
            : $this->model->Registrar($producto);

        $producto->id > 0
            ? $accion = "Modificado"
            : $accion = "Agregado";;

        header('Location:' . getenv('HTTP_REFERER'));
        //header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }
    // Registrar servicio desde el modal
    public function RegistrarServicio()
    {
        $producto = new producto();
        $producto->codigo = $_POST['codigo'];
        $producto->producto = $_POST['servicio'];
        $producto->precio_minorista = $_POST['precio'];
        $producto->tipo = 'servicio';
        // Campos por defecto para servicios
        $producto->id_categoria = 0;
        $producto->marca = '';
        $producto->descripcion = '';
        $producto->precio_costo = 0;
        $producto->precio_mayorista = 0;
        $producto->precio_promo = 0;
        $producto->desde = null;
        $producto->hasta = null;
        $producto->stock = 0;
        $producto->stock_minimo = 0;
        $producto->descuento_max = 0;
        $producto->importado = 0;
        $producto->iva = 10;
        $producto->sucursal = 1;
        $producto->anulado = null;
        $this->model->Registrar($producto);
        echo json_encode(["success" => true]);
        exit;
    }
        // Editar servicio desde el modal
    public function EditarServicio()
    {
        $producto = new producto();
        $producto->id = $_POST['id'];
        $producto->codigo = $_POST['codigo'];
        $producto->producto = $_POST['servicio'];
        $producto->precio_minorista = $_POST['precio'];
        $producto->tipo = 'servicio';
        // Campos por defecto para servicios
        $producto->id_categoria = 0;
        $producto->marca = '';
        $producto->descripcion = '';
        $producto->precio_costo = 0;
        $producto->precio_mayorista = 0;
        $producto->precio_promo = 0;
        $producto->desde = null;
        $producto->hasta = null;
        $producto->stock = 0;
        $producto->stock_minimo = 0;
        $producto->descuento_max = 0;
        $producto->importado = 0;
        $producto->iva = 0;
        $producto->sucursal = '';
        $producto->anulado = 0;
        $this->model->Actualizar($producto);
        echo json_encode(["success" => true]);
        exit;
    }
    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c=' . $_REQUEST['c']);
    }

    public function GenerarCodigo()
    {
        require_once 'view/informes/codigoDeBarra.php';
    }

    public function VerCodigoBarra()
    {
        require_once 'view/producto/codigoDeBarra.php';
    }
}

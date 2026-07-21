<?php
require_once 'model/transferencia_externa.php';
require_once 'model/cierre.php';
require_once 'model/usuario.php';


class transferencia_externaController
{
    private $model;
    private $cierre;
    private $usuario;

    public function __CONSTRUCT()
    {
        $this->model = new transferencia_externa();
        $this->cierre = new cierre();
        $this->usuario = new usuario();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_externa/transferencia_externa.php';
        require_once 'view/footer.php';
    }

    public function Nuevo()
    {
        require_once 'view/header.php';
        require_once 'view/transferencia_externa/transferencia_externa-nuevo.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $transf = new transferencia_externa();
        if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0) {
            $transf = $this->model->Obtener($_REQUEST['id']);
        }
        require_once 'view/transferencia_externa/transferencia_externa-nuevo.php';
    }

    public function Guardar()
    {
        if (!isset($_SESSION)) session_start();
        
        $user_name = 'Usuario Web';
        if (isset($_SESSION['user_id'])) {
            $user_obj = $this->usuario->Obtener($_SESSION['user_id']);
            if ($user_obj) {
                $user_name = $user_obj->user;
            }
        }
        
        $transf = new transferencia_externa();
        $transf->id = $_REQUEST['id'] ?? null;
        $transf->quien_transfiere = $user_name; 
        $transf->monto = $_REQUEST['monto'];
        $transf->concepto = $_REQUEST['concepto'];
        $transf->fecha_envio = date('Y-m-d');
        $transf->hora_envio = date('H:i:s');

        // Handle file upload for comprobante
        $text_url = $_REQUEST['comprobante'] ?? '';
        $file_url = '';

        // If editing, retrieve existing file URL first so we don't lose it
        if ($transf->id > 0) {
            $old_record = $this->model->Obtener($transf->id);
            if ($old_record) {
                if (strpos($old_record->comprobante_url, '|') !== false) {
                    list($old_text, $old_file) = explode('|', $old_record->comprobante_url, 2);
                    $file_url = $old_file;
                } elseif (strpos($old_record->comprobante_url, '/transferencias/comprobante_') !== false) {
                    $file_url = $old_record->comprobante_url;
                }
            }
        }

        if (isset($_FILES['comprobante_file']) && $_FILES['comprobante_file']['name'] != '') {
            $archivo = $_FILES['comprobante_file']['name'];
            $temp = $_FILES['comprobante_file']['tmp_name'];
            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
            
            // Check allowed extensions
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            if (in_array($ext, $allowed_exts)) {
                $nuevo_nombre = 'comprobante_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                
                $upload_dir = 'assets/img/transferencias/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                if (move_uploaded_file($temp, $upload_dir . $nuevo_nombre)) {
                    chmod($upload_dir . $nuevo_nombre, 0777);
                    
                    // Generate full/absolute URL
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    $base_url = $protocol . $_SERVER['HTTP_HOST'] . "/" . explode("/", $_SERVER['REQUEST_URI'])[1] . "/";
                    $file_url = $base_url . $upload_dir . $nuevo_nombre;
                }
            }
        }
        
        if ($file_url != '') {
            $transf->comprobante_url = $text_url . '|' . $file_url;
        } else {
            $transf->comprobante_url = $text_url;
        }

        if ($transf->id > 0) {
            $this->model->Actualizar($transf);
            $accion = "Modificado";
        } else {
            $this->model->Registrar($transf);
            $accion = "Agregado";
        }

        header('Location: index.php?c=transferencia_externa&success=' . $accion);
    }
}

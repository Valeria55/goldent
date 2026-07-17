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
        
        $transf->quien_transfiere = $user_name; 
        $transf->monto = $_REQUEST['monto'];
        $transf->concepto = $_REQUEST['concepto'];
        $transf->fecha_envio = date('Y-m-d');
        $transf->hora_envio = date('H:i:s');
        $transf->comprobante_url = $_REQUEST['comprobante'] ?? '';

        $this->model->Registrar($transf);

        header('Location: index.php?c=transferencia_externa');
    }
}
?>

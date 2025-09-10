<?php
require_once 'model/usuario.php';
require_once 'model/sucursal.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';

class usuarioController
{

    private $model;

    public function __CONSTRUCT()
    {
        $this->model = new usuario();
        $this->sucursal = new sucursal();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }

    public function Index()
    {
        if (!isset($_SESSION)) session_start();
        if ($_SESSION['nivel'] == 1) {
            require_once 'view/header.php';
            require_once 'view/usuario/usuario.php';
            require_once 'view/footer.php';
        } else {
            echo "No tienes permiso";
        }
    }


    public function Listar()
    {
        require_once 'view/usuario/usuario.php';
    }

    public function Crud()
    {
        $usuario = new usuario();

        if (isset($_REQUEST['id'])) {
            $usuario = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/usuario/usuario-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $usuario = new usuario();

        if (isset($_REQUEST['id'])) {
            $usuario = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/usuario/usuario-editar.php';
    }

    public function Password()
    {
        require_once 'view/header.php';
        require_once 'view/usuario/cambiar_pass.php';
        require_once 'view/footer.php';
    }

    public function Validar()
    {
        if (!isset($_SESSION)) session_start();
        $usuario = $this->model->Obtener($_SESSION["user_id"]);

        if ($usuario->pass == $_REQUEST["pass"]) {
            echo "true";
        } else {
            echo "false";
        }
    }
    public function ChangePassword()
    {
        $usuario = new usuario();
        if (!isset($_SESSION)) session_start();
        $usuario->id = $_SESSION["user_id"];
        $usuario->pass = $_REQUEST['pass'];


        $this->model->ChangePass($usuario);

        session_destroy();
        header('Location: index.php');
    }

    public function Guardar()
    {
        $usuario = new usuario();

        $usuario->id = $_REQUEST['id'];
        $usuario->user = $_REQUEST['user'];
        $usuario->pass = $_REQUEST['pass'];
        $usuario->nivel = $_REQUEST['nivel'];
        $usuario->comision = $_REQUEST['comision'];
        $usuario->sucursal = $_REQUEST['sucursal'];


        $usuario->id > 0
            ? $this->model->Actualizar($usuario)
            : $this->model->Registrar($usuario);

        $usuario->id > 0
            ? $accion = "Modificado"
            : $accion = "Agregado";;

        header('Location: index.php?success=' . $accion . '&c=' . $_REQUEST['c']);
    }

    public function Eliminar()
    {
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c=' . $_REQUEST['c']);
    }
}

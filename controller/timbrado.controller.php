<?php
require_once 'model/timbrado.php';
require_once 'model/cierre.php';

class timbradoController
{
    private $model;
    private $cierre;

    public function __CONSTRUCT()
    {
        $this->model = new timbrado();
        $this->cierre = new cierre();
    }

    public function Index()
    {
        require_once 'view/header.php';
        require_once 'view/timbrado/timbrado.php';
        require_once 'view/footer.php';
    }

    public function Listar()
    {
        require_once 'view/timbrado/timbrado.php';
    }

    public function Crud()
    {
        $timbrado = new timbrado();

        if (isset($_REQUEST['id'])) {
            $timbrado = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/header.php';
        require_once 'view/timbrado/timbrado-editar.php';
        require_once 'view/footer.php';
    }

    public function Obtener()
    {
        $timbrado = new timbrado();

        if (isset($_REQUEST['id'])) {
            $timbrado = $this->model->Obtener($_REQUEST['id']);
        }

        require_once 'view/timbrado/timbrado-editar.php';
    }

    public function Guardar()
    {
        $timbrado = new timbrado();

        $timbrado->id = $_REQUEST['id'] ?? null;
        $timbrado->timbrado = $_REQUEST['timbrado'];
        $timbrado->fecha_inicio = $_REQUEST['fecha_inicio'];
        $timbrado->fecha_fin = $_REQUEST['fecha_fin'];
        $timbrado->numero_inicio = $_REQUEST['numero_inicio'];
        $timbrado->numero_fin = $_REQUEST['numero_fin'];
        $timbrado->establecimiento = $_REQUEST['establecimiento'];
        $timbrado->punto_expedicion = $_REQUEST['punto_expedicion'];
        $timbrado->estado = $_REQUEST['estado'] ?? 0;

        if ($timbrado->id > 0) {
            $this->model->Actualizar($timbrado);
            $accion = "Modificado";
        } else {
            $this->model->Registrar($timbrado);
            $accion = "Agregado";
        }

        header('Location: index.php?success=' . $accion . '&c=timbrado');
    }

    public function Activar()
    {
        if (isset($_REQUEST['id'])) {
            $this->model->Activar($_REQUEST['id']);
            $accion = "Activado";
        }
        header('Location: index.php?success=' . $accion . '&c=timbrado');
    }

    public function Eliminar()
    {
        if (isset($_REQUEST['id'])) {
            $this->model->Eliminar($_REQUEST['id']);
            $accion = "Eliminado";
        }
        header('Location: index.php?success=' . $accion . '&c=timbrado');
    }
}

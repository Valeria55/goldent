<?php
// Se requieren los modelos necesarios: marca, cierre, cliente.
require_once 'model/gastos_fijos.php';
require_once 'model/cierre.php';


class gastos_fijosController{
    
    private $model;
    private $cierre;
    private $gastos_fijos;
    
    public function __CONSTRUCT(){
        // Se crea una instancia del modelo "marca" y se almacena en la propiedad privada $model.
        $this->model = new gastos_fijos();
        $this->cierre = new cierre();
        $this->gastos_fijos = new gastos_fijos();
}
public function Index(){
    require_once 'view/header.php';
    require_once 'view/gastos_fijos/gastos_fijos.php';
    require_once 'view/footer.php';
}
public function Listar(){
    require_once 'view/gastos_fijos/gastos_fijos.php';
}
public function Detalles()
    {
        require_once 'view/gastos_fijos/gastos_fijos_detalles.php';
    }
    
public function Cuentasapagarpdf()
    {
        require_once 'view/informes/cuentasapagar.php';
    }
    public function Cierre()
    {

        require_once 'view/informes/cierrepdf.php';
    }

public function Crud(){
    $gastos_fijos = new gastos_fijos();
    
    if(isset($_REQUEST['id'])){
        $gastos_fijos = $this->model->Obtener($_REQUEST['id']);
    }
    
    require_once 'view/header.php';
    require_once 'view/gastos_fijos/gastos_fijos_editar.php';
    require_once 'view/footer.php';
}

public function Obtener(){
    $gastos_fijos = new gastos_fijos();
    
    if(isset($_REQUEST['id'])){
        $gastos_fijos = $this->model->Obtener($_REQUEST['id']);
    }
    
    require_once 'view/gastos_fijos/gastos_fijos_editar.php';
    
}

public function Guardar(){
    $gastos_fijos = new gastos_fijos();
    
    $gastos_fijos->id = $_REQUEST['id'];
    $gastos_fijos->descripcion = $_REQUEST['descripcion'];
    $gastos_fijos->monto = $_REQUEST['monto'];
    $gastos_fijos->fecha = $_REQUEST['fecha'];

  

    $gastos_fijos->id > 0 
        ? $this->model->Actualizar($gastos_fijos)
        : $this->model->Registrar($gastos_fijos);
        
        $gastos_fijos->id > 0 
        ? $accion = "Modificado"
        : $accion = "Agregado";;

    //header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    header('Location:' . getenv('HTTP_REFERER'));

}

public function Eliminar(){
    $this->model->Eliminar($_REQUEST['id']);
    header('Location:' . getenv('HTTP_REFERER'));
}
}

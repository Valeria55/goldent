<?php

require_once 'model/cuenta.php';
require_once 'model/cierre.php';
require_once 'model/cliente.php';

class cuentaController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model = new cuenta();
        $this->cierre = new cierre();
        $this->cliente = new cliente();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/cuenta/cuenta.php';
        require_once 'view/footer.php';
       
    }


    public function Listar(){
        require_once 'view/cuenta/cuenta.php';
    }


    
    public function Crud(){
        $cuenta = new cuenta();
        
        if(isset($_REQUEST['id'])){
            $cuenta = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/header.php';
        require_once 'view/cuenta/cuenta-editar.php';
        require_once 'view/footer.php';
    }
    
    public function Obtener(){
        $cuenta = new cuenta();
        
        if(isset($_REQUEST['id'])){
            $cuenta = $this->model->Obtener($_REQUEST['id']);
        }
        
        require_once 'view/cuenta/cuenta-editar.php';
        
    }
    
    public function Guardar(){
        $cuenta = new cuenta();
        
        $cuenta->id = $_REQUEST['id'];
        $cuenta->id_cliente = $_REQUEST['id_cliente'];
        $cuenta->fecha_emitida = $_REQUEST['fecha_emitida'];
        $cuenta->fecha_pagada = $_REQUEST['fecha_pagada'];
        $cuenta->comprobante = $_REQUEST['comprobante'];
        $cuenta->nro_comprobante = $_REQUEST['nro_comprobante'];
        $cuenta->monto = $_REQUEST['monto'];
        $cuenta->saldo = $_REQUEST['saldo'];
        $cuenta->estado = $_REQUEST['estado'];


      

        $cuenta->id > 0 
            ? $this->model->Actualizar($cuenta)
            : $this->model->Registrar($cuenta);
            
        $cuenta->id > 0 
            ? $accion = "Modificado"
            : $accion = "Agregado";;
        
        header('Location: index.php?success='.$accion.'&c='.$_REQUEST['c']);
    }
    
    public function Eliminar(){
        $this->model->Eliminar($_REQUEST['id']);
        header('Location: index.php?success=Eliminado&c='.$_REQUEST['c']);
    }
}
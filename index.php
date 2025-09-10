<?php
error_reporting(0);

session_start();
$sistema = $_SERVER['HTTP_HOST']."/".explode("/", $_SERVER['REQUEST_URI'])[1]; //direccion del sistema
if($_SESSION["validar"] != "true" || $sistema != $_SESSION['sistema']){
    header("location: login.php");  
    exit();  
}
require_once 'model/database.php';
date_default_timezone_set('America/Asuncion');

$controller = 'venta_tmp';
// error_reporting(0);
// 
// Todo esta lógica hara el papel de un FrontController
if(!isset($_REQUEST['c']))
{
    require_once "controller/$controller.controller.php";
    $controller = ucwords($controller) . 'Controller';

    $controller = new $controller;
    $controller->Index();    
}
else
{
    // Obtenemos el controlador que queremos cargar
    $controller = strtolower($_REQUEST['c']);
    $accion = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'Index';
    
    // Instanciamos el controlador
    require_once "controller/$controller.controller.php";
    $controller = ucwords($controller) . 'Controller';
    $controller = new $controller;
    
    // Llama la accion
    call_user_func( array( $controller, $accion ) );
}
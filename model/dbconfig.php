<?php

/*
|--------------------------------------------------------------------------
| COMPATIBILIDAD CON CÓDIGO ANTIGUO
|--------------------------------------------------------------------------
| database.php contiene la configuración y la conexión PDO principal.
*/

require_once __DIR__ . '/database.php';


/*
|--------------------------------------------------------------------------
| VARIABLES ANTIGUAS
|--------------------------------------------------------------------------
| Se mantienen porque puede haber archivos viejos que utilicen
| $DB_HOST, $DB_USER, $DB_PASS o $DB_NAME.
*/

$DB_HOST = DB_HOST;
$DB_USER = DB_USER;
$DB_PASS = DB_PASS;
$DB_NAME = DB_NAME;


/*
|--------------------------------------------------------------------------
| PDO
|--------------------------------------------------------------------------
| NO se crea otro "new PDO".
| Se reutiliza la conexión central de Database.
*/

$DB_con = Database::StartUp();


/*
|--------------------------------------------------------------------------
| MYSQLI
|--------------------------------------------------------------------------
| Se mantiene para código antiguo que todavía utiliza $con.
|
| El prefijo p: habilita conexión persistente en mysqli.
*/

$con = mysqli_connect(
    'p:' . DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

if (!$con) {

    error_log(
        'Error de conexión MySQLi: ' . mysqli_connect_error()
    );

    throw new Exception('No se pudo conectar a la base de datos.');
}


/*
|--------------------------------------------------------------------------
| CHARSET
|--------------------------------------------------------------------------
*/

mysqli_set_charset($con, 'utf8mb4');


/*
|--------------------------------------------------------------------------
| TIEMPO MÁXIMO DE CONSULTA
|--------------------------------------------------------------------------
| Mantener solamente si Hostinger pidió específicamente los 10 segundos.
*/

try {
    mysqli_query(
        $con,
        "SET SESSION max_statement_time = 10"
    );
} catch (Throwable $e) {
    // No interrumpir el sistema.
}


	
	


	
	

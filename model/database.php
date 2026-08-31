<?php
// define("USER", "u832567584_golden");
// define("PASS", "q918K/S8");
// define("DB", "u832567584_golden");
// define("HOST", "localhost");


// class Database
// {
//     private static $pdo = null;

//     public static function StartUp()
//     {
//         if (self::$pdo === null) {

//             self::$pdo = new PDO(
//                 'mysql:host=' . HOST . ';dbname=' . DB . ';charset=utf8',
//                 USER,
//                 PASS,
//                 [
//                     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//                     PDO::ATTR_PERSISTENT => true
//                 ]
//             );

//             try {
//                 self::$pdo->exec("SET SESSION max_statement_time = 10;");
//             } catch (Exception $e) {
//             }
//         }

//         return self::$pdo;
//     }
// }


/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN DE BASE DE DATOS
|--------------------------------------------------------------------------
| Cambiar solamente estos cuatro valores según cada sistema.
| IMPORTANTE: usar una contraseña nueva porque la actual quedó expuesta
| en las capturas.
*/

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'u832567584_golden');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', 'q918K/S8');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'u832567584_golden');
}


class Database
{
    private static $pdo = null;

    public static function StartUp()
    {
        if (self::$pdo === null) {

            try {

                self::$pdo = new PDO(
                    'mysql:host=' . DB_HOST .
                    ';dbname=' . DB_NAME .
                    ';charset=utf8mb4',
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT         => true
                    ]
                );

                /*
                 * Si Hostinger indicó expresamente limitar las consultas
                 * a 10 segundos, dejar esto.
                 */
                try {
                    self::$pdo->exec(
                        "SET SESSION max_statement_time = 10"
                    );
                } catch (PDOException $e) {
                    // No interrumpir el sistema si el servidor no permite cambiarlo.
                }

            } catch (PDOException $e) {

                error_log(
                    'Error de conexión PDO: ' . $e->getMessage()
                );

                throw $e;
            }
        }

        return self::$pdo;
    }
}
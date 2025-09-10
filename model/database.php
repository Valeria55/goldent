<?php
define("USER", "u832567584_tienda");
define("PASS", "Trinity..2021");
define("DB", "u832567584_tienda");
define("HOST", "localhost");

define("USER_TALLER", "u832567584_tallerscorecar");
define("PASS_TALLER", "Trinity..2021");
define("DB_TALLER", "u832567584_tallerscorecar");
define("HOST_TALLER", "localhost");

class Database
{
    public static function StartUp()
    {
        $pdo = new PDO('mysql:host=' . HOST . ';dbname=' . DB . ';charset=utf8', USER, PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public static function StartUp_taller()
    {
        try {
            $pdo = new PDO('mysql:host=' . HOST_TALLER . ';dbname=' . DB_TALLER . ';charset=utf8', USER_TALLER, PASS_TALLER);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
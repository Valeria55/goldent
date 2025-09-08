<?php
define("USER", "u832567584_golden");
define("PASS", "x=3Tm2&p");
define("DB", "u832567584_golden");
define("HOST", "localhost");

class Database
{
    public static function StartUp()
    {
        $pdo = new PDO('mysql:host=' . HOST . ';dbname=' . DB . ';charset=utf8', USER, PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
<?php
require_once 'model/database.php';
header('Content-Type: text/plain');
try {
    $pdo = Database::StartUp();
    $users = $pdo->query("SELECT id, user, nombre, nivel FROM usuario")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        echo "ID: {$u['id']} | User: {$u['user']} | Nombre: {$u['nombre']} | Nivel: {$u['nivel']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

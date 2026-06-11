<?php
require_once 'model/database.php';
try {
    $pdo = Database::StartUp();
    $rows = $pdo->query("SELECT * FROM timbrados")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        echo "ID: {$row['id']} | Timbrado: {$row['timbrado']} | Inicio: '{$row['fecha_inicio']}' | Fin: '{$row['fecha_fin']}' | Estado: {$row['estado']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
unlink(__FILE__);

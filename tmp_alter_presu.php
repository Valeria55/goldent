<?php
require_once 'model/database.php';
$pdo = Database::StartUp();
try {
    $pdo->exec("ALTER TABLE presupuestos ADD COLUMN id_adelanto INT NULL");
    echo "Column id_adelanto added to presupuestos table\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

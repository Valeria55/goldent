<?php
require_once 'model/database.php';
$pdo = Database::StartUp();
$q = $pdo->query("DESCRIBE presupuestos");
while($row = $q->fetch(PDO::FETCH_ASSOC)) {
    echo "'" . $row['Field'] . "'\n";
}
?>

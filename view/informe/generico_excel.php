<?php
$tipo = $_REQUEST['tipo'];
$desde = date("d/m/Y", strtotime($_REQUEST["desde"]));
$hasta = date("d/m/Y", strtotime($_REQUEST["hasta"]));

?>
<meta charset="utf-8">
<table border="1">
    <thead>
        <tr>
            <th style="background-color: #348993; color: white;">GOLDENT S.A - Informe de <?php echo ucfirst($tipo); ?></th>
        </tr>
        <tr>
            <th style="text-align: left;">Rango: <?php echo $desde; ?> a <?php echo $hasta; ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Este tipo de informe (<?php echo $tipo; ?>) no tiene una vista de Excel específica configurada. Por favor, contacte a soporte si necesita el formato detallado para este reporte.</td>
        </tr>
    </tbody>
</table>

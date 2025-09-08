<h1 class="page-header">Movimientos<?php
$desde = (isset($_POST["desde"])) ? $_POST["desde"] : "";
$hasta = (isset($_POST["hasta"])) ? $_POST["hasta"] : "";

$metodo = $_GET['metodo'];
echo "<a href='?c=metodo&a=informeMetodo&metodo=$metodo&desde=$desde&hasta=$hasta' class='btn btn-info'>Imprimir</a>"; ?></h1>

<h1 class="page-header" style="font-size: 25px; border-bottom: none; color: black; "> Detalles Saldo Anterior</h1>
<?php
$saldoAnterior = $this->model->SaldoAnterior($_GET['metodo'], $desde);

// Variables para almacenar los totales
$totalIngresos = 0;
$totalCredito = 0;
$totalDebito = 0;
$totalEgresos = 0;

if (!empty($saldoAnterior)) {
    foreach ($saldoAnterior as $saldo) {
        // Sumar los valores a los totales
        $totalIngresos += $saldo->ingresos;
        $totalCredito += $saldo->credito;
        $totalDebito += $saldo->debito;
        $totalEgresos += $saldo->egresos;

        echo "<p style='color: black; font-size: 16px;'><strong>Ingresos:</strong> " . number_format($saldo->ingresos, 0, '.', ',') . "</p>";
        echo "<p style='color: black; font-size: 16px;'><strong>Crédito:</strong> " . number_format($saldo->credito, 0, '.', ',') . "</p>";
        echo "<p style='color: black; font-size: 16px;'><strong>Débito:</strong> " . number_format($saldo->debito, 0, '.', ',') . "</p>";
        echo "<p style='color: black; font-size: 16px;'><strong>Egresos:</strong> " . number_format($saldo->egresos, 0, '.', ',') . "</p>";
    }
} else {
    echo "<p style='color: black; font-size: 18px;'>No hay registros de saldo anterior.</p>";
}

// Calcular el saldo total
$saldoTotalAnterior = $totalIngresos - $totalEgresos - $totalCredito - $totalDebito;

// Mostrar el saldo total Anteior
echo "<p style='font-size: 20px; color: black;'><strong>Saldo Total Anterior:</strong> " . number_format($saldoTotalAnterior, 0, '.', ',') . "</p>";
?>


<br><br><br>
<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="post">
                <div class="form-group col-md-2">
                    <label></label>

                </div>
                <div class="form-group col-md-2">
                    <label></label>

                </div>
                <div class="form-group col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_POST['desde'])) ? $_POST['desde'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_POST['hasta'])) ? $_POST['hasta'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-2" style="margin-top: 25px;">
                    <label></label>
                    <input type="submit" name="filtro" value="Filtrar" class="btn btn-success">
                </div>

            </form>
        </div>
    </div>
</div>
<p> </p>
<table class="table table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: #cccccc; color:black">
            <th>N°</th>
            <th>Fecha</th>
            <th>Persona</th>
            <th>Concepto</th>
            <th>N°comprobante</th>
            <th>Ingreso</th>
            <th>-%</th>
            <th>Egreso</th>
            <th>Moneda</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sumaTotal = 0;
        $c = 1;
        $sumin_gs = 0;
        $sumeg_gs = 0;
        $i_descuento = 0;
        $monto_descuento = 0;
        $suma_descuento = 0;
        foreach ($this->model->ListarMovimientos($_GET['metodo'], $desde, $hasta) as $r) :

            if (strlen($r->concepto) >= 30) {
                $concepto = substr($r->concepto, 0, 30) . "...";
            } else {
                $concepto = $r->concepto;
            }

            if ($r->monto > 0) {
                $egreso = "";
                $ingreso = number_format(($r->monto), 0, ".", ",");
                $sumin_gs += $r->monto;

                //  Quitar monto a los ingresos 
                if ($r->tarjeta == 'CREDITO') {
                    $descuento = 5.2 . '%';
                    $i_descuento = $r->monto * 0.052;
                    $monto_descuento = number_format(($i_descuento), 0, ".", ",");
                } elseif ($r->tarjeta == 'DEBITO') {
                    $descuento = 3.3 . '%';
                    $i_descuento = $r->monto * 0.033;
                    $monto_descuento = number_format(($i_descuento), 0, ".", ",");
                } else {
                    $descuento = 0;
                    $i_descuento = 0;
                    $monto_descuento = number_format(($i_descuento), 0, ".", ",");
                }
            } else {
                $descuento = "";
                $ingreso = "";
                $i_descuento = 0;
                $monto_descuento = "";
                $egreso = number_format(($r->monto), 0, ".", ",");
                $sumeg_gs += ($r->monto);
            }

        ?>
            <tr class="click" <?php if ($r->anulado) {
                                    echo "style='color:red'";
                                } elseif ($r->descuento > 0) {
                                    echo "style='color:#F39C12'";
                                } ?>>
                <td><?php echo $c++; ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                <td><?php echo $r->persona; ?></td>
                <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
                <td><?php echo $r->comprobante . ' (' . $r->nro_comprobante . ').'; ?></td>
                <td><?php echo $ingreso; ?></td>
                <td><?php echo $monto_descuento; ?></td>
                <td><?php echo $egreso; ?></td>
                <td><?php echo $r->moneda; ?></td>
                <td><?php echo $descuento . '-' . $r->tarjeta; ?></td>
            </tr>
        <?php $sumaTotal += $r->monto;
            $suma_descuento += $i_descuento;

        endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #dddddd; ">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th><?php echo number_format($sumin_gs, 0, ".", ","); ?></th>
            <th><?php echo number_format($suma_descuento, 0, ".", ","); ?></th>
            <th><?php echo number_format($sumeg_gs, 0, ".", ","); ?></th>
            <th><?php echo number_format($sumin_gs - $suma_descuento + $sumeg_gs, 0, ".", ","); ?></th>
            <th></th>
        </tr>


    </tfoot>
</table>
</div>
</div>
</div>
<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
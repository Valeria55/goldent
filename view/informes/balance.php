<?php
$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$fecha = date('Y-m-d');
?>
<a class="btn btn-primary" href="#mesModal" class="btn btn-primary" data-toggle="modal" data-target="#mesModal">Informe Mensual</a>
<h1 align="center">Balance</h1>

<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="post" action="?c=ingreso&a=balance">
                <div class="form-group col-md-3">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>Año</label>
                    <select name="anho" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control">
                        <option value=0> Todos</option>
                        <?php for ($i = 2019; $i <= date("Y"); $i++) { ?>
                            <option value="<?php echo $i ?>" <?php if (isset($_GET['anho']) && $_GET['anho'] == ($i)) echo 'selected' ?>><?php echo $i ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label></label>
                    <input type="submit" name="filtro" value="Filtrar" class="btn btn-success">
                </div>

            </form>
        </div>
    </div>
</div>
<p> </p>
</table>

<hr />

<div class="col-sm-6" align="center" style="border-right: 1px solid #d6d6d6">
    <div class="content">
        <h2 class="page-header">Lista de ingresos </h2>
        <br><br><br>
        <table class="table table-striped table-bordered display responsive nowrap tablas datatable" width="100%">
            <thead>
                <tr style="background-color: black; color:#fff">
                    <th>En concepto de</th>
                    <th>Metodo</th>
                    <th>Monto (Gs)</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ingreso = 0;
                $ingresos_moneda = array();
                // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                foreach (($this->model->ListarBalance(($_POST['desde'] ?? ''), ($_POST['hasta'] ?? ''), ($_POST['anho'] ?? ''))) as $r) : ?>
                    <tr class="click">
                        <?php
                        // Evitar división por cero para mostrar en guaranies
                        $cambio = ($r->cambio && $r->cambio > 0) ? $r->cambio : 1;
                        $monto_guaranies = $r->monto * $cambio;
                        $ingreso = ($ingreso + $monto_guaranies);
                        
                        // Agrupar por moneda ORIGINAL (sin convertir)
                        $moneda = $r->moneda ?? 'Guaranies';
                        if (!isset($ingresos_moneda[$moneda])) {
                            $ingresos_moneda[$moneda] = 0;
                        }
                        $ingresos_moneda[$moneda] += $r->monto; // Monto original sin convertir
                        ?>
                        <td><?php echo $r->categoria; ?></td>
                        <td><?php echo $r->forma_pago ?? 'N/A'; ?></td>
                        <td><?php echo number_format($monto_guaranies, 0, ",", "."); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tr style="font-size: 16px">
                <td></td>
                <td align="right"><b>Total : </b></td>
                <td><b><?php echo number_format($ingreso, 0, ",", "."); ?></b></td>
                <td></td>
            </tr>
            <tr style="font-size: 14px; background-color: #f8f9fa;">
                <td colspan="4" align="left">
                    <b>Ingresos según moneda:</b><br>
                    <?php
                    // Ordenar las monedas para mostrar siempre en el mismo orden
                    $monedas_ordenadas = array();

                    // Agrupar por tipo de moneda
                    foreach ($ingresos_moneda as $moneda => $total) {
                        $moneda_lower = strtolower($moneda ?? '');
                        if (strpos($moneda_lower, 'dolar') !== false || strpos($moneda_lower, 'usd') !== false) {
                            $monedas_ordenadas['Dolares'] = ($monedas_ordenadas['Dolares'] ?? 0) + $total;
                        } elseif (strpos($moneda_lower, 'real') !== false || strpos($moneda_lower, 'rs') !== false) {
                            $monedas_ordenadas['Reales'] = ($monedas_ordenadas['Reales'] ?? 0) + $total;
                        } else {
                            // Cualquier otra moneda o null se considera Guaranies
                            $monedas_ordenadas['Guaranies'] = ($monedas_ordenadas['Guaranies'] ?? 0) + $total;
                        }
                    }

                    // Mostrar en orden específico
                    $orden = ['Guaranies', 'Dolares', 'Reales'];
                    foreach ($orden as $tipo_moneda) {
                        if (isset($monedas_ordenadas[$tipo_moneda])) {
                            echo $tipo_moneda . ': ' . number_format($monedas_ordenadas[$tipo_moneda], 0, ",", ".") . '<br>';
                        }
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="col-sm-6" align="center">
    <div class="content">
        <h2 class="page-header">Lista de egresos </h2>
        <br><br><br>
        <table class="table table-striped table-bordered display responsive nowrap tablas datatable" width="100%">
            <thead>
                <tr style="background-color: black; color:#fff">
                    <th>Concepto</th>
                    <th>Monto (Gs)</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $egreso = 0;
                $egresos_moneda = array();
                // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                foreach (($this->egreso->ListarBalance(($_POST['desde'] ?? ''), ($_POST['hasta'] ?? ''), ($_POST['anho'] ?? ''))) as $r): ?>
                    <tr class="click">
                        <?php
                        // Evitar división por cero para mostrar en guaranies
                        $cambio = ($r->cambio && $r->cambio > 0) ? $r->cambio : 1;
                        $monto_guaranies = $r->monto * $cambio;
                        $egreso = ($egreso + $monto_guaranies);
                        
                        // Agrupar por moneda ORIGINAL (sin convertir)
                        $moneda = $r->moneda ?? 'Guaranies';
                        if (!isset($egresos_moneda[$moneda])) {
                            $egresos_moneda[$moneda] = 0;
                        }
                        $egresos_moneda[$moneda] += $r->monto; // Monto original sin convertir
                        ?>
                        <td><?php echo $r->categoria; ?></td>
                        <td><?php echo number_format($monto_guaranies, 0, ",", "."); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tr style="font-size: 16px">
                <td align="right"><b>Total : </b></td>
                <td><b><?php echo number_format($egreso, 0, ",", "."); ?></b></td>
                <td></td>
            </tr>
            <tr style="font-size: 14px; background-color: #f8f9fa;">
                <td colspan="3" align="left">
                    <b>Egresos según moneda:</b><br>
                    <?php
                    // Agrupar por tipo de moneda para egresos también
                    $egresos_ordenados = array();

                    foreach ($egresos_moneda as $moneda => $total) {
                        $moneda_lower = strtolower($moneda ?? '');
                        if (strpos($moneda_lower, 'dolar') !== false || strpos($moneda_lower, 'usd') !== false) {
                            $egresos_ordenados['Dolares'] = ($egresos_ordenados['Dolares'] ?? 0) + $total;
                        } elseif (strpos($moneda_lower, 'real') !== false || strpos($moneda_lower, 'rs') !== false) {
                            $egresos_ordenados['Reales'] = ($egresos_ordenados['Reales'] ?? 0) + $total;
                        } else {
                            // Cualquier otra moneda o null se considera Guaranies
                            $egresos_ordenados['Guaranies'] = ($egresos_ordenados['Guaranies'] ?? 0) + $total;
                        }
                    }

                    // Mostrar en orden específico
                    foreach ($orden as $tipo_moneda) {
                        if (isset($egresos_ordenados[$tipo_moneda])) {
                            echo $tipo_moneda . ': ' . number_format($egresos_ordenados[$tipo_moneda], 0, ",", ".") . '<br>';
                        }
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<hr />

<div class="col-sm-6" align="center" style="border-right: 1px solid #d6d6d6">
    <div class="content">

        <h2 class="page-header">Lista de deudores </h2>
        <br><br><br>
        <table class="table table-striped table-bordered display responsive nowrap tablas datatable" width="100%">

            <thead>
                <tr style="background-color: black; color:#fff">
                    <th>Cliente</th>
                    <th>En concepto de</th>
                    <th>Monto (Gs)</th>
                    <th>Saldo (Gs)</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $deudor = 0;
                $deudors = 0;
                // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                foreach (($this->deuda->AgrupadoMes(($_POST['desde'] ?? ''), ($_POST['hasta'] ?? ''), ($_POST['anho'] ?? ''))) as $r): ?>
                    <tr class="click">
                        <?php $deudor = ($deudor + $r->monto); ?>
                        <?php $deudors = ($deudors + $r->saldo); ?>
                        <td><?php echo $r->nombre; ?></td>
                        <td><?php echo $r->concepto; ?></td>
                        <td><?php echo number_format($r->monto, 0, ",", "."); ?></td>
                        <td><?php echo number_format($r->saldo, 0, ",", "."); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tr style="font-size: 16px">
                <td align="right"></td>
                <td align="right"><b>Total : </b></td>
                <td><b><?php echo number_format($deudor, 0, ",", "."); ?></b></td>
                <td><b><?php echo number_format($deudors, 0, ",", "."); ?></b></td>
            </tr>

        </table>
    </div>
</div>
<div class="col-sm-6" align="center" style="border-right: 1px solid #d6d6d6">
    <div class="content">

        <h2 class="page-header">Lista de acreedores </h2>
        <br><br><br>
        <table class="table table-striped table-bordered display responsive nowrap tablas datatable" width="100%">

            <thead>
                <tr style="background-color: black; color:#fff">
                    <th>Proveedor</th>
                    <th>Monto (Gs)</th>
                    <th>Saldo (Gs)</th>
                    <th>Concepto</th>
                    <th>Fecha</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $acreedor = 0;
                $acreedors = 0;
                // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                foreach (($this->acreedor->AgrupadoMes(($_POST['desde'] ?? ''), ($_POST['hasta'] ?? ''), ($_POST['anho'] ?? ''))) as $r): ?>
                    <tr class="click">
                        <?php $acreedor = ($acreedor + $r->monto); ?>
                        <?php $acreedors = ($acreedors + $r->monto); ?>
                        <td><?php echo $r->nombre; ?></td>
                        <td><?php echo number_format($r->monto, 0, ",", "."); ?></td>
                        <td><?php echo number_format($r->saldo, 0, ",", "."); ?></td>
                        <td><?php echo $r->concepto; ?></td>
                        <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tr style="font-size: 16px">
                <td align="right"><b>Total : </b></td>
                <td><b><?php echo number_format($acreedor, 0, ",", "."); ?></b></td>
                <td><b><?php echo number_format($acreedors, 0, ",", "."); ?></b></td>
                <td></td>
                <td></td>
            </tr>

        </table>
    </div>
</div>


<hr />

<div class="col-sm-6" align="center" style="border-right: 1px solid #d6d6d6">
    <div class="content">

        <h2 class="page-header">Lista de Stock </h2>
        <br><br><br>
        <table class="table table-striped table-bordered display responsive nowrap tablas datatable" width="100%">

            <thead>
                <tr style="background-color: black; color:#fff">
                    <th>producto</th>
                    <th>Precio costo</th>
                    <th>Precio min.</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $monto = 0;
                $stock = 0;
                $costo = 0;
                // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                foreach (($this->producto->ListarTodoBalance()) as $r): ?>
                    <tr class="click">
                        <?php $costo = ($costo + $r->precio_costo * $r->stock); ?>
                        <?php $monto = ($monto + $r->precio_minorista * $r->stock); ?>
                        <?php $stock = ($stock + $r->stock); ?>

                        <td><?php echo $r->producto; ?></td>
                        <td><?php echo number_format($r->precio_costo, 0, ",", "."); ?></td>
                        <td><?php echo number_format($r->precio_minorista, 0, ",", "."); ?></td>
                        <td><?php echo $r->stock; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tr style="font-size: 16px">
                <td align="right"><b>Total : </b></td>
                <td><b><?php echo number_format($costo, 0, ",", "."); ?></b></td>
                <td><b><?php echo number_format($monto, 0, ",", "."); ?></b></td>
                <td><b><?php echo $stock; ?></td>
            </tr>

        </table>
    </div>
</div>


<div class="col-sm-12">
    <?php $total = $ingreso - $egreso; ?>
    <h1 align="center"> Balance General: <?php echo number_format($total, 0, ",", "."); ?> (Gs)</h1>
</div>


<?php include("view/venta/mes-modal.php"); ?>
<?php include("view/crud-modal.php"); ?>

<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
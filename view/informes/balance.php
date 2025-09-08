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
<div class="row">
    <div class="col-sm-6" align="center" style="border-right: 1px solid #d6d6d6">
        <div class="content">
            <h2 class="page-header">Lista de ingresos </h2>
            <br><br><br>
            <table class="table table-striped table-bordered display responsive nowrap tablas datatable" width="100%">
                <thead>
                    <tr style="background-color: black; color:#fff">
                        <th>En concepto de</th>
                        <th>Metodo</th>
                        <th>Monto</th>
                        <th>Moneda</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ingreso = 0;
                    $sumin_gs = 0;
                    $sumin_us = 0;
                    $sumin_rs = 0;
                    // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                    foreach (($this->model->AgrupadoMes(($_POST['desde'] ?? ''), ($_POST['hasta'] ?? ''), ($_POST['anho'] ?? ''))) as $r) : ?>
                        <tr class="click">
                            <?php if ($r->moneda == 'GS') {
                                $sumin_gs += $r->monto;
                            } else if ($r->moneda == 'USD') {
                                $sumin_us += $r->monto;
                            } else if ($r->moneda == 'RS') {
                                $sumin_rs += $r->monto;
                            } ?>

                            <td><?php echo $r->categoria; ?></td>
                            <td><?php echo $r->forma_pago; ?></td>
                            <td><?php echo number_format($r->monto, 0, ".", ",");
                                echo (' ' . $r->moneda); ?></td>
                            <td><?php echo ($r->moneda); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <?php
                    $suma_total = array("Total USD" => "$sumin_us", "Total GS" => "$sumin_gs", "Total RS" => "$sumin_rs");
                    foreach ($suma_total as $nombre => $sumaT) :
                    ?>
                        <tr style="background-color: #dddddd; ">
                            <td></td>
                            <td align="right"><b><?php echo $nombre; ?></b></td>
                            <td><b><?php echo number_format($sumaT, 0, ",", "."); ?></b></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                </tfoot>


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
                        <th>Monto</th>
                        <th>Moneda</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $egreso = 0;
                    $sumeg_gs = 0;
                    $sumeg_us = 0;
                    $sumeg_rs = 0;
                    // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                    foreach (($this->egreso->AgrupadoMes(($_POST['desde'] ?? ''), ($_POST['hasta'] ?? ''), ($_POST['anho'] ?? ''))) as $r) :

                        if ($r->moneda == 'GS') {
                            $sumeg_gs += ($r->monto);
                        } else if ($r->moneda == 'USD') {
                            $sumeg_us += ($r->monto);
                        } else if ($r->moneda == 'RS') {
                            $sumeg_rs += ($r->monto);
                        }
                    ?>
                        <tr class="click">
                            <?php $egreso = ($egreso + $r->monto); ?>
                            <td><?php echo $r->categoria; ?></td>
                            <td><?php echo number_format($r->monto, 0, ".", ","); ?></td>
                            <td><?php echo $r->moneda ?></td>
                            <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <?php
                    $suma_total = array("Total USD" => "$sumeg_us", "Total GS" => "$sumeg_gs", "Total RS" => "$sumeg_rs");
                    foreach ($suma_total as $nombre => $sumaT) :
                    ?>
                        <tr style="background-color: #dddddd; ">
                            <td><b><?php echo $nombre; ?></b></td>
                            <td><b><?php echo number_format($sumaT, 0, ".", ","); ?></b></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                </tfoot>


            </table>
        </div>
    </div>
</div>

<hr />
<div class="row">
    <div class="col-sm-6" align="center" style="border-right: 1px solid #d6d6d6">
        <div class="content">

            <h2 class="page-header">Lista de deudores </h2>
            <br><br><br>
            <table class="table table-striped table-bordered display responsive nowrap tablas datatable" width="100%">

                <thead>
                    <tr style="background-color: black; color:#fff">
                        <th width="25%">Cliente</th>
                        <th width="15%">Monto </th>
                        <th width="15%">Saldo </th>
                        <th width="25%">Concepto</th>
                        <th width="20%">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $deudor = 0; //monto total de la deuda
                    $deudors = 0; 
                    // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                    foreach (($this->deuda->AgrupadoMes(($_POST['desde'] ?? ''), ($_POST['hasta'] ?? ''), ($_POST['anho'] ?? ''))) as $r) : ?>
                        <tr class="click">
                            <?php $deudor = ($deudor + $r->monto); ?>
                            <?php $deudors = ($deudors + $r->saldo); ?> 

                            <td><?php echo $r->nombre; ?></td>
                            <td><?php echo number_format($r->monto, 0, ".", ","); ?></td>
                            <td><?php echo number_format($r->saldo, 0, ".", ","); ?></td>
                            <td><?php echo $r->concepto; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #dddddd; ">
                        <td align="right"><b>Total : </b></td>
                        <td><b><?php echo number_format($deudor, 0, ",", "."); ?></b></td>
                        <td><b><?php echo number_format($deudors, 0, ",", "."); ?></b></td>
                        <td></td>
                        <td></td>
                    </tr>

                </tfoot>



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
                        <th>Monto </th>
                        <th>Saldo </th>
                        <th>Concepto</th>
                        <th>Fecha</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $acreedor = 0;
                    $acreedors = 0;
                    // $mes = (isset($_GET['m'])) ? $_GET['m'] : 0;
                    foreach (($this->acreedor->AgrupadoMes(($_POST['desde'] ?? ''), ($_POST['hasta'] ?? ''), ($_POST['anho'] ?? ''))) as $r) : ?>
                        <tr class="click">
                            <?php $acreedor = ($acreedor + $r->monto); ?>
                            <?php $acreedors = ($acreedors + $r->saldo); ?>
                            <td><?php echo $r->nombre; ?></td>
                            <td><?php echo number_format($r->monto, 0, ".", ","); ?></td>
                            <td><?php echo number_format($r->saldo, 0, ".", ","); ?></td>
                            <td><?php echo $r->concepto; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #dddddd; ">
                        <td align="right"><b>Total : </b></td>
                        <td><b><?php echo number_format($acreedor, 0, ",", "."); ?></b></td>
                        <td><b><?php echo number_format($acreedors, 0, ",", "."); ?></b></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>


            </table>
        </div>
    </div>

</div>


<hr />

<div class="col-sm-12" align="center">
    <div class="content">
        <h2 class="page-header">Lista de Stock </h2>
        <br><br><br>
        <table class="table table-striped table-bordered display responsive nowrap tablas datatable" width="100%">

            <thead>
                <tr style="background-color: black; color:#fff">
                    <th>Producto</th>
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
                foreach (($this->producto->ListarTodoBalance()) as $r) : 
                if($r->stock_s1 < 1) continue;
                ?>
                    <tr class="click">
                        <?php $costo = ($costo + $r->precio_costo * $r->stock_s1); ?>
                        <?php $monto = ($monto + $r->precio_minorista * $r->stock_s1); ?>
                        <?php $stock = ($stock + $r->stock_s1); ?>

                        <td><?php echo $r->producto; ?></td>
                        <td><?php echo number_format($r->precio_costo, 0, ".", ","); ?></td>
                        <td><?php echo number_format($r->precio_minorista, 0, ".", ","); ?></td>
                        <td><?php echo number_format($r->stock_s1, 0, ",", "."); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #dddddd; ">
                    <td align="right"><b>Total : </b></td>
                    <td><b><?php echo number_format($costo, 0, ",", "."); ?></b></td>
                    <td><b><?php echo number_format($monto, 0, ",", "."); ?></b></td>
                    <td><b><?php echo number_format($stock, 0, ",", "."); ?></td>
                </tr>
            </tfoot>


        </table>
    </div>
</div>


<div class="col-sm-12">
    <?php $total_us = $sumin_us - $sumeg_us ; ?>
    <?php $total_gs = $sumin_gs - $sumeg_gs; ?>
    <?php $total_rs = $sumin_rs- $sumeg_rs; ?>

    <h3 align="center"> Balance General en Dólares: <?php echo number_format($total_us, 2, ",", "."); ?> </h3>
    <h3 align="center"> Balance General en Guaraníes: <?php echo number_format($total_gs, 0, ",", "."); ?> </h3>
    <h3 align="center"> Balance General en Reales: <?php echo number_format($total_rs, 2, ",", "."); ?> </h3>
</div>


<?php include("view/venta/mes-modal.php"); ?>
<?php include("view/crud-modal.php"); ?>

<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
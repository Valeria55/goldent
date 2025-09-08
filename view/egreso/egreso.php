<h1 class="page-header">Lista de egresos <a class="btn btn-primary" href="#mesModal" data-toggle="modal" data-target="#mesModal" data-c="egreso">Informe</a></h1>
<a class="btn btn-primary pull-right" href="#egresoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="egreso">Agregar</a>

<br><br><br>
<h3 id="filtrar" align="center">Filtros <i class="fas fa-angle-down"></i><i class="fas fa-angle-up"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="post">
                <div class="form-group col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group  col-md-2">
                    <label>Categoria</label>
                    <select name="categoria" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione la categoria --" style="width:100%; display:0">
                        <?php foreach ($this->model->ListarCategoria() as $c) : ?>
                            <option value="<?php echo $c->categoria; ?>"><?php echo $c->categoria; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Nombre</label>
                    <select name="nombre" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione a la persona --" style="width:100%; display:0">
                        <?php foreach ($this->cliente->Listar() as $clie) : ?>
                            <option value="<?php echo $clie->id; ?>"><?php echo $clie->nombre; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Forma de pago</label>
                    <select name="metodo" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione a la persona --" style="width:100%; display:0">
                        <?php foreach ($this->metodo->Listar() as $m) : ?>
                            <option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-1">
                    <label>Tipo</label>
                    <select name="tipo_egreso" class="form-control">
                        <option value="" <?php echo ($_POST['tipo_egreso'] == "Sin seleccionar") ? "selected" : ""; ?>>Sin seleccionar</option>
                        <option value="COMPRA" <?php echo ($_POST['tipo_egreso'] == "COMPRA") ? "selected" : ""; ?>>COMPRA</option>
                        <option value="GASTO FIJO" <?php echo ($_POST['tipo_egreso'] == "GASTO FIJO") ? "selected" : ""; ?>>GASTO FIJO</option>
                        <option value="GASTO VARIABLE" <?php echo ($_POST['tipo_egreso'] == "GASTO VARIABLE") ? "selected" : ""; ?>>GASTO VARIABLE</option>
                        <option value="MOVIMIENTOS" <?php echo ($_POST['tipo_egreso'] == "MOVIMIENTOS") ? "selected" : ""; ?>>MOVIMIENTOS</option>
                    </select>
                </div>
                <div class="form-group col-md-1">
                    <label>Caja</label>
                    <select name="id_caja" class="form-control">
                        <option value="" <?php echo ($_POST['id_caja'] == "Sin seleccionar") ? "selected" : ""; ?>>Sin seleccionar</option>
                        <option value="1" <?php echo ($_POST['id_caja'] == "1") ? "selected" : ""; ?>>CAJA CHICA</option>
                        <option value="3" <?php echo ($_POST['id_caja'] == "3") ? "selected" : ""; ?>>TESORERIA</option>
                        <option value="2" <?php echo ($_POST['id_caja'] == "2") ? "selected" : ""; ?>>BANCO</option>
                    </select>
                </div>

                <div class="form-group col-md-12">
                    <label></label>
                    <center><input type="submit" name="filtro" value="Filtrar" class="btn btn-success"></center>
                </div>

            </form>
        </div>
    </div>
</div>
<p> </p>
<table class="table table-striped table-bordered display responsive datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Usuario</th>
            <th>Proveedor</th>
            <th>Fecha</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>N° de comprobante</th>
            <th>Monto</th>
            <th>Moneda</th>
            <th>Cambio</th>
            <th>Forma de pago</th>
            <th>Tipo</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $lista =  $this->model->Listar_rango($_POST['desde'], $_POST['hasta'], $_POST['nombre'], $_POST['categoria'], $_POST['metodo'], $_POST['tipo_egreso'], $_POST['id_caja']);

        foreach ($lista as $r) :
            
            // echo '<pre>'; var_dump($r); echo '</pre>';
            // $totall +=$r->monto;
            if (strlen($r->concepto) >= 20) {
                $concepto = substr($r->concepto, 0, 20) . "...";
            } else {
                $concepto = $r->concepto;
            }
            if (strlen($r->nombre) >= 10) {
                $nombre = substr($r->nombre, 0, 10) . "...";
            } else {
                $nombre = $r->nombre;
            }
            if (strlen($r->categoria) >= 10) {
                $categoria = substr($r->categoria, 0, 10) . "...";
            } else {
                $categoria = $r->categoria;
            }
            if (strlen($r->user) >= 5) {
                $user = substr($r->user, 0, 5) . "...";
            } else {
                $user = $r->user;
            }

            if (($r->moneda == 'GS') && ($r->anulado == NULL || $r->anulado == 0 )) {
                // echo $r->monto;
                $total_Gs = $total_Gs + ($r->monto);
            } else if (($r->moneda == 'RS') && ($r->anulado == NULL || $r->anulado == 0)) {
                $total_RS = $total_RS + ($r->monto);
            } else if (($r->moneda == 'USD') && ($r->anulado == NULL || $r->anulado == 0)) {
                $total_US = $total_US + ($r->monto);
            }

        ?>


            <tr class="click" <?php if ($r->anulado) {
                                    echo "style='color:gray'";
                                } ?>>
                <?php $total += $r->monto; ?>
                <td><?php echo $r->id; ?></td>
                <td title="<?php echo $r->user; ?>"><?php echo $user; ?></td>
                <td title="<?php echo $r->nombre; ?>"><?php echo ($r->id_cliente == 0) ? "SIN PROVEEDOR" : $r->nombre; ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                <td title="<?php echo $r->categoria; ?>"><?php echo $categoria; ?></td>
                <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
                <?php //echo '<pre>'; var_dump($r); echo '</pre>'; ?>
                <td><?php echo ($r->comprobante == NULL) ? "Sin comprobante N°" : $r->comprobante . ' N° ' . $r->nro_comprobante; ?></td>
                <td><?php echo number_format($r->monto, 0, ".", ","); ?></td>
                <td><?php echo $r->moneda; ?></td>
                <td><?php echo $r->cambio; ?></td>
                <td><?php echo $r->forma_pago; ?></td>
                <td><?php echo $r->tipo_egreso; ?></td>
                <td><a class="btn btn-info" href="?c=egreso&a=ReciboEgreso&id=<?php echo $r->id; ?>">Recibo</a></td>
                <td>

                    <?php if (!$r->anulado) : ?>
                        <?php if ($r->id_compra) : ?>
                            <a href="#detallesCompraModal" class="btn btn-warning" data-toggle="modal" data-target="#detallesCompraModal" data-id="<?php echo $r->id_compra; ?>">Compra</a>
                            <?php if ($r->id_acreedor) : ?>
                                <a href="#pagosModal" class="btn btn-success" data-toggle="modal" data-target="#pagosModal" data-id="<?php echo $r->id_acreedor; ?>">Pagos</a>
                            <?php endif ?>
                        <?php elseif ($r->id_acreedor) : ?>
                            <a href="#pagosModal" class="btn btn-success" data-toggle="modal" data-target="#pagosModal" data-id="<?php echo $r->id_acreedor; ?>">Pagos</a>
                        <?php else : ?>

                            <a class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id; ?>" data-c="egreso">Editar</a>

                        <?php endif ?>
                    <?php else : ?>
                        ANULADO
                    <?php endif ?>
                </td>
                <td>

                    <?php if (!$r->anulado) : ?>
                        <?php if ($r->id_compra) : ?>
                            <?php if ($r->id_acreedor) : ?>
                                <a class="btn btn-danger delete" href="?c=egreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_compra=<?php echo $r->id_compra; ?>"><i class="fas fa-trash-alt"></i></a>
                            <?php endif ?>
                        <?php elseif ($r->id_acreedor) : ?>
                            <a class="btn btn-danger delete" href="?c=egreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_compra=<?php echo $r->id_compra; ?>"><i class="fas fa-trash-alt"></i>Eliminar</a>
                        <?php else : ?>
                            <a class="btn btn-danger delete" href="?c=egreso&a=Eliminar&id=<?php echo $r->id; ?>&id_compra=<?php echo $r->id_compra; ?>">Eliminar</a>

                        <?php endif ?>
                    <?php else : ?>
                        ANULADO
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php //echo $total_Gs  ;?>
    </tbody>
    <tfoot>

        <tr style="background-color: #cccccc;">
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><b>Total USD.</b></td>
                <td><b><?php echo number_format($total_US, 0, ".", ",");  ?></b></td>
                <td></td>
                <td align="right"><b>Total Gs.</b></td>
                <td><b><?php echo number_format($total_Gs, 0, ".", ","); ?></b></td>
                <td></td>
                <td align="right"><b>Total Rs.</b></td>
                <td><b><?php echo number_format($total_RS, 0, ".", ",");  ?></b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
        </tr>

    </tfoot>
</table>

</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/compra/detalles-modal.php"); ?>
<?php include("view/egreso/mes-modal.php"); ?>
<?php include("view/acreedor/pagos-modal.php"); ?>

<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
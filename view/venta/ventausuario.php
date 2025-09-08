<h1 class="page-header">Ventas del usuario </h1>
<a class="btn btn-primary pull-right" href="?c=venta_tmp" class="btn btn-success">Nueva venta</a>
<br><br><br>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <div align="center">
            <form method="get">
                <div class="form-group">
                    <label>Mes</label>
                    <input type="hidden" name="c" value="venta">
                    <input type="hidden" name="a" value="listarusuario">
                    <input type="hidden" name="id_usuario" value="<?php echo $_GET['id_usuario']; ?>">
                    <input type="month" name="mes" value="<?php echo (isset($_GET['mes']))? $_GET['mes']:date("Y-m"); ?>" class="form-control" required>
                </div>
                <input type="submit" name="filtro" value="Filtrar" class="btn btn-success"> 
            </form>
        </div>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div>
<table class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Vendedor</th>
            <th>Cliente</th>
            <th>Sub tot. (Gs.)</th>
            <th>Desc. (%)</th>
            <th>Total (Gs.)</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $sumaTotal = 0; 
    $count = 0;  
    foreach($this->model->ListarUsuarioMes($_GET['id_usuario'], $_GET['mes']) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
            <td><?php echo $r->vendedor; ?></td>
            <td><?php echo $r->nombre_cli; ?></td>
            <td><?php echo number_format($r->subtotal,0,".",","); ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><?php echo number_format($r->total,0,".",","); ?></td>
            <td><?php echo date("d/m/Y", strtotime($r->fecha_venta)); ?></td>
        </tr>
    <?php 
        $sumaTotal += $r->total;
    endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th>Total:</th>
            <th><?php echo number_format($sumaTotal,0,".",","); ?></th>
            <th></th>
        </tr>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th>Comisión <?php echo $r->comision ?>%:</th>
            <th><?php echo number_format(($sumaTotal*($r->comision/100)),0,".",","); ?></th>
            <th></th>
        </tr>
    </tfoot>
</table>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>


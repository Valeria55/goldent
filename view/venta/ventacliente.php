<h1 class="page-header">Compras del cliente</h1>
<a class="btn btn-primary pull-right" href="?c=venta_tmp" class="btn btn-success">Nueva venta</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Vendedor</th>
            <?php if (isset($_REQUEST['id_venta'])): ?>
            <th>Producto</th>    
            <?php endif ?>
            <th>Cliente</th>
            <th>Sub tot. (Gs.)</th>
            <th>Desc. (%)</th>
            <th>Total (Gs.)</th>
            <th>Ganan. (%)</th>
            <th>Hora</th>
            <?php if (!isset($_GET['id_venta'])): ?>        
            <th></th>
            <?php endif ?>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0;  
    foreach($this->model->ListarCliente($_GET['id_cliente']) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
            <td><?php echo $r->vendedor; ?></td>
            <?php if (isset($_REQUEST['id_venta'])): ?>
            <td><?php echo $r->producto; ?></td>    
            <?php endif ?>
            <td><?php echo $r->nombre_cli; ?></td>
            <td><?php echo number_format($r->subtotal,0,".",","); ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><?php echo number_format($r->total,0,".",","); ?></td>
            <td><?php echo round($r->margen_ganancia,2); ?></td>
            <td><?php echo date("H:i", strtotime($r->fecha_venta)); ?></td>
            <?php if (!isset($_GET['id_venta'])): ?>
            <td>
                <a href="#detallesModal" class="btn btn-success" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta;?>">Ver</a>
                <?php echo ($r->anulado)? " ANULADO":"";?>
            </td>
            <?php endif ?>
        </tr>
    <?php 
        $count++;
    endforeach; ?>
    </tbody>
    
</table>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>


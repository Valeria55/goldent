 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Detalles de la compra</h1> 
<div align="center" width="30%"> 
    
</div>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Cod.</th>
            <th>Producto</th>
            <th>Cant</th>
            <th>Precio compra</th>
            <th>Precio venta</th>
            <th>Total (Gs.)</th>
        </tr>
    </thead>
    <tbody>
    <?php
     $cantidad_total = 0;
     $subtotal=0;
     $sumatotal = 0;
     $id_compra = $_GET['id'];
     foreach($this->compra->Listar($id_compra) as $r): 
        $total = $r->precio_compra*$r->cantidad;
        $cantidad_total += $r->cantidad;
     ?>
        <tr>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo number_format($r->precio_compra, 0, "," , "."); ?></td>
            <td><?php echo number_format($r->precio_min, 0, "," , "."); ?></td>
            <td><?php echo number_format($total, 0, "," , "."); ?></td>
        </tr>
    <?php $sumatotal += $total ;endforeach; ?>
        
        
        <tr>
            <td></td>
            <td></td>
            <td><b>Cantidad Items:</b> <?php echo number_format($cantidad_total, 0, "," , "."); ?></td>
            <td></td>
            <td></td>
            <td><b>Total Gs.:</b> <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal,0,",",".") ?></div></td>
        </tr>
    </tbody>
</table> 
</div> 
</div>
</div>

 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Detalles del factura</h1> 
<div align="center" width="30%"> 
    
</div>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Producto</th>
            <th>Precio</th>
            <th>Cant</th>
            <th>Descuento</th>
            <th>Total (Gs.)</th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     $sumatotal = 0;
     $id_factura = $_GET['id'];
     $cant_items = 0;

     foreach($this->factura->ListarDetalle($id_factura) as $r): 
        $total = (($r->precio_venta*$r->cantidad)-(($r->descuento)));
        $cant_items = $cant_items + $r->cantidad;
     ?>
        <tr>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo number_format($r->descuento, 0, "," , "."); ?></td>
            <td><?php echo number_format($total, 0, "," , "."); ?></td>
        </tr>
    <?php $sumatotal += $total ;endforeach; ?>
        
        
        <tr>
            <td></td>
            <td></td>
            <td><b>Cantidad Items: </b> <div style="font-size: 20px"><?php echo $cant_items ;?></div></td>
            <td></td>
            <td><b>Total Gs: </b> <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal,0,",",".") ?></div></td>
        </tr>
    </tbody>
</table> 
</div> 
</div>
</div>

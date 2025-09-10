 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Detalles de la venta</h1> 
<div align="center" width="30%"> 
    
</div>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Codigo</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cant</th>
            <th>Descuento(%)</th>
            <th>Total (Gs.)</th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     $sumatotal = 0;
     $id_venta = $_GET['id'];
     $tabla = $_GET['tabla'];

     if($tabla == 'aprobado'){
         $query_venta = $this->venta->ListarAprobado($id_venta);
     }elseif($tabla == 'aprobar'){
        $query_venta = $this->venta->ListarAprobar($id_venta);
     }else{
        $query_venta = $this->venta->Listar($id_venta);
     }
     
     foreach($query_venta as $r): 
        $total = $r->total;
     ?>
        <tr>
            <td><a  class="btn btn-default" href="?c=venta&a=listarproducto&id_producto=<?php echo $r->id_producto; ?>"><?php echo $r->codigo; ?></a></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><?php echo number_format($total, 0, "," , "."); ?></td>
        </tr>
    <?php $sumatotal += $total ;endforeach; ?>
        
        
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total Gs: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal,0,",",".") ?></div></td>
        </tr>
    </tbody>
</table> 
</div> 
</div>
</div>

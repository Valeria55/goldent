<?php $desde = (isset($_GET["desde"]))? $_GET["desde"]:0; $hasta = (isset($_GET["hasta"]))? $_GET["hasta"]:0; 
$pr=$this->producto->Obtener($_GET['id_producto']);?>
<h2 class="page-header">Historial del producto </h2>
<h1><center><?php  echo $pr->producto ?></h1>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <form method="get">
            <input type="hidden" name="c" value="venta">
            <input type="hidden" name="a" value="listarproducto">
            <input type="hidden" name="id_producto" value="<?php echo $_GET["id_producto"]; ?>">
            <table width="100%">
                <tr>
                    <td>Desde:<input type="datetime-local" name="desde" value="<?php echo (isset($_GET['desde']))? $_GET['desde']:''; ?>" required></td>
                    <td>Hasta:<input type="datetime-local" name="hasta" value="<?php echo (isset($_GET['hasta']))? $_GET['hasta']:''; ?>" required></td>
                    <td>  <input type="submit" name="filtro" value="Filtrar" class="btn btn-success"> </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div>
<p> </p>
<br>
<h4>Ventas</h4>
<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

    <thead>
        <tr style="background-color: #cccccc;">  
            <th>N°</th>
            <th>Vendedor</th>
            <th>Fecha</th>
            <th>P/ costo</th>
            <th>P/ venta</th>
            <th>Cant</th>
            <th>Total venta</th>
            <th>Total costo </th>
            <th>Ganancia</th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; $ganacia = 0;
    foreach($this->venta->ListarProducto($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr >
            <td style=""><?php echo $r->id_presupuesto; ?></td>
            <td style=""><?php echo $r->vendedor; ?></td>
            <td style=""><?php echo date("d/m/Y H:i", strtotime($r->fecha_venta)); ?></td>
            <td style="" align="right"><?php echo number_format($r->precio_costo,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($r->precio_venta-$r->descuento,2,".",","); ?></td>   
            <td style="" align="right"><?php echo $r->cantidad; ?></td>
            <td style="" align="right"><?php echo number_format($r->total,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format(($r->precio_costo*$r->cantidad),2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format(($r->total-($r->precio_costo*$r->cantidad)),2,".",","); ?></td>
        </tr>
    <?php 
        $count++;
        $cantidad += $r->cantidad;
        $costo += $r->precio_costo*$r->cantidad;
        $suma += $r->total;
        $ganacia += $r->total-($r->precio_costo*$r->cantidad);
    }endforeach; ?>
    </tbody>
    <?php  ?>
    <tfoot>
        <tr style="background-color: #cccccc;" >
            <td></td>
            <td></td>
            <td></td>
            <td style="" align="right"></td>
            <td style="" align="right">TOTAL:</td>
            <td style="" align="right"><?php echo number_format($cantidad,0,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($suma,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($costo,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($ganacia,2,".",","); ?></td>
    </tfoot>
    
</table>

<hr>
<h4>Compras</h4>

<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

    <thead>
        <tr style="background-color: #cccccc;">
            <th>N°</th>
            <th>Comprador</th> 
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>P/ venta</th>
            <th>P/ compra</th>
            <th>Cant</th>
            <th>Total</th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; 
    foreach($this->compra->ListarProducto($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr >
            <td style=""><?php echo $r->id_compra; ?></td>
            <td style=""><?php echo $r->vendedor; ?></td>
            <td style=""><?php echo date("d/m/Y H:i", strtotime($r->fecha_compra)); ?></td>
            <td style=""><?php echo $r->nombre_cli; ?></td>
            <td style="" align="right"><?php echo number_format($r->precio_min,2,".",","); ?></td> 
            <td style="" align="right"><?php echo number_format($r->precio_compra,2,".",","); ?></td>  
            <td style="" align="right"><?php echo $r->cantidad; ?></td>
            <td style="" align="right"><?php echo number_format($r->total,2,".",","); ?></td>
        </tr>
    <?php 
        $count++;
        $cantidad += $r->cantidad;
        $suma += $r->total;
    }endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #cccccc;">
        
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="" align="right">TOTAL:</td>
            <td style="" align="right"><?php echo number_format($cantidad,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($suma,2,".",","); ?></td>
    </tfoot>
    
</table>
<hr>
<h4>Ajustes</h4>

<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

   <thead>
        <tr style="background-color: #cccccc;">  
            <th>N°</th>
            <th>Vendedor</th>
            <th>Fecha</th>
            <th>P/ costo</th>
            <th>P/ venta</th>
            <th>Cant</th>
            <th>Total venta</th>
            <th>Total costo </th>
            <th>Ganancia</th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; $ganacia = 0;
    foreach($this->devolucion->ListarProducto($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr >
            <td style=""><?php echo $r->id_venta; ?></td>
            <td style=""><?php echo $r->vendedor; ?></td>
            <td style=""><?php echo date("d/m/Y H:i", strtotime($r->fecha_venta)); ?></td>
            <td style="" align="right"><?php echo number_format($r->precio_costo,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($r->precio_venta-$r->descuento,2,".",","); ?></td>   
            <td style="" align="right"><?php echo $r->cantidad; ?></td>
            <td style="" align="right"><?php echo number_format($r->total,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format(($r->precio_costo*$r->cantidad),2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format(($r->total-($r->precio_costo*$r->cantidad)),2,".",","); ?></td>
        </tr>
    <?php 
        $count++;
        $cantidad += $r->cantidad;
        $costo += $r->precio_costo*$r->cantidad;
        $suma += $r->total;
        $ganacia += $r->total-($r->precio_costo*$r->cantidad);
    }endforeach; ?>
    </tbody>
   <tfoot>
        <tr style="background-color: #cccccc;">
            <td></td>
            <td></td>
            <td></td>
            <td style="" align="right"></td>
            <td style="" align="right">TOTAL:</td>
            <td style="" align="right"><?php echo number_format($cantidad,0,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($suma,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($costo,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($ganacia,2,".",","); ?></td>
    </tfoot>
    
</table>
<hr>
<h4>Devoluciones</h4>

<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

   <thead>
        <tr style="background-color: #cccccc;">  
            <th>N°</th>
            <th>Vendedor</th>
            <th>Fecha</th>
            <th>P/ costo</th>
            <th>P/ venta</th>
            <th>Cant</th>
            <th>Total venta</th>
            <th>Total costo </th>
            <th>Ganancia</th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; $ganacia = 0;$costo = 0;
    foreach($this->devolucion_ventas->ListarProducto($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr >
            <td style=""><?php echo $r->id_venta; ?></td>
            <td style=""><?php echo $r->vendedor; ?></td>
            <td style=""><?php echo date("d/m/Y H:i", strtotime($r->fecha_venta)); ?></td>
            <td style="" align="right"><?php echo number_format($r->precio_costo,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($r->precio_venta-$r->descuento,2,".",","); ?></td>   
            <td style="" align="right"><?php echo $r->cantidad; ?></td>
            <td style="" align="right"><?php echo number_format($r->total,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format(($r->precio_costo*$r->cantidad),2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format(($r->total-($r->precio_costo*$r->cantidad)),2,".",","); ?></td>
        </tr>
    <?php 
        $count++;
        $cantidad += $r->cantidad;
        $costo += $r->precio_costo*$r->cantidad;
        $suma += $r->total;
        $ganacia += $r->total-($r->precio_costo*$r->cantidad);
    }endforeach; ?>
    </tbody>
   <tfoot>
        <tr style="background-color: #cccccc;" >
            <td></td>
            <td></td>
            <td></td>
            <td style="" align="right"></td>
            <td style="" align="right">TOTAL:</td>
            <td style="" align="right"><?php echo number_format($cantidad,0,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($suma,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($costo,2,".",","); ?></td>
            <td style="" align="right"><?php echo number_format($ganacia,2,".",","); ?></td>
    </tfoot>
    
</table>
<h4>Inventario</h4>

<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

   <thead>
        <tr style="background-color: #cccccc;">  
            <th>N°</th>
            <th>Vendedor</th>
            <th>Fecha</th>
            <th>Cant</th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; $ganacia = 0;$costo = 0;
    foreach($this->inventario->ListarProducto($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr >
            <td style=""><?php echo $r->id_invetario; ?></td>
            <td style=""><?php echo $r->vendedor; ?></td>
            <td style=""><?php echo $r->fecha_stock_real!=null?  date("d/m/Y H:i", strtotime($r->fecha_stock_real)):date("d/m/Y H:i", strtotime($r->fecha)); ?></td> 
            <td style="" align="right"><?php echo $r->cantidad !=null ? $r->cantidad:0 ; ?></td>
        </tr>
    <?php 
        $count++;
        $cantidad += $r->cantidad;
       
    }endforeach; ?>
    </tbody>
   <tfoot>
        <tr style="background-color: #cccccc;" >
            
            <td></td>
            <td style="" align="right"></td>
            <td style="" align="right">TOTAL:</td>
            <td style="" align="right"><?php echo number_format($cantidad,0,".",","); ?></td>
    </tfoot>
    
</table>
</div>
</div>

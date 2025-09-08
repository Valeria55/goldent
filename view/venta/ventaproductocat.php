<?php $desde = (isset($_GET["desde"]))? $_GET["desde"]:0; $hasta = (isset($_GET["hasta"]))? $_GET["hasta"]:0; ?>
<h4 class="page-header">Historial de categoría de productos</h4>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <form method="get">
            <input type="hidden" name="c" value="venta">
            <input type="hidden" name="a" value="listarproductocat">
            <input type="hidden" name="id_cat" value="<?php echo $_GET["id_cat"]; ?>">
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
<table class="table table-striped table-bordered display responsive nowrap" style="font-size:9px;">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Categoría</th>
            <th>Sub cat</th>
            <th>Producto</th>
            <th>Costo</th>
            <th>Precio</th>
            <th>Cant</th>
            <th>Total</th>
            <th>Costo </th>
            <th>Ganancia</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; 
    foreach($this->venta->ListarProductoCat($_GET['id_categoria'], $desde, $hasta) as $r): ?>
        <tr >
            <td style="padding:0px"><?php echo $r->categoria; ?></td>
            <td style="padding:0px"><?php echo $r->sub_categoria; ?></td>
            <td style="padding:0px"><?php echo $r->producto; ?></td>
            <td style="padding:0px" align="right"><?php echo number_format($r->precio_costo,0,".",","); ?></td>
            <td style="padding:0px" align="right"><?php echo number_format($r->precio_venta,0,".",","); ?></td>   
            <td style="padding:0px" align="right"><?php echo $r->cantidad; ?></td>
            <td style="padding:0px" align="right"><?php echo number_format($r->total,0,".",","); ?></td>
            <td style="padding:0px" align="right"><?php echo number_format(($r->precio_costo*$r->cantidad),0,".",","); ?></td>
            <td style="padding:0px" align="right"><?php echo number_format(($r->total-($r->precio_costo*$r->cantidad)),0,".",","); ?></td>
        </tr>
    <?php 
        $count++;
        $cantidad += $r->cantidad;
        $costo += $r->precio_costo*$r->cantidad;
        $suma += $r->total;
        endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff" >
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="padding-right:0px" align="right">TOTAL:</td>
            <td style="padding-right:0px" align="right"><?php echo number_format($cantidad,0,".",","); ?></td>
            <td style="padding-right:0px" align="right"><?php echo number_format($suma,0,".",","); ?></td>
            <td style="padding-right:0px" align="right"><?php echo number_format($costo,0,".",","); ?></td>
            <td style="padding-right:0px" align="right"><?php echo number_format(($suma-$costo),0,".",","); ?></td>
        </tr>
    </tfoot>
    
</table>

<hr>
<h4>Compras</h4>

<table class="table table-striped table-bordered display responsive nowrap" style="font-size:9px;">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Categoria</th>
            <th>Sub cat</th>
            <th>Producto</th>
            <th>Fecha</th>
            <th>Comprador</th>    
            <th>Proveedor</th>
            <th>Costo</th>
            <th>Cant</th>
            <th>Total</th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; 
    foreach($this->compra->ListarProductoCat($_GET['id_cat'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr >
            <td style="padding:0px"><?php echo $r->categoria; ?></td>
            <td style="padding:0px"><?php echo $r->sub_categoria; ?></td>
            <td style="padding:0px"><?php echo $r->producto; ?></td>
            <td style="padding:0px"><?php echo date("d/m/Y H:i", strtotime($r->fecha_compra)); ?></td>
            <td style="padding:0px"><?php echo $r->vendedor; ?></td>
            <td style="padding:0px"><?php echo $r->nombre_cli; ?></td>
            <td style="padding:0px" align="right"><?php echo number_format($r->precio_compra,0,".",","); ?></td>  
            <td style="padding:0px" align="right"><?php echo $r->cantidad; ?></td>
            <td style="padding:0px" align="right"><?php echo number_format($r->total,0,".",","); ?></td>
        </tr>
    <?php 
        $count++;
        $cantidad += $r->cantidad;
        $suma += $r->total;
    }endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff" >
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="padding-right:0px" align="right">TOTAL:</td>
            <td style="padding-right:0px" align="right"><?php echo number_format($cantidad,0,".",","); ?></td>
            <td style="padding-right:0px" align="right"><?php echo number_format($suma,0,".",","); ?></td>
    </tfoot>
    
</table>
</div>
</div>

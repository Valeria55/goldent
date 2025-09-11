<?php $desde = (isset($_GET["desde"]))? $_GET["desde"]:0; $hasta = (isset($_GET["hasta"]))? $_GET["hasta"]:0; ?>
<h4 class="page-header">Historial del producto : <?php $p=$this->producto->Obtener($_GET['id_producto']); echo $p->producto; ?></h4>
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
<!--VENTAS-->
<h4>Ventas</h4>
<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

    <thead>
        <tr style="background-color: #212121; color:#fff">  
            <th>Fecha</th>
            <th>Vendedor</th>
            <th>N° Venta</th>
            <?php if($_SESSION['nivel']==1){?>
            <th>Costo/U</th>
            <?php } ?>
            <th>Precio/U</th>
            <th>Cant</th>
            <th>Venta</th>
            <?php if($_SESSION['nivel']==1){?>
            <th>Costo </th>
            <th>Ganancia</th>
            <?php } ?>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; $costo = 0;
    foreach($this->venta->ListarProducto($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr align="right">
            <td style="padding:0px" align="left"><?php echo $r->fecha_venta; ?></td>
            <td style="padding:0px" align="left"><?php echo ($r->presupuestario != null) ? $r->vendedor : $r->vendedor_caja ?></td>
            <td style="padding:0px"><?php echo $r->id_venta; ?></td>
            <?php if($_SESSION['nivel']==1){?>
            <td style="padding:0px" align="right"><?php echo number_format($r->precio_costo,0,".",","); ?></td>
            <?php } ?>
            <td style="padding:0px" align="right"><?php echo number_format($r->precio_venta,0,".",","); ?></td>   
            <td style="padding:0px" align="right"><?php echo $r->cantidad; ?></td>
            <td style="padding:0px" align="right"><?php echo number_format($r->total,0,".",","); ?></td>
            <?php if($_SESSION['nivel']==1){?>
            <td style="padding:0px" align="right"><?php echo number_format(($r->precio_costo*$r->cantidad),0,".",","); ?></td>
            <td style="padding:0px" align="right"><?php echo number_format(($r->total-($r->precio_costo*$r->cantidad)),0,".",","); ?></td>
            <?php } ?>
        </tr>
    <?php 
        $count++;
        $cantidad += $r->cantidad;
        $costo += $r->precio_costo*$r->cantidad;
        $suma += $r->total;
    }endforeach; ?>
    </tbody>
    <?php ?>
    <tfoot>
        <tr style="background-color: #666666; color:#fff" >
            <td></td>
            <td></td>
            <?php if($_SESSION['nivel']==1){?>
            <td></td>
            <?php } ?>
            <td></td>
            <td style="padding-right:0px" align="right">TOTAL:</td>
            <td style="padding-right:0px" align="right"><?php echo number_format($cantidad,0,".",","); ?></td>
            <td style="padding-right:0px" align="right"><?php echo number_format($suma,0,".",","); ?></td>
            <?php if($_SESSION['nivel']==1){?>
            <td style="padding-right:0px" align="right"><?php echo number_format($costo,0,".",","); ?></td>
            <td style="padding-right:0px" align="right"><?php echo number_format(($suma-$costo),0,".",","); ?></td>
            <?php } ?>
    </tfoot> <?php  ?>
    
</table>

<!--COMPRAS-->
<hr>
<h4>Compras</h4>

<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

    <thead>
        <tr style="background-color: #212121; color:#fff">
            
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
    foreach($this->compra->ListarProducto($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr >
      
            <td style="padding:0px"><?php echo date("d/m/Y H:i", strtotime($r->fecha_compra)); ?></td>
            <td style="padding:0px"><?php echo $r->vendedor.' ('.$r->id_compra.')'; ?></td>
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
        <tr style="background-color: #666666; color:#fff" >
           
            <td></td>
            <td></td>
            <td></td>
            <td style="padding-right:0px" align="right">TOTAL:</td>
            <td style="padding-right:0px" align="right"><?php echo number_format($cantidad,0,".",","); ?></td>
            <td style="padding-right:0px" align="right"><?php echo number_format($suma,0,".",","); ?></td>
    </tfoot>
    
</table>

</table>
<!--INVENTARIO-->
<hr />
<h4>Inventario</h4>
<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

    <thead>
        <tr style="background-color: #212121; color:#fff">  
            <th>Fecha</th>
            <th>Usuario</th>
            <th>N° Inventario</th>
            <th>Cantidad</th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; $cantidad = 0; 
    foreach($this->inventario->ListarProductoInv($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr align="right">
            <td style="padding:0px" align="left"><?php echo $r->fecha; ?></td>
            <td style="padding:0px" align="left"><?php echo $r->user; ?></td>
            <td style="padding:0px"><?php echo $r->id; ?></td>
            <td style="padding:0px" align="right"><?php echo ($r->stock_actual !== null) ? $r->stock_actual : 0; ?></td>
        </tr>
    <?php 
    }endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #666666; color:#fff" >
            <td></td>
            <td></td>
            <td></td>
            <td></td>
    </tfoot>
</table>
<hr />
<!--AJUSTES-->
<!--AJUSTES-->
<h4>Ajustes</h4>
<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

    <thead>
        <tr style="background-color: #212121; color:#fff">  
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Entrada</th>
            <th>Salida</th>
    </thead>
    <tbody>
    <?php 
    foreach($this->devolucion->ListarInOut($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr align="right">
            <td style="padding:0px" align="left"><?php echo $r->fecha_venta; ?></td>
            <td style="padding:0px" align="left"><?php echo $r->user; ?></td>
            <td style="padding:0px"><?php echo $r->cantidad>0 ? $r->cantidad : '0'; ?></td>
            <td style="padding:0px"><?php echo $r->cantidad<0 ? $r->cantidad : '0'; ?></td>
        </tr>
    <?php 
    }endforeach; ?>
    </tbody>
    <?php ?>
    <tfoot>
        <tr style="background-color: #666666; color:#000" >
            <td></td>
            <td></td>
            <td></td>
            <td></td>

    </tfoot> <?php  ?>
    
</table>


<!--
  *************************************************************************
  *                                                                       *
  *   DEVOLUCIONES                                                        *
  *                                                                       *
  *************************************************************************
-->

<hr />
<h3 style="text-align: center">DEVOLUCIONES:</h3>
<!--DEVOLUCIONES VENTAS-->

<!--DEVOLUCIONES COMPRAS-->
<h4>Devoluciones Compras</h4>
<table class="table table-striped table-bordered display responsive nowrap" style="font-size:12px;">

    <thead>
        <tr style="background-color: #212121; color:#fff">  
            <th>Fecha de devolucion</th>
            <th>Vendedor</th>
            <th>Cantidad</th>
            <th>Precio de compra</th>
            <th>Motivo</th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $cantidad_devuelta_com = 0;
    foreach($this->devolucion_compras->ListarDevolucionesCom($_GET['id_producto'], $desde, $hasta) as $r):
        if(!$r->anulado){?>
        <tr align="right">
            <td style="padding:0px" align="left"><?php echo $r->fecha_compra; ?></td>
            <td style="padding:0px" align="left"><?php echo $r->nombre_vendedor; ?></td>
            <td style="padding:0px" align="right"><?php echo $r->cantidad_com; ?></td> 
            <td style="padding:0px" align="right"><?php echo number_format($r->precio_compra,0,".",","); ?></td>   
            <td style="padding:0px" align="right"><?php echo $r->motivo; ?></td>
        </tr>
    <?php 
        $cantidad_devuelta_com += $r->cantidad_com;
        $suma += $r->precio_compra;
    }endforeach; ?>
    </tbody>
    <?php ?>
    <tfoot>
        <tr style="background-color: #666666; color:#fff" >
            <td></td>
            <td></td>
            <td style="padding-right:0px" align="right">Cantidad devuelta: <?php echo $cantidad_devuelta_com ?></td>
            <td style="padding-right:0px" align="right">Total devuelto: <?php echo number_format(($suma),0,".",","); ?></td>
            <td></td>
    </tfoot> <?php  ?>
    
</table>
</div>
</div>

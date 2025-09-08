<?php $fecha = date("Y-m-d"); 
?>
<h1 class="page-header">Detalles de la venta</h1> 
<div align="center" width="30%"> 
    
</div>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">


    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Cód.</th>
            <th>Producto</th>
            <th>Precio</th>
          <?php session_start(); if($_SESSION['nivel']== 1){ ?>
            <th>Costo</th>
           <?php  } ?>
            <th>Descuento</th>
            <th>P/U</th>
            <th>Cant</th>
            <th>Total</th>
            <th>Devuelto</th>
            <th>Cant. Devuelta</th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     $costoTotal=0;
     $sumatotal = 0;
     $id_venta = $_GET['id'];
     foreach($this->venta->ListarDetalles($id_venta) as $r): 
        // echo '<pre>'; var_dump($r); echo '</pre>';
        $total = (($r->precio_venta*$r->cantidad)-($r->descuento*$r->cantidad));
        $totalusd = (($r->precio_venta*$r->cantidad)-($r->descuento*$r->cantidad))/$r->cot_dolar;
        $totalrs = (($r->precio_venta*$r->cantidad)-($r->descuento*$r->cantidad))/$r->cot_real;
        $item += $r->cantidad;
        $dev =  $r->cantidad == $r->cantidad_devuelta ?  'Si': '';
        
     ?>
        <tr>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
         <?php  session_start(); if($_SESSION['nivel']== 1){?>
            <td><?php echo number_format($r->precio_costo, 0, "," , "."); ?></td>
          <?php   }?>
            <td><?php echo number_format($r->descuento*$r->cantidad, 0, "," , "."); ?></td>
            <td><?php echo number_format($r->precio_venta-$r->descuento, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo number_format($total, 0, "," , "."); ?></td>
            <td><?php echo $dev; ?></td>
            <td><?php echo $r->cantidad_devuelta; ?></td>
        </tr>
    <?php $sumatotal += $total ;
    $costoTotal += $r->precio_costo ;
    endforeach; ?>

        
        
        <tr>
            <td><td>Cant.: <?php echo $item; ?></td></td>
            <td></td>
            <?php session_start(); if($_SESSION['nivel']== 1){ ?>
                <td><?php echo number_format($costoTotal,0,",",".") ?></td>
            <?php  } ?>
            <td></td>
            <td>RS: <div id="total" style="font-size: 20px"><?php echo number_format($totalrs, 2, ",", ".") ?></div></td> 
            <!-- cambio fijo ?  -->
            <!-- <td>RS: <div id="total" style="font-size: 20px"><?php //echo number_format($sumatotal*5.30,2,",",".") ?></div></td> -->
            <td>GS: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal,0,",",".") ?></div></td>
            <td>USD: <div id="total" style="font-size: 20px"><?php echo number_format($totalusd,2,",",".") ?></div></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table> 
</div> 
</div>
</div>
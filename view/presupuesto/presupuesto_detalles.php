 
<?php $fecha = date("Y-m-d"); ?>

<?php $id_presupuesto = $_GET['id_presupuesto']; ?>

<h1 class="page-header">Detalles del presupuesto N° <?php echo $id_presupuesto ?></h1> 
<div align="center" width="30%"> 
    
</div>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Cód.</th>
            <th>Producto</th>
            <th>Precio</th>
            <?php session_start(); if($_SESSION['nivel']==1){ ?>
            <th>Costo</th>
           <?php  } ?>
            <th>Cant</th>
            <th>Descuento</th>
            <th>Total (Gs.)</th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     $sumatotal = 0;
     foreach($this->presupuesto->ListarDetalle($id_presupuesto) as $r): 
        $total = (($r->precio_venta*$r->cantidad)-(($r->descuento*$r->cantidad)));
     ?>
        <tr>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
            <?php session_start(); if($_SESSION['nivel']== 1){?>
            <td><?php echo number_format($r->precio_costo, 0, "," , "."); ?></td>
            <?php   }?>
            
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo number_format($r->descuento, 0, "," , "."); ?></td>
            <td><?php echo number_format($total, 0, "," , "."); ?></td>
        </tr>
        <!-- Obtener cotización de acuerdo al estado del presupuesto -->
        <?php 
            if ($r->estado == "Vendido") {
               $cotizacion = $this->venta->ListarVentaPresupuesto($id_presupuesto);
            }else{
                $cotizacion = $this->cierre->ObtenerCierre(); 
            }    
        ?>

    <?php $sumatotal += $total ;endforeach; ?>
        
        
        <tr>
        <td></td>
            <td></td>
            <td>RS: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal/$cotizacion->cot_real,2,",",".") ?></div></td>
            <td>GS: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal,0,",",".") ?></div></td>
            <td>USD: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal/$cotizacion->cot_dolar,2,",",".") ?></div></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table> 
</div> 
</div>
</div>

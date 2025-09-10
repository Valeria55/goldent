 <?php $fecha = date("Y-m-d"); ?>
 <h1 class="page-header">Detalles de la transferencia</h1>
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
                 <th>Total (Gs.)</th>
             </tr>
         </thead>
         <tbody>
             <?php
                $subtotal = 0;
                $sumatotal = 0;
                $id_transferencia_producto = $_GET['id_transferencia_producto'];
                $recibido = ($_REQUEST['recibido']) ?? 0;
                if ($recibido == 1) {
                    $detalle = $this->transferencia_producto->ListarDetalleRecibido($id_transferencia_producto);
                } else {
                    $detalle = $this->transferencia_producto->ListarDetalle($id_transferencia_producto);
                }
                foreach ($detalle as $r) :
                    $total = (($r->precio_venta * $r->cantidad) - ($r->precio_venta * $r->cantidad * ($r->descuento / 100)));
                ?>
                 <tr>
                     <td><?php echo $r->codigo; ?></td>
                     <td><?php echo $r->producto; ?></td>
                     <td><?php echo number_format($r->precio_venta, 0, ",", "."); ?></td>
                     <td><?php echo $r->cantidad; ?></td>
                     <td><?php echo number_format($total, 0, ",", "."); ?></td>
                 </tr>
             <?php $sumatotal += $total;
                endforeach; ?>


             <tr>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td>Total Gs: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal, 0, ",", ".") ?></div>
                 </td>
             </tr>
         </tbody>
     </table>
 </div>
 </div>
 </div>
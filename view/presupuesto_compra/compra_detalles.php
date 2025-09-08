 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Detalles del presupuesto de compra</h1> 
<div align="center" width="30%"> 
    
</div>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla2">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Cod.</th>
            <th>Producto</th>
            <th>Precio venta</th>
            <th>Precio compra</th>
            <th>Cantidad</th>
            <th>Total </th>
        </tr>
    </thead>
    <tbody>
    <?php
    $totalItem = 0;  
    $subtotal=0;
    $cant=0;
     $id_presupuesto = $_GET['id'];
        // var_dump($id_presupuesto);
        // die();
     foreach($this->presupuesto_compra->ListarDetalle($id_presupuesto) as $r): 
        $totalItem = $r->precio_compra*$r->cantidad;
        $subtotal += ($totalItem);
        $cant +=$r->cantidad;
     ?>
        <tr>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_min, 2, "," , "."); ?></td>
            <td><?php echo $r->precio_compra; ?></td>
            <!-- <td><?php //echo number_format($r->precio_compra, 2, "," , "."); ?></td> -->
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo number_format($r->total, 2, "," , "."); ?></td>
        </tr>
    <?php endforeach; ?>
         <tfoot> 
        
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Cantidad Items:</b> <?php echo number_format($cant, 0, "," , "."); ?></td>
            <td><b>Total.:</b> <div id="total" style="font-size: 20px"><?php echo number_format($subtotal,2,",",".") ?></div></td>
        </tr>
           </tfoot>
    </tbody>
</table> 
</div> 
</div>
</div>
<script>
 $('#tabla2').DataTable({
     "dom": 'Bfrtip',
     "buttons": [{
         extend: 'excelHtml5',
         footer: true,
         title: "Presupuesto de compra",
     }, {
         extend: 'pdfHtml5',
         footer: true,
         title: "Presupuesto de compra",
         orientation: 'Portrait',
         pageSize: 'LEGAL',
         customize: function(doc) {
             doc.styles.tableBodyOdd.alignment = 'center';
             doc.styles.tableBodyEven.alignment = 'center';
         }
     }, 'colvis'],
     "stateSave": true,
     "scrollY": '50vh',
     "scrollCollapse": true,
     "paging": false
 });
</script>

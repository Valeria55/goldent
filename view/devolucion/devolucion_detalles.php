<?php

$fecha = date("Y-m-d");
$id_devolucion = $_GET['id'];
$devolucion = $this->model->Obtener($id_devolucion);
// $sucursal = "KM6";
$sucursal = $GLOBALS['nombre_sucursal'];

?>
<h1 class="page-header">Detalles de ajuste #<?php echo $_GET['id']; ?></h1>
<div align="center" width="30%">

</div>

<div class="table-responsive">

 <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">

     <thead>
         <tr style="background-color: #5DACCD; color:#fff">
             <th>Cod.</th>
             <th>Producto</th>
             <th>Cat</th>
             <th>Sub cat</th>
             <th>Monto</th>
             <th>Cant</th>
             <th>Motivo</th>
             <th>Total (Gs.)</th>
         </tr>
     </thead>
     <tbody>
         <?php
            $subtotal = 0;
            $sumatotal = 0;
            $sumaCantidad = 0;
            foreach ($this->model->Listar($id_devolucion) as $r) :
                $total = $r->precio_venta * $r->cantidad;
            ?>
             <tr>
                 <td><?php echo $r->codigo; ?></td>
                 <td><?php echo $r->producto; ?></td>
                 <td><?php echo $r->categoria; ?></td>
                 <td><?php echo $r->sub_categoria; ?></td>
                 <td><?php echo number_format($r->precio_venta, 0, ",", ","); ?></td>
                 <td><?php echo $r->cantidad; ?></td>
                 <td><?php echo $r->descuento; ?></td>
                 <td><?php echo number_format($total, 0, ",", ","); ?></td>
             </tr>
         <?php
                $sumatotal += $total;
                $sumaCantidad += $r->cantidad;
            endforeach; ?>

     <tfoot>
         <tr>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td>Cant.: <div id="total" style="font-size: 20px"><?php echo number_format($sumaCantidad, 0, ",", ",") ?></div>
             </td>
             <td></td>
             <td>Total Gs: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal, 0, ",", ",") ?></div>
             </td>
         </tr>
     </tfoot>
 </table>
</div>
</div>
</div>

<script>
 $('#tabla').DataTable({
     "dom": 'Bfrtip',
     "buttons": [{
         extend: 'excelHtml5',
         footer: true,
         title: "Lista de ajustes ",
     }, {
         extend: 'pdfHtml5',
         footer: true,
         title: "Lista de ajustes",
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
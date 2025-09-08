<?php 
$pagos[]="";
foreach($this->metodo->Listar() as $m) {
    $pagos[''.$m->metodo.'']=0;
}
?>
<h1 class="page-header">Movimientos de la caja en la sesión</h1>
<br><br><br>

<p> </p>
<table class="table table-striped display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>N°</th>
        	<th>Fecha</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>N° de comprobante</th>
            <th>Monto</th> 
            <th>Moneda</th> 
            <th>Forma de pago</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $cierre_id = $_GET['id'];
    $cierre = $this->model->Obtener($cierre_id); ?>
    <tr class="click">
            <td> </td>
            <td><?php echo date("d/m/Y H:i", strtotime($cierre->fecha_apertura)); ?></td>
            <td><?php echo "Apertura"; ?></td>
            <td><?php echo "Apertura de caja del día"; ?></td>
            <td><?php echo ""; ?></td>
            <td class="monto"><?php echo number_format($cierre->monto_apertura,0,".",","); ?></td>
            <td><?php echo ""; ?></td>
            <td><?php echo "Efectivo"; ?></td>
        </tr>
    <?php 
    $c=1;
    foreach($this->model->ListarMovimientosSesionCerrada($cierre->id_usuario, $cierre->fecha_apertura, $cierre->fecha_cierre) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:red'";}elseif($r->descuento>0){echo "style='color:#F39C12'";} ?><?php if($r->categoria=='Devolución'){echo "style='color:blue'";} ?>>
            <td><?php echo $c++; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->categoria.'('.$r->id_venta.')'; ?></td>
            <td><?php echo $r->concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo number_format($r->monto,0,".",","); ?></td>
            <td><?php echo $r->moneda; ?></td>
            <td><?php echo $r->forma_pago; ?></td>
        </tr>
    <?php
    if($r->anulado != 1 && $r->categoria!='compra'){
    $pagos[''.$r->forma_pago.'']+=$r->monto;
     $total +=$r->monto;
    }
   
    endforeach; ?>
    </tbody>
    <tfoot>
        <?php 
            foreach($this->metodo->Listar() as $m): ?>
            <tr style="background-color: black; color:#fff">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total <?php echo $m->metodo; ?> </th>
                <th class="monto" id="monto_total"><?php echo number_format($pagos[''.$m->metodo.''],0,".",","); ?></th> 
                <th></th>
                <th></th>
            </tr>
         <?php 
    endforeach; ?>
    
     <tr style="background-color: black; color:#fff; font-size:18px">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>TOTAL</th>
                <th><?php echo number_format($total,0,".",","); ?></th> 
                <th></th>
                <th></th>
            </tr>
    </tfoot>
</table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>
<?php include("view/deuda/cobros-modal.php"); ?>
<?php include("view/venta/cierre-modal.php"); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $( ":input" ).attr("hola","hola");
    });
</script>

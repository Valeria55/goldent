<h1 class="page-header">Movimientos de la caja en la sesión 
<a class="btn btn-lg btn-primary " href="#cierreModal" class="btn btn-success" data-toggle="modal" data-target="#cierreModal" data-c="venta">Cierre de caja</a>
<a href="?c=venta&a=informediario&fecha=<?php echo date("Y-m-d"); ?>" class="btn btn-success" >Informe de ventas del día</a>
</h1>
<br><br><br>

<p> </p>
<table class="table table-striped display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>N</th>
        	<th>Fecha</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>N° de comprobante</th>
            <th>Monto</th> 
            <th>Pago en</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $user_id = (isset($_GET['id']))? $_GET['id']:$_SESSION['user_id'];
    $cierre = $this->model->Consultar($user_id); ?>
    <tr class="click">
            <td> </td>
            <td><?php echo date("d/m/Y H:i", strtotime($cierre->fecha_apertura)); ?></td>
            <td><?php echo "Apertura"; ?></td>
            <td><?php echo "Apertura de caja del día"; ?></td>
            <td><?php echo ""; ?></td>
            <td class="monto"><?php echo number_format($cierre->monto_apertura,0,".",","); ?></td>
            <td><?php echo "Caja chica"; ?></td>
    </tr>
    <?php 
    $sumaEfectivo = $cierre->monto_apertura;
    $caja1 = $cierre->monto_apertura;
    $caja2 = 0;
    $sumaTarjeta = 0;
    $sumaTransferencia = 0;
    $sumaGiro = 0;
    $c=1;
    foreach($this->model->ListarMovimientosSesion($user_id, $_GET['fecha']) as $r):
   // if($r->id_caja == 3){
        if(strlen($r->concepto)>=50){$concepto=substr($r->concepto, 0, 50)."...";}else{$concepto=$r->concepto;}?>
        <tr class="click" <?php if($r->anulado){echo "style='color:red'";}elseif($r->descuento>0){echo "style='color:#F39C12'";} ?>>
            <td><?php echo $c++; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->categoria.'('.$r->id_venta.')'; ?></td>
            <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo number_format($r->monto,0,".",","); ?></td>
            <td><?php echo $r->forma_pago; ?></td>
        </tr>
   <?php
    if($r->anulado != 1){
    $pagos[''.$r->forma_pago.'']+=$r->monto;
     $total +=$r->monto;
    }
   // }
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
<?php include("view/caja/transferencias.php"); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $( ":input" ).attr("hola","hola");
    });
</script>

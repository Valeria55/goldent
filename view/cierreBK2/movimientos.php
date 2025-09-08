<h1 class="page-header">Movimientos de la caja en la sesión <a class="btn btn-lg btn-primary " href="#cierreModal" class="btn btn-success" data-toggle="modal" data-target="#cierreModal" data-c="venta">Cierre de caja</a></h1>
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
            <th>Forma de pago</th>
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
            <td class="monto"><?php echo number_format($cierre->monto_apertura,2,".",","); ?></td>
            <td><?php echo "Efectivo"; ?></td>
        </tr>
    <?php 
    $sumaEfectivo = $cierre->monto_apertura;
    $sumaTarjeta = 0;
    $sumaTransferencia = 0;
    $sumaGiro = 0;
    $c=1;
    foreach($this->model->ListarMovimientosSesion($user_id) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:red'";}elseif($r->descuento>0){echo "style='color:#F39C12'";} ?>>
            <td><?php echo $c++; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->categoria; ?></td>
            <td><?php echo $r->concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo number_format($r->monto,2,".",","); ?></td>
            <td><?php echo $r->forma_pago; ?></td>
        </tr>
    <?php 
    $pos = strpos($r->forma_pago, "Efectivo");
    if(!$r->anulado && $pos !== false) {
    $sumaEfectivo +=  $r->monto;
    }
    
    $pos = strpos($r->forma_pago, "Giro");
    if(!$r->anulado && $pos !== false) {
    $sumaGiro +=  $r->monto;
    }
    if(!$r->anulado && $r->forma_pago == "Tarjeta") {
        $sumaTarjeta +=  $r->monto;
    }
    
    $pos = strpos($r->forma_pago, "Transferencia");
    if(!$r->anulado && $pos !== false) {
        $sumaTransferencia +=  $r->monto;
    }
    endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total efectivo: </th>
            <th class="monto" id="monto_total"><?php echo number_format($sumaEfectivo,0,".",","); ?></th> 
            <th></th>
        </tr>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total tarjeta: </th>
            <th><?php echo number_format($sumaTarjeta,0,".",","); ?></th> 
            <th></th>
        </tr>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total transferencia: </th>
            <th><?php echo number_format($sumaTransferencia,0,".",","); ?></th> 
            <th></th>
        </tr>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total Giros: </th>
            <th><?php echo number_format($sumaGiro,0,".",","); ?></th> 
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

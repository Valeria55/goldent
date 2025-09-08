<h1 class="page-header">Movimientos de la caja</h1>
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
            <th>Ingreso</th>
            <th>Egreso</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $sumaEfectivo = 0;
    $sumaCheque = 0;
    $sumaTarjeta = 0;
    $sumaTransferencia = 0;
    $sumaGiro = 0;
    $c=1;
    foreach($this->model->ListarMovimientosCaja($_GET['id_caja']) as $r):
    if(strlen($r->concepto)>=50){$concepto=substr($r->concepto, 0, 50)."...";}else{$concepto=$r->concepto;}
    if($r->monto>0){
        $ingreso = number_format($r->monto,2,".",",");
        $egreso = "";
    }else{
        $ingreso = "";
        $egreso = number_format(($r->monto*-1),2,".",",");
    } ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:red'";}elseif($r->descuento>0){echo "style='color:#F39C12'";} ?>>
            <td><?php echo $c++; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->categoria; ?></td>
            <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo $ingreso; ?></td>
            <td><?php echo $egreso; ?></td>
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
    
    $pos = strpos($r->forma_pago, "Cheque");
    if(!$r->anulado && $pos !== false) {
        $sumaCheque +=  $r->monto;
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
            <th>Total:</th>
            <th class="monto" id="monto_total"><?php echo number_format(($sumaEfectivo+$sumaTransferencia+$sumaCheque),2,".",","); ?></th> 
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

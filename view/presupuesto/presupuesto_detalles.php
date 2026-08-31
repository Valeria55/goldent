 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Detalles del presupuesto</h1> 
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
            <th>Descuento(%)</th>
            <th>Total (Gs.)</th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     $sumatotal = 0;
     $id_presupuesto = $_GET['id_presupuesto'];
     foreach($this->presupuesto->ListarDetalle($id_presupuesto) as $r): 
        $total = (($r->precio_venta*$r->cantidad)-($r->precio_venta*$r->cantidad*($r->descuento/100)));
     ?>
        <tr>
            <td><a  class="btn btn-default" href="?c=venta&a=listarproducto&id_producto=<?php echo $r->id_producto; ?>"><?php echo $r->codigo; ?></a></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><?php echo number_format($total, 0, "," , "."); ?></td>
        </tr>
    <?php $sumatotal += $total ;endforeach; ?>
        
        
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total Gs: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal,0,",",".") ?></div></td>
        </tr>
    </tbody>
</table> 

<?php
$presu_info = $this->presupuesto->ObtenerId_presupuesto($id_presupuesto);
$id_ade_str = ($presu_info && !empty($presu_info->id_adelanto)) ? $presu_info->id_adelanto : null;
$adelantos_vinculados = array();
$totalAdelantosPresu = 0;
if (!empty($id_ade_str)) {
    require_once 'model/adelanto.php';
    $modelAde = new adelanto();
    $ids_a = array_map('trim', explode(',', $id_ade_str));
    foreach ($ids_a as $id_a) {
        if (!empty($id_a)) {
            $ad_obj = $modelAde->Obtener($id_a);
            if ($ad_obj) {
                $adelantos_vinculados[] = $ad_obj;
                $totalAdelantosPresu += floatval($ad_obj->monto);
            }
        }
    }
}
?>

<?php if (!empty($adelantos_vinculados)) : ?>
<h4 class="page-header" style="color: #17a2b8;"><i class="fa fa-hand-holding-usd"></i> Adelantos Vinculados</h4>
<table class="table table-striped table-bordered display responsive nowrap" width="100%">
    <thead>
        <tr style="background-color: #17a2b8; color:#fff">
            <th>N° Adelanto</th>
            <th>Fecha</th>
            <th>Forma de Pago</th>
            <th>Descripción</th>
            <th>Monto Adelantado (Gs.)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($adelantos_vinculados as $ad_item) : ?>
            <tr>
                <td>#<?php echo $ad_item->id; ?></td>
                <td><?php echo !empty($ad_item->fecha) ? date("d/m/Y H:i", strtotime($ad_item->fecha)) : '-'; ?></td>
                <td><?php echo htmlspecialchars($ad_item->forma_pago ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($ad_item->descripcion ?? '-'); ?></td>
                <td style="font-weight: bold; color: #17a2b8;"><?php echo number_format($ad_item->monto, 0, ",", "."); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #e9ecef; font-weight: bold;">
            <td colspan="4" align="right">Total Adelantos Vinculados:</td>
            <td style="color: #17a2b8;">Gs. <?php echo number_format($totalAdelantosPresu, 0, ",", "."); ?></td>
        </tr>
        <tr style="background-color: #d4edda; font-weight: bold; color: #155724;">
            <td colspan="4" align="right">Saldo Restante a Cobrar / Venta:</td>
            <td>Gs. <?php echo number_format($sumatotal - $totalAdelantosPresu, 0, ",", "."); ?></td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>

</div> 
</div>
</div>

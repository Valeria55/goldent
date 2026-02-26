 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Detalles de la venta</h1> 
<div align="center" width="30%"> 
    
</div>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Codigo</th>
            <th>Producto</th>
            <th>Paciente</th>
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
     $id_venta = $_GET['id'];
     $tabla = $_GET['tabla'];

     if($tabla == 'aprobado'){
         $query_venta = $this->venta->ListarAprobado($id_venta);
     }elseif($tabla == 'aprobar'){
        $query_venta = $this->venta->ListarAprobar($id_venta);
     }else{
        $query_venta = $this->venta->Listar($id_venta);
     }
     
     foreach($query_venta as $r): 
        $total = $r->total;
     ?>
        <tr>
            <td><a  class="btn btn-default" href="?c=venta&a=listarproducto&id_producto=<?php echo $r->id_producto; ?>"><?php echo $r->codigo; ?></a></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo $r->paciente ; ?></td>
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
            <td></td>
            <td></td>
            <td>Total Gs: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal,0,",",".") ?></div></td>
        </tr>
    </tbody>
</table> 

<h4 class="page-header">Pagos / Ingresos</h4>

<?php
$pagos = $this->ingreso->ObtenerCobro($id_venta);
$totalCobrado = 0;
foreach ($pagos as $p) {
    $totalCobrado += (float)$p->monto;
}

$resumenDeuda = $this->deuda->ResumenPorVenta($id_venta);
$saldoDeudores = 0;
if ($resumenDeuda && isset($resumenDeuda->saldo)) {
    $saldoDeudores = (float)$resumenDeuda->saldo;
}
?>

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla2">
    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Forma de pago</th>
            <th>Moneda</th>
            <th>Monto</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($pagos)) : ?>
            <?php foreach ($pagos as $p) : ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)($p->fecha ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)($p->comprobante ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$p->forma_pago, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$p->moneda, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                    <td><?php echo number_format((float)$p->monto, 0, ",", "."); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="5">Sin pagos registrados para esta venta.</td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="4"><strong>Total cobrado</strong></td>
            <td><strong><?php echo number_format($totalCobrado, 0, ",", "."); ?></strong></td>
        </tr>

        <?php if ($saldoDeudores > 0) : ?>
            <tr>
                <td colspan="4"><strong>Saldo pendiente en deudores</strong></td>
                <td><strong><?php echo number_format($saldoDeudores, 0, ",", "."); ?></strong></td>
            </tr>
        <?php else : ?>
            <tr>
                <td colspan="5"><strong>Sin saldo pendiente en deudores.</strong></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div> 
</div>
</div>

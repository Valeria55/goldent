<?php
$pagos = array();
$total = 0; // Inicializar total
foreach ($this->metodo->Listar() as $m) {
    $pagos[$m->metodo] = 0;
}
?>
<h1 class="page-header">Movimientos de la caja en la sesión (Convertido a Guaraníes)</h1>
<br><br><br>

<p> </p>
<table class="table table-striped display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Fecha</th>
            <th>Concepto</th>
            <th>Forma de pago</th>
            <th>Monto original</th>
            <th>Moneda</th>
            <th>Monto convertido (Gs.)</th>
        </tr>
    </thead>
    <tbody> 
        <?php
        $cierre_id = $_GET['id'];
        $cierre = $this->model->Obtener($cierre_id);

        // Agregar el monto de apertura convertido al efectivo y al total
        if (!isset($pagos['Efectivo'])) {
            $pagos['Efectivo'] = 0;
        }
        
        // Usar apertura total convertida en lugar de solo monto_apertura
        $pagos['Efectivo'] += $cierre->apertura_total_convertido;
        $total += $cierre->apertura_total_convertido;
        ?>
        <tr class="click" style="background-color: #e8f5e8;">
            <td><?php echo date("d/m/Y H:i", strtotime($cierre->fecha_apertura)); ?></td>
            <td><strong>APERTURA DE CAJA (TOTAL)</strong></td>
            <td>Efectivo</td>
            <td>-</td>
            <td>MIXTO</td>
            <td><strong><?php echo number_format($cierre->apertura_total_convertido, 2, ".", ","); ?></strong></td>
        </tr>
        
        <?php
        $c = 1;
        foreach ($this->model->ListarMovimientosSesionCerrada($cierre->id_usuario, $cierre->fecha_apertura, $cierre->fecha_cierre) as $r):
            $monto_mostrar = isset($r->monto_convertido) ? $r->monto_convertido : $r->monto;
            
            // Determinar la moneda original
            $moneda_original = 'GS'; // Por defecto
            if (isset($r->cambio) && $r->cambio != 1) {
                if ($r->cambio == $cierre->cot_real) {
                    $moneda_original = 'RS';
                } elseif ($r->cambio == $cierre->cot_dolar) {
                    $moneda_original = 'USD';
                }
            }
            
            if (!isset($pagos[$r->forma_pago])) {
                $pagos[$r->forma_pago] = 0;
            }
            $pagos[$r->forma_pago] += $monto_mostrar;
            $total += $monto_mostrar;
        ?>
            <tr class="click">
                <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                <td><?php echo $r->concepto; ?></td>
                <td><?php echo $r->forma_pago; ?></td>
                <td><?php echo number_format($r->monto, 2, ".", ","); ?></td>
                <td><?php echo $moneda_original; ?></td>
                <td><?php echo number_format($monto_mostrar, 2, ".", ","); ?></td>
            </tr>
        <?php
            $c++;
        endforeach; ?>
    </tbody>
    <tfoot> 
        <tr style="background-color: black; color:#fff">
            <th colspan="5"><strong>RESUMEN POR FORMA DE PAGO (Convertido a Gs.)</strong></th>
            <th></th>
        </tr>
        <?php foreach ($pagos as $metodo => $monto): 
            if ($monto != 0): ?>
        <tr>
            <th colspan="5"><?php echo $metodo; ?></th>
            <th><?php echo number_format($monto, 2, ".", ","); ?></th>
        </tr>
        <?php endif; 
        endforeach; ?>
        
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <th colspan="5">TOTAL GENERAL (Gs.)</th>
            <th><?php echo number_format($total, 2, ".", ","); ?></th>
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
    $(document).ready(function() {
        $(":input").attr("hola", "hola");
    });
</script>
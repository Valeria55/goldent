<h1 class="page-header">Movimientos de la caja.</h1>
<br><br><br>

<p> </p>
<table class="table table-striped display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>N°</th>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>N° de comprobante</th>
            <th data-priority="1">Ingreso</th>
            <th data-priority="2">Egreso</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sumaEfectivo = 0;
        $sumaCheque = 0;
        $sumaTarjeta = 0;
        $sumaTransferencia = 0;
        $sumaGiro = 0;
        $c = 1;
        $total_egreso = 0;
        $total_ingreso = 0;
        if (in_array($_GET['id_caja'], array(1)) && (!in_array($_SESSION['user_id'], array(6, 15)))) { // es caja chica y no es edison ni trinity
            $array_movimientos = $this->model->ListarMovimientosCajaUsuario($_GET['id_caja']);
        } else {
            $array_movimientos = $this->model->ListarMovimientosCajaAdmin($_GET['id_caja']);
        }
        foreach ($array_movimientos as $r) :
            if (strlen($r->concepto) >= 50) {
                $concepto = substr($r->concepto, 0, 50) . "...";
            } else {
                $concepto = $r->concepto;
            }

            if ($r->monto > 0) {
                $ingreso = number_format($r->monto, 2, ".", ",");
                $total_ingreso += ($r->monto);
                $egreso = "";
            } else {
                $ingreso = "";
                $egreso = number_format(($r->monto * -1), 2, ".", ",");
                $total_egreso += ($r->monto * -1);
            } ?>
            <tr class="click" <?php if ($r->anulado) {
                                    echo "style='color:red'";
                                } elseif ($r->descuento > 0) {
                                    echo "style='color:#F39C12'";
                                } ?>>
                <td><?php echo $c++; ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                <td><?php echo $r->usuario; ?></td>
                <td><?php echo $r->categoria; ?></td>
                <?php
                $caja_destino = "";
                if ($r->id_usuario_transferencia > 0) {
                    $caja_destino = "<br><b>Cajero receptor:</b> " . ($this->usuario->Obtener($r->id_usuario)->user ?? 'Tesorería') . 
                                    "<br><b>Cajero remitente:</b> " . $this->usuario->Obtener($r->id_usuario_transferencia)->user;
                } ?>
                <td title="<?php echo $r->concepto; ?>"><?php echo $concepto . $caja_destino; ?></td>
                <td><?php echo $r->comprobante; ?></td>
                <td><?php echo $ingreso; ?></td>
                <td><?php echo $egreso; ?></td>
            </tr>
        <?php
            $pos = strpos($r->forma_pago, "Efectivo");
            if (!$r->anulado && $pos !== false) {
                $sumaEfectivo +=  $r->monto;
            }

            $pos = strpos($r->forma_pago, "Giro");
            if (!$r->anulado && $pos !== false) {
                $sumaGiro +=  $r->monto;
            }

            if (!$r->anulado && $r->forma_pago == "Tarjeta") {
                $sumaTarjeta +=  $r->monto;
            }

            $pos = strpos($r->forma_pago, "Cheque");
            if (!$r->anulado && $pos !== false) {
                $sumaCheque +=  $r->monto;
            }

            $pos = strpos($r->forma_pago, "Transferencia");
            if (!$r->anulado && $pos !== false) {
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
            <th></th>
            <th colspan="">Total de movimientos:</th>
            <th class="monto" id="monto_total"><?php echo number_format(($total_ingreso), 2, ".", ","); ?></th>
            <th class="monto" id="monto_total"><?php echo number_format(($total_egreso), 2, ".", ","); ?></th>
        </tr>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th colspan="">Disponible:</th>
            <th colspan="2" class="monto" id="monto_total"><?php echo number_format(($sumaEfectivo + $sumaTransferencia + $sumaCheque), 2, ".", ","); ?></th>
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
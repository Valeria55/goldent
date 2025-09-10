<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Detalles de Pagos</h1>
<div align="center" width="30%">

</div>

<div class="table-responsive">

    <table class="table table-striped table-bordered display responsive nowrap">

        <thead>
            <tr style="background-color: #5DACCD; color:#fff">
                <th>Fecha</th>
                <th>Comprobante</th>
                <th>Monto</th>
                <th>Moneda</th>
                <th>Cotización</th>
                <th>Equivalente Gs</th>
            </tr>
        </thead>
        <tbody> <?php
                $sumatotal = 0;
                $id_acreedor = isset($_GET['acreedor']) ? $_GET['acreedor'] : (isset($_GET['id']) ? $_GET['id'] : $id_acreedor);
                foreach ($this->egreso->ListarAcreedor($id_acreedor) as $r):
                    // Calcular equivalente en Gs si no es Gs
                    $equivalente_gs = $r->monto;
                    if ($r->moneda == 'USD' && $r->cambio > 0) {
                        $equivalente_gs = $r->monto * $r->cambio;
                    } elseif ($r->moneda == 'RS' && $r->cambio > 0) {
                        $equivalente_gs = $r->monto * $r->cambio;
                    }
                    $sumatotal += $equivalente_gs;
                ?>
                <tr>
                    <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                    <td><?php echo $r->comprobante; ?></td>
                    <td><?php echo number_format($r->monto, ($r->moneda == 'Gs' ? 0 : 2), ",", "."); ?></td>
                    <td>
                        <?php
                        $moneda_display = $r->moneda ?? 'Gs';
                        echo $moneda_display;
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($r->moneda == 'Gs' || $r->moneda == '' || $r->moneda == null) {
                            echo '1';
                        } else {
                            echo number_format($r->cambio ?? 1, 0, ",", ".");
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        echo number_format($equivalente_gs, 0, ",", ".");
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>Total:</strong></td>
                <td><strong>Gs <?php echo number_format($sumatotal, 0, ",", ".") ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>
</div>
</div>
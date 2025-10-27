<table id="pagados-table" class="table table-striped table-bordered display responsive nowrap datatable">
    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Cliente</th>
            <th>Concepto</th>
            <th>Monto</th>
            <th>Saldo</th>
            <th>Fecha</th>
            <th>Vencimiento</th>
            <th></th>
        </tr>
    </thead>

    <!-- tbody para Pagados -->
    <tbody>
        <?php 
        $suma = 0;
        foreach ($this->model->ListarSaldados() as $s) : ?>
            <tr class="click">
                <td><a class="btn btn-default" href="#rangoModal" class="btn btn-success" data-toggle="modal" data-target="#rangoModal" data-id="<?php echo $s->id_cliente; ?>"><?php echo $s->nombre; ?></a>
                </td>
                <td><?php echo $s->concepto; ?></td>
                <td style="padding-right:3px" align="right"><?php echo number_format($s->monto, 0, ",", ","); ?></td>
                <td style="padding-right:3px" align="right"><?php echo number_format($s->saldo, 0, ",", ","); ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($s->fecha)); ?></td>
                <td><?php echo (date("Y", strtotime($s->vencimiento)) > 2000) ? date("d/m/Y", strtotime($s->vencimiento)) : ""; ?></td>
                <?php if ($s->id_venta) : ?>
                    <td>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $s->id; ?>">Cobros</a>
                        <a href="#detallesModal" class="btn btn-default" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $s->id_venta; ?>">Venta</a>
                    </td>
                <?php else : ?>
                    <td>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $s->id; ?>">Cobros</a>
                    </td>
                <?php endif ?>
            </tr>
        <?php 
            $suma += $s->monto;
        endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #666666; color:#fff" >
            <td></td>
            <td></td>
            <td style="padding-right:3px" align="right">TOTAL: <?php echo number_format($suma,0,".",","); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
    </tfoot>
</table>
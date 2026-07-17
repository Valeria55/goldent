<h1 class="page-header">Transferencias a Paseo de la Sonrisa</h1>
<a class="btn btn-primary pull-right" href="?c=transferencia_externa&a=nuevo">Nueva Transferencia</a>
<br><br><br>

<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">
    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Fecha y Hora</th>
            <th>Monto</th>
            <th>Concepto</th>
            <th>Enviado por</th>
            <th>Estado</th>
            <th>Motivo (Si aplica)</th>
            <th>Procesado</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r): ?>
        <tr>
            <td><?php echo $r->id; ?></td>
            <td><?php echo date("d/m/Y", strtotime($r->fecha_envio)) . " " . $r->hora_envio; ?></td>
            <td><?php echo number_format($r->monto, 0, ".", ","); ?> Gs.</td>
            <td><?php echo $r->concepto; ?></td>
            <td><?php echo $r->quien_transfiere; ?></td>
            <td>
                <?php 
                if($r->estado == 'PENDIENTE') echo '<span class="label label-warning">PENDIENTE</span>';
                elseif($r->estado == 'APROBADA') echo '<span class="label label-success">APROBADA</span>';
                elseif($r->estado == 'ANULADA') echo '<span class="label label-danger">ANULADA</span>';
                else echo $r->estado;
                ?>
            </td>
            <td><?php echo $r->motivo_resolucion; ?></td>
            <td><?php echo $r->fecha_procesado ? date("d/m/Y H:i", strtotime($r->fecha_procesado)) : '-'; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

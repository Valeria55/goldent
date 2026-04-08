<h1 class="page-header">Lista de Adelantos</h1>
<a class="btn btn-primary pull-right" href="#adelantoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="adelanto">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>ID</th>
            <th>Cliente</th>
            <th>Monto</th>
            <th>Forma de Pago</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Creado por</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->model->Listar() as $r): ?>
        <tr class="<?php echo $r->anulado == 1 ? 'danger' : ''; ?>">
            <td><?php echo $r->id; ?></td>
            <td><?php echo $r->cliente_nombre; ?></td>
            <td><?php echo number_format($r->monto, 0, ',', '.'); ?></td>
            <td><?php echo $r->forma_pago; ?></td>
            <td><?php echo $r->descripcion; ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->usuario_creador; ?></td>
            <td>
                <?php if ($r->anulado == 1): ?>
                    <span class="label label-danger">ANULADO (por <?php echo $r->usuario_anulador; ?>)</span>
                <?php elseif ($r->estado == 'PENDIENTE'): ?>
                    <span class="label label-warning">PENDIENTE</span>
                <?php else: ?>
                    <span class="label label-success">UTILIZADO</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($r->anulado == 0 && $r->estado == 'PENDIENTE'): ?>
                    <a class="btn btn-warning btn-sm edit" href="#crudModal" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id; ?>" data-c="adelanto">Editar</a>
                    <a class="btn btn-danger btn-sm" onclick="javascript:return confirm('¿Seguro de anular este adelanto?');" href="?c=adelanto&a=Anular&id=<?php echo $r->id; ?>">Anular</a>
                <?php endif; ?>
                <?php if ($_SESSION['nivel'] <= 1): ?>
                    <!-- <a class="btn btn-default btn-sm" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=adelanto&a=Eliminar&id=<?php // echo $r->id; ?>">Eliminar</a> -->
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include "view/crud-modal.php"; ?>

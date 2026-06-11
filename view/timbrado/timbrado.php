<h1 class="page-header">Lista de Timbrados</h1>
<a class="btn btn-primary pull-right" href="#crudModal" data-toggle="modal" data-target="#crudModal" data-c="timbrado">Agregar Timbrado</a>
<br><br><br>
<div class="table-responsive">
    <table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">
        <thead>
            <tr style="background-color: #337ab7; color:#fff">
                <th>ID</th>
                <th>Timbrado</th>
                <th>Establecimiento</th>
                <th>Punto Expedición</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Rango Facturación</th>
                <th>Estado</th>
                <th style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($this->model->Listar() as $r): ?>
            <tr class="click">
                <td><?php echo $r->id; ?></td>
                <td><strong><?php echo htmlspecialchars($r->timbrado); ?></strong></td>
                <td><?php echo str_pad($r->establecimiento, 3, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo str_pad($r->punto_expedicion, 3, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo date("d/m/Y", strtotime($r->fecha_inicio)); ?></td>
                <td><?php echo date("d/m/Y", strtotime($r->fecha_fin)); ?></td>
                <td><?php echo number_format($r->numero_inicio, 0, ',', '.') . ' - ' . number_format($r->numero_fin, 0, ',', '.'); ?></td>
                <td>
                    <?php if ($r->estado == 1): ?>
                        <span class="label label-success" style="font-size: 11px; padding: 4px 8px;">Activo</span>
                    <?php else: ?>
                        <span class="label label-default" style="font-size: 11px; padding: 4px 8px;">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="btn-group">
                        <?php if ($r->estado != 1): ?>
                            <a class="btn btn-xs btn-success" href="?c=timbrado&a=Activar&id=<?php echo $r->id; ?>" style="margin-right: 3px;"><i class="fas fa-check-circle"></i> Activar</a>
                        <?php endif; ?>
                        <a class="btn btn-xs btn-warning edit" href="#crudModal" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="timbrado" style="margin-right: 3px;"><i class="fas fa-edit"></i> Editar</a>
                        <a class="btn btn-xs btn-danger delete" href="?c=timbrado&a=Eliminar&id=<?php echo $r->id; ?>" onclick="return confirm('¿Está seguro de eliminar este timbrado?');"><i class="fas fa-trash-alt"></i> Eliminar</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include("view/crud-modal.php"); ?>

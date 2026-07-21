<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
<h1 class="page-header">Transferencias a Paseo de la Sonrisa</h1>
<a class="btn btn-primary pull-right" href="#crudModal" data-toggle="modal" data-target="#crudModal" data-c="transferencia_externa">Nueva Transferencia</a>
<br><br><br>

<table class="table table-striped table-bordered display responsive  datatable" width="100%">
    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Enviado por</th>
            <th>Estado</th>
            <th>Concepto</th>
            <th>Comprobante</th>
            <th>Monto</th>
            <th>Motivo (Si aplica)</th>
            <th>Procesado</th>
            <th style="width: 80px;">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->model->Listar() as $r): ?>
            <tr>
                <td><?php echo $r->id; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($r->quien_transfiere); ?></strong><br>
                    <small class="text-muted"><?php echo date("d/m/Y", strtotime($r->fecha_envio)) . " " . $r->hora_envio; ?></small>
                </td>
                <td>
                    <?php
                    if ($r->estado == 'PENDIENTE') echo '<span class="label label-warning">PENDIENTE</span>';
                    elseif ($r->estado == 'APROBADA') echo '<span class="label label-success">APROBADA</span>';
                    elseif ($r->estado == 'ANULADA') echo '<span class="label label-danger">ANULADA</span>';
                    else echo $r->estado;
                    ?>
                </td>
                <td><?php echo htmlspecialchars($r->concepto); ?></td>
                <td>
                    <?php 
                    $comprobante = $r->comprobante_url;
                    $text_url = '';
                    $file_url = '';

                    if (strpos($comprobante, '|') !== false) {
                        list($text_url, $file_url) = explode('|', $comprobante, 2);
                    } else {
                        if (strpos($comprobante, '/transferencias/comprobante_') !== false) {
                            $file_url = $comprobante;
                        } else {
                            $text_url = $comprobante;
                        }
                    }

                    if (!empty($text_url)): ?>
                        <div style="margin-bottom: 5px;">
                            <?php if (filter_var($text_url, FILTER_VALIDATE_URL) || strpos($text_url, 'http://') === 0 || strpos($text_url, 'https://') === 0): ?>
                                <a href="<?php echo htmlspecialchars($text_url); ?>" target="_blank" class="btn btn-info btn-xs">
                                    <i class="fa fa-link"></i> Ver Enlace
                                </a>
                            <?php else: ?>
                                <span><?php echo htmlspecialchars($text_url); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($file_url)): ?>
                        <div>
                            <a href="<?php echo htmlspecialchars($file_url); ?>" data-fancybox="gallery" class="btn btn-default btn-xs">
                                <i class="fa fa-image"></i> Ver Archivo/Imagen
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($text_url) && empty($file_url)): ?>
                        <span class="text-muted">Sin comprobante</span>
                    <?php endif; ?>
                </td>
                <td><?php echo number_format($r->monto, 0, ".", ","); ?> Gs.</td>
                <td><?php echo htmlspecialchars($r->motivo_resolucion); ?></td>
                <td>
                    <?php if ($r->fecha_procesado): ?>
                        <strong><?php echo !empty($r->procesado_por) ? htmlspecialchars($r->procesado_por) : '-'; ?></strong><br>
                        <small class="text-muted"><?php echo date("d/m/Y H:i", strtotime($r->fecha_procesado)); ?></small>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($r->estado == 'PENDIENTE'): ?>
                        <a class="btn btn-xs btn-warning edit" href="#crudModal" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id; ?>" data-c="transferencia_externa">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include("view/crud-modal.php"); ?>
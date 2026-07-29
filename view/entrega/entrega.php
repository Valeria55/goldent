<?php
$totalPendientes = count($pendientes);
$totalEntregados = count($entregados);
$sumaMontoPercibido = 0;
foreach ($entregados as $ent) {
    $sumaMontoPercibido += floatval($ent->importe_percibido);
}
?>

<div class="container-fluid" style="padding: 15px;">
    <!-- Encabezado de página -->
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-xs-8 col-sm-8 col-md-8">
            <h2 style="margin: 0; font-weight: 700; color: #2c3e50; font-size: 22px;">
                <i class="fa fa-truck text-primary"></i> Gestión de Entregas
            </h2>
            <p class="text-muted hidden-xs" style="margin-top: 3px; font-size: 13px;">
                Panel optimizado para dispositivos móviles y escritorio.
            </p>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 text-right">
            <button class="btn btn-default btn-sm" onclick="location.reload();">
                <i class="fa fa-refresh"></i> <span class="hidden-xs">Actualizar</span>
            </button>
        </div>
    </div>

    <!-- Tarjetas de Métricas (KPIs) -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-6 col-sm-4 col-md-4" style="padding-left: 5px; padding-right: 5px;">
            <div class="panel" style="border-radius: 8px; border-left: 4px solid #f0ad4e; box-shadow: 0 2px 8px rgba(0,0,0,0.06); background: white; margin-bottom: 10px;">
                <div class="panel-body" style="padding: 12px 15px;">
                    <div class="row">
                        <div class="col-xs-3 text-center" style="padding: 0;">
                            <i class="fa fa-clock-o fa-2x text-warning"></i>
                        </div>
                        <div class="col-xs-9 text-right" style="padding-left: 5px;">
                            <div style="font-size: 22px; font-weight: 700; color: #333;"><?php echo $totalPendientes; ?></div>
                            <div style="color: #777; font-size: 11px; font-weight: 600; text-transform: uppercase;">Pendientes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-6 col-sm-4 col-md-4" style="padding-left: 5px; padding-right: 5px;">
            <div class="panel" style="border-radius: 8px; border-left: 4px solid #5cb85c; box-shadow: 0 2px 8px rgba(0,0,0,0.06); background: white; margin-bottom: 10px;">
                <div class="panel-body" style="padding: 12px 15px;">
                    <div class="row">
                        <div class="col-xs-3 text-center" style="padding: 0;">
                            <i class="fa fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div class="col-xs-9 text-right" style="padding-left: 5px;">
                            <div style="font-size: 22px; font-weight: 700; color: #333;"><?php echo $totalEntregados; ?></div>
                            <div style="color: #777; font-size: 11px; font-weight: 600; text-transform: uppercase;">Finalizados</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-4" style="padding-left: 5px; padding-right: 5px;">
            <div class="panel" style="border-radius: 8px; border-left: 4px solid #337ab7; box-shadow: 0 2px 8px rgba(0,0,0,0.06); background: white; margin-bottom: 10px;">
                <div class="panel-body" style="padding: 12px 15px;">
                    <div class="row">
                        <div class="col-xs-3 text-center" style="padding: 0;">
                            <i class="fa fa-money fa-2x text-primary"></i>
                        </div>
                        <div class="col-xs-9 text-right" style="padding-left: 5px;">
                            <div style="font-size: 18px; font-weight: 700; color: #28a745;">Gs. <?php echo number_format($sumaMontoPercibido, 0, ',', '.'); ?></div>
                            <div style="color: #777; font-size: 11px; font-weight: 600; text-transform: uppercase;">Total Cobrado</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas e Interfaz Principal -->
    <div class="panel panel-default" style="border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: none;">
        <div class="panel-heading" style="background: #ffffff; border-bottom: 2px solid #f1f1f1; padding: 10px 15px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
            <div class="row">
                <div class="col-xs-8 col-sm-8">
                    <ul class="nav nav-pills" id="entregaTabs">
                        <li class="active">
                            <a href="#tabPendientes" data-toggle="tab" style="font-weight: 600; border-radius: 20px; padding: 6px 14px; font-size: 13px;">
                                <i class="fa fa-clock-o text-warning"></i> Pendientes <span class="badge" style="background-color: #f0ad4e;"><?php echo $totalPendientes; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="#tabEntregados" data-toggle="tab" style="font-weight: 600; border-radius: 20px; padding: 6px 14px; font-size: 13px;">
                                <i class="fa fa-check text-success"></i> Entregados <span class="badge" style="background-color: #5cb85c;"><?php echo $totalEntregados; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-xs-4 col-sm-4 text-right">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default btn-sm active" id="btnVistaCards" title="Vista Móvil (Tarjetas)">
                            <input type="radio" name="optionsModoVista" checked><i class="fa fa-th-large"></i>
                        </label>
                        <label class="btn btn-default btn-sm" id="btnVistaTabla" title="Vista Tabla Clásica">
                            <input type="radio" name="optionsModoVista"><i class="fa fa-table"></i>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-body" style="padding: 15px;">
            <div class="tab-content">
                <!-- TAB PEDIDOS PENDIENTES -->
                <div class="tab-pane fade in active" id="tabPendientes">
                    
                    <!-- VISTA CARDS RESPONSIVE (MÓVIL PRIMERO) -->
                    <div id="contenedorCardsPendientes" class="row">
                        <?php if (count($pendientes) == 0) : ?>
                            <div class="col-xs-12 text-center text-muted" style="padding: 40px 15px;">
                                <i class="fa fa-check-circle-o fa-4x text-success" style="opacity: 0.5;"></i>
                                <h4 style="margin-top: 15px;">¡Excelente! No hay entregas pendientes</h4>
                            </div>
                        <?php else : ?>
                            <?php foreach ($pendientes as $p) : 
                                $totalEstimado = ($p->id_venta > 0 && $p->total_venta > 0) ? $p->total_venta : $p->total_presupuesto;
                                $observacion = !empty($p->observacion_presupuesto) ? $p->observacion_presupuesto : ($p->observaciones ?? '');
                            ?>
                                <div class="col-xs-12 col-sm-6 col-md-4 card-entrega-item" style="margin-bottom: 20px;">
                                    <div class="panel panel-default" style="border-radius: 10px; border: 1px solid #e0e0e0; box-shadow: 0 3px 10px rgba(0,0,0,0.06); transition: all 0.2s;">
                                        <!-- Header del Card con Orden de Entrega -->
                                        <div class="panel-heading" style="background: linear-gradient(135deg, #1d5b8c 0%, #337ab7 100%); color: white; border-top-left-radius: 9px; border-top-right-radius: 9px; padding: 12px 15px;">
                                            <div class="row">
                                                <div class="col-xs-7">
                                                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9;">ORDEN DE ENTREGA</span><br>
                                                    <strong style="font-size: 18px;">
                                                        <i class="fa fa-file-text-o"></i> Presupuesto #<?php echo $p->id_presupuesto; ?>
                                                    </strong>
                                                </div>
                                                <div class="col-xs-5 text-right">
                                                    <span class="label label-warning" style="font-size: 11px; padding: 4px 8px;">
                                                        <i class="fa fa-clock-o"></i> Pendiente
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cuerpo del Card -->
                                        <div class="panel-body" style="padding: 15px; font-size: 14px;">
                                            <!-- Cliente y RUC -->
                                            <div style="margin-bottom: 10px;">
                                                <div style="color: #777; font-size: 11px; font-weight: 600; text-transform: uppercase;">Cliente</div>
                                                <strong style="font-size: 16px; color: #2c3e50;">
                                                    <i class="fa fa-user text-primary"></i> <?php echo htmlspecialchars($p->cliente_nombre ? $p->cliente_nombre : 'Cliente Ocasional'); ?>
                                                </strong>
                                                <?php if (!empty($p->cliente_ruc)) : ?>
                                                    <div style="font-size: 12px; color: #666; margin-top: 2px;">
                                                        <strong>RUC/CI:</strong> <?php echo htmlspecialchars($p->cliente_ruc); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Observación del Presupuesto (SI EXISTE) -->
                                            <?php if (!empty($observacion)) : ?>
                                                <div style="background-color: #fff8e1; border-left: 4px solid #ffb300; padding: 10px; border-radius: 4px; margin-bottom: 12px;">
                                                    <div style="color: #b78103; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                                        <i class="fa fa-commenting-o"></i> Observación de la Orden
                                                    </div>
                                                    <div style="color: #444; font-size: 13px; font-weight: 500; margin-top: 3px;">
                                                        <?php echo nl2br(htmlspecialchars($observacion)); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Dirección y Teléfono -->
                                            <div style="margin-bottom: 12px; background: #f9f9f9; padding: 10px; border-radius: 6px;">
                                                <div style="margin-bottom: 5px;">
                                                    <i class="fa fa-map-marker text-danger"></i> 
                                                    <strong>Dirección:</strong> 
                                                    <span style="color: #333;"><?php echo htmlspecialchars($p->cliente_direccion ? $p->cliente_direccion : 'Sin dirección especificada'); ?></span>
                                                </div>
                                                <div>
                                                    <i class="fa fa-phone text-success"></i> 
                                                    <strong>Teléfono:</strong> 
                                                    <?php if (!empty($p->cliente_telefono)) : ?>
                                                        <a href="tel:<?php echo htmlspecialchars($p->cliente_telefono); ?>" class="btn btn-default btn-xs" style="font-weight: bold; color: #28a745; margin-left: 5px;">
                                                            <i class="fa fa-phone"></i> <?php echo htmlspecialchars($p->cliente_telefono); ?>
                                                        </a>
                                                        <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $p->cliente_telefono); ?>" target="_blank" class="btn btn-success btn-xs" style="margin-left: 3px;" title="WhatsApp">
                                                            <i class="fa fa-whatsapp"></i> WhatsApp
                                                        </a>
                                                    <?php else : ?>
                                                        <span class="text-muted">No registrado</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Responsable & Monto Estimado -->
                                            <div class="row" style="margin-bottom: 10px; font-size: 12px;">
                                                <div class="col-xs-6">
                                                    <span class="text-muted">Responsable:</span><br>
                                                    <span class="label label-info"><?php echo htmlspecialchars($p->responsable_nombre); ?></span>
                                                </div>
                                                <div class="col-xs-6 text-right">
                                                    <span class="text-muted">Total Estimado:</span><br>
                                                    <strong style="font-size: 14px; color: #28a745;">Gs. <?php echo number_format($totalEstimado, 0, ',', '.'); ?></strong>
                                                </div>
                                            </div>

                                            <!-- Botones Táctiles Tareas Móviles Grandes -->
                                            <div style="margin-top: 15px;">
                                                <button type="button" class="btn btn-success btn-block btn-lg btnMarcarEntregado" 
                                                        data-id="<?php echo $p->id; ?>"
                                                        data-presupuesto="<?php echo $p->id_presupuesto; ?>"
                                                        data-cliente="<?php echo htmlspecialchars($p->cliente_nombre ? $p->cliente_nombre : 'Cliente Ocasional'); ?>"
                                                        data-direccion="<?php echo htmlspecialchars($p->cliente_direccion); ?>"
                                                        data-responsable="<?php echo htmlspecialchars($p->responsable_nombre); ?>"
                                                        data-monto="<?php echo number_format($totalEstimado, 0, ',', '.'); ?>"
                                                        style="font-weight: 700; border-radius: 6px; padding: 12px; font-size: 15px; box-shadow: 0 2px 5px rgba(40,167,69,0.3);">
                                                    <i class="fa fa-check-circle"></i> MARCAR COMO ENTREGADO
                                                </button>
                                                
                                                <button type="button" class="btn btn-default btn-block btn-sm btnVerDetalle" data-id="<?php echo $p->id; ?>" style="margin-top: 8px; font-weight: 500;">
                                                    <i class="fa fa-info-circle text-info"></i> Ver Detalles / Auditoría
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- VISTA TABLA (ESCRITORIO OPCIONAL) -->
                    <div id="contenedorTablaPendientes" class="table-responsive" style="display: none;">
                        <table class="table table-striped table-hover dataTable" id="tablaPendientes" style="width: 100%;">
                            <thead>
                                <tr style="background-color: #f8f9fa; color: #333;">
                                    <th>ID</th>
                                    <th>Orden (Presupuesto)</th>
                                    <th>Cliente y RUC</th>
                                    <th>Observación Orden</th>
                                    <th>Dirección / Teléfono</th>
                                    <th>Responsable</th>
                                    <th>Fecha Asignación</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendientes as $p) : 
                                    $totalEstimado = ($p->id_venta > 0 && $p->total_venta > 0) ? $p->total_venta : $p->total_presupuesto;
                                    $observacion = !empty($p->observacion_presupuesto) ? $p->observacion_presupuesto : ($p->observaciones ?? '');
                                ?>
                                    <tr>
                                        <td><strong>#<?php echo $p->id; ?></strong></td>
                                        <td>
                                            <span class="label label-primary" style="font-size: 12px;">Presupuesto #<?php echo $p->id_presupuesto; ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($p->cliente_nombre ? $p->cliente_nombre : 'Cliente Ocasional'); ?></strong><br>
                                            <small class="text-muted"><?php echo $p->cliente_ruc; ?></small>
                                        </td>
                                        <td>
                                            <?php if (!empty($observacion)) : ?>
                                                <small class="text-warning font-weight-bold"><i class="fa fa-comment"></i> <?php echo htmlspecialchars($observacion); ?></small>
                                            <?php else : ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($p->cliente_direccion ? $p->cliente_direccion : 'Sin dirección'); ?><br>
                                            <small class="text-muted"><i class="fa fa-phone"></i> <?php echo $p->cliente_telefono; ?></small>
                                        </td>
                                        <td><span class="label label-info"><?php echo htmlspecialchars($p->responsable_nombre); ?></span></td>
                                        <td><small><?php echo date("d/m/Y H:i", strtotime($p->fecha_asignacion)); ?></small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success btn-sm btnMarcarEntregado" 
                                                    data-id="<?php echo $p->id; ?>"
                                                    data-presupuesto="<?php echo $p->id_presupuesto; ?>"
                                                    data-cliente="<?php echo htmlspecialchars($p->cliente_nombre ? $p->cliente_nombre : 'Cliente Ocasional'); ?>"
                                                    data-direccion="<?php echo htmlspecialchars($p->cliente_direccion); ?>"
                                                    data-responsable="<?php echo htmlspecialchars($p->responsable_nombre); ?>"
                                                    data-monto="<?php echo number_format($totalEstimado, 0, ',', '.'); ?>">
                                                <i class="fa fa-check"></i> Entregado
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm btnVerDetalle" data-id="<?php echo $p->id; ?>">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB HISTORIAL ENTREGADOS -->
                <div class="tab-pane fade" id="tabEntregados">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover dataTable" id="tablaEntregados" style="width: 100%;">
                            <thead>
                                <tr style="background-color: #f8f9fa; color: #333;">
                                    <th>ID</th>
                                    <th>Orden Presupuesto</th>
                                    <th>Cliente</th>
                                    <th>Responsable</th>
                                    <th>Entregado Por</th>
                                    <th>Fecha / Hora Entrega</th>
                                    <th>Importe Percibido</th>
                                    <th>Método Pago</th>
                                    <th>Comprobantes</th>
                                    <th class="text-center">Auditoría</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($entregados as $e) : ?>
                                    <tr>
                                        <td><strong>#<?php echo $e->id; ?></strong></td>
                                        <td>
                                            <span class="label label-primary">Presup. #<?php echo $e->id_presupuesto; ?></span>
                                            <?php if ($e->id_venta) : ?>
                                                <span class="label label-success">Venta #<?php echo $e->id_venta; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($e->cliente_nombre ? $e->cliente_nombre : 'Cliente Ocasional'); ?></strong></td>
                                        <td><span class="label label-info"><?php echo htmlspecialchars($e->responsable_nombre); ?></span></td>
                                        <td><span class="label label-success"><?php echo htmlspecialchars($e->usuario_entrega_nombre); ?></span></td>
                                        <td><small><?php echo date("d/m/Y H:i", strtotime($e->fecha_hora_entrega)); ?></small></td>
                                        <td><strong class="text-success">Gs. <?php echo number_format($e->importe_percibido, 0, ',', '.'); ?></strong></td>
                                        <td><span class="label label-default"><?php echo htmlspecialchars($e->metodo_pago); ?></span></td>
                                        <td class="text-center">
                                            <span class="badge" style="background-color: #337ab7;">
                                                <i class="fa fa-file"></i> <?php echo $e->total_comprobantes; ?> archivos
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info btn-sm btnVerDetalle" data-id="<?php echo $e->id; ?>">
                                                <i class="fa fa-search"></i> Ver Auditoría
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once 'confirmar-modal.php';
require_once 'detalles-modal.php';
?>

<script>
$(document).ready(function() {
    // Alternar entre Vista de Cards (Móvil) y Vista de Tabla (Escritorio)
    $('#btnVistaCards').on('click', function() {
        $('#contenedorCardsPendientes').show();
        $('#contenedorTablaPendientes').hide();
    });

    $('#btnVistaTabla').on('click', function() {
        $('#contenedorCardsPendientes').hide();
        $('#contenedorTablaPendientes').show();
    });

    // Inicializar DataTables
    if ($.fn.DataTable) {
        $('#tablaPendientes, #tablaEntregados').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "order": [[ 0, "desc" ]]
        });
    }

    // Evento Abrir Modal Confirmar Entrega
    $(document).on('click', '.btnMarcarEntregado', function() {
        var id = $(this).data('id');
        var presupuesto = $(this).data('presupuesto');
        var cliente = $(this).data('cliente');
        var direccion = $(this).data('direccion');
        var responsable = $(this).data('responsable');
        var monto = $(this).data('monto');

        $('#modal_confirmar_id_entrega').val(id);
        $('#info_id_presupuesto').text(presupuesto ? '#' + presupuesto : '-');
        $('#info_cliente').text(cliente || 'Cliente Ocasional');
        $('#info_direccion').text(direccion || 'Sin dirección');
        $('#info_responsable').text(responsable || '-');
        $('#importe_percibido').val(monto || '0');
        $('#observaciones').val('');
        $('#filePreviewContainer').empty();
        $('#comprobantes').val('');

        $('#modalConfirmarEntrega').modal('show');
    });

    // Evento Abrir Modal Auditoría / Detalle
    $(document).on('click', '.btnVerDetalle', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '?c=entrega&a=VerDetalle',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    var e = res.entrega;
                    var comp = res.comprobantes;

                    $('#det_entrega_id').text(e.id);
                    $('#det_presupuesto_id').text(e.id_presupuesto ? '#' + e.id_presupuesto : 'N/A');
                    $('#det_venta_id').text(e.id_venta ? '#' + e.id_venta : 'No facturado');
                    $('#det_cliente_nombre').text(e.cliente_nombre || 'Cliente Ocasional');
                    $('#det_cliente_ruc').text(e.cliente_ruc || '-');
                    $('#det_cliente_direccion').text(e.cliente_direccion || 'Sin dirección');
                    $('#det_cliente_telefono').text(e.cliente_telefono || '-');

                    $('#det_usuario_asigno').text(e.usuario_asigno_nombre || '-');
                    $('#det_responsable').text(e.responsable_nombre || '-');
                    
                    if (e.estado === 'Entregado') {
                        $('#det_estado').attr('class', 'label label-success').text('Entregado');
                    } else {
                        $('#det_estado').attr('class', 'label label-warning').text('Pendiente');
                    }

                    $('#det_fecha_asignacion').text(e.fecha_asignacion || '-');
                    $('#det_usuario_entrega').text(e.usuario_entrega_nombre || 'Pendiente');
                    $('#det_fecha_hora_entrega').text(e.fecha_hora_entrega || 'Pendiente');

                    $('#det_importe_percibido').text('Gs. ' + (parseFloat(e.importe_percibido) || 0).toLocaleString('de-DE'));
                    $('#det_metodo_pago').text(e.metodo_pago || '-');
                    
                    var obsTexto = '';
                    if (e.observacion_presupuesto) {
                        obsTexto += '<strong>Observación del Presupuesto:</strong> ' + e.observacion_presupuesto + '<br>';
                    }
                    if (e.observaciones) {
                        obsTexto += '<strong>Notas de Entrega:</strong> ' + e.observaciones;
                    }
                    $('#det_observaciones').html(obsTexto ? obsTexto : 'Sin observaciones registradas.');

                    // Galería de comprobantes
                    var galeria = $('#galeriaComprobantes');
                    galeria.empty();
                    $('#det_total_comprobantes').text(comp.length);

                    if (comp.length > 0) {
                        $.each(comp, function(i, c) {
                            var item = $('<div>').css({'position': 'relative', 'margin-bottom': '10px'});
                            if (c.tipo_archivo && c.tipo_archivo.indexOf('image') !== -1) {
                                var img = $('<img>')
                                    .attr('src', c.archivo_path)
                                    .css({
                                        'width': '120px',
                                        'height': '120px',
                                        'object-fit': 'cover',
                                        'border-radius': '6px',
                                        'border': '2px solid #138496',
                                        'cursor': 'pointer'
                                    })
                                    .attr('onclick', "abrirZoomImagen('" + c.archivo_path + "', '" + (c.nombre_original || 'Comprobante') + "')");
                                item.append(img);
                            } else {
                                var link = $('<a>')
                                    .attr('href', c.archivo_path)
                                    .attr('target', '_blank')
                                    .addClass('btn btn-default btn-block')
                                    .css({'padding': '15px', 'text-align': 'center'})
                                    .html('<i class="fa fa-file-pdf-o fa-2x text-danger"></i><br><small>' + (c.nombre_original || 'Documento') + '</small>');
                                item.append(link);
                            }
                            galeria.append(item);
                        });
                    } else {
                        galeria.html('<div class="col-xs-12 text-muted" style="font-style: italic;">No se adjuntaron comprobantes para esta entrega.</div>');
                    }

                    $('#modalDetalleEntrega').modal('show');
                } else {
                    Swal.fire('Error', 'No se pudieron cargar los datos de la entrega', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            }
        });
    });

    // Auto scroll-to-top y habilitar arrastrar modal (Draggable)
    $('#modalConfirmarEntrega, #modalDetalleEntrega').on('show.bs.modal', function () {
        $('html, body').animate({ scrollTop: 0 }, 200);
        hacerModalArrastrable(this);
    });

    function hacerModalArrastrable(modalElem) {
        var $modal = $(modalElem);
        var $dialog = $modal.find('.modal-dialog');
        var $header = $modal.find('.modal-header');

        $header.css('cursor', 'move');
        $dialog.css({ 'left': '0px', 'top': '0px' });

        $header.off('mousedown.drag').on('mousedown.drag', function(e) {
            if ($(e.target).hasClass('close') || $(e.target).parent().hasClass('close')) return;
            var isDragging = true;
            var startX = e.clientX;
            var startY = e.clientY;
            var currentLeft = parseInt($dialog.css('left')) || 0;
            var currentTop = parseInt($dialog.css('top')) || 0;

            $(document).on('mousemove.drag', function(e) {
                if (!isDragging) return;
                var dx = e.clientX - startX;
                var dy = e.clientY - startY;
                $dialog.css({
                    position: 'relative',
                    left: (currentLeft + dx) + 'px',
                    top: (currentTop + dy) + 'px'
                });
            });

            $(document).on('mouseup.drag', function() {
                isDragging = false;
                $(document).off('mousemove.drag mouseup.drag');
            });
        });
    }
});
</script>

<!-- Modal Detalles y Auditoría de Entrega -->
<div id="modalDetalleEntrega" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #2b3e50 0%, #1e2b37 100%); color: white; border-top-left-radius: 8px; border-top-right-radius: 8px; cursor: move;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">&times;</button>
                <h4 class="modal-title" style="font-weight: 600;">
                    <i class="fa fa-shield"></i> Trazabilidad y Auditoría de Entrega #<span id="det_entrega_id"></span> <small style="color: #ccc; font-size: 11px;">(Arrastre para mover)</small>
                </h4>
            </div>
            <div class="modal-body" style="padding: 20px; max-height: calc(100vh - 200px); overflow-y: auto;">
                <div class="row">
                    <!-- Sección Datos del Pedido -->
                    <div class="col-md-6">
                        <div class="panel panel-default" style="border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="panel-heading" style="font-weight: bold; background-color: #f5f5f5;">
                                <i class="fa fa-shopping-cart text-primary"></i> Información Origen y Cliente
                            </div>
                            <div class="panel-body">
                                <p><strong>Presupuesto ID:</strong> <span id="det_presupuesto_id" class="text-primary font-weight-bold"></span></p>
                                <p><strong>Venta Facturada ID:</strong> <span id="det_venta_id" class="text-success font-weight-bold"></span></p>
                                <p><strong>Cliente:</strong> <span id="det_cliente_nombre"></span></p>
                                <p><strong>RUC / CI:</strong> <span id="det_cliente_ruc"></span></p>
                                <p><strong>Dirección:</strong> <span id="det_cliente_direccion"></span></p>
                                <p><strong>Teléfono:</strong> <span id="det_cliente_telefono"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Auditoría y Responsables -->
                    <div class="col-md-6">
                        <div class="panel panel-default" style="border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="panel-heading" style="font-weight: bold; background-color: #f5f5f5;">
                                <i class="fa fa-user-check text-info"></i> Trazabilidad y Responsables
                            </div>
                            <div class="panel-body">
                                <p><strong>Usuario que Asignó:</strong> <span id="det_usuario_asigno" class="label label-default"></span></p>
                                <p><strong>Responsable Asignado:</strong> <span id="det_responsable" class="label label-info"></span></p>
                                <p><strong>Estado Actual:</strong> <span id="det_estado" class="label"></span></p>
                                <p><strong>Fecha de Asignación:</strong> <span id="det_fecha_asignacion"></span></p>
                                <p><strong>Entregado Por:</strong> <span id="det_usuario_entrega" class="label label-success"></span></p>
                                <p><strong>Fecha y Hora Entrega:</strong> <span id="det_fecha_hora_entrega" class="text-success font-weight-bold"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalles Financieros y Pago -->
                <div class="panel panel-info" style="border-radius: 6px;">
                    <div class="panel-heading" style="font-weight: bold;">
                        <i class="fa fa-money"></i> Registro Financiero de Entrega
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4 style="margin:0;">Importe Cobrado: <strong id="det_importe_percibido" class="text-success"></strong></h4>
                            </div>
                            <div class="col-sm-6">
                                <h4 style="margin:0;">Método de Pago: <strong id="det_metodo_pago" class="text-primary"></strong></h4>
                            </div>
                        </div>
                        <hr style="margin: 10px 0;">
                        <p style="margin-bottom:0;"><strong>Observaciones Registradas:</strong></p>
                        <div id="det_observaciones" style="background: #f8f9fa; padding: 10px; border-radius: 4px; font-style: italic; color: #555; margin-top: 5px;"></div>
                    </div>
                </div>

                <!-- Galería de Comprobantes -->
                <div class="panel panel-default" style="border-radius: 6px;">
                    <div class="panel-heading" style="font-weight: bold; background-color: #f5f5f5;">
                        <i class="fa fa-camera"></i> Comprobantes Adjuntos y Recibos (<span id="det_total_comprobantes">0</span>)
                    </div>
                    <div class="panel-body">
                        <div id="galeriaComprobantes" class="row" style="display: flex; flex-wrap: wrap; gap: 15px; padding: 10px;">
                            <!-- Se carga mediante JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Zoom Imagen -->
<div id="modalZoomImagen" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg" style="margin-top: 50px;">
        <div class="modal-content" style="background-color: #222; text-align: center; border-radius: 8px;">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                <h4 id="zoomTitulo" class="modal-title" style="color: white;">Comprobante Adjunto</h4>
            </div>
            <div class="modal-body" style="padding: 10px;">
                <img id="imgZoom" src="" alt="Comprobante" style="max-width: 100%; max-height: 75vh; border-radius: 4px; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
            </div>
        </div>
    </div>
</div>

<script>
function abrirZoomImagen(src, titulo) {
    $('#imgZoom').attr('src', src);
    $('#zoomTitulo').text(titulo || 'Comprobante Adjunto');
    $('#modalZoomImagen').modal('show');
}
</script>

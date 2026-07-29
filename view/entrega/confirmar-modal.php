<!-- Modal Confirmar Entrega -->
<div id="modalConfirmarEntrega" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #337ab7 0%, #1d5b8c 100%); color: white; border-top-left-radius: 8px; border-top-right-radius: 8px; cursor: move;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">&times;</button>
                <h4 class="modal-title" style="font-weight: 600;">
                    <i class="fa fa-truck"></i> Registrar y Confirmar Entrega <small style="color: #e0e0e0; font-size: 11px;">(Arrastre desde el encabezado para mover)</small>
                </h4>
            </div>
            <form action="?c=entrega&a=ConfirmarEntrega" method="post" enctype="multipart/form-data" id="formConfirmarEntrega">
                <input type="hidden" name="id_entrega" id="modal_confirmar_id_entrega" value="">
                <div id="containerFotosBase64"></div>
                
                <div class="modal-body" style="padding: 20px; max-height: calc(100vh - 200px); overflow-y: auto;">
                    <div class="well" style="background-color: #f8f9fa; border-left: 4px solid #337ab7; margin-bottom: 20px; padding: 12px 15px;">
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Pedido/Presupuesto #:</strong> <span id="info_id_presupuesto" class="text-primary font-weight-bold"></span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Cliente:</strong> <span id="info_cliente"></span>
                            </div>
                            <div class="col-sm-6" style="margin-top: 5px;">
                                <strong>Dirección:</strong> <span id="info_direccion"></span>
                            </div>
                            <div class="col-sm-6" style="margin-top: 5px;">
                                <strong>Responsable Asignado:</strong> <span id="info_responsable" class="label label-info"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="importe_percibido" style="font-weight: 600; color: #333;">Importe Cobrado / Percibido (Gs.):</label>
                            <div class="input-group">
                                <span class="input-group-addon" style="background-color: #eee; font-weight: bold;">Gs.</span>
                                <input type="text" name="importe_percibido" id="importe_percibido" class="form-control input-lg" placeholder="0" required style="font-size: 18px; font-weight: bold; color: #28a745;">
                            </div>
                            <small class="text-muted">Monto total pagado o percibido durante la entrega.</small>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="metodo_pago" style="font-weight: 600; color: #333;">Método de Pago:</label>
                            <select name="metodo_pago" id="metodo_pago" class="form-control input-lg" required style="font-size: 15px;">
                                <?php 
                                $listaMetodos = isset($metodos_pago) ? $metodos_pago : (isset($this->metodo) ? $this->metodo->ListarTodos() : array());
                                if (!empty($listaMetodos)) :
                                    foreach ($listaMetodos as $m) : 
                                ?>
                                        <option value="<?php echo htmlspecialchars($m->metodo); ?>"><?php echo htmlspecialchars($m->metodo); ?></option>
                                <?php 
                                    endforeach;
                                else : 
                                ?>
                                    <option value="Efectivo" selected>Efectivo</option>
                                    <option value="Transferencia">Transferencia Bancaria</option>
                                    <option value="Tarjeta">Tarjeta de Crédito / Débito</option>
                                    <option value="QR">Pago QR</option>
                                    <option value="Giro">Giro / Cheque</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px dashed #cccccc;">
                        <label style="font-weight: 600; color: #333; margin-bottom: 10px;">
                            <i class="fa fa-camera text-primary"></i> Adjuntar Comprobantes y Fotos:
                        </label>
                        
                        <!-- Inputs ocultos: SIN MULTIPLE EN CÁMARA PARA EVITAR NAVEGADOR DE ARCHIVOS EN MÓVILES -->
                        <input type="file" name="foto_camara[]" id="foto_camara" accept="image/*" capture="environment" style="display: none;">
                        <input type="file" name="comprobantes[]" id="comprobantes" accept="image/*,application/pdf" style="display: none;" multiple>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6" style="margin-bottom: 10px;">
                                <button type="button" class="btn btn-primary btn-block btn-lg" id="btnTomarFoto" style="font-weight: 600; padding: 12px; box-shadow: 0 2px 6px rgba(51,122,183,0.3);">
                                    <i class="fa fa-camera fa-lg"></i> 📸 TOMAR FOTO CON CÁMARA
                                </button>
                            </div>
                            <div class="col-xs-12 col-sm-6" style="margin-bottom: 10px;">
                                <button type="button" class="btn btn-default btn-block btn-lg" id="btnAbrirGaleria" style="font-weight: 600; padding: 12px; border: 1px solid #bbb;">
                                    <i class="fa fa-folder-open fa-lg text-warning"></i> 📁 GALERÍA / DOCUMENTOS
                                </button>
                            </div>
                        </div>

                        <small class="text-muted" style="display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Pulse <strong>"TOMAR FOTO CON CÁMARA"</strong> para capturar fotos directamente sin buscar archivos.
                        </small>

                        <!-- Vista Previa de Imágenes Seleccionadas -->
                        <div id="filePreviewContainer" style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 10px;"></div>
                    </div>

                    <div class="form-group">
                        <label for="observaciones" style="font-weight: 600; color: #333;">Observaciones Adicionales / Notas de Entrega:</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="3" placeholder="Ingrese comentarios sobre el estado de la entrega, persona que recibió, notas de pago, etc."></textarea>
                    </div>
                </div>

                <div class="modal-footer" style="background-color: #f1f3f5; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight: 500;">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-lg" id="btnConfirmarEntrega" style="font-weight: 600; padding-left: 25px; padding-right: 25px;">
                        <i class="fa fa-check-circle"></i> Confirmar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CÁMARA EN VIVO WEBRTC (CAPTURA DIRECTA SIN ABRIR NAVEGADOR DE ARCHIVOS) -->
<div id="modalCamaraEnVivo" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-md" style="margin-top: 30px;">
        <div class="modal-content" style="background-color: #111; color: white; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.8);">
            <div class="modal-header" style="border-bottom: 1px solid #333; padding: 12px 15px;">
                <button type="button" class="close btnCerrarCamara" style="color: white; opacity: 0.9; font-size: 24px;">&times;</button>
                <h4 class="modal-title" style="font-weight: bold; color: #fff;">
                    <i class="fa fa-camera text-success"></i> Cámara en Vivo
                </h4>
            </div>
            <div class="modal-body" style="padding: 10px; text-align: center;">
                <video id="videoStream" autoplay playsinline style="width: 100%; max-height: 55vh; border-radius: 8px; background: #000; object-fit: cover;"></video>
                <canvas id="canvasCaptura" style="display: none;"></canvas>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #333; text-align: center; padding: 15px;">
                <button type="button" class="btn btn-default btnCerrarCamara" style="margin-right: 10px;">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success btn-lg" id="btnCapturarFotoStream" style="font-weight: bold; padding-left: 30px; padding-right: 30px;">
                    <i class="fa fa-camera"></i> CAPTURAR FOTO
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var activeStream = null;

    $('#btnTomarFoto').on('click', function() {
        // Intentar primero WebRTC Live Camera para captura en pantalla
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            iniciarCamaraEnVivo();
        } else {
            // Fallback directo a input sin multiple
            $('#foto_camara').click();
        }
    });

    function iniciarCamaraEnVivo() {
        var constraints = {
            video: {
                facingMode: { ideal: "environment" },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                activeStream = stream;
                var video = document.getElementById('videoStream');
                video.srcObject = stream;
                $('#modalCamaraEnVivo').modal('show');
            })
            .catch(function(err) {
                console.log("Error al abrir WebRTC camera: ", err);
                // Si falla o el navegador restringe la cámara WebRTC, disparar input capture directamente
                $('#foto_camara').click();
            });
    }

    function detenerCamaraEnVivo() {
        if (activeStream) {
            activeStream.getTracks().forEach(function(track) {
                track.stop();
            });
            activeStream = null;
        }
    }

    $('.btnCerrarCamara').on('click', function() {
        detenerCamaraEnVivo();
        $('#modalCamaraEnVivo').modal('hide');
    });

    $('#modalCamaraEnVivo').on('hidden.bs.modal', function() {
        detenerCamaraEnVivo();
    });

    $('#btnCapturarFotoStream').on('click', function() {
        var video = document.getElementById('videoStream');
        var canvas = document.getElementById('canvasCaptura');
        if (video && video.videoWidth > 0) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            var dataURL = canvas.toDataURL('image/jpeg', 0.85);

            // Crear input hidden con la foto en Base64
            var inputHidden = $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'fotos_capturadas_base64[]')
                .val(dataURL);
            $('#containerFotosBase64').append(inputHidden);

            // Mostrar miniatura en la vista previa
            var container = $('#filePreviewContainer');
            var imgBox = $('<div>').css({ 'position': 'relative', 'display': 'inline-block' });
            var img = $('<img>').attr('src', dataURL).css({
                'width': '85px',
                'height': '85px',
                'object-fit': 'cover',
                'border-radius': '8px',
                'border': '2px solid #28a745',
                'box-shadow': '0 2px 5px rgba(0,0,0,0.15)'
            });
            imgBox.append(img);
            container.append(imgBox);

            detenerCamaraEnVivo();
            $('#modalCamaraEnVivo').modal('hide');
        }
    });

    $('#btnAbrirGaleria').on('click', function() {
        $('#comprobantes').click();
    });

    function mostrarVistaPreviaArchivos(files) {
        var container = $('#filePreviewContainer');
        if (files && files.length > 0) {
            $.each(files, function(i, file) {
                if (file.type.match('image.*')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var imgBox = $('<div>').css({ 'position': 'relative', 'display': 'inline-block' });
                        var img = $('<img>').attr('src', e.target.result).css({
                            'width': '85px',
                            'height': '85px',
                            'object-fit': 'cover',
                            'border-radius': '8px',
                            'border': '2px solid #28a745',
                            'box-shadow': '0 2px 5px rgba(0,0,0,0.15)'
                        });
                        imgBox.append(img);
                        container.append(imgBox);
                    };
                    reader.readAsDataURL(file);
                } else {
                    var badge = $('<span>').addClass('label label-default').html('<i class="fa fa-file-pdf-o"></i> ' + file.name).css({'padding': '10px', 'display': 'inline-block', 'font-size': '12px'});
                    container.append(badge);
                }
            });
        }
    }

    $('#foto_camara, #comprobantes').on('change', function() {
        mostrarVistaPreviaArchivos(this.files);
    });

    // Formatear separador de miles en tiempo real para importe percibido
    $('#importe_percibido').on('keyup input', function() {
        var val = $(this).val().replace(/\D/g, '');
        if (val) {
            $(this).val(parseInt(val, 10).toLocaleString('de-DE'));
        } else {
            $(this).val('');
        }
    });
</script>

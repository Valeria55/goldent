            <?php
            $caja_origen = $this->model->ObtenerBalance($_REQUEST['id_caja']);

            // if (!($cierre = $this->cierre->Consultar($_SESSION['user_id'])) && $caja_origen->id == 1) {
            //     die("<h3>Por favor, realice una apertura de caja para poder efectuar esta transferencia.</h3>");
            // }
            
            // Calcular saldos por moneda
            $gs_disponible = ($caja_origen->ingresos_gs ?? 0) - ($caja_origen->egresos_gs ?? 0);
            $usd_disponible = ($caja_origen->ingresos_usd ?? 0) - ($caja_origen->egresos_usd ?? 0);
            $rs_disponible = ($caja_origen->ingresos_rs ?? 0) - ($caja_origen->egresos_rs ?? 0);
            
            // Verificar si hay al menos una moneda con saldo positivo
            $hay_fondos_disponibles = ($gs_disponible > 0) || ($usd_disponible > 0) || ($rs_disponible > 0);
            
            if (!$hay_fondos_disponibles) {
                die("<h3>No hay fondos disponibles en ninguna moneda.</h3>");
            }
            ?>

            <h3 class="page-header">
                Transferir de: <span class="" style="text-decoration: underline;"><?php echo $caja_origen->caja ?></span>
            </h3>
            
            <?php if ($caja_origen->disponible <= 0): ?>
            <div class="alert alert-warning" style="margin-bottom: 15px;">
                <strong>Nota:</strong> El saldo total es negativo, pero puede transferir de las monedas que tengan saldo positivo.
            </div>
            <?php endif; ?>
            
            <h4><b style="">Monto disponible total:</b> Gs. <span><?php echo number_format($caja_origen->disponible, 0, ",", ".") ?></span></h4>
            
            <!-- Desglose por moneda -->
            <div class="panel panel-info" style="margin-top: 15px;">
                <div class="panel-heading">
                    <h5 class="panel-title">Desglose por moneda disponible:</h5>
                </div>
                <div class="panel-body">
                    <?php 
                    $cotizaciones = $this->model->ObtenerCotizacionesActuales();
                    // Los saldos ya se calcularon arriba para la validación, no necesitamos recalcularlos
                    
                    // Verificar que las cotizaciones no sean nulas
                    $cot_dolar = $cotizaciones->cot_dolar ?? 7500;
                    $cot_real = $cotizaciones->cot_real ?? 1500;
                    
                    // Calcular el total usando las cotizaciones actuales para verificar
                    $total_calculado_manual = $gs_disponible + ($usd_disponible * $cot_dolar) + ($rs_disponible * $cot_real);
                    ?>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Guaraníes (GS):</strong><br>
                            <span class="badge <?php echo ($gs_disponible > 0) ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 12px;">
                                <?php echo number_format($gs_disponible, 0, ".", ","); ?>
                            </span>
                            <?php if ($gs_disponible > 0): ?>
                            <br><small class="text-success">✓ Disponible para transferir</small>
                            <?php else: ?>
                            <br><small class="text-danger">✗ Sin fondos</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Dólares (USD):</strong><br>
                            <span class="badge <?php echo ($usd_disponible > 0) ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 12px;">
                                <?php echo number_format($usd_disponible, 2, ".", ","); ?>
                            </span>
                            <small class="text-muted"><br>≈ Gs. <?php echo number_format($usd_disponible * $cot_dolar, 0, ".", ","); ?></small>
                            <?php if ($usd_disponible > 0): ?>
                            <br><small class="text-success">✓ Disponible para transferir</small>
                            <?php else: ?>
                            <br><small class="text-danger">✗ Sin fondos</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Reales (RS):</strong><br>
                            <span class="badge <?php echo ($rs_disponible > 0) ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 12px;">
                                <?php echo number_format($rs_disponible, 2, ".", ","); ?>
                            </span>
                            <small class="text-muted"><br>≈ Gs. <?php echo number_format($rs_disponible * $cot_real, 0, ".", ","); ?></small>
                            <?php if ($rs_disponible > 0): ?>
                            <br><small class="text-success">✓ Disponible para transferir</small>
                            <?php else: ?>
                            <br><small class="text-danger">✗ Sin fondos</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <?php 
                    $diferencia = abs($caja_origen->disponible - $total_calculado_manual);
                    if ($diferencia > 1): // Si hay diferencia significativa (más de 1 guaraní)
                    ?>
                    <div class="alert alert-info" style="margin-bottom: 10px; padding: 8px;">
                        <small><strong>Nota:</strong> El monto total usa cotizaciones históricas de cada transacción. 
                        Con cotizaciones actuales sería: Gs. <?php echo number_format($total_calculado_manual, 0, ".", ","); ?> 
                        (diferencia: Gs. <?php echo number_format($diferencia, 0, ".", ","); ?>)</small>
                    </div>
                    <?php endif; ?>
                    <small class="text-muted">
                        <strong>Cotizaciones actuales:</strong> 
                        USD: <?php echo number_format($cot_dolar, 0, ".", ","); ?> | 
                        RS: <?php echo number_format($cot_real, 0, ".", ","); ?>
                    </small>
                </div>
            </div>
            <br>
            <form method="post" action="?c=caja&a=transferencia" enctype="multipart/form-data">
                <input type="hidden" name="c" value="caja" id="c" />

                <div class="form-group" style="display: none;">
                    <label>Origen</label>
                    <input type="text" class="form-control" readonly value="<?php echo $caja_origen->caja ?>">
                </div>
                <div class="form-group" style="display: none;">
                    <label>Monto disponible</label>
                    <input type="text" class="form-control" readonly value="Gs. <?php echo number_format($caja_origen->disponible, 0, ",", ".") ?>">
                </div>
                <input type="hidden" value="<?php echo $caja_origen->id ?>" id="id_emisor" name="id_emisor">

                <div class="form-group">
                    <label>Destino</label>
                    <select name="id_receptor" id="id_receptor" class="form-control">
                        <?php
                        foreach ($this->caja->Listar() as $r) :
                            if ($caja_origen->id == 1 && $r->id != 3) continue;
                            if ($r->id != $caja_origen->id) :
                        ?>
                                <option value="<?php echo $r->id ?>" id="<?php echo $r->id ?>"><?php echo $r->caja ?></option>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </select>
                </div>

                <div class="form-group" id="cajero_div" style="display: none;">
                    <label>Cajero <small>(usuarios con apertura de caja activa)</small></label>
                    <select name="id_cajero" id="id_cajero" disabled class="form-control" required>
                        <option value="">Seleccione</option>
                        <?php
                        foreach ($this->usuario->ListarUsuarios() as $r) :
                            $caja_abierta = ($this->cierre->Consultar($r->id));
                            if (!$caja_abierta) continue; // si el usuario no tiene la caja abierta, pasar al siguiente
                        ?>
                            <option value="<?php echo $r->id ?>" id="<?php echo $r->id ?>"><?php echo $r->user; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <?php if($caja_origen->id == 3):?>
                <div class="form-group" id="comprobante_div">
                    <label>Comprobante de transferencia</label>
                    <input type="text" name="comprobante" id="comprobante" class="form-control" placeholder="Ingrese su comprobante" required>
                </div>
                
                <?php endif;?>
                
                <div class="form-group">
                    <label>Moneda de transferencia</label>
                    <select name="moneda_transferencia" id="moneda_transferencia" class="form-control" required>
                        <option value="">Seleccione la moneda</option>
                        <?php if($gs_disponible > 0): ?>
                        <option value="GS">Guaraníes (GS) - Disponible: <?php echo number_format($gs_disponible, 0, ".", ","); ?></option>
                        <?php endif; ?>
                        <?php if($usd_disponible > 0): ?>
                        <option value="USD">Dólares (USD) - Disponible: <?php echo number_format($usd_disponible, 2, ".", ","); ?></option>
                        <?php endif; ?>
                        <?php if($rs_disponible > 0): ?>
                        <option value="RS">Reales (RS) - Disponible: <?php echo number_format($rs_disponible, 2, ".", ","); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Monto <span id="moneda_label">(Seleccione moneda primero)</span></label>
                    <input type="number" name="monto" id="monto" step="any" value="0" min="0" class="form-control" placeholder="Ingrese el monto" required disabled>
                    <small class="help-block" id="equivalencia_texto" style="display: none;"></small>
                </div>
                
                <!-- Cotizaciones para transferencia -->
                <div class="panel panel-warning" style="margin-top: 15px;">
                    <div class="panel-heading">
                        <h5 class="panel-title">Cotizaciones para esta transferencia</h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cotización Dólar (USD)</label>
                                    <input type="number" name="cot_dolar" id="cot_dolar" step="any" value="<?php echo $cot_dolar; ?>" min="0" class="form-control" required>
                                    <small class="text-muted">Cotización actual: <?php echo number_format($cot_dolar, 0, ".", ","); ?></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cotización Real (RS)</label>
                                    <input type="number" name="cot_real" id="cot_real" step="any" value="<?php echo $cot_real; ?>" min="0" class="form-control" required>
                                    <small class="text-muted">Cotización actual: <?php echo number_format($cot_real, 0, ".", ","); ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info" style="margin-bottom: 0; padding: 8px;">
                            <small><strong>Nota:</strong> Estas cotizaciones se aplicarán tanto al egreso como al ingreso de esta transferencia, independientemente de las cotizaciones del cierre de cada usuario.</small>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="this.disabled=true;this.value='Guardando, Espere...';this.form.submit();">Transferir</button>
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                </div>
            </form>

            <script>
                // Variables para cotizaciones y disponibles
                var cotDolar = <?php echo $cot_dolar; ?>;
                var cotReal = <?php echo $cot_real; ?>;
                var gsDisponible = <?php echo $gs_disponible; ?>;
                var usdDisponible = <?php echo $usd_disponible; ?>;
                var rsDisponible = <?php echo $rs_disponible; ?>;
                
                $("#id_receptor").change(function(e) {
                    // e.preventDefault();
                    if (parseInt($(this).val()) == 1) { // si es caja chica, mostrar cajero 
                        $("#id_cajero").attr("disabled", false);
                        $("#cajero_div").show();
                    } else {
                        $("#id_cajero").attr("disabled", true);
                        $("#cajero_div").hide();
                    }

                    if (parseInt($(this).val()) == 2) { // si es banco, mostrar comprobante 
                        $("#comprobante").attr("disabled", false);
                        $("#comprobante_div").show();
                    } else {
                        $("#comprobante").attr("disabled", true);
                        $("#comprobante_div").hide();
                    }
                });
                
                $("#moneda_transferencia").change(function() {
                    var moneda = $(this).val();
                    var montoInput = $("#monto");
                    var monedaLabel = $("#moneda_label");
                    var equivalenciaTexto = $("#equivalencia_texto");
                    
                    if (moneda) {
                        montoInput.prop('disabled', false);
                        
                        if (moneda === 'GS') {
                            montoInput.attr('max', gsDisponible);
                            monedaLabel.text('(en Guaraníes)');
                            equivalenciaTexto.hide();
                        } else if (moneda === 'USD') {
                            montoInput.attr('max', usdDisponible);
                            monedaLabel.text('(en Dólares)');
                            equivalenciaTexto.show();
                            actualizarEquivalencia();
                        } else if (moneda === 'RS') {
                            montoInput.attr('max', rsDisponible);
                            monedaLabel.text('(en Reales)');
                            equivalenciaTexto.show();
                            actualizarEquivalencia();
                        }
                        
                        montoInput.val(0);
                    } else {
                        montoInput.prop('disabled', true);
                        monedaLabel.text('(Seleccione moneda primero)');
                        equivalenciaTexto.hide();
                    }
                });
                
                $("#monto").on('input', function() {
                    actualizarEquivalencia();
                });
                
                // Eventos para actualizar equivalencias cuando cambian las cotizaciones
                $("#cot_dolar, #cot_real").on('input', function() {
                    actualizarEquivalencia();
                });
                
                function actualizarEquivalencia() {
                    var moneda = $("#moneda_transferencia").val();
                    var monto = parseFloat($("#monto").val()) || 0;
                    var equivalenciaTexto = $("#equivalencia_texto");
                    
                    // Usar las cotizaciones de los campos de entrada
                    var cotDolarActual = parseFloat($("#cot_dolar").val()) || cotDolar;
                    var cotRealActual = parseFloat($("#cot_real").val()) || cotReal;
                    
                    if (moneda === 'USD' && monto > 0) {
                        var equivalenciaGs = monto * cotDolarActual;
                        equivalenciaTexto.html('<strong>Equivalencia:</strong> Gs. ' + equivalenciaGs.toLocaleString('es-PY'));
                        equivalenciaTexto.show();
                    } else if (moneda === 'RS' && monto > 0) {
                        var equivalenciaGs = monto * cotRealActual;
                        equivalenciaTexto.html('<strong>Equivalencia:</strong> Gs. ' + equivalenciaGs.toLocaleString('es-PY'));
                        equivalenciaTexto.show();
                    } else {
                        equivalenciaTexto.hide();
                    }
                }
                
                $("#id_emisor").change(function() {
                    var caja = $(this).val();
                    $("#" + caja).hide();
                });
            </script>
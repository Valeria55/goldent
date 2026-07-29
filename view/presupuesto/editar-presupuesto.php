<?php 
$fecha = date("Y-m-d"); 
$cierre = $this->cierre->Ultimo();
$id_presupuesto = isset($_GET['id']) ? intval($_GET['id']) : (isset($id_presupuesto) ? $id_presupuesto : 0);
$items = $this->model->ListarDetalle($id_presupuesto);

if (empty($items)) {
    echo "<div class='alert alert-danger'>Presupuesto no encontrado o no contiene ítems.</div>";
    return;
}

$cabecera = $items[0];
$cliente_actual_id = $cabecera->id_cliente;
$responsable_actual_id = $cabecera->responsable_entrega_id;
$observacion_actual = $cabecera->observacion_presupuesto;
$descuento_actual = $cabecera->descuento;
$id_adelanto_actual = $cabecera->id_adelanto;
?>

<h1 class="page-header">
    <i class="fa fa-edit text-warning"></i> Modificar Presupuesto #<?php echo $id_presupuesto; ?>
    <a class="btn btn-default pull-right" href="?c=presupuesto"><i class="fa fa-arrow-left"></i> Volver a la Lista</a>
</h1>

<div class="well" style="background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;">
    <i class="fa fa-info-circle"></i> Editando presupuesto N° <strong><?php echo $id_presupuesto; ?></strong>. Los cambios guardados actualizarán este presupuesto manteniendo su número de orden.
</div>

<div class="panel panel-default">
    <div class="panel-heading" style="font-weight: bold; background-color: #337ab7; color: white;">
        <i class="fa fa-plus-circle"></i> Agregar Producto al Presupuesto #<?php echo $id_presupuesto; ?>
    </div>
    <div class="panel-body">
        <form method="post" action="?c=presupuesto&a=AgregarItemEdicion">
            <input type="hidden" name="id_presupuesto" value="<?php echo $id_presupuesto; ?>">
            <div class="row">
                <div class="col-sm-3">
                    <label>Producto / Servicio</label>
                    <select name="id_producto" id="producto_edit" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" title="-- Seleccione el producto --" required>
                        <?php foreach ($this->producto->ListarServicios() as $producto): $promo = ($producto->precio_promo > 0) ? " promo = " . number_format($producto->precio_promo, 0, ".", ".") : ""; ?>
                            <option data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>"><?php echo htmlspecialchars($producto->producto) . ' - ' . number_format($producto->precio_minorista, 0, ".", ".") . $promo; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label>Paciente</label>
                    <input type="text" name="paciente" class="form-control" placeholder="Nombre paciente (opcional)">
                </div>
                <div class="col-sm-2">
                    <label>Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" value="1" step="any" min="1" required>
                </div>
                <div class="col-sm-2">
                    <label>Precio Unitario</label>
                    <input type="number" name="precio_venta" id="precio_venta_edit" class="form-control" placeholder="Precio" step="any" min="0" required>
                </div>
                <div class="col-sm-2">
                    <label>Descuento (%)</label>
                    <input type="number" name="descuento" class="form-control" value="0" min="0" max="100">
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="col-sm-12 text-right">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir Ítem al Presupuesto</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading" style="font-weight: bold; background-color: #222; color: white;">
        <i class="fa fa-list"></i> Ítems del Presupuesto #<?php echo $id_presupuesto; ?>
    </div>
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" style="margin-bottom: 0;">
                <thead>
                    <tr style="background-color: #f5f5f5;">
                        <th>Código</th>
                        <th>Producto / Servicio</th>
                        <th>Paciente</th>
                        <th>Precio Unitario (Gs.)</th>
                        <th>Cantidad</th>
                        <th>Descuento (%)</th>
                        <th>Total Ítem (Gs.)</th>
                        <th style="width: 80px; text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subtotal_edit = 0;
                    foreach ($items as $r):
                        $totalItem = (($r->precio_venta * $r->cantidad) - (($r->precio_venta * $r->cantidad) * ($r->descuento / 100)));
                        $subtotal_edit += $totalItem; 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r->codigo); ?></td>
                            <td><strong><?php echo htmlspecialchars($r->producto); ?></strong></td>
                            <td><?php echo htmlspecialchars($r->paciente); ?></td>
                            <td><?php echo number_format($r->precio_venta, 0, ",", "."); ?></td>
                            <td><?php echo $r->cantidad; ?></td>
                            <td><?php echo $r->descuento; ?>%</td>
                            <td><strong><?php echo number_format($totalItem, 0, ",", "."); ?></strong></td>
                            <td text-align="center">
                                <a href="?c=presupuesto&a=EliminarItemEdicion&id=<?php echo $r->id; ?>&id_presupuesto=<?php echo $id_presupuesto; ?>" class="btn btn-danger btn-xs" onclick="return confirm('¿Seguro de eliminar este producto del presupuesto?');">
                                    <i class="fa fa-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr style="background-color: #f9f9f9; font-size: 16px;">
                        <td colspan="6" text-align="right"><strong>TOTAL PRESUPUESTO:</strong></td>
                        <td colspan="2"><strong style="color: #28a745; font-size: 20px;">Gs. <?php echo number_format($subtotal_edit, 0, ",", "."); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PANEL DATOS DE CABECERA Y ENTREGA -->
<div class="panel panel-info">
    <div class="panel-heading" style="font-weight: bold;">
        <i class="fa fa-cogs"></i> Datos de Cliente, Entrega y Adelantos (Presupuesto #<?php echo $id_presupuesto; ?>)
    </div>
    <div class="panel-body">
        <form method="post" action="?c=presupuesto&a=ActualizarPresupuesto">
            <input type="hidden" name="id_presupuesto" value="<?php echo $id_presupuesto; ?>">

            <div class="row">
                <div class="form-group col-sm-6">
                    <label style="font-weight: 600;">Cliente:</label>
                    <select name="id_cliente" id="cliente_edit" class="form-control selectpicker" data-live-search="true" required>
                        <option value="0" <?php echo ($cliente_actual_id == 0) ? 'selected' : ''; ?>>Cliente ocasional</option>
                        <?php foreach ($this->cliente->Listar() as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo ($cliente_actual_id == $c->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c->nombre . ' - ' . $c->ruc); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-sm-6" id="div-adelantos-edit">
                    <label style="font-weight: 600;">Adelantos Disponibles (Selección Múltiple):</label>
                    <select name="id_adelanto[]" id="id_adelanto_edit" class="form-control selectpicker" multiple data-actions-box="true" data-live-search="true" title="-- Seleccione los adelantos a descontar --">
                        <?php
                        if ($cliente_actual_id > 0) {
                            $adelantos_disponibles = $this->adelanto->ListarPendientesPorCliente($cliente_actual_id);
                            $selected_ids = array_map('trim', explode(',', $id_adelanto_actual));
                            
                            // Incluir también los adelantos previamente guardados en este presupuesto
                            foreach ($selected_ids as $s_id) {
                                if (!empty($s_id)) {
                                    $ade_previo = $this->adelanto->Obtener($s_id);
                                    if ($ade_previo) {
                                        $ya_esta = false;
                                        foreach ($adelantos_disponibles as $ad_exist) {
                                            if ($ad_exist->id == $ade_previo->id) { $ya_esta = true; break; }
                                        }
                                        if (!$ya_esta) {
                                            $adelantos_disponibles[] = $ade_previo;
                                        }
                                    }
                                }
                            }

                            foreach ($adelantos_disponibles as $a) {
                                $is_selected = in_array($a->id, $selected_ids) ? 'selected' : '';
                                $desc = $a->descripcion ? ' (' . htmlspecialchars($a->descripcion) . ')' : '';
                                echo '<option value="' . $a->id . '" ' . $is_selected . '>Adelanto #' . $a->id . ' - Gs. ' . number_format($a->monto, 0, '.', '.') . $desc . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-6">
                    <label style="font-weight: 600;"><i class="fa fa-truck text-primary"></i> Responsable del Área de Entregas:</label>
                    <select name="responsable_entrega_id" id="responsable_entrega_id_edit" class="form-control selectpicker" data-live-search="true">
                        <option value="0" <?php echo ($responsable_actual_id == 0) ? 'selected' : ''; ?>>-- Sin asignar / No requiere entrega --</option>
                        <?php 
                        $entregadoresList = method_exists($this->usuario, 'ListarEntregadores') ? $this->usuario->ListarEntregadores() : array();
                        if (empty($entregadoresList)) {
                            foreach($this->usuario->ListarUsuarios() as $u) {
                                if ($u->nivel == 11) $entregadoresList[] = $u;
                            }
                        }
                        foreach($entregadoresList as $u): 
                        ?>
                            <option value="<?php echo $u->id; ?>" <?php echo ($responsable_actual_id == $u->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u->user); ?> (<?php echo htmlspecialchars($u->sucursal ? $u->sucursal : 'General'); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-sm-6">
                    <label style="font-weight: 600;">Descuento Global (%):</label>
                    <input type="number" name="descuento_global" class="form-control" value="<?php echo floatval($descuento_actual); ?>" min="0" max="100" step="0.01">
                    <small class="text-muted">* Si es mayor a 0, actualizará el descuento de todos los ítems.</small>
                </div>
            </div>

            <div class="form-group">
                <label style="font-weight: 600;"><i class="fa fa-comment text-info"></i> Observaciones del Presupuesto (Orden de Entrega):</label>
                <textarea name="observacion_presupuesto" class="form-control" rows="3" placeholder="Indicaciones especiales para la entrega..."><?php echo htmlspecialchars($observacion_actual); ?></textarea>
            </div>

            <hr>

            <div class="text-right">
                <a href="?c=presupuesto" class="btn btn-default btn-lg">Cancelar</a>
                <button type="submit" class="btn btn-success btn-lg" style="font-weight: bold; padding-left: 30px; padding-right: 30px;">
                    <i class="fa fa-save"></i> GUARDAR CAMBIOS EN PRESUPUESTO #<?php echo $id_presupuesto; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $('#cliente_edit').on('change', function() {
        var id_cliente = $(this).val();
        if (id_cliente > 0) {
            $.post('?c=adelanto&a=ListarPendientes', {id_cliente: id_cliente}, function(data) {
                var adelantos = JSON.parse(data);
                var $select = $('#id_adelanto_edit');
                $select.empty();
                if (adelantos.length > 0) {
                    adelantos.forEach(function(a) {
                        var desc = a.descripcion ? ' (' + a.descripcion + ')' : '';
                        $select.append('<option value="' + a.id + '">Adelanto #' + a.id + ' - Gs. ' + parseFloat(a.monto).toLocaleString('es-PY') + desc + '</option>');
                    });
                }
                $select.selectpicker('refresh');
            });
        } else {
            $('#id_adelanto_edit').empty().selectpicker('refresh');
        }
    });

    $('#producto_edit').on('change', function() {
        var id_producto = $(this).val();
        if (id_producto > 0) {
            $.post('?c=producto&a=Buscar', {id: id_producto}, function(data) {
                var p = typeof data === 'string' ? JSON.parse(data) : data;
                if (p) {
                    var precio = p.precio_minorista ? p.precio_minorista : (p.precio_venta ? p.precio_venta : 0);
                    $('#precio_venta_edit').val(precio);
                }
            });
        }
    });
</script>

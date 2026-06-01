<?php
// Pre-fill values
$desde = isset($_REQUEST['desde']) ? $_REQUEST['desde'] : date('Y-m-01');
$hasta = isset($_REQUEST['hasta']) ? $_REQUEST['hasta'] : date('Y-m-d');
$id_producto_sel = isset($_REQUEST['id_producto']) ? $_REQUEST['id_producto'] : '';
$comprobante_sel = isset($_REQUEST['comprobante']) ? $_REQUEST['comprobante'] : 'Todos';
$agrupado_sel = isset($_REQUEST['agrupado']) ? (int)$_REQUEST['agrupado'] : 0;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header" style="font-weight: 300; color: #2c3e50; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-file-invoice" style="color: #348993;"></i> Informe de Ítems Facturados
            </h1>
        </div>
    </div>

    <!-- Panel de Filtros Premium -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: none; margin-bottom: 25px;">
                <div class="panel-body" style="padding: 25px;">
                    <form method="get" action="index.php" id="form-filtros">
                        <input type="hidden" name="c" value="informe">
                        <input type="hidden" name="a" value="itemsFacturados">
                        
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label style="font-weight: 600; color: #34495e; margin-bottom: 8px;">
                                    <i class="fas fa-calendar-alt" style="color: #348993;"></i> Fecha Desde
                                </label>
                                <input type="date" name="desde" class="form-control" value="<?php echo $desde; ?>" required style="border-radius: 8px; height: 40px;">
                            </div>
                            
                            <div class="form-group col-md-3">
                                <label style="font-weight: 600; color: #34495e; margin-bottom: 8px;">
                                    <i class="fas fa-calendar-alt" style="color: #348993;"></i> Fecha Hasta
                                </label>
                                <input type="date" name="hasta" class="form-control" value="<?php echo $hasta; ?>" required style="border-radius: 8px; height: 40px;">
                            </div>

                            <div class="form-group col-md-3">
                                <label style="font-weight: 600; color: #34495e; margin-bottom: 8px;">
                                    <i class="fas fa-box" style="color: #348993;"></i> Producto / Servicio
                                </label>
                                <select name="id_producto" class="form-control selectpicker" data-live-search="true" title="-- Todos los Productos --" style="border-radius: 8px;">
                                    <option value="">-- Todos los Productos --</option>
                                    <?php foreach ($productos as $p): ?>
                                        <option value="<?php echo $p->id; ?>" <?php echo ((string)$id_producto_sel === (string)$p->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($p->producto . ' (' . ($p->codigo ?? '') . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label style="font-weight: 600; color: #34495e; margin-bottom: 8px;">
                                    <i class="fas fa-receipt" style="color: #348993;"></i> Comprobante
                                </label>
                                <select name="comprobante" class="form-control" style="border-radius: 8px; height: 40px;">
                                    <option value="Todos" <?php echo $comprobante_sel === 'Todos' ? 'selected' : ''; ?>>Todos</option>
                                    <option value="Factura" <?php echo $comprobante_sel === 'Factura' ? 'selected' : ''; ?>>Facturas</option>
                                    <option value="Ticket" <?php echo $comprobante_sel === 'Ticket' ? 'selected' : ''; ?>>Tickets</option>
                                    <option value="TicketSi" <?php echo $comprobante_sel === 'TicketSi' ? 'selected' : ''; ?>>Sin Comprobante / Sin impresión</option>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-md-4">
                                <label style="font-weight: 600; color: #34495e; margin-bottom: 8px; display: block;">
                                    <i class="fas fa-stream" style="color: #348993;"></i> Visualización / Agrupación
                                </label>
                                <div class="btn-group" data-toggle="buttons" style="width: 100%; display: flex;">
                                    <label class="btn btn-default <?php echo $agrupado_sel === 0 ? 'active' : ''; ?>" style="flex: 1; border-radius: 8px 0 0 8px; padding: 10px; font-weight: 500;">
                                        <input type="radio" name="agrupado" value="0" autocomplete="off" <?php echo $agrupado_sel === 0 ? 'checked' : ''; ?>> 
                                        <i class="fas fa-list-ul"></i> Detallado (Desagrupado)
                                    </label>
                                    <label class="btn btn-default <?php echo $agrupado_sel === 1 ? 'active' : ''; ?>" style="flex: 1; border-radius: 0 8px 8px 0; padding: 10px; font-weight: 500;">
                                        <input type="radio" name="agrupado" value="1" autocomplete="off" <?php echo $agrupado_sel === 1 ? 'checked' : ''; ?>> 
                                        <i class="fas fa-cubes"></i> Agrupado por Producto
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-8 text-right" style="margin-top: 25px;">
                                <button type="submit" class="btn btn-primary" style="background-color: #348993; border: none; padding: 10px 25px; border-radius: 20px; font-weight: bold; transition: all 0.3s; height: 42px;">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                
                                <button type="button" onclick="exportar('excel')" class="btn btn-success" style="background-color: #27ae60; border: none; padding: 10px 20px; border-radius: 20px; font-weight: bold; margin-left: 10px; height: 42px;">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                
                                <button type="button" onclick="exportar('pdf')" class="btn btn-danger" style="background-color: #e74c3c; border: none; padding: 10px 20px; border-radius: 20px; font-weight: bold; margin-left: 10px; height: 42px;">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado / Tabla de Datos -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: none;">
                <div class="panel-heading" style="background-color: #f8f9fa; border-radius: 12px 12px 0 0; border-bottom: 1px solid #eee; padding: 15px 20px;">
                    <h3 class="panel-title" style="font-weight: bold; color: #2c3e50; margin: 0; display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="fas fa-table" style="color: #348993;"></i> Resultados del Informe</span>
                        <span class="badge" style="background-color: #348993; font-size: 14px; padding: 6px 12px; border-radius: 12px;">
                            <?php echo count($resultados); ?> registros
                        </span>
                    </h3>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-hover table-align-middle" style="margin-bottom: 0;">
                            <?php if ($agrupado_sel === 1): ?>
                                <!-- Vista Agrupada -->
                                <thead>
                                    <tr style="background-color: #f1f4f6; color: #34495e;">
                                        <!-- Removed Código -->
                                        <th style="padding: 15px; font-weight: 600;">Item (Producto / Servicio)</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: center;">Cantidad Total</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: right;">Precio Venta Promedio</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: right;">Total Acumulado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalCant = 0;
                                    $totalMonto = 0;
                                    foreach ($resultados as $r): 
                                        $totalCant += $r->cantidad;
                                        $totalMonto += $r->total;
                                    ?>
                                        <tr style="transition: all 0.2s;">
                                            <!-- Removed Código -->
                                            <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($r->producto); ?></td>
                                            <td style="padding: 12px 15px; text-align: center; font-weight: bold; color: #348993;"><?php echo number_format($r->cantidad, 0, ",", "."); ?></td>
                                            <td style="padding: 12px 15px; text-align: right;"><?php echo number_format($r->precio_venta, 0, ",", "."); ?> Gs.</td>
                                            <td style="padding: 12px 15px; text-align: right; font-weight: bold; color: #27ae60;"><?php echo number_format($r->total, 0, ",", "."); ?> Gs.</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($resultados) === 0): ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 30px; color: #7f8c8d;">
                                                <i class="fas fa-folder-open" style="font-size: 2em; display: block; margin-bottom: 10px;"></i>
                                                Sin registros para el filtro seleccionado.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (count($resultados) > 0): ?>
                                    <tfoot>
                                        <tr style="background-color: #eaeded; font-weight: bold; font-size: 15px;">
                                            <td colspan="1" style="padding: 15px; text-align: right; color: #2c3e50;">TOTALES GENERALES:</td>
                                            <td style="padding: 15px; text-align: center; color: #348993;"><?php echo number_format($totalCant, 0, ",", "."); ?></td>
                                            <td></td>
                                            <td style="padding: 15px; text-align: right; color: #27ae60;"><?php echo number_format($totalMonto, 0, ",", "."); ?> Gs.</td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>

                            <?php else: ?>
                                <!-- Vista Desagrupada (Detallada) -->
                                <thead>
                                    <tr style="background-color: #f1f4f6; color: #34495e;">
                                        <th style="padding: 15px; font-weight: 600; text-align: center;">Nº Venta</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: center;">Nº Orden (Presupuesto)</th>
                                        <th style="padding: 15px; font-weight: 600;">Cliente</th>
                                        <th style="padding: 15px; font-weight: 600;">Paciente</th>
                                        <th style="padding: 15px; font-weight: 600;">Item (Producto / Servicio)</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: center;">Fecha</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: center;">Comprobante</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: center;">Cant.</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: right;">Precio Unit.</th>
                                        <th style="padding: 15px; font-weight: 600; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalCant = 0;
                                    $totalMonto = 0;
                                    foreach ($resultados as $r): 
                                        $totalCant += $r->cantidad;
                                        $totalMonto += $r->total;
                                        
                                        // Formatear comprobante
                                        $compLabel = 'Sin Comprobante';
                                        $compClass = 'label-default';
                                        if ($r->comprobante === 'Factura') {
                                            $compLabel = 'Factura';
                                            $compClass = 'label-success';
                                        } elseif ($r->comprobante === 'Ticket') {
                                            $compLabel = 'Ticket';
                                            $compClass = 'label-info';
                                        }
                                    ?>
                                        <tr style="transition: all 0.2s;">
                                            <td style="padding: 12px 15px; text-align: center; font-weight: bold; color: #34495e;"><?php echo htmlspecialchars($r->id_venta); ?></td>
                                            <td style="padding: 12px 15px; text-align: center; color: #7f8c8d;"><?php echo htmlspecialchars($r->id_presupuesto ?? '-'); ?></td>
                                            <td style="padding: 12px 15px; font-weight: bold; color: #7f8c8d;"><?php echo htmlspecialchars($r->cliente ?? '-'); ?></td>
                                            <td style="padding: 12px 15px; font-weight: bold; color: #7f8c8d;"><?php echo htmlspecialchars($r->paciente ?? '-'); ?></td>
                                            <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($r->producto); ?></td>
                                            <td style="padding: 12px 15px; text-align: center;"><?php echo date('d/m/Y H:i', strtotime($r->fecha_venta)); ?></td>
                                            <td style="padding: 12px 15px; text-align: center;">
                                                <span class="label <?php echo $compClass; ?>" style="font-size: 11px; padding: 4px 8px; border-radius: 4px;">
                                                    <?php echo $compLabel; ?>
                                                </span>
                                            </td>
                                            <td style="padding: 12px 15px; text-align: center; font-weight: bold; color: #348993;"><?php echo number_format($r->cantidad, 0, ",", "."); ?></td>
                                            <td style="padding: 12px 15px; text-align: right;"><?php echo number_format($r->precio_venta, 0, ",", "."); ?> Gs.</td>
                                            <td style="padding: 12px 15px; text-align: right; font-weight: bold; color: #27ae60;"><?php echo number_format($r->total, 0, ",", "."); ?> Gs.</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($resultados) === 0): ?>
                                        <tr>
                                            <td colspan="9" style="text-align: center; padding: 30px; color: #7f8c8d;">
                                                <i class="fas fa-folder-open" style="font-size: 2em; display: block; margin-bottom: 10px;"></i>
                                                Sin registros para el filtro seleccionado.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (count($resultados) > 0): ?>
                                    <tfoot>
                                        <tr style="background-color: #eaeded; font-weight: bold; font-size: 15px;">
                                            <td colspan="7" style="padding: 15px; text-align: right; color: #2c3e50;">TOTALES GENERALES:</td>
                                            <td style="padding: 15px; text-align: center; color: #348993;"><?php echo number_format($totalCant, 0, ",", "."); ?></td>
                                            <td></td>
                                            <td style="padding: 15px; text-align: right; color: #27ae60;"><?php echo number_format($totalMonto, 0, ",", "."); ?> Gs.</td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f1f4f6;
        box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
    }
    .table-responsive tfoot td {
        position: sticky;
        bottom: 0;
        z-index: 10;
        background-color: #eaeded;
        box-shadow: 0 -2px 2px -1px rgba(0,0,0,0.1);
    }
    .btn-group .btn.active {
        background-color: #348993 !important;
        color: white !important;
        border-color: #348993 !important;
        box-shadow: inset 0 3px 5px rgba(0,0,0,0.125);
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    .table-align-middle td, .table-align-middle th {
        vertical-align: middle !important;
    }
</style>

<script>
    $(document).ready(function() {
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }
    });

    function exportar(tipo) {
        var form = $('#form-filtros');
        var originalAction = form.attr('action');
        var originalA = form.find('input[name="a"]').val();
        
        if (tipo === 'excel') {
            form.find('input[name="a"]').val('itemsFacturadosExcel');
            form.attr('target', '_blank');
        } else if (tipo === 'pdf') {
            form.find('input[name="a"]').val('itemsFacturadosPdf');
            form.attr('target', '_blank');
        }
        
        form.submit();
        
        // Restaurar estado original del formulario
        form.attr('action', originalAction);
        form.find('input[name="a"]').val(originalA);
        form.removeAttr('target');
    }
</script>

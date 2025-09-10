<h3 class="page-header">Movimientos</h3>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div align="center" id="filtro">
                <form method="post" class="form-inline" style="justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="margin-right: 5px;">Desde:</label>
                        <input type="date" name="desde" value="<?php echo (isset($_POST['desde'])) ? $_POST['desde'] : ''; ?>" class="form-control" style="width: auto;">
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="margin-right: 5px;">Hasta:</label>
                        <input type="date" name="hasta" value="<?php echo (isset($_POST['hasta'])) ? $_POST['hasta'] : ''; ?>" class="form-control" style="width: auto;">
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="margin-right: 5px;">Tipo:</label>
                        <select name="tipo_transaccion" class="form-control" style="width: auto;">
                            <option value="">Todos</option>
                            <option value="enviados" <?php echo (isset($_POST['tipo_transaccion']) && $_POST['tipo_transaccion'] == 'enviados') ? 'selected' : ''; ?>>Enviados</option>
                            <option value="recibidos" <?php echo (isset($_POST['tipo_transaccion']) && $_POST['tipo_transaccion'] == 'recibidos') ? 'selected' : ''; ?>>Recibidos</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="margin-right: 5px;">Caja:</label>
                        <select name="id_caja" class="form-control" style="width: auto;">
                            <option value="">Todas las cajas</option>
                            <?php
                            // Obtener lista de cajas
                            $cajas = $this->model->Listar(); // Asumiendo que existe un modelo para cajas
                            foreach ($cajas as $caja): ?>
                                <option value="<?php echo $caja->id; ?>" <?php echo (isset($_POST['id_caja']) && $_POST['id_caja'] == $caja->id) ? 'selected' : ''; ?>>
                                    <?php echo $caja->caja; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="margin-right: 5px;">Moneda:</label>
                        <select name="moneda" class="form-control" style="width: auto;">
                            <option value="">Todas las monedas</option>
                            <option value="GS" <?php echo (isset($_POST['moneda']) && $_POST['moneda'] == 'GS') ? 'selected' : ''; ?>>Guaraníes (GS)</option>
                            <option value="USD" <?php echo (isset($_POST['moneda']) && $_POST['moneda'] == 'USD') ? 'selected' : ''; ?>>Dólares (USD)</option>
                            <option value="RS" <?php echo (isset($_POST['moneda']) && $_POST['moneda'] == 'RS') ? 'selected' : ''; ?>>Reales (RS)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <input type="submit" name="filtro" value="Filtrar" class="btn btn-success">
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<p> Lista de movimientos </p>



<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Concepto</th>
            <th>Comprobante</th>
            <th>Monto</th>
            <th>Moneda</th>
            <th>Cambio</th>
            <th>Caja</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Aplicar filtros
        $desde = isset($_POST['desde']) ? $_POST['desde'] : '';
        $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : '';
        $id_caja = isset($_POST['id_caja']) ? $_POST['id_caja'] : '';
        $moneda = isset($_POST['moneda']) ? $_POST['moneda'] : '';
        $tipo_transaccion = isset($_POST['tipo_transaccion']) ? $_POST['tipo_transaccion'] : '';

        // Usar la nueva función del modelo
        $lista = $this->model->ListarMovimientosCronologicos($desde, $hasta, $id_caja, $moneda);

        foreach ($lista as $r): 
            // Filtrar por tipo de transacción si se especifica
            if (!empty($tipo_transaccion)) {
                if ($tipo_transaccion == 'enviados' && $r->tipo_transaccion != 'egreso') continue;
                if ($tipo_transaccion == 'recibidos' && $r->tipo_transaccion != 'ingreso') continue;
            }
            
            // Saltear registros anulados
            if ($r->anulado) continue;
            
            // Determinar el color solo para tipo y monto
            $tipoLabel = $r->tipo_transaccion == 'ingreso' ? 'Recibido' : 'Enviado';
            $montoColor = $r->tipo_transaccion == 'ingreso' ? 'green' : 'red';
            ?>
            <tr>
                <td><?php echo date('d/m/Y H:i', strtotime($r->fecha)); ?></td>
                <td><span class="badge badge-<?php echo $r->tipo_transaccion == 'ingreso' ? 'success' : 'danger'; ?>"><?php echo $tipoLabel; ?></span></td>
                <td><?php echo $r->concepto; ?></td>
                <td><?php echo $r->comprobante; ?></td>
                <td style="color: <?php echo $montoColor; ?>; font-weight: bold;">
                    <?php echo number_format(abs($r->monto), 0, ',', '.'); ?>
                    <?php if (!empty($r->moneda) && $r->moneda != 'GS' && !empty($r->monto_moneda)): ?>
                        <br><small>(<?php echo number_format(abs($r->monto_moneda), 0, ',', '.'); ?> <?php echo 'GS'; ?>)</small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    if (!empty($r->moneda) && $r->moneda != 'GS') {
                        echo $r->moneda;
                    } else {
                        echo 'GS';
                    }
                    ?>
                </td>
                <td><?php echo $r->cambio; ?></td>
                <td><?php echo isset($r->caja) ? $r->caja : 'N/A'; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
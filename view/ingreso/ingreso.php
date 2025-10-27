<h3 class="page-header">Lista de ingresos <a class="btn btn-primary pull-right" href="#ingresoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="ingreso">Agregar</a></h3>

<?php
// Mostrar mensajes de éxito o error
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'pago_eliminado') {
        echo '<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Éxito:</strong> Pago eliminado correctamente. El saldo ha sido restaurado.
              </div>';
    }
}
if (isset($_GET['error'])) {
    $mensaje_error = 'Error desconocido';
    switch($_GET['error']) {
        case 'ingreso_no_encontrado':
            $mensaje_error = 'No se encontró el ingreso especificado';
            break;
        case 'deuda_no_encontrada':
            $mensaje_error = 'No se encontró la deuda asociada';
            break;
        case 'error_eliminar_pago':
            $mensaje_error = 'Error al eliminar el pago';
            break;
    }
    echo '<div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error:</strong> ' . $mensaje_error . '
          </div>';
}
?>

<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up" style="display: none"></i></h3>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <div align="center" id="filtro" style="display: none;">
            <form method="post">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde']))? $_GET['desde']:''; ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta']))? $_GET['hasta']:''; ?>" class="form-control" required>
                </div>
                <input type="submit" name="filtro" value="Filtrar" class="btn btn-success"> 
            </form>
        </div>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div>
<p> </p>
<div class="table-responsive">
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Fecha</th>
            <th>Categoría</th>
            <th>Cliente</th>
            <th>Usuario</th>
            <th>Concepto</th>
            <th>Comprobante</th>
            <th>Monto</th>
            <th>Moneda</th>
            <th>Forma de pago</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $fecha=date('Y-m-d');
    $lista = (isset($_POST['desde']))? $this->model->Listar_rango($_POST['desde'],$_POST['hasta']):$this->model->Listar($fecha);
    
    foreach($lista as $r): 
    if(strlen($r->concepto)>=50){$concepto=substr($r->concepto, 0, 50)."...";}else{$concepto=$r->concepto;} ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";}?>>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->categoria; ?></td>
            <td><?php echo isset($r->cliente_nombre) ? $r->cliente_nombre : '-'; ?></td>
            <td><?php echo isset($r->usuario_nombre) ? $r->usuario_nombre : '-'; ?></td>
            <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td>
                <?php 
                // Mostrar el monto con formato según la moneda
                $moneda = isset($r->moneda) ? $r->moneda : 'GS';
                if ($moneda == 'USD' || $moneda == 'RS') {
                    echo number_format($r->monto, 2, ".", ",");
                } else {
                    echo number_format($r->monto, 0, ".", ",");
                }
                ?>
            </td>
            <td><?php echo isset($r->moneda) ? $r->moneda : 'GS'; ?></td>
            <td><?php echo $r->forma_pago; ?></td>
            <td>
                 <?php if ($r->id_gift == null || ($r->anulado==1)): ?>
                <?php if (!$r->anulado): ?>
                    <?php if ($r->id_venta): ?>
                    <a href="#detallesModal" class="btn btn-warning" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta; ?>">Venta</a>
                        <?php if ($r->id_deuda): ?>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id_deuda; ?>">Cobros</a>
                        <a class="btn btn-danger delete-pago-deuda" 
                           href="?c=ingreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"
                           data-monto="<?php echo $r->monto; ?>" 
                           data-moneda="<?php echo isset($r->moneda) ? $r->moneda : 'GS'; ?>"
                           data-concepto="<?php echo htmlspecialchars($r->concepto); ?>">
                           <i class="fas fa-trash-alt"></i>
                        </a>
                        <?php endif ?>
                    <?php elseif($r->id_deuda): ?>
                    <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id_deuda; ?>">Cobros</a>
                    <a class="btn btn-danger delete-pago-deuda" 
                       href="?c=ingreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"
                       data-monto="<?php echo $r->monto; ?>" 
                       data-moneda="<?php echo isset($r->moneda) ? $r->moneda : 'GS'; ?>"
                       data-concepto="<?php echo htmlspecialchars($r->concepto); ?>">
                       <i class="fas fa-trash-alt"></i>
                    </a>
                    <?php else: ?>
                    <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="ingreso">Editar</a>
                    <a  class="btn btn-danger delete" href="?c=ingreso&a=Eliminar&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>">Eliminar</a>
                    <?php endif ?>
                <?php else: ?>
                    ANULADO
                <?php endif ?>
                <?php endif ?>
            </td> 
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>
<?php include("view/deuda/cobros-modal.php"); ?>

<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });

    // Confirmación especial para eliminación de pagos de deuda
    $('.delete-pago-deuda').click(function(e) {
        e.preventDefault();
        
        var monto = $(this).data('monto');
        var moneda = $(this).data('moneda');
        var concepto = $(this).data('concepto');
        var href = $(this).attr('href');
        
        var mensaje = 'ATENCIÓN: Está a punto de eliminar un pago de deuda.\n\n';
        mensaje += 'Concepto: ' + concepto + '\n';
        mensaje += 'Monto: ' + monto + ' ' + moneda + '\n\n';
        mensaje += 'Al eliminar este pago, el saldo se restaurará automáticamente\n';
        mensaje += 'distribuyéndose desde las deudas más recientes a las más antiguas.\n\n';
        mensaje += '¿Está seguro de continuar?';
        
        if (confirm(mensaje)) {
            window.location.href = href;
        }
    });
</script>
</script>
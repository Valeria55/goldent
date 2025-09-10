<h1 class="page-header">Lista de ingresos</h1>
<a class="btn btn-primary pull-right" href="#ingresoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="ingreso">Agregar</a>
<br><br><br>

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
            <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td>
                <?php 
                // Mostrar el monto con formato según la moneda
                $moneda = isset($r->moneda) ? $r->moneda : 'GS';
                if ($moneda == 'USD' || $moneda == 'RS') {
                    echo number_format($r->monto, 3, ".", ",");
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
                        <a  class="btn btn-danger delete" href="?c=ingreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"><i class="fas fa-trash-alt"></i></a>
                        <?php endif ?>
                    <?php elseif($r->id_deuda): ?>
                    <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id_deuda; ?>">Cobros</a>
                    <a  class="btn btn-danger delete" href="?c=ingreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"><i class="fas fa-trash-alt"></i></a>
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
</script>
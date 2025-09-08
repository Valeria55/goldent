<h1 class="page-header">Lista de egresos </h1>
<a class="btn btn-primary pull-right" href="#egresoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="egreso">Agregar</a>
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
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Cliente</th>
        	<th>Fecha</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>N° de comprobante</th>
            <th>Monto</th> 
            <th>Forma de pago</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $lista = (isset($_POST['desde']))? $this->model->Listar_rango($_POST['desde'],$_POST['hasta']):$this->model->Listar();
    
    foreach($lista as $r): ?>
        <tr class="click">
            <td><?php echo $r->nombre; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->categoria; ?></td>
            <td><?php echo $r->concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo number_format($r->monto,0,".",","); ?></td>
            <td><?php echo $r->forma_pago; ?></td>
            <td>
                <?php if ($r->id_compra): ?>
                <a href="#detallesCompraModal" class="btn btn-warning" data-toggle="modal" data-target="#detallesCompraModal" data-id="<?php echo $r->id_compra;?>">Compra</a>
                    <?php if ($r->id_acreedor): ?>
                    <a href="#pagosModal" class="btn btn-success" data-toggle="modal" data-target="#pagosModal" data-id="<?php echo $r->id_acreedor; ?>">Pagos</a>
                    <a  class="btn btn-danger delete" href="?c=egreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_compra=<?php echo $r->id_compra; ?>"><i class="fas fa-trash-alt"></i></a>
                    <?php endif ?>
                <?php elseif($r->id_acreedor): ?>
                <a href="#pagosModal" class="btn btn-success" data-toggle="modal" data-target="#pagosModal" data-id="<?php echo $r->id_acreedor; ?>">Pagos</a>
                <a  class="btn btn-danger delete" href="?c=egreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_compra=<?php echo $r->id_compra; ?>"><i class="fas fa-trash-alt"></i></a>
                <?php else: ?>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="egreso">Editar</a>
                <a  class="btn btn-danger delete" href="?c=egreso&a=Eliminar&id=<?php echo $r->id; ?>&id_compra=<?php echo $r->id_compra; ?>">Eliminar</a>
                <?php endif ?>
            </td>    
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/compra/detalles-modal.php"); ?>
<?php include("view/acreedor/pagos-modal.php"); ?>

<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
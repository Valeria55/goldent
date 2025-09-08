<h1 class="page-header">Lista de cierres de caja </h1>
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
            <th>Usuario</th>
        	<th>Apertura</th>
            <th>Cierre</th>
            <th>Monto de apertura</th>
            <th>Monto de cierre</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $lista = (isset($_POST['desde']))? $this->model->Listar_rango($_POST['desde'],$_POST['hasta']):$this->model->Listar();
    
    foreach($lista as $r): ?>
        <tr class="click">
            <td><?php echo $r->user; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha_apertura)); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_cierre)); ?></td>
            <td><?php echo number_format($r->monto_apertura,0,".",","); ?></td>
            <td><?php echo number_format($r->monto_cierre,0,".",","); ?></td>
            <td>
                <a href="?c=cierre&a=CierrePDF&id_cierre=<?php echo $r->id; ?>" class="btn btn-warning">Ver detalles</a>
            </td>    
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>

<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
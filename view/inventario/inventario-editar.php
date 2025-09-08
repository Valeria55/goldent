<h1 class="page-header">
    <?php echo $inventario->id != null ? $inventario->inventario : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=inventario">Inventario</a></li>
  <li class="active"><?php echo $inventario->id != null ? $inventario->inventario : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=inventario&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="inventario" id="c"/>
    <input type="hidden" name="id" value="<?php echo $inventario->id; ?>" id="id" />
    
    

    <div class="form-group">
        <label>Inventario</label>
        <input type="text" name="inventario" value="<?php echo $inventario->inventario; ?>" class="form-control" placeholder="Ingrese su inventario" required>
    </div>
    

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

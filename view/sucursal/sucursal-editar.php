<h1 class="page-header">
    <?php echo $sucursal->id != null ? $sucursal->sucursal : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=sucursal">sucursal</a></li>
  <li class="active"><?php echo $sucursal->id != null ? $sucursal->sucursal : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=sucursal&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="sucursal" id="c"/>
    <input type="hidden" name="id" value="<?php echo $sucursal->id; ?>" id="id" />
    
    

    <div class="form-group">
        <label>sucursal</label>
        <input type="text" name="sucursal" value="<?php echo $sucursal->sucursal; ?>" class="form-control" placeholder="Ingrese su sucursal" required>
    </div>
    

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

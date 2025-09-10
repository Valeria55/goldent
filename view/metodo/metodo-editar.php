<h1 class="page-header">
    <?php echo $metodo->id != null ? $metodo->metodo : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=metodo">metodo</a></li>
  <li class="active"><?php echo $metodo->id != null ? $metodo->metodo : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=metodo&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="metodo" id="c"/>
    <input type="hidden" name="id" value="<?php echo $metodo->id; ?>" id="id" />

    <div class="form-group">
        <label>Método</label>
        <input type="text" name="metodo" class="form-control" value="<?php echo $metodo->metodo; ?>"  required="required">
    </div>


    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
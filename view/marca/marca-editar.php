<h1 class="page-header">
    <?php echo $marca->id != null ? $marca->marca : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=marca">marca</a></li>
  <li class="active"><?php echo $marca->id != null ? $marca->marca : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=marca&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="marca" id="c"/>
    <input type="hidden" name="id" value="<?php echo $marca->id; ?>" id="id" />
    
    

    <div class="form-group">
        <label>marca</label>
        <input type="text" name="marca" value="<?php echo $marca->marca; ?>" class="form-control" placeholder="Ingrese su marca" required>
    </div>
    

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<h1 class="page-header">
    <?php echo $categoria->id != null ? $categoria->categoria : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=categoria">categoria</a></li>
  <li class="active"><?php echo $categoria->id != null ? $categoria->categoria : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=categoria&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="categoria" id="c"/>
    <input type="hidden" name="id" value="<?php echo $categoria->id; ?>" id="id" />
    
    

    <div class="form-group">
        <label>Categoria</label>
        <input type="text" name="categoria" value="<?php echo $categoria->categoria; ?>" class="form-control" placeholder="Ingrese su categoria" required>
    </div>

    
    <div class="form-group">
        <label>Sub Categoría de:</label>
        <select name="id_padre" class="form-control">
            <option value="0">Principal</option>
            <?php foreach($this->model->Listar() as $r){ if($categoria->id != $r->id && $r->id_padre == 0){ ?>
            <option value="<?php echo $r->id; ?>" <?php echo ($categoria->id_padre == $r->id)? "selected":""; ?>><?php echo $r->categoria; ?></option>    
            <?php }}; ?>
        </select>
    </div>
    

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

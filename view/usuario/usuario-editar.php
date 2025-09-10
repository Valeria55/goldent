<h1 class="page-header">
    <?php echo $usuario->id != null ? $usuario->user : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=usuario">usuario</a></li>
  <li class="active"><?php echo $usuario->id != null ? $usuario->user : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=usuario&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="usuario" id="c"/>
    <input type="hidden" name="id" value="<?php echo $usuario->id; ?>" id="id" />
    
    <div class="form-group">
        <label>Usuario</label>
        <input type="text" name="user" value="<?php echo $usuario->user; ?>" class="form-control" placeholder="Ingrese su usuario" required>
    </div>

    <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="pass" value="<?php echo $usuario->pass; ?>" class="form-control" placeholder=" Ingrese su Contraseña" required>
    </div>
        
    <div class="form-group">
        <label>Nivel</label>
        <select name="nivel" class="form-control">
            <option value="1" <?php echo ($usuario->nivel==1)? "selected":""; ?>>Administrador</option>
            <option value="2" <?php echo ($usuario->nivel==2)? "selected":""; ?>>Cajero</option>
            <option value="3" <?php echo ($usuario->nivel==3)? "selected":""; ?>>Vendedor</option>
            <option value="4" <?php echo ($usuario->nivel==3)? "selected":""; ?>>Especial</option>
        </select> 
    </div>

    <div class="form-group">
        <label>Sucursal</label>
        <select name="sucursal" class="form-control">
            <?php foreach($this->sucursal->Listar() as $r): ?>
                <option value="<?php echo $r->id; ?>"><?php echo $r->sucursal; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label>Comisión (%)</label>
        <input type="number" name="comision" step="any" value="<?php echo $usuario->comision; ?>" class="form-control" placeholder="Ingrese su comisión" required>
    </div>

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

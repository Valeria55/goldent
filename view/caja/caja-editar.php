<h1 class="page-header">
    <?php echo $caja->id != null ? $caja->user : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=caja">caja</a></li>
  <li class="active"><?php echo $caja->id != null ? $caja->user : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=caja&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="caja" id="c"/>
    <input type="hidden" name="id" value="<?php echo $caja->id; ?>" id="id" />

    <div class="form-group">
        <label>Entidad</label>
        <input type="text" name="caja" value="<?php echo $caja->caja; ?>" class="form-control" placeholder="Ingrese entidad" list="entidades" required>
        <datalist id="entidades">
            <?php foreach($this->model->ListarAgrupado() as $r): ?>
            <option value="<?php echo $r->caja ?>">
            <?php endforeach; ?>
        </datalist>
    </div>

    <div class="form-group">
        <label>Movimiento</label>
        <select name="movimiento" class="form-control">
            <option value="1">Depósito</option>
            <option value="-1">Extracción</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" step="any" value="<?php echo $caja->monto; ?>" min="0" class="form-control" placeholder="Ingrese su monto" required>
    </div>

    <div class="form-group">
        <label>Comprobante</label>
        <input type="text" name="comprobante" value="<?php echo $caja->comprobante; ?>" class="form-control" placeholder="Ingrese comprobante" required>
    </div>

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

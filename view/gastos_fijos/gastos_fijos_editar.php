
<h1 class="page-header">
    <?php echo 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=gastos_fijos">Gastos</a></li>
  <li class="active"><?php echo  'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=gastos_fijos&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="gastos_fijos" id="c"/>
    <input type="hidden" name="id" value="<?php echo $gastos_fijos->id; ?>" id="id" />
    
    <div class="form-group">
        <label>Fecha inicial de pago</label>
        <input class="form-control" type="date" name="fecha" value="<?php echo empty($gastos_fijos->fecha) ? date('Y-m-d') : $gastos_fijos->fecha; ?>">
    </div>

    <div class="form-group">
        <label>Descripción</label>
        <input type="text" name="descripcion" value="<?php echo $gastos_fijos->descripcion; ?>" class="form-control" placeholder="Ingrese su descripcion" required>
    </div>
    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" value="<?php echo $gastos_fijos->monto; ?>" class="form-control" placeholder="Ingrese su descripcion" required>
    </div>
   

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<h1 class="page-header">
    <?php echo $cuenta->id != null ? $cuenta->nro_comprobante : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=cuenta">Cuenta</a></li>
  <li class="active"><?php echo $cuenta->id != null ? $cuenta->nro_comprobante : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=cuenta&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="cuenta" id="c"/>
    <input type="hidden" name="id" value="<?php echo $cuenta->id; ?>" id="id" />
    
    <div class="form-group">
        <label>Código de cliente</label>
        <input type="number" name="id_cliente" value="<?php echo $cuenta->id_cliente; ?>" class="form-control" placeholder="Código del cliente" required>
    </div>

    <div class="form-group">
        <label>Fecha emitida</label>
        <input type="date" name="fecha_emitida" value="<?php echo $cuenta->fecha_emitida; ?>" class="form-control" placeholder="Ingrese la fecha emitida" required>
    </div>

    <div class="form-group">
        <label>Fecha pagada</label>
        <input type="date" name="fecha_pagada" value="<?php echo $cuenta->fecha_pagada; ?>" class="form-control" placeholder="Ingrese la fecha pagada" required>
    </div>

    <div class="form-group">
        <label>Comprobante</label>
        <input type="text" name="comprobante" value="<?php echo $cuenta->comprobante; ?>" class="form-control" placeholder="Ingrese el comprobante" required>
    </div>

    <div class="form-group">
        <label>Número de comprobante</label>
        <input type="text" name="nro_comprobante" value="<?php echo $cuenta->nro_comprobante; ?>" class="form-control" placeholder="Ingrese el número de comprobante" required>
    </div>

    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" value="<?php echo $cuenta->monto; ?>" class="form-control" placeholder="Ingrese el monto" required>
    </div>

    <div class="form-group">
        <label>Saldo</label>
        <input type="number" name="saldo" value="<?php echo $cuenta->saldo; ?>" class="form-control" placeholder="Ingrese el saldo" required>
    </div>

    <div class="form-group">
        <label>Estado</label>
        <input type="text" name="estado" value="<?php echo $cuenta->estado; ?>" class="form-control" placeholder="Ingrese el estado" required>
    </div>
    

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

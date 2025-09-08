
<h1 class="page-header">
    <?php echo $ingreso->id != null ? $ingreso->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=ingreso">ingreso</a></li>
  <li class="active"><?php echo $ingreso->id != null ? $ingreso->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=ingreso&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="ingreso" id="c"/>
    <input type="hidden" name="id" value="<?php echo $ingreso->id; ?>" id="id" />
    <input type="hidden" name="fecha" value="<?php echo ($ingreso->fecha) ? date("Y-m-d", strtotime($ingreso->fecha)):date("Y-m-d H:i"); ?>">
    <input type="hidden" name="id_cliente" value="0">
    
    <div class="form-group">
        <label>Categoria</label>
        <input type="text" name="categoria" value="<?php echo $ingreso->categoria; ?>" class="form-control" placeholder="Ingrese la categoria" required>
    </div>

    <div class="form-group">
        <label>Concepto</label>
        <input type="text" name="concepto" value="<?php echo $ingreso->concepto; ?>" class="form-control" placeholder="Ingrese su concepto" required>
    </div>
    
    <div class="form-group">
        <label>Comprobante</label>
        <input type="text" name="comprobante" value="<?php echo $ingreso->comprobante; ?>" class="form-control" placeholder="Ingrese su comprobante" required>
    </div>
    
    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" value="<?php echo $ingreso->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="0" required>
    </div>

    <div class="form-group">
        <label>Forma de pago</label>
        <select name="forma_pago" class="form-control">
            <option value="Efectivo" <?php echo ($ingreso->forma_pago == "Efectivo")? "selected":""; ?>>Efectivo</option>
            <option value="Cheque" <?php echo ($ingreso->forma_pago == "Cheque")? "selected":""; ?>>Cheque</option>
            <option value="Transferencia" <?php echo ($ingreso->forma_pago == "Transferencia")? "selected":""; ?>>Transferencia</option>
        </select>
    </div>
    
    <input type="hidden" name="sucursal" value="0">
    
    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

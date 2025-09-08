
<h1 class="page-header">
    <?php echo $egreso->id != null ? $egreso->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=egreso">Extracción</a></li>
  <li class="active"><?php echo $egreso->id != null ? $egreso->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=egreso&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="egreso" id="c"/>
    <input type="hidden" name="id" value="<?php echo $egreso->id; ?>" id="id" />
    <input type="hidden" name="fecha" value="<?php echo (!$egreso->fecha)? (date("Y-m-d")."T".date("H:i")) : date("Y-m-d", strtotime($egreso->fecha))."T".date("H:i", strtotime($egreso->fecha)); ?>">
    <input type="hidden" name="id_cliente" value="0">
    
    <div class="form-group">
        <label>Categoria</label>
        <input type="text" name="categoria" value="<?php echo $egreso->categoria; ?>" class="form-control" placeholder="Ingrese la categoria" required>
    </div>

    <div class="form-group">
        <label>Concepto</label>
        <input type="text" name="concepto" value="<?php echo $egreso->concepto; ?>" class="form-control" placeholder="Ingrese su concepto" required>
    </div>
    
    <div class="form-group">
        <label>Comprobante</label>
        <input type="text" name="comprobante" value="<?php echo $egreso->comprobante; ?>" class="form-control" placeholder="Ingrese su comprobante" required>
    </div>
    
    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" value="<?php echo $egreso->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="0" required>
    </div>

    <div class="form-group">
        <label>Forma de pago</label>
        <select name="forma_pago" class="form-control">
            <option value="Efectivo" <?php echo ($egreso->forma_pago == "Efectivo")? "selected":""; ?>>Efectivo</option>
            <option value="Cheque" <?php echo ($egreso->forma_pago == "Cheque")? "selected":""; ?>>Cheque</option>
            <option value="Transferencia" <?php echo ($egreso->forma_pago == "Transferencia")? "selected":""; ?>>Transferencia</option>
        </select>
    </div>
    
    <input type="hidden" name="sucursal" value="0">
    
    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

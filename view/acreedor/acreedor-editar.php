<h1 class="page-header">
    <?php echo $acreedor->id != null ? $acreedor->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=acreedor">acreedor</a></li>
  <li class="active"><?php echo $acreedor->id != null ? $acreedor->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=acreedor&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="acreedor" id="c"/>
    <input type="hidden" name="id" value="<?php echo $acreedor->id; ?>" id="id" />
    <input type="hidden" name="id_compra" value="0">
    <div class="form-group">
        <label>Fecha</label>
        <input type="date" name="fecha" value="<?php echo ($acreedor->fecha) ? date("Y-m-d", strtotime($acreedor->fecha)):date("Y-m-d"); ?>" class="form-control" placeholder="Fecha" required>
    </div>

    <div class="form-group">
        <label>Cliente</label>
        <select name="id_cliente" id="id_cliente" class="form-control" data-show-subtext="true" data-live-search="true">
            <option value="2">Cliente casual (XXX)</option>
            <?php foreach($this->cliente->Listar() as $clie): ?> 
            <option value="<?php echo $clie->id; ?>" <?php echo ($clie->id == $acreedor->id_cliente)? "selected":""; ?>><?php echo $clie->nombre." ( ".$clie->ruc." )"; ?> </option>
            <?php endforeach; ?>
        </select>
    </div> 
    
    <div class="form-group">
        <label>Concepto</label>
        <input type="text" name="concepto" value="<?php echo $acreedor->concepto; ?>" class="form-control" placeholder="Ingrese su concepto" required>
    </div>
    
    <div class="form-group">
        <label>Monto</label>
        <input type="number" id="monto" name="monto" value="<?php echo $acreedor->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="0" required>
    </div>
        
    <div class="form-group">
        <label>Saldo</label>
        <input type="number" id="saldo" name="saldo" value="<?php echo $acreedor->saldo; ?>" class="form-control" placeholder="Ingrese el saldo" min="0" required>
    </div>

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script>
    $( "#monto" ).keyup(function() {
        $( "#saldo" ).val($( "#monto" ).val());
    });  
</script>
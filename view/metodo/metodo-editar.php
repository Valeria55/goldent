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
        <label>Fecha Inicial</label>
         <input type="datetime-local" name="fecha_inicio" value="<?php echo (!$metodo->fecha_inicio)? (date("Y-m-d")."T".date("H:i")) : date("Y-m-d", strtotime($metodo->fecha_inicio))."T".date("H:i", strtotime($metodo->fecha_inicio)); ?>" class="form-control" placeholder="Fecha" <?php if (($metodo->id != null))  echo "readonly"; ?> required >
    </div>
    <div class="form-group">
        <label>Método</label>
        <input type="text" name="metodo" class="form-control" value="<?php echo $metodo->metodo; ?>"  required="required" <?php if (($metodo->id != null))  echo "readonly"; ?> >
    </div>
    <div class="form-group">
        <label>Saldo Inicial</label>
        <input type="float" name="saldo_inicial" class="form-control" value="<?php echo $metodo->saldo_inicial; ?>" <?php if (($metodo->id != null))  echo "readonly"; ?> required="required" >
    </div>
    <div class="form-group">
        <label>Porcentaje</label>
        <input type="float" name="porcentaje" class="form-control" value="<?php echo $metodo->porcentaje; ?>" <?php if (($metodo->id != null))  echo "readonly"; ?> required="required">
    </div>

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
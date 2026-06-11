<h1 class="page-header">
    <?php echo $timbrado->id != null ? 'Editar Timbrado: ' . htmlspecialchars($timbrado->timbrado) : 'Nuevo Timbrado'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=timbrado">Timbrados</a></li>
  <li class="active"><?php echo $timbrado->id != null ? 'Editar' : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=timbrado&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="timbrado" id="c"/>
    <input type="hidden" name="id" value="<?php echo $timbrado->id; ?>" id="id" />
    <input type="hidden" name="estado" value="<?php echo $timbrado->id != null ? $timbrado->estado : 0; ?>" />
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Número de Timbrado <span class="text-danger">*</span></label>
                <input type="number" name="timbrado" value="<?php echo $timbrado->timbrado; ?>" class="form-control" placeholder="Ingrese el número de timbrado" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Establecimiento <span class="text-danger">*</span></label>
                <input type="number" name="establecimiento" value="<?php echo $timbrado->establecimiento; ?>" class="form-control" placeholder="Ej: 1" min="1" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Punto de Expedición <span class="text-danger">*</span></label>
                <input type="number" name="punto_expedicion" value="<?php echo $timbrado->punto_expedicion; ?>" class="form-control" placeholder="Ej: 1" min="1" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Fecha de Inicio <span class="text-danger">*</span></label>
                <input type="date" name="fecha_inicio" value="<?php echo $timbrado->fecha_inicio; ?>" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Fecha de Fin <span class="text-danger">*</span></label>
                <input type="date" name="fecha_fin" value="<?php echo $timbrado->fecha_fin; ?>" class="form-control" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Número de Factura Desde <span class="text-danger">*</span></label>
                <input type="number" name="numero_inicio" value="<?php echo $timbrado->numero_inicio; ?>" class="form-control" placeholder="Ej: 1" min="1" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Número de Factura Hasta <span class="text-danger">*</span></label>
                <input type="number" name="numero_fin" value="<?php echo $timbrado->numero_fin; ?>" class="form-control" placeholder="Ej: 1000" min="1" required>
            </div>
        </div>
    </div>
    
    <hr />
    
    <div class="text-right">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
    </div>
</form>

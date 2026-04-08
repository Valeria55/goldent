<?php
    $adelanto = new adelanto();
    if(isset($_REQUEST['id'])){
        $adelanto = $this->model->Obtener($_REQUEST['id']);
    }
?>
<h1 class="page-header">
    <?php echo $adelanto->id != null ? 'Editar Adelanto' : 'Nuevo Adelanto'; ?>
</h1>

<ol class="breadcrumb">
    <li><a href="?c=adelanto">Adelantos</a></li>
    <li class="active"><?php echo $adelanto->id != null ? 'Editar' : 'Nuevo'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=adelanto&a=Guardar" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $adelanto->id; ?>" />

    <div class="form-group">
        <label>Cliente</label>
        <select name="id_cliente" class="form-control selectpicker" data-live-search="true" required style="width: 100%;">
            <option value="">Seleccione un cliente</option>
            <?php foreach($this->cliente->Listar() as $c): ?>
                <option value="<?php echo $c->id; ?>" <?php echo $adelanto->id_cliente == $c->id ? 'selected' : ''; ?>>
                    <?php echo $c->ruc . ' - ' . $c->nombre; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" value="<?php echo $adelanto->monto; ?>" class="form-control" placeholder="Ingrese el monto" required step="any">
    </div>

    <div class="form-group">
        <label>Fecha</label>
        <input type="datetime-local" name="fecha" value="<?php echo $adelanto->id != null ? date('Y-m-d\TH:i', strtotime($adelanto->fecha)) : date('Y-m-d\TH:i'); ?>" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" placeholder="Descripción del adelanto"><?php echo $adelanto->descripcion; ?></textarea>
    </div>

    <div class="form-group">
        <label>Forma de Pago <span class="text-danger">*</span></label>
        <select name="forma_pago" class="form-control" required>
            <option value="">Seleccione una forma de pago</option>
            <?php foreach($this->metodo->ListarTodos() as $m): ?>
                <option value="<?php echo $m->metodo; ?>" <?php echo (isset($adelanto->forma_pago) && $adelanto->forma_pago == $m->metodo) ? 'selected' : ''; ?>>
                    <?php echo $m->metodo; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Comprobante <small class="text-muted">(Nro. de recibo, transferencia, etc.)</small></label>
        <input type="text" name="comprobante" value="<?php echo $adelanto->comprobante; ?>" class="form-control" placeholder="Número de comprobante (opcional)">
    </div>

    <hr />

    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }
    });
</script>

<h1 class="page-header">
    <?php echo $ingreso->id != null ? $ingreso->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
    <li><a href="?c=ingreso">ingreso</a></li>
    <li class="active"><?php echo $ingreso->id != null ? $ingreso->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=ingreso&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="ingreso" id="c" />
    <input type="hidden" name="id" value="<?php echo $ingreso->id; ?>" id="id" />
    <div class="form-group">
        <label>Fecha</label>
        <input type="datetime-local" name="fecha" value="<?php echo ($ingreso->fecha) ? date("Y-m-d", strtotime($ingreso->fecha)) : date("Y-m-d") ?>T<?php echo date("H:i"); ?>" class="form-control" placeholder="Fecha" required>
    </div>

    <div class="form-group">
        <label>Cliente</label>
        <select name="id_cliente" id="id_cliente" class="form-control" data-show-subtext="true" data-live-search="true" data-style="form-control"
            title="-- Seleccione el Cliente --" style="width:100%; display:0">
            <option value="0">Sin seleccionar</option>
            <?php foreach ($this->cliente->Listar() as $clie): ?>
                <option value="<?php echo $clie->id; ?>"><?php echo $clie->nombre . " ( " . $clie->ruc . " )"; ?> </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Forma de pago</label>
        <select name="forma_pago" class="form-control" id="pago">
            <?php foreach ($this->metodo->Listar() as $m): ?>
                <option value="<?php echo $m->metodo ?>" <?php echo ($m->metodo == $ingreso->forma_pago) ? "selected" : ""; ?>><?php echo $m->metodo ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Selector de moneda -->
    <div class="form-group">
        <label>Moneda</label>
        <select name="moneda" id="moneda_selector" class="form-control">
            <option value="GS" <?php echo (isset($ingreso->moneda) && $ingreso->moneda == 'GS') ? "selected" : ""; ?>>Guaraníes (GS)</option>
            <option value="USD" <?php echo (isset($ingreso->moneda) && $ingreso->moneda == 'USD') ? "selected" : ""; ?>>Dólares (USD)</option>
            <option value="RS" <?php echo (isset($ingreso->moneda) && $ingreso->moneda == 'RS') ? "selected" : ""; ?>>Reales (RS)</option>
        </select>
    </div>

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
        <input type="number" name="monto" value="<?php echo $ingreso->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="0" step="0.001" required>
    </div>



    <input type="hidden" name="sucursal" value="0">

    <hr />

    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Controlar la habilitación del selector de moneda al cargar
        controlMonedaSelector();

        $('#pago').on('change', function() {
            controlMonedaSelector();
        });

        function controlMonedaSelector() {
            var formaPago = $('#pago').val();
            var monedaSelect = $('#moneda_selector');

            if (formaPago === "Efectivo") {
                monedaSelect.prop('disabled', false);
            } else {
                // Para otros métodos, solo Guaraníes
                monedaSelect.prop('disabled', true);
                monedaSelect.val('GS');
            }
        }
    });
</script>
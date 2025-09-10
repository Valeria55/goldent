<h1 class="page-header">
    <?php echo $egreso->id != null ? $egreso->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
    <li><a href="?c=egreso">egreso</a></li>
    <li class="active"><?php echo $egreso->id != null ? $egreso->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=egreso&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="egreso" id="c" />
    <input type="hidden" name="id" value="<?php echo $egreso->id; ?>" id="id" />
    <div class="form-group">
        <label>Fecha</label>
        <input type="datetime-local" name="fecha" value="<?php echo (!$egreso->fecha) ? (date("Y-m-d") . "T" . date("H:i")) : date("Y-m-d", strtotime($egreso->fecha)) . "T" . date("H:i", strtotime($egreso->fecha)); ?>" class="form-control" placeholder="Fecha" required>
    </div>

    <div class="form-group">
        <label>Proveedor</label>
        <select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
            title="-- Seleccione al proveedor --" autofocus>
            <option value="1" selected>Proveedor ocasional</option>
            <?php foreach ($this->cliente->Listar() as $cliente): ?>
                <option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>" <?php echo ($cliente->id == $egreso->id_cliente) ? "selected" : ""; ?>><?php echo $cliente->nombre; ?> </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Forma de pago</label>
        <select name="forma_pago" class="form-control" id="pago">
            <?php foreach ($this->metodo->Listar() as $m): ?>
                <option value="<?php echo $m->metodo ?>" <?php echo ($m->metodo == $egreso->forma_pago) ? "selected" : ""; ?>><?php echo $m->metodo ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Selector de moneda -->
    <div class="form-group">
        <label>Moneda</label>
        <select name="moneda" id="moneda_selector" class="form-control">
            <option value="GS" <?php echo (isset($egreso->moneda) && $egreso->moneda == 'GS') ? "selected" : ""; ?>>Guaraníes (GS)</option>
            <option value="USD" <?php echo (isset($egreso->moneda) && $egreso->moneda == 'USD') ? "selected" : ""; ?>>Dólares (USD)</option>
            <option value="RS" <?php echo (isset($egreso->moneda) && $egreso->moneda == 'RS') ? "selected" : ""; ?>>Reales (RS)</option>
        </select>
    </div>

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
        <input type="number" name="monto" value="<?php echo $egreso->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="0" step="0.001" required>
    </div>



    <div class="form-group" id="nro_cheque" style="display: none;">
        <label>Nro Cheque</label>
        <input type="text" name="nro_cheque" id="cheque" value="<?php echo $egreso->nro_cheque; ?>" class="form-control" placeholder="Ingrese el comprobante">
    </div>
    <div class="form-group" id="plazo" style="display: none;">
        <label>Plazo</label>
        <input type="date" name="plazo" id="plazo_input" value="<?php echo $egreso->plazo; ?>" class="form-control" placeholder="Ingrese plazo">
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
        controlChequeFields();

        $('#pago').on('change', function() {
            controlMonedaSelector();
            controlChequeFields();
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

        function controlChequeFields() {
            var valor = $('#pago').val();
            if (valor == "Cheque") {
                $("#plazo").show();
                $("#nro_cheque").show();
            } else {
                $("#plazo").hide();
                $("#nro_cheque").hide();
            }
        }
    });
</script>
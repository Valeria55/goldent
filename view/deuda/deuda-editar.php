<?php $cierre = $this->cierre->Consultar($_SESSION['user_id']);  ?>
<h1 class="page-header">
    <?php echo $deuda->id != null ? $deuda->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
    <li><a href="?c=deuda">deuda</a></li>
    <li class="active"><?php echo $deuda->id != null ? $deuda->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=deuda&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="deuda" id="c" />
    <input type="hidden" name="id" value="<?php echo $deuda->id; ?>" id="id" />
    <input type="hidden" name="id_venta" value="0">

    <div class="row">
        <div class="form-group col-sm-6">
            <label>Fecha</label>
            <input type="date" name="fecha" value="<?php echo ($deuda->fecha) ? date("Y-m-d", strtotime($deuda->fecha)) : date("Y-m-d"); ?>" class="form-control" placeholder="Fecha" required>
        </div>
        <div class="form-group col-sm-6">
            <label>Vencimiento</label>
            <input type="date" name="vencimiento" value="<?php echo $deuda->vencimiento; ?>" class="form-control" placeholder="Ingrese el vencimiento">
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-12">
            <label>Cliente</label>
            <select name="id_cliente" id="id_cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true">
                <option value="">Cliente casual (XXX)</option>
                <?php foreach ($this->cliente->Listar() as $clie) : ?>
                    <option value="<?php echo $clie->id; ?>" <?php echo ($clie->id == $deuda->id_cliente) ? "selected" : ""; ?>><?php echo $clie->nombre . " ( " . $clie->ruc . " )"; ?> </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-12">
            <label>Concepto</label>
            <input type="text" name="concepto" value="<?php echo $deuda->concepto; ?>" class="form-control" placeholder="Ingrese su concepto" required>
        </div>
    </div>
    <div class="row">

        <div class="form-group col-sm-6">
            <label>Monto</label>
            <input type="float" id="monto" name="monto" step="any" value="<?php echo $deuda->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="0" required>
        </div>



        <div class="form-group col-sm-6">
            <label>Saldo</label>
            <input type="float" id="saldo" name="saldo" value="<?php echo $deuda->saldo; ?>" class="form-control" placeholder="Ingrese el saldo" min="0" required <?php echo $deuda->id != null ? 'readonly' : ''; ?>>
        </div>
    </div>
    <hr />

    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script>
    $("#monto").keyup(function() {
        $("#saldo").val($("#monto").val());
    });
</script>
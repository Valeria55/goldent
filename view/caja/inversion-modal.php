<?php
?>

<h3 class="page-header">
    Agregar ingreso externo
</h3>
<br>
<form method="post" action="?c=ingreso&a=entradaExterna" enctype="multipart/form-data">

    <div class="form-group">
        <label>Destino</label>
        <select name="id_caja" id="id_caja" class="form-control">
            <?php
            $tesoreria = $this->model->ObtenerBalance(3); //tesoreria
            ?>
                <option value="<?php echo $tesoreria->id ?>" id="<?php echo $tesoreria->id ?>"><?php echo $tesoreria->caja ?></option>
            <?php
            ?>
        </select>
    </div>

    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" autofocus step="any" value="0" max="" id="monto" min="0" class="form-control" placeholder="Ingrese su monto" required>
    </div>

    <hr/>

    <div class="modal-footer">
        <button class="btn btn-primary" onclick="return confirm('Agregar ingreso externo de ' + $('#monto').val() + ' Gs.?')">Confirmar</button>
        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
    </div>
</form>

<script>
    $("#id_emisor").change(function() {
        var caja = $(this).val();
        $("#" + caja).hide();
    });
</script>
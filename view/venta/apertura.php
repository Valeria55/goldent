<?php $cierre = $this->cierre->Ultimo(); ?>
<div class="col-sm-4">
</div>
<div class="col-sm-4">
    <form id="crud-frm" method="post" action="?c=cierre&a=apertura">
        <h1 style="color: black" align="center">Apertura de caja</h1>
        <div class="form-group">
            <label style="color: black">Cotización Dolar</label>
            <input type="number" id="dolares" name="cot_dolar" value="<?php echo $cierre->cot_dolar; ?>" class="form-control" min="1" step="0.01">
        </div>

        <div class="form-group">
            <label style="color: black">Cotización Real</label>
            <input type="number" id="reales" name="cot_real" value="<?php echo $cierre->cot_real; ?>" class="form-control" min="1" step="0.01">
        </div>

        <div class="form-group">
            <label style="color: black">Caja</label>
            <select name="id_caja" class="form-control">
                <?php //foreach($this->caja->ListarUsuario($_SESSION['user_id']) as $r): 
                ?>
                <option value="3">Caja chica</option>
                <?php //endforeach; 
                ?>
            </select>
        </div>

        <div class="form-group">
            <label style="color: black">Monto Inicial GS (Guaraníes)</label>
            <input type="number" name="monto_apertura" value="0" class="form-control" min="0" step="0.01">
        </div>

        <div class="form-group">
            <label style="color: black">Monto Inicial RS (Reales)</label>
            <input type="number" name="apertura_rs" value="0" class="form-control" min="0" step="0.01">
        </div>

        <div class="form-group">
            <label style="color: black">Monto Inicial USD (Dólares)</label>
            <input type="number" name="apertura_usd" value="0" class="form-control" min="0" step="0.01">
        </div>

        <div class="form-group">
            <input type="submit" onclick="this.disabled='disabled';this.form.submit();" class="btn btn-primary" value="Dar Apertura">
            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
        </div>
    </form>
    <div class="form-group">
        <p style="color: gray" align="center">* Referencia Cambios Chaco</p>
        <iframe width="100%" height="300" src="http://www.cambioschaco.com.py/widgets/cotizacion/?lang=es" frameborder="0"></iframe>
    </div>
</div>
<div class="col-sm-4">
</div>
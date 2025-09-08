
 <h1>Editar cierre de caja</h1>
<form id="crud-frm" method="post" enctype="multipart/form-data">

       
        <input type="hidden" name="c" value="cierre">
        <input type="hidden" name="a" value="EditarCierre">
        <input type="hidden" name="id" value="<?php echo $cierre->id; ?>">
         <center><h2>APERTURA</h2></center>
        <div class="form-group">
            <label>Apertura en (GS)</label>
            <input type="number" value="<?php echo $cierre->monto_apertura; ?>" name="monto_apertura" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Apertura en (USD)</label>
            <input type="number" value="<?php echo $cierre->apertura_usd; ?>" name="apertura_usd" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Apertura en (RS)</label>
            <input type="number" value="<?php echo $cierre->apertura_rs; ?>" name="apertura_rs" class="form-control" required>
        </div>
        <center><h2>CIERRE</h2></center>
        <div class="form-group">
            <label>Monto Efectivo(GS)</label>
            <input type="number" value="<?php echo $cierre->monto_cierre; ?>" name="monto_cierre" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Monto Efectivo(USD)</label>
            <input type="number" value="<?php echo $cierre->monto_dolares; ?>" name="monto_dolares" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Monto Efectivo(RS)</label>
            <input type="number" value="<?php echo $cierre->monto_reales; ?>" name="monto_reales" class="form-control" required>
        </div>

        <div class="text-right">
            <button class="btn btn-primary">Generar</button>
        </div>
                                    

                </form>
           
        </div>
    </div>

<div id="monedaModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
				<div class="modal-body">
                <?php $venta_tmp = $this->model->ObtenerMoneda();?>
				<form id="crud-frm" method="post" action="?c=venta_tmp&a=moneda">  
                    <h1 style="color: black" align="center">Apertura de caja</h1>
				    
				    <div class="form-group">
				        <label style="color: black">Cotización Dolar</label>
				        <input type="number" id="dolares" name="dolares" value="<?php echo $venta_tmp->dolares; ?>" class="form-control" min="1">
				    </div>

                    <div class="form-group">
                        <label style="color: black">Cotización Real</label>
                        <input type="number" id="reales" name="reales" value="<?php echo $venta_tmp->reales; ?>" class="form-control" min="1">
                    </div>

                    <div class="form-group">
                        <label style="color: black">Monto Inicial En caja</label>
                        <input type="number" name="monto_inicial" value="<?php echo $venta_tmp->monto_inicial; ?>" class="form-control" min="1">
                    </div>
                </div>
                    <div class="modal-footer">
                    	<input type="submit" class="btn btn-primary" value="Ajustar">
                        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                    </div>
            </form>
            <div align="center">
            <p style="color: gray" align="center">* Referencia Cambios Chaco</p>
            <iframe width="400" height="300" src="http://www.cambioschaco.com.py/widgets/cotizacion/?lang=es" frameborder="0"></iframe>
            </div>
            </div>
        </div>
    </div>

<!-- 


<form id="crud-frm" method="post" action="?c=venta&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="venta" id="c"/>
    <input type="hidden" name="id" value="<?php //echo $venta->id; ?>" id="id" />
    <div class="form-group">
        <label>Fecha</label>
        <input type="datetime-local" name="fecha" value="<?php //echo ($venta->fecha_venta) ? date("Y-m-d", strtotime($venta->fecha_venta)):date("Y-m-d") ?>T<?php //echo date("H:i"); ?>" class="form-control" placeholder="Fecha" required>
    </div>

   <div class="form-group col-sm-12" >
	    <label>Cliente </label>
        <select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                title="-- Seleccione el cliente --" autofocus>
            <option value="0" selected>CLiente ocasional</option>
                            <?php //foreach($this->cliente->Listar() as $cliente): ?> 
            <option data-subtext="<?php //echo $cliente->ruc; ?>" value="<?php //echo $cliente->id; ?>"<?php //echo ($venta->id_cliente == $cliente->id)? "selected":""; ?>><?php //echo $cliente->nombre.' '.$cliente->ruc; ?> </option>
            <?php //endforeach; ?>
        </select>
    </div>
    <div class="form-group col-sm-6" id="nro_comprobante">
        <label>Nro. comprobante</label>
        <input type="text" name="nro_comprobante" class="form-control" value="<?php //echo $venta->nro_comprobante; ?>" placeholder="Ingrese el nro de comprobante">
    </div>
    <div class="form-group col-sm-6">
    	<label>Comprobante</label>
    	<select name="comprobante" id="comprobante" class="form-control">
    		<option value="Ticket"<?php //echo ($venta->comprobante == 'Ticket')? "selected":""; ?>>Ticket</option>
    		<option value="TicketSi"<?php //echo ($venta->comprobante == 'TicketSi')? "selected":""; ?>>Sin impresión</option>
    		<option value="Factura" <?php //echo ($venta->comprobante == 'Factura')? "selected":""; ?>>Factura</option>  
    	</select>
    </div>
    
    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form> -->





<div id="editarVentaModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">  
                <form method="get">
                    <center><h2>Editar Venta </h2></center>
                    <input type="hidden" name="c" value="venta">
                    <input type="hidden" name="a" value="editar">
                    <input type="hidden" name="id_venta" value="" id="tipo">
                   
                    <div class="form-group col-sm-12" >
                        <label>Cliente </label>
                        <select name="id_cliente" id="cli" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                                title="-- Seleccione el cliente --" autofocus>
                            
                                            <?php foreach($this->cliente->Listar() as $cliente): ?> 
                            <option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>"><?php echo $cliente->nombre.' '.$cliente->ruc; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-6" >
                        <label>Nro. comprobante</label>
                        <input type="text" name="nro_comprobante" id="n" class="form-control" placeholder="Ingrese el numero de comprobante" readonly>
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Comprobante</label>
                        <select name="comprobante" id="co" class="form-control" disabled>
                            <option value="Ticket">Ticket</option>
                            <option value="TicketSi">Sin impresión</option>
                            <option value="Factura">Factura</option>  
                        </select>
                    </div>

                    <div class="form-group col-sm-6" id="div_pagare_edit">
                        <label>Generar Pagaré?</label>
                        <select name="pagare" id="pagare-edit" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Si</option>
                        </select>
                    </div>

                    <div class="text-right">
                        <!-- Campo oculto para indicar que solo se guarda sin imprimir -->
                        <input type="hidden" name="solo_guardar" value="1">
                        <button class="btn btn-success">Guardar</button>
                    </div>  
                                    

                </form>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
        </div>
    </div>
</div>


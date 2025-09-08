<?php 
    $cierre = $this->cierre->Consultar($_SESSION['user_id']);  ?>
<h1 class="page-header">
    <?php echo $ingreso->id != null ? $ingreso->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=ingreso">ingreso</a></li>
  <li class="active"><?php echo $ingreso->id != null ? $ingreso->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=ingreso&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="ingreso" id="c"/>
    <input type="hidden" name="id" value="<?php echo $ingreso->id; ?>" id="id" />

    <div class="form-group col-md-12">
        <label>Fecha</label>
        <input type="datetime-local" name="fecha" value="<?php echo ($ingreso->fecha) ? date("Y-m-d", strtotime($ingreso->fecha)):date("Y-m-d") ?>T<?php echo date("H:i"); ?>" class="form-control" placeholder="Fecha" required>
    </div>
    <div class="form-group col-md-12">
        <label>Cliente</label>
        <select name="id_cliente" id="id_cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                    title="-- Seleccione el Cliente --" style="width:100%; display:0">
            <option value="0">Sin seleccionar</option>
            <?php foreach($this->cliente->ListarClientes() as $clie): ?> 
            <option value="<?php echo $clie->id; ?>"<?php echo ($ingreso->id_cliente == $clie->id)? "selected":""; ?>><?php echo $clie->nombre." ( ".$clie->ruc." )"; ?> </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Categoria</label>
        <input type="text" name="categoria" value="<?php echo $ingreso->categoria; ?>" class="form-control" placeholder="Ingrese la categoria" required>
    </div>

    <div class="form-group col-md-6">
        <label>Concepto</label>
        <input type="text" name="concepto" value="<?php echo $ingreso->concepto; ?>" class="form-control" placeholder="Ingrese su concepto" required>
    </div>
    
    <div class="form-group col-md-6">
        <label>Comprobante</label>
        <select name="comprobante" class="form-control">
            <option value="Sin comprobante" <?php echo ($ingreso->comprobante == "Sin comprobante")? "selected":""; ?>>Sin comprobante</option>
            <option value="Factura" <?php echo ($ingreso->comprobante == "Factura")? "selected":""; ?>>Factura</option>
            <option value="Recibo" <?php echo ($ingreso->comprobante == "Recibo")? "selected":""; ?>>Recibo</option>
            <option value="Ticket" <?php echo ($ingreso->comprobante == "Ticket")? "selected":""; ?>>Ticket</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>N°Comprobante</label>
        <input type="text" name="nro_comprobante" value="<?php echo $ingreso->nro_comprobante; ?>" class="form-control" placeholder="Ingrese su comprobante" required>
    </div>
    
    <div class="form-group col-md-3">
        <label>Monto</label>
        <input type="float" name="monto" value="<?php echo $ingreso->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="1" required>
    </div>
    
    <div class="form-group col-md-2" id="mon">
        <label>Moneda</label>
        <select name="moneda" id="moneda"  class="form-control">
            <option value="GS">GS</option>
            <option value="USD">USD</option>
            <option value="RS">RS</option>
        </select>
    </div>

    <div class="form-group col-md-2" id="can">
        <label style="color: black">Cambio</label>
        <input type="text" name="cambio" id="cambio" value="1" class="form-control" min="1">
    </div>

    <div class="form-group col-md-3">
        <label>Forma de pago</label>
        <select name="forma_pago" class="form-control" >
            <?php foreach ($this->metodo->Listar() as $m): ?>
                <option value="<?php echo $m->metodo ?>"<?php echo ($ingreso->forma_pago ==  $m->metodo)? "selected":""; ?>><?php echo $m->metodo ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group col-sm-2">
        <label>Tipo</label>
        <select type="text" name="tarjeta" id="tarjeta" class="form-control" >
            <option value="Sin seleccionar" <?php echo ($ingreso->tarjeta == 'Sin seleccionar')? "selected":""; ?>>Sin seleccionar</option>
            <option value="CREDITO" <?php echo ($ingreso->tarjeta == 'CREDITO')? "selected":""; ?>>CREDITO</option>
            <option value="DEBITO" <?php echo ($ingreso->tarjeta == 'DEBITO')? "selected":""; ?>>DEBITO</option>
            <option value="TRANSFERENCIA" <?php echo ($ingreso->tarjeta == 'TRANSFERENCIA')? "selected":""; ?>>TRANSFERENCIA</option>
        </select>
    </div>
    <div class="form-group col-md-12">
        <label>Caja</label>
        <select name="id_caja" class="form-control">
            <option value="1" <?php echo ($ingreso->id_caja == 1)? "selected":""; ?>>CAJA CHICA</option>
            <option value="3" <?php echo ($ingreso->id_caja == 3)? "selected":""; ?>>TESORERIA</option>
            <option value="2" <?php echo ($ingreso->id_caja == 2)? "selected":""; ?>>BANCO</option>
        </select>
    </div>
    <input type="hidden" name="sucursal" value="0">
    
    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script>
	$('#moneda').on('change',function(){
		var valor = $(this).val();
		
		var cot_gs = '1';
		// var cot_dol = '1';
		var cot_real = <?php echo $cierre->cot_real_tmp??0; ?>;
		// var cot_real = <?php //echo $cierre->cot_real_tmp??0; ?>;
		var cot_dol = <?php echo $cierre->cot_dolar_tmp??0; ?>;
		// var cot_gs = <?php //echo $cierre->cot_dolar_tmp??0; ?>;
		
		console.log($(this).val());
		if (valor == "GS") {
			$("#cambio").val(cot_gs);
			console.log('valor = '+ valor);
			
		}else if(valor == "RS"){
			$("#cambio").val(cot_real);
			console.log('valor = '+ valor);

		}else{
			$("#cambio").val(cot_dol);
			console.log('valor = '+ valor);
		}

	});
</script>

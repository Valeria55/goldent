<?php 
    $cierre = $this->cierre->Consultar($_SESSION['user_id']);  ?>
<h1 class="page-header">
    <?php echo $egreso->id != null ? $egreso->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=egreso">egreso</a></li>
  <li class="active"><?php echo $egreso->id != null ? $egreso->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=egreso&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="egreso" id="c"/>
    <input type="hidden" name="id" value="<?php echo $egreso->id; ?>" id="id" />
    <div class="form-group col-md-12">
        <label>Fecha</label>
        <input type="datetime-local" name="fecha" value="<?php echo (!$egreso->fecha)? (date("Y-m-d")."T".date("H:i")) : date("Y-m-d", strtotime($egreso->fecha))."T".date("H:i", strtotime($egreso->fecha)); ?>" class="form-control" placeholder="Fecha" required>
    </div>

    <input type="hidden" name="id_cliente" value="0">    
    <div class="form-group col-md-12" >
        <label>Proveedor</label>
       <select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                    title="-- Seleccione al proveedor --" autofocus>
            <option value="0" selected>Proveedor ocasional</option>
            <?php foreach($this->cliente->Listar() as $cliente): ?> 
                 <option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>"<?php echo ($cliente->id ==$egreso->id_cliente)? "selected":""; ?>><?php echo $cliente->nombre; ?> </option>
            <?php endforeach; ?>
        </select>
    </div>


    <div class="form-group col-md-6">

        <label>Gastos Fijos</label>
        <select name="gasto_fijo" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                title="-- Seleccione el Gasto Fijo --" style="width:100%; display:0" >

            <?php foreach ($this->gastos_fijos->Listar() as $g): ?>
                <?php if ($g->anulado == null): ?>
                <option value="<?php echo $g->id ?>"<?php echo ($egreso->id_gasto_fijo ==  $g->id)? "selected":""; ?>><?php echo $g->descripcion ?></option>
                <?php endif; ?>
                <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Mes y Año de Pago</label>
         <input type="date"  max="<?php echo date("Y-m") ?>" name="fecha_gasto_fijo" value="<?php echo $egreso->fecha_gasto_fijo;?>"  class="form-control">
    </div>

    <div class="form-group col-md-6">
        <label>Categoria</label>
        <input type="text" name="categoria" value="<?php echo $egreso->categoria; ?>" class="form-control" placeholder="Ingrese la categoria" required>
    </div>

    <div class="form-group col-md-6">
        <label>Concepto</label>
        <input type="text" name="concepto" value="<?php echo $egreso->concepto; ?>" class="form-control" placeholder="Ingrese su concepto" required>
    </div>
    
    <div class="form-group col-md-4">
        <label>Comprobante</label>
        <select name="comprobante" class="form-control">
            <option value="Sin comprobante" <?php echo ($egreso->comprobante == "Sin comprobante")? "selected":""; ?>>Sin comprobante</option>
            <option value="Factura" <?php echo ($egreso->comprobante == "Factura")? "selected":""; ?>>Factura</option>
            <option value="Recibo" <?php echo ($egreso->comprobante == "Recibo")? "selected":""; ?>>Recibo</option>
            <option value="Ticket" <?php echo ($egreso->comprobante == "Ticket")? "selected":""; ?>>Ticket</option>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>N° Comprobante</label>
        <input type="text" name="nro_comprobante" value="<?php echo $egreso->nro_comprobante; ?>" class="form-control" placeholder="Ingrese su comprobante" >
    </div>
    <div class="form-group col-md-4">
        <label>Tipo</label>
        <select name="tipo_egreso" class="form-control">
            <option value="SIN SELECCIONAR" <?php echo ($egreso->tipo_egreso == "SIN SELECCIONAR")? "selected":""; ?>>SIN SELECCIONAR</option>
            <option value="COMPRA" <?php echo ($egreso->tipo_egreso == "COMPRA")? "selected":""; ?>>COMPRA</option>
            <option value="GASTO FIJO" <?php echo ($egreso->tipo_egreso == "GASTO FIJO")? "selected":""; ?>>GASTO FIJO</option>
            <option value="MOVIMIENTOS" <?php echo ($egreso->tipo_egreso == "MOVIMIENTOS")? "selected":""; ?>>MOVIMIENTOS</option>
            <option value="GASTO VARIABLE" <?php echo ($egreso->tipo_egreso == "GASTO VARIABLE")? "selected":""; ?>>GASTO VARIABLE</option>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label>Monto</label>
        <input type="float" name="monto" value="<?php echo $egreso->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="1" required>
    </div>

    <div class="form-group col-md-2" id="mon">
        <label>Moneda</label>
        <select name="moneda" id="moneda"  class="form-control"  required>
            <option value="GS" <?php echo ($egreso->moneda == "GS")? "selected":""; ?>>GS</option>
            <option value="USD" <?php echo ($egreso->moneda == "USD")? "selected":""; ?>>USD</option>
            <option value="RS" <?php echo ($egreso->moneda == "RS")? "selected":""; ?>>RS</option>
        </select>
    </div>

    <div class="form-group col-md-3" id="can">
        <label style="color: black">Cambio</label>
        <input type="text" name="cambio" id="cambio" value="<?php echo $egreso->cambio; ?>" class="form-control" placeholder="Ingrese el monto" min="1" required>
    </div>
    <div class="form-group col-md-4">
        <label>Forma de pago</label>
        <select name="forma_pago" class="form-control" id="pago" >
            <?php foreach ($this->metodo->Listar() as $m): ?>
                <option value="<?php echo $m->metodo ?>"<?php echo ($egreso->forma_pago ==  $m->metodo)? "selected":""; ?>><?php echo $m->metodo ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group col-md-12">
        <label>Caja</label>
        <select name="id_caja" class="form-control">
            <option value="1" <?php echo ($egreso->id_caja == 1)? "selected":""; ?>>CAJA CHICA</option>
            <option value="3" <?php echo ($egreso->id_caja == 3)? "selected":""; ?>>TESORERIA</option>
            <option value="2" <?php echo ($egreso->id_caja == 2)? "selected":""; ?>>BANCO</option>
        </select>
    </div>
     <div class="form-group"id="nro_cheque"style="display: none;" >
        <label>Nro Cheque</label>
        <input type="text" name="nro_cheque" id="cheque" value="<?php echo $egreso->nro_cheque; ?>" class="form-control" placeholder="Ingrese el comprobante"  >
    </div>
    <div class="form-group" id="plazo" style="display: none;">
        <label>Plazo</label>
        <input type="date" name="plazo" id="plazo" value="<?php echo $egreso->plazo; ?>" class="form-control" placeholder="Ingrese plazo"  >
    </div>
    
    <input type="hidden" name="sucursal" value="0">
    
    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>


<script>
	$('#pago').on('change',function(){
		var valor = $(this).val();
		if (valor == "Cheque") {
			
			$("#plazo").show();
			$("#nro_cheque").show();
		}else{
		
			$("#plazo").hide();
			$("#nro_cheque").hide();
		}
	});

    $('#moneda').on('change',function(){
		var valor = $(this).val();
		
        var cot_gs = '1';
		var cot_real = <?php echo $cierre->cot_real_tmp??0; ?>;
		var cot_dol = <?php echo $cierre->cot_dolar_tmp??0; ?>;
		// var cot_dol = '1';
		// var cot_real = <?php //echo $cierre->cot_real_tmp; ?>;
		// var cot_gs = <?php //echo $cierre->cot_dolar_tmp; ?>;
		
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

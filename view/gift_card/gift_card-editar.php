<h1 class="page-header">
     Gift Card
</h1>

<ol class="breadcrumb">
  <li><a href="?c=gift_card">gift_card</a></li>
  <li class="active"><?php echo $gift_card->id != null ? $gift_card->id : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=gift_card&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="gift_card" id="c"/>
    <input type="hidden" name="id" value="<?php echo $gift_card->id; ?>" id="id" />
  
    
  <div class="form-group" >
		<label>Cliente</label>
        <select name="id_cliente" id="cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el cliente --" autofocus>
            <?php foreach($this->cliente->Listar() as $cliente): ?> 
                 <option data-subtext="<?php echo $cliente->ruc; ?>" value="<?php echo $cliente->id; ?>"<?php echo ($gift_card->id_cliente == $cliente->id)? "selected":""; ?>><?php echo $cliente->nombre; ?> </option>
            <?php endforeach; ?>
        </select>
  </div>
  <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" value="<?php echo $gift_card->monto; ?>" class="form-control" placeholder="Ingrese el monto" required>
  </div>
  <div class="form-group">
       <div class="alert alert-danger alert-dismissible fade in" role="alert" style="display: none;" id="incorrecta">
            <strong> Error!</strong> Ya existe el N° de Tarjeta
        </div>
        <label>N° tarjeta</label>
        <input type="text" name="nro_tarjeta"  id="nro_tarjeta" value="<?php echo $gift_card->nro_tarjeta; ?>" class="form-control" placeholder="Ingrese el numero de tarjeta" required>
  </div>
  <div class="form-group">
        <label>Comprobante</label>
        <select name="comprobante" id="comprobante" class="form-control">
                <option value="Sin comprobante">Sin impresión</option>
                <option value="Ticket">Ticket</option>
                <option value="Recibo">Recibo</option>
                <option value="Factura">Factura</option>  
        </select>
  </div>
                    
  <div class="form-group" id="nro_comprobante">
         <label>Nro. comprobante</label>
                <input type="text" name="nro_comprobante" class="form-control" placeholder="Ingrese el nro de comprobante">
  </div>
   <div class="form-group">
		<label>Forma de pago</label>
		<select name="forma_pago" class="form-control" >
			<?php foreach ($this->metodo->Listar() as $m): ?>
				<option value="<?php echo $m->metodo ?>"<?php echo ($m->metodo == $gift_card->forma_pago)? "selected":""; ?>><?php echo $m->metodo ?></option>
			<?php endforeach; ?>
		</select>
	</div>
    

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script type="text/javascript">

   $('#nro_tarjeta').change(function() {

         event.preventDefault();
        var nro_tarjeta = $("#nro_tarjeta").val();

        var url = "?c=gift_card&a=BuscarTarjeta&numero="+nro_tarjeta;
         console.log(url);
            $.ajax({
                url: url,
                method : "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var correcta = respuesta;
                      //alert(correcta);
              

                if(correcta == "true"){
                    $("#incorrecta").show();
                }else{
                   $("#incorrecta").hide();
                }
                

                    
                 // $("#nro_tarjeta").reset();
                 // alert(respuesta);
                 
                }

            })
    });

    
</script>

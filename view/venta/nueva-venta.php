 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Nueva venta <a class="btn btn-info " href="#clienteModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="cliente">+Cliente</a>
<a class="btn btn-lg btn-primary pull-right" href="#cierreModal" class="btn btn-success" data-toggle="modal" data-target="#cierreModal" data-c="venta">Cierre de caja</a></h1>
<!-- <div class="container">
    <div class="row" >
        <form method="post" id="productoNuevo">
        <div class="col-sm-3">
            <label>Servicios</label>
            <select name="id_producto" id="producto" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                    title="-- Seleccione el producto --" autofocus required>
                <?php //foreach($this->producto->ListarServicios() as $producto): $promo = ($producto->precio_promo > 0)? " promo = ".number_format($producto->precio_promo,0,".","."):""; ?> 
                <option style="font-size: 18px" data-subtext="<?php //echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>" ><?php echo $producto->producto.' - '.number_format($producto->precio_minorista,0,".",".").$promo; ?> </option>
                <?php //endforeach; ?>
        </select>
        </div>
            <div class="col-sm-2">
                <label>Cantidad</label>
                <input type="number" name="cantidad" class="form-control" id="cantidad" value="1" step="any" min="1" max="">   
            </div>
            <div class="col-sm-3">
                <label>Precio</label>
                <select name="precio_venta" class="form-control" id="precio_venta">
                    <option id="precio_minorista" value=""> -- Seleccionar --</option>
                </select>
            </div>
            <div class="col-sm-2">
                <label>Descuento (%)</label>
                <input type="number" name="descuento" class="form-control" id="descuento" max="10" value="0">  
            </div>
            <div class="col-sm-2" style="padding-top: 25px">
                <input type="submit" name="bton" value="Agregar" class="btn btn-primary">   
            </div>
        </form>
    </div>
</div> -->
<p> </p>

<?php include("view/crud-modal.php"); ?>
<div class="table-responsive" id="tabla_items">

 <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Codigo</th>
            <th>Producto</th>
            <th>Paciente</th>
            <th>Precio por Unidad</th>
            <th>Cantidad</th>
            <th>Descuento (%)</th>
            <th>Total (Gs.)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     $totalItem=0;
     $tiene_descuento = false;
     foreach($this->model->Listar() as $r):
        // si esta false se pregunta si el descuento != 0, en ese caso queda true
        if(!$tiene_descuento){ 
            $tiene_descuento = ($r->descuento != 0); 
        }
        $totalItem = (($r->precio_venta*$r->cantidad)-(($r->precio_venta*$r->cantidad)*($r->descuento/100)));
        $subtotal += ($totalItem);?>
        <tr>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo $r->paciente; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                <?php echo number_format( $totalItem, 0, "," , "."); ?></div></td>
            <td>
                <a  class="btn btn-danger cancelar" id_item="<?php echo $r->id; ?>">Cancelar</a>
            </td>
        </tr>
        <input type="hidden" id="clienteId" value="<?php echo $r->id_venta; ?>">
    <?php endforeach; ?>
        
        
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Total Gs: <div id="total" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Paraguay.png"></i><?php echo number_format($subtotal,0,",",".") ?></div></td>
            <td>Total Rs: <div id="totalrs" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Brazil.png"></i><?php echo number_format(($subtotal/$cierre->cot_real), 2, "," , ".") ?></div></td>
            <td>Total Us: <div id="totalus" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/USA.png"></i><?php echo number_format(($subtotal/$cierre->cot_dolar), 2, "," , ".") ?></div></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
 </table>

 <?php if($subtotal>0){ ?>
    <div align="center">
        <a class="btn btn-lg btn-primary " href="#finalizarModal" class="btn btn-success" data-toggle="modal" data-target="#finalizarModal" data-c="venta">Finalizar (F4)</a>
        <a  class="btn btn-lg btn-danger delete" href="?c=venta_tmp&a=CancelarVenta">Cancelar Todo</a>
    </div>
<?php } ?>
 </div>
</div>

</div>
</div>
</div>
 <?php include("view/venta/finalizar-modal.php"); ?>
<?php include("view/venta/cierre-modal.php"); ?>
<script type="text/javascript">



    $('.cancelar').on('click',function(){
        var datos = {};
        datos.id = $(this).attr("id_item");
        $.ajax({
            method : "POST",
            url: "?c=venta_tmp&a=eliminar",
            data: datos,
            success: function (data) { $("#tabla_items").html(data) } 
        });
    });

    $('#productoNuevo').submit(function (e) {

      e.preventDefault();
      var datos = $(this).serialize();
      $.ajax({
        method : "POST",
        url: "?c=venta_tmp&a=guardar",
        data: datos,
        success: function (data) {
            $("#tabla_items").html(data); 
            $("#productoNuevo")[0].reset();
            $('#producto').selectpicker('refresh');
            $("#precio_minorista").html("");
            $("#producto").focus();
            $('.selectpicker').selectpicker();
        } 
      });
    });
    
    $('#finalizarModal').on('show.bs.modal', function (event) {
		$("#monto_efectivo").focus();
	})

    $('#producto').on('change',function(){
        
        var id = $(this).val(); 
        var url = "?c=producto&a=buscar&id="+id;
            $.ajax({

                url: url,
                method : "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var producto = JSON.parse(respuesta);
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = today.getFullYear();
                    
                    today = yyyy + '-' + mm + '-' + dd;
                    console.log(producto.precio_promo + '> 0 && ' + producto.desde + '<=' +today + '<=' + producto.hasta);
                    if(producto.precio_promo > 0 && producto.desde <= today && producto.hasta >= today){
                        $("#precio_minorista").val(producto.precio_promo);
                        $("#precio_minorista").html(producto.precio_promo);
                    }else{
                        $("#precio_minorista").val(producto.precio_minorista);
                        $("#precio_minorista").html(producto.precio_minorista);
                    }
                    
                    $("#precio_mayorista").val(producto.precio_mayorista);
                    $("#precio_mayorista").html(producto.precio_mayorista+" (Mayor)");
                    $("#cantidad").select();
                }

            })
    });
    
    

    

    

</script>
 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Editar compra</h1> 
<div align="center" width="30%"> 
    
</div>
<div align="center" width="30%"> 
    <form method="post" action="?c=compra&a=guardarUno">
        <input type="hidden" name="id_compra" value="<?php echo $compra->id_compra ?>">
        <input type="hidden" name="id_cliente" value="<?php echo $compra->id_cliente ?>">
        <input type="hidden" name="id_vendedor" value="<?php echo $compra->id_vendedor ?>">
        <input type="hidden" id="precio_costo" name="precio_costo" value="">
        <input type="hidden" name="comprobante" value="<?php echo $compra->comprobante ?>">
        <input type="hidden" name="nro_comprobante" value="<?php echo $compra->nro_comprobante ?>">
        <input type="hidden" name="id_vendedor" value="<?php echo $compra->id_vendedor ?>">
        <input type="hidden" name="fecha_compra" value="<?php echo $compra->fecha_compra ?>">
        <input type="hidden" name="metodo" value="<?php echo $compra->metodo ?>">
        <input type="hidden" name="banco" value="<?php echo $compra->banco ?>">
        <input type="hidden" name="contado" value="<?php echo $compra->contado ?>">

        <table>
            <tr>
                <td>Producto: &nbsp;</td>
                <td>
                    <select name="id_producto" id="producto" class="selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                    title="-- Seleccione el producto --" autofocus>
                        <?php foreach($this->producto->Listar() as $producto): ?> 
                        <option data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->codigo; ?>"><?php echo $producto->producto; ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="id_res" value="0">
                    <input type="submit" name="s" value="" style="display: none">
                </td>

                <td><input type="number" class="form-control" step="any" min="0" name="cantidad" id="cantidad" placeholder="cantidad" required> </td>
                <td><input type="number" class="form-control" step="any" min="0" name="precio_compra" id="precio_compra" placeholder="Precio" required> </td>
            </tr>
        </table>
        <br />
    </form>
</div>
<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Producto</th>
            <th>Precio por Unidad</th>
            <th>Cantidad</th>
            <th>Total (GS)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     $id_compra = $_GET['id'];
     foreach($this->compra->Listar($id_compra) as $r): ?>
        <tr>
            
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_compra, 0, "," , "."); ?></td>
            <td>
                <input type="number" class="cantidad_item" name="cantidad_item" id="cantidad_item" min="1" id_item="<?php echo $r->id; ?>" id_compra="<?php echo $id_compra; ?>" cantidad_ant="<?php echo $r->cantidad; ?>" codigo="<?php echo $r->id_producto; ?>" value="<?php echo $r->cantidad; ?>">
            </td>
            <td><div id="precioTotal<?php echo $r->id; ?>" class="total_item"><?php echo number_format(($r->precio_compra*$r->cantidad), 0, "," , "."); ?></div></td>
            <td>
                <a  class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este Item?');" href="?c=compra&a=cancelar&id_item=<?php echo $r->id; ?>&id_compra=<?php echo $id_compra; ?>&cantidad_item=<?php echo $r->cantidad; ?>&codigo=<?php echo $r->id_producto; ?>">Cancelar</a>
            </td>
        </tr>
    <?php $subtotal += ($r->precio_compra*$r->cantidad) ;endforeach; ?>
        <tr>
            <td></td>
            <td></td>
            <td>Sub total:</td>
            <td><div id="subtotal" style="font-size: 20px"><?php echo number_format($subtotal,2,",",".") ?></div></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>Descuento:</td>
            <td><input type="number" min="0" max="70" value="0" id="descuento" name="descuento"></td>
            <td></td>
        </tr>
        
        <tr>
            <td></td>
            <td>Total Gs: <div id="total" style="font-size: 20px"><?php echo number_format($subtotal,2,",",".") ?></div></td>
            <td>Total Rs: <input type="number" min="0" name="totalrs" class="totalrs" id="totalrs" value="<?php echo round(($subtotal/$compra_tmp->reales),2) ?>"  readonly></td>
            <td>Total Us: <input type="number" min="0" name="totalus" class="totalus" id="totalus" value="<?php echo round(($subtotal/$compra_tmp->dolares),2) ?>"  readonly></td>
            <td></td>
        </tr>
    </tbody>
</table> 
<?php if($subtotal < 0){ ?>
<div align="center"><a class="btn btn-primary " href="#finalizarModal" class="btn btn-success" data-toggle="modal" data-target="#finalizarModal" data-c="compra">Finalizar</a></div>
<?php } ?>
</div> 
</div>
</div>
<?php include("view/finalizar-modal.php"); ?>

<script type="text/javascript">

    $('#producto').on('change',function(){
        var id = $(this).val();
        var url = "?c=producto&a=buscar&id="+id;
        alert(url);
            $.ajax({

                url: url,
                method : "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var producto = JSON.parse(respuesta);
                    console.log(respuesta);
                    $("#precio_compra").val(producto.precio_compra);
                    $("#precio_costo").val(producto.precio_costo);
                }

            })
    });

    $('.cantidad_item').on('change',function(){
        var cantidad = $(this).val();
        var id_item = $(this).attr("id_item");
        var cantidad_ant = $(this).attr("cantidad_ant");
        var codigo = $(this).attr("codigo");
        var id_compra = $(this).attr("id_compra");
        var url = "?c=compra&a=cambiar&cantidad="+cantidad+"&id_item="+id_item+"&id_compra="+id_compra+"&cantidad_ant="+cantidad_ant+"&codigo="+codigo;
        $(this).attr("cantidad_ant", cantidad);
            $.ajax({

                url: url,
                method : "POST",
                data: cantidad,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var producto = JSON.parse(respuesta);
                    var total = (producto.cantidad*producto.precio_compra).toLocaleString();
                    $("#precioTotal"+id_item).html(total);
                    var sum=0;
                    $('.total_item').each(function() {  
                        sum += parseInt($(this).text().replace(/\D/g,""));
                    }); 
                    $('#subtotal').html(sum.toLocaleString());
                    $('#total').html(sum.toLocaleString());
                }

            })
    });


    function calcular(){
        var subtotal = $('#subtotal').val();
        var descuento = $('#descuento').val();
        var iva = $('#iva').val(); 
        var reales = $('#reales').val();
        var dolares = $('#dolares').val();       
        $('#descuentoval').val(descuento); 
        $('#ivaval').val(iva);
        if(descuento==0 && iva==0){
            var total = subtotal;
        }
        if(descuento==0 && iva!=0){
            var ivac = parseInt(subtotal * (iva/100));
            var total = parseInt(subtotal) + ivac;
        }
        if(descuento!=0 && iva==0){
            var total = subtotal - (subtotal * (descuento/100));
        }
        if(descuento!=0 && iva!=0){
            var ivac = parseInt(subtotal * (iva/100));
            var num = parseInt(subtotal) + ivac;
            var total = num - (subtotal * (descuento/100));
        }
        var totalrs = (total/reales).toFixed(2);
        var totalus = (total/dolares).toFixed(2);
        var totalc = total.toLocaleString();

        $('.totaldesc').val(totalc);
        $('#totalrs').val(totalrs);
        $('#totalus').val(totalus);
    }


</script>
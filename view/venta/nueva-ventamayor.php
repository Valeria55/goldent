 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Nueva venta mayorista<a class="btn btn-lg btn-primary " href="#cierreModal" class="btn btn-success" data-toggle="modal" data-target="#cierreModal" data-c="venta">Cierre de caja</a></h1>
<div class="container">
    <div class="row" >
        <form method="post" action="?c=venta_tmp&a=guardarmayorista">
            <div class="col-sm-3">
            <label>Producto</label>
            <select name="id_producto" id="producto" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                    title="-- Seleccione el producto --" autofocus>
                <?php foreach($this->producto->Listar() as $producto): ?> 
                <option style="font-size: 18px" data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>" <?php echo ($producto->stock<1)? 'disabled':''; ?> 
                    <?php
                    if(isset($_GET['id_producto']) && $_GET['id_producto'] == $producto->id){
                        echo 'selected';
                    }
                    ?>
                ><?php echo $producto->producto.' ( '.$producto->stock.' ) - '.number_format($producto->precio_minorista,0,".","."); ?> </option>
                <?php endforeach; ?>
        </select>
        </div>
            <div class="col-sm-3">
                <label>Cantidad</label>
                <input type="number" name="cantidad" class="form-control" id="cantidad" value="1" min="1" max="<?php echo $_GET['max'] ?>">   
            </div>
            <div class="col-sm-3">
                <label>Precio</label>
                <input type="number" name="precio_venta" id="precio_mayorista" value="<?php echo $_GET['precio'] ?>" class="form-control">
            </div>
            <div class="col-sm-3">
                <a class="btn btn-lg btn-primary " href="#historialModal" class="btn btn-success" data-toggle="modal" data-target="#historialModal" data-c="venta">Historial de venta</a>
                <input type="hidden" name="descuento" value="0">
                <input type="submit" name="bton" style="display: none">   
            </div>
        </form>
    </div>
</div>
<p> </p>
<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Producto</th>
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
     foreach($this->model->Listar() as $r): 
        $totalItem = (($r->precio_venta*$r->cantidad)-(($r->precio_venta*$r->cantidad)*($r->descuento/100)));
        $subtotal += ($totalItem);?>
        <tr>
            
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                <?php echo number_format( $totalItem, 0, "," , "."); ?></div></td>
            <td>
                <a  class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=venta_tmp&a=Eliminar&id=<?php echo $r->id; ?>">Cancelar</a>
            </td>
        </tr>
        <input type="hidden" id="clienteId" value="<?php echo $r->id_venta; ?>">
    <?php endforeach; ?>
        
        
        <tr>
            <td></td>
            <td>Total Gs: <div id="total" style="font-size: 30px"><?php echo number_format($subtotal,0,",",".") ?></div></td>
            <td>Total Rs: <div id="totalrs" style="font-size: 30px"><?php echo number_format(($subtotal/$cierre->cot_real), 2, "," , ".") ?></div></td>
            <td>Total Us: <div id="totalus" style="font-size: 30px"><?php echo number_format(($subtotal/$cierre->cot_dolar), 2, "," , ".") ?></div></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table> 
<?php if($subtotal>0){ ?>
<div align="center"><a class="btn btn-lg btn-primary " href="#finalizarModal" class="btn btn-success" data-toggle="modal" data-target="#finalizarModal" data-c="venta">Finalizar (F4)</a></div>
<?php } ?>
</div> 
</div>
</div>
<?php include("view/venta/historial-modal.php"); ?>
<?php include("view/venta/finalizar-modal-mayorista.php"); ?>
<?php include("view/venta/cierre-modal.php"); ?>

<script type="text/javascript">
    

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
                    var precio = producto.precio_mayorista;
                    $("#precio_minorista").val(producto.precio_minorista);
                    $("#precio_mayorista").val(producto.precio_mayorista);
                    $("#precio_minorista").html(producto.precio_minorista+" (Minor)");
                    $("#precio_mayorista").html(producto.precio_mayorista+" (Mayor)");
                    $("#descuento").attr("max",producto.descuento_max);
                    $("#cantidad").attr("max",producto.stock);
                    $("#cantidad").focus();
                    window.location="?c=venta_tmp&a=mayorista&id_producto="+id+"&precio="+producto.precio_mayorista+"&max="+producto.stock;
                }

            });
    });

    $(document).ready(function () {
        var id = $("#clienteId").val();
        if(id>0){
            var url = "?c=cliente&a=buscar&id="+id;
            var categoria = "Plata";
            var descuento = 0;
            $.ajax({

                url: url,
                method : "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var cliente = JSON.parse(respuesta);
                    $("#puntos").val(cliente.puntos);
                    if(cliente.gastado < 3000000){
                        categoria = 'Plata';
                        descuento = 5;
                    }else if(cliente.gastado >= 3000000 && cliente.gastado < 10000000){
                        categoria = 'Oro';
                        descuento = 10;
                    }else{
                        categoria = 'Platino';
                        descuento = 15;
                    }
                    $("#categoria").val(categoria);
                    $("#cont_cli").html("<input type='text' value='"+cliente.nombre+"' class='form-control' readonly>");
                }

            })
        }
    });

    $('#cliente').on('change',function(){
        var id = $(this).val();
        var url = "?c=cliente&a=buscar&id="+id;
        var categoria = "Plata";
        var descuento = 0;
            $.ajax({

                url: url,
                method : "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var cliente = JSON.parse(respuesta);
                    $("#puntos").val(cliente.puntos);
                    if(cliente.gastado<3000000){
                        categoria = 'Plata';
                        descuento = 5;
                    }else if(cliente.gastado >= 3000000 && cliente.gastado<10000000){
                        categoria = 'Oro';
                        descuento = 10;
                    }else{
                        categoria = 'Platino';
                        descuento = 15;
                    }
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
<?php 
    $fecha = date("Y-m-d"); 
    $id_venta = $_GET['id_venta'];
?>
<h1 class="page-header"> Nueva devolución - venta <?php echo $id_venta ?></h1>
<div class="container">
    <div class="row" >
        <form method="post">
                <input type="hidden" id="id_venta" name="id_venta" value="<?php echo $id_venta ?>">
                <input type="hidden" name="c" value="devolucion_tmpventas">
                <input type="hidden" name="a" value="guardar">
                <div class="col-sm-3">
                <label>Producto</label>
                <select name="id_producto" id="producto" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                            title="-- Seleccione el producto --" autofocus>
                        <?php foreach($this->producto->ListarVentaProducto($id_venta) as $producto): ?> 
                            <?php if ($producto->cantidad_restante > 0): ?>
                                <option style="font-size: 18px" data-subtext="<?php echo $producto->id; ?>" value="<?php echo $producto->id; ?>"><?php echo $producto->producto.' ( '.$producto->stock_s1.' ) - '.number_format($producto->precio_minorista,0,".","."); ?> </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-3">
                <label>Cantidad</label>
                <input type="number" name="cantidad" class="form-control" id="cantidad" value="1" step="any" min="0" max="">   
            </div>
             <div class="col-sm-3">
                <label>Precio</label>
               <input type="float" name="precio_venta" class="form-control" id="precio_venta" value="">   
            </div>
            <div class="col-sm-3" style="display: none">
                <label>Descuento (%)</label>
                <input type="number" name="descuento" class="form-control" id="descuento" value="0">
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
            <th>Descuento (%) <?php echo $cierre->cot_real ;?></th>
            <th>Total (Gs.)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     foreach($this->model->Listar() as $r): 
        $totalItem = ($r->precio_venta*$r->cantidad);
        $subtotal += ($totalItem);?>
        <tr>
            
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                <?php echo number_format( $totalItem, 0, "," , "."); ?></div></td>
            <td>
                <a  class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=devolucion_tmpventas&a=Eliminar&id=<?php echo $r->id; ?>&id_venta=<?php echo $id_venta; ?>">Cancelar</a>
            </td>
        </tr>
        <input type="hidden" id="clienteId" value="<?php echo $r->id_venta; ?>">
    <?php endforeach; ?>
        
        
        <tr>
            <td></td>
               <td>Total Usd: <div id="totalus" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/USA.png"><?php echo number_format($subtotal/$cierre->cot_dolar,2,",",".") ?></div></td>
            <td>Total Gs: <div id="total" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Paraguay.png"></i></i> <span id="total_gua"><?php echo number_format(($subtotal), 0, "," , ".") ?></div></td>
            <td>Total Rs: <div id="totalrs" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Brazil.png"></i></i><span id="total_real"><?php echo number_format(($subtotal/$cierre->cot_real), 2, "," , ".") ?></div></td>   
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table> 

<div align="center"><a class="btn btn-lg btn-primary " href="#devolucionModal" class="btn btn-success" data-toggle="modal" data-target="#devolucionModal" data-c="devolucion_ventas">Finalizar (F4)</a></div>

</div> 
</div>
</div>

<?php include("view/devolucion_ventas/finalizar-modal-devolucion.php"); ?>

<script type="text/javascript">


    $('#producto').on('change',function(){
        var id_producto = $(this).val();
        var id_venta = $("#id_venta").val();
        //alert(id_producto);
        var url = "?c=venta&a=ObtenerCantMaxima&id_venta="+id_venta+"&id_producto="+id_producto;
            $.ajax({

                url: url,
                method : "POST",
                data: id_venta,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var producto = JSON.parse(respuesta);
                    console.log(producto);
                    $("#precio_venta").val(producto.precio_venta);
                    $("#precio_venta").html(producto.precio_venta);
                    // $("#descuento").attr("max",producto.descuento);
                    $("#cantidad").attr("max",producto.cantidad_maxima);
                    //alert(producto.cantidad);
                    $("#cantidad").focus();
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
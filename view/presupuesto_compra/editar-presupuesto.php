 
<?php $fecha = date("Y-m-d"); $id_presupuesto = $_GET['id_presupuesto']; ?>
<h1 class="page-header">Editar presupuesto de compra
    <!-- <a class="btn btn-primary" href="#productoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="producto">Nuevo producto</a>  -->
</h1>
<div class="container">
    <div class="row">
        <form method="post" action="?c=presupuesto_compra&a=guardaruno">
            <input type="hidden" name="id_presupuesto" value="<?php echo $id_presupuesto; ?>">
            <div class="col-sm-2">
                <label>Producto </label>
                <select name="id_producto" id="producto" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                        title="-- Seleccione el producto --" autofocus>
                    <?php foreach($this->producto->Listar() as $producto): ?> 
                        <option data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>"><?php echo $producto->producto.' '.$producto->precio_minorista.' ( '.$producto->stock_s1.' )'; ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-1">
                <label>Cantidad</label>
                <input type="number" name="cantidad" class="form-control" id="cantidad" value=""  min="1" step="any" required>   
            </div>
            <div class="col-sm-2">
                <label>Precio de compra</label>
                <input type="float" step="0.01" value="" name="precio_compra" id="precio_compra" class="form-control" min="0" required>
                <input type="submit" name="bton" style="display: none">
            </div>
           
            <div class="col-sm-2">
                <label>P/Minorista</label>
                <input type="float" step="0.01" value="" id="precio_min" name="precio_min" class="form-control" min="0">   
            </div>

            <div class="col-sm-2">
                <label>P/Intermedio</label>
                <input type="float" step="0.01" value=""  name="precio_intermedio" id="precio_intermedio" class="form-control" min="1" required >   
            </div>

            <div class="col-sm-2">
                <label>P/Mayorista</label>
                <input type="float" value="" step="0.01"  id="precio_may" name="precio_may" class="form-control" min="0">   
            </div>
        </form>
    </div>
</div>
<p> </p>
<div class="table-responsive">

    <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

        <thead>
            <tr style="background-color: #000; color:#fff">
                <th>Codigo</th>
                <th>Producto</th>
                <th>P/Minorista</th>
                <th>P/Intermedio</th>
                <th>P/Mayorista</th>
                <th>Costo</th>
                <th>Cantidad</th>
                <th>Total (Gs)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $totalItem = 0;  
        $subtotal=0;
        $cant=0;
        foreach($this->model->Listar($id_presupuesto) as $r): 
            $totalItem = $r->precio_compra*$r->cantidad;
            $subtotal += ($totalItem);
            $cant +=$r->cantidad;?>
            <tr>
                <td><?php echo $r->codigo; ?></td>    
                <td><?php echo $r->producto; ?></td>
                <td><?php echo number_format($r->precio_min, 0, "," , "."); ?></td>
                <td><?php echo number_format($r->precio_intermedio, 0, "," , "."); ?></td>
                <td><?php echo number_format($r->precio_may, 0, "," , "."); ?></td>
                <td>
                    <input type="number" class="precio_c form-control" min="1" id_item="<?php echo $r->id; ?>" id_presupuesto="<?php echo $id_presupuesto; ?>" cantidad="<?php echo $r->cantidad; ?>" codigo="<?php echo $r->id_producto; ?>" value="<?php echo $r->precio_compra;?>">
                </td>

                <td>
                    <input type="number" class="cantidad_p form-control" name="cantidad_item" min="1" id_item="<?php echo $r->id; ?>" id_presupuesto="<?php echo $id_presupuesto; ?>" cantidad_ant="<?php echo $r->cantidad; ?>" codigo="<?php echo $r->id_producto; ?>" stock="<?php echo $r->stock_s1; ?>"  value="<?php echo $r->cantidad; ?>">

                </td>
                <td><div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                    <?php echo number_format( $totalItem, 0, "," , "."); ?></div></td>
                <td>
                    <!-- VER -->
                    <a  class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=presupuesto_compra&a=EliminarItem&id=<?php echo $r->id; ?>">Cancelar</a> 
                </td>
            </tr>
        <?php endforeach; ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Cantidad: <div id="total" style="font-size: 30px"><?php echo number_format($cant,0,",",".") ?></div></td>
                <td>Total Gs: <div id="total" style="font-size: 30px"><?php echo number_format($subtotal,0,",",".") ?></div></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table> 
        <?php if($subtotal>0){ ?>
        <div align="center"><a class="btn btn-lg btn-primary " href="?c=presupuesto_compra" class="btn btn-success">Finalizar</a></div>
        <?php } ?>
</div> 
<!-- </div>
</div> -->

<?php include("view/presupuesto_compra/finalizar-modal.php"); ?>
<?php include("view/crud-modal.php"); ?>

<script type="text/javascript">


    $('#producto').on('change',function(){
        var id = $(this).val();
        var url = "?c=producto&a=Buscar&id="+id;
            $.ajax({

                url: url,
                method : "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var producto = JSON.parse(respuesta);
                    $("#precio_compra").val(producto.precio_costo);
                    $("#precio_min").val(producto.precio_minorista);
                    $("#precio_may").val(producto.precio_mayorista);
                    $("#precio_brasil").val(producto.precio_brasil);
                    $("#precio_intermedio").val(producto.precio_intermedio);
                    $("#cantidad").focus();
                    $('.selectpicker').selectpicker();
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

    $('.cantidad_p').on('change',function(){
        
        var cantidad = $(this).val();
        var id_item = $(this).attr("id_item");
        var cantidad_ant = $(this).attr("cantidad_ant");
        var codigo = $(this).attr("codigo");
        var id_presupuesto = $(this).attr("id_presupuesto");
        var stock = $(this).attr("stock");
        var url = "?c=presupuesto_compra&a=cambiar&cantidad="+cantidad+"&id_item="+id_item+"&id_presupuesto="+id_presupuesto+"&cantidad_ant="+cantidad_ant+"&codigo="+codigo;
        $(this).attr("cantidad_ant", cantidad);
   
            $.ajax({
                url: url,
                method : "POST",
                data: cantidad,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                     window.location.href = "?c=presupuesto_compra&a=editar&id_presupuesto=<?php echo $_GET["id_presupuesto"] ;?>";
                }
            })
    });

    $('.precio_c').on('change',function(){
        var precio_compra = $(this).val();
        var cantidad = $(this).attr("cantidad");
        var id_item = $(this).attr("id_item");
        var id_presupuesto = $(this).attr("id_presupuesto");
        var codigo = $(this).attr("codigo");
        var url = "?c=presupuesto_compra&a=cambiartotal&cantidad="+cantidad+"&id_item="+id_item+"&id_presupuesto="+id_presupuesto+"&cantidad="+cantidad+"&codigo="+codigo+"&precio_compra="+precio_compra;
        $(this).attr("precio_compra", precio_compra);
   
            $.ajax({
                url: url,
                method : "POST",
                data: cantidad,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                     window.location.href = "?c=presupuesto_compra&a=editar&id_presupuesto=<?php echo $_GET["id_presupuesto"] ;?>";
                }
            })
    });

   <?php if(isset($_REQUEST['dupl'])):?>
        Swal.fire({
            icon: 'error',
            customClass: "swal-lg",
            title: 'Producto duplicado',
            text: 'Ya fue cargado el producto en esta compra'
        });
    <?php endif;?>


</script>
 
<?php $fecha = date("Y-m-d"); ?>
<?php $cierre = $this->cierre->ConsultarParaCompra($_SESSION['user_id']); ?>
<h1 class="page-header">Nueva compra <a class="btn btn-primary" href="#productoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="producto">Nuevo producto</a> </h1>


    <div class="row">
        <form method="post" action="?c=compra_tmp&a=guardar">
        <input type="hidden" name="id_presupuesto" value="<?php $id_presupuesto = $_GET['id_presupuesto']; ?>">
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
            <input type="number" name="cantidad" class="form-control" id="cantidad" value="" min="1" required step="any" autofocus>   
        </div>
        <div class="col-sm-2">
            <label>Precio de compra</label>
            <input type="number" step="0.01" value="" name="precio_compra" id="precio_compra" class="form-control" min="1" required >
            
        </div>
        <div class="col-sm-2">
            <label>P/Minorista</label>
            <input type="number" step="0.01" value=""  name="precio_min"  id="precio_min" class="form-control" min="1" required >   
        </div>
        <!-- <div class="col-sm-1">
            <label>P/brasil</label>
            <input type="float" step="0.01" value=""  name="precio_brasil" id="precio_brasil" class="form-control" min="1" required >   
        </div> -->
        <div class="col-sm-2">
            <label>P/Intermedio</label>
            <input type="number" step="0.01" value=""  name="precio_intermedio" id="precio_intermedio" class="form-control" min="1" required >   
        </div>
        <div class="col-sm-2">
            <label>P/Mayorista</label>
             <input type="number" value="" id="precio_may" name="precio_may" class="form-control"  min="1" required >
            <!--<input type="hidden" value="" id="precio_may" name="precio_may" class="form-control" required>     --> 
             <input class="btn btn-primary center-block" style="visibility:hidden;" type="submit" name="bton" value="Confirmar" >
        </div>
    </form>
    </div>
    
<p> </p>
<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap " width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Codigo</th>
            <th>Producto</th>
            <th>P/Minorista</th>
            <!-- <th>P/Brasil</th> -->
            <th>P/Intermedio</th>
            <th>P/Mayorista</th>
            <th>Costo</th>
            <th>Cantidad</th>
            <th>Total (Gs.)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     $cant=0;
     foreach($this->model->Listar() as $r): 
        $totalItem = $r->precio_compra*$r->cantidad;
        $subtotal += ($totalItem);
        $cant += $r->cantidad; ?>
        <tr>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_min, 0, "," , "."); ?></td>
            <!-- <td><?php //echo number_format($r->precio_brasil, 0, "," , "."); ?></td> -->
            <td><?php echo number_format($r->precio_intermedio, 0, "," , "."); ?></td>
            <td><?php echo number_format($r->precio_may, 0, "," , "."); ?></td>
            <td><?php echo number_format($r->precio_compra, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                <?php echo number_format( $totalItem, 0, "," , "."); ?></div></td>
            <td>
                <a  class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=compra_tmp&a=Eliminar&id=<?php echo $r->id; ?>">Cancelar</a>
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
            <td></td>
            <td>Cantidad: <div id="total" style="font-size: 30px"><?php echo number_format($cant,0,",",".") ?></div></td>
            <td>Total: <div id="total" style="font-size: 30px"><?php echo number_format($subtotal,0,",",".") ?></div></td>
        </tr>
    </tbody>
</table> 
<?php if($subtotal>0){ ?>
<div align="center"><a class="btn btn-lg btn-primary " href="#finalizarModal" class="btn btn-success" data-toggle="modal" data-target="#finalizarModal" data-c="compra">Finalizar (F4)</a>
<!-- preguntar -->
<!-- <a class="btn btn-lg btn-danger delete" href="?c=compra_tmp&a=CancelarCompra">Cancelar Todo</a> -->
</div>
<?php } ?>
</div>
</div> 
</div>
</div>

<?php include("view/compra/finalizar-modal.php"); ?>
<?php include("view/crud-modal.php"); ?>

<script type="text/javascript">

    $(document).ready(function() {
            $('input').on('keypress', function(e) {
                if (e.which == 13) {
                    switch ($(this).attr('id')) {
                        case 'cantidad':
                            $('#precio_compra').select();
                            e.preventDefault();
                            break;
                        case 'precio_compra':
                            $('#precio_min').select();
                            e.preventDefault();
                            break;
                        case 'precio_min':
                            $('#precio_intermedio').select();
                            e.preventDefault();
                            break;
                        case 'precio_intermedio':
                            $('#precio_may').select();
                            e.preventDefault();
                            break;
                    }
                }
            });
    });


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
                    $("#precio_intermedio").val(producto.precio_intermedio);
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

    <?php if(isset($_REQUEST['dupl'])):?>
        Swal.fire({
            icon: 'error',
            customClass: "swal-lg",
            title: 'Producto duplicado',
            text: 'Ya fue cargado el producto en esta compra'
        });
    <?php endif;?>

    <?php if(isset($_REQUEST['cant'])):?>
        Swal.fire({
            icon: 'error',
            customClass: "swal-lg",
            title: 'No se guardó el registro',
            text: 'Ingrese una cantidad válida'
        });
    <?php endif;?>



</script>
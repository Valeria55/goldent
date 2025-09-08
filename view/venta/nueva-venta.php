<?php $fecha = date("Y-m-d"); ?>
<?php $cierre = $this->cierre->ObtenerCierre(); ?>

<h1 class="page-header">Nueva venta <a class="btn btn-info " href="#clienteModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="cliente">+Cliente</a>
    <a class="btn btn-lg btn-primary pull-right" href="#cierreModal" class="btn btn-success" data-toggle="modal" data-target="#cierreModal" data-c="venta">Cierre de caja</a>
</h1>

<div class="container">
    <form method="post" id="productoNuevo">
        <div class="row">
            <div class="form-group col-sm-3">
                <label style="color: black">Cambio a Guaraníes <small>(Gs. a Usd. )</small></label>
                <input type="number" id="dolares" name="cot_dolar" value="<?php echo $cierre->cot_dolar_tmp; ?>" class="form-control" min="1">
            </div>

            <div class="form-group col-sm-3">
                <label style="color: black">Cambio a Reales <small>(Gs. a Rs.)</small></label>
                <input type="number" step="any" id="reales" name="cot_real" value="<?php echo $cierre->cot_real_tmp; ?>" class="form-control" min="1">
            </div>
            <!-- <div class="form-group col-sm-3">
                <label style="color: black">Cant. disponible <small>(Central.)</small></label>
                <input type="text" step="any" id="suc_1"  value=" " class="form-control" min="1" readonly>
            </div>
            <div class="form-group col-sm-3">
                <label style="color: black">Cant. disponible <small>(Suc. 2)</small></label>
                <input type="text" step="any" id="suc_2"  value=" " class="form-control" min="1" readonly>
            </div> -->
        </div>
        <div class="row">
             <div class="col-sm-3">
                 <label>Producto</label>
                 <select name="id_producto" id="producto" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el producto --" autofocus required>
                     <?php foreach ($this->producto->Listar() as $producto) : $promo = ($producto->precio_promo > 0) ? " promo = " . number_format($producto->precio_promo, 0, ".", ".") : ""; ?>
                         <option style="font-size: 18px" data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>" <?php echo ($producto->stock_s1 < 1) ? 'disabled' : ''; ?>><?php echo $producto->producto . ' ( ' . $producto->stock_s1 . ' ) - ' . number_format($producto->precio_minorista, 0, ".", ".") . $promo; ?> </option>
                     <?php endforeach; ?>
                 </select>
             </div>
            
            <input type="hidden" name="id_sucursal" class="form-control" id="sucursal" value="1" >
             <div class="col-sm-2">
                 <label>Cantidad</label>
                 <input type="number" name="cantidad" class="form-control" id="cantidad" value="1" step="any" min="0">
             </div>

             <div class="col-sm-3">
                 <label>Precio</label>
                 <select name="precio_venta" class="form-control" id="precio_venta">
                     <option id="precio_minorista" value="precio_minorista"> Precio minorista</option>
                     <option id="precio_mayorista" value="precio_mayorista" > Precio mayorista</option>
                     <option id="precio_intermedio" value="precio_intermedio"> Precio intermedio</option>
                     <option id="ultimo_precio" value="ultimo_precio">Último precio</option>
                 </select>
             </div>
            <div class="col-sm-2">
                 <label>MONTO</label>
                 <input type="number"  class="form-control" id="montos" step="any" value="" required>
                 
             </div>
             <div class="col-sm-2">
                 <label>Descuento</label>
                 <input type="float" name="descuento" class="form-control" id="descuento" value="" required>
                 
             </div>
            <!-- <div class="btn_movil">-->
            <!--    <input class="btn btn-primary center-block" style="visibility:hidden;" type="submit" name="bton" value="Confirmar" >-->
            <!--</div>-->
         </div>
     </form>
 </div>

<p> </p>

<?php include("view/crud-modal.php"); ?>
<div class="table-responsive" id="tabla_items">

    <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

        <thead>
            <tr style="background-color: #000; color:#fff">
                <th>Codigo</th>
                <th>Producto</th>
                <th>Precio por Unidad</th>
                <th>Cantidad</th>
                <th>Descuento </th>
                <th>Total (Gs.)</th> <!-- era USD -->
                <!-- <th>Producto Facturable / cantidad</th> -->
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $subtotal = 0;
            $totalItem = 0;
            foreach ($this->model->Listar() as $r) :
                // echo '<pre>'; var_dump($r); echo '</pre>';
                $totalItem = (($r->precio_venta * $r->cantidad) - ($r->descuento*$r->cantidad));
                $subtotal += ($totalItem);
                $p = $this->producto->Obtener($r->id_producto); ?>
                <!-- <tr class="click" <?php //if ($p->confactura == 0) {
                                        //echo "style='color:red'";
                                    //} ?>> -->
                    <td><?php echo $r->codigo; ?></td>
                    <td><?php echo $r->producto; ?></td>
                    <td><?php echo number_format($r->precio_venta, 0, ",", "."); ?></td>
                    <td><?php echo $r->cantidad; ?></td>
                    <td><?php echo $r->descuento; ?></td>
                    <td>
                        <div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                            <?php echo number_format($totalItem, 0, ",", "."); ?>
                        </div>
                    </td>
                    <!-- <td> -->
                        <!-- <?php
                        //if ($p->confactura == 0) { ?>
                            <div class="form-group col-sm-8">
                                <select name="prod_factura" id="<?php //echo $r->id; ?>" class="form-control selectpicker prod_factura" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el producto --" autofocus required>
                                    <?php //foreach ($this->producto->ListarProdctoFactura() as $producto) : ?>
                                        <option style="font-size: 18px" data-subtext="<?php //echo $producto->codigo; ?>" value="<?php //echo $producto->id; ?>" <?php //echo ($r->prod_factura == $producto->id) ? "selected" : ""; ?>><?php //echo $producto->producto . ' ( ' . $producto->confactura . ' ) - ' . number_format($producto->precio_minorista, 2, ".", ".") . $promo; ?> </option>
                                    <?php //endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <input min="" id="<?php //echo $r->id; ?>" can_factura="<?php //echo $r->can_factura; ?>" name="can_factura" class="form-control can_factura" type="number" value="<?php //echo $r->can_factura; ?>" id="can_factura">
                            </div>
                        <?php// } else {
                        //} ?> -->
                    <!-- </td> -->
                    <td>
                        <a class="btn btn-danger cancelar" id_item="<?php echo $r->id; ?>">Cancelar</a>
                    </td>
                </tr>
                <input type="hidden" id="clienteId" value="<?php echo $r->id_venta; ?>">
            <?php endforeach; ?>
            <?php //echo $subtotal;?>


            <tr>
                <td></td>
                <td></td>
              
                
                <td>Total Gs: <div id="total" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Paraguay.png"></i></i> <span id="total_gua"><?php echo number_format(($subtotal), 0, ",", ".") ?></span></div>
                </td>
                <td>Total USD: <div id="totalus" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/USA.png"><?php echo number_format(($subtotal/$cierre->cot_dolar_tmp), 2, ",", ".") ?></div>
                </td>
                <td>Total Rs: <div id="totalrs" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Brazil.png"></i></i><span id="total_real"> <?php echo number_format(($subtotal / $cierre->cot_real_tmp), 2, ",", ".") ?></span></div>
                </td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    <?php if ($subtotal >= 0) { ?>
        <div align="center">
            <a class="btn btn-lg btn-primary " href="#finalizarModal" class="btn btn-success" data-toggle="modal" data-target="#finalizarModal" data-c="venta">Finalizar (F4)</a>
            <a class="btn btn-lg btn-danger delete" href="?c=venta_tmp&a=CancelarVenta">Cancelar Todo</a>
        </div>
    <?php } ?>

</div>

</div>
</div>
</div>

<?php include("view/venta/finalizar-modal.php"); ?>
<?php include("view/venta/cierre-modal.php"); ?>
<script type="text/javascript">
    // $('.prod_factura').on('change', function() {
    //     var prod_factura = $(this).val();
    //     var id = parseInt($(this).attr("id"));
    //     console.log('id=' + id);
    //     //  alert(id);
    //     var url = "?c=venta_tmp&a=ProductoFactura&id=" + id + "&prod_factura=" + prod_factura;

    //     $.ajax({

    //         url: url,
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         success: function(respuesta) {
    //             //$("#precioTotal"+idItem).html(precio);
    //             //$("#tabla_items").html(respuesta);
    //             //location.reload(true);
    //             //alert(respuesta);
    //         }

    //     })
    // });



    $('.can_factura').on('change', function() {

        var can_factura = $(this).val();
        var id = parseInt($(this).attr("id"));
        var url = "?c=venta_tmp&a=CantidadFactura&id=" + id + "&can_factura=" + can_factura;

        $.ajax({

            url: url,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                //$("#precioTotal"+idItem).html(precio);
                //$("#tabla_items").html(respuesta);
                //location.reload(true);
                //alert(respuesta);
            }

        })
    });

    $('#dolares').on('change', function() {
        // $("#dolares").val(this.val());
        $("#cot_dolar").val($(this).val());
        var total_formatted = ($(this).val() * <?php echo $subtotal; ?>).toLocaleString('en-US', {});

        $("#total_gua").text(total_formatted);
        $("#total_gstabla").text(total_formatted);
        $.ajax({
            method: "POST",
            url: "?c=cierre&a=ActualizarCotizacion",
            data: {
                cot_dolar_tmp: $(this).val(),
            },
            success: function(data) {
                console.log("cotizacion actualizada gs");
            }
        });

    });

    $('#reales').on('change', function() {
        $("#cot_real").val($(this).val());
        var total_real = ($(this).val() * <?php echo $subtotal; ?>).toLocaleString('en-US', {})
        $("#total_real").text(total_real);
        $("#total_rstabla").text(total_real);
        $.ajax({
            method: "POST",
            url: "?c=cierre&a=ActualizarCotizacion",
            data: {
                cot_real_tmp: $(this).val(),
            },
            success: function(data) {
                console.log("cotizacion actualizada real");
            }
        });

    });


    $('.cancelar').on('click', function() {
        var datos = {};
        datos.id = $(this).attr("id_item");
        $.ajax({
            method: "POST",
            url: "?c=venta_tmp&a=eliminar",
            data: datos,
            success: function(data) {
                $("#tabla_items").html(data)
            }
        });
    });

    $('#productoNuevo').submit(function(e) {

        e.preventDefault();
        var datos = $(this).serialize();
        var tipo_venta = $('#precio_venta').val();
        console.log(tipo_venta);
        $.ajax({
            method: "POST",
            url: "?c=venta_tmp&a=guardar&tipo_venta" + tipo_venta,
            data: datos,
            success: function(data) {
                $("#tabla_items").html(data);
                $("#productoNuevo")[0].reset();
                $('#producto').selectpicker('refresh');
                $("#precio_minorista").html("");
                $("#producto").focus();
                $('.selectpicker').selectpicker();
            }
        });
    });

    $('#finalizarModal').on('show.bs.modal', function(event) {
        $("#monto_efectivo").focus();
    })

    $('#producto').on('change', function() {
        var id = $(this).val();
        var url = "?c=producto&a=buscar&id=" + id;
        $.ajax({

            url: url,
            method: "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                var producto = JSON.parse(respuesta);
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();

                today = yyyy + '-' + mm + '-' + dd;
                console.log(producto.precio_promo + '> 0 && ' + producto.desde + '<=' + today + '<=' + producto.hasta);
                if (producto.precio_promo > 0 && producto.desde <= today && producto.hasta >= today) {
                    $("#precio_minorista").val(producto.precio_promo);
                    $("#precio_minorista").html(producto.precio_promo + " (Promo)");
                } else {
                    $("#precio_minorista").val(producto.precio_minorista);
                    $("#precio_minorista").html(producto.precio_minorista+ " (Minorista)");
                }

                //$("#montos").attr("min",producto.precio_turista); // que el precio atacado sea el precio minimo 
                $("#precio_mayorista").val(producto.precio_mayorista);
                $("#precio_mayorista").html(producto.precio_mayorista + " (Mayorista)");
                 $("#precio_intermedio").val(producto.precio_intermedio);
                $("#precio_intermedio").html(producto.precio_intermedio + " (Intermedio)");
                //$("#descuento").attr("max",producto.descuento_max);
                $("#cantidad").attr("max",producto.stock_s1);
                $("#cantidad").select();
                
                if(parseFloat(producto.ultimo_precio) > 0){// solo poder seleccionar si esta cargado el precio
                    $("#ultimo_precio").val(producto.ultimo_precio);
                    $("#ultimo_precio").html(producto.ultimo_precio + " (Último)");
                    $("#ultimo_precio").attr("disabled", false);
                    //puede que el ultimo precio sea menor que el atacado, entonces usar ese como minimo
                    if(producto.ultimo_precio < producto.precio_intermedio){
                        $("#montos").attr("min",producto.ultimo_precio); // que el precio atacado sea el precio minimo
                    }else{
                        $("#montos").attr("min",producto.precio_intermedio); // que el precio atacado sea el precio minimo
                    }
                    
                }else{
                    $("#ultimo_precio").val('');
                    $("#ultimo_precio").html( " (Último)");
                    $("#ultimo_precio").attr("disabled", true);
                    $("#montos").attr("min",producto.precio_intermedio); // que el precio atacado sea el precio minimo
                    
                }
                //$('#productoNuevo').submit();
            }

        })
    });


    $('#cliente').on('change', function() {
        var id = $(this).val();
        var url = "?c=cliente&a=buscar&id=" + id;
        var categoria = "Plata";
        var descuento = 0;
        $.ajax({

            url: url,
            method: "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                var cliente = JSON.parse(respuesta);
                $("#puntos").val(cliente.puntos);
                if (cliente.gastado < 3000000) {
                    categoria = 'Plata';
                    descuento = 5;
                } else if (cliente.gastado >= 3000000 && cliente.gastado < 10000000) {
                    categoria = 'Oro';
                    descuento = 10;
                } else {
                    categoria = 'Platino';
                    descuento = 15;
                }
            }

        })
    });

    /*$('#cantidad').on('change', function() {

        var valor = $(this).val();
        var id = $("#producto").val();
        var url = "?c=producto&a=buscar&id=" + id;

        console.log(id);
        console.log(valor);

        $.ajax({

            url: url,
            method: "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                var producto = JSON.parse(respuesta);
                if (valor >= producto.apartir) {
                    console.log('precio mayorista');
                    $("#precio_venta").val(producto.precio_mayorista);
                    // $("#precio_venta").html(producto.precio_mayorista + " (Mayorista)");     
                }
            }

        })

    });*/

    $('#producto').on('change', function() {

        var valor = $(this).val();
        var id = $("#producto").val();
        var url = "?c=producto&a=buscar&id=" + id;

        console.log(id);
        console.log(valor);

        $.ajax({

            url: url,
            method: "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                var producto = JSON.parse(respuesta);
                // if (valor >= producto.apartir) {
                //     console.log('precio mayorista');
                //     $("#precio_venta").val(producto.precio_mayorista);
                //     // $("#precio_venta").html(producto.precio_mayorista + " (Mayorista)");     
                // }
                $("#suc_1").val(producto.stock_s1);
                $("#suc_2").val(producto.stock_s2);
            }

        })

    });

$('#montos').on('keyup', function() {
    
        var monto=$("#montos").val();
        var venta=$("#precio_venta").val();
        var descuento = venta - monto;
        //alert (total_real);
        
       $('#descuento').val(descuento);

    });
    function calcular() {
        var subtotal = $('#subtotal').val();
        var descuento = $('#descuento').val();
        var iva = $('#iva').val();
        var reales = $('#reales').val();
        var dolares = $('#dolares').val();
        $('#descuentoval').val(descuento);
        $('#ivaval').val(iva);
        if (descuento == 0 && iva == 0) {
            var total = subtotal;
        }
        if (descuento == 0 && iva != 0) {
            var ivac = parseInt(subtotal * (iva / 100));
            var total = parseInt(subtotal) + ivac;
        }
        if (descuento != 0 && iva == 0) {
            var total = subtotal - (subtotal * (descuento / 100));
        }
        if (descuento != 0 && iva != 0) {
            var ivac = parseInt(subtotal * (iva / 100));
            var num = parseInt(subtotal) + ivac;
            var total = num - (subtotal * (descuento / 100));
        }
        var totalus = (total / dolares).toFixed(2);
        var totalrs = (total / reales).toFixed(2);
        var totalc = total.toLocaleString();

        $('#totalus').val(totalus);
        $('#totalrs').val(totalrs);
        $('.totaldesc').val(totalc);
    }



</script>
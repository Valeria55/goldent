    <?php $cierres = $this->cierre->UltimoUsuario($_SESSION['user_id']); ?>
    <div>
        <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

            <thead>
                <tr style="background-color: #000; color:#fff">
                    <th>Codigo</th>
                    <th>Producto</th>
                    <th>Precio por Unidad</th>
                    <th>Cantidad</th>
                    <th>Descuento </th>
                    <th>Total (Gs.)</th>
                    <!-- <th>Producto Facturable / cantidad</th> -->
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                $totalItem = 0;
                foreach ($this->model->Listar() as $r) :
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
                         <td><?php echo number_format($r->descuento * $r->cantidad, 0, ",", "."); ?></td>
                        <td>
                            <div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                                <?php echo number_format($totalItem, 0, ",", "."); ?>
                            </div>
                        </td>
                        <!-- <td>
                            <?php
                            //if ($p->confactura == 0) { ?>
                                <div class="form-group col-sm-8">
                                    <select name="prod_factura" id="<?php //echo $r->id; ?>" class="form-control selectpicker prod_factura" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el producto --" autofocus required>
                                        <?php //foreach ($this->producto->ListarProdctoFactura() as $producto) : ?>
                                            <option style="font-size: 18px" data-subtext="<?php //echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>" <?php //echo ($r->prod_factura == $producto->id) ? "selected" : ""; ?>><?php //echo $producto->producto . ' ( ' . $producto->confactura . ' ) - ' . number_format($producto->precio_minorista, 2, ".", ".") . $promo; ?> </option>
                                        <?php //endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <input min="" id="<?php //echo $r->id; ?>" can_factura="<?php //echo $r->can_factura; ?>" name="can_factura" class="form-control can_factura" type="number" value="<?php //echo $r->can_factura; ?>" id="can_factura">
                                </div>
                            <?php //} else {
                           // } ?>
                        </td> -->
                        <td>
                            <a class="btn btn-danger cancelar" id_item="<?php echo $r->id; ?>">Cancelar</a>
                        </td>
                    </tr>
                    <input type="hidden" id="clienteId" value="<?php echo $r->id_venta; ?>">
                <?php endforeach; ?>


                <tr>
                    <td></td>
                    <td></td>
                   
                    <td>Total Gs: <div id="total" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Paraguay.png"></i></i> <span id="total_gua"><?php echo number_format(($subtotal), 0, ",", ".") ?></span></div>
                    </td>
                     <td>Total USD: <div id="totalus" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/USA.png"><?php echo number_format(($subtotal/ $cierres->cot_dolar_tmp), 2, ",", ".") ?></div>
                    </td>
                    <td>Total Rs: <div id="totalrs" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Brazil.png"></i></i><span id="total_real"> <?php echo number_format(($subtotal / $cierres->cot_real_tmp), 2, ",", ".") ?></span></div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div align="center">
            <a class="btn btn-lg btn-primary " href="#finalizarModal" class="btn btn-success" data-toggle="modal" data-target="#finalizarModal" data-c="venta">Finalizar (F4)</a>
            <a class="btn btn-lg btn-danger delete" href="?c=venta_tmp&a=CancelarVenta">Cancelar Todo</a>
        </div>

    </div>
</div>
</div>
</div>

    <?php // include("view/venta/finalizar-modal.php"); ?>
    <script>
        $('.prod_factura').on('change', function() {

            var prod_factura = $(this).val();
            var id = parseInt($(this).attr("id"));
            // console.log('id=' +id);
            var url = "?c=venta_tmp&a=ProductoFactura&id=" + id + "&prod_factura=" + prod_factura;

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

        $('#finalizarModal').on('show.bs.modal', function(event) {
            $("#finalizar_venta").focus();
        })
    </script>
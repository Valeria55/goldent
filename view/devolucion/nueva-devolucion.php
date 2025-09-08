<?php
$fecha = date("Y-m-d");

?>
 <style>
    @media screen and (min-width: 1000px) {
    .btn_movil {
        display: none;
    }
    }
</style>
<h1 class="page-header"> Nuevo ajuste <a class="btn btn-info" href="#productoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="producto">+ Producto</a></h1>
<div class="container">
    <div class="row">
        <form method="post">
            <input type="hidden" id="id_venta" name="id_venta" value="<?php echo $id_venta ?>">
            <input type="hidden" name="c" value="devolucion_tmp">
            <input type="hidden" name="a" value="guardar">
            <div class="col-sm-3">
                <label>Producto</label>
                <select name="id_producto" id="producto" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" autofocus>
                    <option value="" disabled selected>--Seleccionar producto--</option>
                    <?php foreach($this->producto->Listar() as $producto): ?> 
                    <option style="font-size: 18px" data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>"><?php echo $producto->producto.' ( '.$producto->stock_s1.' ) - '.number_format($producto->precio_minorista,0,".","."); ?> </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" name="" style="display:none;">
            </div>
            <div class="col-sm-3">
                <label>Motivo</label>
                <select name="descuento" id="motivo" class="form-control">
                    <option value="Ajuste">Ajuste</option>
                    <option value="Vencimiento">Vencimiento</option>
                </select>
            </div>
            <div class="col-sm-3">
                <label>Precio</label>
                <select name="precio_venta" class="form-control" id="precio_venta" min="0">
                    <option id="precio_costo" value="" style="display:none"> </option>
                </select>
            </div>
            <div class="col-sm-3">
                <label>Cantidad</label>
                <input type="number" name="cantidad" class="form-control" id="cantidad" value="" step="any" required>
            </div>
            <div class="btn_movil">
                <input class="btn btn-primary center-block" type="submit" name="bton" value="Confirmar">
            </div>
        </form>
    </div>
</div>
<p> </p>
<div class="table-responsive">

    <table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla1">

        <thead>
            <tr style="background-color: #000; color:#fff">
                <th>Cod</th>
                <th>Producto</th>
                <th>Costo por Unidad</th>
                <th>Cantidad</th>
                <th>Motivo</th>
                <th>Total (Gs.)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $subtotal = 0;
            foreach ($this->model->Listar() as $r) :
                $totalItem = $r->precio_venta * $r->cantidad;
                $subtotal += ($totalItem); ?>
                <tr>
                    <td><?php echo $r->codigo; ?></td>
                    <td><?php echo $r->producto; ?></td>
                    <td><?php echo number_format($r->precio_venta, 0, ",", "."); ?></td>
                    <td><?php echo $r->cantidad; ?></td>
                    <td><?php echo $r->descuento; ?></td>
                    <td>
                        <div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                            <?php echo number_format($totalItem, 0, ",", "."); ?></div>
                    </td>
                    <td>
                        <a class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=devolucion_tmp&a=Eliminar&id=<?php echo $r->id; ?>">Cancelar</a>
                    </td>
                </tr>
                <input type="hidden" id="clienteId" value="<?php echo $r->id_venta; ?>">
            <?php endforeach; ?>


            <tr>
                <td></td>
                <td></td>
                <td>Total Gs: <div id="total" style="font-size: 30px"><?php echo number_format($subtotal, 0, ",", ".") ?></div>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
   <?php if($subtotal!=0){ ?>

<div align="center"><input type="button" class="btn btn-primary" value="Finalizar ajuste" data-toggle="modal" data-target="#finalizarModal">

</div>
<?php } ?>
    <!--<?php //if ($subtotal != 0) { ?>-->
    <!--    <form method="post" id="frm-finalizar-ajuste" action="?c=devolucion&a=guardar">-->
    <!--        <div class="form-group" style="display:none">-->
    <!--            <label>En forma de</label>-->
    <!--            <select name="contado" id="contado" class="form-control">-->
    <!--                <option value="Efectivo">Efectivo</option>-->
    <!--                <option value="Credito">Crédito</option>-->
    <!--            </select>-->
    <!--        </div>-->

    <!--        <input type="hidden" name="id_venta" value="<?php echo $id_venta ?>">-->
    <!--        <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">-->
    <!--        <input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">-->
    <!--        <input type="hidden" name="descuentoval" id="descuentoval" value="0">-->
    <!--        <input type="hidden" name="ivaval" id="ivaval" value="0">-->
    <!--        <input type="hidden" name="id_vendedor" value="12">-->
    <!--        <div align="center"><input type="button" onclick="finalizarAjuste()" class="btn btn-primary" value="Finalizar ajuste"></div>-->
    <!--    </form>-->
    <!--<?php //} ?>-->
    <?php include("view/devolucion/finalizar-modal-devolucion.php"); ?>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<script type="text/javascript">
    function finalizarAjuste() {
        var total = $("#total").text();
        Swal.fire({
            title: 'Desea finalizar el ajuste?',
            icon: 'warning',
            html: `<p>Verifique si todos los datos están correctos antes de confirmar</p>
					<table class="table">
						<tbody>
							<tr>
							<th scope="row">Monto total</th>
							<td>${total}</td>
							</tr>
						</tbody>
					</table>
				`,
            showCancelButton: true,
            focusCancel: true,
            customClass: 'swal-lg',
            confirmButtonText: 'Finalizar',
            cancelButtonText: `Cancelar`,
            // timer: 3000, // para cerrar automaticamente
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Registrando ajuste',
                    html: 'Por favor, aguarde...',
                    customClass: 'swal-lg',
                    onOpen: () => {
                        Swal.showLoading()
                        $("#frm-finalizar-ajuste").submit();
                    }
                });
            } else if (result.isDenied) { // cancelado
            }
        })
    }


    $('#producto').on('change', function() {
        var id_producto = $(this).val();
        var id_venta = $("#id_venta").val();
        var url = "?c=producto&a=Buscar&id=" + id_producto;
        $.ajax({

            url: url,
            method: "POST",
            data: id_venta,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                var producto = JSON.parse(respuesta);
                $("#precio_costo").val(producto.precio_costo);
                $("#precio_costo").html(producto.precio_costo);

                $("#cantidad").focus();
            }

        })
    });

    $('#motivo').on('change', function() {
        var motivo = $(this).val();
        if (motivo == "Vencimiento") {
            $("#precio_costo").attr('selected', 'selected');

        } else {

            $("#precio_costo").attr('selected', 'selected');
        }
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
        var totalrs = (total / reales).toFixed(2);
        var totalus = (total / dolares).toFixed(2);
        var totalc = total.toLocaleString();

        $('.totaldesc').val(totalc);
        $('#totalrs').val(totalrs);
        $('#totalus').val(totalus);
    }
</script>
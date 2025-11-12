<?php
$fecha = date("Y-m-d");
?>
<h1 class="page-header"> Nuevo ajuste </h1>
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
                    <?php foreach ($this->producto->Listar() as $producto): ?>
                        <option style="font-size: 18px" data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>"><?php echo $producto->producto . ' ( ' . $producto->stock . ' ) - ' . number_format($producto->precio_minorista, 0, ".", "."); ?> </option>
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
                <label>Precio Costo</label>
                <input type="number" name="precio_venta" class="form-control" id="precio_costo" min="0" step="any">
            </div>
            <div class="col-sm-3">
                <label>Cantidad</label>
                <input type="number" name="cantidad" class="form-control" id="cantidad" value="-1" step="any">
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
            foreach ($this->model->Listar() as $r):
                $totalItem = $r->precio_venta * $r->cantidad;
                $subtotal += ($totalItem); ?>
                <tr>

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
                <td>Total Gs: <div id="total" style="font-size: 30px"><?php echo number_format($subtotal, 0, ",", ".") ?></div>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <?php if ($subtotal != 0) { ?>
        <!-- Botón para abrir el modal -->
        <div align="center">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAjuste">Finalizar ajuste</button>
        </div>

        <!-- Modal Bootstrap -->
        <div class="modal fade" id="modalAjuste" tabindex="-1" role="dialog" aria-labelledby="modalAjusteLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="?c=devolucion&a=guardar" id="formAjuste">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAjusteLabel">Finalizar ajuste</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="observacion">Observación <span style="color:red">*</span></label>
                                <textarea class="form-control" id="observacion" name="observacion" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="venta">Seleccionar venta</label>
                                <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                    title="-- Seleccione una venta --" autofocus required id="venta" name="venta">
                                    <option value="" disabled selected>--Seleccionar venta--</option>
                                    <?php foreach ($this->venta->ListarVenta() as $venta): ?>
                                        <option value="<?php echo $venta->id_venta; ?>">
                                            Venta #<?php echo $venta->id_venta; ?> - <?php echo $venta->cliente; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_user">Seleccionar Funcionario</label>
                                <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                    title="-- Seleccione una venta --" autofocus required id="id_user" name="id_user">
                                    <option value="" disabled selected>--Seleccionar funcionario--</option>
                                    <?php foreach ($this->usuario->Listar() as $u): ?>
                                        <option value="<?php echo $u->id; ?>">
                                            <?php echo $u->user; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
                            <input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
                            <input type="hidden" name="descuentoval" id="descuentoval" value="0">
                            <input type="hidden" name="ivaval" id="ivaval" value="0">
                            <input type="hidden" name="id_vendedor" value="12">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar ajuste</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
</div>
</div>

<script type="text/javascript">
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

    // Validación del modal
    $("#formAjuste").on("submit", function(e) {
        var obs = $("#observacion").val().trim();
        var venta = $("#venta").val();
        if (obs === "" || venta === null) {
            alert("La observación es obligatoria y debe seleccionar una venta.");
            e.preventDefault();
        }
    });
</script>
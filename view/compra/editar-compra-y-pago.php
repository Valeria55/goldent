<?php
if (!isset($_SESSION)) session_start();
$id_compra = isset($_GET['id_compra']) ? $_GET['id_compra'] : 0;
$productos = $this->compra_tmp->Listar($id_compra);
?>
<h1 class="page-header">Editar Compra y Pago</h1>
<a class="btn btn-default" href="?c=compra">&laquo; Volver a compras</a>
<br><br>
<div class="container">
    <div class="row">
        <form method="post" action="?c=compra_tmp&a=guardar&return=compra&return_action=editarCompraYPago&id_compra=<?php echo $id_compra; ?>">
            <input type="hidden" name="id_compra" value="<?php echo $id_compra; ?>">
            <div class="col-sm-4">
                <label>Producto </label>
                <select name="id_producto" id="producto" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                        title="-- Seleccione el producto --" autofocus>
                    <?php foreach($this->producto->Listar() as $producto): ?> 
                    <option data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>"><?php echo $producto->producto.' '.$producto->precio_minorista.' ( '.$producto->stock.' )'; ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <label>Cantidad</label>
                <input type="number" name="cantidad" class="form-control" id="cantidad" value="" min="1" required step="any">   
            </div>
            <div class="col-sm-2">
                <label>Precio de compra</label>
                <input type="number" value="0" name="precio_compra" id="precio_compra" class="form-control" min="0">
                <input type="submit" name="bton" style="display: none">
            </div>
            <div class="col-sm-2">
                <label>Precio de venta</label>
                <input type="number" value="0" id="precio_min" name="precio_min" class="form-control" min="0">   
            </div>
            <div class="col-sm-2" style="display:none">
                <label>Mayorista</label>
                <input type="number" value="0" id="precio_may" name="precio_may" class="form-control" min="0">   
            </div>
        </form>
    </div>
</div>
<p> </p>
<?php if ($id_compra && count($productos) > 0): ?>
<div class="table-responsive">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Compra</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $p): ?>
        <tr>
            <td><?php echo htmlspecialchars($p->producto); ?></td>
            <td><?php echo htmlspecialchars($p->cantidad); ?></td>
            <td><?php echo number_format($p->precio_compra, 0, '.', ','); ?></td>
            <td><?php echo number_format($p->precio_compra * $p->cantidad, 0, '.', ','); ?></td>
            <td>
                <a class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=compra_tmp&a=Eliminar&id=<?php echo $p->id; ?>&return=compra&return_action=editarCompraYPago&id_compra=<?php echo $id_compra; ?>">Cancelar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-right"><strong>Total General:</strong></td>
            <td>
                <?php 
                $total_general = 0;
                foreach ($productos as $p) {
                    $total_general += ($p->precio_compra * $p->cantidad);
                }
                echo number_format($total_general, 0, '.', ','); 
                ?>
            </td>
            <td></td>
        </tr>
    </tfoot>
</table>
</div>

<?php if ($id_compra && count($productos) > 0): ?>
<div class="row">
    <div class="col-sm-12 text-center" style="margin-top: 20px;">
        <button type="button" class="btn btn-lg btn-success" id="btn-finalizar-edicion" data-toggle="modal" data-target="#finalizar-step-edicion">
            Finalizar Edición
        </button>
    </div>
</div>
<?php endif; ?>

<?php else: ?>
    <div class="alert alert-warning">No se encontraron productos para esta compra.</div>
<?php endif; ?>
<script>
    // Función para obtener precios vía AJAX según el enfoque de nueva-compra.php
    $('#producto').on('change', function() {
        var id = $(this).val();
        var url = "?c=producto&a=Buscar&id=" + id;
        $.ajax({
            url: url,
            method: "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                console.log("Respuesta recibida:", respuesta); // Para depuración
                var producto = JSON.parse(respuesta);
                $("#precio_compra").val(producto.precio_costo);
                $("#precio_min").val(producto.precio_minorista);
                $("#precio_may").val(producto.precio_mayorista);
                $("#cantidad").focus();
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", status, error);
            }
        });
    });
</script>

<!-- Modal para finalizar edición -->
<div id="finalizar-step-edicion" class="modal fade bd-example-modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body" id="modal-detalles-edicion">
                <!-- El contenido se cargará vía AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
    // Manejar el evento de mostrar el modal para finalizar edición
    $('#finalizar-step-edicion').on('show.bs.modal', function (event) {
        var id_compra = <?php echo $id_compra; ?>;
        var url = "?c=compra&a=finalizar_edicion&id_compra=" + id_compra;
        
        $.ajax({
            url: url,
            method: "GET",
            success: function(respuesta) {
                $("#modal-detalles-edicion").html(respuesta);
                // Inicializar selectpickers después de cargar el contenido AJAX
                $('.selectpicker').selectpicker();
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar el modal:", status, error);
                alert("Error al cargar el formulario de finalización");
            }
        });
    });

    // Limpiar el modal cuando se cierra
    $('#finalizar-step-edicion').on('hidden.bs.modal', function (event) {
        $("#modal-detalles-edicion").html('');
    });
</script>

<!-- Incluir el modal de step para funcionalidades adicionales si es necesario -->
<?php include('view/compra/step-modal.php'); ?>

<h1 class="page-header">Lista de acreedores </h1>
<a class="btn btn-primary pull-right" href="#acreedorModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="acreedor">Agregar</a>
<br><br><br>
<div class="table-responsive">
    <table class="table table-striped table-bordered display responsive nowrap datatable">

        <thead>
            <tr style="background-color: black; color:#fff">
                <th></th>
                <th>Cliente</th>
                <th>Concepto</th>
                <th>Monto</th>
                <th>Saldo</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->model->Listar() as $r): ?>
                <tr class="click">
                    <td>
                        <div align="center"><a class="btn btn-primary " href="#pagarModal" class="btn btn-success" data-toggle="modal" data-target="#pagarModal" data-id="<?php echo $r->id; ?>">Pagar</a></div>
                    </td>
                    <td><a class="btn btn-default" href="?c=acreedor&a=clientepdf&id=<?php echo $r->id_cliente; ?>&cli=<?php echo $r->nombre; ?>"><?php echo $r->nombre; ?></a>
                    </td>
                    <td><?php echo $r->concepto . '<b> N° </b>' . $r->nro_comprobante; ?></td>
                    <td><?php echo number_format($r->monto, 0, ",", "."); ?></td>
                    <td><?php echo number_format($r->saldo, 0, ",", "."); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                    <?php if ($r->id_compra): ?>
                        <td>
                            <a href="#pagosModal" class="btn btn-success" data-toggle="modal" data-target="#pagosModal" data-id="<?php echo $r->id; ?>">Pagos</a>
                            <a href="#detallesCompraModal" class="btn btn-warning" data-toggle="modal" data-target="#detallesCompraModal" data-id="<?php echo $r->id_compra; ?>">Compra</a>
                        </td>
                    <?php else: ?>
                        <td>
                            <a href="#pagosModal" class="btn btn-success" data-toggle="modal" data-target="#pagosModal" data-id="<?php echo $r->id; ?>">Pagos</a>
                            <a class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id; ?>" data-c="acreedor">Editar</a>
                            <a class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=acreedor&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
                        </td>
                    <?php endif ?>
                </tr>



            <?php endforeach; ?>

        </tbody>
    </table>
</div>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/acreedor/pagar-modal.php"); ?>
<?php include("view/compra/detalles-modal.php"); ?>
<?php include("view/acreedor/pagos-modal.php"); ?>

<script type="text/javascript">
    $('#pagarModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id');
        if (id > 0) {
            var url = "?c=acreedor&a=pagarModal&id=" + id;
        } else {
            var url = "?c=acreedor&a=pagar";
        }
        $.ajax({

            url: url,
            method: "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                $("#modal-body").html(respuesta);
            }

        })
    })

    $('#pagosModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id');
        if (id > 0) {
            var url = "?c=acreedor&a=PagosModal&id=" + id;
            $.ajax({
                url: url,
                method: "GET",
                cache: false,
                success: function(respuesta) {
                    $("#egreso-detalles").html(respuesta);
                }
            });
        }
    })
</script>
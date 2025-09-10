<!--<a class="btn btn-primary" href="#diaModal" class="btn btn-primary" data-toggle="modal" data-target="#diaModal">Informe diario</a>
<a class="btn btn-primary" href="#mesModal" class="btn btn-primary" data-toggle="modal" data-target="#mesModal">Informe Mensual</a>
<a class="btn btn-primary pull-right" href="?c=devolucion_tmp" class="btn btn-success">Nueva devolución</a>-->
<h1 class="page-header">Lista de Devoluciones de Compras</h1>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Usuario</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Fecha y Hora</th>
            <?php if (!isset($_GET['id_compra'])) : ?>
                <th></th>
            <?php endif ?>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $suma = 0;
        $count = 0;
        $id_compra = (isset($_REQUEST['id_compra'])) ? $_REQUEST['id_compra'] : 0;
        $suma = 0;
        $count = 0;
        foreach ($this->model->Listar($id_compra) as $r) : ?>
            <tr class="click" <?php if ($r->anulado) {
                                    echo "style='color:gray'";
                                } ?>>
                <td><?php echo $r->id_compra; ?></td>
                <td><?php echo $r->vendedor; ?></td>
                <td><?php echo $r->nombre_cli; ?></td>
                <td><?php echo number_format($r->total, 0, ".", ","); ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_compra)); ?></td>
                <?php if (!isset($_GET['id_compra'])) : ?>
                    <td>

                        <a href="#devolucionCompraModal" class="btn btn-success" data-toggle="modal" data-target="#devolucionCompraModal" data-c='devolucion_compras' data-id="<?php echo $r->id_compra; ?>">Ver</a>

                        <!--<a  class="btn btn-primary edit" href="?c=compra_tmp&a=editar&id=<?php //echo $r->id_compra 
                                                                                                ?>" class="btn btn-success" >Editar</a>-->
                        <?php if ($r->anulado) : ?>
                            ANULADO
                        <?php else : ?>
                            <a href='#detallesModal' class='btn btn-info' data-toggle='modal' data-target='#detallesCompraModal' data-c='compra' data-id="<?php echo $r->id_compra; ?>">Detalles de Compra</a>
                            <?php if(false):?>
                                <a class="btn btn-primary pull-right" href="?c=compra_tmp&id_devolucion=<?php echo $r->id ?>" class="btn btn-success">Nueva compra</a>
                            <a class="btn btn-danger delete" href="?c=devolucion_compras&a=Anular&id=<?php echo $r->id ?>&id_compra=<?php echo $r->id_compra ?>" class="btn btn-success">ANULAR</a>
                            <?php endif;?>
                        <?php endif ?>
                    </td>
                <?php endif ?>
                <td><a href="#devolucionCompraModal" class="btn btn-info" data-toggle="modal" data-target="#devolucionCompraModal" data-id="<?php echo $r->id_compra; ?>">Detalle Devolucion</td>
            </tr>
        <?php
            $count++;
        endforeach; ?>
    </tbody>

</table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/devolucion_compras/detalles-modal.php"); ?>
<?php include("view/compra/detalles-modal.php"); ?>

<script>
    $('#devolucionCompraModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id');
        var url = "?c=devolucion_compras&a=detalles&id_compra=" + id;
        $.ajax({

            url: url,
            method: "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                $("#devolucion-detalles").html(respuesta);

            }

        })
    })

</script>
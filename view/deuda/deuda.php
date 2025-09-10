<h1 class="page-header">Lista de deudas </h1>
<ul id="tab-list" class="nav nav-tabs">
    <li id="deudores-tab" class="active"><a href="#">Deudores</a></li>
    <li id="pagados-tab" class=""><a href="#">Pagados</a></li>
</ul>


<!--
  *************************************************************************
  *                                                                       *
  *   TABLA PARA DEUDORES *
  *                                                                       *
  *************************************************************************
-->
<div id="deudores-content"> 
<table  id="deudores-table" class="table table-striped table-bordered display responsive nowrap datatable">
    <a class="btn btn-primary pull-right" href="#deudaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="deuda">Agregar</a>
    <br><br><br>

    <thead>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th>Cliente</th>
            <th>Concepto</th>
            <th>Monto</th>
            <th>Saldo</th>
            <th>Fecha</th>
            <th>Vencimiento</th>
            <th></th>
        </tr>
    </thead>

    <!-- tbody para Deudores -->
    <tbody>
        <?php 
            $suma = 0; $saldo = 0;
        foreach ($this->model->Listar() as $r) : ?>
            <tr class="click">
                <td>
                    <div align="center"><a class="btn btn-primary " href="#cobrarModal" class="btn btn-success" data-toggle="modal" data-target="#cobrarModal" data-id="<?php echo $r->id; ?>">Cobrar</a></div>
                </td>
                <!-- ... resto de las columnas de la tabla para Deudores ... -->
                <td><a class="btn btn-default" href="#rangoModal" class="btn btn-success" data-toggle="modal" data-target="#rangoModal" data-id="<?php echo $r->id_cliente; ?>"><?php echo $r->nombre; ?></a>
                </td>
                <td><?php echo $r->concepto; ?></td>
                <td style="padding-right:2px" align="right"><?php echo number_format($r->monto, 0, ",", ","); ?></td>
                <td style="padding-right:2px" align="right"><?php echo number_format($r->saldo, 0, ",", ","); ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                <td><?php echo (date("Y", strtotime($r->vencimiento)) > 2000) ? date("d/m/Y", strtotime($r->vencimiento)) : ""; ?></td>
                <?php if ($r->id_venta) : ?>
                    <td>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id; ?>">Cobros</a>
                        <a href="#detallesModal" class="btn btn-default" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta; ?>">Venta</a>
                    </td>
                <?php else : ?>
                    <td>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id; ?>">Cobros</a>
                        <a class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id; ?>" data-c="deuda">Editar</a>
                        <!-- <a class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=deuda&a=Eliminar&id=<?php //echo $r->id; ?>">Eliminar</a> -->
                    </td>
                <?php endif ?>
            </tr>
        <?php 
            $suma += $r->monto; $saldo += $r->saldo;
        endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #666666; color:#fff" >
            <td></td>
            <td></td>
            <td style="padding-right:2px" align="right">TOTAL: </td>
            <td style="padding-right:2px" align="right"><?php echo number_format($suma,0,".",","); ?></td>
            <td  style="padding-right:2px" align="right"><?php echo number_format($saldo,0,".",","); ?></td>
            <td></td>
            <td></td>
            <td></td>
    </tfoot>
</table>

</div>
<!--
  *************************************************************************
  *                                                                       *
  *   TABLA PAGADOS *
  *                                                                       *
  *************************************************************************
-->
<div id="pagados-content" style="display: none;"> 
<table id="pagados-table" class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Cliente</th>
            <th>Concepto</th>
            <th>Monto</th>
            <th>Saldo</th>
            <th>Fecha</th>
            <th>Vencimiento</th>
            <th></th>
        </tr>
    </thead>

    <!-- tbody para Pagados -->
    <tbody>
        <?php 
        $suma = 0;
        foreach ($this->model->ListarSaldados() as $s) : ?>
            <tr class="click">
                <td><a class="btn btn-default" href="#rangoModal" class="btn btn-success" data-toggle="modal" data-target="#rangoModal" data-id="<?php echo $s->id_cliente; ?>"><?php echo $s->nombre; ?></a>
                </td>
                <td><?php echo $s->concepto; ?></td>
                <td style="padding-right:3px" align="right"><?php echo number_format($s->monto, 0, ",", ","); ?></td>
                <td style="padding-right:3px" align="right"><?php echo number_format($s->saldo, 0, ",", ","); ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($s->fecha)); ?></td>
                <td><?php echo (date("Y", strtotime($r->vencimiento)) > 2000) ? date("d/m/Y", strtotime($r->vencimiento)) : ""; ?></td>
                <?php if ($s->id_venta) : ?>
                    <td>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $s->id; ?>">Cobros</a>
                        <a href="#detallesModal" class="btn btn-default" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $s->id_venta; ?>">Venta</a>
                    </td>
                <?php else : ?>
                    <td>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $s->id; ?>">Cobros</a>
                    </td>
                <?php endif ?>
            </tr>
        <?php 
            $suma += $s->monto;
        endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #666666; color:#fff" >
            <td></td>
            <td></td>
            <td style="padding-right:3px" align="right">TOTAL: <?php echo number_format($suma,0,".",","); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
    </tfoot> <?php  ?>

</table>
</div>

</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/deuda/cobrar-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>
<?php include("view/deuda/cobros-modal.php"); ?>
<?php include("view/deuda/rango-modal.php"); ?>

<script type="text/javascript">
    $('#cobrarModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id');
        if (id > 0) {
            var url = "?c=deuda&a=cobrarModal&id=" + id;
        } else {
            var url = "?c=deuda&a=cobrar";
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
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // Maneja el evento de clic en las pestañas
        $('#tab-list li').click(function() {
            // Remueve la clase active de todas las pestañas
            $('#tab-list li').removeClass('active');
            // Agrega la clase active a la pestaña clickeada
            $(this).addClass('active');

            if ($(this).attr('id') === 'deudores-tab') {
                $('#deudores-content').show();
                $('#pagados-content').hide();
                $('#deudores-table').show();
                $('#pagados-table').hide();
            } else {
                $('#deudores-content').hide();
                $('#pagados-content').show();
                $('#deudores-table').hide();
                $('#pagados-table').show();
            }
        });
    });
</script>
<h1 class="page-header">Lista de deudas </h1>
<a class="btn btn-primary pull-right" href="#deudaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="deuda">Agregar</a>
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
    <?php foreach($this->model->Listar() as $r): ?>
        <tr class="click">
            <td>
                <div align="center"><a class="btn btn-primary " href="#cobrarModal" class="btn btn-success" data-toggle="modal" data-target="#cobrarModal" data-id="<?php echo $r->id;?>">Cobrar</a></div>
                            </td>
        	<td><a href="?c=deuda&a=clientepdf&id=<?php echo $r->id_cliente; ?>&cli=<?php echo $r->nombre; ?>"><?php echo $r->nombre; ?></a></td>
            <td><?php echo $r->concepto; ?></td>
            <td><?php echo number_format($r->monto,0,",","."); ?></td>
            <td><?php echo number_format($r->saldo,0,",","."); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <?php if ($r->id_venta): ?>
            <td>
                <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id; ?>">Cobros</a>
                <a href="#detallesModal" class="btn btn-default" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta; ?>">Venta</a>
            </td>
            <?php else: ?>
            <td>
                <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id; ?>">Cobros</a>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="deuda">Editar</a>
                <a  class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=deuda&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
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
<?php include("view/deuda/cobrar-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>
<?php include("view/deuda/cobros-modal.php"); ?>

<script type="text/javascript">
    $('#cobrarModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id');
        if(id>0){
            var url = "?c=deuda&a=cobrarModal&id="+id;
        }else{
            var url = "?c=deuda&a=cobrar";
        }
        $.ajax({

            url: url,
            method : "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success:function(respuesta){
                $("#modal-body").html(respuesta);
            }

        })
    })
</script>


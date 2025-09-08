<h1 class="page-header">Lista de deudas </h1>
<a class="btn btn-primary pull-right" href="#deudaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="deuda">Agregar</a>
<br><br><br>
<div class="table-responsive">
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>N° de comprobante</th>
            <th>Monto</th> 
            <th>Forma de pago</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $lista = (isset($_POST['desde']))? $this->model->Listar_rango($_POST['desde'],$_POST['hasta']):$this->model->MiLista();
    
    foreach($lista as $r): 
    if(strlen($r->concepto)>=50){$concepto=substr($r->concepto, 0, 50)."...";}else{$concepto=$r->concepto;} ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";}?>>
            <td><?php echo $r->nombre; ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->categoria; ?></td>
            <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo number_format($r->monto,0,".",","); ?></td>
            <td><?php echo $r->forma_pago; ?></td>
            <td>
                <?php if (!$r->anulado): ?>
                    <?php if ($r->id_venta): ?>
                    <a href="#detallesModal" class="btn btn-warning" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta; ?>">Venta</a>
                        <?php if ($r->id_deuda): ?>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id_deuda; ?>">Cobros</a>
                        <a  class="btn btn-danger delete" href="?c=ingreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"><i class="fas fa-trash-alt"></i></a>
                        <?php endif ?>
                    <?php elseif($r->id_deuda): ?>
                    <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id_deuda; ?>">Cobros</a>
                    <a  class="btn btn-danger delete" href="?c=ingreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"><i class="fas fa-trash-alt"></i></a>
                    <?php else: ?>
                    <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="ingreso">Editar</a>
                    <a  class="btn btn-danger delete" href="?c=ingreso&a=Eliminar&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"><i class="fas fa-trash-alt"></i></a>
                    <?php endif ?>
                <?php else: ?>
                    ANULADO
                <?php endif ?>
            </td>    
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


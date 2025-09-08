<h1 class="page-header">Lista de transferencias</h1>
<a class="btn btn-primary pull-right" href="#transferenciaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="transferencia">Agregar</a>
<a class="btn btn-success pull-right" href="?c=transferencia&a=varios">Agregar Varios</a>
<br><br><br>

<p> </p>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Emisor</th>
        	<th>Receptor</th>
            <th>Local emisor</th>
            <th>Tipo</th> 
            <th>Local receptor</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Fecha solicitada</th>
            <th>Fecha aceptada</th>
            <th>Observación</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 

    $lista = $this->model->Listar();
    session_start();
    foreach($lista as $r): 
        if (($r->local_receptor == $_SESSION['sucursal'] || $r->local_emisor == $_SESSION['sucursal']) && $r->observacion != "VARIOSPROD") {
        ?>
        <tr class="click" <?php if ($r->estado=="Aceptado" || $r->estado=="Rechazado" || $r->estado=="Cancelado"){echo "style='color:gray'";}?>>
            <td><?php echo $r->emisor; ?></td>
        	<td><?php echo $r->receptor; ?></td>
            <td><?php echo $r->suc_emisor; ?></td>
            <td><?php echo $r->tipo; ?></td>
            <td><?php echo $r->suc_receptor; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo date("d/m/Y", strtotime($r->fecha_solicitada)); ?></td>
            <td><?php echo (date("Y", strtotime($r->fecha_aceptada))>2000)? date("d/m/Y", strtotime($r->fecha_aceptada)):""; ?></td>
            <td><?php echo $r->observacion; ?></td>
            <td><?php echo $r->estado; ?></td>
            <td>
                <?php if ($r->estado=="Aceptado" || $r->estado=="Rechazado" || $r->estado=="Cancelado"): ?>
                <?php echo $r->estado; ?>
                <?php else: ?>
                    <?php if ($r->usuario_emisor==$_SESSION['user_id']): ?>
                        <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="transferencia">Editar</a>
                        <a  class="btn btn-danger delete" href="?c=transferencia&a=Cancelar&id=<?php echo $r->id; ?>&estado=Cancelado">Cancelar</a>
                    <?php else: ?>    
                        <a  class="btn btn-primary" href="?c=transferencia&a=Aceptar&id=<?php echo $r->id; ?>&receptor=<?php echo $_SESSION['user_id'] ?>">Aceptar</a>
                        <a  class="btn btn-danger delete" href="?c=transferencia&a=Cancelar&id=<?php echo $r->id; ?>&estado=Rechazado">Rechazar</a>
                    <?php endif ?>
                <?php endif ?>
            </td>    
        </tr>
    <?php } endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/compra/detalles-modal.php"); ?>
<?php include("view/acreedor/pagos-modal.php"); ?>

<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
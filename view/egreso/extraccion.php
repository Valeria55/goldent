<h1 class="page-header">Lista de extracciones</h1>
<a class="btn btn-primary pull-right" href="#egresoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="egreso">Agregar</a>
<a class="btn btn-primary pull-right" href="#transferenciaModal" class="btn btn-success" data-toggle="modal" data-target="#transferenciaModal">Transferir</a>
<br><br><br>

<p> </p>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
        	<th>Fecha</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>N° de comprobante</th>
            <th>Monto</th> 
            <th>Forma de pago</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 
    
    foreach($this->model->ListarSesion($_SESSION['user_id']) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->categoria; ?></td>
            <td><?php echo $r->concepto; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo number_format($r->monto,0,".",","); ?></td>
            <td><?php echo $r->forma_pago; ?></td>
            <td>
                <?php if (!$r->anulado): ?>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="egreso">Editar</a>
                <a  class="btn btn-danger delete" href="?c=egreso&a=Anular&id=<?php echo $r->id; ?>">Anular</a>
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
<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>
<?php include("view/deuda/cobros-modal.php"); ?>
<?php include("view/caja/transferencias.php"); ?>

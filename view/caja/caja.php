<h1 class="page-header">Lista de cajas </h1>
<a class="btn btn-primary pull-right" href="#cajaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="caja">Agregar</a>
<a class="btn btn-primary pull-right" href="#transferenciaModal" class="btn btn-success" data-toggle="modal" data-target="#transferenciaModal">Transferir</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Entidad</th>
            <th>Encargado</th>
            <th>Fecha</th>
            <th>Monto Apertura</th>
            <th>Disponible</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r):?>
        
        <tr class="click" <?php echo ($r->anulado==1)? "style='background-color:gray'":"";?>>
            <td><a href="?c=caja&a=movimientos&id_caja=<?php echo $r->id; ?>"><?php echo $r->caja; ?></a></td>
            <td><?php echo $r->usuario; ?></td>
            <td><?php echo date("d/m/Y",strtotime($r->fecha)); ?></td>
            <td><?php echo number_format($r->monto,0,".",","); ?></td>
            <td><?php echo number_format(($r->ingresos-$r->egresos),0,".",","); ?></td>
            <td>
                <?php if ($r->anulado==1): ?>
                ANULADO
                <?php else: ?>    
                <a  class="btn btn-danger delete" href="?c=caja&a=Anular&id=<?php echo $r->id; ?>">Anular</a>
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
<?php include("view/caja/transferencias.php"); ?>


<h1 class="page-header">Lista de métodos de pago</h1>
<a class="btn btn-primary pull-right" href="#imagenModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="metodo">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Método</th>
            <th>Ingresos</th>
            <th>Egresos</th>
            <th>TOTAL</th>  
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 
        $totalin = 0;
        $totalegre = 0; 
        $total = 0; 
    foreach($this->model->Listar() as $r): ?>
        <tr class="click">
            <td><a class="btn btn-default" href="?c=metodo&a=movimientos&metodo=<?php echo $r->metodo; ?>"><?php echo $r->metodo; ?></a></td>
            <td><?php echo number_format($r->ingresos); ?></td>
            <td><?php echo number_format($r->egresos); ?></td>
            <td><?php echo number_format(($r->ingresos-$r->egresos)); ?></td>
            <td>
                <?php if(1 == 2):?>
                    <a class="btn btn-danger delete" href="?c=metodo&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
                <?php endif;?>
            </td>
        </tr>
        <?php $totalin +=$r->ingresos; ?>
        <?php $totalegre +=$r->egresos; ?>
        <?php $total +=($r->ingresos-$r->egresos); ?>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff">

            <th></th>
            <th>  <?php echo number_format($totalin); ?> </th>
            <th> <?php echo number_format($totalegre); ?></th>
            <th> <?php echo number_format($total); ?></th> 
            <th></th>

        </tr>
</tfoot>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


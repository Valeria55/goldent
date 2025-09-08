<h1 class="page-header">Lista de métodos de pago</h1>
<a class="btn btn-primary pull-right" href="#imagenModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="metodo">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Método</th>
            <th>Ingresos</th>
            <th>Egresos </th>
            <th>Saldo </th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar2() as $r):
    // echo '<pre>'; var_dump($r); echo '</pre>';
        $suma_ingresos += $r->ingresos;
        $suma_egresos += $r->egresos;?>
        
        <tr class="click">
            <td><a class="btn btn-default" href="?c=metodo&a=movimientos&metodo=<?php echo $r->metodo; ?>"><?php echo $r->metodo; ?></a></td>
            <td><?php echo number_format($r->ingresos, 0, ".", ","); ?></td>
            <td><?php echo number_format($r->egresos, 0, "." , ","); ?></td>
            <td><?php echo number_format($r->ingresos-$r->egresos, 0, "." , ","); ?></td>
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="metodo">Editar</a>
                <a class="btn btn-danger delete" href="?c=metodo&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #cccccc;">
            <th>TOTAL:</th>
            <th><?php echo number_format($suma_ingresos, 0, "," , ".");?></th>
            <th><?php echo number_format($suma_egresos, 0, "," , ".") ;?></th>
            <th></th>
            <th></th>
        </tr>


    </tfoot>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


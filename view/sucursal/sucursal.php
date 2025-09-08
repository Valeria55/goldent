<h1 class="page-header">Lista de sucursales </h1>
<a class="btn btn-primary pull-right" href="#sucursalModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="sucursal">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Cód.</th>
            <th>sucursal</th>        
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r): ?>
        
        <tr class="click">
            <td><?php echo $r->id; ?></td>
            <td><?php echo $r->sucursal; ?></td>
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="sucursal">Editar</a>
            </td>
            <td>
                <a  class="btn btn-danger delete" href="?c=sucursal&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table> 
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>


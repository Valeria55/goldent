<h1 class="page-header">Lista de marcas </h1>
<a class="btn btn-primary pull-right" href="#marcaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="marca">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Cód.</th>
            <th>marca</th>        
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r): ?>
        
        <tr class="click">
            <td><?php echo $r->id; ?></td>
            <td><?php echo $r->marca; ?></td>
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="marca">Editar</a>
            </td>
            <td>
                <a  class="btn btn-danger delete" href="?c=marca&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


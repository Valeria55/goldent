<h1 class="page-header">Lista de imagenes de <?php echo $_GET['prod'] ?></h1>
<a class="btn btn-primary pull-right" href="#imagenModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="imagen">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Id Producto</th>
            <th>Imagen</th>        
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar($_GET['id_prod']) as $r): ?>
        <tr class="click">
            <td><?php echo $r->id_producto; ?></td>
            <td><img src="assets/img/<?php echo $r->imagen; ?>" width="100px"></td>
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="imagen">Editar</a>
            </td>
            <td>
                <a  class="btn btn-danger delete" href="?c=imagen&a=Eliminar&id=<?php echo $r->id; ?>&id_producto=<?php echo $_GET['id_prod'] ?>&prod=<?php echo $_GET['prod'] ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


<h1 class="page-header">Lista de vendedores </h1>
<a class="btn btn-primary pull-right" href="#vendedorModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="vendedor">Agregar</a>
<br><br><br>
<div class="table-responsive">
<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">

    <thead>
        <tr style="background-color: black; color:#fff">
        	<th>Nombre</th>
            <th>Porcentaje</th>         
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r): ?>
        <tr class="click">
            <td><?php echo $r->nombre; ?></td>
            <td><?php echo $r->porcentaje; ?></td>
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="vendedor">Editar</a>
            </td>
            <td>
                <a  class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=vendedor&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table> 
</div> 
</div>
</div>
<?php include("view/crud-modal.php"); ?>


<h1 class="page-header">Lista de categorias </h1>
<a class="btn btn-primary pull-right" href="#categoriaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="categoria">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Cód.</th>
            <th>Sub Categoría de</th>
            <th>Nombre de la categoría</th>        
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r): ?>
        <?php
        if($r->id_padre==0){
            $padre="Principal";
        }else{
            $categoria = $this->model->Obtener($r->id_padre);
            $padre = $categoria->categoria;
        } ?>
        <tr class="click">
            <td><?php echo $r->id; ?></td>
            <td><?php echo $padre; ?></td>
            <td><?php echo $r->categoria; ?></td>
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="categoria">Editar</a>
            </td>
            <td>
                <a  class="btn btn-danger delete" href="?c=categoria&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


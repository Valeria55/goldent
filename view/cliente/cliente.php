<h1 class="page-header">Lista de personas</h1>
<a class="btn btn-primary pull-right" href="#clienteModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="cliente">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>CI/RUC</th>
            <th>Nombre y Apellido</th>
            <th>Teléfono</th>
            <th>Direccion</th>
            <th>Mayorista</th>
            <th>Registro</th>
            <th>Fecha de Nac.</th>
            
            <th></th>
            <?php if($_SESSION['nivel']<=1){ ?>
            <th></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->model->Listar() as $r):
        if ($r->gastado < 3000000) {
            $categoria = "Plata";
        }elseif ($r->gastado >= 3000000 && $r->gastado < 10000000) {
            $categoria = "Oro";
        }else{
            $categoria = "Platino";
        }
    ?>
        <tr class="click">
            <td><?php echo $r->ruc; ?></td>
            <td><a href="?c=venta&a=listarcliente&id_cliente=<?php echo $r->id; ?>"><?php echo $r->nombre; ?></a></td>
            <td><?php echo $r->telefono; ?></td>
            <td><?php echo $r->direccion; ?></td>
            <td><?php echo $r->mayorista; ?></td>
            <td><?php echo  date("d/m/Y",strtotime($r->fecha_registro)); ?></td>
            <td><?php echo  date("d/m/Y",strtotime($r->cumple)); ?></td>
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id; ?>" data-c="cliente">Editar</a>
            </td>
            <?php if($_SESSION['nivel']<=1){ ?>
            
            <td>
                <a  class="btn btn-danger delete" href="?c=cliente&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
            </td>
            <?php } ?>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
</div>
</div>
</div>
<?php include "view/crud-modal.php";?>


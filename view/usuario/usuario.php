<h1 class="page-header">Lista de usuarios </h1>
<a class="btn btn-primary pull-right" href="#usuarioModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="usuario">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Usuario</th>
            <th>Contraseña</th>
            <th>Rol</th>
            <th>Comisión (%)</th>
            <th>Sucursal</th>           
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r): ?>
        <?php 
            if($r->nivel==1){
                $nivel = 'Administrador';
            }elseif($r->nivel==2){
                $nivel = 'Cajero';
            }elseif($r->nivel==4){
                $nivel = 'Gerente';
            }else{
                $nivel = 'Vendedor';
            }
        ?>
        <tr class="click">
            <td><a href="?c=venta&a=listarusuario&id_usuario=<?php echo $r->id; ?>&mes=<?php echo date("Y-m"); ?>"><?php echo $r->user; ?></a></td>
            <td>............</td>
            <td><?php echo $nivel; ?></td>
            <td><?php echo $r->comision; ?></td>
            <td><?php echo $r->sucursal; ?></td>
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="usuario">Editar</a>
            </td>
            <td>
                <a  class="btn btn-danger delete" href="?c=usuario&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


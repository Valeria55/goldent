<h1 class="page-header">Informe del Inventario </h1>
<?php if($_SESSION['nivel']<=1) ?>
<p> </p>
<table class="table table-striped table-bordered display responsive datatable" width="100%">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Usuario</th>
            <th>Fecha de Apertura</th> 
            <th>Fecha de Cierre</th>    
            <th>Motivo</th>    
            <th></th> 
        </tr>
    </thead>
    <tbody>
    <?php 
$lista = $this->model->Listar();
    foreach($lista as $c):  ?>
        <tr >
            <td><?php echo $c->usuario; ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($c->fecha_apertura)); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($c->fecha_cierre)); ?></td>
            <td><?php echo $c->motivo; ?></td>
            <td align="center">
                <a class="btn btn-primary" href="?c=inventario&a=FechaApertura&fecha=<?php echo date("Y-m-d",strtotime($c->fecha_apertura))?>">Detalles</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>




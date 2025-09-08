<h1 class="page-header">Lista de Cuentas </h1>
<a class="btn btn-primary pull-right" href="#cuentaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="cuenta">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Cod. Cliente</th>
            <th>Fecha emitida</th>
            <th>Fecha pagada</th>
            <th>Comprobante</th>
            <th>Número de comprobante</th>
            <th>Monto</th>
            <th>Saldo</th>
            <th>Estado</th>       
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r): ?>
        <tr class="click">
            <td><?php echo $r->id_cliente; ?></td>
            <td><?php echo date("d/m/Y", strtotime($r->fecha_emitida)); ?></td>
            <td><?php echo date("d/m/Y", strtotime($r->fecha_pagada)); ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo $r->nro_comprobante; ?></td>
            <td><?php echo number_format($r->monto,0,",","."); ?></td>
            <td><?php echo number_format($r->saldo,0,",","."); ?></td>
            <td><?php echo $r->estado; ?></td>
            
            <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="cuenta">Editar</a>
            </td>
            <td>
                <a  class="btn btn-danger delete" href="?c=cuenta&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


<h1 class="page-header">
    Detalles Historial
</h1>


<?php $fecha = date("Y-m-d"); ?>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla1">


    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Cód.</th>
            <th>Proveedor</th>
            <th>Monto</th>
            <th>Mes</th>
            <th>Comprobante</th>
            <th>Usuario</th> 
        </tr>
    </thead>
    <tbody>
        <?php 
        $id = $_GET['id'];
        foreach($this->gastos_fijos->ListarDetalles($id) as $r):?>
            <tr>
                <td><?php echo $r->id ?></td>
                <td><?php echo $r->nombre ?></td>
                <td><?php echo number_format($r->monto, 0, "," , "."); ?></td>
                <td><?php echo $r->fecha_gasto_fijo ?></td>
                <td><?php echo $r->comprobante ?></td>
                <td><?php echo $r->user ?></td>
            </tr>
       <?php endforeach; ?>
    </tbody>
</table> 
</div> 
</div>
</div>

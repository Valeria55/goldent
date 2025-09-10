<h1 class="page-header">Lista de compras &nbsp;</h1>
<a class="btn btn-primary" href="#diaModal" class="btn btn-primary" data-toggle="modal" data-target="#diaModal">Informe Diario</a>
<a class="btn btn-primary" href="#mesModal" class="btn btn-primary" data-toggle="modal" data-target="#mesModal">Informe Mensual</a>
<a class="btn btn-primary pull-right" href="?c=compra_tmp" class="btn btn-success">Nueva compra</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <?php if (isset($_REQUEST['id_compra'])): ?>
            <th>Producto</th>    
            <?php endif ?>
            <th>ID</th>
            <th>Proveedor</th>
            <th>Comprobante</th>
            <th>Nro. comprobante</th>
            <th>Método</th>
            <th>Pago</th>
            <th>Total</th>
            <th>Fecha y Hora</th>
            <th></th>
            <?php if (!isset($_SESSION)) session_start(); if (!isset($_GET['id_compra']) && (($_SESSION['nivel']==1) || ($_SESSION['nivel'] == 4))): ?>        
                <th></th>
            <?php endif ?>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0;  
    $id_compra = (isset($_REQUEST['id_compra']))? $_REQUEST['id_compra']:0;
    $suma = 0; $count = 0;  
    foreach($this->model->Listar($id_compra) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
            <?php if (isset($_REQUEST['id_compra'])): ?>
            <td><?php echo $r->producto; ?></td>    
            <?php endif ?>
            <td><?php echo $r->id_compra; ?></td>
            <td><?php echo $r->nombre_cli; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo $r->nro_comprobante; ?></td>
            <td><?php echo $r->metodo; ?></td>
            <td><?php echo $r->contado; ?></td>
            <td><?php echo number_format($r->total,0,".",","); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_compra)); ?></td>
            <td>
                <a href="#detallesCompraModal" class="btn btn-success" data-toggle="modal" data-target="#detallesCompraModal" data-id="<?php echo $r->id_compra;?>">Ver</a>
            </td>
            <?php if (!isset($_SESSION)) session_start(); if (!isset($_GET['id_compra']) && (($_SESSION['nivel']==1) || ($_SESSION['nivel'] == 4))): ?>
            <td>
               
                <!--<a  class="btn btn-primary edit" href="?c=compra_tmp&a=editar&id=<?php //echo $r->id_compra ?>" class="btn btn-success" >Editar</a>-->
                <?php if ($r->anulado): ?>
                ANULADO    
                <?php else: ?>
                <a  class="btn btn-warning" href="?c=compra&a=editarCompraYPago&id_compra=<?php echo $r->id_compra ?>" class="btn btn-success">Editar</a>
                <a  class="btn btn-danger delete" href="?c=compra&a=anular&id=<?php echo $r->id_compra ?>" class="btn btn-success">ANULAR</a>
                <a href="?c=devolucion_tmpcompras&id_compra=<?php echo $r->id_compra ?>" class="btn btn-primary">Devolución</a>
                <?php endif ?>
            </td>
            <?php endif ?>
            <?php $devolucion = $this->model->ObtenerDevolucion($r->id_compra)->existe_registro; ?>
                <!-- <td><a href="#devolucionCompraModal" class="btn btn-info" data-toggle="modal" data-target="#devolucionCompraModal" data-id="// echo $r->id_compra; ?>">Detalle Devolucion</td> -->
        </tr>
    <?php 
        $count++;
    endforeach; ?>
    </tbody>
    
</table>

</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/compra/mes-modal.php"); ?>
<?php include("view/compra/dia-modal.php"); ?>
<?php include("view/compra/detalles-modal.php"); ?>
<?php include("view/devolucion_compras/detalles-modal.php"); ?>

<script type='text/javascript'>
    window.onload = function() {
        // Asumiendo que estás pasando un parámetro GET 'error'
        var error = new URLSearchParams(window.location.search).get('error');
        if (error === 'cliente') {
            Swal.fire({
                title: '¡RUC NO EXISTE!',
                text: 'DEBE REGISTRAR AL CLIENTE EN EL SISTEMA DE CENTRO DE INSTALACIONES CON EL RUC CORRESPONDIENTE',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
</script>
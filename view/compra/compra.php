<h1 class="page-header">Lista de compras &nbsp;</h1>
 <?php if($_SESSION['nivel']==1){ ?>
<a class="btn btn-primary" href="#diaModal" class="btn btn-primary" data-toggle="modal" data-target="#diaModal">Informe Diario</a>
<a class="btn btn-primary" href="#mesModal" class="btn btn-primary" data-toggle="modal" data-target="#mesModal">Informe Mensual</a>
 <?php } ?>
<a class="btn btn-primary pull-right" href="?c=compra_tmp" class="btn btn-success">Nueva compra</a>
<br><br><br>
<h3 id="filtrar" align="center">Filtro<i class="fas fa-angle-down"></i><i class="fas fa-angle-up"></i></h3>
<div align="center" class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="post" action="">
                <div class="form-group col-md-4">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : ''; ?>" class="form-control">
                </div>
                
                <div class="form-group col-md-4">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : ''; ?>" class="form-control">
                </div>
                
                <div class="form-group col-md-4">
                    <label>Proveedor</label>
                    <select name="nombre" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el Proveedor --" style="width:100%; display:0">
                        <?php foreach ($this->cliente->Listar() as $clie) : ?>
                            <option value="<?php echo $clie->id; ?>"><?php echo $clie->nombre; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-12">
                    <label></label>
                    <center><input type="submit" name="filtro" value="Filtrar" class="btn btn-success"></center>
                </div>
            </form>
        </div>
    </div>
</div>
<p> </p>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <?php if (isset($_REQUEST['id_compra'])): ?>
            <th>Producto</th>    
            <?php endif ?>
            <th>ID</th>
            <th>Comprador</th>
            <th>Proveedor</th>
            <th>Comprobante</th>
            <th>Nro. comprobante</th>
            <th>Método</th>
            <th>Moneda</th>
            <th>Pago</th>
            <th>Total</th>
            <th>Fecha y Hora</th>
            <?php if (!isset($_GET['id_compra'])): ?>        
            <th></th>
            <?php endif ?>
    </thead>
    <tbody>
    <?php
    $desde = isset($_POST['desde']) ? $_POST['desde'] : '';
    $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : '';
    $id_proveedor = isset($_POST['nombre']) ? $_POST['nombre'] : 0;
    //$id_compra = (isset($_REQUEST['id_compra']))? $_REQUEST['id_compra']:0;
    $id_compra = 0;
// var_dump($id_proveedor);
        if (isset($desde) || isset($hasta) || isset($id_proveedor)) {
            $listar = $this->model->ListarFiltro($id_compra, $desde, $hasta,$id_proveedor);
            
        } else {
            $listar = $this->model->Listar($id_compra);
            
       
        }
        // if (empty($id_proveedor)){

        //     $listar = $this->model->Listar($id_compra);
        // } else {
        //     $listar = $this->model->ListarFiltro($id_compra, $desde, $hasta, $id_proveedor);
        // }
        ?>
    <?php 
    $suma = 0; $count = 0;  
    
    $suma = 0; $count = 0;  
    foreach ($listar as $r) : 
    $totals +=$r->total;?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
            <?php if (isset($_REQUEST['id_compra'])): ?>
            <td><?php echo $r->producto; ?></td>    
            <?php endif ?>
            <td><?php echo $r->id_compra; ?></td>
            <td><?php echo $r->vendedor; ?></td>  
            <td><?php echo ($r->nombre_cli == NULL) ? "SIN PROVEEDOR" : $r->nombre_cli; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo ($r->comprobante == "Sin comprobante") ? "Sin N° de comprobante" :  ' N° ' . $r->nro_comprobante; ?></td>
            <td><?php echo $r->metodo; ?></td>
            <td><?php echo $r->moneda; ?></td>
            <td><?php echo $r->contado; ?></td>
            <td><?php echo number_format($r->total,0,".",","); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_compra)); ?></td>
            <?php if (!isset($_GET['id_compra'])): ?>
            <td>
                <a href="#detallesCompraModal" class="btn btn-success" data-toggle="modal" data-target="#detallesCompraModal" data-id="<?php echo $r->id_compra;?>">Ver</a>
                <!--<a  class="btn btn-primary edit" href="?c=compra_tmp&a=editar&id=<?php //echo $r->id_compra ?>" class="btn btn-success" >Editar</a>-->
                <?php if ($r->anulado): ?>
                ANULADO    
                <?php else: ?>
                <a  class="btn btn-warning" href="?c=compra&a=editar&id_compra=<?php echo $r->id_compra ?>" class="btn btn-success">Editar</a>
                 <?php if($_SESSION['nivel']==1){ ?>
                <a  class="btn btn-danger delete" href="?c=compra&a=anular&id=<?php echo $r->id_compra ?>" class="btn btn-success">ANULAR</a>
                 <?php } ?>
                <?php endif ?>
            </td>
            <?php endif ?>
        </tr>
    <?php 
        $count++;
    endforeach; ?>
    </tbody>
     <tfoot>
        <tr style="background-color: black; color:#fff">
            <?php if (isset($_REQUEST['id_compra'])): ?>
            <td></td>    
            <?php endif ?>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></th>
            <td></th>
            <td></td>
            <td><?php echo number_format($totals,0,",","."); ?></td>
            <td></td>
            <?php if (!isset($_GET['id_compra'])): ?>        
            <td></td>
            <?php endif ?>
    </tfoot>
    
</table>

</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/compra/mes-modal.php"); ?>
<?php include("view/compra/dia-modal.php"); ?>
<?php include("view/compra/detalles-modal.php"); ?>
<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>


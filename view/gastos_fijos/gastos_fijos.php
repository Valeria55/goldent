
<h1 class="page-header">Gastos Fijos <button class="btn btn-primary" id="cuentasapagarPDF">Cuentas a Pagar PDF</button> </h1>

<h3 id="filtrar" align="center">Filtro<i class="fas fa-angle-down"></i><i class="fas fa-angle-up"></i></h3>
<div align="center" class="row">
    <div class="col-sm-8">
        <div align="center" id="filtro">
            <form method="post" action="">
            <div class="form-group col-md-6">
                </div>
                <div class="form-group col-md-6">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-6">
                </div>
                <div class="form-group col-md-6">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-6">
                </div>
                <div class="form-group col-md-6">
                    <label></label>
                    <center><input type="submit" name="filtro" value="Filtrar" class="btn btn-success"></center>
                </div>
            </form>
        </div>
    </div>
</div>
<p> </p>

<a class="btn btn-primary pull-right" href="#gastos-fijosModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="gastos_fijos">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Cód.</th>
            <th>Descripción</th>      
            <th>Monto</th>   
            <th>Fecha</th>     
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    
    <tbody>
    <?php
    $desde = isset($_POST['desde']) ? $_POST['desde'] : '';
    $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : '';

        if (empty($desde) || empty($hasta)) {
            $listar = $this->model->Listar();
        } else {
            $listar = $this->model->ListarFiltro($desde, $hasta);
        }
        ?>
    <?php 
    $totalMonto = 0;
    foreach ($listar as $r) : ?>
        <tr class="click">
            <td><?php echo $r->id; ?></td>
            <td><?php echo $r->descripcion; ?></td>
            <td><?php echo number_format($r->monto, 0,",", "."); ?></td>
            <td><?php echo $r->fecha === '0000-00-00' ? '' : date('d/m/y', strtotime($r->fecha)); ?></td>
            <td>
                <?php if ($r->anulado==NULL){?>

                    <a href="#detallesGastos" class="btn btn-info" data-toggle="modal" data-target="#detallesGastos" data-id="<?php echo $r->id;?>">Ver</a>
                    <?php }else{?>
                        ANULADO
                        <?php }?>
            </td>
            <td>
                <?php if ($r->anulado==NULL){?>

                    <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="gastos_fijos">Editar</a>
                    <?php }else{?>
                        ANULADO
                        <?php }?>
            </td>
            <td>
                <?php if ($r->anulado==NULL){?>
                <a  class="btn btn-danger delete" href="?c=gastos_fijos&a=Eliminar&id=<?php echo $r->id; ?>">Eliminar</a>
                 <?php }else{?>
                    ANULADO
                    <?php }?>
            </td>


            <?php if ($r->anulado == NULL) {
                $totalMonto += $r->monto; // Suma el monto al total
            }?>

        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #000; color:#fff">
            <th></th>
            <th><b>Total: </b></th>      
            <th> <b><?php echo number_format($totalMonto, 0, ".", ","); ?></th>   
            <th></th>     
            <th></th>
            <th></th>
            <th></th>
        </tr>
       
       
    </tfoot>
</table>
</div>
</div>
</div> 

<?php include("view/crud-modal.php"); ?>
<?php include("view/gastos_fijos/detalles-modal.php");?>

<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>

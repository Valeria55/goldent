
<h1 class="page-header">Lista de cierres de caja en efectivo <a class="btn btn-primary" href="#cajaModal" class="btn btn-primary" data-toggle="modal" data-target="#cajaModal">Informe de caja</a></h1>
<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up" style="display: none"></i></h3>
<div class="container">
  <div class="row">
    <div class="col">
        <div align="center" id="filtro">
            <form method="get" class="form-inline">
                <input type="hidden" name="c" value="cierre">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="datetime-local" name="desde" value="<?php echo (isset($_GET['desde']))? $_GET['desde']:''; ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="datetime-local" name="hasta" value="<?php echo (isset($_GET['hasta']))? $_GET['hasta']:''; ?>" class="form-control" required>
                </div>
                <input type="submit" name="filtro" value="Filtrar" class="btn btn-success"> 
            </form>
        </div>
    </div>
  </div>
</div>
<p> </p>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Usuario</th>
        	<th>Inicio</th>
            <th>Fin</th>
            <th>Diferencia(GS)</th>
            <th>Diferencia(RS)</th>
            <th>Diferencia(USD)</th>
            <?php session_start(); if($_SESSION['nivel']==1){?>
            <th></th>
            <?php }?>
        </tr>
    </thead>
    <tbody>
    <?php 
    $sumaSistema = 0;
    $sumaCierre = 0;
    $desde = (isset($_GET["desde"]))? $_GET["desde"]:0;
    $hasta = (isset($_GET["hasta"]))? $_GET["hasta"]:0;
    foreach($this->model->Listar($desde, $hasta) as $r): ?>
        <tr class="click">
            <td><?php echo $r->user; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha_apertura)); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_cierre)); ?></td>
            <td><?php echo number_format(($r->monto_cierre-($r->ingreso_gs+$r->monto_apertura-$r->egreso_gs)),0,".",","); ?></td>
            <!-- Diferencia RS -->
            <td><?php echo number_format(($r->monto_reales-($r->ingreso_rs+$r->apertura_rs-$r->egreso_rs)),2,".",","); ?></td>
            <!-- Diferencia USD -->
            <td><?php echo number_format(($r->monto_dolares-($r->ingreso_usd+$r->apertura_usd-$r->egreso_usd)),2,".",","); ?></td>
             <?php session_start(); if($_SESSION['nivel']==1){?>
           <td>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="cierre">Editar</a>
                <!--<a href="?c=cierre&a=detalles&id=<?php //echo $r->id; ?>" class="btn btn-warning">Ver detalles</a>-->
                <!--<a href="?c=cierre&a=cierrepdf&id_cierre=<?php //echo $r->id; ?>" class="btn btn-info">Informe</a>-->
            </td>  
             <?php }?>
        </tr>
    <?php 

    $sumaCierre += ($r->monto_cierre-($r->ingreso_gs+$r->monto_apertura-$r->egreso_gs));
    $sumaCierreRS +=($r->monto_reales-($r->ingreso_rs+$r->apertura_rs-$r->egreso_rs));
    $sumaCierreUSD += ($r->monto_dolares-($r->ingreso_usd+$r->apertura_usd-$r->egreso_usd));
    
    $sumaSistemags = $sumaCierre;
    $sumaSistemars =$sumaCierreRS;
    $sumaSistemausd = $sumaCierreUSD;
    endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff">
            <th></th>
        	<th></th>
            <th></th>
            <th><?php echo number_format($sumaSistemags,0,".",","); ?></th>
            <th><?php echo number_format($sumaSistemars,0,".",","); ?></th>
            <th><?php echo number_format($sumaSistemausd,0,".",","); ?></th>
             <?php session_start(); if($_SESSION['nivel']==1){?>
            <th></th>
              <?php }?>
        </tr>
    </tfoot>
</table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/cierre/rango-modal.php"); ?>
<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
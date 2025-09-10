<?php
$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$fecha = date('Y-m-d');
?>

<h1 align="center">Movimientos</h1>
<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up" style="display: none"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro" style="display: none;">
            <form method="post" action="?c=metodo&a=movimientos&metodo=<?php echo $_REQUEST['metodo']?>">
                <div class="form-group col-md-3">
                    <label>Desde</label>
                
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>Año</label>
                    <select name="anho" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control">
                        <option value=0> Todos</option>
                        <?php for ($i = 2019; $i <= date("Y"); $i++) { ?>
                            <option value="<?php echo $i ?>" <?php if (isset($_GET['anho']) && $_GET['anho'] == ($i)) echo 'selected' ?>><?php echo $i ?></option>
                        <?php } ?>
                    </select>
                </div>
               
                <div class="form-group col-md-2">
                    <label></label>
                    <input type="submit" name="filtro" value="Filtrar" class="btn btn-success">
                </div>

            </form>
        </div>
    </div>
</div>
<p> </p>
<table class="table table-striped display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>N°</th>
        	<th>Fecha</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>N° de comprobante</th>
            <th>Ingreso</th>
            <th>Egreso</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sumaTotal = 0;
    $sumaIngre = 0; 
    $sumaEgre = 0; 
    $c=1;
    foreach($this->model->ListarMovimientosFecha(($_POST['desde']??''), ($_POST['hasta']??''), ($_POST['anho']??''), ($_GET['metodo'])) as $r):
            if(strlen($r->concepto)>=50){$concepto=substr($r->concepto, 0, 50)."...";}else{$concepto=$r->concepto;}
            if($r->monto>0){
                $ingreso = number_format($r->monto,0,".",",");
                $sumaIngre += $r->monto;
                $egreso = "";
            }else{
                $ingreso = "";
                $egreso = number_format(($r->monto*-1),0,".",",");
                $sumaEgre += $r->monto*-1; 
            } ?>
                <tr class="click" <?php if($r->anulado){echo "style='color:red'";}elseif($r->descuento>0){echo "style='color:#F39C12'";} ?>>
                    <td><?php echo $c++; ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                    <td><?php echo $r->categoria; ?></td>
                    <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
                    <td><?php echo $r->comprobante; ?></td>
                    <td><?php echo $ingreso; ?></td>
                    <td><?php echo $egreso; ?></td>
                </tr>
            <?php 
            $sumaTotal += $r->monto; 
    

    endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total:</th>
            <th><?php echo number_format($sumaIngre,0,".",","); ?></th> 
            <th><?php echo number_format($sumaEgre,0,".",","); ?></th> 
            
        </tr>
    </tfoot>
</table>
</div>
</div>
</div>

<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
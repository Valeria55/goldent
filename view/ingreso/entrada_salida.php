<h1 class="page-header">Movimientos de  <?php $persona=$this->cliente->Obtener($_GET['id_persona']); echo $persona->nombre ?></h1>
<br><br><br>

<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up" style="display: none"></i></h3>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <div align="center" id="filtro" style="display: none;">
            <form method="post">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde']))? $_GET['desde']:''; ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta']))? $_GET['hasta']:''; ?>" class="form-control" required>
                </div>
                <input type="submit" name="filtro" value="Filtrar" class="btn btn-success"> 
            </form>
        </div>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div>
<p> </p>

<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" border-collapse: collapse;>

    <thead>
        <tr style="background-color: black; color:#fff">
        	<th>Fecha</th>
        	<th>Usuario</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>Comprobante</th>
            <th>GS</th> 
            <th>USD</th> 
            <th>RS</th> 
            <th>Moneda</th> 
            <th>Cambio</th>
            <th>Forma de pago</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $fecha=date('Y-m-d');
    $ingreso=0;
    $egreso=0;
    $total_Gs=0;
    $total_RS=0;
    $total_US=0;
   
    $lista = $this->model->ListarEntradaSalida($_POST['desde'],$_POST['hasta'], $_GET['id_persona']);
    
    foreach($lista as $r): 
        $ingreso +=$r->ingreso/$r->cambio;
        $egreso +=$r->egreso/$r->cambio;

        if(strlen($r->concepto)>=50){$concepto=substr($r->concepto, 0, 50)."...";}else {$concepto=$r->concepto;} 
         $monto_gs=0;
         $monto_rs=0;
         $monto_us=0;
        if (($r->moneda == 'GS') && ($r->anulado == NULL)){
            $monto_gs = $r->monto;
            $total_Gs += $r->monto;
        }else if (($r->moneda == 'RS') && ($r->anulado == NULL)){
            $monto_rs = $r->monto;
            $total_RS += ($r->monto);
        }else if (($r->moneda == 'USD') && ($r->anulado == NULL)){
            $monto_us = $r->monto;
            $total_US += ($r->monto);
        }
        
        ?>
        <tr class="click" 
        <?php if($r->anulado){echo "style='color:gray'";}?>
        <?php if($r->monto<0){echo "style='color:red'";}?>>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
        	<td><?php echo $r->user; ?></td>
            <td><?php echo $r->categoria.' '.$r->id_presupuesto; ?></td>
            <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
            <td><?php echo $r->comprobante.' N° '.$r->nro_comprobante; ?></td>
            <td><?php echo number_format($monto_gs,0,".",","); ?></td>
            <td><?php echo number_format($monto_us,0,".",","); ?></td>
            <td><?php echo number_format($monto_rs,0,".",","); ?></td>
            <td><?php echo $r->moneda; ?></td>
            <td><?php echo $r->cambio; ?></td>
            <td><?php echo $r->forma_pago; ?></td>
            <td>
                <?php if ($r->monto>0) {?>
                 <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="ingreso">Editar</a></td>
                <?php }else{?>
                <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="egreso">Editar</a></td>
                <?php }?>
           
        </tr>
    
    <?php endforeach; ?>
    
    </tbody>
    <tfoot>
               
                <tr   style="background-color: black; color:#fff">
                    
                    <td></td>
                    <td></td>  
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b><?php  echo number_format($total_Gs,0,".",",");  ?></b></td>
                    <td><b><?php  echo number_format($total_US,0,".",","); ; ?></b></td>
                    <td><b><?php  echo number_format($total_RS,0,".",",");?></b></td> 
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                 <tr   style="background-color: black; color:#fff">
                    
                    <td></td>
                    <td></td>  
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total US.</b></td> 
                    <td><b><?php  echo number_format($ingreso+$egreso,0,".",","); echo ' USD'; ?></b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <!--<tr  style="background-color: #cccccc;">-->
                    
                <!--    <td></td>-->
                <!--    <td></td>  -->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--    <td><b>Total USD.</b></td> -->
                <!--    <td><b><?php // echo number_format($total_US,2,".",","); echo ' USD'; ?></b></td>-->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--</tr>-->
                <!--<tr  style="background-color: #dddddd;">-->
              
                <!--    <td></td>-->
                <!--    <td></td> -->
                <!--    <td></td> -->
                <!--    <td></td>-->
                <!--    <td><b>Total Rs.</b></td> -->
                <!--    <td><b><?php  //echo number_format($total_RS,2,".",","); echo ' RS'; ?></b></td>-->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--</tr>-->
                <!--<tr  style="background-color: #cccccc;">-->
             
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--    <td><b>Total Gs.</b></td>-->
                <!--    <td><b><?php  //echo number_format($total_Gs,0,".",","); echo ' GS.'; ?></b></td> -->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--    <td></td>-->
                <!--</tr>-->
                
    </tfoot>
</table>

</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>

<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
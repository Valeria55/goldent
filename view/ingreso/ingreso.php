<h1 class="page-header">Lista de ingresos </h1>
<a class="btn btn-primary pull-right" href="#ingresoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="ingreso">Agregar</a>
<br><br><br>

<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="post" >
                <div class="form-group col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : ''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : ''; ?>" class="form-control">
                </div>
                 <div class="form-group  col-md-2">
                        <label>Categoria</label>
                        <select name="categoria" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                    title="-- Seleccione la categoria --" style="width:100%; display:0">
                            <?php foreach($this->model->ListarCategoria() as $c): ?> 
                            <option value="<?php echo $c->categoria; ?>"><?php echo $c->categoria; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                     <div class="form-group col-md-2">
                        <label>Nombre</label>
                        <select name="persona"  class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                    title="-- Seleccione a la persona --" style="width:100%; display:0">
                            <?php foreach($this->cliente->Listar() as $clie): ?> 
                            <option value="<?php echo $clie->id; ?>"><?php echo $clie->nombre; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Forma de pago</label>
                        <select name="metodo"  class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control"
                                    title="-- Seleccione a la persona --" style="width:100%; display:0" >
                            <?php foreach ($this->metodo->Listar() as $m): ?>
                                <option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
                            <?php endforeach; ?>
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
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" border-collapse: collapse;>

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Usuario</th>
        	<th>Fecha</th>
            <th>Persona</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>Comprobante</th>
            <th>Monto</th> 
            <th>Moneda</th> 
            <th>Cambio</th>
            <th>Forma de pago</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $fecha=date('Y-m-d');
    //$lista = (isset($_POST['desde']))? $this->model->Listar_rango($_POST['desde'],$_POST['hasta'],$_POST['categoria'],$_POST['persona']):$this->model->Listar($fecha);
    $lista = $this->model->Listar_rango($_POST['desde'],$_POST['hasta'],$_POST['categoria'],$_POST['persona'],$_POST['metodo']);
    foreach($lista as $r): 

        //  $totall +=$r->monto;
        if(strlen($r->concepto)>=50){$concepto=substr($r->concepto, 0, 50)."...";}else {$concepto=$r->concepto;} 
        
        if (($r->moneda == 'GS') && ($r->anulado == NULL || $r->anulado == 0 )){
            $total_Gs = $total_Gs + ($r->monto);
        }else if (($r->moneda == 'RS') && ($r->anulado == NULL || $r->anulado == 0)){
            $total_RS = $total_RS + ($r->monto);
        }else if (($r->moneda == 'USD') && ($r->anulado == NULL || $r->anulado == 0)){
            $total_US = $total_US + ($r->monto);
        }
        
        ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";}?>>
            <td><?php echo $r->id; ?></td>
            <td><?php echo $r->user; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->nombre; ?></td>
            <td><?php echo $r->categoria.' '.$r->id_presupuesto; ?></td>
            <td title="<?php echo $r->concepto; ?>"><?php echo $concepto; ?></td>
            <td><?php echo $r->comprobante.' N° '.$r->nro_comprobante; ?></td>
            <td><?php echo number_format($r->monto,0,".",","); ?></td>
            <td><?php echo $r->moneda; ?></td>
            <td><?php echo $r->cambio; ?></td>
            <td><?php echo $r->forma_pago; ?></td>
            <td >
                 <?php if ($r->id_gift == null || ($r->anulado==1)): ?>
                    <?php if (!$r->anulado): ?>
                        <?php if ($r->id_venta): ?>
                        <a href="#detallesModal" class="btn btn-warning" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta; ?>">Venta</a>
                            <?php if ($r->id_deuda): ?>
                            <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id_deuda; ?>">Cobros</a>
                            <a  class="btn btn-danger delete" href="?c=ingreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"><i class="fas fa-trash-alt"></i>Eliminar</a>
                            <?php endif ?>
                        <?php elseif($r->id_deuda): ?>
                        <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id_deuda; ?>">Cobros</a>
                        <a  class="btn btn-danger delete" href="?c=ingreso&a=EliminarPago&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>"><i class="fas fa-trash-alt"></i></a>
                        <?php else: ?>
                        <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="ingreso">Editar</a>
                        <a  class="btn btn-danger delete" href="?c=ingreso&a=Eliminar&id=<?php echo $r->id; ?>&id_venta=<?php echo $r->id_venta; ?>">Eliminar</a>
                        <?php endif ?>
                    <?php else: ?>
                        ANULADO
                    <?php endif ?>
                <?php endif ?>
            </td>    
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
                
                <tr  style="background-color: #cccccc;">
                    <td></td>
                    <td></td>
                    <td align="right"><b>Total USD.</b></td> 
                    <td><b><?php  echo number_format($total_US,2,".",","); ?></b></td>
                    <td align="right"><b>Total Gs.</b></td>
                    <td><b><?php  echo number_format($total_Gs,0,",","."); ?></b></td> 
                    <td></td>
                    <td align="right"><b>Total Rs.</b></td> 
                    <td><b><?php  echo number_format($total_RS,2,".",",");?></b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                
    </tfoot>
</table>

</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>
<?php include("view/deuda/cobros-modal.php"); ?>

<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
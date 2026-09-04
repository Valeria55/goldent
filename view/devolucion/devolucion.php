<?php
$desde = (isset($_GET['desde']) && $_GET['desde'] != '') ? $_GET['desde'] : date('Y-m-01');
$hasta = (isset($_GET['hasta']) && $_GET['hasta'] != '') ? $_GET['hasta'] : date('Y-m-d');
$id_vendedor = isset($_GET['id_vendedor']) ? $_GET['id_vendedor'] : '';
$id_user = isset($_GET['id_user']) ? $_GET['id_user'] : '';

$usuarios = $this->usuario->ListarUsuarios();
?>
<h1 class="page-header">Lista de ajustes &nbsp; <a href="?c=devolucion_tmp" class="btn btn-primary"> Nuevo ajuste </a></h1>

<h3 id="filtrar" align="center" style="cursor: pointer;">Filtros <i class="fas fa-angle-down"></i><i class="fas fa-angle-up" style="display: none"></i></h3>
<div class="container-fluid" style="margin-bottom: 20px;">
    <div class="row">
        <div class="col-sm-12">
            <div align="center" id="filtro">
                <form method="get" action="index.php">
                    <input type="hidden" name="c" value="devolucion">

                    <div class="form-group col-md-3 text-left">
                        <label>Desde</label>
                        <input type="date" name="desde" value="<?php echo $desde; ?>" class="form-control">
                    </div>
                    <div class="form-group col-md-3 text-left">
                        <label>Hasta</label>
                        <input type="date" name="hasta" value="<?php echo $hasta; ?>" class="form-control">
                    </div>
                    <div class="form-group col-md-3 text-left">
                        <label>Usuario</label>
                        <select name="id_vendedor" class="form-control selectpicker" data-live-search="true" data-width="100%">
                            <option value="">Todos</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?php echo $u->id; ?>" <?php echo ((string)$id_vendedor === (string)$u->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u->user, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3 text-left">
                        <label>Funcionario</label>
                        <select name="id_user" class="form-control selectpicker" data-live-search="true" data-width="100%">
                            <option value="">Todos</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?php echo $u->id; ?>" <?php echo ((string)$id_user === (string)$u->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u->user, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 text-center" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
                        <a href="?c=devolucion" class="btn btn-default"><i class="fas fa-sync"></i> Limpiar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Venta</th>
            <th>Usuario</th>
            <th>Funcionario</th>
            <th>Observación</th>
            <th>Monto Venta</th>
            <th>Costo</th>
            <th>Diferencia</th>
            <th>Fecha y Hora</th>
            <?php if (!isset($_GET['id_venta'])): ?>        
            <th></th>
            <?php endif ?>
        </tr>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0;  
    $id_venta = (isset($_REQUEST['id_venta']))? $_REQUEST['id_venta']:0;
    $suma = 0; $count = 0;  
    foreach($this->model->Listar($id_venta, $desde, $hasta, $id_vendedor, $id_user) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
            <td><?php echo $r->id_venta; ?></td>
            <td><a href='#detallesModal' class='btn btn-info' data-toggle='modal' data-target='#detallesModal' data-id="<?php echo $r->venta;?>"><?php echo $r->venta; ?></a></td>
            <td><?php echo $r->vendedor; ?></td>
            <td><?php echo isset($r->user) && $r->user ? $r->user : '-'; ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo number_format($r->monto_venta,0,".",","); ?></td>
            <td><?php echo number_format($r->total,0,".",","); ?></td>
            <td><?php echo number_format($r->monto_venta - $r->total,0,".",","); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_venta)); ?></td>
            <?php if (!isset($_GET['id_venta'])): ?>
            <td>
                <a href="#devolucionModal" class="btn btn-success" data-toggle="modal" data-target="#devolucionModal" data-id="<?php echo $r->id_venta;?>">Ver</a>
                <a  class="btn btn-primary" href="?c=devolucion&a=Ticket&id=<?php echo $r->id_venta ?>" >Ticket</a>
                <?php if ($r->anulado): ?>
                ANULADO    
                <?php else: ?>
                <a  class="btn btn-danger delete" href="?c=devolucion&a=anular&id=<?php echo $r->id_venta ?>" class="btn btn-success">ANULAR</a>
                <?php endif ?>
            </td>
            <?php endif ?>
        </tr>
    <?php 
        $count++;
    endforeach; ?>
    </tbody>
    
</table>

</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/mes-modal.php"); ?>
<?php include("view/venta/dia-modal.php"); ?>
<?php include("view/devolucion/detalles-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>

<script type="text/javascript">
    $("#filtrar").click(function() {
      $("#filtro").toggle("slow");
      $("#filtrar i").toggle();
    });
</script>



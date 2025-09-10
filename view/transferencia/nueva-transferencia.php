 
<?php $fecha = date("Y-m-d"); 
$transferencia= new transferencia(); ?>
<h1 class="page-header">Agregar transferencias</h1>
<div class="container">
    <div class="row">
        <form id="crud-frm" method="post" action="?c=transferencia_tmp&a=guardar" enctype="multipart/form-data">
            

            <input type="hidden" name="c" value="transferencia" id="c"/>
            <input type="hidden" name="usuario_emisor" value="<?php echo $_SESSION['user_id'] ?>" />
            <input type="hidden" name="usuario_receptor" value="" />
            <input type="hidden" name="local_emisor" value="<?php echo $_SESSION['sucursal'] ?>" />
            <input type="hidden" name="tipo_link" value="<?php echo $_GET['tipo'] ?>" />
            <input type="hidden" name="local_receptor" value="" />
            <input type="hidden" name="fecha_solicitada" value="<?php echo date("Y-m-d") ?>" />
            <input type="hidden" name="fecha_aceptada" value="" />
            <input type="hidden" name="estado" value="Enviado" />
            <input type="hidden" name="observacion" value="VARIOSPROD" />
            <input type="hidden" name="id" value="<?php echo $transferencia->id; ?>" id="id" />

            <div class="col-sm-3">
                <label>Tipo</label>
                <select name="tipo" id="tipo" class="form-control">
                    <option value="seleccionar">-- Seleccionar --</option>
                    <option value="solicitud" <?php echo ($_GET['tipo'] == "solicitud")? "selected":""; ?>>Quiero que me transfieran</option>
                    <option value="transferencia" <?php echo ($_GET['tipo'] == "transferencia")? "selected":""; ?>>Quiero transferir</option>
                </select>
            </div>
            <div class="col-sm-3" id="sucursal" <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='transferencia')? '':'style="display : none;"'; ?>>
                <label>Sucursal</label>
                <select name="sucursal" class="form-control">
                    <?php foreach($this->sucursal->Listar() as $r): if($r->id!=$_SESSION['sucursal']){?>
                        <option value="<?php echo $r->id; ?>" <?php if($r->id == $_GET['suc']){echo 'selected';} ?>><?php echo $r->sucursal; ?></option>
                    <?php } endforeach; ?>
                </select>
            </div>
            <div class="col-sm-3">
            <label>Producto</label>
            <select name="id_producto" id="producto" class="form-control " data-show-subtext="true" data-live-search="true" data-style="form-control"
                    title="-- Seleccione el producto --" autofocus required>
                <?php foreach($this->producto->Listar() as $producto): ?> 
                <option style="font-size: 18px" data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>" <?php echo ($producto->stock<1)? 'disabled':''; ?>><?php echo $producto->producto.' ( '.$producto->stock.' ) - '.number_format($producto->precio_minorista,0,".",".").$promo; ?> </option>
                <?php endforeach; ?>
        </select>
        </div>
            <div class="col-sm-3">
                 <label>Cantidad</label>
                 <input type="text" name="cantidad" value="<?php echo $transferencia->cantidad; ?>" class="form-control" placeholder="Ingrese la cantidad" required>
            </div>

        </form>
    </div>
</div>
<p> </p>
<div class="table-responsive">

<p> </p>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Emisor</th>
            <th>Receptor</th>
            <th>Local emisor</th>
            <th>Tipo</th> 
            <th>Local receptor</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Fecha solicitada</th>
            <th>Fecha aceptada</th>
            <th>Observación</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 

    $lista = $this->model->Listar();
    
    foreach($lista as $r): 
        if ($r->observacion == "VARIOSPROD" AND $r->usuario_emisor == $_SESSION["user_id"]) {
        ?>
        <tr class="click" <?php if ($r->estado=="Aceptado" || $r->estado=="Rechazado" || $r->estado=="Cancelado"){echo "style='color:gray'";}?>>
            <td><?php echo $r->emisor; ?></td>
            <td><?php echo $r->receptor; ?></td>
            <td><?php echo $r->suc_emisor; ?></td>
            <td><?php echo $r->tipo; ?></td>
            <td><?php echo $r->suc_receptor; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo date("d/m/Y", strtotime($r->fecha_solicitada)); ?></td>
            <td><?php echo (date("Y", strtotime($r->fecha_aceptada))>2000)? date("d/m/Y", strtotime($r->fecha_aceptada)):""; ?></td>
            <td><?php echo $r->observacion; ?></td>
            <td><?php echo $r->estado; ?></td>
            <td>
                <?php if ($r->estado=="Aceptado" || $r->estado=="Rechazado" || $r->estado=="Cancelado"): ?>
                <?php echo $r->estado; ?>
                <?php else: ?>
                    <?php if ($r->usuario_emisor==$_SESSION['user_id']): ?>
                        <a class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="transferencia">Editar</a>
                        <a class="btn btn-danger delete" href="?c=transferencia&a=Borrar&id=<?php echo $r->id; ?>">Cancelar</a>
                    <?php else: ?>    
                        <a class="btn btn-primary" href="?c=transferencia&a=Aceptar&id=<?php echo $r->id; ?>&receptor=<?php echo $_SESSION['user_id'] ?>">Aceptar</a>
                        <a class="btn btn-danger delete" href="?c=transferencia&a=Borrar&id=<?php echo $r->id; ?>">Rechazar</a>
                    <?php endif ?>
                <?php endif ?>
            </td>    
        </tr>
    <?php } endforeach; ?>
    </tbody>
</table>
<?php if(true){ ?>
<div align="center"><a class="btn btn-lg btn-primary " href="?c=transferencia&a=FinalizarCarga&id=<?php echo $_SESSION['user_id'] ?>" class="btn btn-success">Finalizar (F4)</a></div>
<?php } ?>
</div> 
</div>
</div>

<?php include("view/venta/finalizar-modal.php"); ?>

<script type="text/javascript">
    // $('#tipo').on('change',function(){
    //     var tipo = $(this).val();
    //     alert(tipo);
    //     if(tipo=='solicitud'){
    //         window.location="https://trinitytech.com.py/sionsys/index.php?c=transferencia&a=varios&tipo=solicitud";
    //     }else{
    //         window.location="https://trinitytech.com.py/sionsys/index.php?c=transferencia&a=varios&tipo=transferencia";
    //     }
    // });
    
</script>
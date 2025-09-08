<?php
    if (!isset($_SESSION['user_id'])) {
        session_start();
    }
?>
<h1 class="page-header">
    <?php echo $transferencia->id != null ? $transferencia->fecha_solicitada : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=transferencia">transferencia</a></li>
  <li class="active"><?php echo $transferencia->id != null ? $transferencia->fecha_solicitada : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=transferencia&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="transferencia" id="c"/>
    <input type="hidden" name="usuario_emisor" value="<?php echo $_SESSION['user_id'] ?>" />
    <input type="hidden" name="usuario_receptor" value="" />
    <input type="hidden" name="local_emisor" value="<?php echo $_SESSION['sucursal'] ?>" />
    <input type="hidden" name="local_receptor" value="" />
    <input type="hidden" name="fecha_solicitada" value="<?php echo date("Y-m-d") ?>" />
    <input type="hidden" name="fecha_aceptada" value="" />
    <input type="hidden" name="estado" value="Enviado" />
    <input type="hidden" name="id" value="<?php echo $transferencia->id; ?>" id="id" />
   

    <div class="form-group">
        <label>Tipo</label>
        <select name="tipo" id="tipo" class="form-control">
            <option value="solicitud" <?php echo ($transferencia->tipo == "solicitud")? "selected":""; ?>>Quiero que me transfieran</option>
            <option value="transferencia" <?php echo ($transferencia->tipo == "transferencia")? "selected":""; ?>>Quiero transferir</option>
        </select>
    </div>

    <div class="form-group" id="sucursal" style="display : none;">
        <label>Sucursal</label>
        <select name="sucursal" class="form-control">
            <?php foreach($this->sucursal->Listar() as $r): if($r->id!=$_SESSION['sucursal']){?>
                <option value="<?php echo $r->id; ?>"><?php echo $r->sucursal; ?></option>
            <?php } endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label>Buscar producto</label>
        <input type="text" name="id_producto" id="buscar_producto" value="<?php echo $transferencia->id_producto; ?>" class="form-control" required list="productos">
        <datalist id="productos">
            <?php foreach($this->producto->ListarTodo() as $producto): ?> 
            <option data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>" <?php echo ($producto->id == $transferencia->id_producto)? "selected":""; ?> <?php echo ($producto->stock<1)? 'disabled':''; ?> class='<?php echo ($producto->id_sucursal==$_SESSION['sucursal'])? 'local':'externo'; ?>'><?php echo $producto->producto.' ( '.$producto->stock.' ) - '.number_format($producto->precio_minorista,0,".",".")." - ".$producto->sucursal; ?> </option>
            <?php endforeach; ?>
        </datalist>
    </div>

    <div class="form-group">
        <label>Producto seleccionado</label>
        <select name="id_productos" id="producto" class="form-control selectpicke" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione el producto --" disabled>
            <option  value="">-- Producto seleccionado --</option>
            <?php foreach($this->producto->ListarTodo() as $producto): ?> 
            <option data-subtext="<?php echo $producto->codigo; ?>" value="<?php echo $producto->id; ?>" <?php echo ($producto->id == $transferencia->id_producto)? "selected":""; ?> <?php echo ($producto->stock<1)? 'disabled':''; ?> class='<?php echo ($producto->id_sucursal==$_SESSION['sucursal'])? 'local':'externo'; ?>'><?php echo $producto->producto.' ( '.$producto->stock.' ) - '.number_format($producto->precio_minorista,0,".",".")." - ".$producto->sucursal; ?> </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    
    <div class="form-group">
        <label>Cantidad</label>
        <input type="text" name="cantidad" value="<?php echo $transferencia->cantidad; ?>" class="form-control" placeholder="Ingrese la cantidad" required>
    </div>


    <div class="form-group">
        <label>Observación</label>
        <input type="text" name="observacion" value="<?php echo $transferencia->observacion; ?>" class="form-control" placeholder="Ingrese la Observación" required>
    </div>

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script type="text/javascript">
    $('.externo').hide();
    
    $('#tipo').on('change',function(){
        var tipo = $(this).val();
        if(tipo=='solicitud'){
            $('.local').hide();
            $('.externo').show();
            $('#sucursal').hide();
        }else{
            $('.externo').hide();
            $('.local').show();
            $('#sucursal').show();
        }
    });
    
    $('#buscar_producto').on('change',function(){
        var id = $(this).val();
        $("#producto").val(id);
    });
    
</script>
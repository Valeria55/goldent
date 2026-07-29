<h1 class="page-header">
    <?php echo $deuda->id != null ? $deuda->fecha : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=deuda">deuda</a></li>
  <li class="active"><?php echo $deuda->id != null ? $deuda->fecha : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=deuda&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="deuda" id="c"/>
    <input type="hidden" name="id" value="<?php echo $deuda->id; ?>" id="id" />
    <input type="hidden" name="id_venta" value="0">
    
    <div class="form-group col-sm-6">
        <label>Fecha</label>
        <input type="date" name="fecha" value="<?php echo ($deuda->fecha) ? date("Y-m-d", strtotime($deuda->fecha)):date("Y-m-d"); ?>" class="form-control" placeholder="Fecha" required>
    </div>
    <div class="form-group col-sm-6">
        <label>Vencimiento</label>
        <input type="date" name="vencimiento" value="<?php echo $deuda->vencimiento; ?>" class="form-control" placeholder="Ingrese el vencimiento" >
    </div>
    <div class="form-group col-sm-12">
        <label>Cliente</label>
        <select name="id_cliente" id="id_cliente" class="form-control selectpicker" data-show-subtext="true" data-live-search="true">
            <option value="2">Cliente casual (XXX)</option>
            <?php foreach($this->cliente->Listar() as $clie): ?> 
            <option value="<?php echo $clie->id; ?>" <?php echo ($clie->id == $deuda->id_cliente)? "selected":""; ?>><?php echo $clie->nombre." ( ".$clie->ruc." )"; ?> </option>
            <?php endforeach; ?>
        </select>
    </div> 

    <div class="form-group col-sm-12" id="div-obs-cliente-deuda-edit" style="display:none;">
        <div class="alert alert-warning" style="margin-bottom: 0; border-left: 5px solid #f0ad4e;">
            <i class="fa fa-exclamation-triangle"></i> <strong>Observación del Cliente:</strong> <span id="txt-obs-cliente-deuda-edit"></span>
        </div>
    </div>
    
    <div class="form-group col-sm-12">
        <label>Concepto</label>
        <input type="text" name="concepto" value="<?php echo $deuda->concepto; ?>" class="form-control" placeholder="Ingrese su concepto" required>
    </div>
    
    <div class="form-group col-sm-12">
        <label>Monto</label>
        <input type="number" id="monto" name="monto" value="<?php echo $deuda->monto; ?>" class="form-control" placeholder="Ingrese el monto" min="0" required>
    </div>
        
    <div class="form-group col-sm-12">
        <label>Saldo</label>
        <input type="number" id="saldo" name="saldo" value="<?php echo $deuda->saldo; ?>" class="form-control" placeholder="Ingrese el saldo" min="0" required>
    </div>

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script>
    $( "#monto" ).keyup(function() {
        $( "#saldo" ).val($( "#monto" ).val());
    });  

    $('#id_cliente').on('change', function() {
        var id_cli = $(this).val();
        if (id_cli > 0) {
            $.post('?c=cliente&a=Buscar', {id: id_cli}, function(data) {
                var c = typeof data === 'string' ? JSON.parse(data) : data;
                var obs = (c && c.observacion_cliente && $.trim(c.observacion_cliente) !== '') ? c.observacion_cliente : (c && c.observacion ? c.observacion : '');
                if (obs && $.trim(obs) !== '') {
                    $('#txt-obs-cliente-deuda-edit').text(obs);
                    $('#div-obs-cliente-deuda-edit').slideDown();
                } else {
                    $('#div-obs-cliente-deuda-edit').slideUp();
                }
            });
        } else {
            $('#div-obs-cliente-deuda-edit').slideUp();
        }
    });

    $(document).ready(function() {
        var id_ini = $('#id_cliente').val();
        if (id_ini > 0) {
            $('#id_cliente').trigger('change');
        }
    });
</script>
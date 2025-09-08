<h1 class="page-header">
    <?php echo $vendedor->id != null ? $vendedor->nombre : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=vendedor">vendedor</a></li>
  <li class="active"><?php echo $vendedor->id != null ? $vendedor->nombre : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=vendedor&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="vendedor" id="c"/>
    <input type="hidden" name="id" value="<?php echo $vendedor->id; ?>" id="id" />
    
    <div class="form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?php echo $vendedor->nombre; ?>" class="form-control" placeholder="Ingrese nombre" required>
    </div>
    
    <div class="form-group">
        <label>Porcentaje</label>
        <input type="number" name="porcentaje" value="<?php echo $vendedor->porcentaje; ?>" class="form-control" placeholder="Ingrese el porcentaje" min="1" required>
    </div>
    
    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script>
    $("#crud-frm").submit(function(event) {
    	alert("sfaef");
        var parametros = $(this).serialize();
        var c = $("#c").val();
        var id = $("#id").val();
        var url = "?c="+c+"&a=guardar&id="+id;
            $.ajax({
                type: "POST",
                url: url,
                data: parametros,
                success: function(respuesta){
                    load(c);
                    $("#crudModal").modal('hide');
                }
            });
        event.preventDefault();
    }
</script>
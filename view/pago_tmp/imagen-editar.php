<h1 class="page-header">
    <?php echo $imagen->id != null ? $imagen->imagen : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
  <li><a href="?c=imagen">imagen</a></li>
  <li class="active"><?php echo $imagen->id != null ? $imagen->imagen : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=imagen&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="imagen" id="c"/>
    <input type="hidden" name="prod" value="" id="prod" />
    <input type="hidden" name="id_producto" value="" id="id_prod" />
    <input type="hidden" name="id" value="<?php echo $imagen->id; ?>" id="id" />

    <div class="form-group">
        <label>Imagen</label>
         <input type="file" name="imagen[]" class="form-control"  required="required" id="imagen"  multiple>
    </div>


    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script type="text/javascript">
    $('#imagen').on("click", function (e) {
        
        var idProd = obtenerValorParametro('id_prod');
        var prod = obtenerValorParametro('prod');
        $("#id_prod").attr("value",idProd);
        $("#prod").attr("value",prod);

        function obtenerValorParametro(sParametroNombre) {
            var sPaginaURL = window.location.search.substring(1);
             var sURLVariables = sPaginaURL.split('&');
              for (var i = 0; i < sURLVariables.length; i++) {
                var sParametro = sURLVariables[i].split('=');
                if (sParametro[0] == sParametroNombre) {
                  return sParametro[1];
                }
              }
             return null;
            }
    })

    
</script>
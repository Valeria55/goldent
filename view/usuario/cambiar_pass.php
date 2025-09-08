<h1 class="page-header">
    Cambio de contraseña
</h1>

<ol class="breadcrumb">

  <li><a href="?c=usuario">Usuario</a> </li>
  <li class="active"><?php echo $_SESSION['username']; ?></li>
</ol>

<form  id="usuario" id="crud-frm" method="post" action="?c=usuario&a=ChangePassword" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>" />

     <div class="form-group">
        <div class="alert alert-danger alert-dismissible fade in" role="alert" style="display: none;" id="incorrecta">
            <strong> Error!</strong> Contraseña actual incorrecta
        </div>
        <label>Contraseña actual</label>
        <input type="text" id="actual" name="actual" minlength="4" value="" class="form-control" placeholder=" Ingrese Contraseña actual" required>
    </div>


    <div class="form-group">
        <label>Contraseña nueva</label>
        <input type="text" id="pass" name="pass" minlength="4" value="" class="form-control" placeholder=" Ingrese  nueva contraseña" required>
    </div>

    <div class="form-group">
        <div class="alert alert-danger alert-dismissible fade in" role="alert" style="display: none;" id="noCoinciden">
            <strong> Error!</strong> Las contraseñas no coinciden
        </div>
        <label>Confirmar contraseña</label>
        <input type="text" id="confirm_pass" name="confirm_pass" minlength="4" value="" class="form-control" placeholder=" Ingrese  nueva contraseña" required>
    </div>

    <hr />
    
    <div class="text-right">
        <button class="btn btn-primary">Cambiar contraseña</button>
    </div>
</form>

<script type="text/javascript">
  $( "#usuario" ).submit(function( event ) {
        
        event.preventDefault();
        var actual = $("#actual").val();
        var url = "?c=usuario&a=validar&pass="+actual;
        $.ajax({

            url: url,
            method : "POST",
            data: actual,
            cache: false,
            contentType: false,
            processData: false,
            success:function(respuesta){
                console.log("respuesta",respuesta);
                
                if(respuesta=="true"){
                    $("#incorrecta").hide();
                }else{
                    $("#incorrecta").show();
                }
                
                if(pass != confirm_pass){
                    $("#noCoinciden").show();
                }else{
                    $("#noCoinciden").hide();
                    if(respuesta=="true"){
                        $('#usuario').unbind('submit').submit();
                    }
                }
            }

        })

        
        var pass = $("#pass").val();
        var confirm_pass = $("#confirm_pass").val();

         
        
        
    });

    $( "#actual" ).change(function() {
        var actual = $("#actual").val();
        var url = "?c=usuario&a=validar&pass="+actual;
        $.ajax({

            url: url,
            method : "POST",
            data: actual,
            cache: false,
            contentType: false,
            processData: false,
            success:function(respuesta){
                var correcta = respuesta;
               
                if(correcta=="false"){
                    $("#incorrecta").show();
                }else{
                    $("#incorrecta").hide();
                }
            }

        })
    });
</script>

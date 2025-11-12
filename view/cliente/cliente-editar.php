<h1 class="page-header">
    <?php echo $cliente->id != null ? $cliente->nombre : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
    <li><a href="?c=cliente">Cliente</a></li>
    <li class="active"><?php echo $cliente->id != null ? $cliente->nombre : 'Nuevo Registro'; ?></li>
</ol>

<form id="crud-frm" method="post" action="?c=cliente&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="cliente" id="c" />
    <input type="hidden" name="url" value="<?php echo $_SERVER['PHP_SELF'] ?>" />
    <input type="hidden" name="nick" value=" " />
    <input type="hidden" name="pass" value=" " />
    <input type="hidden" name="sucursal" value=" " />
    <input type="hidden" name="id" value="<?php echo $cliente->id; ?>" id="id" />

    <div class="form-group">
        <label>CI/RUC</label>
        <div class="input-group">
            <input type="text" name="ruc" id="ruc" value="<?php echo $cliente->ruc; ?>" class="form-control" placeholder="Ingrese ruc/ci" required>
            <span class="input-group-btn">
                <button class="btn btn-primary" type="button" id="btnBuscar">
                    <i class="glyphicon glyphicon-search"></i>
                </button>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo $cliente->nombre; ?>" class="form-control" placeholder="Ingrese nombre" required>
    </div>

    <div class="form-group">
        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?php echo $cliente->telefono; ?>" class="form-control" placeholder="Ingrese telefono">
    </div>
    <div class="form-group">
        <label>Correo</label>
        <input type="email" name="correo" value="<?php echo $cliente->correo; 
                                                ?>" class="form-control" placeholder="Ingrese correo" >
    </div>
    <div class="form-group">
        <label>Fecha de nacimiento</label>
        <input type="date" name="cumple" value="<?php echo $cliente->cumple; 
                                                ?>" class="form-control">
    </div>
    <div class="form-group">
        <label>Dirección</label>
        <input type="text" name="direccion" value="<?php echo $cliente->direccion; ?>" class="form-control" placeholder="Ingrese dirección">
    </div>
    <div class="form-group">
        <label>¿Es Mayorista?</label>
        <select name="mayorista" class="form-control">
            <option value="NO" <?php if ($cliente->mayorista  == "NO") {
                                    echo "selected";
                                } ?>>NO</option>
            <option value="SI" <?php if ($cliente->mayorista  == "SI") {
                                    echo "selected";
                                } ?>>SI</option>
        </select>
    </div>

    <?php if (!isset($_SESSION)) session_start();
    if ($_SESSION['nivel'] == 3) { ?>
        <input type="hidden" name="cliente" value="1" />
        <input type="hidden" name="proveedor" value="0" />
    <?php } else { ?>
        <div class="col-sm-3">
            <input type="checkbox" id="cl" name="cliente" value="<?php echo $cliente->cliente; ?>" <?php if ($cliente->cliente == 1) {
                                                                                                        echo "value='0' checked ";
                                                                                                    } else {
                                                                                                        echo "value='1'";
                                                                                                    } ?>>
            <label for="cl">Cliente</label>
        </div>
        <div class="col-sm-3">
            <input type="checkbox" id="p" name="proveedor" value="<?php echo $cliente->proveedor; ?>" <?php if ($cliente->proveedor == 1) {
                                                                                                            echo "value='0' checked ";
                                                                                                        } else {
                                                                                                            echo "value='1'";
                                                                                                        } ?>>
            <label for="p">Proveedor</label>
        </div>
    <?php } ?>

    <div class="form-group" style='display:none'>
        <label>Foto</label>
        <input type="file" name="foto_perfil" class="form-control">
    </div>

    <hr />

    <div class="text-right">
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>

<script>

    $('#btnBuscar').click(function () {
        const id = $('#ruc').val().trim();

        if (id === '') {
            alert('Por favor, ingrese un ID.');
            return;
        }

        // URL de la API (ajusta esto según tu configuración)
        const apiUrl = `https://trinitytech.com.py/consulta_ruc/index.php?id=${id}`;

        // Hacer la solicitud GET
        $.ajax({
            url: apiUrl,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log(response);
                // Mostrar el resultado
                resultado = JSON.parse(response);
                $('#nombre').val(resultado.nombre);
                $('#ruc').val(resultado.id+"-"+resultado.tipo);
            },
            error: function (xhr) {
                // Manejar errores
                if (xhr.status === 404) {
                    $('#result').text('No se encontraron registros con el ID proporcionado.');
                    $('#nombre').val("No se encontró");
                } else {
                    $('#result').text(`Error: ${xhr.status} - ${xhr.statusText}`);
                }
            }
        });
    });
    
    $('#cl').on('click', function() {
        var cl = $(this).val();
        if (cl == 0) {
            $("#cl").val(1);

        } else {
            $("#cl").val(0);
        }
    });
    $('#p').on('click', function() {
        var p = $(this).val();
        if (p == 0) {
            $("#p").val(1);

        } else {
            $("#p").val(0);
        }
    });

    $('#crud-frm1').on('submit', function(event) {
        var parametros = $(this).serialize();
        var c = $("#c").val();
        var id = $("#id").val();

        var url = "?c=" + c + "&a=guardar&id=" + id;
        $.ajax({
            type: "POST",
            url: url,
            data: parametros,
            cache: false,
            processData: false,
            success: function(respuesta) {
                load(c);
                $("#crudModal").modal('hide');
            }
        });
        event.preventDefault();
    })
</script>
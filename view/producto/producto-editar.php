<h1 class="page-header">
    <?php echo $producto->id != null ? $producto->producto : 'Nuevo Registro'; ?>
</h1>

<ol class="breadcrumb">
    <li><a href="?c=producto">Producto</a></li>
    <li class="active"><?php echo $producto->id != null ? $producto->producto : 'Nuevo Registro'; ?></li>
</ol>

<form id="frm-producto" method="post" action="?c=producto&a=guardar" enctype="multipart/form-data">
    <input type="hidden" name="c" value="producto" id="c" />
    <input type="hidden" name="id" value="<?php echo $producto->id; ?>" id="id" />
    <input type="hidden" name="stock" value="<?php echo $producto->stock; ?>" id="stock" />

    <div class="row">
        <div class="form-group col-md-12">
            <label>Código <a href='#' class='btn btn-default' id="autocodigo" <?php if ($producto->id != null) echo 'disabled'; ?>>Auto código</a></label>
            <input type="text" name="codigo" id="codigo" value="<?php echo $producto->codigo; ?>" <?php if ($producto->id != null) echo 'readonly'; ?> class="form-control" placeholder="Ingrese el codigo" required>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label>Categoría</label>
            <select name="id_categoria" class="form-control selectpickerr" data-show-subtext="true" data-live-search="true" data-style="form-control">
                <?php foreach ($this->categoria->Listar() as $r) : ?>
                    <option value="<?php echo $r->id; ?>" <?php echo ($r->id == $producto->id_categoria) ? "selected" : ""; ?>><?php echo $r->categoria; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label>Marca</label>
            <select name="marca" class="form-control selectpickerr" data-show-subtext="true" data-live-search="true" data-style="form-control">
                <?php foreach ($this->marca->Listar() as $r) : ?>
                    <option value="<?php echo $r->id; ?>" <?php echo ($r->id == $producto->marca) ? "selected" : ""; ?>><?php echo $r->marca; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <?php  // if (!isset($_SESSION)) session_start(); 
            ?>
            <input type="hidden" name="sucursal" value="<?php echo $_SESSION['sucursal']; ?>">
        </div>
        <div class="form-group col-md-12">
            <label>Producto</label>
            <input type="text" name="producto" value="<?php echo $producto->producto; ?>" class="form-control" placeholder="Ingrese el producto" list="prod" required>
            <datalist id="prod">
                <?php foreach ($this->model->Listar() as $prod) : ?>
                    <option data-subtext="<?php echo $prod->codigo; ?>" value="<?php echo $prod->id; ?>" <?php echo ($prod->stock < 1) ? 'disabled' : ''; ?>><?php echo $prod->producto . ' ( ' . $prod->stock . ' ) - ' . number_format($prod->precio_minorista, 0, ".", "."); ?> </option>
                <?php endforeach; ?>
            </datalist>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6" style="display:none;">
            <label>Descripción</label>
            <textarea name="descripcion" id="editorr" class="form-control"><?php echo $producto->descripcion; ?></textarea>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label>Precio costo</label>
            <input type="text" name="precio_costo" id="precio_costo" value="<?php echo $producto->precio_costo; ?>" class="form-control" placeholder="Ingrese el precio" required <?php if (($_SESSION['nivel'] != 1) && ($producto->id != null))  echo "readonly"; ?>>
        </div>

        <div class="form-group col-md-6">
            <label>Precio de venta</label>
            <input type="number" id="porcentaje_minorista" class="form-control" placeholder="Ingrese el porcentaje" <?php if (($_SESSION['nivel'] != 1) && ($producto->id != null))   echo "readonly"; ?>>
            <input type="number" name="precio_minorista" id="precio_minorista" value="<?php echo $producto->precio_minorista; ?>" class="form-control" placeholder="Ingrese el precio" <?php if (($_SESSION['nivel'] != 1) && ($producto->id != null))  echo "readonly"; ?>>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6" style="display:none;">
            <label>Precio may</label>
            <input type="number" id="porcentaje_mayorista" class="form-control" placeholder="Ingrese el porcentaje">
            <input type="number" name="precio_mayorista" id="precio_mayorista" value="<?php echo $producto->precio_mayorista; ?>" class="form-control" placeholder="Ingrese el precio">
        </div>

        <div class="form-group col-md-4">
            <label>Precio promocional</label>
            <input type="number" name="precio_promo" id="precio_promo" value="<?php echo $producto->precio_promo; ?>" class="form-control" placeholder="Ingrese el precio">
        </div>
        <div class="form-group col-md-4">
            <label>Promo desde</label>
            <input type="date" name="desde" id="desde" value="<?php echo $producto->desde; ?>" class="form-control">
        </div>

        <div class="form-group col-md-4">
            <label>Promo hasta</label>
            <input type="date" name="hasta" id="hasta" value="<?php echo $producto->hasta; ?>" class="form-control">
        </div>
    </div>


    <div class="row">
        <div class="form-group col-md-6">
            <label>Stock </label>
            <input type="number" name="stock" value="<?php echo $producto->stock; ?>" <?php if ($producto->id != null) echo 'readonly'; ?> class="form-control" placeholder="Ingrese el stock " readonly <?php //if (($_SESSION['nivel'] == 1) && ($producto->id != null))   echo "readonly"; ?>>
        </div>

        <div class="form-group col-md-6">
            <label>IVA</label>
            <select name="iva" class="form-control">
                <option value="10" <?php echo ($producto->iva == '10') ? "selected" : ""; ?>>10%</option>
                <option value="5" <?php echo ($producto->iva == '5') ? "selected" : ""; ?>>5%</option>
            </select>
        </div>

    </div>

    <div class="row">
        <div class="form-group col-md-6" style="display:none;">
            <label>Descuento máximo</label>
            <input type="number" name="descuento_max" value="<?php echo $producto->descuento_max; ?>" class="form-control" placeholder="Ingrese el descuento máximo">
        </div>
        <div class="form-group col-md-12" style="display:none;">
            <label>Importado</label>
            <select name="importado" class="form-control">
                <option value="NO" <?php echo ($producto->importado == 'NO') ? "selected" : ""; ?>>NO</option>
                <option value="SI" <?php echo ($producto->importado == 'SI') ? "selected" : ""; ?>>SI</option>
            </select>
        </div>

    </div>

    <input type="hidden" name="imagen[]" class="form-control" multiple>


    <hr />

    <div class="text-right">
        <button class="btn btn-primary" id="guardar">Guardar</button>
    </div>
</form>

<script src="plugins/ckeditor/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>

<script type="text/javascript">
     $("#frm-producto").submit(function(e) {
       var precio_costo = $("#precio_costo").val();
        var precio_minorista = $("#precio_minorista").val();
        var precio_mayorista = $("#precio_mayorista").val();



        if ((precio_costo * 1.05) >= precio_minorista) {
            e.preventDefault();
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-right',
                iconColor: 'red',

                customClass: 'swal-wide',
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'error',
                title: `La ganancia no puede ser menor al 5%`
            });
            $("#precio_minorista").select();
        }
        // if ((precio_costo * 1.05) >= precio_mayorista) {
        //     e.preventDefault();
        //     const Toast = Swal.mixin({
        //         toast: true,
        //         position: 'top-right',
        //         iconColor: 'red',

        //         customClass: 'swal-wide',
        //         showConfirmButton: false,
        //         timer: 6000,
        //         timerProgressBar: true
        //     });
        //     Toast.fire({
        //         icon: 'error',
        //         title: `La ganancia no puede ser menor al 5%`
        //     });
        //     $("#precio_mayorista").select();
        // }

    });
    $("#porcentaje_minorista").keyup(function() {

        var costo = parseInt($("#precio_costo").val());
        var porcentaje = parseInt($("#porcentaje_minorista").val());

        var precio_minorista = costo + (costo * (porcentaje / 100));

        $("#precio_minorista").val(precio_minorista);

    });

    $("#autocodigo").click(function() {

        // find diff
        let difference = 999999 - 100000;

        // generate random number 
        let rand = Math.random();

        // multiply with difference 
        rand = Math.floor(rand * difference);

        // add with min value 
        rand = rand + 100000;

        $("#codigo").val(rand);
    });

    $("#porcentaje_mayorista").keyup(function() {

        var costo = parseInt($("#precio_costo").val());
        var porcentaje = parseInt($("#porcentaje_mayorista").val());

        var precio_mayorista = costo + (costo * (porcentaje / 100));

        $("#precio_mayorista").val(precio_mayorista);

    });
</script>
<script type="text/javascript">
    hotkeys('f2, f4, ctrl+b', function(event, handler) {
        switch (handler.key) {
            case 'f2':
                location.href = "?c=venta_tmp";
                break;
            case 'f4':
                $("#guardar").click();
                break;
            case 'ctrl+b':
                alert('you pressed ctrl+b!');
                break;
            default:
                alert(event);
        }
    });
</script>
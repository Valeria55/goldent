<h1 class="page-header">Inventario del <?php echo date("d/m/Y"); ?> &nbsp;
<?php if($_SESSION['nivel']<=1) ?> </h1>
<a class="btn btn-primary pull-right" href="?c=inventario&a=guardar" class="btn btn-success">Nuevo Inventario</a>
<br><br><br>
<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up" style="display: none"></i></h3>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <div align="center" id="filtro" style="display: none;">
            <form method="post">
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde']))? $_GET['desde']:''; ?>" class="form-control" required>
                </div>
                
                <input type="submit" name="filtro" value="Filtrar" class="btn btn-success"> 
            </form>
        </div>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div>
<p> </p>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Usuario</th> 
            <th>Código</th>
            <th>Producto</th>    
            <th>Stock Actual</th>    
            <th>Stock Real</th>
            <th>Diferencia</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $fecha = date('Y-m-d');
    $lista = (isset($_POST['desde']))? $this->model->ListarRango($_POST['desde']):$this->model->Listar($fecha);
    
    foreach($lista as $r):  ?>
        <tr >
            <td><?php echo $r->usuario; ?></td>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto.' ('.$r->precio_costo; ?>)</td>
            <td id="<?php echo $r->id_i; ?>"><?php echo $r->stock_actual; ?></td>
            <td align="center" width="10%">
                <!--Tiene un campo que se puede tipear. Stock_real <!-->
                <?php if($r->fecha == date('Y-m-d')){ ?>
                     <div class="form-group">
                    <input  id_real="<?php echo $r->id_i; ?>" stock_real="<?php echo $r->stock_real; ?>"  name="stock_real" class="stock_real form-control" type="number" value="<?php echo $r->stock_real; ?>" id="stock_real">
                </div>
                <?php }else { ?>
                    <div class="form-group"> 
                    <input  id_real="<?php echo $r->id_i; ?>" stock_real="<?php echo $r->stock_real; ?>"  name="stock_real" class="stock_real form-control" type="number" value="<?php echo $r->stock_real; ?>" id="stock_real" readonly>
                </div>
                <?php } ?>
               
            </td>
            <td id="faltante<?php echo $r->id_i; ?>"><?php echo $r->faltante; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>

</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>

<script type="text/javascript">
    
   /*  $('.stock_real').on('change',function(){
        
        var stock_real = $(this).val();
        var id = parseInt($(this).attr("id"));
        console.log(id);
        //alert(id);
        //La accion es StockReal
        var url = "?c=inventario&a=StockReal&id="+id+"&stock_real="+stock_real;
            $.ajax({

                url: url,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    //$("#stock_real").html(respuesta);
                    //location.reload(true);
                    //alert(respuesta);
                }

            })
            var real = parseInt($(this).val());
           // var actual = parseInt($(this).val());
            //var total = parseInt($(this).val());
            var url = "?c=inventario&a=obtenerjson&id="+id;
                $.ajax({

                url: url,
                method : "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var inventario = JSON.parse(respuesta);
                    var actual = inventario.stock_actual;
                    var total =  actual - real;
                    console.log(actual);

                    $("#faltante").html((total).toLocaleString('de-DE'));
                    console.log(total);
                }

            })
    });

   $('#stock_real').on('keyup',function(){
        var id = $(this).val();
        var real = parseInt($(this).val());
        //alert(real);
        console.log(total);
        var url = "?c=inventario&a=obtener&id="+id;
            $.ajax({

                url: url,
                method : "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    var actual = $("#stock_actual").val();
                    var total = actual - real;
                    $("#faltante").html((total).toLocaleString('de-DE'));
                }

            })
    });*/


/*            $('#stock_real').on('keyup',function(){
            var real = parseInt($(this).val());
            alert(real);
            var actual = $("#stock_actual").val();
            var total = actual - real;
            $("#faltante").html((total).toLocaleString('de-DE'));
    });

    $("#link").val(window.location.href);*/
</script>

<script type="text/javascript">

    $('.stock_real').on('keyup',function(){

        var id_real = $(this).attr("id_real");
        var stock_real = $(this).val();
        var stock_actual = parseInt($("#"+id_real).text());

        $("#faltante"+id_real).html(( stock_real - stock_actual));

        var url = "?c=inventario&a=StockReal&id="+id_real+"&stock_real="+stock_real;

        $.ajax({

            url: url,
            cache: false,
            contentType: false,
            processData: false,
            success:function(respuesta){
            }

        })

    });

    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>


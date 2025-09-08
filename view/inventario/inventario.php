<h1 class="page-header">Inventario <?php
                                    // if ($_GET['fecha'] == '') {
                                    $fecha = date('Y-m-d');
                                    // } else {
                                    //     $fecha = $_GET['fecha'];
                                    // }

                                    $id_c = $_GET['id_c']; //??die('Datos faltantes, vuelva a la pagina anterior');
                                    ?> &nbsp;
    <?php
    $cierre_inv_actual = $this->cierre_inventario->Obtener($id_c);
    $c = is_null($cierre_inv_actual->fecha_cierre);

    if ($_SESSION['nivel'] <= 1) ?>
</h1>
<a class="btn btn-info pull-right" align="center" href="?c=cierre_inventario&a=Cierreinventario">Historial de Inventarios </a>
<?php if ($c) : ?>
    <a class="btn btn-warning pull-right " align="center" href="#finalizar" data-toggle="modal" data-target="#finalizar" data-c="inventario" style="margin-right: 1rem">Finalizar </a>
<?php else : ?>
    <a class="btn btn-warning pull-right " align="center" href="?c=inventario&a=InventarioPdf&id_c=<?php echo $id_c; ?>" style="margin-right: 1rem" onclick="Swal.fire({
        title: 'Cargando',
        html: 'Generando PDF... por favor, aguarde.',
        showConfirmButton: false,
        backdrop: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        onOpen: () => {
            swal.showLoading();
        },
        }).then((result) => {
        /* Read more about handling dismissals below */

        })">Exportar a PDF</a>
<?php endif; ?>
<br><br><br>
<!--<h3 id="filtrar" align="center">Filtrar por Fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up" style="display: none"></i></h3>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <div align="center" id="filtro" style="display: none">
            <form method="post">
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="desde" value="<?php //echo (isset($_GET['desde']))? $_GET['desde']:"";
                                                            ?>" class="form-control" required>
                </div>
                
                <input type="submit" name="filtro" value="Filtrar" class="btn btn-success"> 
            </form>
        </div>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div>-->

<div class="row">
    <div class="col-md-4"></div>
    <div class="center col-md-4 offset-md-1">
        <div class="form-group">
            <select style="width: 100%;" class="form-control form-control-lg selectpickerr" onchange="filtrarTabla()" data-show-subtext="true" data-live-search="true" name="filtrar_inventario" id="filtrar_inventario">
                <option value="0">Sin Filtrar</option>
                <option value="1">Productos sin cargar</option>
                <option value="2">Productos cargados</option>
            </select>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>

<p> </p>
<table class="table table-striped table-bordered display responsive" id="tabla-cierre-inventario" width="100%">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th id="header_id">Id</th>
            <th>Código</th>
            <th>Marca</th>
            <th>Producto</th>
            <th>P. Costo</th>
            <th>P. Venta</th>
            <th>Stock Actual</th>
            <th>Stock Real</th>
            <th id="header_fecha_carga">Fecha Carga</th>
            <th>Faltante</th>
            <th>Monto Faltante</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr style="background-color: #000; color:#fff">
            <th>Id</th>
            <th>Código</th>
            <th>Categ.</th>
            <th>Producto</th>
            <th>P. Costo</th>
            <th>P. Venta</th>
            <th>Stock Actual</th>
            <th>Stock Real</th>
            <th>Fecha Carga</th>
            <th>Faltante</th>
            <th>Monto Faltante</th>
        </tr>
    </tfoot>
</table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/inventario/finalizar-inventario.php"); ?>


<script type="text/javascript">
    function filtrarTabla() {
        let filtro = $("#filtrar_inventario").val();
        cargarTabla(filtro);
    }
    var tabla;
    $(document).ready(function() {
        var filtro = '0';
        cargarTabla(filtro);
    });

    function cargarTabla(filtro) {
        let url = "?c=inventario&a=ListarSS&id_c=<?php echo $id_c; ?>&q=" + filtro;
        // let url = "?c=paquete&a=ListarRecibirSS&q=" + filtro;
        let ordenar = 0; // id



        console.log(filtro);
        switch (parseInt(filtro)) {
            case 1:
                setTimeout(() => { 
                    // esta fue la unica manera que encontre de que el ordenamiento sea dinamico
                    // Datatables no permite cambiar facilmente el ordenamiento solo a traves del Json
                    $("#header_id").trigger('click');
                    $("#header_id").trigger('click');
                }, 100);
                ordenar = '0'; // id
                break;
            case 2:
                setTimeout(() => {
                    $("#header_fecha_carga").trigger('click');
                    $("#header_fecha_carga").trigger('click');
                }, 100);
                ordenar = '8'; // fecha
                break;

            default:
                setTimeout(() => {
                    $("#header_id").trigger('click');
                    $("#header_id").trigger('click');
                }, 100);
                ordenar = '0'; // id
                break;
        }
        console.log('ordenar ' + ordenar);
        tabla =
            $("#tabla-cierre-inventario").DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true, //ACTIVAR SI ES CANTIDAD DE REGISTROS ES MAYOR A 2000
                "ajax": {
                    "url": url
                },
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "order": [
                    [ordenar, 'desc']
                ],
                "stateSave": true,
                "columnDefs": [{
                        'targets': [3],
                        'createdCell': function(td, cellData, rowData, row, col) {
                            $(td).attr('id', rowData[0]);
                        }
                    },
                    {
                        'targets': [4],
                        'createdCell': function(td, cellData, rowData, row, col) {
                            if (cellData < 1) {
                                $(td).css('color', 'red')
                            }
                            td.id = "stock_real_" + rowData[0];
                        }
                    },
                    {
                        'targets': [-1],
                        'createdCell': function(td, cellData, rowData, row, col) {
                            $(td).attr('id', "faltante" + rowData[0]);
                        }
                    },
                ],
                /*
                //sumatoria en footer

                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    $.ajax({

                        url: "?c=inventario&a=ObtenerSumatorias&id_c=<?php //echo $id_c; ?>&q=" + filtro,
                        cache: false,
                        async: false,
                        contentType: false,
                        processData: false,
                        success: function(respuesta) {

                            resp = JSON.parse(respuesta);
                            // sumatoria en footer
                            // $(api.column(10).footer()).html('Total faltante: ' + resp.monto_faltante_formatted);

                        }

                    })

                }
                */
            });
    }
    $(function() {});
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
    function setStockReal(input) {

        var id_real = input.attr("id_real");
        var stock_real = input.val();
        var stock_actual = parseInt($("#" + id_real).text());


        // $("#faltante" + id_real).html((stock_actual - stock_real));


        var url = "?c=inventario&a=StockReal&id=" + id_real + "&stock_real=" + stock_real;

        $.ajax({

            url: url,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                // const Toast = Swal.mixin({
                //     toast: true,
                //     position: 'top-right',
                //     iconColor: 'white',

                //     customClass: {
                //         popup: 'colored-toast'
                //     },
                //     showConfirmButton: false,
                //     timer: 1500,
                //     timerProgressBar: true
                // })
                // Toast.fire({
                //     icon: 'success',
                //     title: 'Cambios guardados'
                // })
                try {
                    var resp = JSON.parse(respuesta);
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-right',
                        iconColor: 'white',

                        customClass: 'swal-wide',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    })
                    Toast.fire({
                        icon: 'success',
                        title: `El stock del producto ${resp.producto} fue guardado`
                    })
                    /*
                    Swal.fire({
                        // position: 'top-end',
                        icon: 'success',
                        title: 'Guardado',
                        text: `El stock del producto ${resp.producto} fue guardado`,
                        showConfirmButton: true,
                        // timer: 2000
                    })
                    */

                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ocurrió un error',
                        text: respuesta,
                        footer: 'Intente nuevamente o recargue el sitio'
                    })
                }
            },
            error: function(xhr, status) {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo completar la operación',
                    text: 'Revise su conexión a internet'
                    // footer: ''
                });
                input.val(0);
                input.css("background-color", "#f7b4bb");
            },
            complete: function(respuesta) {
                $('#tabla-cierre-inventario').DataTable().ajax.reload(null, false);
            }

        })

    }
    // $('.stock_real').on('keyup', setStockReal());

    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
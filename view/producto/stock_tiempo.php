<h1 class="page-header">Lista de productos</h1>
<!--<a class="btn btn-primary" href="?c=transferencia_producto" class="btn btn-success">Transferencias de Productos</a>-->
<a style="display: none;" class="btn btn-primary pull-right" href="?c=transferencia">Transferencia</a>
<a class="btn btn-primary pull-right" href="#productoModal" data-toggle="modal" data-target="#crudModal" data-c="producto">Agregar</a>
<br><br><br>
<?php if ($_SESSION['nivel'] == 1) { ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <div align="center" id="filtro">
                    <form method="get">
                        <input type="hidden" name="c" value="producto">
                        <input type="hidden" name="a" value="stock">
                        <div class="form-group">
                            <div class="form-group col-md-8">
                                <label>Fecha</label>
                                <input type="date" name="fecha" value="<?php echo (isset($_GET['fecha'])) ? $_GET['fecha'] : ''; ?>" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <input type="submit" name="filtro" value="Filtrar" class="btn btn-success" style="margin-top: 25px;">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>
<?php } ?>
<table id="tabla" class="table responsive display" style="width:100%">
    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>id</th>
            <th>codigo</th>
            <th>producto</th>
            <th>costo</th>
            <th>stock</th>
        </tr>
    </thead>
    <tbody>
        <?php /*$sumaCosto=0;
            $q = (isset($_REQUEST['sucursal']))? $_REQUEST['sucursal']:"";
            foreach($this->model->ListarBuscar($q) as $r): 
            if(true){ ?>
            
            <tr class="click">
                <td><?php echo $r->codigo; ?></td>
                <td><?php echo substr($r->categoria,0,15); ?></td>
                <td><?php echo substr($r->categoria,0,15); ?></td>
                <td><a href="?c=venta&a=listarproducto&id_producto=<?php echo $r->id; ?>"><?php echo substr($r->producto,0,100); ?></a></td>
                <td><?php echo number_format($r->precio_costo,0,".",","); ?></td>
                <td><?php echo number_format($r->precio_minorista,0,".",","); ?></td>
                <td><?php echo $r->stock; ?></td>
                <td><?php echo $r->iva; ?></td>
                <?php if($_SESSION['nivel']<=1){ ?>
                <td>
                    <a class="btn btn-warning edit" href="#crudModal" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="producto">Edit</a>
                </td>
                <td>
                    <a class="btn btn-danger delete" href="?c=producto&a=Eliminar&id=<?php echo $r->id; ?>">Borrar</a>
                </td>
                <?php } ?>
            </tr>
            <?php $sumaCosto+=($r->precio_costo*$r->stock); } endforeach; */ ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #000; color:#fff">
            <th>id</th>
            <th>codigo</th>
            <th>producto</th>
            <th>costo</th>
            <th>stock</th>
        </tr>
    </tfoot>
</table>
<?php include("view/crud-modal.php"); ?>
<?php if (!isset($_SESSION)) session_start(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        $('#tabla tfoot th').each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });

        // DataTable
        let tablaUsuarios = $('#tabla').DataTable({
            "ajax": {
                "url": "?c=producto&a=ListarStockTiempo&fecha=<?php echo (isset($_GET['fecha'])) ? $_GET['fecha'] : ''; ?>",
                "dataSrc": ""
            },
            "columns": [
                { "data": "id" },
                { "data": "codigo" },
                { "data": "producto" },
                { "data": "precio_costo", render: $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "stock_total" }
            ],
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'portrait',
                    pageSize: 'LEGAL',
                    exportOptions: { columns: ':visible' }
                },
                'colvis'
            ],
            "stateSave": true,
            responsive: { details: true },
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página.",
                "search": "Buscar en todos",
                "buttons": { "colvis": "Columnas Visibles" },
                "zeroRecords": "Lo sentimos. No se encontraron registros.",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros aún.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "LoadingRecords": "Cargando ...",
                "Processing": "Procesando...",
                "SearchPlaceholder": "Comience a teclear...",
                "paginate": {
                    "previous": "Anterior",
                    "next": "Siguiente"
                }
            },
            initComplete: function() {
                this.api().columns().every(function() {
                    var that = this;
                    $('input', this.footer()).on('keyup change clear', function() {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
            }
        });

        $("#tabla tbody").on("click", ".btnBorrar", function() {
            let data = tablaUsuarios.row($(this).parents()).data();
            let id = data.id;
            let respuesta = confirm("¿Está seguro de borrar " + data.producto + "?");

            if (respuesta) {
                $.ajax({
                    url: "?c=producto&a=Eliminar&id=" + id,
                    method: "POST",
                    success: function(respuesta) {
                        tablaUsuarios.row($(this).parents('tr')).remove().draw();
                    }
                });
            }
        });

        $("#tabla tbody").on("click", ".btnEditar", function() {
            let data = tablaUsuarios.row($(this).parents()).data();
            let id = data.id;
            $('#crudModal').modal('show');
            $.ajax({
                url: "?c=producto&a=obtener&id=" + id,
                method: "POST",
                success: function(respuesta) {
                    $("#edit_form").html(respuesta);
                }
            });
        });
    });
</script>

<style type="text/css">
    tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
        color: black;
    }
</style>

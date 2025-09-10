<h1 class="page-header">Lista de transferencias &nbsp;
</h1>
<a class="btn btn-primary pull-right" href="?c=salida_tmp" class="btn btn-success">Nueva Transferencia</a>
<br><br><br>

<h3 id="filtrar" align="center">Filtros <i class="fas fa-angle-right"></i><i class="fas fa-angle-left" style="display: none"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="get">
                <input type="hidden" name="c" value="salida">

                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php //echo (isset($_GET['desde']))? $_GET['desde']:''; 
                                                            ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php //echo (isset($_GET['hasta']))? $_GET['hasta']:''; 
                                                            ?>" class="form-control">
                </div>

                <div class="form-group col-md-2">
                    <label></label>
                    <input type="submit" value="Filtrar" class="form-control btn btn-success">
                </div>

            </form>
        </div>
    </div>
</div>
<!--<table class="table table-striped table-bordered display responsive nowrap " id="tabla" width="100%">-->
<table id="tabla" class="table table-striped table-bordered display responsive " width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha y Hora</th>
            <th>Nro. comprobante</th>
            <th>Costo</th>
            <th></th>
            <th></th>
    </thead>
    <tbody>
        <?php ?>
    </tbody>

</table>
</div>
</div>
</div>

<?php include("view/crud-modal.php"); ?>
<?php //include("view/salida/detalles-modal.php");
if (!isset($_SESSION)) session_start();
?>
<script type="text/javascript">
    $(document).ready(function() {

        let tablaUsuarios = $('#tabla').DataTable({

            "dom": 'Bfrtip',
            "buttons": [{
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            }, {
                extend: 'pdfHtml5',
                footer: true,
                title: "Gastos",
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6, 7, 9]
                }
            }, 'colvis'],

            responsive: {
                details: true
            },
            "sort": false,
            <?php if (isset($_GET['desde'])) { ?> "ajax": {
                    "url": "?c=salida&a=ListarFiltros&desde=<?php echo $_GET['desde'] ?>&hasta=<?php echo $_GET['hasta'] ?>",
                    "dataSrc": ""
                },
            <?php } else { ?>

                "ajax": {
                    "url": "?c=salida&a=ListarAjax",
                    "dataSrc": ""
                },
            <?php } ?>

            "columns": [{
                    "data": "id_venta"
                },
                {
                    "data": "nombre_cli"
                },
                {
                    "data": "fecha_venta"
                },

                {
                    "data": "nro_comprobante"
                },

                <?php
                if ($_SESSION['nivel'] <> 4) { ?> {
                        "data": "costo",
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                <?php } ?>

                {
                    "defaultContent": "",
                    render: function(data, type, row) {

                        return "<a href='#detallesModal' class='btn btn-info' data-toggle='modal' data-target='#detallesModal' data-c='venta' data-id='" + row.id_venta + "'>Ver</a>";

                    }
                },



                <?php
                if ($_SESSION['nivel'] == 1) { ?> {
                        "defaultContent": "",
                        render: function(data, type, row) {
                            if (row.anulado == 1) {
                                return 'ANULADO'
                            } else {

                                let link = "?c=venta&a=anular&id=" + row.id_venta;
                                return '<a href="' + link + '" class="btn btn-danger">Eliminar</a>';

                            }

                        }
                    }
                <?php } ?>

            ],

        });
    });
</script>
<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });

    $('#editarVentaModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id');
        var n = button.data('n');
        var co = button.data('co');
        var cli = button.data('cli');
        $('#tipo').val(id);
        $('#n').val(n);
        $('#co').val(co);
        //$('#cli').val(cli);
        $('#cli option[value="' + cli + '"]').prop("selected", true);
        $('.selectpicker').selectpicker('refresh');
        $('.selectpicker').selectpicker('refresh');

    })
</script>
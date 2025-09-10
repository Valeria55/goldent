<h1 class="page-header">Lista de transferencias de productos enviadas &nbsp;
    <!--<a class="btn btn-primary" href="#diaModal" class="btn btn-primary" data-toggle="modal" data-target="#diaModal">Informe diario</a>
<a class="btn btn-primary" href="#mesModal" class="btn btn-primary" data-toggle="modal" data-target="#mesModal">Informe Mensual</a>-->
</h1>
<a class="btn btn-primary pull-right" href="?c=transferencia_producto_tmp" class="btn btn-success">Nueva transferencia</a>
<br><br><br>

<!--<table class="table table-striped table-bordered display responsive nowrap " id="tabla" width="100%">-->
<table id="tabla" class="table table-striped table-bordered display responsive " width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Usuario</th>
            <th>Fecha y Hora</th>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>


    </thead>
    <tbody>

    </tbody>

</table>

</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/transferencia_producto/mes-modal.php"); ?>
<?php include("view/transferencia_producto/dia-modal.php"); ?>
<?php include("view/transferencia_producto/detalles-modal.php");
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
                    "url": "?c=transferencia_producto&a=ListarFiltros&desde=<?php echo $_GET['desde'] ?>&hasta=<?php echo $_GET['hasta'] ?>",
                    "dataSrc": ""
                },
            <?php } else { ?>

                "ajax": {
                    "url": "?c=transferencia_producto&a=ListarAjax",
                    "dataSrc": ""
                },
            <?php } ?>

            "columns": [{
                    "data": "id_transferencia_producto"
                },
                {
                    "data": "user"
                },
                {
                    "data": "fecha_transferencia_producto"
                },
                {
                    "data": "total",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                <?php
                if (($_SESSION['nivel'] == 1) || ($_SESSION['nivel'] == 4) || ($_SESSION['nivel'] == 2)) { ?> {
                        "defaultContent": "",
                        render: function(data, type, row) {

                            let link = "?c=transferencia_producto&a=TransferenciaPdf&id=" + row.id_transferencia_producto;
                            return '<a href="' + link + '" class="btn btn-primary">Imprimir</a>';
                        }
                    },
                <?php } ?>
                <?php
                if (($_SESSION['nivel'] == 1) || ($_SESSION['nivel'] == 4) || ($_SESSION['nivel'] == 2)) { ?> {
                        "defaultContent": "",
                        render: function(data, type, row) {
                            if (row.estado == 'finalizado') {
                                return 'Finalizado'
                            } else if (row.estado == 'pendiente') {
                                let link = "?c=transferencia_producto&a=CancelarTransferencia&id_transferencia_producto=" + row.id_transferencia_producto;
                                let btn = '<a onclick="return confirm(`Desea cancelar la transferencia?`)" href="' + link + '" class="btn btn-danger">Cancelar</a>';
                                return 'Pendiente<br>' + btn;
                            } else if (row.estado == 'cancelado') {
                                return 'Cancelado';
                            }
                        }
                    },
                <?php } ?> {
                    "defaultContent": "",
                    render: function(data, type, row) {


                        return "<a href='#transferencia_productoModal' class='btn btn-info' data-toggle='modal' data-target='#transferencia_productoModal' data-c='transferencia_producto' data-rec='0' data-id='" + row.id_transferencia_producto + "'>Ver</a>";

                    }
                }

            ],

        });
    });
</script>
<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
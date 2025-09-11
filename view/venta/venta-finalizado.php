<table id="tabla1" class="table table-striped table-bordered display responsive nowrap" width="100%">
    <div style="height: 45px;"></div>
    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha y Hora</th>
            <th>Comprobante</th>
            <th>Nro. comprobante</th>
            <th>Pago</th>
            <?php if (!isset($_SESSION)) session_start();
            if ($_SESSION['nivel'] == 1) { ?>
                <th>Costo</th>
            <?php } ?>
            <th>Total</th>
            <?php if (!isset($_SESSION)) session_start();
            if ($_SESSION['nivel'] == 1) { ?>
                <th>Ganancia</th>
            <?php } ?>
            <th></th>
            <th></th>
            <th></th>
            <?php if (!isset($_SESSION)) session_start();
            if ($_SESSION['nivel'] == 1) { ?>
                <th></th>
            <?php } ?>
    </thead>
    <tbody>
        <?php  ?>
    </tbody>
    </div>
</table>


<?php
if (!isset($_SESSION)) session_start();
?>
<script type="text/javascript">
    $(document).ready(function() {

        let tablaUsuarios = $('#tabla1').DataTable({

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
                    "url": "?c=venta&a=ListarFiltros&desde=<?php echo $_GET['desde'] ?>&hasta=<?php echo $_GET['hasta'] ?>",
                    "dataSrc": ""
                },
            <?php } else { ?>

                "ajax": {
                    "url": "?c=venta&a=ListarAjax",
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
                    "data": "",
                    render: function(data, type, row) {

                        if (row.comprobante == 'Ticket') {
                            return 'Ticket';
                        } else if (row.comprobante == 'TicketSi') {
                            return 'Sin Impresión';
                        } else {
                            return 'Factura';
                        }
                    }
                },
                {
                    "data": "nro_comprobante"
                },
                {
                    "data": "contado",
                },
                <?php
                if ($_SESSION['nivel'] == 1) { ?> {
                        "data": "costo",
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                <?php } ?> {
                    "data": "total",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },

                <?php
                if ($_SESSION['nivel'] == 1) { ?> {
                        "data": "ganancia",
                        render: $.fn.dataTable.render.number(',', '.', 0)
                    },
                <?php } ?> {
                    "defaultContent": "",
                    render: function(data, type, row) {

                        return "<a href='#detallesModal' class='btn btn-info' data-toggle='modal' data-target='#detallesModal' data-c='venta' data-id='" + row.id_venta + "'>Ver</a>";

                    }
                },

                {
                    "defaultContent": "",
                    render: function(data, type, row) {

                        if (row.comprobante == 'Ticket') {
                            return "<a href='#editarVentaModal' class='btn btn-primary' data-toggle='modal' data-target='#editarVentaModal' data-id='" + row.id_venta + "'data-n='" + row.nro_comprobante + "'data-co='" + row.comprobante + "'data-cli='" + row.id_cliente + "'>Ticket</a>";
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "defaultContent": "",
                    render: function(data, type, row) {

                        if (row.comprobante == 'Factura') {
                            return "<a href='#editarVentaModal' class='btn btn-primary' data-toggle='modal' data-target='#editarVentaModal' data-id='" + row.id_venta + "'data-n='" + row.nro_comprobante + "'data-co='" + row.comprobante + "'data-cli='" + row.id_cliente + "'>Factura</a>";
                        } else {
                            return '';
                        }
                    }
                }

                <?php
                if ($_SESSION['nivel'] == 1) { ?>, {
                        "defaultContent": "",
                        render: function(data, type, row) {
                            if (row.anulado == 1) {
                                return 'ANULADO'
                            } else {

                                let link = "?c=venta&a=anular&id=" + row.id_venta;
                                return '<a href="' + link + '" class="btn btn-danger" onclick="return confirm(\'¿Seguro de eliminar este registro?\');">Eliminar</a>';

                            }

                        }
                    },
                <?php } ?>



            ],

        });
    });
</script>

<script type="text/javascript">
    // $("#filtrar").click(function() {
    //     $("#filtro1").toggle("slow");
    //     $("i").toggle();
    // });

    // $('#editarVentaModal').on('show.bs.modal', function(event) {
    //     var button = $(event.relatedTarget); // Button that triggered the modal
    //     var id = button.data('id');
    //     var n = button.data('n');
    //     var co = button.data('co');
    //     var cli = button.data('cli');
    //     $('#tipo').val(id);
    //     $('#n').val(n);
    //     $('#co').val(co);
    //     //$('#cli').val(cli);
    //     $('#cli option[value="' + cli + '"]').prop("selected", true);
    //     $('.selectpicker').selectpicker('refresh');
    //     $('.selectpicker').selectpicker('refresh');

    // })
</script>

<table id="tabla1" class="table table-striped table-bordered display responsive nowrap" width="100%">
    <style>
        /* Resaltar ventas anuladas */
        tr.venta-anulada > td {
            background-color: #f8d7da !important;
            color: #721c24 !important;
        }
    </style>
    <div style="height: 45px;"></div>
    <thead>
        <tr style="background-color: black; color:#fff">
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha y Hora</th>
            <th>Comprobante</th>
            <th>Nro. comprobante</th>
            <th>Factura</th>
            <?php if (!isset($_SESSION)) session_start();
            if ($_SESSION['nivel'] == 1) { ?>
                <th>Costo</th>
            <?php } ?>
            <th>Total</th>
            <?php if (!isset($_SESSION)) session_start();
            if ($_SESSION['nivel'] == 1) { ?>
                <th>Ganancia</th>
            <?php } ?>
            <th>Acciones</th>
            <?php if (!isset($_SESSION)) session_start();
            if ($_SESSION['nivel'] == 1) { ?>
                <!-- Columna extra eliminada, ahora todo esta en Acciones -->
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

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

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
            <?php
            $filtroDesde = $_GET['desde'] ?? '';
            $filtroHasta = $_GET['hasta'] ?? '';
            $filtroCliente = $_GET['id_cliente'] ?? '';
            $extraCliente = (!empty($filtroCliente)) ? '&id_cliente=' . urlencode($filtroCliente) : '';
            if (isset($_GET['desde']) || isset($_GET['hasta']) || isset($_GET['id_cliente'])) {
            ?> "ajax": {
                    "url": "?c=venta&a=ListarFiltros&desde=<?php echo $filtroDesde ?>&hasta=<?php echo $filtroHasta ?><?php echo $extraCliente; ?>",
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
                    "data": null,
                    render: function(data, type, row) {
                        if (type !== 'display') {
                            return row.nombre_cli;
                        }

                        var idCliente = row.id_cliente ?? '';
                        var nombreCliente = escapeHtml(row.nombre_cli ?? '');
                        var url = "?c=venta&a=listarcliente&id_cliente=" + encodeURIComponent(idCliente);
                        return "<a class='btn btn-default btn-xs' href='" + url + "'>" + nombreCliente + "</a>";
                    }
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
                    "data": "condicion_factura",
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
                <?php } ?> 
                {
                    "data": null,
                    "orderable": false,
                    "width": "160px", 
                    render: function(data, type, row) {
                        let botones = '<div class="action-grid">';

                        // 1. Ver Detalles
                        botones += "<a href='#detallesModal' class='btn btn-info action-btn' data-toggle='modal' data-target='#detallesModal' data-c='venta' data-id='" + row.id_venta + "' title='Ver Detalles'><i class='fas fa-eye'></i> Ver</a>";

                        // 2. Editar (Solo Guardar Datos)
                        botones += "<a href='#editarVentaModal' class='btn btn-warning action-btn' data-toggle='modal' data-target='#editarVentaModal' data-id='" + row.id_venta + "' data-pagare='" + row.pagare + "' data-n='" + row.nro_comprobante + "' data-co='" + row.comprobante + "' data-contado='" + row.contado + "' data-cli='" + row.id_cliente + "' title='Editar'><i class='fas fa-edit'></i> Editar</a>";

                        // 3. Imprimir (Factura o Ticket)
                        if (row.comprobante == 'Factura') {
                             botones += "<a href='?c=venta&a=factura&id=" + row.id_venta + "' target='_blank' class='btn btn-primary action-btn' title='Imprimir Factura'><i class='fas fa-print'></i> Factura</a>";
                        } else if (row.comprobante == 'Ticket') {
                             botones += "<a href='?c=venta&a=ticket&id=" + row.id_venta + "' target='_blank' class='btn btn-primary action-btn' title='Imprimir Ticket'><i class='fas fa-receipt'></i> Ticket</a>";
                        } else {
                            // Espacio vacío si no hay comprobante imprimible
                             botones += "<div></div>"; 
                        }

                        // 4. Pagaré (Si corresponde y es Crédito)
                        if (row.pagare == 1 && row.contado == 'Credito') {
                            botones += "<a href='javascript:void(0)' class='btn btn-success action-btn' onclick='abrirVentanaFlotante(\"?c=venta&a=Pagare&id=" + row.id_venta + "\", \"Pagaré\")' title='Ver Pagaré'><i class='fas fa-file-contract'></i> Pagaré</a>";
                        } else {
                             botones += "<div></div>";
                        }

                        // 5. Orden Delivery
                        botones += "<a href='javascript:void(0)' class='btn btn-dark action-btn' onclick='abrirVentanaFlotante(\"?c=venta&a=OrdenDelivery&id=" + row.id_venta + "\", \"Orden de Entrega\")' title='Orden Delivery'><i class='fas fa-truck'></i>Nota de remisión</a>";
                        
                        <?php if ($_SESSION['nivel'] == 1) { ?>
                        // 6. Eliminar (Admin)
                            if (row.anulado == 1) {
                                botones += "<span class='badge badge-danger action-btn'>Anulado</span>";
                            } else {
                                let link = "?c=venta&a=anular&id=" + row.id_venta;
                                botones += '<a href="' + link + '" class="btn btn-danger action-btn" onclick="return confirm(\'¿Seguro de eliminar este registro?\');" title="Eliminar"><i class="fas fa-trash"></i> Eliminar</a>';
                            }
                        <?php } else { ?>
                             botones += "<div></div>";
                        <?php } ?>

                        botones += '</div>'; // Cierra grid
                        return botones;
                    }
                }
            ],

            "createdRow": function(row, data, dataIndex) {
                if (data && (data.anulado == 1 || data.anulado === '1')) {
                    $(row).addClass('venta-anulada');
                }
            }

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

<script type="text/javascript">
function abrirVentanaFlotante(url, titulo) {
    // Configuración de la ventana flotante
    var width = 900;
    var height = 700;
    
    // Calcular posición centrada en la pantalla
    var left = (screen.width / 2) - (width / 2);
    var top = (screen.height / 2) - (height / 2);
    
    // Características de la ventana
    var features = 'width=' + width + 
                   ',height=' + height + 
                   ',left=' + left + 
                   ',top=' + top + 
                   ',scrollbars=yes' +
                   ',resizable=yes' +
                   ',toolbar=no' +
                   ',menubar=no' +
                   ',location=no' +
                   ',status=no';
    
    // Abrir la ventana flotante
    var ventanaFlotante = window.open(url, titulo.replace(/\s+/g, '_'), features);
    
    // Enfocar la ventana si no está bloqueada
    if (ventanaFlotante) {
        ventanaFlotante.focus();
    } else {
        alert('Por favor, permite las ventanas emergentes para ver el documento PDF.');
    }
}
</script>
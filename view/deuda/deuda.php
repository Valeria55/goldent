<h1 class="page-header">Lista de deudas
    <a class="btn btn-primary" href="#clienteModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="cliente">+ Cliente</a>
    <a class="btn btn-primary pull-right" href="#deudaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="deuda">Agregar</a>
</h1>
<ul id="tab-list" class="nav nav-tabs">
    <!-- <li id="deudores-tab" class=""><a href="#">Deudores</a></li> -->
    <li id="agrupados-tab" class="active"><a href="#">Por Cliente</a></li>
    <li id="pagados-tab" class=""><a href="#">Pagados</a></li>
    <!-- <li id="recibos-tab" class=""><a href="#">Recibos</a></li> -->
</ul>


<!--
  *************************************************************************
  *                                                                       *
  *   TABLA PARA DEUDORES *
  *                                                                       *
  *************************************************************************
-->
<div id="deudores-content" style="display: none;">
    <table id="deudores-table" class="table table-striped table-bordered display responsive nowrap datatable">
        <a class="btn btn-primary pull-right" href="#deudaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="deuda">Agregar</a>
        <br><br><br>

        <thead>
            <tr style="background-color: black; color:#fff">
                <th></th>
                <th>Cliente</th>
                <th>Concepto</th>
                <th>Comprobante</th>
                <th>Monto</th>
                <th>Saldo</th>
                <th>Fecha</th>
                <th>Vencimiento</th>
                <th></th>
            </tr>
        </thead>

        <!-- tbody para Deudores -->
        <tbody>
            <?php
            $suma = 0;
            $saldo = 0;
            foreach ($this->model->Listar() as $r) : ?>
                <tr class="click">
                    <td>
                        <div align="center"><a class="btn btn-primary " href="#cobrarModal" class="btn btn-success" data-toggle="modal" data-target="#cobrarModal" data-id="<?php echo $r->id; ?>">Cobrar</a></div>
                    </td>
                    <!-- ... resto de las columnas de la tabla para Deudores ... -->
                    <td><a class="btn btn-default" href="#rangoModal" class="btn btn-success" data-toggle="modal" data-target="#rangoModal" data-id="<?php echo $r->id_cliente; ?>"><?php echo $r->nombre; ?></a>
                    </td>
                    <td><?php echo $r->concepto; ?></td>
                    <td><?php echo $r->nro_comprobante ? $r->nro_comprobante : '-'; ?></td>
                    <td style="padding-right:2px" align="right"><?php echo number_format($r->monto, 0, ",", ","); ?></td>
                    <td style="padding-right:2px" align="right"><?php echo number_format($r->saldo, 0, ",", ","); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                    <td><?php echo (date("Y", strtotime($r->vencimiento)) > 2000) ? date("d/m/Y", strtotime($r->vencimiento)) : ""; ?></td>
                    <?php if ($r->id_venta) : ?>
                        <td>
                            <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id; ?>">Cobros</a>
                            <a href="#detallesModal" class="btn btn-default" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta; ?>">Venta</a>
                        </td>
                    <?php else : ?>
                        <td>
                            <a href="#cobrosModal" class="btn btn-success" data-toggle="modal" data-target="#cobrosModal" data-id="<?php echo $r->id; ?>">Cobros</a>
                            <a class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id; ?>" data-c="deuda">Editar</a>
                            <!-- <a class="btn btn-danger" onclick="javascript:return confirm('¿Seguro de eliminar este registro?');" href="?c=deuda&a=Eliminar&id=<?php //echo $r->id; 
                                                                                                                                                                    ?>">Eliminar</a> -->
                        </td>
                    <?php endif ?>
                </tr>
            <?php
                $suma += $r->monto;
                $saldo += $r->saldo;
            endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #666666; color:#fff">
                <td></td>
                <td></td>
                <td></td>
                <td style="padding-right:2px" align="right">TOTAL: </td>
                <td style="padding-right:2px" align="right"><?php echo number_format($suma, 0, ".", ","); ?></td>
                <td style="padding-right:2px" align="right"><?php echo number_format($saldo, 0, ".", ","); ?></td>
                <td></td>
                <td></td>
                <td></td>
        </tfoot>
    </table>

</div>

<!--
  *************************************************************************
  *                                                                       *
  *   VISTA AGRUPADA POR CLIENTE *
  *                                                                       *
  *************************************************************************
-->
<div id="agrupados-content">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>Buscar Cliente</h4>
                </div>
                <div class="panel-body">
                    <input type="text" id="buscar-cliente" class="form-control" placeholder="Buscar cliente...">
                    <br>
                    <div id="lista-clientes" style="max-height: 400px; overflow-y: auto;">
                        <div id="clientes-loader" style="text-align: center; padding: 50px; display: none;">
                            <i class="fa fa-spinner fa-spin fa-2x"></i>
                            <p>Cargando clientes...</p>
                        </div>
                        <div id="clientes-container">
                            <!-- Lista de clientes se carga aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 id="cliente-seleccionado-titulo">Selecciona un cliente</h4>
                </div>
                <div class="panel-body">
                    <div id="detalle-cliente" style="display: none;">
                        <!-- Resumen del cliente -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5><strong>Total Adeudado:</strong> <span id="total-adeudado" class="text-danger">0</span></h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pago Total:</label>
                                    <div class="input-group">
                                        <!-- <input type="number" id="pago-total" class="form-control" placeholder="Cantidad a pagar"> -->
                                        <span class="input-group-btn">
                                            <!-- <button class="btn btn-success" id="pagar-total" type="button">Pago Simple</button> -->
                                            <button class="btn btn-info" id="pagar-total-multiple" type="button">Múltiples Métodos</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Tabla de deudas del cliente -->
                        <div id="deudas-cliente-container">
                            <!-- Deudas específicas del cliente -->
                        </div>

                        <!-- Panel de pagos múltiples registrados -->
                        <div class="panel panel-warning" style="margin-top: 20px;">
                            <div class="panel-heading">
                                <h5><i class="fa fa-history"></i> Pagos Múltiples Registrados</h5>
                            </div>
                            <div class="panel-body">
                                <div id="pagos-multiples-container">
                                    <div class="text-center text-muted">
                                        <i class="fa fa-info-circle"></i> Selecciona un cliente para ver sus pagos múltiples
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenedor de Recibos Anulados -->
                        <div class="panel panel-danger" style="margin-top: 20px;">
                            <div class="panel-heading">
                                <h5><i class="fa fa-ban"></i> Recibos Anulados</h5>
                            </div>
                            <div class="panel-body">
                                <div id="recibos-anulados-pagados-container">
                                    <div class="text-center text-muted">
                                        <i class="fa fa-info-circle"></i> Selecciona un cliente para ver sus recibos anulados
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="mensaje-seleccionar" class="text-center" style="padding: 50px;">
                        <i class="fa fa-hand-pointer-o fa-3x text-muted"></i>
                        <p class="text-muted">Selecciona un cliente de la lista para ver sus deudas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--
  *************************************************************************
  *                                                                       *
  *   CONTENEDOR PARA TABLA PAGADOS (CARGADA DINÁMICAMENTE) *
  *                                                                       *
  *************************************************************************
-->
<div id="pagados-content" style="display: none;">
    <div id="pagados-loader" style="text-align: center; padding: 50px; display: none;">
        <i class="fa fa-spinner fa-spin fa-2x"></i>
        <p>Cargando datos de pagados...</p>
    </div>
    <div id="pagados-table-container">
        <!-- La tabla se cargará aquí dinámicamente -->
    </div>
</div>

<!--
  *************************************************************************
  *                                                                       *
  *   CONTENEDOR PARA RECIBOS *
  *                                                                       *
  *************************************************************************
-->
<div id="recibos-content" style="display: none;">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>Buscar Cliente para Recibos</h4>
                </div>
                <div class="panel-body">
                    <input type="text" id="buscar-cliente-recibo" class="form-control" placeholder="Buscar cliente...">
                    <br>
                    <div id="lista-clientes-recibo" style="max-height: 400px; overflow-y: auto;">
                        <div id="clientes-recibo-loader" style="text-align: center; padding: 50px; display: none;">
                            <i class="fa fa-spinner fa-spin fa-2x"></i>
                            <p>Cargando clientes...</p>
                        </div>
                        <div id="clientes-recibo-container">
                            <!-- Lista de clientes para recibos se carga aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4 id="cliente-recibo-titulo">Selecciona un cliente</h4>
                </div>
                <div class="panel-body">
                    <div id="recibos-lista-container">
                        <div class="text-center text-muted" style="padding: 50px;">
                            <i class="fa fa-receipt fa-3x"></i>
                            <p>Selecciona un cliente para ver sus recibos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/deuda/cobrar-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>
<?php include("view/deuda/cobros-modal.php"); ?>
<?php include("view/deuda/rango-modal.php"); ?>

<!-- Modal para cobro específico -->
<div class="modal fade" id="cobroEspecificoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Cobro Específico</h4>
            </div>
            <div class="modal-body">
                <div id="cobro-especifico-content">
                    <!-- Contenido del formulario de cobro específico -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para pago total múltiple -->
<div class="modal fade" id="pagoTotalMultipleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pago Total con Múltiples Métodos</h4>
            </div>
            <div class="modal-body">
                <div id="pago-total-multiple-content">
                    <!-- Contenido del formulario de pago total múltiple -->
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#cobrarModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id');
        if (id > 0) {
            var url = "?c=deuda&a=cobrarModal&id=" + id;
        } else {
            var url = "?c=deuda&a=cobrar";
        }
        $.ajax({

            url: url,
            method: "POST",
            data: id,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                $("#modal-body").html(respuesta);
            }

        })
    })
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var pagadosCargados = false;
        var clientesCargados = false;
        var clienteSeleccionado = null;
        var metodosPago = []; // Variable global para almacenar métodos de pago
        var tiposCambio = {
            USD: 7500,
            RS: 1400,
            GS: 1
        }; // Variable global para tipos de cambio

        // Funciones utilitarias globales
        function formatearNumero(numero) {
            if (!numero) return '0';
            return parseFloat(numero).toLocaleString('es-PY', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function formatearFecha(fecha) {
            if (!fecha) return 'N/A';
            var date = new Date(fecha);
            return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function verRecibo(grupoPagoId) {
            var url = '?c=deuda&a=generarReciboPDF&grupo_pago_id=' + grupoPagoId;
            window.open(url, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,status=no');
        }

        function imprimirRecibo(grupoPagoId) {
            var url = '?c=deuda&a=generarReciboPDF&grupo_pago_id=' + grupoPagoId;
            var ventana = window.open(url, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,status=no');
            // El PDF se puede imprimir directamente desde el visor del navegador
        }

        function descargarRecibo(grupoPagoId) {
            var url = '?c=deuda&a=generarReciboPDF&grupo_pago_id=' + grupoPagoId + '&download=1';
            var link = document.createElement('a');
            link.href = url;
            link.download = 'Recibo_' + grupoPagoId + '.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function generarPDFAnuladoPagados(grupoPagoId) {
            var url = '?c=deuda&a=generarReciboPDF&grupo_pago_id=' + grupoPagoId + '&anulado=1';
            window.open(url, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,status=no');
        }

        // Cargar métodos de pago y tipos de cambio al inicio
        cargarMetodosPago();
        cargarTiposCambio();

        // Función para cargar tipos de cambio desde la base de datos
        function cargarTiposCambio() {
            $.ajax({
                url: '?c=deuda&a=obtenerTiposCambio',
                method: 'GET',
                cache: false,
                success: function(response) {
                    var resultado = JSON.parse(response);
                    if (resultado.success) {
                        tiposCambio = resultado.tipos_cambio;
                        // Actualizar los displays de tipos de cambio en la interfaz
                        actualizarDisplayTiposCambio();
                    }
                },
                error: function() {
                    console.error('Error al cargar tipos de cambio');
                }
            });
        }

        // Función para actualizar los displays de tipos de cambio
        function actualizarDisplayTiposCambio() {
            $('#tipo-cambio-usd').text(tiposCambio.USD);
            $('#tipo-cambio-rs').text(tiposCambio.RS);
            $('#tipo-cambio-usd-modal').text(tiposCambio.USD);
            $('#tipo-cambio-rs-modal').text(tiposCambio.RS);
        }

        // Función para cargar métodos de pago desde la base de datos
        function cargarMetodosPago() {
            $.ajax({
                url: '?c=deuda&a=obtenerMetodosPago',
                method: 'GET',
                cache: false,
                success: function(response) {
                    var resultado = JSON.parse(response);
                    if (resultado.success) {
                        metodosPago = resultado.metodos;
                    } else {
                        console.error('Error al cargar métodos de pago:', resultado.message);
                        // Fallback a métodos por defecto
                        metodosPago = [{
                                id: 1,
                                metodo: 'Efectivo'
                            },
                            {
                                id: 2,
                                metodo: 'Transferencia'
                            },
                            {
                                id: 3,
                                metodo: 'Cheque'
                            },
                            {
                                id: 4,
                                metodo: 'Tarjeta'
                            }
                        ];
                    }
                },
                error: function() {
                    console.error('Error al cargar métodos de pago');
                    // Fallback a métodos por defecto
                    metodosPago = [{
                            id: 1,
                            metodo: 'Efectivo'
                        },
                        {
                            id: 2,
                            metodo: 'Transferencia'
                        },
                        {
                            id: 3,
                            metodo: 'Cheque'
                        },
                        {
                            id: 4,
                            metodo: 'Tarjeta'
                        }
                    ];
                }
            });
        }

        // Función para generar opciones de métodos de pago
        function generarOpcionesMetodos(seleccionado = '') {
            var opciones = '<option value="">Método</option>';
            metodosPago.forEach(function(metodo) {
                var selected = (metodo.metodo === seleccionado) ? 'selected' : '';
                opciones += `<option value="${metodo.metodo}" ${selected}>${metodo.metodo}</option>`;
            });
            return opciones;
        }

        // Maneja el evento de clic en las pestañas
        $('#tab-list li').click(function() {
            $('#tab-list li').removeClass('active');
            $(this).addClass('active');

            if ($(this).attr('id') === 'deudores-tab') {
                $('#deudores-content').show();
                $('#agrupados-content').hide();
                $('#pagados-content').hide();
                $('#recibos-content').hide();
            } else if ($(this).attr('id') === 'agrupados-tab') {
                $('#deudores-content').hide();
                $('#agrupados-content').show();
                $('#pagados-content').hide();
                $('#recibos-content').hide();

                if (!clientesCargados) {
                    cargarListaClientes();
                }
            } else if ($(this).attr('id') === 'pagados-tab') {
                $('#deudores-content').hide();
                $('#agrupados-content').hide();
                $('#pagados-content').show();
                $('#recibos-content').hide();

                if (!pagadosCargados) {
                    cargarTablaPagados();
                }
            } else if ($(this).attr('id') === 'recibos-tab') {
                $('#deudores-content').hide();
                $('#agrupados-content').hide();
                $('#pagados-content').hide();
                $('#recibos-content').show();

                cargarListaClientesRecibo();
            }
        });

        // Función para cargar la lista de clientes con deudas
        function cargarListaClientes() {
            $('#clientes-loader').show();
            $('#clientes-container').empty();

            $.ajax({
                url: '?c=deuda&a=cargarClientesConDeudas',
                method: 'GET',
                cache: false,
                success: function(response) {
                    $('#clientes-loader').hide();
                    $('#clientes-container').html(response);
                    clientesCargados = true;

                    // Manejar clic en cliente
                    $('.cliente-item').click(function() {
                        var idCliente = $(this).data('id');
                        var nombreCliente = $(this).data('nombre');
                        seleccionarCliente(idCliente, nombreCliente);
                    });
                },
                error: function() {
                    $('#clientes-loader').hide();
                    $('#clientes-container').html('<div class="alert alert-danger">Error al cargar clientes.</div>');
                }
            });
        }

        // Función para seleccionar un cliente y cargar sus deudas
        function seleccionarCliente(idCliente, nombreCliente) {
            clienteSeleccionado = idCliente;
            $('#cliente-seleccionado-titulo').text('Deudas de: ' + nombreCliente);
            $('#mensaje-seleccionar').hide();
            $('#detalle-cliente').show();

            // Cargar deudas del cliente
            $.ajax({
                url: '?c=deuda&a=cargarDeudasCliente',
                method: 'GET',
                data: {
                    id_cliente: idCliente
                },
                cache: false,
                success: function(response) {
                    var data = JSON.parse(response);
                    $('#total-adeudado').text(data.total_formateado);
                    $('#deudas-cliente-container').html(data.tabla_html);

                    // Cargar pagos múltiples del cliente
                    cargarPagosMultiples(idCliente);
                },
                error: function() {
                    $('#deudas-cliente-container').html('<div class="alert alert-danger">Error al cargar deudas del cliente.</div>');
                }
            });
        }

        // Función para cargar pagos múltiples
        function cargarPagosMultiples(idCliente) {
            $.ajax({
                url: '?c=deuda&a=listarPagosMultiples',
                method: 'GET',
                data: {
                    id_cliente: idCliente
                },
                cache: false,
                success: function(response) {
                    console.log('Respuesta pagos múltiples:', response);
                    var data = JSON.parse(response);
                    console.log('Datos parseados:', data);

                    if (data.success && data.pagos.length > 0) {
                        var html = '<div class="table-responsive">';
                        html += '<table class="table table-bordered table-striped table-condensed">';
                        html += '<thead>';
                        html += '<tr style="background-color: #f39c12; color: white;">';
                        html += '<th>Recibo N°</th>';
                        html += '<th>Fecha</th>';
                        html += '<th>Métodos</th>';
                        html += '<th>Deudas</th>';
                        html += '<th>Total</th>';
                        html += '<th>Acciones</th>';
                        html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';

                        data.pagos.forEach(function(pago) {
                            html += '<tr>';
                            html += '<td><strong>' + (pago.nro_recibo || 'N/A') + '</strong></td>';
                            html += '<td>' + new Date(pago.fecha_pago).toLocaleDateString('es-PY') + '</td>';
                            html += '<td><small>' + pago.cantidad_metodos + ' método(s)</small></td>';
                            html += '<td><small>' + pago.deudas_afectadas.split(',').length + ' deuda(s)</small></td>';
                            html += '<td class="text-right"><strong>' + formatearNumero(pago.total_monto) + '</strong></td>';
                            html += '<td>';
                            html += '<div class="btn-group" role="group">';
                            html += '<button class="btn btn-xs btn-primary ver-recibo-btn" ';
                            html += 'data-grupo-id="' + pago.grupo_id + '" ';
                            html += 'title="Ver recibo PDF">';
                            html += '<i class="fa fa-file-pdf-o"></i> PDF';
                            html += '</button>';
                            html += '<button class="btn btn-xs btn-danger revertir-pago-btn" ';
                            html += 'data-grupo-id="' + pago.grupo_id + '" ';
                            html += 'data-total="' + pago.total_monto + '" ';
                            html += 'title="Revertir pago múltiple">';
                            html += '<i class="fa fa-undo"></i> Revertir';
                            html += '</button>';
                            html += '</div>';
                            html += '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody>';
                        html += '</table>';
                        html += '</div>';

                        $('#pagos-multiples-container').html(html);

                        // Cargar recibos anulados después de cargar pagos múltiples
                        cargarRecibosAnuladosEnPagados(idCliente);
                    } else {
                        $('#pagos-multiples-container').html(
                            '<div class="text-center text-muted">' +
                            '<i class="fa fa-info-circle"></i> No hay pagos múltiples registrados para este cliente' +
                            '</div>'
                        );

                        // Aún así cargar recibos anulados
                        cargarRecibosAnuladosEnPagados(idCliente);
                    }
                },
                error: function() {
                    $('#pagos-multiples-container').html(
                        '<div class="alert alert-danger">Error al cargar pagos múltiples</div>'
                    );

                    // En caso de error, aún intentar cargar recibos anulados
                    cargarRecibosAnuladosEnPagados(idCliente);
                }
            });
        }

        // Manejar reversión de pagos múltiples
        $(document).on('click', '.revertir-pago-btn', function() {
            var grupoId = $(this).data('grupo-id');
            var total = $(this).data('total');

            var mensaje = 'Esta acción revertirá el pago múltiple por ' + formatearNumero(total) + ' Gs.\n\n';
            mensaje += 'Se restaurarán los saldos de las deudas afectadas.\n';
            mensaje += '¿Está seguro de continuar?';

            if (confirm(mensaje)) {
                $.ajax({
                    url: '?c=deuda&a=revertirPagoMultiple',
                    method: 'POST',
                    data: {
                        grupo_pago_id: grupoId
                    },
                    success: function(response) {
                        var resultado = JSON.parse(response);
                        if (resultado.success) {
                            alert('Pago revertido correctamente');
                            // Recargar las deudas del cliente
                            if (clienteSeleccionado) {
                                var nombreCliente = $('#cliente-seleccionado-titulo').text().replace('Deudas de: ', '');
                                seleccionarCliente(clienteSeleccionado, nombreCliente);
                            }
                        } else {
                            alert('Error: ' + resultado.message);
                        }
                    },
                    error: function() {
                        alert('Error al revertir el pago');
                    }
                });
            }
        });

        // Manejar ver recibo desde pagos múltiples
        $(document).on('click', '.ver-recibo-btn', function() {
            var grupoId = $(this).data('grupo-id');
            verRecibo(grupoId);
        });

        // Manejar imprimir recibo desde pagos múltiples
        $(document).on('click', '.imprimir-recibo-btn', function() {
            var grupoId = $(this).data('grupo-id');
            imprimirRecibo(grupoId);
        });

        // Manejar descargar recibo desde pagos múltiples
        $(document).on('click', '.descargar-recibo-btn', function() {
            var grupoId = $(this).data('grupo-id');
            descargarRecibo(grupoId);
        });

        // Búsqueda de clientes
        $('#buscar-cliente').on('keyup', function() {
            var filtro = $(this).val().toLowerCase();
            $('.cliente-item').each(function() {
                var nombre = $(this).text().toLowerCase();
                if (nombre.indexOf(filtro) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Pago total (desde el más viejo al más nuevo)
        $('#pagar-total').click(function() {
            var cantidad = parseFloat($('#pago-total').val());
            if (!cantidad || cantidad <= 0) {
                alert('Por favor ingrese una cantidad válida');
                return;
            }

            if (!clienteSeleccionado) {
                alert('No hay cliente seleccionado');
                return;
            }

            if (confirm('¿Confirma el pago de ' + cantidad + ' con método simple (Efectivo)?')) {
                realizarPagoTotal(clienteSeleccionado, cantidad);
            }
        });

        // Pago total con múltiples métodos
        $('#pagar-total-multiple').click(function() {
            if (!clienteSeleccionado) {
                alert('No hay cliente seleccionado');
                return;
            }

            var totalAdeudado = parseFloat($('#total-adeudado').text().replace(/\./g, ''));
            abrirModalPagoTotalMultiple(totalAdeudado);
        });

        // Función para realizar pago total
        function realizarPagoTotal(idCliente, cantidad) {
            $.ajax({
                url: '?c=deuda&a=pagoTotal',
                method: 'POST',
                data: {
                    id_cliente: idCliente,
                    cantidad: cantidad
                },
                success: function(response) {
                    var resultado = JSON.parse(response);
                    if (resultado.success) {
                        alert('Pago realizado correctamente');
                        // Recargar las deudas del cliente
                        var nombreCliente = $('#cliente-seleccionado-titulo').text().replace('Deudas de: ', '');
                        seleccionarCliente(idCliente, nombreCliente);
                        $('#pago-total').val('');
                    } else {
                        alert('Error: ' + resultado.message);
                    }
                },
                error: function() {
                    alert('Error al procesar el pago');
                }
            });
        }

        // Función para cargar la tabla de pagados
        function cargarTablaPagados() {
            $('#pagados-loader').show();
            $('#pagados-table-container').empty();

            $.ajax({
                url: '?c=deuda&a=cargarPagados',
                method: 'GET',
                cache: false,
                success: function(response) {
                    $('#pagados-loader').hide();
                    $('#pagados-table-container').html(response);
                    pagadosCargados = true;

                    if ($.fn.DataTable.isDataTable('#pagados-table')) {
                        $('#pagados-table').DataTable().destroy();
                    }
                    $('#pagados-table').DataTable({
                        responsive: true,
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                        }
                    });
                },
                error: function() {
                    $('#pagados-loader').hide();
                    $('#pagados-table-container').html('<div class="alert alert-danger">Error al cargar los datos de pagados.</div>');
                }
            });
        }

        // Manejar cobro específico
        $(document).on('click', '.cobro-especifico-btn', function() {
            var idDeuda = $(this).data('id');
            var concepto = $(this).data('concepto');
            var saldo = $(this).data('saldo');
            var saldoNumerico = parseFloat(saldo.replace(/\./g, ''));

            $('#cobroEspecificoModal .modal-title').text('Cobrar: ' + concepto);

            var formulario = `
                <form id="form-cobro-especifico">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Concepto:</label>
                                <input type="text" class="form-control" value="${concepto}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Saldo pendiente:</label>
                                <input type="text" class="form-control" value="${saldo}" readonly>
                                <input type="hidden" id="saldo-numerico" value="${saldoNumerico}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h5>Métodos de Pago</h5>
                                </div>
                                <div class="panel-body">
                                    <div id="metodos-pago-container">
                                        <!-- Primer método de pago por defecto -->
                                        <div class="metodo-pago-item" data-index="0">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <select class="form-control metodo-select" required>
                                                        ${generarOpcionesMetodos()}
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select class="form-control moneda-select" required>
                                                        <option value="GS">Guaraníes</option>
                                                        <option value="USD">Dólares</option>
                                                        <option value="RS">Reales</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control cantidad-input" placeholder="Cantidad" step="0.01" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-metodo" disabled>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-top: 5px;">
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control observacion-input" placeholder="Observaciones (opcional)">
                                                </div>
                                            </div>
                                            <hr style="margin: 10px 0;">
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="button" class="btn btn-info btn-sm" id="agregar-metodo">
                                            <i class="fa fa-plus"></i> Agregar método de pago
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h5>Resumen</h5>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-condensed">
                                        <tr>
                                            <td><strong>Saldo pendiente:</strong></td>
                                            <td class="text-right" id="saldo-pendiente">${saldo}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total a pagar:</strong></td>
                                            <td class="text-right text-success" id="total-pagar">0</td>
                                        </tr>
                                        <tr style="border-top: 2px solid #ccc;">
                                            <td><strong>Saldo restante:</strong></td>
                                            <td class="text-right" id="saldo-restante">${saldo}</td>
                                        </tr>
                                    </table>
                                    
                                    <div id="tipo-cambio-info" style="margin-top: 10px; font-size: 12px;">
                                        <strong>Tipos de cambio:</strong><br>
                                        USD: <span id="tipo-cambio-usd">7500</span><br>
                                        RS: <span id="tipo-cambio-rs">1400</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="btn-realizar-pago" disabled>Realizar Pago</button>
                    </div>
                </form>
            `;

            $('#cobro-especifico-content').html(formulario);
            $('#cobroEspecificoModal').modal('show');

            // Variables para el control de métodos de pago
            var contadorMetodos = 1;
            // Usar tipos de cambio globales (ya cargados desde la base de datos)

            // Función para calcular totales
            function calcularTotales() {
                var totalPagar = 0;
                var saldoPendiente = parseFloat($('#saldo-numerico').val());

                $('.metodo-pago-item').each(function() {
                    var cantidad = parseFloat($(this).find('.cantidad-input').val()) || 0;
                    var moneda = $(this).find('.moneda-select').val();

                    if (cantidad > 0 && moneda) {
                        // Convertir a guaraníes
                        var cantidadEnGuaranies = cantidad * tiposCambio[moneda];
                        totalPagar += cantidadEnGuaranies;
                    }
                });

                var saldoRestante = saldoPendiente - totalPagar;

                // Actualizar displays
                $('#total-pagar').text(formatearNumero(totalPagar));
                $('#saldo-restante').text(formatearNumero(saldoRestante));

                // Cambiar color según el estado
                if (saldoRestante < 0) {
                    $('#saldo-restante').removeClass('text-success text-warning').addClass('text-danger');
                } else if (saldoRestante === 0) {
                    $('#saldo-restante').removeClass('text-danger text-warning').addClass('text-success');
                } else {
                    $('#saldo-restante').removeClass('text-danger text-success').addClass('text-warning');
                }

                // Habilitar/deshabilitar botón de pago
                $('#btn-realizar-pago').prop('disabled', totalPagar <= 0 || saldoRestante < 0);
            }

            // Función para formatear números
            function formatearNumero(numero) {
                return new Intl.NumberFormat('es-PY').format(Math.round(numero));
            }

            // Agregar nuevo método de pago
            $('#agregar-metodo').click(function() {
                var nuevoMetodo = `
                    <div class="metodo-pago-item" data-index="${contadorMetodos}">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control metodo-select" required>
                                    ${generarOpcionesMetodos()}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control moneda-select" required>
                                    <option value="GS">Guaraníes</option>
                                    <option value="USD">Dólares</option>
                                    <option value="RS">Reales</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control cantidad-input" placeholder="Cantidad" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-metodo">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 5px;">
                            <div class="col-md-12">
                                <input type="text" class="form-control observacion-input" placeholder="Observaciones (opcional)">
                            </div>
                        </div>
                        <hr style="margin: 10px 0;">
                    </div>
                `;

                $('#metodos-pago-container').append(nuevoMetodo);
                contadorMetodos++;

                // Habilitar botones de eliminar si hay más de un método
                if ($('.metodo-pago-item').length > 1) {
                    $('.remove-metodo').prop('disabled', false);
                }
            });

            // Eliminar método de pago
            $(document).on('click', '.remove-metodo', function() {
                $(this).closest('.metodo-pago-item').remove();

                // Deshabilitar botones de eliminar si solo queda uno
                if ($('.metodo-pago-item').length === 1) {
                    $('.remove-metodo').prop('disabled', true);
                }

                calcularTotales();
            });

            // Calcular totales en tiempo real
            $(document).on('input change', '.cantidad-input, .moneda-select', function() {
                calcularTotales();
            });

            // Manejar envío del formulario
            $('#form-cobro-especifico').on('submit', function(e) {
                e.preventDefault();
                realizarCobroEspecificoMultiple(idDeuda);
            });
        });

        // Función para realizar cobro específico con múltiples métodos
        function realizarCobroEspecificoMultiple(idDeuda) {
            var metodosPago = [];
            var totalPagar = 0;

            $('.metodo-pago-item').each(function() {
                var metodo = $(this).find('.metodo-select').val();
                var moneda = $(this).find('.moneda-select').val();
                var cantidad = parseFloat($(this).find('.cantidad-input').val()) || 0;
                var observaciones = $(this).find('.observacion-input').val();

                if (metodo && moneda && cantidad > 0) {
                    metodosPago.push({
                        metodo: metodo,
                        moneda: moneda,
                        cantidad: cantidad,
                        observaciones: observaciones
                    });

                    // Convertir a guaraníes para el total usando tipos de cambio globales
                    totalPagar += cantidad * tiposCambio[moneda];
                }
            });

            if (metodosPago.length === 0) {
                alert('Debe agregar al menos un método de pago válido');
                return;
            }

            $.ajax({
                url: '?c=deuda&a=cobroEspecificoMultiple',
                method: 'POST',
                data: {
                    id_deuda: idDeuda,
                    metodos_pago: JSON.stringify(metodosPago),
                    total_pagar: totalPagar
                },
                success: function(response) {
                    var resultado = JSON.parse(response);
                    if (resultado.success) {
                        alert('Pago realizado correctamente');
                        $('#cobroEspecificoModal').modal('hide');
                        // Recargar las deudas del cliente
                        if (clienteSeleccionado) {
                            var nombreCliente = $('#cliente-seleccionado-titulo').text().replace('Deudas de: ', '');
                            seleccionarCliente(clienteSeleccionado, nombreCliente);
                        }
                    } else {
                        alert('Error: ' + resultado.message);
                    }
                },
                error: function() {
                    alert('Error al procesar el pago');
                }
            });
        }

        // Función para abrir modal de pago total múltiple
        function abrirModalPagoTotalMultiple(totalAdeudado) {
            var saldoFormateado = new Intl.NumberFormat('es-PY').format(totalAdeudado);

            $('#pagoTotalMultipleModal .modal-title').text('Pago Total: ' + saldoFormateado + ' Gs');

            var formulario = `
                <form id="form-pago-total-multiple">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>Total adeudado:</strong> ${saldoFormateado} Gs<br>
                                <small>Este pago se distribuirá automáticamente desde la deuda más antigua a la más reciente.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h5>Métodos de Pago</h5>
                                </div>
                                <div class="panel-body">
                                    <div id="metodos-pago-total-container">
                                        <!-- Primer método de pago por defecto -->
                                        <div class="metodo-pago-total-item" data-index="0">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <select class="form-control metodo-total-select" required>
                                                        ${generarOpcionesMetodos('Efectivo')}
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select class="form-control moneda-total-select" required>
                                                        <option value="GS" selected>Guaraníes</option>
                                                        <option value="USD">Dólares</option>
                                                        <option value="RS">Reales</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control cantidad-total-input" placeholder="Cantidad" step="0.01" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-metodo-total" disabled>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-top: 5px;">
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control observacion-total-input" placeholder="Observaciones (opcional)">
                                                </div>
                                            </div>
                                            <hr style="margin: 10px 0;">
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="button" class="btn btn-info btn-sm" id="agregar-metodo-total">
                                            <i class="fa fa-plus"></i> Agregar método de pago
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h5>Resumen</h5>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-condensed">
                                        <tr>
                                            <td><strong>Total adeudado:</strong></td>
                                            <td class="text-right" id="total-adeudado-modal">${saldoFormateado}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total a pagar:</strong></td>
                                            <td class="text-right text-success" id="total-pagar-modal">0</td>
                                        </tr>
                                        <tr style="border-top: 2px solid #ccc;">
                                            <td><strong>Saldo restante:</strong></td>
                                            <td class="text-right" id="saldo-restante-modal">${saldoFormateado}</td>
                                        </tr>
                                    </table>
                                    
                                    <div id="tipo-cambio-info-modal" style="margin-top: 10px; font-size: 12px;">
                                        <strong>Tipos de cambio:</strong><br>
                                        USD: <span id="tipo-cambio-usd-modal">7500</span><br>
                                        RS: <span id="tipo-cambio-rs-modal">1400</span>
                                    </div>
                                    
                                    <div class="alert alert-warning" style="margin-top: 10px; font-size: 12px;">
                                        <strong>Nota:</strong> Si el pago es menor al total adeudado, se aplicará desde la deuda más antigua.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="btn-realizar-pago-total" disabled>Realizar Pago Total</button>
                    </div>
                </form>
            `;

            $('#pago-total-multiple-content').html(formulario);
            $('#pagoTotalMultipleModal').modal('show');

            // Variables para el control de métodos de pago total
            var contadorMetodosTotal = 1;
            // Usar tipos de cambio globales (ya cargados desde la base de datos)

            // Función para calcular totales del pago total
            function calcularTotalesTotal() {
                var totalPagar = 0;
                var totalAdeudadoNumerico = totalAdeudado;

                $('.metodo-pago-total-item').each(function() {
                    var cantidad = parseFloat($(this).find('.cantidad-total-input').val()) || 0;
                    var moneda = $(this).find('.moneda-total-select').val();

                    if (cantidad > 0 && moneda) {
                        var cantidadEnGuaranies = cantidad * tiposCambio[moneda];
                        totalPagar += cantidadEnGuaranies;
                    }
                });

                var saldoRestante = totalAdeudadoNumerico - totalPagar;

                $('#total-pagar-modal').text(formatearNumeroTotal(totalPagar));
                $('#saldo-restante-modal').text(formatearNumeroTotal(saldoRestante));

                if (saldoRestante < 0) {
                    $('#saldo-restante-modal').removeClass('text-success text-warning').addClass('text-danger');
                } else if (saldoRestante === 0) {
                    $('#saldo-restante-modal').removeClass('text-danger text-warning').addClass('text-success');
                } else {
                    $('#saldo-restante-modal').removeClass('text-danger text-success').addClass('text-warning');
                }

                $('#btn-realizar-pago-total').prop('disabled', totalPagar <= 0);
            }

            function formatearNumeroTotal(numero) {
                return new Intl.NumberFormat('es-PY').format(Math.round(numero));
            }

            $('#agregar-metodo-total').click(function() {
                var nuevoMetodoTotal = `
                    <div class="metodo-pago-total-item" data-index="${contadorMetodosTotal}">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control metodo-total-select" required>
                                    ${generarOpcionesMetodos()}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control moneda-total-select" required>
                                    <option value="GS">Guaraníes</option>
                                    <option value="USD">Dólares</option>
                                    <option value="RS">Reales</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control cantidad-total-input" placeholder="Cantidad" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-metodo-total">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 5px;">
                            <div class="col-md-12">
                                <input type="text" class="form-control observacion-total-input" placeholder="Observaciones (opcional)">
                            </div>
                        </div>
                        <hr style="margin: 10px 0;">
                    </div>
                `;

                $('#metodos-pago-total-container').append(nuevoMetodoTotal);
                contadorMetodosTotal++;

                if ($('.metodo-pago-total-item').length > 1) {
                    $('.remove-metodo-total').prop('disabled', false);
                }
            });

            $(document).on('click', '.remove-metodo-total', function() {
                $(this).closest('.metodo-pago-total-item').remove();

                if ($('.metodo-pago-total-item').length === 1) {
                    $('.remove-metodo-total').prop('disabled', true);
                }

                calcularTotalesTotal();
            });

            $(document).on('input change', '.cantidad-total-input, .moneda-total-select', function() {
                calcularTotalesTotal();
            });

            $('#form-pago-total-multiple').on('submit', function(e) {
                e.preventDefault();
                realizarPagoTotalMultiple();
            });
        }

        function realizarPagoTotalMultiple() {
            var metodosPago = [];
            var totalPagar = 0;

            $('.metodo-pago-total-item').each(function() {
                var metodo = $(this).find('.metodo-total-select').val();
                var moneda = $(this).find('.moneda-total-select').val();
                var cantidad = parseFloat($(this).find('.cantidad-total-input').val()) || 0;
                var observaciones = $(this).find('.observacion-total-input').val();

                if (metodo && moneda && cantidad > 0) {
                    metodosPago.push({
                        metodo: metodo,
                        moneda: moneda,
                        cantidad: cantidad,
                        observaciones: observaciones
                    });

                    // Usar tipos de cambio globales
                    totalPagar += cantidad * tiposCambio[moneda];
                }
            });

            if (metodosPago.length === 0) {
                alert('Debe agregar al menos un método de pago válido');
                return;
            }

            $.ajax({
                url: '?c=deuda&a=pagoTotalMultiple',
                method: 'POST',
                data: {
                    id_cliente: clienteSeleccionado,
                    metodos_pago: JSON.stringify(metodosPago),
                    total_pagar: totalPagar
                },
                success: function(response) {
                    var resultado = JSON.parse(response);
                    if (resultado.success) {
                        alert('Pago realizado correctamente');
                        $('#pagoTotalMultipleModal').modal('hide');
                        if (clienteSeleccionado) {
                            var nombreCliente = $('#cliente-seleccionado-titulo').text().replace('Deudas de: ', '');
                            seleccionarCliente(clienteSeleccionado, nombreCliente);
                        }
                        $('#pago-total').val('');
                    } else {
                        alert('Error: ' + resultado.message);
                    }
                },
                error: function() {
                    alert('Error al procesar el pago');
                }
            });
        }

        // =====================================
        // FUNCIONES PARA MANEJO DE RECIBOS
        // =====================================

        function cargarListaClientesRecibo() {
            $('#clientes-recibo-loader').show();
            $('#clientes-recibo-container').empty();

            $.ajax({
                url: '?c=deuda&a=cargarClientesConDeudas',
                method: 'GET',
                cache: false,
                success: function(response) {
                    $('#clientes-recibo-loader').hide();
                    // Modificar el HTML para usar IDs específicos de recibo
                    var htmlModificado = response.replace(/cliente-item/g, 'cliente-recibo-item');
                    $('#clientes-recibo-container').html(htmlModificado);

                    // Manejar clic en cliente para recibos
                    $('.cliente-recibo-item').click(function() {
                        var idCliente = $(this).data('id');
                        var nombreCliente = $(this).data('nombre');
                        seleccionarClienteRecibo(idCliente, nombreCliente);
                    });
                },
                error: function() {
                    $('#clientes-recibo-loader').hide();
                    $('#clientes-recibo-container').html('<div class="alert alert-danger">Error al cargar clientes.</div>');
                }
            });
        }

        function seleccionarClienteRecibo(idCliente, nombreCliente) {
            $('#cliente-recibo-titulo').text('Recibos de: ' + nombreCliente);
            cargarRecibosCliente(idCliente);
        }

        function cargarRecibosCliente(idCliente) {
            $.post('?c=deuda&a=obtenerRecibosCliente', {
                id_cliente: idCliente
            }, function(response) {
                console.log('Respuesta recibos:', response);
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        mostrarListaRecibos(data.recibos);
                    } else {
                        $('#recibos-lista-container').html('<div class="alert alert-danger">Error: ' + data.message + '</div>');
                    }
                } catch (e) {
                    console.error('Error al parsear respuesta:', e);
                    $('#recibos-lista-container').html('<div class="alert alert-danger">Error en la respuesta del servidor</div>');
                }
            }).fail(function() {
                $('#recibos-lista-container').html('<div class="alert alert-danger">Error de conexión al cargar recibos</div>');
            });
        }

        function mostrarListaRecibos(recibos) {
            console.log('Recibos recibidos:', recibos);

            if (!recibos || recibos.length === 0) {
                $('#recibos-lista-container').html(
                    '<div class="text-center text-muted" style="padding: 50px;">' +
                    '<i class="fa fa-receipt fa-2x"></i>' +
                    '<p>Este cliente no tiene recibos registrados</p>' +
                    '</div>'
                );
                return;
            }

            var html = '<div class="table-responsive">' +
                '<table class="table table-striped table-bordered">' +
                '<thead>' +
                '<tr style="background-color: #5cb85c; color: white;">' +
                '<th>Fecha</th>' +
                '<th>Recibo Nº</th>' +
                '<th>Total</th>' +
                '<th>Deudas</th>' +
                '<th>Usuario</th>' +
                '<th>Acciones</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';

            recibos.forEach(function(recibo) {
                html += '<tr>' +
                    '<td>' + formatearFecha(recibo.fecha_recibo) + '</td>' +
                    '<td>' + recibo.grupo_pago_id + '</td>' +
                    '<td class="text-right">' + formatearNumero(recibo.total_recibo) + '</td>' +
                    '<td class="text-center">' + recibo.cantidad_deudas + '</td>' +
                    '<td>' + recibo.usuario_nombre + '</td>' +
                    '<td>' +
                    '<div class="btn-group" role="group">' +
                    '<button class="btn btn-sm btn-primary" onclick="verRecibo(\'' + recibo.grupo_pago_id + '\')">' +
                    '<i class="fa fa-eye"></i> Ver PDF' +
                    '</button>' +
                    '</div>' +
                    '</td>' +
                    '</tr>';
            });

            html += '</tbody></table></div>';
            $('#recibos-lista-container').html(html);
        }

        // Función para cargar recibos anulados en la pestaña "Por Cliente"
        function cargarRecibosAnuladosEnPagados(idCliente) {
            $.ajax({
                url: '?c=deuda&a=obtenerRecibosAnulados',
                method: 'POST',
                data: {
                    id_cliente: idCliente
                },
                success: function(response) {
                    try {
                        var data = JSON.parse(response);
                        if (data.success) {
                            mostrarRecibosAnuladosPagados(data.recibos);
                        } else {
                            $('#recibos-anulados-pagados-container').html(
                                '<div class="text-center text-muted">' +
                                '<i class="fa fa-info-circle"></i> No hay recibos anulados para este cliente' +
                                '</div>'
                            );
                        }
                    } catch (e) {
                        $('#recibos-anulados-pagados-container').html(
                            '<div class="alert alert-warning">Error al procesar la respuesta</div>'
                        );
                    }
                },
                error: function() {
                    $('#recibos-anulados-pagados-container').html(
                        '<div class="alert alert-danger">Error al cargar recibos anulados</div>'
                    );
                }
            });
        }

        function mostrarRecibosAnuladosPagados(recibos) {
            if (!recibos || recibos.length === 0) {
                $('#recibos-anulados-pagados-container').html(
                    '<div class="text-center text-muted">' +
                    '<i class="fa fa-info-circle"></i> No hay recibos anulados para este cliente' +
                    '</div>'
                );
                return;
            }

            var html = '<div class="table-responsive">';
            html += '<table class="table table-bordered table-striped table-condensed">';
            html += '<thead>';
            html += '<tr style="background-color: #d9534f; color: white;">';
            html += '<th>Recibo N°</th>';
            html += '<th>Fecha</th>';
            html += '<th>Total</th>';
            html += '<th>Cant. Deudas</th>';
            html += '<th>Usuario</th>';
            html += '<th>Acciones</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            recibos.forEach(function(recibo) {
                html += '<tr style="background-color: #f2dede;">';
                html += '<td><strong>' + (recibo.nro_recibo || 'N/A') + '</strong></td>';
                html += '<td>' + formatearFecha(recibo.fecha_recibo) + '</td>';
                html += '<td class="text-right"><strong>' + formatearNumero(recibo.total_recibo) + '</strong></td>';
                html += '<td class="text-center">' + recibo.cantidad_deudas + '</td>';
                html += '<td>' + (recibo.usuario_nombre || 'N/A') + '</td>';
                html += '<td>';
                html += '<button class="btn btn-xs btn-danger btn-pdf-anulado" data-grupo-id="' + recibo.grupo_pago_id + '" title="Ver PDF del recibo anulado">';
                html += '<i class="fa fa-file-pdf-o"></i> PDF';
                html += '</button>';
                html += '</td>';
                html += '</tr>';
            });

            html += '</tbody>';
            html += '</table>';
            html += '</div>';

            $('#recibos-anulados-pagados-container').html(html);
        }

        // Manejo del buscador de clientes para recibos
        $('#buscar-cliente-recibo').on('input', function() {
            var filtro = $(this).val().toLowerCase();
            $('.cliente-recibo-item').each(function() {
                var nombre = $(this).text().toLowerCase();
                if (nombre.includes(filtro)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Event listener para botones PDF de recibos anulados
        $(document).on('click', '.btn-pdf-anulado', function() {
            var grupoPagoId = $(this).data('grupo-id');
            generarPDFAnuladoPagados(grupoPagoId);
        });

        // Cargar automáticamente la lista de clientes al inicializar la página
        // ya que "Por Cliente" es la pestaña activa por defecto
        cargarListaClientes();
    });
</script>
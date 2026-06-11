<style>
    /* para que los estilos del sidebar no obliguen a que iconos no deseados aparezcan en los tabs */
    a[aria-expanded="true"][data-toggle="tab"]::before {
        content: '';
    }

    a[aria-expanded="false"][data-toggle="tab"]::before {
        content: '';
    }
</style>

<style>
    .btn {
        padding: 0.8rem 1.5rem;
        border-radius: 25px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .quantity-control button {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        background: white;
        color: var(--primary);
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-group .btn.active {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    /* Grid de Acciones */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5px;
        width: 100%;
        min-width: 120px;
    }
    
    .action-btn {
        padding: 8px 12px !important; /* Más grandes */
        font-size: 13px; /* Texto más legible */
        font-weight: bold;
        text-align: center;
        width: 100%;
        border-radius: 4px;
        margin: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px; /* Espacio entre icono y texto */
    }
    
    .action-btn i {
        margin-right: 0;
        font-size: 14px; /* Iconos un poco más grandes */
    }
</style>
<div class="container">

    <h3 class="page-header">Lista de ventas &nbsp;
        <!-- <a class="btn btn-primary" href="#diaModal" class="btn btn-primary" data-toggle="modal" data-target="#diaModal">Informe diario</a>
    <a class="btn btn-primary" href="#mesModal" class="btn btn-primary" data-toggle="modal" data-target="#mesModal">Informe Mensual</a> -->
        <?php if ($_SESSION['nivel'] <> 2) : ?>
            <a class="btn btn-primary" href="#mesModalVendedor" class="btn btn-primary" data-toggle="modal" data-target="#mesModalVendedor">Ventas por cada vendedor</a>
        <?php endif; ?>
    </h3>

    <h3 id="filtrar" align="center">Filtros <i class="fas fa-angle-right"></i><i class="fas fa-angle-left" style="display: none"></i></h3>
    <div class="row">
        <div class="col-sm-12">
            <div align="center" id="filtro">
                <form method="get">
                    <input type="hidden" name="c" value="venta">

                   
                  
                    <div class="form-group col-md-2">
                        <label>Desde</label>
                        <input type="date" name="desde" value="<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : '';
                                                                ?>" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Hasta</label>
                        <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : '';
                                                                ?>" class="form-control">
                    </div>                     <div class="form-group col-md-2">
                        <label>Cliente</label>
                        <select name="id_cliente" class="selectpicker" data-live-search="true" data-width="100%" title="Todos">
                            <?php
                            $clienteSeleccionado = $_GET['id_cliente'] ?? '';

                            $selTodos = ((string)$clienteSeleccionado === '') ? 'selected' : '';
                            echo "<option value=\"\" {$selTodos}>Todos</option>";

                            // Muchos registros no tienen el flag `cliente=1` cargado, por eso ListarClientes() puede traer 1 solo.
                            // Para el filtro, listamos todos y excluimos proveedores.
                            $clientes = $this->cliente->Listar();

                            foreach ($clientes as $c) {
                                if (isset($c->proveedor) && (int)$c->proveedor === 1) {
                                    continue;
                                }
                                $selected = ((string)$clienteSeleccionado === (string)$c->id) ? 'selected' : '';
                                $nombre = htmlspecialchars($c->nombre, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                $ruc = htmlspecialchars((string)($c->ruc ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                $label = trim($nombre . (($ruc !== '') ? " - $ruc" : ''));
                                echo "<option value=\"{$c->id}\" {$selected}>{$label}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-md-2">
                        <label>Paciente</label>
                        <input type="text" name="paciente" value="<?php echo htmlspecialchars($_GET['paciente'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" class="form-control" placeholder="Nombre / apellido">
                    </div>

                    <div class="form-group col-md-2">
                        <label>Nro. Comprobante</label>
                        <input type="text" name="nro_comprobante" value="<?php echo htmlspecialchars($_GET['nro_comprobante'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" class="form-control" placeholder="Número de comprobante">
                    </div>

                    <div class="form-group col-md-2">
                        <label>Estado Factura</label>
                        <select name="sin_facturar" class="form-control">
                            <option value="">Todos</option>
                            <option value="1" <?php echo (isset($_GET['sin_facturar']) && $_GET['sin_facturar'] == '1') ? 'selected' : ''; ?>>Sin Facturar</option>
                        </select>
                    </div>

                    <div class="form-group col-md-1">
                        <label></label>
                        <input type="submit" value="Filtrar" class="form-control btn btn-success" style="padding: 6px 12px;">
                    </div>

                </form>
            </div>
        </div>
    </div>


    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">Ventas Contado</a></li>
        <?php //if (!isset($_SESSION)) session_start();
        //if ($_SESSION['nivel'] == 1) { ?>
            <!-- <li><a href="#tab2" data-toggle="tab">A aprobar</a></li> -->
        <?php //} ?>

        <!-- <li><a href="#tab3" data-toggle="tab">Aprobados</a></li> -->
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <br>
            <?php require_once 'venta-finalizado.php'; ?>
        </div>

        <!-- <div class="tab-pane" id="tab2">
            <br>
            <?php //require_once 'venta-a-aprobar.php'; ?>
        </div>
        <div class="tab-pane" id="tab3">
            <br>
            <?php //require_once 'venta-aprobado.php'; ?>
        </div> -->
    </div>
</div>
</div>
</div>
</div>


<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/mes-modal.php"); ?>
<?php include("view/venta/mes-modal-vendedores.php"); ?>
<?php include("view/venta/dia-modal.php"); ?>
<?php include("view/venta/editar_venta.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>


<script>
    $(document).ready(function() {
        // Select con búsqueda (bootstrap-select)
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }

        // Ocultar todas las tablas excepto la primera al cargar la página
        $("#tab2, #tab3").hide();

        // Verificar el nivel de usuario antes de mostrar la pestaña "tab2"
        <?php if (!isset($_SESSION)) session_start(); ?>
        <?php if ($_SESSION['nivel'] == 1) : ?>
            $('a[href="#tab2"]').parent('li').show(); // Mostrar la pestaña "tab2" si el nivel es 1
        <?php else : ?>
            $('a[href="#tab2"]').parent('li').hide(); // Ocultar la pestaña "tab2" si el nivel no es 1
        <?php endif; ?>

        // Manejar el cambio de pestañas
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var targetTab = $(e.target).attr("href"); // Obtener el ID de la pestaña seleccionada
            $(".tab-pane").hide(); // Ocultar todas las tablas
            $(targetTab).show(); // Mostrar la tabla correspondiente a la pestaña seleccionada
        });

        // Mostrar la tabla de la pestaña activa al cargar la página
        var activeTab = $('.nav-tabs li.active a').attr("href");
        $(activeTab).show();
    });
</script>

<script>
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
        var pagare = button.data('pagare');
        var contado = button.data('contado'); // Get payment method
        
        $('#tipo').val(id);
        $('#n').val(n);
        $('#co').val(co);
        $('#co_hidden').val(co);
        //$('#cli').val(cli);
        $('#cli option[value="' + cli + '"]').prop("selected", true);
        
        // Show/Hide Pagare field based on Payment Method
        if (contado == 'Credito') {
            $('#div_pagare_edit').show();
            if(pagare == 1 ){
                $('#pagare-edit').val(1);
            }else{
                $('#pagare-edit').val(0);
            }
        } else {
            $('#div_pagare_edit').hide();
            $('#pagare-edit').val(0); // Reset to No
        }
        
        $('.selectpicker').selectpicker('refresh');
        $('.selectpicker').selectpicker('refresh');

    })
</script>

<!-- Modal de Facturación Masiva -->
<div id="facturarMasivoModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #337ab7; color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
                <h4 class="modal-title"><i class="fas fa-file-invoice"></i> Generar Factura de Ventas Seleccionadas</h4>
            </div>
            <form id="form-facturar-masivo">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cliente:</label>
                        <input type="text" id="fm-cliente" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Ventas Seleccionadas (IDs):</label>
                        <input type="text" id="fm-ids-display" class="form-control" readonly>
                        <input type="hidden" name="ids_ventas" id="fm-ids-input">
                    </div>
                    <div class="form-group">
                        <label>Monto Total:</label>
                        <div class="input-group">
                            <span class="input-group-addon">Gs.</span>
                            <input type="text" id="fm-total" class="form-control" readonly style="font-weight: bold; font-size: 16px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Próximo Nro. Factura (Correlativo):</label>
                        <?php
                        $t_activo = $this->model->ObtenerTimbradoActivo();
                        $next_auto = $this->model->UltimoAutoimpresor()->autoimpresor + 1;
                        $nro_formateado = "No configurado";
                        if ($t_activo) {
                            $nro_formateado = str_pad($t_activo->establecimiento, 3, '0', STR_PAD_LEFT) . '-' .
                                              str_pad($t_activo->punto_expedicion, 3, '0', STR_PAD_LEFT) . '-' .
                                              str_pad($next_auto, 7, '0', STR_PAD_LEFT);
                        }
                        ?>
                        <input type="text" class="form-control" value="<?php echo $nro_formateado; ?>" readonly style="font-weight: bold; color: #d9534f;">
                    </div>
                    <div class="form-group">
                        <label>Fecha de Emisión (Factura): <span style="color: red;">*</span></label>
                        <input type="datetime-local" name="fecha_factura" id="fm-fecha" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción para la Factura (Concepto): <span style="color: red;">*</span></label>
                        <textarea name="factura_concepto" id="fm-concepto" class="form-control" rows="3" placeholder="Ej: Servicios de limpieza y mantenimiento del mes" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-factura"><i class="fas fa-check"></i> Generar Factura</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Mapa para almacenar ventas seleccionadas de forma persistente entre páginas
    var checkedSalesMap = {};

    // Función para actualizar la barra flotante de facturación
    function updateMassiveInvoiceBar() {
        var ids = Object.keys(checkedSalesMap);
        var total = 0;
        var clientName = "";

        ids.forEach(function(id) {
            var sale = checkedSalesMap[id];
            total += sale.total;
            clientName = sale.clienteNombre;
        });

        if (ids.length > 0) {
            $('#selected-count').text(ids.length);
            $('#selected-total').text(total.toLocaleString('es-ES'));
            $('#massive-invoice-bar').slideDown();

            // Rellenar campos del modal
            $('#fm-cliente').val(clientName);
            $('#fm-ids-display').val(ids.join(', '));
            $('#fm-ids-input').val(ids.join(','));
            $('#fm-total').val(total.toLocaleString('es-ES'));
        } else {
            $('#massive-invoice-bar').slideUp();
        }
    }

    // Toggle de todos los checkboxes en la página actual
    $(document).on('change', '#select-all-ventas', function() {
        var isChecked = $(this).prop('checked');
        $('.select-venta-chk').each(function() {
            $(this).prop('checked', isChecked);
            var id = $(this).data('id');
            var saleTotal = parseFloat($(this).data('total'));
            var cId = $(this).data('cliente');
            var cName = $(this).data('cliente-nombre');

            if (isChecked) {
                checkedSalesMap[id] = { total: saleTotal, cliente: cId, clienteNombre: cName };
            } else {
                delete checkedSalesMap[id];
            }
        });
        updateMassiveInvoiceBar();
    });

    // Checkbox individual
    $(document).on('change', '.select-venta-chk', function() {
        var isChecked = $(this).prop('checked');
        var id = $(this).data('id');
        var saleTotal = parseFloat($(this).data('total'));
        var cId = $(this).data('cliente');
        var cName = $(this).data('cliente-nombre');

        if (isChecked) {
            // Validar que pertenezca al mismo cliente (por seguridad adicional)
            var currentClientId = null;
            Object.keys(checkedSalesMap).forEach(function(key) {
                currentClientId = checkedSalesMap[key].cliente;
            });

            if (currentClientId !== null && currentClientId !== cId) {
                Swal.fire({
                    title: 'Error de Cliente',
                    text: 'No puedes facturar ventas de diferentes clientes juntas.',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Entendido'
                });
                $(this).prop('checked', false);
                return;
            }

            checkedSalesMap[id] = { total: saleTotal, cliente: cId, clienteNombre: cName };
        } else {
            delete checkedSalesMap[id];
        }

        // Actualizar select-all
        var allChecked = true;
        $('.select-venta-chk').each(function() {
            if (!$(this).prop('checked')) {
                allChecked = false;
            }
        });
        $('#select-all-ventas').prop('checked', allChecked && $('.select-venta-chk').length > 0);

        updateMassiveInvoiceBar();
    });

    // Envío del formulario de facturación
    $(document).on('submit', '#form-facturar-masivo', function(e) {
        e.preventDefault();
        
        var concept = $('#fm-concepto').val().trim();
        if (concept === '') {
            Swal.fire('Atención', 'Por favor ingresa una descripción para la factura.', 'warning');
            return;
        }

        var btn = $('#btn-submit-factura');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');

        $.ajax({
            url: '?c=venta&a=FacturarMasivo',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Generar Factura');
                if (response.success) {
                    $('#facturarMasivoModal').modal('hide');
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.message,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Ver PDF',
                        cancelButtonText: 'Cerrar'
                    }).then((result) => {
                        // Recargar tabla DataTable
                        if ($.fn.DataTable.isDataTable('#tabla1')) {
                            $('#tabla1').DataTable().ajax.reload();
                        }
                        // Abrir PDF si se solicita
                        if (result.value) {
                            abrirVentanaFlotante(response.redirect, 'Factura');
                        }
                    });
                    // Reset de selección
                    checkedSalesMap = {};
                    $('#select-all-ventas').prop('checked', false);
                    updateMassiveInvoiceBar();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Generar Factura');
                Swal.fire('Error', 'Ocurrió un error inesperado al procesar la solicitud.', 'error');
            }
        });
    });
</script>


<!-- <script>
$(document).ready(function() {
    // Guardar la URL base
    var baseUrl = "index.php?c=venta";

    // Manejar el cambio de pestañas
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var targetTab = $(e.target).attr("href"); // Obtener el ID de la pestaña seleccionada
        $(".tab-pane").hide(); // Ocultar todas las tablas
        $(targetTab).show(); // Mostrar la tabla correspondiente a la pestaña seleccionada

        // Limpiar el parámetro de fecha de la URL manteniendo la URL base
        if (history.pushState) {
            var newurl = baseUrl + targetTab; // Reconstruir la URL completa
            window.history.pushState({ path: newurl }, '', newurl);
        }
    });

    // Mostrar la tabla de la pestaña activa al cargar la página
    var activeTab = $('.nav-tabs li.active a').attr("href");
    $(activeTab).show();
});
</script> -->
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
                    </div>

                    <div class="form-group col-md-4">
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
                        <label></label>
                        <input type="submit" value="Filtrar" class="form-control btn btn-success">
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
<h1 class="page-header">Lista de presupuestos &nbsp;
    <a class="btn btn-primary pull-right" href="?c=presupuesto_tmp" class="btn btn-success">Nuevo presupuesto</a>
</h1>



<!-- Filtros por estado -->
<div class="row">
    <div class="col-sm-12">
        <h4>Filtrar por estado:</h4>
        <div class="btn-group" role="group" style="margin-bottom: 15px;">
            <button type="button" class="btn btn-info active" onclick="filtrarPorEstado('todos')">Todos</button>
            <button type="button" class="btn btn-warning" onclick="filtrarPorEstado('Pendiente')">Pendiente</button>
            <button type="button" class="btn btn-success" onclick="filtrarPorEstado('Aprobado')">Aprobado</button>
            <button type="button" class="btn btn-primary" onclick="filtrarPorEstado('Vendido')">Vendido</button>
        </div>
        <div id="filtros-activos" style="margin-top: 10px;">
            <small class="text-muted">
                <strong>Filtros activos:</strong> 
                <span id="estado-activo">Todos los estados</span>
                <span id="fecha-activa"></span>
            </small>
        </div>
    </div>
</div>

<h3 id="filtrar" align="center">Filtros <i class="fas fa-angle-right"></i><i class="fas fa-angle-left" style="display: none"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="post">
                <input type="hidden" name="c" value="presupuesto">

                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_POST['desde'])) ? $_POST['desde'] : '';
                                                            ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_POST['hasta'])) ? $_POST['hasta'] : '';
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
            <th>Vendedor</th>
            <th>Ruc</th>
            <th>Cliente</th>
            <th>Fecha y Hora</th>
            <th>Total</th>
            <th>Estado</th>
            <?php if (!isset($_SESSION)) session_start();
            if (($_SESSION['nivel'] <> 3)) { ?>
                <th></th>
            <?php } ?>
            <th></th>
            <th></th>


    </thead>
    <tbody>
        <?php /*
    $suma = 0; $count = 0;  
    $id_venta = (isset($_REQUEST['id_venta']))? $_REQUEST['id_venta']:0;
    $suma = 0; $count = 0;  
    foreach($this->model->Listar($id_venta) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
            <?php if (isset($_REQUEST['id_venta'])): ?>
            <td><?php echo $r->producto; ?></td>    
            <?php endif ?>
            <td><?php echo $r->id_venta; ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_venta)); ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo $r->nro_comprobante; ?></td>
            <td><?php echo $r->metodo; ?></td>
            <td><?php echo $r->contado; ?></td>
            <td><?php echo number_format($r->total,0,".",","); ?></td>
            <?php if (!isset($_GET['id_venta'])): ?>
            <td>
                <a href="#detallesModal" class="btn btn-success" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta;?>">Ver</a>
                <a  class="btn btn-warning" href="?c=venta&a=ticket&id=<?php echo $r->id_venta ?>" class="btn btn-success">Reimprimir</a>
                <!--<a  class="btn btn-primary edit" href="?c=venta_tmp&a=editar&id=<?php //echo $r->id_venta ?>" class="btn btn-success" >Editar</a>-->
                <?php if ($r->anulado): ?>
                ANULADO    
                <?php else: ?>
                 <?php if($r->comprobante=="Factura"){ ?>
                 
                 <a  class="btn btn-warning" href="?c=devolucion_tmp&id_venta=<?php echo $r->id_venta ?>" class="btn btn-success">Devolución</a>
                <?php } ?>
                <a  class="btn btn-danger delete" href="?c=venta&a=anular&id=<?php echo $r->id_venta ?>" class="btn btn-success">ANULAR</a>
                <?php endif ?>
            </td>
            <?php endif ?>
        </tr>
    <?php 
        $count++;
    endforeach; */ ?>
    </tbody>

</table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/presupuesto/mes-modal.php"); ?>
<?php include("view/presupuesto/dia-modal.php"); ?>
<?php include("view/presupuesto/detalles-modal.php");
if (!isset($_SESSION)) session_start();
?>

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

    /* Estilos adicionales para los botones de estado */
    .btn-group .btn {
        margin-right: 5px;
    }

    .btn-group .btn.active {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }
</style>
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
            <?php //if (isset($_GET['desde'])) { 
            ?> "ajax": {
                "url": "?c=presupuesto&a=ListarFiltros&desde=<?php echo (isset($_POST['desde'])) ? $_POST['desde'] : ''; ?>&hasta=<?php echo (isset($_POST['hasta'])) ? $_POST['hasta'] : '';
                                                                                                                                ?>",
                "dataSrc": ""
            },
            <?php //} else { 
            ?>

            // "ajax": {
            //     "url": "?c=presupuesto&a=ListarAjax",
            //     "dataSrc": ""
            // },
            <?php //} 
            ?>

            "columns": [{
                    "data": "id_presupuesto"
                },
                {
                    "data": "user"
                },
                {
                    "data": "ruc"
                },
                {
                    "data": "nombre"
                },
                {
                    "data": "fecha_presupuesto"
                },
                {
                    "data": "total",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "estado",
                    render: function(data, type, row) {
                        if (row.estado == 'Pendiente') {
                            let aprobarLink = "?c=presupuesto&a=Aprobar&id_presupuesto=" + row.id_presupuesto;
                            return '<span class="badge badge-warning">Pendiente aprobación</span><br><a href="' + aprobarLink + '" class="btn btn-sm btn-success mt-1">Aprobar</a>';
                        } else if (row.estado == 'Vendido') {
                            return '<span class="badge badge-success">Vendido</span>';
                        } else if (row.estado == 'Aprobado') {
                            return '<span class="badge badge-primary">Aprobado</span>';
                        } else {
                            return '<span class="badge badge-secondary">Sin estado</span>';
                        }
                    }
                },
                <?php
                if (($_SESSION['nivel'] <> 3)) { ?> {
                        "defaultContent": "",
                        render: function(data, type, row) {
                            if (row.estado == 'Vendido') {
                                return '<span class="badge badge-success">Finalizado</span>'
                            } else if (row.estado == 'Pendiente') {
                                return '<span class="text-muted">Requiere aprobación</span>';
                            } else if (row.estado == 'Aprobado') {
                                let link = "?c=presupuesto&a=Venta&id_presupuesto=" + row.id_presupuesto;
                                return '<a href="' + link + '" class="btn btn-primary">Venta</a>';
                            } else {
                                return '<span class="text-muted">Sin estado</span>';
                            }
                        }
                    },
                <?php } ?> {
                    "defaultContent": "",
                    render: function(data, type, row) {
                        let link = "?c=presupuesto&a=Presupuestopdf&id=" + row.id_presupuesto;
                        return '<a href="' + link + '" class="btn btn-warning">Imprimir</a>';
                    }
                },
                {
                    "defaultContent": "",
                    render: function(data, type, row) {


                        return "<a href='#presupuestoModal' class='btn btn-info' data-toggle='modal' data-target='#presupuestoModal' data-c='presupuesto' data-id='" + row.id_presupuesto + "'>Ver</a>";

                    }
                }

            ],

        });
        
        // Inicializar estado actual
        window.estadoActual = 'todos';
        
        // Interceptar el envío del formulario de fechas
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            // Obtener las fechas del formulario
            let desde = $('input[name="desde"]').val();
            let hasta = $('input[name="hasta"]').val();
            
            // Construir URL considerando el estado actual
            let url;
            if (window.estadoActual === 'todos') {
                url = "?c=presupuesto&a=ListarFiltros&desde=" + desde + "&hasta=" + hasta;
            } else {
                url = "?c=presupuesto&a=ListarPorEstado&estado=" + window.estadoActual + "&desde=" + desde + "&hasta=" + hasta;
            }
            
            // Recargar la tabla con la nueva URL
            $('#tabla').DataTable().ajax.url(url).load();
            
            // Actualizar indicadores de filtros activos
            actualizarFiltrosActivos();
        });
        
        // Inicializar indicadores al cargar la página
        actualizarFiltrosActivos();
    });

    // Función para filtrar por estado
    function filtrarPorEstado(estado) {
        // Obtener las fechas actuales del formulario
        let desde = document.querySelector('input[name="desde"]').value;
        let hasta = document.querySelector('input[name="hasta"]').value;
        
        let url;
        if (estado === 'todos') {
            url = "?c=presupuesto&a=ListarFiltros&desde=" + desde + "&hasta=" + hasta;
        } else {
            url = "?c=presupuesto&a=ListarPorEstado&estado=" + estado + "&desde=" + desde + "&hasta=" + hasta;
        }

        // Recargar la tabla con la nueva URL
        $('#tabla').DataTable().ajax.url(url).load();

        // Actualizar el estilo de los botones
        $('.btn-group button').removeClass('active');
        $('button[onclick="filtrarPorEstado(\'' + estado + '\')"]').addClass('active');
        
        // Guardar el estado actual para uso posterior
        window.estadoActual = estado;
        
        // Actualizar indicadores de filtros activos
        actualizarFiltrosActivos();
    }
    
    // Función para actualizar la visualización de filtros activos
    function actualizarFiltrosActivos() {
        let estadoTexto = window.estadoActual === 'todos' ? 'Todos los estados' : 'Estado: ' + window.estadoActual;
        document.getElementById('estado-activo').textContent = estadoTexto;
        
        let desde = document.querySelector('input[name="desde"]').value;
        let hasta = document.querySelector('input[name="hasta"]').value;
        let fechaTexto = '';
        
        if (desde && hasta) {
            fechaTexto = ' | Fechas: ' + desde + ' a ' + hasta;
        } else if (desde) {
            fechaTexto = ' | Desde: ' + desde;
        } else if (hasta) {
            fechaTexto = ' | Hasta: ' + hasta;
        }
        
        document.getElementById('fecha-activa').textContent = fechaTexto;
    }
</script>
<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
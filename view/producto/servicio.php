
<h1 class="page-header">Lista de Servicios
<button type="button" class="btn btn-primary mb-3 btn-pull-right" id="btnRegistrarServicio" >Registrar Servicio</button>
</h1>
<table id="tabla" class="table responsive display" style="width:100%">
    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Id</th>
            <th>Código</th>
            <th>Servicio</th>
            <th>Precio</th>
            <th>IVA</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php  ?>

    </tbody>
    <tfoot>
        <tr style="background-color: #000; color:#fff">
            <th>Id</th>
            <th>Código</th>
            <th>Servicio</th>
            <th>Precio</th>
            <th>IVA</th>
            <th></th>
        </tr>
    </tfoot>
</table>
</div>
</div>
</div>


<?php include("view/producto/modal-registrar-servicio.php"); ?>

<?php include("view/crud-modal.php");
if (!isset($_SESSION)) session_start();
?>

<script type="text/javascript">
    $(document).ready(function() {
        // Mostrar modal al hacer clic en el botón Registrar Servicio
        $('#btnRegistrarServicio').on('click', function() {
            $('#modalRegistrarServicio').modal('show');
        });

        // Enviar formulario de registro de servicio
        $('#formRegistrarServicio').on('submit', function(e) {
            e.preventDefault();
            var datos = $(this).serialize();
            $.ajax({
                url: '?c=producto&a=RegistrarServicio', // Ajusta la URL según tu controlador
                method: 'POST',
                data: datos,
                success: function(respuesta) {
                    $('#modalRegistrarServicio').modal('hide');
                    $('#formRegistrarServicio')[0].reset();
                    $('#tabla').DataTable().ajax.reload();
                    alert('Servicio registrado correctamente');
                },
                error: function() {
                    alert('Error al registrar el servicio');
                }
            });
        });

        $('#tabla tfoot th').each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });

        // DataTable
        let tablaUsuarios = $('#tabla').DataTable({
            "ajax": {
                "url": "?c=producto&a=ListarServicios",
                "dataSrc": ""
            },
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data": "codigo",
                    render: function(data, type, row) {
                        let link = "?c=venta&a=listarproducto&id_producto=" + row.id;
                        return '<a href="' + link + '" class="btn btn-default">' + data + '</a>';
                    }
                },
                {
                    "data": "producto"
                },
                {
                    "data": "precio_minorista"
                },
                {
                    "data": "iva"
                },
                {
                    "defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-warning btn-sm btnEditar'>Editar</button><button class='btn btn-danger btn-sm btnBorrar'>Del</button></div></div>"
                }
            ],
            "dom": 'Bfrtip',
            "buttons": [{
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'portrait',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                'colvis'
            ],
            "stateSave": true,
            responsive: {
                details: true
            },
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página.",
                "search": "Buscar en todos",
                "buttons": {
                    "colvis": "Columnas Visibles"
                },
                "zeroRecords": "Lo sentimos. No se encontraron registros.",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros aún.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "LoadingRecords": "Cargando ...",
                "Processing": "Procesando...",
                "SearchPlaceholder": "Comience a teclear...",
                "paginate": {
                    "previous": "Anterior",
                    "next": "Siguiente",
                }
            },
            initComplete: function() {
                // Apply the search
                this.api().columns().every(function() {
                    var that = this;

                    $('input', this.footer()).on('keyup change clear', function() {
                        if (that.search() !== this.value) {
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                });
            }
        });




        $("#tabla tbody").on("click", ".btnCodigo", function() {
            let data = tablaUsuarios.row($(this).parents()).data();
            var id = data.id;
            var url = "?c=producto&a=VerCodigoBarra&id=" + id;
            window.open(url, '_blank');
        });

        $("#tabla tbody").on("click", ".btnBorrar", function() {

            let data = tablaUsuarios.row($(this).parents()).data();
            id = data.id;
            var respuesta = confirm("¿Está seguro de borrar " + data.producto + "?");

            var url = "?c=producto&a=Eliminar&id=" + id;
            tablaUsuarios.row(0).remove().draw();
            $.ajax({

                url: url,
                method: "POST",
                data: id,
                cache: false,
                contentType: false,
                processData: false,
                success: function(respuesta) {}

            })
        });

        // Editar servicio
        $("#tabla tbody").on("click", ".btnEditar", function() {
            let data = tablaUsuarios.row($(this).parents('tr')).data();
            // Rellenar el modal con los datos del servicio
            $('#modalRegistrarServicioLabel').text('Editar Servicio');
            $('#codigo').val(data.codigo);
            $('#servicio').val(data.producto);
            $('#precio').val(data.precio_minorista);
            // Guardar el id temporalmente
            $('#formRegistrarServicio').data('id', data.id);
            $('#modalRegistrarServicio').modal('show');
        });

        // Enviar formulario para editar o registrar
        $('#formRegistrarServicio').off('submit').on('submit', function(e) {
            e.preventDefault();
            var datos = $(this).serialize();
            var id = $(this).data('id');
            var url = id ? '?c=producto&a=EditarServicio' : '?c=producto&a=RegistrarServicio';
            if (id) datos += '&id=' + id;
            $.ajax({
                url: url,
                method: 'POST',
                data: datos,
                success: function(respuesta) {
                    $('#modalRegistrarServicio').modal('hide');
                    $('#formRegistrarServicio')[0].reset();
                    $('#formRegistrarServicio').removeData('id');
                    $('#modalRegistrarServicioLabel').text('Registrar Servicio');
                    $('#tabla').DataTable().ajax.reload();
                    alert(id ? 'Servicio editado correctamente' : 'Servicio registrado correctamente');
                },
                error: function() {
                    alert('Error al guardar el servicio');
                }
            });
        });


    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // Setup - add a text input to each footer cell
        $('#tabla tfoot th').each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Buscar ' + title + '" />');
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
</script>
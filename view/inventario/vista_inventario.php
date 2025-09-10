<h1 class="page-header">Lista de Inventario&nbsp;</h1>
<a class="btn btn-warning pull-right " align="center" href="?c=inventario&a=InventarioPdf&id_c=<?php echo $_REQUEST['id_c']; ?>" style="margin-right: 1rem" onclick="Swal.fire({
        title: 'Cargando',
        html: 'Generando PDF... por favor, aguarde.',
        showConfirmButton: false,
        backdrop: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        onOpen: () => {
            swal.showLoading();
        },
        }).then((result) => {
        /* Read more about handling dismissals below */

        })">Exportar a PDF</a>

<br><br>
<div class="row">
    <div class="col-md-4"></div>
    <div class="center col-md-4 offset-md-1">
        <div class="form-group">
            <select style="width: 100%;" class="form-control form-control-lg selectpickerr" onchange="filtrarTabla()" data-show-subtext="true" data-live-search="true" name="filtrar_inventario" id="filtrar_inventario">
                <option value="0">Sin Filtrar</option>
                <option value="3">Productos sobrantes</option>
                <option value="4">Productos faltantes</option>
                <option value="5">Productos con stock correcto</option>
            </select>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>
<table class="table table-striped table-bordered display nowrap responsive" width="100%" id="tabla">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Codigo</th>
            <th>Categoria</th>
            <th>Producto</th>
            <th>Costo</th>
            <th>Venta</th>
            <th>Stock Al Iniciar</th>
            <th>Stock Al Cargar</th>
            <th>Inventario </th>
            <th>Diferencia</th>
            <th>Monto Falt.</th>
            <th>Monto Sobr.</th>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff">
            <th>Codigo</th>
            <th>Categoria</th>
            <th>Producto</th>
            <th>Costo</th>
            <th>Venta</th>
            <th>Stock Al Iniciar</th>
            <th>Stock Al Cargar</th>
            <th>Inventario </th>
            <th>Diferencia</th>
            <th>Monto Falt.</th>
            <th>Monto Sobr.</th>
        </tr>
    </tfoot>

</table>

</div>
</div>
</div>
<script type="text/javascript">
    function filtrarTabla() {
        let filtro = $("#filtrar_inventario").val();
        let descripcion_filtro = $("#filtrar_inventario option:selected" ).text();
        cargarTabla(filtro, descripcion_filtro);
    }
    var tabla;
    $(document).ready(function() {
        var filtro = '0';
        cargarTabla(filtro, 'Sin Filtrar');
    });

    function cargarTabla(filtro, descripcion_filtro_func) {
        $('#tabla').DataTable().destroy(); //linea necesaria para evitar el error de reinicializacion (cannot reinitialize datatable)
        let url = "?c=inventario&a=ListarInventario&id_c=<?php echo ($_REQUEST['id_c']) ?>&q=" + filtro;

        // console.log(descripcion_filtro_func);
        // console.log(url);

        var suma_faltante = 0;
        var suma_sobrante = 0;
        // DataTable
        let tablaUsuarios = $('#tabla').DataTable({

            "dom": 'Bfrtip',
            "buttons": [{
                extend: 'excelHtml5',
                title: "Inventario Nro. <?php echo ($_REQUEST['id_c']);?>",
                messageTop: "Filtrado por: " + descripcion_filtro_func,
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                }
            }, {
                extend: 'pdfHtml5',
                footer: true,
                title: "Inventario Nro. <?php echo ($_REQUEST['id_c']);?>",
                messageTop: "Filtrado por: " + descripcion_filtro_func,
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: ':visible'
                }
            }, 'colvis'],
            "stateSave": true,
            //"scrollY": '50vh',
            //"scrollCollapse": true,
            "paging": true,
            responsive: {
                details: true
            },

            "ajax": {
                "url": url,
                "dataSrc": ""
            },

            "columns": [{
                    "data": "codigo"
                },
                {
                    "data": "categoria_hijo"
                },
                {
                    "data": "producto"
                },
                {
                    "data": "costo",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "venta",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "stock_actual",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "stock_productos_view",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "inventario",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "faltante",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },

                {
                    "data": "monto",
                    render: function(data, type, row) {
                        let monto_faltante = (row.faltante * row.venta);

                        if (row.faltante > 0) suma_faltante += monto_faltante;
                        // cuando faltan mercaderias, faltante es positivo
                        return (row.faltante > 0) ? (monto_faltante.toLocaleString("en-US")) : '';
                    }
                },
                {
                    "data": "monto",
                    render: function(data, type, row) {
                        let monto_sobrante = (row.faltante * row.venta);
                        if (row.faltante < 0) suma_sobrante += (monto_sobrante*-1);
                        //cuadno sobran mercaderias,row.faltante es negativo
                        return (row.faltante < 0) ? ((monto_sobrante*-1).toLocaleString("en-US")) : '';
                    }
                }



            ],

            "language": {

                "thousands": ".",
                "lengthMenu": "Mostrar _MENU_ registros por página.",
                "search": "Buscar",
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
            "sort": false,
            "stateSave": true,
            "sort": false,
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // Total over all pages
                data = api.column(8).cache('search');
                total = data.length ?
                    data.reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }) :
                    0;

                data = api.column(9).cache('search');
                total_2 = data.length ?
                    data.reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }) :
                    0;

                $(api.column(8).footer()).html(total.toLocaleString('en-US'));
                $(api.column(9).footer()).html(total_2.toLocaleString('en-US'));
            },

        });
    }



    $(document).ready(function() {
        
    });
</script>
<h1 class="page-header">Lista de ventas completa &nbsp;

</h1>
<br><br><br>

<h3 id="filtrar" align="center">Filtros <i class="fas fa-angle-right"></i><i class="fas fa-angle-left" style="display: none"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="get">
                <input type="hidden" name="c" value="venta">
                <input type="hidden" name="a" value="ListarProductoVenta">

                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php echo (isset($_GET['desde']))? $_GET['desde']:''; 
                                                            ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php echo (isset($_GET['hasta']))? $_GET['hasta']:''; 
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
            <th>Cliente</th>
            <th>Cod.</th>
            <th>Producto</th>
            <th>Costo</th>
            <th>Venta</th>
            <th>Desc</th>
            <th>P/U</th>
            <th>Cant.</th>
            <th>Total V.</th>
            <th>Total C.</th>
            <th>Utilidad</th>

        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>

        </tr>
    </tfoot>

</table>

</div>
</div>
</div>

?>
<script type="text/javascript">
    $(document).ready(function() {

         jQuery.fn.dataTable.Api.register('sum()', function() {
            return this.flatten().reduce(function(a, b) {
                if (typeof a === 'string') {
                    a = a.replace(/[^\d.-]/g, '') * 1;
                }
                if (typeof b === 'string') {
                    b = b.replace(/[^\d.-]/g, '') * 1;
                }

                return a + b;
            }, 0);
        });

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
                title: "Ventas Afrodite CDE",
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6, 7, 9,10,11,12]
                }
            }, 'colvis'],

            responsive: {
                details: true
            },
            "sort": false,
             "ajax": {
                    "url": "?c=venta&a=ListarPorItem&desde=<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : '' ?>&hasta=<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : '' ?>",
                    "dataSrc": ""
                },

            "columns": [
                {
                    "data": "id_presupuesto"
                },
                {
                    "data": "",
                     render: function(data, type, row) {

                       if(row.id_presupuesto==null){
                            return row.vendedor_caja
                       }else{
                            return row.vendedor
                       }
                    }
                },
                 {
                    "data": "nombre_cli"
                },
                {
                    "data": "codigo"
                },
                 {
                    "data": "producto"
                },
                {
                    "data": "precio_costo",
                    "className": "my-class2",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "precio_venta",
                    "className": "my-class2",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "descuento",
                    "className": "my-class2",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "descuento_venta",
                    "className": "my-class2",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "cantidad",
                     "className": "my-class"
                },
                {
                    "data": "total_unidad",
                    "className": "my-class2",
                    render: $.fn.dataTable.render.number(',', '.', 0)

                },
                 {
                    "data": "total_costo",
                    "className": "my-class2",
                     render: $.fn.dataTable.render.number(',', '.', 0)
                },

                {
                    "data": "utilidad",
                    "className": "my-class2",
                     render: $.fn.dataTable.render.number(',', '.', 0)
                }


            ],

             "drawCallback": function() {
                //alert("La tabla se está recargando"); 
                var api = this.api();
                $(api.column(5).footer()).html(
                    api.column(5, {
                        page: 'all'
                    }).data().sum().toLocaleString()
                )
                $(api.column(6).footer()).html(
                    api.column(6, {
                        page: 'all'
                    }).data().sum().toLocaleString()
                )
                $(api.column(7).footer()).html(
                    api.column(7, {
                        page: 'all'
                    }).data().sum().toLocaleString()
                )
                $(api.column(8).footer()).html(
                    api.column(8, {
                        page: 'all'
                    }).data().sum().toLocaleString()
                )
                 $(api.column(9).footer()).html(
                    api.column(9, {
                        page: 'all'
                    }).data().sum().toLocaleString()
                )
                 $(api.column(10).footer()).html(
                    api.column(10, {
                        page: 'all'
                    }).data().sum().toLocaleString()
                )
                 $(api.column(11).footer()).html(
                    api.column(11, {
                        page: 'all'
                    }).data().sum().toLocaleString()
                )
                $(api.column(12).footer()).html(
                    api.column(12, {
                        page: 'all'
                    }).data().sum().toLocaleString()
                )
            }

        });
    });
</script>
<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });
</script>
<h1 class="page-header">Lista de presupuestos de compra &nbsp;

<a class="btn btn-primary pull-right" href="?c=presupuesto_compra_tmp" class="btn btn-success">Nuevo presupuesto</a>
<br><br><br>

<h3 id="filtrar" align="center"><i class="fas fa-angle-right"></i><i class="fas fa-angle-left" style="display: none"></i></h3> 
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="get">
                <input type="hidden" name="c" value="compra">

                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                    <!-- <label>Desde</label> -->
                    <input type="hidden" name="desde" value="<?php echo (isset($_GET['desde']))? $_GET['desde']:''; 
                                                            ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <!-- <label>Hasta</label> -->
                    <input type="hidden" name="hasta" value="<?php echo (isset($_GET['hasta']))? $_GET['hasta']:''; 
                                                            ?>" class="form-control">
                </div>

                <div class="form-group col-md-2">
                    <label></label>
                    <input type="hidden" value="Filtrar" class="form-control btn btn-success">
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
            <th>Comprador</th>
            <th>Proveedor</th>
            <th>Comprobante</th>
            <th>Nro. comprobante</th>
            <th>Fecha y Hora</th>
            <th>Total</th>
            <th></th>
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
<?php include("view/presupuesto_compra/finalizar-presupuesto-modal.php"); ?>
<?php include("view/presupuesto_compra/detalles-modal.php"); 
session_start();
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
                title: "Lista de presupuestos de compra",
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
                    "url": "?c=presupuesto_compra&a=ListarFiltros&desde=<?php echo $_GET['desde'] ?>&hasta=<?php echo $_GET['hasta'] ?>",
                    "dataSrc": ""
                },
            <?php } else { ?>

                "ajax": {
                    "url": "?c=presupuesto_compra&a=ListarAjax",
                    "dataSrc": ""
                },
            <?php } ?>

            "columns": [{
                    "data": "id_presupuesto"
                },
                {
                    "data": "user"
                },
                {
                    "data": "nombre"
                },
                {
                    "data": "comprobante"
                },
                {
                    "data": "nro_comprobante"
                },
                {
                    "data": "fecha_compra"
                },
                {
                    "data": "total",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                        "defaultContent": "",
                        render: function(data, type, row) {
                            if (row.estado == 'Comprado') {
                                return 'Finalizado'
                            } else {
                               <?php if ($_SESSION['nivel']==1) { ?>   
                                let link = "?c=presupuesto_compra&a=Compra&id_presupuesto=" + row.id_presupuesto;
                                return '<a href="' + link + '" class="btn btn-primary">Compra</a>';
                                <?php } ?>
                                }
                        }
                    },


                {"defaultContent": "",
                    
                    render: function(data, type, row) {
                        if ((row.anulado == 1)) {
                                return 'ANULADO'
                            } else if (row.estado != 'Comprado') {  

                                let link = "?c=presupuesto_compra&a=Editar&id_presupuesto=" + row.id_presupuesto;
                                return '<a href="' + link + '" class="btn btn-warning">Editar</a>';

                            }
                       
                    }
                },

                 {
                        "defaultContent": "",
                        render: function(data, type, row) {
                            
                            if (row.anulado == 1) {
                                return 'ANULADO'
                            } else if (row.estado != 'Comprado') {  

                                let link = "?c=presupuesto_compra&a=anular&id=" + row.id_presupuesto;
                                return '<a href="' + link + '" class="btn btn-danger">Anular</a>';

                            }

                        }
                    },
                {
                    "defaultContent": "",
                    render: function(data, type, row) {


                        return "<a href='#detallesCompraModal' class='btn btn-info' data-toggle='modal' data-target='#detallesCompraModal' data-presupuesto='presupuesto' data-id='" + row.id_presupuesto + "'>Ver</a>";

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
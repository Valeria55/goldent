<h1 class="page-header">Lista de transferencias recibidas &nbsp;
    <!--<a class="btn btn-primary" href="#diaModal" class="btn btn-primary" data-toggle="modal" data-target="#diaModal">Informe diario</a>
<a class="btn btn-primary" href="#mesModal" class="btn btn-primary" data-toggle="modal" data-target="#mesModal">Informe Mensual</a>-->
</h1>
<a class="btn btn-primary pull-right" href="?c=transferencia_producto_tmp" class="btn btn-success" style="display: none;">Nueva transferencia</a>
<br><br><br>

<h3 id="filtrar" align="center" style="display: none;">Filtros <i class="fas fa-angle-right"></i><i class="fas fa-angle-left" style="display: none"></i></h3>
<div class="row">
    <div class="col-sm-12" style="display: none;">
        <div align="center" id="filtro">
            <form method="get">
                <input type="hidden" name="c" value="venta">

                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php //echo (isset($_GET['desde']))? $_GET['desde']:''; 
                                                            ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php //echo (isset($_GET['hasta']))? $_GET['hasta']:''; 
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
            <th>Usuario</th>
            <th>Fecha y Hora</th>
            <th>Cant. Prod.</th>
            <th>Total</th>
            <?php if (!isset($_SESSION)) session_start();
            if (($_SESSION['nivel'] == 1) || ($_SESSION['nivel'] == 4) || ($_SESSION['nivel'] == 2)) { ?>
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
<?php include("view/transferencia_producto/mes-modal.php"); ?>
<?php include("view/transferencia_producto/dia-modal.php"); ?>
<?php include("view/transferencia_producto/detalles-modal.php");
if (!isset($_SESSION)) session_start();
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
                    "url": "?c=transferencia_producto&a=ListarFiltros&desde=<?php echo $_GET['desde'] ?>&hasta=<?php echo $_GET['hasta'] ?>",
                    "dataSrc": ""
                },
            <?php } else { ?>

                "ajax": {
                    "url": "?c=transferencia_producto&a=ListarAjaxRecibir",
                    "dataSrc": ""
                },
            <?php } ?>

            "columns": [{
                    "data": "id_transferencia_producto"
                },
                {
                    "data": "user"
                },
                {
                    "data": "fecha_transferencia_producto"
                },
                {
                    "data": "cant_total",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "total",
                    render: $.fn.dataTable.render.number(',', '.', 0)
                },
                <?php
                if (($_SESSION['nivel'] == 1) || ($_SESSION['nivel'] == 4) || ($_SESSION['nivel'] == 2)) { ?> {
                        "defaultContent": "",
                        render: function(data, type, row) {
                            if (row.estado == 'finalizado') {
                                return 'Finalizado'
                            } else if (row.estado == 'pendiente') {
                                let link = "?c=transferencia_producto&a=ConfirmarTransferencia&id_transferencia_producto=" + row.id_transferencia_producto;
                                return '<a href="' + link + '" class="btn btn-primary" onclick="return confirm(`Desea confirmar la transferencia?`)">Confirmar</a>';
                            } else {
                                return 'Cancelado';
                            }
                        }
                    },
                <?php } ?> {
                    "defaultContent": "",
                    render: function(data, type, row) {
                        let link = "?c=transferencia_producto&a=transferencia_productopdf&id=" + row.id_transferencia_producto;
                        // return '<a href="' + link + '" class="btn btn-warning">Imprimir</a>';
                        return "";
                    }
                },
                {
                    "defaultContent": "",
                    render: function(data, type, row) {

                        return "<a href='#transferencia_productoModal' class='btn btn-info' data-toggle='modal' data-target='#transferencia_productoModal' data-c='transferencia_producto' data-rec='1' data-id='" + row.id_transferencia_producto + "'>Ver</a>";

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
    $(".confirmar_btn").click(function(e) {
        if (confirm("Desea confirmar la transferencia?") == true) {
            // text = "You pressed OK!";
        } else {
            e.preventDefault();
            // text = "You canceled!";
        }

    });
</script>
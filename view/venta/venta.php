<h1 class="page-header">Lista de ventas &nbsp;
     <?php if($_SESSION['nivel']==1){ ?>
    <a class="btn btn-primary" href="#diaModal" class="btn btn-primary" data-toggle="modal" data-target="#diaModal">Informe diario</a>
    <a class="btn btn-primary" href="#mesModal" class="btn btn-primary" data-toggle="modal" data-target="#mesModal">Informe Mensual</a>
         <?php } ?>
</h1>
<a class="btn btn-primary pull-right" href="?c=venta_tmp" class="btn btn-success">Nueva venta</a>
<br><br><br>

<h3 id="filtrar" align="center">Filtros <i class="fas fa-angle-right"></i><i class="fas fa-angle-left" style="display: none"></i></h3>
<div class="row">
    <div class="col-sm-12">
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
            <th>Presupuesto</th>
            <th>Cliente</th>
            <th>Fecha y Hora</th>
            <th>Comprobante</th>
            <th>Nro. comprobante</th>
            <th>Pago</th>
            <th>Total</th>
            <th></th>
            <?php session_start();
            if ($_SESSION['nivel'] == 1) { ?>
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
<?php include("view/venta/mes-modal.php"); ?>
<?php include("view/venta/dia-modal.php"); ?>
<?php include("view/venta/detalles-modal.php");
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
                    "url": "?c=venta&a=ListarFiltros&desde=<?php echo $_GET['desde'] ?>&hasta=<?php echo $_GET['hasta'] ?>",
                    "dataSrc": ""
                },
            <?php } else { ?>

                "ajax": {
                    "url": "?c=venta&a=ListarAjax",
                    "dataSrc": ""
                },
            <?php } ?>

            "columns": [
                {
                    "data": "id_presupuesto"
                },
                {
                    "data": "",
                    render: function(data, type, row) {

                        if (row.id_cliente == 0) {

                            return 'Cliente ocacional';

                        } else {

                            return row.nombre_cli;
                        }
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
                        } else if (row.comprobante == 'Recibo') {
                            return 'Recibo';
                        } else {
                            return 'Factura';
                        }
                    }
                },
                {
                    "data": "nro_comprobante"
                },
                {
                    "data": "contado"
                },
                {
                    "data": "total",
                    render: $.fn.dataTable.render.number('.', ',', 0)
                },
                
                {
                    "defaultContent": "",
                    render: function(data, type, row) {


                        return "<a href='#detallesModal' class='btn btn-info' data-toggle='modal' data-target='#detallesModal' data-c='venta' data-id='" + row.id_venta + "'>Ver</a>";

                    }
                },
                {
                    "defaultContent": "",
                    render: function(data, type, row) {
                        if(row.comprobante == 'Recibo'){
                            let link = "?c=venta&a=recibo&id=" + row.id_venta;
                            return '<a href="' + link + '" class="btn btn-sucess">Recibo</a>';
                        }else if(row.comprobante == 'Factura'){
                            let link = "?c=venta&a=factura&id=" + row.id_venta;
                            return '<a href="' + link + '" class="btn btn-primary">Factura</a>';  
                        }else{
                            let link = "?c=venta&a=Ticket&id=" + row.id_venta;
                            return '<a href="' + link + '" class="btn btn-warning">Ticket</a>';
                        }
                       
                    }
                },
                <?php
                if ($_SESSION['nivel'] == 1) { ?> {
                        "defaultContent": "",
                        render: function(data, type, row) {
                            if (row.anulado == 1) {
                                return 'ANULADO'
                            } else {

                                let link = "?c=venta&a=anular&id=" + row.id_venta;
                                return '<a href="' + link + '" class="btn btn-danger">Eliminar</a>';

                            }

                        }
                    },
                <?php } ?> {
                    "defaultContent": "",
                    render: function(data, type, row) {
                        if (row.anulado == 0) {
                            let link = "?c=devolucion_tmpventas&id_venta=" + row.id_venta;
                            return '<a href="' + link + '" class="btn btn-warning">Devolución</a>';
                        }
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
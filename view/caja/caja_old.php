<?php if(($_SESSION['nivel'] == 2 ) || $_SESSION['nivel'] > 4) die('<h3 class="page-header">Permisos insuficientes</h3>');?>
    
<h1 class="page-header">Lista de cajas </h1>
<a class="btn btn-primary pull-right" style="margin-left: .5em !important;" href="#inversion_modal" class="btn btn-success" data-toggle="modal" data-target="#inversion_modal" data-c="caja">Entrada externa</a>
<a class="btn btn-primary pull-right" href="#cajaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="caja">Nueva Caja</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Entidad</th>
            <th>Encargado</th>
            <th>Fecha</th>
            <th>Monto Apertura</th>
            <th>Disponible</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->model->Listar() as $r) : 
                // si no es sesion 1 o 4, solo mostrar caja chica
                if( ($r->id != 1) && ( $_SESSION['nivel'] == 2 ) ) continue; 
            ?>
            
            <tr class="click" <?php echo ($r->anulado == 1) ? "style='background-color:gray'" : ""; ?>>
                <td><a href="?c=caja&a=movimientos&id_caja=<?php echo $r->id; ?>"><?php echo $r->caja; ?></a></td>
                <td><?php echo $r->usuario; ?></td>
                <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                <td><?php echo number_format($r->monto, 0, ".", ","); ?></td>
                <td><?php echo number_format(($r->ingresos - $r->egresos), 0, ".", ","); ?></td>
                <td>
                    <?php if ($r->anulado != 1) : ?>
                        <button type="button" class="btn btn-info" data-id_caja="<?php echo $r->id; ?>" data-toggle="modal" data-target="#transf_cajaModal">Transferir</button>
                        <!-- <a href="#transferencia_cajaModal" data-id_caja="<?php //echo $r->id; ?>">Transferir a</a> -->
                    <?php endif ?>
                </td>
            </tr>

        <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>

<div id="transf_cajaModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="transferencia_form">

            </div>

        </div>
    </div>
</div>

<div id="inversion_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="inversion_form">

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#inversion_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            // var id = button.data('id');
            // var caja = button.data('id_caja');
            var url = "?c=caja&a=inversionModal";
            $.ajax({

                url: url,
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                success: function(respuesta) {
                    $("#inversion_form").html(respuesta);

                }

            })
        })
    });
</script>
<?php include("view/crud-modal.php"); ?>
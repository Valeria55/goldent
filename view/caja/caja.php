<?php if(($_SESSION['nivel'] == 3 ) || $_SESSION['nivel'] > 4) die('<h3 class="page-header">Permisos insuficientes</h3>');?>
    
<h1 class="page-header">Lista de cajas </h1>
<?php if($_SESSION['nivel'] == 1):?>
<a class="btn btn-primary pull-right" style="margin-left: .5em !important;" href="#inversion_modal" class="btn btn-success" data-toggle="modal" data-target="#inversion_modal" data-c="caja">Entrada externa</a>
<!-- <a class="btn btn-primary pull-right" href="#cajaModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="caja">Nueva Caja</a> -->
<?php endif;?>
<!-- Botón de documentación -->
<a class="btn btn-warning pull-right" style="margin-right: .5em !important;" target="_blank" href="index.php?c=caja&a=guiaPDF">
    Documentación
</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Entidad</th>
            <th>Fecha</th>
            <th>Disponible (Total)</th>
            <th>Desglose por Moneda</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->model->Listar() as $r) : 

                // en taller: Gerente puede ver solamente tesoreria y su propia caja chica
                if(!in_array($r->id, array(1, 3)) && ( $_SESSION['nivel'] == 4 ) ) continue; // gerente: nivel 4

                if(in_array($r->id, array(2)) && ( $_SESSION['nivel'] != 1 ) ) continue; // si no es nivel 1 no ve banco
                
                if( ($r->id != 1) && ( $_SESSION['nivel'] == 2 ) ) continue; // nivel 2 (cajero) solo puede ver caja chica
                
            ?>
            
            <tr class="click" <?php echo ($r->anulado == 1) ? "style='background-color:gray'" : ""; ?>>
                <td><a href="?c=caja&a=movimientos&id_caja=<?php echo $r->id; ?>"><?php echo $r->caja; ?></a></td>
                <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
                <td><strong>
                    <?php 
                    $total_gs = ($r->ingresos_gs ?? 0) - ($r->egresos_gs ?? 0);
                    $total_usd = ($r->ingresos_usd ?? 0) - ($r->egresos_usd ?? 0);
                    $total_rs = ($r->ingresos_rs ?? 0) - ($r->egresos_rs ?? 0);
                    $total_convertido = ($r->ingresos - $r->egresos);
                    
                    // Siempre mostrar el total equivalente en GS
                    echo number_format($total_convertido, 0, ".", ",");
                    ?>
                </strong></td>
                <td>
                    <small>
                        <strong>GS:</strong> <?php echo number_format((($r->ingresos_gs ?? 0) - ($r->egresos_gs ?? 0)), 0, ".", ","); ?><br>
                        <strong>USD:</strong> <?php echo number_format((($r->ingresos_usd ?? 0) - ($r->egresos_usd ?? 0)), 2, ".", ","); ?><br>
                        <strong>RS:</strong> <?php echo number_format((($r->ingresos_rs ?? 0) - ($r->egresos_rs ?? 0)), 2, ".", ","); ?>
                        <?php 
                        $total_convertido = ($r->ingresos - $r->egresos);
                        $total_gs = ($r->ingresos_gs ?? 0) - ($r->egresos_gs ?? 0);
                        $diferencia = $total_convertido - $total_gs;
                        if ($total_usd != 0 || $total_rs != 0 || abs($diferencia) > 1): ?>
                            <br><em style="color: #666;">Total equiv: <?php echo number_format($total_convertido, 0, ".", ","); ?> GS</em>
                            <?php if (abs($diferencia) > 1 && $total_usd == 0 && $total_rs == 0): ?>
                                <br><small style="color: #999;">Incluye movimientos USD/RS históricos</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </small>
                </td>
                <td>
                    <?php if ($r->anulado != 1 && !(in_array($_SESSION['user_id'], array(6, 28)) && $r->id == 1 ) ) : ?>
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
        });
        
        // Modal de transferencia con manejo de datos de caja
        $('#transf_cajaModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id_caja = button.data('id_caja');
            var url = "?c=caja&a=transferenciaModal&id_caja=" + id_caja;
            
            $.ajax({
                url: url,
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                success: function(respuesta) {
                    $("#transferencia_form").html(respuesta);
                },
                error: function() {
                    $("#transferencia_form").html("<h3>Error al cargar el formulario</h3>");
                }
            });
        });
    });
</script>
<?php include("view/crud-modal.php"); ?>
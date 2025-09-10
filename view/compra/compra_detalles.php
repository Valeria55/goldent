<?php $fecha = date("Y-m-d"); ?>
<style>
    .scrolleable {
        height: 500px;
        overflow-y: scroll;
    }
</style>
<h1 class="page-header">Detalles de la compra</h1>
<div align="center" width="30%">

</div>


<div class="table-responsive">

    <table class="table table-striped table-bordered display datatable nowrap" id="tabla1">

        <thead>
            <tr style="background-color: #5DACCD; color:#fff">
                <th>Producto</th>
                <th>Cant</th>
                <th>Precio compra</th>
                <th>Precio venta</th>
                <th>Total (Gs.)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $subtotal = 0;
            $sumatotal = 0;
            $id_compra = $_GET['id'];
            foreach ($this->compra->Listar($id_compra) as $r) :
                $total = $r->precio_compra * $r->cantidad;
            ?>
                <tr>
                    <td><?php echo $r->producto; ?></td>
                    <td><?php echo $r->cantidad; ?></td>
                    <td><?php echo number_format($r->precio_compra, 0, ",", "."); ?></td>
                    <td><?php echo number_format($r->precio_min, 0, ",", "."); ?></td>
                    <td><?php echo number_format($total, 0, ",", "."); ?></td>
                </tr>
            <?php $sumatotal += $total;
            endforeach; ?>


        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Total Gs: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal, 0, ",", ".") ?></div>
                </td>
            </tr>

        </tfoot>

    </table>
</div>
<?php $devolucion = $this->model->ObtenerDevolucion($id_compra)->existe_registro; 
if($devolucion > 0): ?>
<div>
    <h1 class="page-header">Detalles de devolución</h1>
    <p>Todas las devoluciones hechas a la Compra N°<?php echo $id_compra; ?></p>
    <div align="center" width="30%">

    </div>

    <div class="table-responsive">

        <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

            <thead>
                <tr style="background-color: #5DACCD; color:#fff">
                    <th>Producto</th>
                    <th>Costo</th>
                    <th>Cant</th>
                    <th>Motivo</th>
                    <th>Fecha Devolución</th>
                    <th>Total (Gs.)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                $sumatotal = 0;

                foreach ($this->devolucion_compras->Listar($id_compra) as $r) :
                    $total = $r->precio_compra * $r->cantidad;
                ?>
                    <tr>

                        <td><?php echo $r->producto; ?></td>
                        <td><?php echo number_format($r->precio_compra, 0, ",", "."); ?></td>
                        <td><?php echo $r->cantidad; ?></td>
                        <td><?php echo $r->motivo; ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($r->fecha_compra)) ?></td>
                        <td><?php echo number_format($total, 0, ",", "."); ?></td>
                    </tr>
                <?php $sumatotal += $total;
                endforeach; ?>


                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Total Gs: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal, 0, ",", ".") ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>
</div>
<script>
    $('#tabla1').dataTable({
        "dom": 'Bfrtip',
        "scrollY": '50vh',
        "scrollCollapse": true,
        "paging": false
    });
</script>
<?php $cierre = $this->cierre->Consultar($_SESSION['user_id']); ?>
<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla1">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Código</th>
            <th>Producto</th>
            <th>Precio por Unidad</th>
            <th>Cantidad</th>
            <th>Descuento (%)</th>
            <th>Total (Gs.)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
     $subtotal=0;
     foreach($this->model->Listar() as $r): 
        $totalItem = (($r->precio_venta*$r->cantidad)-(($r->precio_venta*$r->cantidad)*($r->descuento/100)));
        $subtotal += ($totalItem);?>
        <tr>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo number_format($r->precio_venta, 0, "," , "."); ?></td>
            <td><?php echo $r->cantidad; ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><div id="precioTotal<?php echo $r->id; ?>" class="total_item">
                <?php echo number_format( $totalItem, 0, "," , "."); ?></div></td>
            <td>
                <a  class="btn btn-danger cancelar" id_item="<?php echo $r->id; ?>">Cancelar</a>
            </td>
        </tr>
        <input type="hidden" id="clienteId" value="<?php echo $r->id_venta; ?>">
    <?php endforeach; ?>
        
        
        <tr>
            <td></td>
            <td></td>
            <td>Total Gs: <div id="total" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Paraguay.png"></i><?php echo number_format($subtotal,0,",",".") ?></div></td>
            <td>Total Rs: <div id="totalrs" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/Brazil.png"></i><?php echo number_format(($subtotal/$cierre->cot_real), 2, "," , ".") ?></div></td>
            <td>Total Us: <div id="totalus" style="font-size: 30px"><img src="http://www.customicondesign.com/images/freeicons/flag/round-flag/48/USA.png"></i><?php echo number_format(($subtotal/$cierre->cot_dolar), 2, "," , ".") ?></div></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>

<div align="center"><a class="btn btn-lg btn-primary " href="#finalizarModal" class="btn btn-success" data-toggle="modal" data-target="#finalizarModal" data-c="salida">Finalizar (F4)</a>
<a  class="btn btn-lg btn-danger delete" href="?c=salida_tmp&a=CancelarVenta">Cancelar Todo</a></div>

</div>
</div>
</div>
</div>
<?php include("view/salida/finalizar-modal.php"); ?>
<script>
    $('.cancelar').on('click',function(){
        var datos = {};
        datos.id = $(this).attr("id_item");
        $.ajax({
            method : "POST",
            url: "?c=salida_tmp&a=eliminar",
            data: datos,
            success: function (data) { $("#tabla_items").html(data) } 
        });
    });
    $('#finalizarModal').on('show.bs.modal', function (event) {
		$("#finalizar_venta").focus();
	})
	
</script>
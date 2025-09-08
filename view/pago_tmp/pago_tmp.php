<?php
session_start();
$cambio = $this->cierre->Consultar($_SESSION['user_id']);
$monto_venta = $this->venta_tmp->ObtenerMonto();
//$monto_pago =  $this->pago_tmp->listar();
$pagototal = 0;
$pago = 0;


foreach ($this->pago_tmp->Listar() as $monto_pago) :
    // echo '<pre>'; var_dump($cambio); echo '</pre>';
    if ($monto_pago->moneda == 'USD') {
        $pago = $monto_pago->monto* $cambio->cot_dolar_tmp;        
    } elseif ($monto_pago->moneda == 'RS') {
        $pago = $monto_pago->monto * $cambio->cot_real_tmp;
    } elseif ($monto_pago->moneda == 'GS') {
        $pago = $monto_pago->monto;
    }
    $pagototal += $pago;
endforeach;


$saldo = $monto_venta->monto - $pagototal;

if($saldo < 0.01){
    $saldo = 0;
}
?>
<div class="form-group col-sm-12" id="banco" style="display: none;">
    <label>Banco</label>
    <input type="text" name="banco" class="form-control" placeholder="Ingrese nombre de banco">
</div>
<input type="hidden" name="subtotal" value="<?php echo $subtotal ?>">
<input type="hidden" name="total" class="totaldesc" id="totaldesc" value="<?php echo $subtotal ?>">
<input type="hidden" name="cot_dolar"  id="cot_dolar" value="<?php echo $cambio->cot_dolar_tmp ?>">
<input type="hidden" name="cot_real"  id="cot_real" value="<?php echo $cambio->cot_real_tmp ?>">
<input type="hidden" name="descuentoval" id="descuentoval" value="0">
<input type="hidden" name="ivaval" id="ivaval" value="0">
<input type="hidden" name="id_vendedor" value="12">
<input type="hidden" id="sub" value="<?php echo $monto_venta->monto ?>">

<div class="form-group mt-3">
    
    <table class="table table-sm table-bordered table-striped display responsive nowrap" style="text-align: center;   border-collapse: collapse;
    ">
        <tbody>
            <tr style="background-color: #eeeeee;">
                <th style="text-align: center;">Total (Gs.)</th>
                <th style="text-align: center;">Dif. (USD)</th>
                <th style="text-align: center;">Dif. (Gs.)</th>
                <th style="text-align: center;">Dif. (Rs.)</th>
            </tr>
            <tr>
                <td><?php echo number_format($saldo, 0, ".", ".") ?></td>
                <td><?php echo number_format(($saldo/ $cambio->cot_dolar_tmp), 2, ".", ".") ?></td>
                <td id="total_gstabla"><?php echo number_format(($saldo), 0, ".", ".") ?></td>
                <td id="total_rstabla"><?php echo number_format(($saldo / $cambio->cot_real_tmp), 2, ".", ".") ?></td>
            </tr>
        </tbody>
    </table>
</div>
<div class="row">
    
    <div class="form-group col-sm-4">
        <label>Monto a cubrir (GS)</label>
        <h3><?php echo number_format($monto_venta->monto, 0, ".", ".") ?></h3>
        <input type="hidden" id="montogs" value="<?php echo ($monto_venta->monto) ?>">
        <label>MONTO PAGADO</label>
        <input type="text" class="form-control" id="monto_gs" placeholder="Ingrese el monto de pago">
    </div>
    <!-- <div class="form-group col-sm-4">
        <label>Monto a cubrir (USD)</label>
        <h3><?php //echo number_format($monto_venta->monto/$cambio->cot_dolar_tmp, 2, ".", ".") ?></h3>
        <input type="hidden" id="montousd" value="<?php //echo ($monto_venta->monto / $cambio->cot_real_tmp) ?>">
        <label>MONTO PAGADO</label>
        <input type="text" class="form-control" id="monto_usd" placeholder="Ingrese el monto de pago">
    </div>
     <div class="form-group col-sm-4">
        <label>Monto a cubrir (RS)</label>
        <h3><?php //echo number_format($monto_venta->monto/$cambio->cot_real_tmp, 2, ".", ".") ?></h3>
        <input type="hidden" id="montors" value="<?php //echo ($monto_venta->monto / $cambio->cot_real_tmp) ?>">
        <label>MONTO PAGADO</label>
        <input type="text" class="form-control" id="monto_rs" placeholder="Ingrese el monto de pago">
    </div> -->
 </div>
 <div class="row">
    <div class="form-group col-sm-4">
        <label>VUELTO GS</label>
        <h4 id="vueltogs"></h4>
    </div>
    <!-- <div class="form-group col-sm-4">
        <label>VUELTO USD</label>
        <h4 id="vueltousd"></h4>
    </div>
    <div class="form-group col-sm-4">
        <label>VUELTO RS</label>
        <h4 id="vueltors"></h4>
    </div> -->
</div><?php if ($saldo == 0) : ?>
<div align="center">
    <input type="submit" class="btn btn-primary" value="Finalizar venta" onclick="this.disabled=true;this.value='Guardando, Espere...';this.form.submit();">
</div>
<?php endif ?>
<div align="center" style='display:none' id='fin'>
    <input type="submit" class="btn btn-primary" value="Finalizar venta" onclick="this.disabled=true;this.value='Guardando, Espere...';this.form.submit();">
</div>


</form>
<br>
<div id="creditos">
    <label>Pagos</label>
    <?php if ($saldo != 0) : ?>
        <form method="POST" id="pago_frm">
            <div class="container">
                <div class="row">
                    <div class="col-sm-2">
                        <select name="pago" id="pago" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="--Sin seleccionar --" style="width:100%; display:0" required>
                       
                            <?php foreach ($this->metodo->Listar() as $m) : ?>
                                <option value="<?php echo $m->metodo ?>"><?php echo $m->metodo ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <select name="tarjeta" id="tarjeta" class="form-control" >
                            <option value="Sin seleccionar">Sin seleccionar</option>
                            <option value="CREDITO">CREDITO</option>
                            <option value="DEBITO">DEBITO</option>
                            <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-sm-2">
                        <select name="moneda" id="moneda_pago" class="form-control">
                            <option value="GS">GS</option>
                            <option value="USD">USD</option>
                            <option value="RS">RS</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" name="monto"  id="monto" class="form-control" value="<?php echo round($saldo, 2); ?>" placeholder="Ingrese el Monto">
                    </div>
                    <div class="col-sm-2">
                        <input class="btn btn-primary" type="submit" value="Agregar pago">
                    </div>
                </div>
            </div>

        </form>
    <?php endif ?>
    <br>
    <table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">
        <thead>
            <tr style="background-color: #000; color:#fff">
                <th>Pago</th>
                <th>Tarjeta</th>
                <th>Monto</th>
                <th>Moneda</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $sumaTotal = 0;
            foreach ($this->pago_tmp->Listar() as $r) : ?>
                <tr class="click">
                    
                    <td><?php echo $r->pago; ?></td>
                    <td><?php echo $r->tarjeta; ?></td>
                    <td><?php echo number_format($r->monto, 0, ".", "."); ?></td>
                    <td><?php echo $r->moneda; ?></td>
                    <td>
                        <a class="btn btn-danger eliminar" id_pago="<?php echo $r->id; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php $sumaTotal += $r->monto;
            endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #000; color:#fff">
                <th>Total cubierto</th>
                <th></th>
                <th><?php echo number_format($pagototal, 0, ".", "."); ?></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
<?php include("view/crud-modal.php"); ?>
<script>
    $('#pago_frm').on('submit', function(e) {
        e.preventDefault();
        var pago = $("#pago").val();
        var tarjeta = $("#tarjeta").val();
        var monto = $("#monto").val();
        var moneda = $("#moneda_pago").val();
        console.log('monto=' + monto);
        var url = "?c=pago_tmp&a=guardar&pago=" + pago + "&monto=" + monto + "&moneda=" + moneda+ "&tarjeta=" + tarjeta;
        $.ajax({

            url: url,
            method: "POST",
            data: pago,
            success: function(respuesta) {
                $("#pagos").html(respuesta);
                $("#monto").focus();
                $('.selectpicker').selectpicker();
            }

        })
    });

    $('.eliminar').on('click', function() {

        var id = $(this).attr("id_pago");
        var monto = $("#monto").val();
        var url = "?c=pago_tmp&a=eliminar&id=" + id;

        $.ajax({

            url: url,
            method: "POST",
            data: id,
            success: function(respuesta) {
                $("#pagos").html(respuesta);
                $('.selectpicker').selectpicker();
            }

        })

    });
    
    $('#pago').on('change', function() {
        var valor = $(this).val();
        
        if(valor =='Efectivo'){
            $("#tarjeta").val('Sin seleccionar');
            
        }
       
    });

    $('#monto_usd').on('keyup', function() {
        var valor = parseInt($(this).val());
        var monto = $("#monto").val();
        var total = $("#sub").val();
        var vuelto = valor - total;
        $("#vueltousd").html((vuelto).toLocaleString('de-DE'));
        $("#vueltors").html((vuelto*<?php echo  $cambio->cot_real_tmp; ?>).toLocaleString('de-DE'));
        $("#vueltogs").html((vuelto*<?php echo  $cambio->cot_dolar_tmp; ?> ).toLocaleString('de-DE'));
        $("#moneda_pago").val('USD');
        $("#monto").val(total);
    });
    $('#monto_rs').on('keyup', function() {monto
        var valor = parseInt($(this).val());
        var monto = $("#monto").val();
        var total = $("#montors").val();
        var vuelto = valor - total;
        var rsgs=(vuelto/<?php echo  $cambio->cot_real_tmp; ?>)*<?php echo  $cambio->cot_dolar_tmp; ?>;
        $("#vueltors").html((vuelto).toLocaleString('de-DE'));
        $("#vueltousd").html((vuelto/<?php echo  $cambio->cot_real_tmp; ?>).toLocaleString('de-DE'));
        $("#vueltogs").html((rsgs ).toLocaleString('de-DE'));
        $("#moneda_pago").val('RS');
        $("#monto").val(total);
    });
    $('#monto_gs').on('keyup', function() {
        var valor = parseInt($(this).val());
        var monto = $("#monto").val();
        var total = $("#montogs").val();
        var vuelto = valor - total;
        var gsrs=1500;
        $("#vueltogs").html((vuelto).toLocaleString('de-DE'));
        $("#vueltors").html((vuelto/gsrs).toLocaleString('de-DE'));
        $("#vueltousd").html((vuelto).toLocaleString('de-DE'));
        $("#moneda_pago").val('GS');
        $("#monto").val(total);
    });

    // $('#moneda_pago').on('change',function(){
	// 	var valor = $(this).val();
    //     var monto = $("#monto").val();
    //     var cot_dolar = $("#cot_dolar").val();
    //     var cot_real = $("#cot_real").val();
	// 	if (valor == "GS") {
    //         monto =  monto / cot_dolar
	// 		$("#creditos").hide();	
	// 	}else if(valor == "RS"){
    //         monto =  monto / cot_real
	// 		$("#creditos").show();
	// 	}else{
    //         $("#creditos").show();
    //     }
	// });

</script>
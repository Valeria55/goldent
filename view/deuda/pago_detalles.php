 
<?php $fecha = date("Y-m-d"); ?>
<h1 class="page-header">Detalles de Cobros</h1> 
<div align="center" width="30%"> 
    
</div>

<div class="table-responsive">

<table class="table table-striped table-bordered display responsive nowrap">

    <thead>
        <tr style="background-color: #5DACCD; color:#fff">
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Monto</th>
        </tr>
    </thead>
    <tbody>
    <?php
     $sumatotal = 0;
     $id_cliente = $_REQUEST['id_cliente'];
     foreach($this->model->ListarDeuda($id_cliente) as $r):  ?>

        <?php 
            $monto = $r->monto;
            $total = $r->monto * $r->cambio;
        ?>
        <tr>
            
            <td><?php echo date("d/m/Y", strtotime($r->fecha)); ?></td>
            <td><?php echo $r->comprobante; ?></td>
            <td><?php echo number_format($monto, 0, "," , "."); echo ('  '.$r->moneda ); 
                if ($r->cambio != 1) {
                echo ' ' . $moneda . ' -> ';
                echo number_format($total, 0, "," , ".") . ' Gs'; // Imprimir el monto convertido a Gs
                    } ?>
            </td>
        </tr>


    <?php
        
        $sumatotal += $total ;
        
        endforeach; ?>
        
        
        <tr>
            <td></td>
            <td></td>
            <td>Total: <div id="total" style="font-size: 20px"><?php echo number_format($sumatotal, 0, ",", ".") ?> Gs</div></td>
        </tr>
    </tbody>
</table> 
</div> 
</div>
</div>

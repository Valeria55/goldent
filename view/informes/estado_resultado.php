<?php

$fecha = (isset($_GET["fecha"]))? $_GET["fecha"]."-10":date("Y-m-d");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
?>

<h1 align="center">Estado de Resultados
</h1>
<hr />
<table width="20%" align="center">
    <tr>
        <td>
        <form action="?c=venta&a=estado_resultado">
            <table>  
            <tr>
                <td>
                <input type="hidden" name="c" value="venta">
                <input type="hidden" name="a" value="EstadoResultado">
                <input type="month" name="fecha" value="<?php echo date("Y-m", strtotime($fecha)) ?>" class="form-control">
                </td>
                <td>
                <input class="btn btn-primary" type="submit" name="buscar" value="Buscar">
                </td>
            </tr> 
            </table>  
        </form>
        </td>
    </tr>
</table>
<hr />
<div class="col-sm-6" align="center" style="border-right: 1px solid #d6d6d6">
<div class="content">

<h2 class="page-header">Resultados </h2>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Id venta</th>
            <th>Costo</th>
            <th>Venta</th>
            <th>Utilidad</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $sumaCosto=0;$sumaTotal=0;$sumaGanancia=0;
    $mes = (isset($_GET['m']))? $_GET['m']:0; 
     foreach($this->venta->utilidad($fecha) as $r): 
        $sumaCosto += $r->costo;
        $sumaTotal += $r->total; 
        $sumaGanancia += $r->ganancia;
        ?>
        <tr class="click">
            <td><?php echo $r->id_venta; ?></td>
            <td><?php echo number_format($r->costo,0,",","."); ?></td>
            <td><?php echo number_format($r->total,0,",","."); ?></td>
            <td><?php echo number_format($r->ganancia, 0,",","."); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>  
        <tr style="background-color: black; color:#fff">
            <td align="right"><b>Total : </b></td>
            <td align="right"><b><?php echo number_format($sumaCosto,0,",","."); ?></b></td>
            <td align="right"><b><?php echo number_format($sumaTotal,0,",","."); ?></b></td>
            <td><b><?php echo number_format($sumaGanancia,0,",","."); ?></b></td>
          
        </tr>
    </tfoot>
</table>
</div>
</div>

<div class="col-sm-6" align="center">
<div class="content">

<h2 class="page-header">Lista de egresos </h2>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Concepto</th>
            <th>Monto (Gs)</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $egreso=0;
    $mes = (isset($_GET['m']))? $_GET['m']:0; 
    foreach($this->egreso->AgrupadoFechaMes($fecha) as $r): ?>
        <tr class="click">
            <?php $egreso = ($egreso + $r->monto); ?>
            <td><?php echo $r->categoria; ?></td>
            <td><?php echo number_format($r->monto,0,",","."); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>  
    <tfoot>  
        <tr style="background-color: black; color:#fff">
            <td align="right"><b>Total : </b></td>
            <td><b><?php echo number_format($egreso,0,",","."); ?></b></td>
        </tr>
    </tfoot>  
</table>
</div>
</div>

<div class="col-sm-12">
</div>
<?php include("view/crud-modal.php"); ?>
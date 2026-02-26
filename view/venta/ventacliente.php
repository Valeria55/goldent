<?php
if (!isset($_GET['id_cliente'])) {
    die('Falta parámetro id_cliente');
}

$hoy = date('Y-m-d');
$primerDiaMes = date('Y-m-01');
$desdeVal = $_GET['desde'] ?? $primerDiaMes;
$hastaVal = $_GET['hasta'] ?? $hoy;
?>

<h1 class="page-header">Compras del cliente</h1>
<a class="btn btn-primary pull-right" href="?c=venta_tmp" class="btn btn-success">Nueva venta</a>

<form id="form-informe-pdf" method="get" action="index.php" style="margin: 10px 0 20px 0;" onsubmit="return abrirInformePdfFlotante(this);">
    <input type="hidden" name="c" value="venta">
    <input type="hidden" name="a" value="InformePorCliente">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id_cliente'], ENT_QUOTES, 'UTF-8'); ?>">

    <label style="margin-right:6px;">Desde</label>
    <input type="date" name="desde" value="<?php echo htmlspecialchars($desdeVal, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="display:inline-block; width:auto; margin-right:10px;">

    <label style="margin-right:6px;">Hasta</label>
    <input type="date" name="hasta" value="<?php echo htmlspecialchars($hastaVal, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="display:inline-block; width:auto; margin-right:10px;">

    <button type="submit" class="btn btn-info">INFORME .PDF</button>
</form>

<table class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Fecha</th>
            <th>Vendedor</th>
            <th>Cliente</th>
            <th>Sub tot. (Gs.)</th>
            <th>Desc.</th>
            <th>Total (Gs.)</th> 
            <th></th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0;  
    foreach($this->model->ListarCliente($_GET['id_cliente']) as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
            <td><?php echo date("d/m/Y", strtotime($r->fecha_venta)); ?></td>
            <td><?php echo $r->vendedor; ?></td>
            <td><?php echo $r->nombre_cli; ?></td>
            <td><?php echo number_format($r->subtotal,0,".",","); ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><?php echo number_format($r->total,0,".",","); ?></td>
        
            <td>
                <a href="#detallesModal" class="btn btn-success" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta;?>">Ver</a>
                <?php echo ($r->anulado)? " ANULADO":"";?>
            </td>
           
        </tr>
    <?php 
        $count++;
    endforeach; ?>
    </tbody>
    
</table>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
<?php include("view/venta/detalles-modal.php"); ?>

<script type="text/javascript">
function abrirVentanaFlotante(url, titulo) {
    var width = 900;
    var height = 700;
    var left = (screen.width / 2) - (width / 2);
    var top = (screen.height / 2) - (height / 2);

    var features = 'width=' + width +
        ',height=' + height +
        ',left=' + left +
        ',top=' + top +
        ',scrollbars=yes' +
        ',resizable=yes' +
        ',toolbar=no' +
        ',menubar=no' +
        ',location=no' +
        ',status=no';

    var ventanaFlotante = window.open(url, titulo.replace(/\s+/g, '_'), features);
    if (ventanaFlotante) {
        ventanaFlotante.focus();
    } else {
        alert('Por favor, permite las ventanas emergentes para ver el documento PDF.');
    }
}

function abrirInformePdfFlotante(formEl) {
    try {
        var params = new URLSearchParams(new FormData(formEl));
        var url = (formEl.getAttribute('action') || 'index.php') + '?' + params.toString();
        abrirVentanaFlotante(url, 'Informe PDF');
    } catch (e) {
        // Fallback: si el navegador no soporta URLSearchParams/FormData en este contexto
        formEl.target = '_blank';
        return true;
    }

    return false; // evitar navegación normal
}
</script>


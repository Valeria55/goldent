<?php
if (!isset($_GET['id_cliente'])) {
    die('Falta parámetro id_cliente');
}

$hoy = date('Y-m-d');
$primerDiaMes = date('Y-m-01');
$desdeVal = $_GET['desde'] ?? $primerDiaMes;
$hastaVal = $_GET['hasta'] ?? $hoy;
$pacienteVal = $_GET['paciente'] ?? '';
$controllerVal = $_GET['c'] ?? 'venta';

$useFilters = isset($_GET['desde']) || isset($_GET['hasta']) || isset($_GET['paciente']);
?>

<h1 class="page-header">Compras del cliente</h1>
<a class="btn btn-primary pull-right" href="?c=venta_tmp" class="btn btn-success">Nueva venta</a>

<form id="form-filtros" method="get" action="index.php" style="margin: 10px 0 20px 0;">
    <input type="hidden" name="c" value="<?php echo htmlspecialchars($controllerVal, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
    <input type="hidden" name="a" value="listarcliente">
    <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($_GET['id_cliente'], ENT_QUOTES, 'UTF-8'); ?>">

    <label style="margin-right:6px;">Desde</label>
    <input type="date" name="desde" value="<?php echo htmlspecialchars($desdeVal, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="display:inline-block; width:auto; margin-right:10px;">

    <label style="margin-right:6px;">Hasta</label>
    <input type="date" name="hasta" value="<?php echo htmlspecialchars($hastaVal, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="display:inline-block; width:auto; margin-right:10px;">

    <label style="margin-right:6px;">Paciente</label>
    <input type="text" name="paciente" value="<?php echo htmlspecialchars($pacienteVal, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" class="form-control" style="display:inline-block; width:220px; margin-right:10px;" placeholder="Nombre / apellido">

    <button type="submit" class="btn btn-success">Filtrar</button>
    <?php if ((string)$controllerVal === 'venta') { ?>
        <button type="button" class="btn btn-info" onclick="abrirInformePorClientePdf();">INFORME .PDF</button>
    <?php } ?>
</form>

<table class="table table-striped table-bordered display responsive nowrap datatable">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Venta</th>
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Cliente</th>
            <th>Paciente</th>
            <th>Sub tot. (Gs.)</th>
            <th>Desc.</th>
            <th>Total (Gs.)</th> 
            <th></th>
    </thead>
    <tbody>
    <?php 
    $suma = 0; $count = 0; 
    $idCliente = $_GET['id_cliente'];
    if ($useFilters && method_exists($this->model, 'ListarClienteFiltros')) {
        $lista = $this->model->ListarClienteFiltros($idCliente, $desdeVal, $hastaVal, $pacienteVal);
    } else {
        $lista = $this->model->ListarCliente($idCliente);
    }
    foreach($lista as $r): ?>
        <tr class="click" <?php if($r->anulado){echo "style='color:gray'";} ?>>
            <td><?php echo $r->id_venta; ?></td>
            <td><?php echo date("d/m/Y", strtotime($r->fecha_venta)); ?></td>
            <td><?php echo $r->nro_comprobante; ?></td>
            <td><?php echo $r->nombre_cli; ?></td>
            <td><?php echo $r->paciente; ?></td>
            <td><?php echo number_format($r->subtotal,0,".",","); ?></td>
            <td><?php echo $r->descuento; ?></td>
            <td><?php echo number_format($r->total,0,".",","); ?></td>
        
            <td>
                <a href="#detallesModal" class="btn btn-success js-ver-detalles" data-toggle="modal" data-target="#detallesModal" data-id="<?php echo $r->id_venta;?>">Ver</a>
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

<script type="text/javascript">
// Al abrir el modal, forzar scroll al inicio de la página
// (Bootstrap suele hacer preventDefault del href, así que lo hacemos manual)
document.addEventListener('click', function (ev) {
    var el = ev.target;
    if (!el) return;

    // Por si se hace click en un elemento interno
    if (el.closest) {
        el = el.closest('.js-ver-detalles');
    }
    if (!el || !el.classList || !el.classList.contains('js-ver-detalles')) return;

    try {
        window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
    } catch (e) {
        window.scrollTo(0, 0);
    }
}, true);
</script>

<script type="text/javascript">
function abrirInformePorClientePdf() {
    var formEl = document.getElementById('form-filtros');
    if (!formEl) return;

    try {
        var params = new URLSearchParams(new FormData(formEl));
        params.set('c', 'venta');
        // cambiar acción a InformePorCliente y enviar id
        params.set('a', 'InformePorCliente');
        params.set('id', params.get('id_cliente') || '');
        var url = (formEl.getAttribute('action') || 'index.php') + '?' + params.toString();
        abrirVentanaFlotante(url, 'Informe PDF');
    } catch (e) {
        // fallback: abrir en nueva pestaña
        var action = formEl.getAttribute('action') || 'index.php';
        window.open(action, '_blank');
    }
}
</script>


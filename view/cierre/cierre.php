<h1 class="page-header">Lista de cierres de caja en efectivo </h1>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'actualizado'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>¡Éxito!</strong> Los montos del cierre han sido actualizados correctamente.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> No se pudieron actualizar los montos del cierre.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<h3 id="filtrar" align="center">Filtrar por fecha <i class="fas fa-angle-down"></i><i class="fas fa-angle-up" style="display: none"></i></h3>
<div class="container">
    <div class="row">
        <div class="col">
            <div align="center" id="filtro">
                <form method="get" class="form-inline">
                    <input type="hidden" name="c" value="cierre">
                    <div class="form-group">
                        <label>Desde</label>
                        <input type="datetime-local" name="desde" value="<?php echo (isset($_GET['desde'])) ? $_GET['desde'] : ''; ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Hasta</label>
                        <input type="datetime-local" name="hasta" value="<?php echo (isset($_GET['hasta'])) ? $_GET['hasta'] : ''; ?>" class="form-control" required>
                    </div>
                    <input type="submit" name="filtro" value="Filtrar" class="btn btn-success">
                </form>
            </div>
        </div>
    </div>
</div>
<p> </p>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Usuario</th>
            <th>Apertura</th>
            <th>Cierre</th>
            <th>Monto apertura</th>
            <th>Monto ingreso</th>
            <th>Monto egreso</th>
            <!--Monto del sistema -->
            <th>Cierre sistema</th>
            <th>Cierre usuario</th>
            <th>Diferencia</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sumaSistema = 0;
        $sumaCierre = 0;
        $sumaCierreTotal = 0; // Nueva variable para el cierre total convertido
        $sumaAperturaTotal = 0; // Nueva variable para la apertura total convertida
        $sumadiferencia = 0;
        $sumaegreso = 0;
        $sumaapertura = 0;
        $desde = (isset($_GET["desde"])) ? $_GET["desde"] : 0;
        $hasta = (isset($_GET["hasta"])) ? $_GET["hasta"] : 0;
        foreach ($this->model->Listar30Dias($desde, $hasta) as $r): ?>
            <tr class="click">
                <td><?php echo $r->user; ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_apertura)); ?></td>
                <td><?php echo date("d/m/Y H:i", strtotime($r->fecha_cierre)); ?></td>
                <td><?php echo number_format($r->apertura_total_convertido, 0, ".", ","); ?></td>
                <td><?php echo number_format($r->monto_sistema, 0, ".", ","); ?></td>
                <td><?php echo number_format($r->monto_egreso, 0, ".", ","); ?></td>
                <td><?php echo number_format($r->apertura_total_convertido + $r->monto_sistema - $r->monto_egreso, 0, ".", ","); ?></td>
                <td><?php echo number_format($r->cierre_total_convertido, 0, ".", ","); ?></td>
                <td><?php echo number_format(($r->cierre_total_convertido - (($r->monto_sistema + $r->apertura_total_convertido) - $r->monto_egreso)), 0, ".", ","); ?></td>
                <td>
                    <a href="?c=cierre&a=detalles&id=<?php echo $r->id; ?>" class="btn btn-warning">Ver detalles</a>
                    <a href="?c=cierre&a=cierrepdf&id_cierre=<?php echo $r->id; ?>" class="btn btn-info" target="_blank" onclick="abrirPDFFlotante(this.href); return false;" title="Abrir informe en ventana flotante">
                        <i class="fas fa-file-pdf"></i> Informe
                    </a>
                    <?php if($_SESSION['nivel'] == 1): ?>
                    <button type="button" class="btn btn-success btn-sm" 
                            onclick="abrirModalEditarCierre(
                                '<?php echo $r->id; ?>',
                                '<?php echo addslashes($r->user); ?>',
                                '<?php echo date("d/m/Y H:i", strtotime($r->fecha_apertura)); ?>',
                                '<?php echo date("d/m/Y H:i", strtotime($r->fecha_cierre)); ?>',
                                '<?php echo $r->monto_apertura; ?>',
                                '<?php echo $r->monto_cierre; ?>',
                                '<?php echo $r->apertura_rs ?? 0; ?>',
                                '<?php echo $r->monto_cierre_rs ?? 0; ?>',
                                '<?php echo $r->apertura_usd ?? 0; ?>',
                                '<?php echo $r->monto_cierre_usd ?? 0; ?>',
                                '<?php echo $r->cot_dolar ?? 0; ?>',
                                '<?php echo $r->cot_real ?? 0; ?>'
                            )">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php
            $sumadiferencia += ($r->cierre_total_convertido - (($r->monto_sistema + $r->apertura_total_convertido) - $r->monto_egreso));
            $sumaSistema += $r->monto_sistema;
            $sumaCierre += $r->monto_cierre;
            $sumaCierreTotal += $r->cierre_total_convertido; // Sumar el cierre total convertido
            $sumaAperturaTotal += $r->apertura_total_convertido; // Sumar la apertura total convertida
            $sumaegreso += $r->monto_egreso;
            $sumaapertura += $r->monto_apertura;
        endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: black; color:#fff">
            <th></th>
            <th></th>
            <th>Total:</th>
            <th><?php echo number_format($sumaAperturaTotal, 0, ".", ","); ?></th>
            <th><?php echo number_format($sumaSistema, 0, ".", ","); ?></th>
            <th><?php echo number_format(($sumaegreso), 0, ".", ","); ?></th>
            <th></th>
            <th><?php echo number_format($sumaCierreTotal, 0, ".", ","); ?></th>
            <th><?php echo number_format($sumadiferencia, 0, ".", ","); ?></th>
            <th></th>
        </tr>
    </tfoot>
</table>
</div>
</div>
</div>

<!-- Incluir el modal de editar cierre -->
<?php include 'view/cierre/editar-cierre.php'; ?>

<script type="text/javascript">
    $("#filtrar").click(function() {
        $("#filtro").toggle("slow");
        $("i").toggle();
    });

    // Función para abrir PDF en ventana flotante
    function abrirPDFFlotante(url) {
        // Configuración de la ventana flotante optimizada
        const ancho = Math.min(1200, window.screen.availWidth - 100);
        const alto = Math.min(800, window.screen.availHeight - 100);
        const left = (window.screen.availWidth - ancho) / 2;
        const top = (window.screen.availHeight - alto) / 2;
        
        const configuracion = `
            width=${ancho},
            height=${alto},
            left=${left},
            top=${top},
            scrollbars=yes,
            resizable=yes,
            menubar=no,
            toolbar=yes,
            location=no,
            status=yes,
            titlebar=yes
        `;
        
        // Abrir ventana flotante con título descriptivo
        const ventanaPDF = window.open(url, 'InformeCierrePDF', configuracion);
        
        // Dar foco a la ventana flotante
        if (ventanaPDF) {
            ventanaPDF.focus();
        } else {
            // Fallback si el popup fue bloqueado
            alert('El navegador bloqueó la ventana emergente. Por favor, permite las ventanas emergentes para este sitio.');
        }
        
        return false;
    }
</script>
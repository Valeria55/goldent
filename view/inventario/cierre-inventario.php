<h1 class="page-header">Informe del Inventario
    <?php if (!$this->model->CierrePendiente()) : ?>
        <a class="btn btn-primary" onclick="Swal.fire({
        title: 'Cargando',
        html: 'Registrando productos, por favor, aguarde.',
        showConfirmButton: false,
        backdrop: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        onOpen: () => {
            swal.showLoading();
        },
        }).then((result) => {
        /* Read more about handling dismissals below */

        })" href="?c=inventario&a=guardar" class="btn btn-success">Nuevo Inventario
        </a>
    <?php endif; ?>
</h1>
<?php if ($_SESSION['nivel'] <= 1) ?>

<p> </p>
<table class="table table-striped table-bordered display responsive datatable" width="100%">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Fecha de Apertura</th>
            <th>Fecha de Cierre</th>
            <th>Motivo</th>
            <th>Sobrante Caja</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $lista = $this->model->Listar();
        foreach ($lista as $c) :  ?>
            <tr>
                <td><?php echo date("d/m/Y H:i", strtotime($c->fecha_apertura)); ?></td>
                <td><?php if ($c->fecha_cierre) echo date("d/m/Y H:i", strtotime($c->fecha_cierre)); ?></td>
                <td><?php echo $c->motivo; ?></td>
                <td><?php echo number_format($c->sobrante_caja); ?></td>
                <td align="center">
                    <?php $finalizado = !is_null($c->fecha_cierre); ?>
                    <?php if ($finalizado) : ?>
                        <a class="btn btn-primary" href="?c=inventario&a=inventario&id_c=<?php echo ($c->id) ?>">
                            Detalles
                        </a>
                    <?php else : ?>
                        <a class="btn btn-warning" href="?c=inventario&a=ListarPorIdC&id_c=<?php echo ($c->id) ?>">
                            Continuar
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); ?>
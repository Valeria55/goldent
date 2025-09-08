<h1 class="page-header">Lista sesiones activas</h1>

<br><br><br>

<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%">

    <thead>
        <tr style="background-color: black; color:#fff">
            <th>Usuario</th>
            <th>Fecha</th>
        	<th>Apertura GS</th>
            <th>Apertura RS</th>
            <th>Apertura USD</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $lista = $this->model->ListarActivas();
    
    foreach($lista as $r): ?>
        <tr class="click">
            <td><?php echo $r->user; ?></td>
        	<td><?php echo date("d/m/Y H:i", strtotime($r->fecha_apertura)); ?></td>
            <td><?php echo number_format($r->monto_apertura,0,",","."); ?></td>
            <td><?php echo number_format($r->apertura_rs,2,".",","); ?></td>
            <td><?php echo number_format($r->apertura_usd,2,".",","); ?></td>
            <td>
                <a href="?c=cierre&a=movimientos&id=<?php echo $r->id_usuario; ?>&fecha=<?php echo $r->fecha_apertura; ?>" class="btn btn-warning">Ver detalles</a>
                <a href="?c=cierre&a=cierrepdf&id_cierre=<?php echo $r->id; ?>" class="btn btn-info">Informe</a>
            </td>    
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>

<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
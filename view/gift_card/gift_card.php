<h1 class="page-header">Gift Card</h1>
<a class="btn btn-primary pull-right" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="gift_card">Agregar</a>
<br><br><br>
<table class="table table-striped table-bordered display responsive nowrap datatable" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>N° de tarjeta </th>
            <th>Funcionario</th>
            <th>Cliente</th> 
            <th>Monto</th>
            <th>Fecha</th>
            <th></th>
            
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->model->Listar() as $r): ?>
        
        <tr class="click">
            <td><?php echo $r->nro_tarjeta; ?></td>
            <td><?php echo $r->funcionario; ?></td>
            <td><?php echo $r->cliente; ?></td>
            <td><?php echo number_format($r->monto,0,",",","); ?></td>
            <td><?php echo $r->fecha; ?></td>
             <?php if(($r->retirado!='RETIRADO')) { ?>
            <?php if(($r->anulado==1)) { ?>
            <td align="center" width="15%">
               ANULADO
            </td>
        <?php }else{ ?>
            <td align="center" width="15%">
             <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="gift_card">Editar</a>
             <a  class="btn btn-danger delete " href="?c=gift_card&a=Anular&id=<?php echo $r->id; ?>">Anular</a>
             <!--<a  class="btn btn-danger info " href="?c=gift_card&a=Ticket&id=<?php // echo $r->id; ?>">Ticket</a>-->
             
            </td>
        <?php }?>
         <?php }else{ ?>

               <td align="center" width="15%"><?php echo $r->retirado; ?></td>
            <?php }?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


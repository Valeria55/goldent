<h1 class="page-header">Lista de Productos
</h1>
<br>
<?php if ($_SESSION['nivel'] == 10) { ?>
    <div class="container" style="display:none;">
        <div class="row">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-4">
                <div align="center" id="filtro">
                    <form method="get">
                        <input type="hidden" name="c" value="producto">
                        <div class="form-group">
                            <div class="form-group">
                                <label>Sucursal</label>
                                <select name="sucursal" class="form-control">
                                    <?php foreach ($this->sucursal->Listar() as $r) : ?>
                                        <option value="<?php echo $r->id; ?>" <?php if (isset($_GET['sucursal']) && $_GET['sucursal'] == $r->id) {
                                                                                    echo "selected";
                                                                                } ?>><?php echo $r->sucursal; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-4">
            </div>
        </div>
    </div>
<?php } ?>
<table id="tabla" class="table responsive display" style="width:100%">
    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Id</th>
            <th>Código</th>
            <th>Marca</th>
            <th>Categoría</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Stock</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php /*$sumaCosto=0;
            $q = (isset($_REQUEST['sucursal']))? $_REQUEST['sucursal']:"";
            foreach($this->model->ListarBuscar($q) as $r): 
            if(true){ ?>
            
            <tr class="click">
                <td><?php echo $r->codigo; ?></td>
                <td><?php echo substr($r->categoria,0,15); ?></td>
                <td><?php echo substr($r->categoria,0,15); ?></td>
                <td><a href="?c=venta&a=listarproducto&id_producto=<?php echo $r->id; ?>"><?php echo substr($r->producto,0,100); ?></a></td>
                <td><?php echo number_format($r->precio_costo,0,".",","); ?></td>
                <td><?php echo number_format($r->precio_minorista,0,".",","); ?></td>
                <td><?php echo $r->stock; ?></td>
                <td><?php echo $r->iva; ?></td>
                <?php if($_SESSION['nivel']<=1){ ?>
                <td>
                    <a  class="btn btn-warning edit" href="#crudModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-id="<?php echo $r->id;?>" data-c="producto">Edit</a>
                </td>
                <td>
                    <a  class="btn btn-danger delete" href="?c=producto&a=Eliminar&id=<?php echo $r->id; ?>">Borrar</a>
                </td>
                <?php } ?>
            </tr>
            <?php $sumaCosto+=($r->precio_costo*$r->stock); } endforeach; */ ?>

    </tbody>
    <tfoot>
        <tr style="background-color: #000; color:#fff">
            <th>Id</th>
            <th>Código</th>
            <th>Marca</th>
            <th>Categoría</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Stock</th>
            <th></th>
        </tr>
    </tfoot>
</table>
</div>
</div>
</div>
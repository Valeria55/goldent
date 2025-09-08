<div id="transferenciaModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <ol class="breadcrumb">
                  <li>Transferencia</li>
                </ol>

                <form method="post" action="?c=caja&a=transferencia" enctype="multipart/form-data">
                    <input type="hidden" name="c" value="caja" id="c"/>
                    
                    <div class="form-group">
                        <label>Transferir de</label>
                        <select name="id_emisor" id="id_emisor" class="form-control">
                            <?php 
                            foreach($this->caja->ListarTodo() as $r): 
                                if($r->id!=1){
                            ?>
                                <option value="<?php echo $r->id ?>"><?php echo $r->caja ?></option>
                            <?php
                                }
                            endforeach; 
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Transferir a</label>
                        <select name="id_receptor" id="id_receptor" class="form-control">
                            <?php 
                            foreach($this->caja->ListarTodo() as $r): 
                            ?>
                                <option value="<?php echo $r->id ?>" id="<?php echo $r->id ?>"><?php echo $r->caja ?></option>
                            <?php
                            endforeach; 
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Monto</label>
                        <input type="number" name="monto" step="any" value="0" min="0" class="form-control" placeholder="Ingrese su monto" required>
                    </div>
                    
                    <hr />
                    
                    <div class="text-right">
                        <button class="btn btn-primary">Transferir</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
            
        </div>
    </div>
</div>
<script >
    $( "#id_emisor" ).change(function() {
      var caja = $(this).val();
      $("#"+caja).hide();
    });
</script>
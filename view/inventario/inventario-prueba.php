<h1 class="page-header">Inventario </h1>
<a class="btn btn-primary pull-right" href="?c=inventario&a=guardar" class="btn btn-success">Nuevo Inventario</a>
<br><br><br>
<h3 id="filtrar" align="center">Filtros <i class="fas fa-angle-right"></i><i class="fas fa-angle-left" style="display: none"></i></h3>
<div class="row">
    <div class="col-sm-12">
        <div align="center" id="filtro">
            <form method="get">
                <input type="hidden" name="c" value="inventario">
                
                 <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?php //echo (isset($_GET['desde']))? $_GET['desde']:''; ?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?php //echo (isset($_GET['hasta']))? $_GET['hasta']:''; ?>" class="form-control">
                </div>
                
                <div class="form-group col-md-2">
                    <label></label>
                    <input type="submit" value="Filtrar" class="form-control btn btn-success">
                </div>
                
            </form>
        </div>
    </div>
</div>
<table class="table table-striped table-bordered display responsive nowrap" width="100%" id="tabla">

    <thead>
        <tr style="background-color: #000; color:#fff">
            <th>Usuario</th> 
            <th>Código</th>
            <th>Producto</th>    
            <th>Stock Actual</th>    
            <th>Stock Real</th>
            <th>Faltante</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php /* foreach($this->model->Listar() as $r): ?>
        
        <tr class="click">
            <td><?php echo $r->usuario; ?></td>
            <td><?php echo $r->codigo; ?></td>
            <td><?php echo $r->producto; ?></td>
            <td><?php echo $r->stock_actual; ?></td>
             <td align="center" width="10%">
<!--Tiene un campo que se puede tipear <!-->
                <input  id="<?php echo $r->id_i; ?>" stock_real="<?php echo $r->stock_real; ?>"  name="stock_real" class="stock_real" type="number" value="<?php echo $r->stock_real; ?>" id="stock_real">
            </td>
            <td><?php echo $r->stock_actual - $r->stock_real; ?></td>
          
            <td>
                <a  class="btn btn-danger delete" href="?c=inventario&a=Eliminar&id_i=<?php echo $r->id_i; ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; */?>
    </tbody>

</table>
</div>
</div>
</div> 
<?php include("view/crud-modal.php"); ?>


!--<script type="text/javascript">
    $(document).ready(function() {
        
        let tablaInventario = $('#tabla').DataTable({
            
             "dom": 'Bfrtip',
            "buttons": [{
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            }, {
                extend: 'pdfHtml5',
                footer: true,
                title: "Inventario",
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,6,7,9]
                }
            }, 'colvis'],
           
            responsive: {
                details: true
            },
             <?php if(isset($_GET['desde'])){ ?>
              "ajax":{            
                    "url": "?c=inventario&a=ListarFiltros&desde=<?php echo $_GET['desde']?>&hasta=<?php echo $_GET['hasta']?>",
                    "dataSrc":""
                },
            <?php }else{ ?>
            
                    "ajax":{            
                    "url": "?c=inventario&a=ListarAjax",
                    "dataSrc":""
                },    
                <?php } ?>


            "columns":[
                {"data": "usuario"},
                {"data": "codigo"},
                {"data": "producto"},
                {"data": "stock_actual"},
                {"data" : "stock_real",
           "render" : function ( data, type, row, meta ){
                            return "<input id=\"id_i\" name=\"stock_real\" value=\""+ data +"\" onclick=\"startThisActivity('" + data + "')\" > ";
                      }
    },
                {"data": "faltante"},
                {"defaultContent": "",
                    render: function(data, type, row) {
                       
                           
                               return "<a href='#detallesModal' class='btn btn-info' data-toggle='modal' data-target='#detallesModal' data-c='inventario' data-id='"+row.id_i+"'>Ver</a>";
                             
                        }    
                 },

                 {"defaultContent": "",
                    render: function(data, type, row) {
                        //anula el dato
                        if(row.anulado==1){
                            return 'ANULADO'
                        }else{
                       
                            let link = "?c=inventario&a=anular&id="+row.id_i;
                            return '<a href="' + link + '" class="btn btn-danger">Eliminar</a>';
                        
                        }
                        
                    }
                 }

                ],
              
        });
    });
       $('.stock_real').on('change',function(){
        
        var stock_real = $(this).val();
        var id = parseInt($(this).attr("id"));
        alert(id);
        //La accion es StockReal
        var url = "?c=inventario&a=StockReal&id="+id+"&stock_real="+stock_real;
            $.ajax({

                url: url,
                cache: false,
                contentType: false,
                processData: false,
                success:function(respuesta){
                    //$("#precioTotal"+idItem).html(precio);
                    //$("#stock_real").html(respuesta);
                    //location.reload(true);
                    //alert(respuesta);
                }

            })
    });

    $("#link").val(window.location.href);
</script><!-->
<script type="text/javascript">
    $( "#filtrar" ).click(function() {
      $("#filtro").toggle("slow");
      $("i").toggle();
    });
</script>
</table>
</div>
</div>
</div>


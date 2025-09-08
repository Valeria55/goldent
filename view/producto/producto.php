<h1 class="page-header">Lista de Productos</h1>
<a class="btn btn-primary pull-right" href="#productoModal" class="btn btn-success" data-toggle="modal" data-target="#crudModal" data-c="producto">Agregar</a>
<br><br><br>
<div class="container">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <div align="center" id="filtro">
            <form method="get">
                <input type="hidden" name="c" value="producto">
                <div class="form-group">
                   <div class="form-group">
                        <label>Marca</label>
                        <select name="marca" class="form-control selectpicker" data-show-subtext="true" data-live-search="true" data-style="form-control" title="-- Seleccione la marca --" autofocus >
                            <option value="">Todos</option>
                            <?php foreach ($this->marca->Listar() as $r) : ?>
                                <option value="<?php echo $r->id; ?>" <?php echo ($r->id == $producto->marca) ? "selected" : ""; ?>><?php echo $r->marca; ?></option>
            
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <input type="submit" name="filtro" value="Filtrar" class="btn btn-success"> 
            </form>
        </div>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div>
<table id="tabla" class="table responsive display" style="width:100%">
        <thead>
            <tr style="background-color: #000; color:#fff">
                <th>ID</th>
                <th>Código</th>
                <th>Marca</th>
                <th>Categoría</th>
                <th>Producto</th>
                <th>Costo</th>
                <th>MIN</th>
                <th>INT</th>
                <th>MAY</th>
                <th>Especial</th>
                <th>Stock</th>
                <th>IVA</th>
                <th></th>
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
            <?php $sumaCosto+=($r->precio_costo*$r->stock); } endforeach; */?>
            
        </tbody>
        <tfoot>
            <tr style="background-color: #000; color:#fff">
                <th>ID</th>
                <th>Código</th>
                <th>Marca</th>
                <th>Categoría</th>
                <th>Producto</th>
                <th>Costo</th>
                <th>MIN</th>
                <th>INTER</th>
                <th>MAY</th>
                <th>Especial</th>
                <th>Stock</th>
                <th>IVA</th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
</div>
</div>
<?php include("view/crud-modal.php"); 
session_start();
?>

<script type="text/javascript">
    $(document).ready(function() {
        
        $('#tabla tfoot th').each( function () {
        var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        } );
     
        // DataTable
        let tablaUsuarios = $('#tabla').DataTable({
            
             <?php if (isset($_GET['marca'])) { ?> "ajax": {
                    "url": "?c=producto&a=ListarFiltros&marca=<?php echo $_GET['marca'] ?>",
                    "dataSrc": ""
                },
            <?php } else { ?>

                "ajax": {
                    "url": "?c=producto&a=ListarAjax",
                    "dataSrc": ""
                },
            <?php } ?>
            
            "columns":[
                {"data": "id"},
                {"data": "codigo",
                    render: function(data, type, row) {
                        let link = "?c=venta&a=listarproducto&id_producto="+row.id;
                        return '<a href="' + link + '" class="btn btn-default">' + data + '</a>';
                    }
                },
                {"data": "marca"},
                {"data": "categoria"},
                {"data": "producto"},
                {"data": "precio_costo",render: $.fn.dataTable.render.number(',', '.', 0)},
                // {"data": "precio_brasil",render: $.fn.dataTable.render.number('.', ',', 0)},
                {"data": "precio_minorista", render: $.fn.dataTable.render.number(',', '.', 0)},
                {"data": "precio_intermedio", render: $.fn.dataTable.render.number(',', ',', 0)},
                {"data": "precio_mayorista", render: $.fn.dataTable.render.number(',', '.', 0)},
                {"data": "ultimo_precio", render: $.fn.dataTable.render.number(',', '.', 0)},
                {"data": "stock_s1"},
                {"data": "iva"},
                 
                {"defaultContent": "",
                
                    render: function(data, type, row) {
                        return "<a href='#crudModal' class='btn btn-warning' data-toggle='modal' data-target='#crudModal' data-c='producto' data-id='"+row.id+"'>Editar</a>";
                    }
                },
                 {"defaultContent": "",
                
                    render: function(data, type, row) {
                        let link = "?c=producto&a=Eliminar&id="+row.id;
                        return '<a href="' + link + '" class="btn btn-danger">Eliminar</a>';
                    }
                }
                 
            ],
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'portrait',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                'colvis'
            ],
            "stateSave": true,
        	responsive: {
            	details: true
        	},
            "language": {
                "lengthMenu":"Mostrar _MENU_ registros por página.",
                "search" : "Buscar en todos",
                "buttons": {
                    "colvis": "Columnas Visibles"
                },
                "zeroRecords": "Lo sentimos. No se encontraron registros.",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros aún.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "LoadingRecords": "Cargando ...",
                "Processing": "Procesando...",
                "SearchPlaceholder": "Comience a teclear...",
                "paginate": {
         			"previous": "Anterior",
         			"next": "Siguiente", 
          	    }
            },
            initComplete: function () {
                // Apply the search
                this.api().columns().every( function () {
                    var that = this;
     
                    $( 'input', this.footer() ).on( 'keyup change clear', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    } );
                } );
            }
        });
        
        
     
        
        $("#tabla tbody").on("click", ".btnBorrar", function(){
        
            let data = tablaUsuarios.row($(this).parents()).data();        
            id = data.id;		       
            var respuesta = confirm("¿Está seguro de borrar "+data.producto+"?");                
            
            var url = "?c=producto&a=Eliminar&id="+id;
            tablaUsuarios.row(0).remove().draw();
    		$.ajax({
    
    			url: url,
    			method : "POST",
    			data: id,
    			cache: false,
    			contentType: false,
    			processData: false,
    			success:function(respuesta){
    			}
    
    		})
        });
        
        $("#tabla tbody").on("click", ".btnEditar", function(){
        
            let data = tablaUsuarios.row($(this).parents()).data();        
            var id = data.id;
            //var id = parseInt($(this).closest('tr').find('td:eq(0)').text()) ;	
            $('#crudModal').modal('show'); 
            var url = "?c=producto&a=obtener&id="+id;
    		$.ajax({
    
    			url: url,
    			method : "POST",
    			data: id,
    			cache: false,
    			contentType: false,
    			processData: false,
    			success:function(respuesta){
    				$("#edit_form").html(respuesta);
    			}
    
    		})
            
        });
        
     
    } );
    
</script>

<script type="text/javascript">
    $(document).ready(function() {
    // Setup - add a text input to each footer cell
        $('#tabla tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
        } );
     
    } );
</script>

<style type="text/css">
    tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
        color :black;
    }
</style>
</script>


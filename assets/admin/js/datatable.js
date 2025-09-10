$(document).ready(function() {
    $('.datatable').DataTable( {
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
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: ':visible'
                }
            },
            'colvis'
        ],
        //"stateSave": true,
        //"scrollY":        '50vh',
       // "scrollCollapse": true,
        "paging":         true,
    	responsive: {
        	details: true
    	},
    	"sort": false,
    	"columnDefs": [ {
            "orderable": false
        } ],
        "language": {
            "lengthMenu":"Mostrar _MENU_ registros por página.",
            "search" : "Buscar",
            "buttons": {
                "colvis": "Columnas visibles"
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
        }
    } );

    function llamarDatatable(){
        $('#table').DataTable( {
        responsive: {
            details: true
        },
        "language": {
            "lengthMenu":"Mostrar _MENU_ registros por página.",
            "search" : "Buscar",
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
        }
    } );    
    }
} );

/**
new $.fn.dataTable.Responsive( table, {
    details: false
} ); **/
    
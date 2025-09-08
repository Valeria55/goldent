
	$('#crudModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var c = button.data('c'); 
		if(id>0){
			var url = "?c="+c+"&a=obtener&id="+id;
		}else{
			var url = "?c="+c+"&a=obtener";
		}
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#edit_form").html(respuesta);
				$('.selectpicker').selectpicker();
			}

		})
	})


	$('#detallesModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=venta&a=detalles&id="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#modal-detalles").html(respuesta);
				
			}

		})
	})
	$('#detallesGastos').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=gastos_fijos&a=Detalles&id="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#modal-gastos").html(respuesta);
				
			}

		})
	})

	$('#presupuestoModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=presupuesto&a=detalles&id_presupuesto="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#modal-detalles").html(respuesta);
				
			}

		})
	})
	
		$('#devolucionVentaModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=devolucion_ventas&a=detalles&id="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#devolucion-detalles").html(respuesta);
				
			}

		})
	})
	
	$('#rangoModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=deuda&a=RangoForm&id="+id;
		
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#modal-rango").html(respuesta);
				$('.selectpicker').selectpicker();
			}

		})
	})
	
	
	$('#devolucionModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=devolucion&a=detalles&id="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#modal-detalles").html(respuesta);
				$('.selectpicker').selectpicker();
			}

		})
	})


	$('#detallesCompraModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var presupuesto = button.data('presupuesto');
		console.log(id);
		if(presupuesto){
			var url = "?c=presupuesto_compra&a=detalles&id="+id;
		}else{
			var url = "?c=compra&a=detalles&id="+id;
		}

		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#modal-detallesCompra").html(respuesta);
			}

		})
	})

	$('#cobrosModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var id_cliente = button.data('cliente');
		console.log(id_cliente);
		var url = "?c=ingreso&a=detalles&deuda="+id+"&id_cliente="+id_cliente;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#ingreso-detalles").html(respuesta);
				
			}

		})
	})


	$('#pagosModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=egreso&a=detalles&acreedor="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#egreso-detalles").html(respuesta);
			}

		})
	})


	function load(c){
		var url = "?c="+c+"&a=listar";
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#content").html(respuesta);
				$('#tabla').DataTable( {
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
                    });
			}

		});
	}

    


	//función que presiona el botón de la fila al hacer doble click

	$(".click").dblclick(function(){
		$(this).find("a").eq(0).trigger("click");
	})

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

	$('#finalizarModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var c = button.data('c'); 
		var url = "?c=compra&a=Finalizar&id="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#finalizar_form").html(respuesta);
				$('.selectpicker').selectpicker();
			}

		})
	})

	$('#devolucionCompraModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=devolucion_compras&a=detalles&id="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#devolucion-detalles").html(respuesta);
				$('.selectpicker').selectpicker();
			}

		})
	})

	//controlador rango p clientes deudores
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


	$('#large-modal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var c = button.data('c');
		var a = button.data('a');
		if (typeof (a) == "undefined") {
			a = "obtener";
		}
		if (id > 0) {
			var url = "?c=" + c + "&a=" + a + "&id=" + id;
		} else {
			var url = "?c=" + c + "&a=" + a;
		}
		$.ajax({
	
			url: url,
			method: "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success: function (respuesta) {
				$("#lg-form").html(respuesta);
				$('.selectpicker').selectpicker();
			}
	
		})
	})

	$('#editarModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=venta&a=EditarVenta&id="+id;
		$.ajax({

			url: url,
			method : "POST",
			data: id,
			cache: false,
			contentType: false,
			processData: false,
			success:function(respuesta){
				$("#editarModal").html(respuesta);
				$("#id").html(respuesta);
				$('.selectpicker').selectpicker();
			}

		})
	})
	$('#detallesModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var tabla = button.data('tabla');
		var url = "?c=venta&a=detalles&id="+id+"&tabla="+tabla;
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
				$("#ajuste-detalles").html(respuesta);
				$('.selectpicker').selectpicker();
			}

		})
	})

	$('#detallesCompraModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var id = button.data('id');
		var url = "?c=compra&a=detalles&id="+id;
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
		var url = "?c=ingreso&a=detalles&deuda="+id;
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

$('#transferencia_productoModal').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget); // Button that triggered the modal
	var id = button.data('id');
	var rec = button.data('rec');
	var url = "?c=transferencia_producto&a=detalles&recibido=" + rec + "&id_transferencia_producto=" + id;
	$.ajax({

		url: url,
		method: "POST",
		data: id,
		cache: false,
		contentType: false,
		processData: false,
		success: function (respuesta) {
			$("#modal-detalles").html(respuesta);

		}

	})
});
$('#transf_cajaModal').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget); // Button that triggered the modal
	// var id = button.data('id');
	var caja = button.data('id_caja');
	var url = "?c=caja&a=transferenciaModal&id_caja=" + caja;
	$.ajax({

		url: url,
		method: "POST",
		cache: false,
		contentType: false,
		processData: false,
		success: function (respuesta) {
			$("#transferencia_form").html(respuesta);

		}

	})
})

    


	//función que presiona el botón de la fila al hacer doble click

	$(".click").dblclick(function(){
		$(this).find("a").eq(0).trigger("click");
	})
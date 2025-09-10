// ------------step-wizard-------------
$(document).ready(function () {
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        nextTab($active);

    });
    $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

    });

    $('.pago').on('change', function() {
        var valor = $(this).val();
    
        if (valor == "Efectivo") {
            $(".caja_descontar").show();
        } else {
            $(".caja_descontar").hide();
        }
    });
    
    $('#id_cliente').on('change', function() {
        var id_cliente = $(this).val();
        $.ajax({
            url: '?c=compra&a=MandarId',
            type: 'POST',
            data: {id_cliente: id_cliente},
            dataType: 'html',
            success: function(response) {
                $('#pagos_compras').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
            }
        });
    });

    $('#pagos').on('change', function() {
		var valor = $(this).val();
		if (valor == "Transferencia" || valor == "Giro") {
			$("#banco").show();
		} else {
			$("#banco").hide();
		}
	});
    
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}


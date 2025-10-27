<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Dinero</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
    <style>
        .recibo-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border: 2px solid #000;
        }
        .header-recibo {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .empresa-nombre {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .recibo-titulo {
            font-size: 18px;
            font-weight: bold;
            background: #000;
            color: white;
            padding: 10px;
            margin: 20px 0;
        }
        .datos-tabla {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .datos-tabla td {
            padding: 8px;
            border: 1px solid #000;
        }
        .datos-tabla .label {
            background: #f0f0f0;
            font-weight: bold;
            width: 30%;
        }
        .detalle-tabla {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .detalle-tabla th,
        .detalle-tabla td {
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
        }
        .detalle-tabla th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .total-seccion {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 15px;
        }
        .total-tabla {
            width: 100%;
            border-collapse: collapse;
        }
        .total-tabla td {
            padding: 5px;
            border: 1px solid #000;
        }
        .total-tabla .label {
            background: #f0f0f0;
            font-weight: bold;
            width: 70%;
        }
        .total-final {
            font-size: 18px;
            font-weight: bold;
            background: #000;
            color: white;
        }
        .firma-seccion {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .firma-caja {
            width: 45%;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
            .recibo-container {
                margin: 0;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="recibo-container">
        <!-- Header -->
        <div class="header-recibo">
            <div class="empresa-nombre">GOLDENT S.A.</div>
            <div>RUC: 80.068.261-8</div>
            <div>Casa Central: MCAL. CALLE CAMBO</div>
            <div>Timbó - PYG - Asunción</div>
        </div>

        <!-- Título -->
        <div class="recibo-titulo text-center">
            RECIBO DE DINERO
        </div>

        <!-- Datos del recibo -->
        <table class="datos-tabla">
            <tr>
                <td class="label">Fecha:</td>
                <td id="fecha-recibo"></td>
                <td class="label">Recibo Nº:</td>
                <td id="numero-recibo"></td>
            </tr>
            <tr>
                <td class="label">Cliente:</td>
                <td colspan="3" id="cliente-nombre"></td>
            </tr>
            <tr>
                <td class="label">RUC/CI:</td>
                <td id="cliente-documento"></td>
                <td class="label">Usuario:</td>
                <td id="usuario-nombre"></td>
            </tr>
        </table>

        <!-- Concepto -->
        <div style="margin: 20px 0;">
            <strong>En concepto de pago de las siguientes deudas:</strong>
        </div>

        <!-- Detalle de facturas -->
        <table class="detalle-tabla">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>COMPROBANTE</th>
                    <th>CONCEPTO</th>
                    <th>IMPORTE</th>
                </tr>
            </thead>
            <tbody id="detalle-facturas">
            </tbody>
        </table>

        <!-- Total -->
        <div class="total-seccion">
            <table class="total-tabla">
                <tr>
                    <td class="label">TOTAL Gs.</td>
                    <td id="total-guaranies"></td>
                </tr>
            </table>
        </div>

        <!-- Métodos de pago -->
        <div style="margin: 20px 0;">
            <strong>Forma de pago:</strong>
            <div id="metodos-pago-detalle"></div>
        </div>

        <!-- Firmas -->
        <div class="firma-seccion">
            <div class="firma-caja">
                <div>FIRMA Y SELLO</div>
            </div>
            <div class="firma-caja">
                <div>RECIBÍ CONFORME</div>
            </div>
        </div>

        <!-- Botones -->
        <div class="no-print text-center" style="margin-top: 30px;">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fa fa-print"></i> Imprimir
            </button>
            <button type="button" class="btn btn-secondary" onclick="window.close()">
                <i class="fa fa-times"></i> Cerrar
            </button>
        </div>
    </div>

    <script src="<?= BASE_URL ?>assets/js/jquery.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/bootstrap.min.js"></script>
    <script>
        function cargarRecibo(grupoPagoId) {
            $.post('<?= BASE_URL ?>controller/deuda.controller.php', {
                accion: 'obtenerDetalleRecibo',
                grupo_pago_id: grupoPagoId
            }, function(response) {
                if (response.success) {
                    const detalle = response.detalle;
                    mostrarRecibo(detalle);
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }

        function mostrarRecibo(detalle) {
            if (!detalle.detalle_deudas || detalle.detalle_deudas.length === 0) {
                alert('No se encontraron detalles para este recibo');
                return;
            }

            const primera_deuda = detalle.detalle_deudas[0];
            
            // Llenar datos del encabezado
            $('#fecha-recibo').text(formatearFecha(primera_deuda.fecha));
            $('#numero-recibo').text(detalle.grupo_pago_id);
            $('#cliente-nombre').text(primera_deuda.cliente_nombre);
            $('#cliente-documento').text(primera_deuda.cliente_documento || 'N/A');
            $('#usuario-nombre').text(primera_deuda.usuario_nombre);

            // Llenar detalle de facturas
            let detalleHTML = '';
            let totalGeneral = 0;

            detalle.detalle_deudas.forEach(function(deuda) {
                detalleHTML += `
                    <tr>
                        <td>${formatearFecha(deuda.deuda_fecha)}</td>
                        <td>${deuda.nro_comprobante || 'N/A'}</td>
                        <td>${deuda.deuda_concepto}</td>
                        <td class="text-right">${formatearNumero(deuda.monto_aplicado)}</td>
                    </tr>
                `;
                totalGeneral += parseFloat(deuda.monto_aplicado);
            });

            $('#detalle-facturas').html(detalleHTML);
            $('#total-guaranies').text(formatearNumero(totalGeneral));

            // Mostrar métodos de pago
            let metodosHTML = '';
            if (detalle.metodos_pago && detalle.metodos_pago.length > 0) {
                detalle.metodos_pago.forEach(function(metodo) {
                    metodosHTML += `
                        <div>${metodo.forma_pago} (${metodo.moneda}): ${formatearNumero(metodo.total_metodo)}</div>
                    `;
                });
            }
            $('#metodos-pago-detalle').html(metodosHTML);
        }

        function formatearFecha(fecha) {
            if (!fecha) return '';
            const date = new Date(fecha);
            return date.toLocaleDateString('es-PY');
        }

        function formatearNumero(numero) {
            if (!numero) return '0';
            return parseFloat(numero).toLocaleString('es-PY', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Cargar recibo al cargar la página si se pasa el parámetro
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const grupoPagoId = urlParams.get('grupo_pago_id');
            
            if (grupoPagoId) {
                cargarRecibo(grupoPagoId);
            }
        });
    </script>
</body>
</html>
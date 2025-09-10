<div class="container">
    <div class="row" style="align-items: center; margin-bottom: 10px;">
        <div class="col-md-6 col-12" style="display: flex; align-items: center; margin-bottom: 10px;">
            <h4 class="page-header" style="margin-bottom:0;"><b>Movimientos de la caja</b></h4>
        </div>
        <div class="col-md-6 col-12">
            <form method="get" class="form-inline responsive-form" style="text-align:right; justify-content: flex-end; display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="hidden" name="id_caja" value="<?php echo $_GET['id_caja']; ?>">
                <input type="hidden" name="a" value="movimientos">
                <input type="hidden" name="c" value="caja">
                <div class="form-group" style="margin-bottom: 5px;">
                    <label style="margin-right:5px;">Desde</label>
                    <input type="date" name="desde" class="form-control" style="width: auto;" value="<?php echo isset($_GET['desde']) ? $_GET['desde'] : ''; ?>">
                </div>
                <div class="form-group" style="margin-bottom: 5px;">
                    <label style="margin-right:5px;">Hasta</label>
                    <input type="date" name="hasta" class="form-control" style="width: auto;" value="<?php echo isset($_GET['hasta']) ? $_GET['hasta'] : ''; ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">Filtrar</button>
            </form>
        </div>
    </div>
    <style>
        .card-resumen-separada {
            background: #fafbfc;
            color: #222;
            border-radius: 18px 8px 8px 18px;
            padding: 18px 32px;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            font-size: 1.15rem;
            min-width: 220px;
            max-width: 340px;
            text-align: left;
            border: none;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            margin-left: auto;
            margin-right: auto;
        }

        .card-ingresos {
            border-left: 7px solid #23b04a;
        }

        .card-egresos {
            border-left: 7px solid #e74c3c;
        }

        .card-diferencia {
            border-left: 7px solid #007bff;
        }

        .card-resumen-separada .titulo {
            font-size: 1.6rem;
            /* más pequeño que antes */
            font-weight: 700;
            margin-bottom: 0;
            color: #222;
            margin-right: 16px;
        }

        .card-resumen-separada .valor {
            font-size: 1.6rem;
            /* más pequeño que antes */
            font-weight: 700;
            margin-left: 8px;
            white-space: nowrap;
            /* evita salto de línea */
        }

        .card-ingresos .valor {
            color: #23b04a;
        }

        .card-egresos .valor {
            color: #e74c3c;
        }

        .card-diferencia .valor {
            color: #007bff;
        }

        @media (max-width: 991px) {
            .card-resumen-separada {
                min-width: 160px;
                max-width: 100%;
                padding: 14px 10px;
                font-size: 1.08rem;
            }

            .card-resumen-separada .titulo,
            .card-resumen-separada .valor {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 768px) {
            .card-resumen-separada {
                flex-direction: column !important;
                text-align: center !important;
                padding: 12px 8px !important;
                min-width: 120px;
                gap: 4px !important;
            }

            .card-resumen-separada .titulo {
                font-size: 0.9rem !important;
                margin-right: 0 !important;
            }

            .card-resumen-separada .valor {
                font-size: 1rem !important;
                margin-left: 0 !important;
            }
        }

        @media (max-width: 576px) {
            .row[style*="justify-content: space-between"] {
                flex-direction: column !important;
                align-items: center !important;
            }
            
            .row[style*="justify-content: space-between"] > div {
                margin-bottom: 10px !important;
                width: 100% !important;
                max-width: 280px !important;
            }
            
            .card-resumen-separada {
                width: 100% !important;
                max-width: none !important;
                min-width: auto !important;
            }
        }

        /* Estilos responsive para el formulario */
        @media (max-width: 768px) {
            .responsive-form {
                justify-content: center !important;
                text-align: center !important;
            }
            
            .responsive-form .form-group {
                margin-right: 5px !important;
                margin-bottom: 8px !important;
            }
            
            .responsive-form .form-control {
                width: 120px !important;
                font-size: 0.9rem !important;
            }
            
            .responsive-form .btn {
                font-size: 0.9rem !important;
                padding: 6px 12px !important;
            }
        }

        @media (max-width: 576px) {
            .responsive-form {
                flex-direction: column !important;
                align-items: center !important;
                gap: 8px !important;
            }
            
            .responsive-form .form-group {
                margin-right: 0 !important;
                display: flex !important;
                align-items: center !important;
                gap: 8px !important;
            }
            
            .responsive-form .form-control {
                width: 140px !important;
            }
            
            h4.page-header {
                font-size: 1.3rem !important;
                text-align: center !important;
            }
        }

        /* Estilos para que todas las columnas se vean correctamente */
        .datatable {
            table-layout: fixed !important;
            width: 100% !important;
        }
        
        .datatable th:nth-child(1), .datatable td:nth-child(1) {
            width: 5% !important;
            text-align: center;
        }
        
        .datatable th:nth-child(2), .datatable td:nth-child(2) {
            width: 12% !important;
        }
        
        .datatable th:nth-child(3), .datatable td:nth-child(3) {
            width: 15% !important;
            word-wrap: break-word;
        }
        
        .datatable th:nth-child(4), .datatable td:nth-child(4) {
            width: 20% !important;
            word-wrap: break-word;
        }
        
        .datatable th:nth-child(5), .datatable td:nth-child(5) {
            width: 16% !important;
            text-align: right;
        }
        
        .datatable th:nth-child(6), .datatable td:nth-child(6) {
            width: 16% !important;
            text-align: right;
        }
        
        .datatable th:nth-child(7), .datatable td:nth-child(7) {
            width: 16% !important;
            text-align: right;
        }

        /* Estilos responsive para la tabla en móviles */
        @media (max-width: 768px) {
            .datatable {
                font-size: 0.75rem !important;
                table-layout: auto !important;
            }
            
            .datatable th, .datatable td {
                padding: 4px 2px !important;
                word-wrap: break-word !important;
                white-space: normal !important;
            }
            
            .datatable th:nth-child(1), .datatable td:nth-child(1) {
                width: 8% !important;
                font-size: 0.7rem;
            }
            
            .datatable th:nth-child(2), .datatable td:nth-child(2) {
                width: 18% !important;
                font-size: 0.7rem;
            }
            
            .datatable th:nth-child(3), .datatable td:nth-child(3) {
                width: 20% !important;
                font-size: 0.7rem;
            }
            
            .datatable th:nth-child(4), .datatable td:nth-child(4) {
                width: 18% !important;
                font-size: 0.7rem;
            }
            
            .datatable th:nth-child(5), .datatable td:nth-child(5) {
                width: 12% !important;
                font-size: 0.7rem;
            }
            
            .datatable th:nth-child(6), .datatable td:nth-child(6) {
                width: 12% !important;
                font-size: 0.7rem;
            }
            
            .datatable th:nth-child(7), .datatable td:nth-child(7) {
                width: 12% !important;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .datatable {
                font-size: 0.65rem !important;
            }
            
            .datatable th, .datatable td {
                padding: 3px 1px !important;
            }
            
            /* Ocultar columnas menos importantes en móviles muy pequeños */
            .datatable th:nth-child(1), .datatable td:nth-child(1) {
                display: none !important;
            }
            
            .datatable th:nth-child(4), .datatable td:nth-child(4) {
                width: 25% !important;
            }
            
            .datatable th:nth-child(5), .datatable td:nth-child(5) {
                width: 15% !important;
            }
            
            .datatable th:nth-child(6), .datatable td:nth-child(6) {
                width: 15% !important;
            }
            
            .datatable th:nth-child(7), .datatable td:nth-child(7) {
                width: 15% !important;
            }
        }

        /* Estilos responsive para el detalle por monedas */
        @media (max-width: 768px) {
            .detalle-monedas .col-md-4 {
                margin-bottom: 15px;
            }
            
            .detalle-monedas .card-moneda {
                padding: 12px !important;
                font-size: 0.9rem !important;
            }
            
            .detalle-monedas h6 {
                font-size: 1rem !important;
                margin-bottom: 8px !important;
            }
            
            .detalle-monedas .contenido-moneda {
                font-size: 0.85rem !important;
                line-height: 1.3 !important;
            }
        }

        @media (max-width: 576px) {
            .detalle-monedas .card-moneda {
                padding: 10px !important;
                margin-bottom: 8px !important;
            }
            
            .detalle-monedas h6 {
                font-size: 0.9rem !important;
                margin-bottom: 6px !important;
            }
            
            .detalle-monedas .contenido-moneda {
                font-size: 0.8rem !important;
                line-height: 1.2 !important;
            }
            
            .detalle-monedas .contenido-moneda div {
                margin-bottom: 2px;
            }
        }
    </style>
    <?php
    $monto_inicial = $this->model->Obtener($_GET['id_caja']);
    $sumai = 0;
    $sumaegr = 0;

    $saldo_anterior = 0;
    $fecha_desde = isset($_GET['desde']) && $_GET['desde'] ? $_GET['desde'] : null;
    $fecha_hasta = isset($_GET['hasta']) && $_GET['hasta'] ? $_GET['hasta'] : null;

    // Siempre calcular el saldo anterior hasta el día anterior a la fecha desde si hay filtro
    if ($fecha_desde) {
        $desde_saldo_anterior = null;
        $hasta_saldo_anterior = date('Y-m-d', strtotime($fecha_desde . ' -1 day'));
    } else {
        // Si no hay filtro, saldo anterior es hasta el último día del mes anterior
        $primer_dia_mes = date('Y-m-01');
        $hasta_saldo_anterior = date('Y-m-d', strtotime($primer_dia_mes . ' -1 day'));
        $desde_saldo_anterior = date('2025-05-23');
    }

    $movs_anteriores = $this->model->ListarMovimientosCajaNew($_GET['id_caja'], $desde_saldo_anterior, $hasta_saldo_anterior);
    $ingresos_ant = 0;
    $egresos_ant = 0;
    foreach ($movs_anteriores as $r) {
        if ($r->anulado) continue; // No sumar anulados
        if ($r->monto > 0) {
            $ingresos_ant += $r->monto;
        } else {
            $egresos_ant += $r->monto;
        }
    }
    $saldo_anterior = $monto_inicial->monto + $ingresos_ant + $egresos_ant;

    // Ahora calcula los movimientos del rango filtrado normalmente
    $sumai = 0;
    $sumaegr = 0;
    foreach ($this->model->ListarMovimientosCajaNew($_GET['id_caja'], null, null) as $r) {
        if ($r->anulado) continue; // No sumar anulados
        if ($r->monto > 0) {
            $sumai += $r->monto;
        } else {
            $sumaegr += $r->monto;
        }
    }
    ?>
    <div class="row" style="margin-bottom: 18px; display: flex; justify-content: space-between;">
        <div class="col-md-4" style="display: flex; justify-content: center;">
            <div class="card-resumen-separada card-ingresos">
                <span class="titulo">Saldo anterior:</span>
                <span class="valor"><?php echo number_format($saldo_anterior, 0, ",", "."); ?> <span style="font-weight:400;">Gs</span></span>
            </div>
        </div>
        <div class="col-md-4" style="display: flex; justify-content: center;">
            <div class="card-resumen-separada card-egresos">
                <span class="titulo">Movimiento del día:</span>
                <span class="valor"><?php echo number_format($sumai + $sumaegr, 0, ",", "."); ?><span style="font-weight:400;">Gs</span></span>
            </div>
        </div>
        <div class="col-md-4" style="display: flex; justify-content: center;">
            <div class="card-resumen-separada card-diferencia">
                <span class="titulo">Saldo actual:</span>
                <span class="valor">
                    <?php
                    // SIEMPRE mostrar el saldo total real de la caja (saldo anterior + movimientos del rango)
                    echo number_format($monto_inicial->monto + $sumai + $sumaegr, 0, ",", ".") . ' <span style="font-weight:400;">Gs</span>';
                    ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Nuevo apartado para mostrar montos por moneda -->
  
    
    <table class="table table-striped display responsive nowrap datatable" width="100%">
        <thead>
            <tr style="background-color: #ccc; color:#000">
                <th style="width: 5%;">N°</th>
                <th style="width: 12%;">Fecha</th>
                <th style="width: 15%;">Categoría</th>
                <th style="width: 20%;">Persona</th>
                <th style="width: 16%;">Ingreso</th>
                <th style="width: 16%;">Egreso</th>
                <th style="width: 16%;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fila de saldo inicial -->
            <tr class="click" style="font-weight:bold; background:#f5f5f5;">
                <td style="width: 5%;"></td>
                <td style="width: 12%;"><?php echo date("d/m/Y", strtotime($monto_inicial->fecha)); ?></td>
                <td style="width: 15%;">Saldo Inicial</td>
                <td style="width: 20%;"></td>
                <td style="width: 16%;"></td>
                <td style="width: 16%;"></td>
                <td style="width: 16%;"><?php echo number_format($monto_inicial->monto, 0, ".", ","); ?></td>
            </tr>
            <!-- Fila de saldo anterior -->
            <tr class="click" style="font-weight:bold; background:#e9ecef;">
                <td style="width: 5%;"></td>
                <td style="width: 12%;"><?php echo date("d/m/Y", strtotime($hasta_saldo_anterior)); ?></td>
                <td style="width: 15%;">Saldo anterior</td>
                <td style="width: 20%;"></td>
                <td style="width: 16%;"></td>
                <td style="width: 16%;"></td>
                <td style="width: 16%;"><?php echo number_format($saldo_anterior, 0, ".", ","); ?></td>
            </tr>
            <?php
            $sumaEfectivo = 0;
            $sumaCheque = 0;
            $sumaTarjeta = 0;
            $sumaTransferencia = 0;
            $sumaGiro = 0;
            $sumaegre = 0;
            $sumai = 0;
            $c = 1;
            $saldo_corriente = $saldo_anterior;
            $desde = isset($_GET['desde']) && $_GET['desde'] ? $_GET['desde'] : date('Y-m-01');
            $hasta = isset($_GET['hasta']) && $_GET['hasta'] ? $_GET['hasta'] : date('Y-m-d');

            foreach ($this->model->ListarMovimientosCajaNew($_GET['id_caja'], $desde, $hasta) as $r):
                // Mostrar todos, pero solo sumar si NO está anulado
                if (strlen($r->categoria) >= 15) {
                    $categoria = substr($r->categoria, 0, 15) . "...";
                } else {
                    $categoria = $r->categoria;
                }
                if (strlen($r->concepto) >= 15) {
                    $concepto = substr($r->concepto, 0, 15) . "...";
                } else {
                    $concepto = $r->concepto;
                }
                if (strlen($r->nombre) >= 15) {
                    $nombre = substr($r->nombre, 0, 15) . "...";
                } else {
                    $nombre = $r->nombre;
                }
                // Calcular ingreso/egreso para mostrar siempre
                if ($r->monto > 0) {
                    $ingreso = number_format($r->monto, 0, ".", ",");
                    $egreso = "";
                } else {
                    $ingreso = "";
                    $egreso = number_format(($r->monto * -1), 0, ".", ",");
                }
                if (!$r->anulado) {
                    // Solo sumar/restar si NO está anulado
                    if ($r->monto > 0) {
                        $sumai += $r->monto;
                    } else {
                        $sumaegr += $r->monto;
                    }
                    $saldo_corriente += $r->monto;
                    $saldo_mostrar = number_format($saldo_corriente, 0, ".", ",");
                } else {
                    // Si está anulado, no sumar ni restar, mostrar el saldo actual (sin modificar)
                    $saldo_mostrar = number_format($saldo_corriente, 0, ".", ",");
                }
                $cierre = $r->categoria == 'Transferencia' ? $this->cierre->Obtener($r->comprobante) : '';
            ?>
                <tr class="click" <?php if ($r->anulado) {
                                        echo "style='color:red'";
                                    } elseif ($r->descuento > 0) {
                                        echo "style='color:#F39C12'";
                                    } ?>>
                    <td style="width: 5%;"><?php echo $c++; ?></td>
                    <td style="width: 12%;"><?php echo date("d/m/Y H:i", strtotime($r->fecha)); ?></td>
                    <td style="width: 15%;" title="<?php echo $r->categoria; ?>"><?php echo $r->categoria == 'Transferencia' ? $concepto : $categoria; ?></td>
                    <td style="width: 20%;" title="<?php echo $r->nombre; ?>"><?php echo $r->categoria == 'Transferencia' ? $cierre->nombre_usuario.' ('.$cierre->usuario.')' : $nombre ; ?></td>
                    <td style="width: 16%;"><?php echo $ingreso . (isset($r->moneda) && $r->moneda != 'GS' && $r->moneda != '' && $r->moneda != null ? ' ('.number_format($r->monto_moneda, 0, ".", ",").' '.$r->moneda.')' : ''); ?></td>
                    <td style="width: 16%;"><?php echo $egreso . (isset($r->moneda) && $r->moneda != 'GS' && $r->moneda != '' && $r->moneda != null && $egreso != '' ? ' ('.number_format($r->monto_moneda, 0, ".", ",").' '.$r->moneda.')' : ''); ?></td>
                    <td style="width: 16%;"><?php echo $saldo_mostrar; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #ccc; color:#000">
                <th></th>
                <th></th>
                <th></th>
                <th>Saldo:</th>
                <th></th>
                <th></th>
                <th class="monto" id="monto_total">
                    <?php
                    // El saldo final de la tabla es el saldo real de la caja
                    echo number_format($saldo_corriente, 0, ",", ".");
                    ?>
                </th>
            </tr>
        </tfoot>
    </table>
      <div class="row detalle-monedas" style="margin-bottom: 20px;">
        <div class="col-md-12">
            <div style="background: #f8f9fa; border-radius: 10px; padding: 20px; border: 1px solid #e9ecef;">
                <h5 style="margin-bottom: 15px; color: #495057; font-weight: 600;">
                    <i class="fa fa-money" style="margin-right: 8px;"></i>Detalle por Monedas
                </h5>
                <div class="row">
                    <?php
                    // Obtener datos originales de la caja usando la nueva función del modelo
                    $caja_original = $this->model->ObtenerMontosOriginales($_GET['id_caja']);
                    
                    // Calcular movimientos anteriores por moneda
                    $movs_ant_gs = 0; $movs_ant_usd = 0; $movs_ant_rs = 0;
                    foreach ($movs_anteriores as $mov) {
                        if ($mov->anulado) continue;
                        if (isset($mov->moneda)) {
                            if ($mov->moneda == 'USD') {
                                $movs_ant_usd += ($mov->monto_moneda > 0 ? $mov->monto_moneda : $mov->monto_moneda);
                            } elseif ($mov->moneda == 'RS') {
                                $movs_ant_rs += ($mov->monto_moneda > 0 ? $mov->monto_moneda : $mov->monto_moneda);
                            } else {
                                // Para GS usamos el monto convertido ya que viene en guaraníes
                                $movs_ant_gs += ($mov->monto > 0 ? $mov->monto : $mov->monto);
                            }
                        } else {
                            // Sin moneda específica, asumimos que es GS
                            $movs_ant_gs += ($mov->monto > 0 ? $mov->monto : $mov->monto);
                        }
                    }
                    
                    // Calcular movimientos del período por moneda
                    $movs_periodo_gs = 0; $movs_periodo_usd = 0; $movs_periodo_rs = 0;
                    foreach ($this->model->ListarMovimientosCajaNew($_GET['id_caja'], $desde, $hasta) as $mov) {
                        if ($mov->anulado) continue;
                        if (isset($mov->moneda)) {
                            if ($mov->moneda == 'USD') {
                                $movs_periodo_usd += ($mov->monto_moneda > 0 ? $mov->monto_moneda : $mov->monto_moneda);
                            } elseif ($mov->moneda == 'RS') {
                                $movs_periodo_rs += ($mov->monto_moneda > 0 ? $mov->monto_moneda : $mov->monto_moneda);
                            } else {
                                // Para GS usamos el monto convertido ya que viene en guaraníes
                                $movs_periodo_gs += ($mov->monto > 0 ? $mov->monto : $mov->monto);
                            }
                        } else {
                            // Sin moneda específica, asumimos que es GS
                            $movs_periodo_gs += ($mov->monto > 0 ? $mov->monto : $mov->monto);
                        }
                    }
                    ?>
                    
                    <!-- Guaraníes -->
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="card-moneda" style="background: white; border-radius: 8px; padding: 15px; border-left: 4px solid #28a745; margin-bottom: 10px;">
                            <h6 style="margin: 0 0 10px 0; color: #28a745; font-weight: 600;">
                                <i class="fa fa-money" style="margin-right: 5px;"></i>Guaraníes (GS)
                            </h6>
                            <div class="contenido-moneda" style="font-size: 1rem; line-height: 1.4;">
                                <div><strong>Inicial:</strong> <?php echo number_format($caja_original->monto ?? 0, 0, ",", "."); ?> Gs</div>
                                <div><strong>Movimientos Ant.:</strong> <?php echo number_format($movs_ant_gs, 0, ",", "."); ?> Gs</div>
                                <div><strong>Saldo Anterior:</strong> <?php echo number_format(($caja_original->monto ?? 0) + $movs_ant_gs, 0, ",", "."); ?> Gs</div>
                                <div><strong>Movimientos Período:</strong> <?php echo number_format($movs_periodo_gs, 0, ",", "."); ?> Gs</div>
                                <div style="border-top: 1px solid #dee2e6; margin-top: 8px; padding-top: 8px;">
                                    <strong>Saldo Actual:</strong> <?php echo number_format(($caja_original->monto ?? 0) + $movs_ant_gs + $movs_periodo_gs, 0, ",", "."); ?> Gs
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dólares -->
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="card-moneda" style="background: white; border-radius: 8px; padding: 15px; border-left: 4px solid #007bff; margin-bottom: 10px;">
                            <h6 style="margin: 0 0 10px 0; color: #007bff; font-weight: 600;">
                                <i class="fa fa-dollar" style="margin-right: 5px;"></i>Dólares (USD)
                            </h6>
                            <div class="contenido-moneda" style="font-size: 1rem; line-height: 1.4;">
                                <div><strong>Inicial:</strong> <?php echo number_format($caja_original->usd_monto ?? 0, 0, ",", "."); ?> USD</div>
                                <div><strong>Movimientos Ant.:</strong> <?php echo number_format($movs_ant_usd, 0, ",", "."); ?> USD</div>
                                <div><strong>Saldo Anterior:</strong> <?php echo number_format(($caja_original->usd_monto ?? 0) + $movs_ant_usd, 0, ",", "."); ?> USD</div>
                                <div><strong>Movimientos Período:</strong> <?php echo number_format($movs_periodo_usd, 0, ",", "."); ?> USD</div>
                                <div style="border-top: 1px solid #dee2e6; margin-top: 8px; padding-top: 8px;">
                                    <strong>Saldo Actual:</strong> <?php echo number_format(($caja_original->usd_monto ?? 0) + $movs_ant_usd + $movs_periodo_usd, 0, ",", "."); ?> USD
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reales -->
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="card-moneda" style="background: white; border-radius: 8px; padding: 15px; border-left: 4px solid #ffc107; margin-bottom: 10px;">
                            <h6 style="margin: 0 0 10px 0; color: #e68900; font-weight: 600;">
                                <i class="fa fa-money" style="margin-right: 5px;"></i>Reales (RS)
                            </h6>
                            <div class="contenido-moneda" style="font-size: 1rem; line-height: 1.4;">
                                <div><strong>Inicial:</strong> <?php echo number_format($caja_original->rs_monto ?? 0, 0, ",", "."); ?> RS</div>
                                <div><strong>Movimientos Ant.:</strong> <?php echo number_format($movs_ant_rs, 0, ",", "."); ?> RS</div>
                                <div><strong>Saldo Anterior:</strong> <?php echo number_format(($caja_original->rs_monto ?? 0) + $movs_ant_rs, 0, ",", "."); ?> RS</div>
                                <div><strong>Movimientos Período:</strong> <?php echo number_format($movs_periodo_rs, 0, ",", "."); ?> RS</div>
                                <div style="border-top: 1px solid #dee2e6; margin-top: 8px; padding-top: 8px;">
                                    <strong>Saldo Actual:</strong> <?php echo number_format(($caja_original->rs_monto ?? 0) + $movs_ant_rs + $movs_periodo_rs, 0, ",", "."); ?> RS
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
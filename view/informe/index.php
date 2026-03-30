<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h6 class="page-header">
                <i class="fas fa-file-invoice-dollar"></i> Centro de Informes
            </h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default" style="border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: none;">
               
                <div class="panel-body" style="padding: 30px;">
                    <form action="?c=informe&a=Generar" method="post" target="_blank">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="desde"><i class="fas fa-calendar-alt"></i> Fecha Desde</label>
                                    <input type="date" name="desde" id="desde" class="form-control" value="<?php echo date('Y-m-01'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hasta"><i class="fas fa-calendar-alt"></i> Fecha Hasta</label>
                                    <input type="date" name="hasta" id="hasta" class="form-control" value="<?php echo date('Y-m-t'); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <label><i class="fas fa-list"></i> Seleccione el Tipo de Informe</label>
                                <div class="list-group" id="report-types" style="max-height: 380px; overflow-y: auto; padding-right: 10px;">
                                    <label class="list-group-item active-item" style="cursor: pointer; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s; border: 1px solid #ddd;">
                                        <input type="radio" name="tipo" value="ingreso" checked style="display: none;">
                                        <div class="report-option">
                                            <i class="fas fa-plus-circle text-success" style="font-size: 1.5em; margin-right: 15px;"></i>
                                            <div>
                                                <h4 style="margin: 0; font-weight: bold;">Informes de Ingresos</h4>
                                                <small class="text-muted">Listado detallado de todo el dinero ingresado.</small>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="list-group-item" style="cursor: pointer; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s; border: 1px solid #ddd;">
                                        <input type="radio" name="tipo" value="egreso" style="display: none;">
                                        <div class="report-option">
                                            <i class="fas fa-minus-circle text-danger" style="font-size: 1.5em; margin-right: 15px;"></i>
                                            <div>
                                                <h4 style="margin: 0; font-weight: bold;">Informes de Egresos</h4>
                                                <small class="text-muted">Listado de gastos y egresos del periodo.</small>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="list-group-item" style="cursor: pointer; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s; border: 1px solid #ddd;">
                                        <input type="radio" name="tipo" value="deuda" style="display: none;">
                                        <div class="report-option">
                                            <i class="fas fa-user-clock text-warning" style="font-size: 1.5em; margin-right: 15px;"></i>
                                            <div>
                                                <h4 style="margin: 0; font-weight: bold;">Deudas (Deudores)</h4>
                                                <small class="text-muted">Cuentas por cobrar de clientes.</small>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="list-group-item" style="cursor: pointer; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s; border: 1px solid #ddd;">
                                        <input type="radio" name="tipo" value="acreedor" style="display: none;">
                                        <div class="report-option">
                                            <i class="fas fa-hand-holding-usd text-info" style="font-size: 1.5em; margin-right: 15px;"></i>
                                            <div>
                                                <h4 style="margin: 0; font-weight: bold;">Acreedores</h4>
                                                <small class="text-muted">Cuentas por pagar a proveedores.</small>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="list-group-item" style="cursor: pointer; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s; border: 1px solid #ddd;">
                                        <input type="radio" name="tipo" value="venta_factura" style="display: none;">
                                        <div class="report-option">
                                            <i class="fas fa-receipt text-primary" style="font-size: 1.5em; margin-right: 15px;"></i>
                                            <div>
                                                <h4 style="margin: 0; font-weight: bold;">Ventas Facturadas</h4>
                                                <small class="text-muted">Ventas procesadas con factura legal.</small>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="list-group-item" style="cursor: pointer; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s; border: 1px solid #ddd;">
                                        <input type="radio" name="tipo" value="venta_sin_factura" style="display: none;">
                                        <div class="report-option">
                                            <i class="fas fa-ticket-alt text-secondary" style="font-size: 1.5em; margin-right: 15px;"></i>
                                            <div>
                                                <h4 style="margin: 0; font-weight: bold;">Ventas Sin Factura</h4>
                                                <small class="text-muted">Ventas procesadas con ticket o sin comprobante fiscal.</small>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="list-group-item" style="cursor: pointer; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s; border: 1px solid #ddd;">
                                        <input type="radio" name="tipo" value="todos" style="display: none;">
                                        <div class="report-option">
                                            <i class="fas fa-globe text-dark" style="font-size: 1.5em; margin-right: 15px;"></i>
                                            <div>
                                                <h4 style="margin: 0; font-weight: bold;">Todos (Resumen General)</h4>
                                                <small class="text-muted">Consolidado de todos los movimientos anteriores.</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 30px;">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg" style="background-color: #348993; border: none; padding: 15px 40px; border-radius: 30px; font-weight: bold; width: 100%;">
                                    <i class="fas fa-download"></i> GENERAR PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .report-option {
        display: flex;
        align-items: center;
    }
    .list-group-item:hover {
        background-color: #f8f9fa !important;
        transform: scale(1.01);
        border-color: #348993 !important;
    }
    .active-item {
        background-color: #eaf6f8 !important;
        border: 2px solid #348993 !important;
    }
    .page-header {
        color: #333;
        margin-bottom: 30px;
        font-weight: 300;
    }
</style>

<script>
    $(document).ready(function() {
        $('#report-types label').click(function() {
            $('#report-types label').removeClass('active-item');
            $(this).addClass('active-item');
        });
    });
</script>

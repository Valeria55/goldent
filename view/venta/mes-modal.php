<style>
    .scrolleable {
        height: 200px;
        overflow-y: scroll;
    }
</style>
<div id="mesModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="edit_form">
                <form method="post">
                    <h1>Generar informe mensual</h1>
                    <input type="hidden" name="c" value="venta">
                    <input type="hidden" name="a" value="cierreMes">
                    <div class="form-group">
                        <label>Desde</label>
                        <input type="date" required min="2020-11-01" max="<?php echo date("Y-m-d") ?>" name="desde" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Hasta</label>
                        <input type="date" required min="2020-11-01" max="<?php echo date("Y-m-d") ?>" name="hasta" class="form-control">
                    </div>


                    <p>
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            Personalizar contenido del informe
                        </button>
                    </p>
                    <div class="collapse scrolleable" id="collapseExample">
                        <div class="card card-body">

                            <div class="list-group">
                                <label class="list-group-item-primary">
                                    <input class="form-check-input me-1" type="checkbox" id="select_all" name="" checked value="">
                                    Marcar/Desmarcar todo </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[vent_cont]" checked value="true">
                                    Lista de ventas al contado </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[vent_cred]" checked value="true">
                                    Lista de ventas a crédito </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[mov_prod]" checked value="true">
                                    Movimientos de cada producto
                                </label>
                                <label class="list-group-item"> 
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[ven_prod]" checked value="true">
                                    Ventas por producto
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[cli_comp]" checked value="true">
                                    Clientes con más compras
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[vent_vend]" checked value="true">
                                    Ventas por vendedores
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[cobr_deud]" checked value="true">
                                    Cobros de deudas
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[ingr]" checked value="true">
                                    Ingresos
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[egr]" checked value="true">
                                    Egresos
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[compr]" checked value="true">
                                    Compras
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[gast]" checked value="true">
                                    Gastos
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[prod]" checked value="true">
                                    Productos
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[cuent_cobr]" checked value="true">
                                    Cuentas a cobrar
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[cuent_pag]" checked value="true">
                                    Cuentas a pagar
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary">Generar</button>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#select_all').click(function() {
            $('input[type="checkbox"]').prop('checked', this.checked);
        })
    });
</script>
<style>
    .scrolleable {
        height: 200px;
        overflow-y: scroll;
    }
</style>
<form method="post">
		    	<input type="hidden" name="c" value="deuda">
		    	<input type="hidden" name="a" value="Clientepdf">
		    	<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">

                    <h3>Generar informe </h3>
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
                                    Marcar/Desmarcar todo 
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[vent_credito]" checked value="true">
                                    Extracto General
								</label>
                                <!-- <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="items_informe[pagos_client]" checked value="true">
                                    Pagos y Deudas
								</label> -->

                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary">Generar</button>
                    </div>
</form>

<script>
    $(document).ready(function() {
        $('#select_all').click(function() {
            $('input[type="checkbox"]').prop('checked', this.checked);
        })
    });
</script>
          	
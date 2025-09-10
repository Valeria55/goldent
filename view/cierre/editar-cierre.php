<!-- Modal para editar montos de apertura y cierre -->
<div class="modal fade" id="modalEditarCierre" tabindex="-1" role="dialog" aria-labelledby="modalEditarCierreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarCierreLabel">
                    <i class="fas fa-edit"></i> Editar Montos de Apertura y Cierre
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarCierre" method="POST" action="?c=cierre&a=actualizar">
                <div class="modal-body">
                    <input type="hidden" id="idCierre" name="id" value="">
                    
                    <!-- Información del cierre -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body p-2">
                                    <h6 class="text-primary mb-1">
                                        <i class="fas fa-user"></i> Usuario: <span id="usuarioNombre" class="font-weight-bold"></span>
                                    </h6>
                                    <h6 class="text-secondary mb-1">
                                        <i class="fas fa-calendar-plus"></i> Fecha Apertura: <span id="fechaApertura"></span>
                                    </h6>
                                    <h6 class="text-secondary mb-0">
                                        <i class="fas fa-calendar-minus"></i> Fecha Cierre: <span id="fechaCierre"></span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Montos en Guaraníes -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-success">
                                <i class="fas fa-money-bill-wave"></i> Guaraníes (Gs)
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto_apertura">
                                    <i class="fas fa-play-circle"></i> Monto Apertura Gs:
                                </label>
                                <input type="number" class="form-control" id="monto_apertura" name="monto_apertura" 
                                       step="1" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto_cierre">
                                    <i class="fas fa-stop-circle"></i> Monto Cierre Gs:
                                </label>
                                <input type="number" class="form-control" id="monto_cierre" name="monto_cierre" 
                                       step="1" min="0" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <!-- Montos en Reales -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-info">
                                <i class="fas fa-money-bill-wave"></i> Reales (R$)
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apertura_rs">
                                    <i class="fas fa-play-circle"></i> Apertura R$:
                                </label>
                                <input type="number" class="form-control" id="apertura_rs" name="apertura_rs" 
                                       step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto_cierre_rs">
                                    <i class="fas fa-stop-circle"></i> Cierre R$:
                                </label>
                                <input type="number" class="form-control" id="monto_cierre_rs" name="monto_cierre_rs" 
                                       step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Montos en Dólares -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-warning">
                                <i class="fas fa-dollar-sign"></i> Dólares (US$)
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apertura_usd">
                                    <i class="fas fa-play-circle"></i> Apertura US$:
                                </label>
                                <input type="number" class="form-control" id="apertura_usd" name="apertura_usd" 
                                       step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto_cierre_usd">
                                    <i class="fas fa-stop-circle"></i> Cierre US$:
                                </label>
                                <input type="number" class="form-control" id="monto_cierre_usd" name="monto_cierre_usd" 
                                       step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                            <div class="alert alert-info">
                                <h6 class="text-primary mb-1">
                                    <i class="fas fa-exchange-alt"></i> Tipos de cambio aplicados:
                                </h6>
                                <p class="mb-0 small">
                                    <span id="cotDolar" class="mr-3"></span>
                                    <span id="cotReal"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalEditarCierre(id, usuario, fechaApertura, fechaCierre, montoApertura, montoCierre, aperturaRs, cierreRs, aperturaUsd, cierreUsd, cotDolar, cotReal) {
    // Llenar los campos del modal
    document.getElementById('idCierre').value = id;
    document.getElementById('usuarioNombre').textContent = usuario;
    document.getElementById('fechaApertura').textContent = fechaApertura;
    document.getElementById('fechaCierre').textContent = fechaCierre;
    
    // Montos en Guaraníes
    document.getElementById('monto_apertura').value = parseFloat(montoApertura) || 0;
    document.getElementById('monto_cierre').value = parseFloat(montoCierre) || 0;
    
    // Montos en Reales
    document.getElementById('apertura_rs').value = parseFloat(aperturaRs) || 0;
    document.getElementById('monto_cierre_rs').value = parseFloat(cierreRs) || 0;
    
    // Montos en Dólares
    document.getElementById('apertura_usd').value = parseFloat(aperturaUsd) || 0;
    document.getElementById('monto_cierre_usd').value = parseFloat(cierreUsd) || 0;
    
    // Tipos de cambio
    document.getElementById('cotDolar').textContent = 'Dólar: ' + (parseFloat(cotDolar) || 'No definido');
    document.getElementById('cotReal').textContent = 'Real: ' + (parseFloat(cotReal) || 'No definido');
    
    // Mostrar el modal
    $('#modalEditarCierre').modal('show');
}

// Validación del formulario
document.getElementById('formEditarCierre').addEventListener('submit', function(e) {
    // Validar que al menos un campo tenga valor
    const campos = ['monto_apertura', 'monto_cierre', 'apertura_rs', 'monto_cierre_rs', 'apertura_usd', 'monto_cierre_usd'];
    let tieneValor = false;
    
    campos.forEach(function(campo) {
        const valor = parseFloat(document.getElementById(campo).value);
        if (!isNaN(valor) && valor > 0) {
            tieneValor = true;
        }
    });
    
    if (!tieneValor) {
        alert('Debe ingresar al menos un monto mayor a 0.');
        e.preventDefault();
        return false;
    }
    
    // Confirmación final
    if (!confirm('¿Está seguro que desea actualizar estos montos? Esta acción modificará los valores de apertura y cierre del usuario.')) {
        e.preventDefault();
        return false;
    }
});

// Formatear números mientras se escriben
document.addEventListener('DOMContentLoaded', function() {
    const camposNumero = ['monto_apertura', 'monto_cierre', 'apertura_rs', 'monto_cierre_rs', 'apertura_usd', 'monto_cierre_usd'];
    
    camposNumero.forEach(function(campo) {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.addEventListener('input', function(e) {
                // Evitar valores negativos
                if (parseFloat(this.value) < 0) {
                    this.value = 0;
                }
            });
        }
    });
});
</script>

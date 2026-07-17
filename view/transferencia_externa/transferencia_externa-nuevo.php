<h1 class="page-header">
    Nueva Transferencia a Paseo de la Sonrisa
</h1>

<ol class="breadcrumb">
    <li><a href="?c=transferencia_externa">Transferencias Externas</a></li>
    <li class="active">Nuevo Registro</li>
</ol>

<form id="crud-frm" method="post" action="?c=transferencia_externa&a=Guardar" enctype="multipart/form-data">
    <div class="form-group">
        <label>Monto</label>
        <input type="number" name="monto" class="form-control" placeholder="Ingrese el monto" min="0" step="0.001" required>
    </div>

    <div class="form-group">
        <label>Concepto</label>
        <input type="text" name="concepto" class="form-control" placeholder="Ej: Envío de recaudación" required>
    </div>

    <div class="form-group">
        <label>Comprobante (URL o Ruta opcional)</label>
        <input type="text" name="comprobante" class="form-control" placeholder="Ingrese URL de comprobante (opcional)">
    </div>

    <hr />
    <div class="alert alert-info">
        <strong>Atención:</strong> Esta transferencia será enviada directamente a la bandeja de entrada de Paseo de la Sonrisa. Quedará en estado PENDIENTE hasta que sea procesada en la central.
        <br><br>
        <em>Nota: Este registro no afecta los saldos locales de las cajas en este sistema. Es meramente un reporte externo.</em>
    </div>

    <div class="text-right">
        <button class="btn btn-primary">Enviar Transferencia</button>
    </div>
</form>

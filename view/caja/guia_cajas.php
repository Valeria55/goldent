<?php
require_once('plugins/tcpdf2/tcpdf.php');

// Crear nuevo PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GOLDENT LAB');
$pdf->SetTitle('Guía de Cajas');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

$fecha = date('d/m/Y \a \l\a\s H:i') . ' hs';

// Contenido extendido
$html = '
<h1 style="text-align:center;">SISTEMA DE CAJAS - DOCUMENTACIÓN</h1>
<h3>Guía de Usuario para Gestión de Cajas y Transferencias</h3>
<small>Generado el ' . $fecha . '</small>
<hr>
<h2>1. INFORMACIÓN GENERAL DEL SISTEMA</h2>
<h3>1.1 Conceptos Básicos</h3>
<ul>
<li><b>Desglose por Moneda:</b>
    <ul>
        <li>Muestra cuánto dinero físico disponible tienes de cada moneda (GS, USD, RS).</li>
        <li>Se calcula sumando los ingresos y restando los egresos de cada moneda.</li>
        <li>Representa el efectivo real que puedes encontrar en cada caja.</li>
    </ul>
</li>
<li><b>Disponible (Total):</b>
    <ul>
        <li>Es la conversión de todas las transacciones usando sus cotizaciones históricas.</li>
        <li>Cada transacción se convierte con la cotización que tenía en su momento.</li>
        <li>Representa el valor económico real de todas las operaciones en Guaraníes (GS).</li>
        <li>Puede diferir del saldo físico por fluctuaciones del tipo de cambio y puede ser negativo si hubo egresos con cotizaciones altas.</li>
    </ul>
</li>
<li><b>¿Por qué pueden diferir?</b>
    <ul>
        <li>El desglose muestra dinero físico actual.</li>
        <li>El total convertido muestra el valor histórico de las transacciones.</li>
        <li>Las fluctuaciones del tipo de cambio crean esta diferencia natural.</li>
    </ul>
</li>
</ul>
<h3>1.2 Tipos de Moneda</h3>
<ul>
    <li><b>GS (Guaraníes):</b> Moneda nacional paraguaya.</li>
    <li><b>USD (Dólares):</b> Moneda internacional.</li>
    <li><b>RS (Reales):</b> Moneda brasileña.</li>
    <li><b>Cotizaciones:</b>
        <ul>
            <li>Cada transacción registra la cotización del momento.</li>
            <li>Se usa para convertir automáticamente a GS.</li>
            <li>Las cotizaciones históricas se preservan para cálculos precisos.</li>
        </ul>
    </li>
</ul>
<h3>1.3 Niveles de Usuario</h3>
<ul>
    <li><b>Administrador (Nivel 1):</b> Ve todas las transacciones de todas las cajas.</li>
    <li><b>Cajero (Nivel 2):</b> Ve solo sus propias transacciones y movimientos de caja chica.</li>
    <li><b>Gerente (Nivel 4):</b> Acceso a tesorería y caja chica propia.</li>
    <li><b>Vendedor (Nivel 3):</b> No tiene acceso a movimientos de caja.</li>
</ul>
<hr>
<h2>2. GUÍA DE TRANSFERENCIAS</h2>
<h3>2.1 ¿Qué es una Transferencia?</h3>
<p>
Una transferencia permite mover dinero entre diferentes cajas del sistema, pudiendo cambiar de moneda en el proceso. Es útil para:
<ul>
    <li>Distribuir efectivo entre sucursales.</li>
    <li>Cambiar monedas (ejemplo: GS a USD).</li>
    <li>Centralizar o descentralizar fondos.</li>
    <li>Mantener liquidez en diferentes cajas.</li>
</ul>
</p>
<h3>2.2 Campos del Formulario de Transferencia</h3>
<ul>
    <li><b>Caja Destino:</b>
        <ul>
            <li>Selecciona la caja que recibirá el dinero (ejemplo: Banco, Tesorería).</li>
        </ul>
    </li>
    <li><b>Moneda de Transferencia:</b>
        <ul>
            <li>La moneda que sale de la caja actual (GS, USD, RS).</li>
            <li>Debe coincidir con el dinero disponible en la caja.</li>
        </ul>
    </li>
    <li><b>Monto:</b>
        <ul>
            <li>Cantidad que sale de la caja actual.</li>
            <li>No puede ser mayor al saldo disponible en la moneda seleccionada.</li>
        </ul>
    </li>
    <li><b>Cotización:</b>
        <ul>
            <li>La cotización vigente se utiliza para calcular el equivalente en GS si la transferencia es en USD o RS.</li>
            <li>La cotización se muestra en pantalla y se registra en la transacción.</li>
        </ul>
    </li>
    <li><b>Comprobante:</b>
        <ul>
            <li>En transferencias a Banco o Tesorería puede requerirse comprobante de la operación.</li>
        </ul>
    </li>
    <li><b>Cajero receptor:</b>
        <ul>
            <li>En transferencias a caja chica, se debe seleccionar el cajero receptor con apertura activa.</li>
        </ul>
    </li>
</ul>
<h3>2.3 Funcionamiento de la Transferencia entre Cajas</h3>
<ul>
    <li>El sistema descuenta el monto de la moneda elegida en la caja origen y lo acredita en la caja destino.</li>
    <li>Si la transferencia es en USD o RS, el equivalente en GS se calcula usando la cotización actual.</li>
    <li>El desglose por moneda se actualiza en ambas cajas.</li>
    <li>El total convertido refleja la suma de todos los movimientos históricos, cada uno con su cotización.</li>
    <li>Las transferencias quedan registradas con fecha, usuario, moneda, monto y cotización utilizada.</li>
</ul>
<hr>
<h2>3. RECOMENDACIONES Y ACLARACIONES</h2>
<ul>
    <li>El saldo real de cada moneda es el que se puede utilizar para transferencias o pagos.</li>
    <li>El total convertido es solo una referencia contable y puede variar respecto al saldo físico.</li>
    <li>Las diferencias entre el saldo físico y el total convertido son normales y esperadas por las variaciones de cotización.</li>
    <li>Siempre revise la cotización antes de realizar transferencias entre monedas.</li>
    <li>Consulte con el administrador en caso de dudas sobre saldos o movimientos históricos.</li>
</ul>
<hr>
<small>Página 1/1 - Generado el ' . $fecha . '</small>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('guia_cajas.pdf', 'I');

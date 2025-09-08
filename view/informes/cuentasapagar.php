<?php

require_once('plugins/tcpdf2/tcpdf.php');




$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage('P', 'A4');





$fechahoy = date("d/m/Y");
$result=$this->model->SumarMontosPorFechaHoy();
$result1=$this->model->SumarMontosPorFechaManana();
$result2=$this->model->SumarMontosPorFechaAnterior();
$suma= $result + $result1;

$html1 = <<<EOF
        <table width"100%" style="border: 2px solid #333; font-size:12px; background-color: #d1d1d1; color: black">
        <h3 align="center">Control Mensual de Cuentas a Pagar</h3>
        </table>
                <table width"100%" style="border: 0.5px solid #333; font-size:11px; background-color: #d1d1d1; color: black">
                    <tr align="center">
                        <th width="30%" style="border-left-width:1px ; border-right-width:1px">Vencen hoy:</th>
                        <th width="30%" style="border-left-width:1px ; border-right-width:1px">$result</th>
                        <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
                        <th width="14%" style="border-left-width:1px ; border-right-width:1px"><b>Fecha</b></th>
                        <th width="12%" style="border-left-width:1px ; border-right-width:1px">$fechahoy</th>
                    </tr>
                </table>
                
                <table width"100%" style="border: 0.5px solid #333; font-size:11px; background-color: #d1d1d1; color: black">
                    <tr align="center">
                        <th width="30%" style="border-left-width:1px ; border-right-width:1px">Vencen mañana:</th>
                        <th width="30%" style="border-left-width:1px ; border-right-width:1px">$result1</th>
                        <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
                        <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
                        <th width="12%" style="border-left-width:1px ; border-right-width:1px">     </th>
                    </tr>
                </table>
                <table width"100%" style="border: 0.5px solid #333; font-size:11px; background-color: #d1d1d1; color: black">
                    <tr align="center">
                        <th width="30%" style="border-left-width:1px ; border-right-width:1px">Total de pagos a vencer:</th>
                        <th width="30%" style="border-left-width:1px ; border-right-width:1px">$suma</th>
                        <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
                        <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
                        <th width="12%" style="border-left-width:1px ; border-right-width:1px">     </th>
                    </tr>
                </table>
                <table width"100%" style="border: 2px solid #333; font-size:11px; background-color: #d1d1d1; color: black">
                <tr align="center">
                    <th width="30%" style="border-left-width:1px ; border-right-width:1px">Vencidos:</th>
                    <th width="30%" style="border-left-width:1px ; border-right-width:1px">$result2</th>
                    <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
                    <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
                    <th width="12%" style="border-left-width:1px ; border-right-width:1px">     </th>
                </tr>
            </table>
    EOF;
    $pdf->writeHTML($html1, false, false, false, false, '');

    foreach($this->model->Listarinforme() as $r):
        $proveedor = $r->nombre;
        $concepto = $r->concepto;
        $monto = $r->monto;
        $montoSumar += $r->monto;
    
    $html1 = <<<EOF
    <table width"100%" style="border: 2px solid #333; font-size:10px; background-color: #d1d1d1; color: black">
    <tr align="center">
        <th width="30%" style="border-left-width: 1px; border-right-width: 1px; text-align: center; vertical-align: middle;"><b>PROVEEDOR</b></th>
        <th width="30%" style="border-left-width: 1px; border-right-width: 1px; text-align: center; vertical-align: middle;"><b>DESCRIPCIÓN DEL ÍTEM</b></th>
        <th width="14%" style="border-left-width: 1px; border-right-width: 1px; text-align: center; vertical-align: middle;"><b>CUOTA ESTE MES</b></th>
        <th width="14%" style="border-left-width: 1px; border-right-width: 1px; text-align: center; vertical-align: middle;"><b>VENCIMIENTO CUOTA ESTE MES</b></th>
        <th width="12%" style="border-left-width: 1px; border-right-width: 1px; text-align: center; vertical-align: middle;"><b>FECHA VIGENTE PAGO DE CUOTA</b></th>
    </tr>
    </table>
    <table width"100%" style="border: 2px solid #333; font-size:11px; background-color: #d1d1d1; color: black">
    <tr align="center">
        <th width="30%" style="border-left-width:1px ; border-right-width:1px">$proveedor</th>
        <th width="30%" style="border-left-width:1px ; border-right-width:1px">$concepto</th>
        <th width="14%" style="border-left-width:1px ; border-right-width:1px">$monto</th>
        <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
        <th width="12%" style="border-left-width:1px ; border-right-width:1px">     </th>
    </tr>
</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

endforeach;

$totalSumar= $montoSumar;

$html1 = <<<EOF
<table width"100%" style="border: 2px solid #333; font-size:11px; background-color: #d1d1d1; color: black">
    <tr align="center">
        
        <th width="60%" style="border-left-width:1px ; border-right-width:1px"><b> TOTAL: </b></th>
        <th width="14%" style="border-left-width:1px ; border-right-width:1px">$totalSumar</th>
        <th width="14%" style="border-left-width:1px ; border-right-width:1px">     </th>
        <th width="12%" style="border-left-width:1px ; border-right-width:1px">     </th>
    </tr>
</table>

EOF;
$pdf->writeHTML($html1, false, false, false, false, '');


$pdf->Output('cierre.pdf', 'I');



?>
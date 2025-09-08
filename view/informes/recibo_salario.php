<?php

// FALTA TERMINAR MODELO


class NumeroALetras
{
    private static $UNIDADES = [
        '',
        'UN ',
        'DOS ',
        'TRES ',
        'CUATRO ',
        'CINCO ',
        'SEIS ',
        'SIETE ',
        'OCHO ',
        'NUEVE ',
        'DIEZ ',
        'ONCE ',
        'DOCE ',
        'TRECE ',
        'CATORCE ',
        'QUINCE ',
        'DIECISEIS ',
        'DIECISIETE ',
        'DIECIOCHO ',
        'DIECINUEVE ',
        'VEINTE '
    ];

    private static $DECENAS = [
        'VENTI',
        'TREINTA ',
        'CUARENTA ',
        'CINCUENTA ',
        'SESENTA ',
        'SETENTA ',
        'OCHENTA ',
        'NOVENTA ',
        'CIEN '
    ];

    private static $CENTENAS = [
        'CIENTO ',
        'DOSCIENTOS ',
        'TRESCIENTOS ',
        'CUATROCIENTOS ',
        'QUINIENTOS ',
        'SEISCIENTOS ',
        'SETECIENTOS ',
        'OCHOCIENTOS ',
        'NOVECIENTOS '
    ];

    public static function convertir($number, $moneda = '', $centimos = '', $forzarCentimos = false)
    {
        $converted = '';
        $decimales = '';

        if (($number < 0) || ($number > 999999999)) {
            return 'No es posible convertir el numero a letras';
        }

        $div_decimales = explode('.',$number);

        if(count($div_decimales) > 1){
            $number = $div_decimales[0];
            $decNumberStr = (string) $div_decimales[1];
            if(strlen($decNumberStr) == 2){
                $decNumberStrFill = str_pad($decNumberStr, 9, '0', STR_PAD_LEFT);
                $decCientos = substr($decNumberStrFill, 6);
                $decimales = self::convertGroup($decCientos);
            }
        }
        else if (count($div_decimales) == 1 && $forzarCentimos){
            $decimales = 'CERO ';
        }

        $numberStr = (string) $number;
        $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);

        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'UN MILLON ';
            } else if (intval($millones) > 0) {
                $converted .= sprintf('%sMILLONES ', self::convertGroup($millones));
            }
        }

        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'MIL ';
            } else if (intval($miles) > 0) {
                $converted .= sprintf('%sMIL ', self::convertGroup($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'UN ';
            } else if (intval($cientos) > 0) {
                $converted .= sprintf('%s ', self::convertGroup($cientos));
            }
        }

        if(empty($decimales)){
            $valor_convertido = $converted . strtoupper($moneda);
        } else {
            $valor_convertido = $converted . strtoupper($moneda) . ' CON ' . $decimales . ' ' . strtoupper($centimos);
        }

        return $valor_convertido;
    }

    private static function convertGroup($n)
    {
        $output = '';

        if ($n == '100') {
            $output = "CIEN ";
        } else if ($n[0] !== '0') {
            $output = self::$CENTENAS[$n[0] - 1];   
        }

        $k = intval(substr($n,1));

        if ($k <= 20) {
            $output .= self::$UNIDADES[$k];
        } else {
            if(($k > 30) && ($n[2] !== '0')) {
                $output .= sprintf('%sY %s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            } else {
                $output .= sprintf('%s%s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            }
        }

        return $output;
    }
}


require_once('plugins/tcpdf2/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->AddPage();

$mes = (!isset($_GET["mes"]))? date("Y-m-d"):$_GET["mes"]."-01"; 
 $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 

 /*   // $IPS='';
    $r=$this->model->ObtenerRecibo($_REQUEST['id']);
    $total = $r->monto;
    $monto = $_REQUEST['monto'];
    $saldo = $r->saldo;
    $fecha_pagada = $r->fecha_pagada;
    $fecha_recibo = $r->fecha_emitida;
    $concepto = $r->concepto;
    $fecha = date("Y-m-d");
    
    //  $ipss = number_format(($r->ips), 0, "," , ".");
    // if($r->ips != null){
    //     $IPS='IPS ('.$ipss.')';
    // }
  
    $d= strtoupper(date("d", strtotime($fecha)));
    $m= strtoupper($meses[intval(date("m", strtotime($fecha)))-1]) ;
    $y=date("Y", strtotime($fecha));
    
    // $monto = number_format(($_REQUEST['monto']), 0, "," , ".");
    $saldo  = number_format(($r->saldo), 0, "," , ".");
    $total = number_format(($r->monto), 0, "," , ".");
    $monto = number_format(($monto), 0, "," , ".");
    $fecha_pagada= date("d/m/Y", strtotime($r->fecha_pagada)); 
    $fecha_recibo= date("d/m/Y", strtotime($r->fecha_emitida)); 
    $mes= ($meses[intval(date("m", strtotime($r->fecha_emitida)))-1]) ;
    
    $tipo = (($r->moneda)=='Gs')? 'GUARANIES': 'DÓLARES';
    $tipo2 = (($r->moneda)=='Gs')? 'Gs.': 'Usd.';
    $letras = NumeroALetras::convertir($saldo, $tipo, 'CENTAVOS');

*/


$salarioBasico = "2.192.839";
$IPS = "197.356";
$nombreEmpleado = "SUSANA GIMENEZ";
$nombreEmpleador = "IMPORTADORA IMPORTADOS S.A.";
$CI = "5.713.203";
$fechahoy = date("d/m/Y");
$desde = "01/03/2023";
$hasta = "31/03/2023";
$salarioACobrar = "1.995.483";

$html1 = <<<EOF

<table width ="100%" style="border: 1px solid #333; text-align:center; line-height: 15px; font-size:9px">
	
    <tr align="center" height="5">
		<td colspan ="2"></td>                
	</tr>
    <tr align="center" height="5">
        <td colspan ="2" style="font-size:12px"><b> RECIBO DE SALARIO CORRESPONDIENTE AL MES DE ABRIL 2023 </b></td>                
    </tr>
    <tr align="center" height="5">
        <td colspan ="2"></td>                
    </tr>
    <tr align="center" height="5">
        <td colspan ="2"></td>                
    </tr>

    <tr align="left">
        <td colspan="1"><b>Empleador:</b> $nombreEmpleador </td>
        <td colspan="1"> <b>Periodo de Pago:</b> desde el $desde hasta $hasta </td>
    </tr>

    <tr align="left">
        <td colspan="1"><b>Trabajador:</b> $nombreEmpleado </td>
        <td colspan="1"><b>Salario Básico:</b> Gs. $salarioBasico </td>
    </tr>

    <tr align="center" height="10px">
        <td colspan ="2" style="border: 1px solid #666;"></td>                
    </tr>

    <tr align="center" style="font-size:7px" nowrap="nowrap">
        <td width="12%" style="border: 1px solid #666" rowspan ="2" ><b>DÍAS TRABAJADOS</b></td>
        <td width="8%" style="border: 1px solid #666" rowspan ="2"><b>SALARIO BÁSICO</b></td>
        <td width="8%" style="border: 1px solid #666" rowspan ="2"><b>SUB TOTAL</b></td>
        <td width="8%" style="border: 1px solid #666" rowspan ="2"><b>HORAS EXTRAS</b></td>
        <td width="8%" style="border: 1px solid #666" rowspan ="2"><b>COMISIONES</b></td>
        <td width="10%" style="border: 1px solid #666" rowspan ="2"><b>OTRAS REMUNERACIONES (especificar)</b></td>
        <td width="8%" style="border: 1px solid #666" rowspan ="2"><b>TOTAL SALARIO</b></td>
        <td width="20%" style="border: 1px solid #666" colspan ="2" ><b>DESCUENTOS</b></td>
        <td width="8%" style="border: 1px solid #666"><b>TOTAL</b></td>
        <td width="10%" style="border: 1px solid #666"><b>SALDO</b></td>
    </tr>
    
    <tr align="center" style="font-size:7px" nowrap="nowrap">

        <td width="10%" style="border: 1px solid #666"><b>IPS</b></td>
        <td width="10%" style="border: 1px solid #666"><b>OTROS</b></td>
        <td width="8%" style="border: 1px solid #666"><b>DESC.  </b></td>
        <td width="10%" style="border: 1px solid #666"><b>A COBRAR</b></td>
    </tr>

    <tr nowrap="nowrap" style="font-size:8px; text-align:center">
		<td width="12%" vertical-align: sub; style="border: 1px solid #666">30</td>
		<td width="8%" vertical-align: sub;  style="border-left-width:1px ; border-right-width:1px ; border: 1px solid #666">$salarioBasico</td>
        <td width="8%" style="border: 1px solid #666">$salarioBasico</td>
        <td width="8%" style="border: 1px solid #666">0</td>
        <td width="8%" style="border: 1px solid #666">0</td>
        <td width="10%" style="border: 1px solid #666">0</td>
        <td width="8%" style="border: 1px solid #666">$salarioBasico</td>
        <td width="10%" style="border: 1px solid #666">$IPS</td>
        <td width="10%" style="border: 1px solid #666">0</td>
        <td width="8%" style="border: 1px solid #666">197.356</td>
        <td width="10%" style="border: 1px solid #666">1.995.483</td>
        
	</tr>
   
    <tr>
        <td width="80%"></td>
        <td width="20%"></td> 
	</tr>

    <tr align="left" height="10px">
        <td colspan ="2" style="border: 1px solid #666;"><b>A COBRAR: $salarioACobrar.-Son: </b>letras..</td>            
    </tr>

    <tr align="left" height="10px">
    <td colspan ="2" style="border: 1px solid #666;"></td>            
    </tr>



    <tr width="100%" height="50px" align="bottom">
		  <td width="50%" nowrap="nowrap" style="border-left-width:1px solid #333; border-right-width:1px solid #333;" align="center"> ...........................................
          </td>
          <td width="50%"  nowrap="nowrap" style="border-left-width:1px solid #333; border-right-width:1px solid #333;" align="center"> ...........................................
         </td>
	</tr> 

    <tr width="100%" height="50px" align="bottom" style="font-size:8px">
        <td width="50%" nowrap="nowrap" style="border-left-width:1px solid #333; border-right-width:1px solid #333;" align="center"> EMPLEADOR O REPRESENTANTE LEGAL
        </td>
        <td width="50%"  nowrap="nowrap" style="border-left-width:1px solid #333; border-right-width:1px solid #333;" align="center"> RECIBI CONFORME: $nombreEmpleado
        </td>
    </tr> 

    <tr width="100%" height="50px" align="bottom" style="font-size:8px">
        <td width="50%" nowrap="nowrap" style="border-rigth: 1px solid #333" align="center"> 
        </td>
        <td width="50%"  nowrap="nowrap" style="border-left-width:1px solid #333; border-right-width:1px solid #333;" align="center"> $fechahoy C.I. N° $CI
        </td>
    </tr> 

    <tr><td style="border-left-width:1px solid #333; border-right-width:1px solid #333;"></td></tr>
     
</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');




// <tr width="100%" height="50px" align="bottom">
// <td colspan="6" nowrap="nowrap" style="border: 1px solid #333" > ...........................
// </td>
// <td colspan="6" nowrap="nowrap" style="border: 1px solid #333" >
//   <h4 align="center">REPRESENTANTE LEGAL:</h4> 
//   <h4 align="center"> Firma:</h4>
// </td>
// </tr> 

ob_end_clean();
$pdf->Output('recibosalario.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
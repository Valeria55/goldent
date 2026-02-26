<?php

// PRUEBA

/**
 * Clase que implementa un coversor de números
 * a letras.
 *
 * Soporte para PHP >= 5.4
 * Para soportar PHP 5.3, declare los arreglos
 * con la función array.
 *
 * @author AxiaCore S.A.S
 *
 */

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



// FIN  PRUEBA 


require_once('plugins/tcpdf/pdf/tcpdf_include.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$medidas = array(210, 340); // Ajustar aqui segun los milimetros necesarios;
$pdf = new TCPDF('P', 'mm', $medidas, true, 'UTF-8', false); 
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

$pdf->AddPage();

$id = $_GET['id'];

$ingreso = $this->model->ListarVenta($id);

$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

$dia = date("d", strtotime($ingreso->fecha));
$mes = date("m", strtotime($ingreso->fecha));
$anho = date("Y", strtotime($ingreso->fecha));

$monto = number_format($ingreso->monto, 0,",",".");

$letras = NumeroALetras::convertir($ingreso->monto);

if($ingreso->forma_pago == "Efectivo "){
    $efectivo = "X";
    $cheque = "";
    $nroCheque = "";
    $banco = "";
}elseif($ingreso->forma_pago == "Cheque"){
    $efectivo = "";
    $cheque = "X";
    $nroCheque = $ingreso->comprobante;
    $banco = $ingreso->banco;
}

$mes = $meses[$mes-1];

$header = <<<EOF
    <p> </p>
    <p> </p>
	<table width ="100%" style="text-align:center; line-height: 18px; font-size:9px">
	    <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap>  </td>
          <td width="31%" align="left" nowrap>  </td>
          <td width="12%" align="left">$dia</td>
          <td width="20%" align="left">$mes</td>
          <td width="10%" align="left">$anho</td>
        </tr>
        <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap> $ingreso->nro_comprobante </td>
          <td width="30%" align="left" nowrap> $monto </td>
          <td width="40%" align="left">$ingreso->nombre</td>
        </tr>
        <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap>  </td>
          <td width="48%" align="left" nowrap>  </td>
          <td width="40%" align="left">$ingreso->ruc</td>
        </tr>
        <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap>  </td>
          <td width="32%" align="left" nowrap></td>
          <td width="40%" align="left"></td>
        </tr>
        <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap>  </td>
          <td width="15%" align="left" nowrap></td>
          <td width="40%" align="left" style="font-size:8px;">$letras</td>
        </tr>
        <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap>  </td>
          <td width="31%" align="left" nowrap></td>
          <td width="40%" align="left">$ingreso->concepto</td>
        </tr>
        <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap>  </td>
          <td width="15%" align="left" nowrap></td>
          <td width="40%" align="left"></td>
        </tr>
        
        <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap>  </td>
          <td width="15%" align="left" nowrap></td>
          <td width="40%" align="left"></td>
        </tr>
        
        <tr>
          <td width="10%"></td>
          <td width="20%" align="left" nowrap>  </td>
          <td width="40%" align="left">$monto</td>
        </tr>
        <tr>
          <td width="15%">$efectivo</td>
          <td width="20%" align="left" nowrap> $cheque</td>
          <td width="40%" align="left">$nroCheque</td>
          <td width="40%" align="left">$banco</td>
        </tr>
        <tr>
          <td width="11%"></td>
          <td width="18%" align="left" nowrap></td>
          <td width="40%" align="left"></td>
          <td width="40%" align="left"></td>
        </tr>
        <tr>
          <td width="11%"></td>
          <td width="18%" align="left" nowrap> X</td>
          <td width="40%" align="left">$monto</td>
          <td width="40%" align="left"></td>
        </tr>
	</table>
EOF;

$pdf->writeHTML($header, false, false, false, false, '');
$espacio = <<<EOF
    <p> </p>
EOF;
$pdf->writeHTML($espacio, false, false, false, false, '');
$pdf->writeHTML($espacio, false, false, false, false, '');
$pdf->writeHTML($header, false, false, false, false, '');
$pdf->writeHTML($espacio, false, false, false, false, '');
$pdf->writeHTML($header, false, false, false, false, '');

if (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output('uin.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
<?php


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


require_once('plugins/tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->AddPage('P', 'A4');

$mes = (!isset($_GET["mes"]))? date("Y-m-d"):$_GET["mes"]."-01"; 
 $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 

    $IPS='';
    $r=$this->model->Obtener($_GET['id']);
    $total = $r->total;
    $concepto = $r->concepto;
    $motivo = $r->motivo_descuento;
    $categoria = $r->categoria;
    
     $ipss = number_format(($r->ips), 0, "," , ".");
    if($r->ips != null){
        $IPS='IPS ('.$ipss.')';
    }
  
    $d= strtoupper(date("d", strtotime($r->fecha)));
    $m= strtoupper($meses[intval(date("m", strtotime($r->fecha)))-1]) ;
    $y=date("Y", strtotime($r->fecha));
    
    $monto = number_format(($r->monto), 0, "," , ".");
    $descuento = number_format(($r->descuento), 0, "," , ".");
    $total = number_format(($r->monto - $r->descuento - $r->ips), 0, "," , ".");
    $fecha= date("d/m/Y", strtotime($r->fecha)); 



$html1 = <<<EOF

   <table  width ="100%"<span style="border: 1px solid #333; text-align:justify; line-height: 15px; font-size:9px ">>
       <tr><br>
          <td width="50%" align="center" style=" font-size:12px" ><img src="assets/img/LogoTrinity.PNG" width="80"></td>
          <td width="50%" align="left" style=" font-size:12px; line-height: 35px" ><h2>RECIBO</h2></td>
       </tr>
         <tr>
           <td  width="100%" align="center" style="border: 1px solid #959595"><b>CIUDAD DEL ESTE, $d DE $m DEL $y </b> </td>
       </tr>
       <tr>
          <td  width="20%" align="justify" style="border: 1px solid #000000">RECIBIMOS DE: </td>
          <td width="80%" align="center" style="border: 1px solid #000000;  font-size:9px"> <b> GOLDENT LAB</b></td>
       </tr>
        <tr>
          <td width="20%" align="justify" style="border: 1px solid #000000">RUC:</td>
          <td width="80%" align="center" style="border: 1px solid #000000"> <b> 80.098.689-0</b></td>
       </tr>
        <tr>
          <td width="20%" align="justify" style="border: 1px solid #000000">EN CONCEPTO DE:</td>
          <td width="80%" align="left" style="border: 1px solid #000000"> <b>$categoria $concepto 
          ($monto)</b></td>
       </tr>
         <tr>
         <td  width="20%" align="left" style="border: 1px solid #000000">DESCUENTO:</td>
          <td  width="80%" align="left" style="border: 1px solid #000000"> <b></b></td>
       </tr>
       <tr>   
          <td  width="20%" align="justify" style="border: 1px solid #000000">SUMA DE GS O USD:</td>
          <td width="50%" align="left" style="border: 1px solid #000000"> <b>$total $r->moneda </b></td>
       </tr>
       <tr>   
          <td  width="20%" align="left" style="border: 1px solid #000000">ACLARACION:</td>
          <td  width="50%" align="left" style="border: 1px solid #000000"> <b>$r->nombre</b></td>
          
       </tr>
        <tr>   
          <td  width="20%" align="left" style="border: 1px solid #000000">CEDULA:</td>
          <td  width="50%" align="left" style="border: 1px solid #000000"> <b>$r->ruc</b></td>
       </tr>
      
       <tr>   
          <td  width="20%" align="left" style="border: 1px solid #000000">FECHA:</td>
          <td  width="50%" align="left" style="border: 1px solid #000000"><b>$fecha</b></td>
          <td  width="30%" align="center" style="border: 1px solid #000000">FIRMA</td>
       </tr>
    
    </table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');



ob_end_clean();
$pdf->Output('recibosalario.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
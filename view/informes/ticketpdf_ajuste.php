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


require_once('plugins/tcpdf2/tcpdf.php');
$medidas = array(80, 250); // Ajustar aqui segun los milimetros necesarios;

$pdf = new TCPDF('P', 'mm', $medidas, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetDefaultMonospacedFont('courier');
$pdf->SetMargins(7,0,7, true);
$pdf->SetAutoPageBreak(TRUE, 0);



$pdf->AddPage();

$id_venta = $_GET['id'];
foreach($this->model->Listar($id_venta) as $r){
    
    $cliente = $r->user;
    $ruc = $r->ruc;
    $fecha = date("d/m/Y", strtotime($r->fecha_venta));
    $contado = $r->contado;
    $telefono = $r->telefono;
    $direccion = $r->direccion;
    $vendedor = $r->vendedor;
    $observacion = $r->comprobante;
}
//<img src="assets/img/CANDY1.jpg" width="100">

$html1 = <<<EOF

	<table width ="100%" style="text-align:center; line-height: 15px; font-size:10px">
	    <p></p>
		<tr>
			<td style="vertical-align: middle;"><img src="assets/img/LogoTrinity.PNG" width="120"></td>
		</tr>
	<tr>
	    <td><b>Ticket N°:</b> $id_venta </td>    
	</tr>
    <tr>
      <td align="left"><b>Fecha de Emisión:</b> $fecha</td>
    </tr>
    <tr align="left">
      <td><b>Observacion:</b> $observacion </td>
    </tr>
    <tr align="left">
      <td><b>Funcionario:</b> $cliente </td>
    </tr>
        
    </table>
    <table>
		<tr><td>----------------------------------------------</td></tr>
	</table>
    <table width ="100%" style=" text-align:center; line-height: 15px; font-size:7px">
    <tr align="center">
      <td width="15%"><b>Ca.</b></td>
      <td width="15%"><b>Cod.</b></td>
      <td width="50%"><b>Prod.</b></td>
      <td width="20%"><b>| Monto</b></td>
    </tr>
    </table>
    
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$sumaTotal = 0;
$sumades = 0;
$cantidad_total = 0;
$items = 0;
foreach($this->model->Listar($id_venta) as $r){
$items++;

// Convertir valores a numéricos para evitar errores de tipo
$precio_venta_num = (float)$r->precio_venta;
$cantidad_num = (float)$r->cantidad;
$descuento_num = (float)($r->descuento ?? 0);
$descuentov_num = (float)($r->descuentov ?? 0);

$subTotal = number_format($precio_venta_num, 0, "," , ".");
$venta = ($precio_venta_num * $cantidad_num);
$venta =  number_format($venta, 0, "," , ".");

if($descuentov_num == 0){
    $descuento = 0;
    $des = 0;
}else{
    $descuento = $precio_venta_num - ($precio_venta_num * ($descuento_num / 100)); //precio con descuento
    $des = ($precio_venta_num * $cantidad_num) * ($descuentov_num / 100);
}
$descuento = number_format($descuento, 0, "," , ".");
$total = (($precio_venta_num * $cantidad_num) - ($precio_venta_num * $cantidad_num * ($descuento_num / 100)));
$total =  number_format($total, 0, "," , ".");
$precio_venta =  number_format($precio_venta_num, 0, "," , ".");
$descuentov =  number_format($des, 0, "," , ".");


$html1 = <<<EOF

		<table width="100%" style="text-align:center; line-height: 8px; font-size:7px">
			<tr nowrap="nowrap">
		        <td width="12%" >$r->cantidad</td>
		        <td width="18%" style="text-align:center; font-size:6px">$r->codigo</td>
				<td width="50%" style="text-align:center; font-size:6px">$r->producto</td>
                <td width="20%" >$venta</td>
			</tr>
			
	    </table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$cantidad_total += $cantidad_num;
$sumaTotal += ($precio_venta_num * $cantidad_num);
$sumades += $des;

}
$totalventa=$sumaTotal-$sumades;
$sumaTotades =  number_format($sumades, 0, "," , ".");
$sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
$sumaTotalpago =  number_format($totalventa, 0, "," , ".");
$html2 = <<<EOF

	<table>
		<tr><td>----------------------------------------------</td></tr>
	</table>
	
	<table width="100%" style="text-align:center; line-height: 7px; font-size:8px">
	
	    <tr>
	      <td width="30%">Items:$items</td>
	      <td width="30%" align="right"style="text-align:center;font-size:5px">TOTAL: Gs.</td>
	      <td width="40%"><b>$sumaTotalV</b></td>
	    </tr>
	</table>
	<br><br><br><br>.
	
	
    
EOF;

$pdf->writeHTML($html2, false, false, false, false, '');




// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('uin.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
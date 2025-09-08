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

$body_table_style = 'font-size:8px; border-top: 1px solid #0000; border-bottom: .5px solid #0000; background-color: #fff;';
$header_table_style = 'border-top: .5px solid #0000; border-bottom: 1px solid #0000; font-size:8.5px; background-color: #fff; color: #202020; font-weight: bold;';

// require_once('plugins/tcpdf/pdf/tcpdf_include.php');
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
foreach($this->model->ListarPresupuestoticket($id_venta) as $r){
    $cliente = $r->nombre;
    $ruc = $r->ruc;
    $fecha = date("d/m/Y", strtotime($r->fecha_presupuesto));
    $contado = $r->contado;
    $telefono = $r->telefono;
    $direccion = $r->direccion;
    $vendedor = $r->user;
}
//<img src="assets/img/CANDY1.jpg" width="100">

$html1 = <<<EOF

	<table width ="100%" style="text-align:center; line-height: 15px; font-size:10px">
	    <p></p>
		<tr>
			<td style="vertical-align: middle;"><img src="assets/img/logo.png" width="70"></td>
		</tr>
	<tr>
	    <td><b>Ticket N°:</b> $id_venta</td>    
	</tr>
    <tr>
      <td align="left"><b>Fecha de Emisión:</b> $fecha</td>
    </tr>
    <tr align="left">
      <td><b>RUC/CI:</b> $ruc </td>
    </tr>
    <tr align="left">
      <td><b>Cliente:</b> $cliente </td>
    </tr>
    <tr align="left">
      <td><b>Vendedor:</b> $vendedor </td>
    </tr>
        
    </table>
    <table>
		<tr><td>----------------------------------------------</td></tr>
	</table>
    <table width ="100%" style="$header_table_style">
    <tr align="center">
      <td width="15%"><b>Ca.</b></td>
      <td width="15%"><b>Cód.</b></td>
      <td width="50%"><b>Prod.</b></td>
      <td width="20%"><b>| Monto</b></td>
    </tr>
    </table>
    
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$sumaTotal = 0;
$items = 0;
$indice = 0;
foreach($this->model->ListarPresupuestoticket($id_venta) as $r){
$items +=$r->cantidad;

$subTotal = number_format(($r->precio_venta), 0, "," , ".");
$venta = ($r->precio_venta*$r->cantidad)-($r->descuento*$r->cantidad);
$venta =  number_format($venta, 0, "," , ".");

$precio_venta =  number_format($r->precio_venta-$r->descuento, 0, "," , ".");
$descuentov =  number_format($des, 0, "," , ".");


	$bg = ($indice % 2 == 0) ? 'background-color: #eeeeee;' : '';

$html1 = <<<EOF

		<table width="100%" style="border-top: 1px solid #333; font-size: 7px;">
			<tr nowrap="nowrap">
		        <td width="12%" >$r->cantidad</td>
		        <td width="14%" style="text-align:left; font-size:6px">$r->codigo_producto</td>
				<td width="60%" style="text-align:left; font-size:8px"><b>$r->producto (UN. $precio_venta)</b></td>
                <td width="20%" align="right">$venta</td>
			</tr>
			
	    </table>
	    

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$cantidad_total += $r->cantidad;
$sumaTotal +=  ($r->precio_venta*$r->cantidad)-($r->descuento*$r->cantidad);
$sumades += $des;
$indice++;
}

$f=date('Y-m-d');

$c=$this->cierre->ObtenerCierre();

$totalventa=$sumaTotal-$sumades;
$sumaTotades =  number_format($sumades, 0, "," , ".");
$sumaTotalgs=  number_format($sumaTotal, 0, "," , ".");
$sumaTotalpago =  number_format($totalventa, 0, "," , ".");

$sumaTotalV =  number_format($sumaTotal/$c->cot_dolar_tmp, 2, "," , ".");
$sumaTotalrs =  number_format($sumaTotal/$c->cot_real_tmp, 2, "," , ".");
$html2 = <<<EOF

	<table>
		<tr><td>----------------------------------------------</td></tr>
	</table>
	
	<table width="100%" style="text-align:center; line-height: 7px; font-size:10px">
	
	    <tr>
	      <td width="30%">Items:$items</td>
	      <td width="30%" align="right"style="text-align:center;font-size:10px">Total: Gs.</td>
	      <td width="40%" align="right"><b>$sumaTotalgs</b></td>
	    </tr>
	    <br>
	    <tr>
	      <td width="30%"></td>
	      <td width="30%" align="right"style="text-align:center;font-size:10px">Total: Usd.</td>
	      <td width="40%" align="right"><b>$sumaTotalV</b></td>
	    </tr>
	    <br>
	     <tr>
	      <td width="30%"></td>
	      <td width="30%" align="right"style="text-align:center;font-size:10px">Total: Rs.</td>
	      <td width="40%" align="right"><b>$sumaTotalrs</b></td>
	    </tr>
	</table>
	<br><br>
	
	
    
EOF;

$pdf->writeHTML($html2, false, false, false, false, '');


$html1 = <<<EOF

		<table width="100%" style="text-align:center; line-height: 8px; font-size:8px">
			<tr nowrap="nowrap">
		        <td width="100%" >"Verifique las piezas antes de salir, ya que no se aceptarán devoluciones posteriores."</td>
		      
			</tr>
			 <tr nowrap="nowrap">
			    <td width="100%" ></td>
			    </tr>
			    <tr nowrap="nowrap">
                <td width="100%" style="font-style: italic;"><br>Desarrollado por Trinity Technologies E.A.S.</td>
			    </tr>
			    <tr nowrap="nowrap">
			    <td width="100%" ><br>www.trinitytech.com.py</td>
			    </tr>
	    </table>
<br><br>
EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('uin.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
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
        'un ',
        'dos ',
        'tres ',
        'cuatro ',
        'cinco ',
        'seis ',
        'sieste ',
        'ocho ',
        'nueve ',
        'diez ',
        'once ',
        'doce ',
        'trece ',
        'catorce ',
        'quince ',
        'dieciseis ',
        'diecisiete ',
        'dieciocho ',
        'diecinueve ',
        'veinte '
    ];

    private static $DECENAS = [
        'venti',
        'treinta ',
        'cuarenta ',
        'cincuenta ',
        'sesenta ',
        'setenta ',
        'ochenta ',
        'noventa ',
        'cien '
    ];

    private static $CENTENAS = [
        'ciento ',
        'docientos ',
        'trescientos ',
        'cuatrocientos ',
        'quinientos ',
        'seiscientos ',
        'setecientos ',
        'ochocientos ',
        'novecientos '
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
            $decimales = 'cero ';
        }

        $numberStr = (string) $number;
        $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);

        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'un millon ';
            } else if (intval($millones) > 0) {
                $converted .= sprintf('%smillones ', self::convertGroup($millones));
            }
        }

        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'mil ';
            } else if (intval($miles) > 0) {
                $converted .= sprintf('%smil ', self::convertGroup($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'un ';
            } else if (intval($cientos) > 0) {
                $converted .= sprintf('%s ', self::convertGroup($cientos));
            }
        }

        if(empty($decimales)){
            $valor_convertido = $converted . strtoupper($moneda);
        } else {
            $valor_convertido = $converted . strtoupper($moneda) . ' con ' . $decimales . ' ' . strtoupper($centimos);
        }

        return $valor_convertido;
    }

    private static function convertGroup($n)
    {
        $output = '';

        if ($n == '100') {
            $output = "cien ";
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

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$medidas = array(210, 357); // Ajustar aqui segun los milimetros necesarios;
$pdf = new TCPDF('P', 'mm', $medidas, true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

$pdf->AddPage();

$id_venta = $_GET['id'];
foreach($this->venta->Listar($id_venta) as $r){
    $cliente = $r->nombre_cli;
    $ruc = $r->ruc;
    $fecha = date("d/m/Y", strtotime($r->fecha_venta));
    $telefono = $r->telefono;
    $direccion = $r->direccion;
    $vendedor = $r->vendedor;
    $contado = "";
    $credito = "";
    if($r->contado=="Contado"){
        $contado = "X";
    }else{
        $credito = "X";
    }
    if($r->motivo_cliente=="gs"){
    	$tipo ="Guaraníes";
    }else{
    	$tipo ="Guaraníes";
    }
}


$header = <<<EOF
    <h1> </h1>
	<table width ="100%" style="text-align:center; line-height: 13px; font-size:9px">
		<tr>
          <td style="font-size:19px" width="65%" align="left" nowrap></td>
        </tr>
	    <tr>
          <td width="16%"></td>
          <td width="31%" align="left" nowrap> $fecha </td>
          <td width="42%" align="left" nowrap> $ruc </td>
          <td width="10%" align="left">$contado</td>
          <td width="10%" align="left">$credito</td>
        </tr>
        <tr>
          <td width="21%"></td>
          <td width="53%" align="left" nowrap>$cliente</td>
          <td width="20%" align="left">$telefono</td>
          <td width="5%"></td>
        </tr>
        <tr align="left">
          <td width="12%"></td>
          <td width="70%">$direccion</td>
          <td width="25%"></td>
          <td width="5%"></td>
        </tr>
    </table>
    <table>
		<tr nowrap="nowrap" style="font-size:8px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
	<table>
		<tr nowrap="nowrap" style="font-size:12px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
EOF;

$pdf->writeHTML($header, false, false, false, false, '');

$header2 .= <<<EOF

	<table width ="100%" style="text-align:center; line-height: 13px; font-size:9px">
		<tr>
          <td style="font-size:27px" width="65%" align="left" nowrap></td>
        </tr>
	    <tr>
          <td width="16%"></td>
          <td width="31%" align="left" nowrap> $fecha </td>
          <td width="42%" align="left" nowrap> $ruc </td>
          <td width="10%" align="left">$contado</td>
          <td width="10%" align="left">$credito</td>
        </tr>
        <tr>
          <td width="21%"></td>
          <td width="53%" align="left" nowrap>$cliente</td>
          <td width="20%" align="left">$telefono</td>
          <td width="5%"></td>
        </tr>
        <tr align="left">
          <td width="12%"></td>
          <td width="70%">$direccion</td>
          <td width="25%"></td>
          <td width="5%"></td>
        </tr>
    </table>
    <table>
		<tr nowrap="nowrap" style="font-size:8px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
	<table>
		<tr nowrap="nowrap" style="font-size:12px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
EOF;
$header3 .= <<<EOF

 
	<table width ="100%" style="text-align:center; line-height: 13px; font-size:9px">
		<tr>
          <td style="font-size:22px" width="65%" align="left" nowrap></td>
        </tr>
	    <tr>
          <td width="16%"></td>
          <td width="31%" align="left" nowrap> $fecha </td>
          <td width="42%" align="left" nowrap> $ruc </td>
          <td width="10%" align="left">$contado</td>
          <td width="10%" align="left">$credito</td>
        </tr>
        <tr>
          <td width="21%"></td>
          <td width="53%" align="left" nowrap>$cliente</td>
          <td width="20%" align="left">$telefono</td>
          <td width="5%"></td>
        </tr>
        <tr align="left">
          <td width="12%"></td>
          <td width="70%">$direccion</td>
          <td width="25%"></td>
          <td width="5%"></td>
        </tr>
    </table>
    <table>
		<tr nowrap="nowrap" style="font-size:8px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
	</table>
	<table>
		<tr nowrap="nowrap" style="font-size:12px;">
			<td width="7%" ></td>
			<td width="44%"></td>
			<td width="12%" align="right"></td>
			<td width="12%"></td>
			<td width="12%" align="right"></td>
			<td width="12%" align="right"></td>
		</tr>
			<tr>
          <td style="font-size:5px" width="65%" align="left" nowrap></td>
        </tr>
	</table>
EOF;


$sumaTotal = 0;
$cantidad = 0;
$sumaTotal5 = 0.0;
$sumaTotal10 = 0.0;
$sumaTotalexe=0.0;
$iva10 = 0.0;
$items = "";
$cantidad_total = 0;
$espacio = "";
$espacio2 = "";
$iva5 = 0.0;
$exe=0.0;
$exeP=0.0;

foreach($this->venta->Listar($id_venta) as $r){
  $cantidad++;
  if($r->motivo_cliente=="gs"){
    if ($r->iva==5){
      //$sumaTotal5 += ((($r->precio_venta*$r->cantidad)-($r->precio_venta*$r->cantidad*($r->descuento/100))));
      $sumaTotal5 += $r->total;
      $iva5+=($r->total/1.05);
      $iva5P=number_format(($r->total), 0, "," , ".");
      $iva10P = "";
      $exeP= "";
    }else{
      if($r->iva==10){
      $sumaTotal10 += $r->total;
      $iva10+=$r->total/11;
      $iva10P=number_format(($r->total), 0, "," , ".");
      $iva5P="";
      $exeP="";
      }else{
        $sumaTotalexe += ($r->total);
        $exe+=($r->total);
        $exeP=number_format(($r->total), 0, "," , ".");
        $iva5P="";
        $iva10P = "";
      }
    }
  }else{
    if ($r->iva==5){
      $sumaTotal5 += ($r->total);
      $iva5+=($r->total/1.05);
      $iva5P=number_format(($r->total), 0, "," , ".");
      $iva10P = "";
      $exeP= "";
    }else{
      if($r->iva==10){
        $sumaTotal10 += $r->total;
        $iva10+=$r->total/11;
        $iva10P=number_format(($r->total), 0, "," , ".");
        $iva5P="";
        $exeP="";
      }else{
        $sumaTotalexe += ($r->total);
        $exe+=$r->total;
        $exeP=number_format(($r->total), 0, "," , ".");
        $iva5P="";
        $iva10P = "";
      }
    }
  }

  if($r->motivo_cliente=="gs"){
    $subTotal = number_format(($r->precio_venta), 0, "," , ".");
    $descuento = ($r->precio_venta)-($r->precio_venta*($r->descuento/100)); //precio con descuento
    $descuento = number_format($descuento, 0, "," , ".");
    $total = $r->total;
    $total =  number_format($total, 0, "," , ".");
  }else{
    $subTotal = number_format(($r->precio_venta), 0, "," , ".");
    $descuento = ($r->precio_venta)-($r->precio_venta*($r->descuento/100)); //precio con descuento
    $descuento = number_format($descuento, 0, "," , ".");
    $total = $r->total;
    $total =  number_format($total, 0, "," , ".");
  }

$items .= <<<EOF

		<table>
			<tr>
          		<td style="font-size:1px" width="65%" align="left" nowrap></td>
        	</tr>
			<tr nowrap="nowrap" style="font-size:8px">
		    	<td width="8%"align="left">$r->cantidad</td>
				<td width="5%" align="rigth"></td>
				<td width="49%" align="center">$r->producto</td>
				<td width="11%" align="center">$subTotal</td>
				<td width="10%" align="center">$exeP</td>
				<td width="7%" align="center">$iva5P</td>
				<td width="12%" align="center">$iva10P</td>
			</tr>
		</table>

EOF;

$cantidad_total += $r->cantidad;
$sumaTotal += $r->total;

if($cantidad>7){
    
    $pdf->writeHTML($items, false, false, false, false, '');

    $c=9-$cantidad;

    for($i=0;$i<$c;$i++){
        // ESPACIO MAYOR A 7 ITEMS
      $espacio = <<<EOF

          <table>
            <tr nowrap="nowrap" style="font-size:14.05px;">
              <td width="7%" ></td>
              <td width="44%"></td>
              <td width="12%"></td>
              <td width="12%"></td>
              <td width="12%"></td>
              <td width="12%"></td>
            </tr>
          </table>

      EOF;
    }

  $pdf->writeHTML($espacio, false, false, false, false, '');


  $letrasDecimal = "";
  if($sumaTotal != intval($sumaTotal)){
      $decimal = ($sumaTotal - intval($sumaTotal))*100;
      $letrasDecimal = 'con '.NumeroALetras::convertir($decimal).' centavos';
  }
  if($r->motivo_cliente=="gs"){
    $letras = NumeroALetras::convertir($sumaTotal);
    $sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
    $sumaTotal5V =  number_format($sumaTotal5, 0, "," , ".");
    $sumaTotal10V =  number_format($sumaTotal10, 0, "," , ".");
    $sumaTotalexeV =  number_format($sumaTotalexe, 0, "," , ".");
    $iva5V=number_format($iva5, 0, "," , ".");
    $iva10V=number_format($iva10, 0, "," , ".");


    $iva10U = number_format($iva10,0,",",".");
    $ivaexeV=number_format($exe, 0, "," , ".");
    $ivaTotal = number_format(($iva5 + $iva10), 0, "," , ".");
  }else{
    $letras = NumeroALetras::convertir($sumaTotal);
    $sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
    $sumaTotal5V =  number_format($sumaTotal5, 0, "," , ".");
    $sumaTotal10V =  number_format($sumaTotal10, 0, "," , ".");
    $sumaTotalexeV =  number_format($sumaTotalexe, 0, "," , ".");
    $iva5V=number_format($iva5, 0, "," , ".");
    $iva10V=number_format($iva10, 0, "," , ".");


    $iva10U = number_format($iva10,0,",",".");
    $ivaexeV=number_format($exe, 0, "," , ".");
    $ivaTotal = number_format(($iva5 + $iva10), 0, "," , ".");
  }
  //FOOTER MAS DE 9 ITEMS
  $footer = <<<EOF
    
    <table width="100%" style="text-align:center; line-height: 15px; font-size:8px">
      
      <tr>
            <td style="font-size:5px" width="65%" align="left" nowrap></td>
          </tr>
      <tr align="center">
        <td width="3%"></td>
        <td width="70%"></td>
          <td width="9%">$sumaTotalexeV</td>
          <td width="12%"><b>$sumaTotal5V</b></td>
          <td width="12%"><b>$sumaTotal10V</b></td>
        </tr>
        <tr align="center">
        <td width="5%"></td>
        <td width="65%">$tipo $letras $letrasDecimal</td>
          <td width="9%"></td>
          <td width="1%"></td>
          <td width="24%"><b>$sumaTotalV</b></td>
        </tr>
    </table>
      <table width="100%" style="text-align:left; line-height: 15px">
        <tr>
          <td width="30%" align='center' style="font-size:8px"></td>
          <td width="20%" align='center' style="font-size:8px">$iva5V</td>
          <td width="35%" align='right' style="font-size:8px">$iva10V</td>
          <td width="10%" align='right' style="font-size:8px">$ivaTotal</td>
        </tr>
    </table>
    <table width="100%" style="text-align:center; line-height: 15px">
        <tr>
          <td width="25%" align='center' style="font-size:8px"></td>
          <td width="20%" align='center' style="font-size:8px"></td>
          <td width="40%" align='center' style="font-size:8px"></td>
          <td width="10%" align='center' style="font-size:8px"></td>
        <td width="23%" align='center' style="font-size:8px"></td>
          <td width="18%" align="right"></td>
          <td width="18%" style="font-size:10px"></td>
        </tr>
    </table>
    <table width="100%" style="text-align:center; line-height: 33px">
        <tr>
          <td width="25%" align='center' style="font-size:4px"></td>
          <td width="20%" align='center' style="font-size:4px"></td>
          <td width="40%" align='center' style="font-size:4px"></td>
          <td width="10%" align='center' style="font-size:4px"></td>
        <td width="23%" align='center' style="font-size:4px"></td>
          <td width="18%" align="right"></td>
          <td width="18%" style="font-size:10px"></td>
        </tr>
    </table>
  EOF;

  $pdf->writeHTML($footer, false, false, false, false, '');

  $espacio_pre_header2 = <<<EOF

      <table>
        <tr nowrap="nowrap" style="font-size:5px;">
          <td width="7%" ></td>
          <td width="44%"></td>
          <td width="12%"></td>
          <td width="12%"></td>
          <td width="12%"></td>
          <td width="12%"></td>
        </tr>
      </table>

  EOF;


  $pdf->writeHTML($espacio_pre_header2, false, false, false, false, '');

  $espacio_pre_footer_2 = <<<EOF

        <table>
          <tr nowrap="nowrap" style="font-size:20px;">
            <td width="7%" >asdasd</td>
            <td width="44%"></td>
            <td width="12%"></td>
            <td width="12%"></td>
            <td width="12%"></td>
            <td width="12%"></td>
          </tr>
        </table>

    EOF;
  //MAS DE 8 ITEMS
  //DUPLICADO

  $pdf->writeHTML($header2, false, false, false, false, '');
  $pdf->writeHTML($items, false, false, false, false, '');
  $pdf->writeHTML($$espacio_pre_footer_2, false, false, false, false, '');
  $pdf->writeHTML($footer, false, false, false, false, '');

  //TRIPLICADO

  $pdf->writeHTML($header2, false, false, false, false, '');
  $pdf->writeHTML($items, false, false, false, false, '');
  $pdf->writeHTML($espacio, false, false, false, false, '');
  $pdf->writeHTML($footer, false, false, false, false, '');

  //$pdf->AddPage();

  $pdf->writeHTML($header3, false, false, false, false, '');

  $items = "";
  $cantidad=0;
  $sumaTotal5 = 0;
  $iva5 = 0;
  $sumaTotal10 = 0;
  $iva10 = 0;
  $cantidad_total = 0;
  $sumaTotal = 0;

}

}

if($cantidad<8){
    
$pdf->writeHTML($items, false, false, false, false, '');

$c=9-$cantidad;

for($i=0;$i<$c;$i++){
    // ESPACIO MENOR A 8 ITEMS
$espacio1 .= <<<EOF

		<table>
			<tr nowrap="nowrap" style="font-size:8px;">
				<td width="7%" ></td>
				<td width="44%"></td>
				<td width="12%"></td>
				<td width="12%"></td>
				<td width="12%"></td>
				<td width="12%"></td>
			</tr>
		</table>

EOF;
}

$pdf->writeHTML($espacio1, false, false, false, false, '');


$letrasDecimal = "";
if($sumaTotal != intval($sumaTotal)){
    $decimal = ($sumaTotal - intval($sumaTotal))*100;
    $letrasDecimal = 'con '.NumeroALetras::convertir($decimal).' centavos';
}


if($r->motivo_cliente=="gs"){
	  $letras = NumeroALetras::convertir($sumaTotal);
    $sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
    $sumaTotal5V =  number_format($sumaTotal5, 0, "," , ".");
    $sumaTotal10V =  number_format($sumaTotal10, 0, "," , ".");
    $sumaTotalexeV =  number_format($sumaTotalexe, 0, "," , ".");
    $iva5V=number_format($iva5, 0, "," , ".");
    $iva10V=number_format($iva10, 0, "," , ".");
    $ivaTotal = number_format(($iva5 + $iva10), 0, "," , ".");
    $ivaexeV=number_format($exe, 0, "," , ".");
} else{
	  $letras = NumeroALetras::convertir($sumaTotal);
    $sumaTotalV =  number_format($sumaTotal, 0, "," , ".");
    $sumaTotal5V =  number_format($sumaTotal5, 0, "," , ".");
    $sumaTotal10V =  number_format($sumaTotal10, 0, "," , ".");
    $sumaTotalexeV =  number_format($sumaTotalexe, 0, "," , ".");
    $iva5V=number_format($iva5,0,",",".");
    $iva10V=number_format($iva10,0,",",".");
    $ivaTotal = number_format(($iva5 + $iva10),0,",",".");
    $ivaexeV=number_format($exe, 0, "," , ".");
}

// FOOTER MENOS DE 9 ITEMS
$footer = <<<EOF
	
	<table width="100%" style="text-align:center; line-height: 15px; font-size:8px">
		
		<tr>
          <td style="font-size:8px" width="65%" align="left" nowrap></td>
        </tr>
		<tr align="center">
		  <td width="3%"></td>
		  <td width="70%"></td>
	      <td width="9%">$sumaTotalexeV</td>
	      <td width="12%"><b>$sumaTotal5V</b></td>
	      <td width="12%"><b>$sumaTotal10V</b></td>
	    </tr>
	    <tr align="center">
		  <td width="5%"></td>
		  <td width="65%">$tipo $letras $letrasDecimal</td>
	      <td width="9%"></td>
	      <td width="1%"></td>
	      <td width="24%"><b>$sumaTotalV</b></td>
	    </tr>
	</table>
    <table width="100%" style="text-align:left; line-height: 15px">
	    <tr>
	      <td width="30%" align='center' style="font-size:8px"></td>
	      <td width="20%" align='center' style="font-size:8px">$iva5V</td>
	      <td width="35%" align='right' style="font-size:8px">$iva10V</td>
	      <td width="10%" align='right' style="font-size:8px">$ivaTotal</td>
	    </tr>
	</table>
	<table width="100%" style="text-align:center; line-height: 15px">
	    <tr>
	      <td width="25%" align='center' style="font-size:8px"></td>
	      <td width="20%" align='center' style="font-size:8px"></td>
	      <td width="40%" align='center' style="font-size:8px"></td>
	      <td width="10%" align='center' style="font-size:8px"></td>
		  <td width="23%" align='center' style="font-size:8px"></td>
	      <td width="18%" align="right"></td>
	      <td width="18%" style="font-size:10px"></td>
	    </tr>
	</table>
	<table width="100%" style="text-align:center; line-height: 33px">
	    <tr>
	      <td width="25%" align='center' style="font-size:4px"></td>
	      <td width="20%" align='center' style="font-size:4px"></td>
	      <td width="40%" align='center' style="font-size:4px"></td>
	      <td width="10%" align='center' style="font-size:4px"></td>
		  <td width="23%" align='center' style="font-size:4px"></td>
	      <td width="18%" align="right"></td>
	      <td width="18%" style="font-size:10px"></td>
	    </tr>
	</table>
EOF;

$pdf->writeHTML($footer, false, false, false, false, '');
$espacio2 = <<<EOF

		<table>
			<tr nowrap="nowrap" style="font-size:5px;">
				<td width="100%" ></td>
			</tr>
		</table>

EOF;
$pdf->writeHTML($espacio2, false, false, false, false, '');

//DUPLICADO

$pdf->writeHTML($header2, false, false, false, false, '');
$pdf->writeHTML($items, false, false, false, false, '');
$pdf->writeHTML($espacio1, false, false, false, false, '');
$pdf->writeHTML($espacio2, false, false, false, false, '');
$pdf->writeHTML($footer, false, false, false, false, '');

//TRIPLICADO

$pdf->writeHTML($header2, false, false, false, false, '');
$pdf->writeHTML($items, false, false, false, false, '');
$pdf->writeHTML($espacio1, false, false, false, false, '');
$pdf->writeHTML($espacio2, false, false, false, false, '');
$pdf->writeHTML($footer, false, false, false, false, '');

}
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('uin.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>


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
$pdf = new TCPDF('A4', 'mm', $medidas, true, 'UTF-8', false); 
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

$pdf->AddPage();

$id = $_GET['id'];


foreach($this->model->Listar($id) as $r){
    $cliente = $r->nombre_cli;
    $ruc = $r->ruc;
    $fecha = date("d/m/Y", strtotime($r->fecha_venta));
    $telefono = $r->telefono;
    $direccion = $r->direccion;
    $vendedor = $r->vendedor;
    $contado = "";
    $credito = "";
    if($r->contado=="Contado"){
        $contado = "Contado";
    }else{
        $contado = "Credito";
    }
}


//============================================================+
// INICIO HEADER
//============================================================+
$header = <<<EOF
<table width ="100%" style="border: 1px solid #333; text-align:center; line-height: 15px; font-size:9px">
	<tr>
		<td width="65%" height="60px" style="border: 1px solid #666; vertical-align:middle; justify-content: center; align-items: center;"><img src="assets/img/DALI HOME.png" width="130px"></td>
		<td width="35%" height="40px"  style="border: 1px solid #666;line-height: 85px; font-size:10px"><b>Nota interna N°:</b> $id </td>
	</tr>
    <tr align="left">
        <td width="50%"><b>Nombre:</b>$cliente</td>
        <td width="50%"><b>Vendedor:</b> $vendedor</td>
      </tr>
      <tr align="left">
        <td width="50%"><b>RUC/CI:</b> $ruc</td>
        <td width="30%"><b>Fecha de Emisión:</b> $fecha </td>
        <td width="20%"><b> $contado </b></td>
      </tr>
      <tr align="left">
        <td width="50%"><b>Telefono:</b> $telefono </td>
        <td width="50%"><b>PC:</b> </td>
       
      </tr>
      <tr align="center"style="border: 1px solid #333; text-align:center; line-height: 15px; font-size:7px">
        <td width="16%" style="border: 1px solid #666"><b>CODIGO.</b></td>
        <td width="40%" style="border: 1px solid #666"><b>DESCRIPCION DEL ARTICULO</b></td>
        <td width="7%" style="border: 1px solid #666"><b>CANT</b></td>
        <td width="7%" style="border: 1px solid #666"><b>FARDO</b></td>
        <td width="15%" style="border: 1px solid #666"><b>PRECIO</b></td>
        <td width="15%" style="border: 1px solid #666"><b>SUBTOTAL</b></td>
      </tr>
    
    </table>
EOF;

$pdf->writeHTML($header, false, false, false, false, '');

//============================================================+
// INICIO LISTAR
//============================================================+
$sumaTotal = 0;
$cantidad = 0;
foreach($this->model->Listar($id) as $v){
$cantidad++; 
if($v->fardo!=0){
if($v->cantidad >= $v->fardo){
    $fardo=floor($v->cantidad/$v->fardo);
    $fardo =  number_format($fardo, 0, "," , ".");
    $fardo=$fardo;
    $can=$v->cantidad-($fardo*$v->fardo);
    //$can =  number_format($can, 0, "," , ".");
    
}else{
    $fardo='';
    $can=$v->cantidad;
}
}else{
    $fardo='';
    $can=$v->cantidad;
}
$porunidad =  number_format($v->precio_venta, 2, "," , ".");
$sumasubtotal +=$v->total;
$totalnota +=($v->precio_venta*$v->cantidad);
$sumadescuento +=(($v->precio_venta*$v->cantidad)*($v->descuento/100));
$porcantidad =  number_format($v->total, 2, "," , ".");

if($fardo!=''){
$items = <<<EOF
        <table width ="100%" style="border: 1px solid #333; text-align:right; line-height: 15px; font-size:7px">
        
         	<tr align="right"style="border: 1px solid #333; text-align:right; line-height: 15px; font-size:7px">
    				<td width="16%" style="border: 1px solid #666; text-align:left">$v->codigo</td>
                    <td width="40%" style="border: 1px solid #666; text-align:left">$v->producto</td>
                    <td width="7%" style="border: 1px solid #666"></td>
                    <td width="7%" style="border: 1px solid #666">$fardo</td>
                    <td width="15%" style="border: 1px solid #666">$porunidad</td>
                    <td width="15%" style="border: 1px solid #666">$porcantidad</td>
    		</tr>
    		
        </table>
EOF;
$items_fardo .= $items;
 $pdf->writeHTML($items, false, false, false, false, '');
}        
if($can>0){      

$itemss = <<<EOF
        <table width ="100%" style="border: 1px solid #333; text-align:right; line-height: 15px; font-size:7px">
        
         	<tr align="right"style="border: 1px solid #333; text-align:right; line-height: 15px; font-size:7px">
    				<td width="16%" style="border: 1px solid #666; text-align:left">$v->codigo</td>
                    <td width="40%" style="border: 1px solid #666; text-align:left">$v->producto</td>
                    <td width="7%" style="border: 1px solid #666">$can</td>
                    <td width="7%" style="border: 1px solid #666"></td>
                    <td width="15%" style="border: 1px solid #666">$porunidad</td>
                    <td width="15%" style="border: 1px solid #666">$porcantidad</td>
    		</tr>
    		
        </table>
EOF;
$items_cantidad .= $itemss;
 $pdf->writeHTML($itemss, false, false, false, false, '');
}

}

$c=(12-$cantidad);
    
    for($i=0;$i<$c;$i++){
        
$espacio .= <<<EOF
    
    		<table>
    			<tr align="center"style="border: 1px solid #333; text-align:center; line-height: 15px; font-size:7px">
    				<td width="16%" style="border: 1px solid #666"><b></b></td>
                    <td width="40%" style="border: 1px solid #666"><b></b></td>
                    <td width="7%" style="border: 1px solid #666"><b></b></td>
                    <td width="7%" style="border: 1px solid #666"><b></b></td>
                    <td width="15%" style="border: 1px solid #666"><b></b></td>
                    <td width="15%" style="border: 1px solid #666"><b></b></td>
    			</tr>
    		</table>
    
EOF;
    
    }
    $pdf->writeHTML($espacio, false, false, false, false, '');




//============================================================+
// INICIO FOOTER
//============================================================+
$deuda=$this->deuda->Obtenerdeudas($r->id_cliente, $id);
$entrega=$this->ingreso->ObtenerExtra($id);

$saldoanterior =  number_format($deuda->saldo, 2, "," , ".");
$saldorestante =$deuda->saldo + $sumasubtotal;
$saldorestante =  number_format($saldorestante-$entrega->monto, 2, "," , ".");
$sumasubtotal =  number_format($sumasubtotal-$entrega->monto, 2, "," , ".");
$sumadescuento =  number_format($sumadescuento, 2, "," , ".");
$totalnota =  number_format($totalnota, 2, "," , ".");
$entregaventa = number_format($entrega->monto, 2, "," , ".");
$footer = <<<EOF
<table width ="100%" style="border: 1px solid #333; text-align:left; line-height: 15px; font-size:9px">
    <tr align="center">
    	<td width="16%" style="border: 1px solid #666"><b></b></td>
        <td width="40%" style="border: 1px solid #666"><b></b></td>
        <td width="7%" style="border: 1px solid #666"><b></b></td>
        <td width="7%" style="border: 1px solid #666"><b></b></td>
        <td width="15%" style="border: 1px solid #666"><b></b></td>
        <td width="15%" style="border: 1px solid #666"><b>$totalnota</b></td>
	</tr>
    
    <tr>
		 <td width="25%"><b>Entrega:</b>$entregaventa</td>
		 <td width="35%"><b>Descuento:</b>$sumadescuento</td>
		 <td width="25%" style="text-align:right"><b>Total Nota:</b></td>
		 <td width="15%" style="text-align:right">$sumasubtotal</td>
	</tr> 
	 <tr>
		 <td width="40%"><b>Moneda:</b>Dolar</td>
		 <td width="45%" style="text-align:right"><b>Saldo Anterior:</b> </td>
		 <td width="15%" style="text-align:right">$saldoanterior</td>
	</tr> 
	<tr>
		 <td width="40%"></td>
		 <td width="45%" style="text-align:right"><b>Saldo Restante:</b> </td>
		 <td width="15%" style="text-align:right">$saldorestante</td>
	</tr> 
    
    
       
    </table>
EOF;

$pdf->writeHTML($footer, false, false, false, false, '');
$espacio1 = <<<EOF
    
    		<table>
    			<tr align="center"style=" font-size:23px">
    				<td width="16%" ><b></b></td>
                    <td width="40%" ><b></b></td>
                    <td width="7%" ><b></b></td>
                    <td width="7%" ><b></b></td>
                    <td width="15%" ><b></b></td>
                    <td width="15%" ><b></b></td>
    			</tr>
    		</table>
    
EOF;
$pdf->writeHTML($espacio1, false, false, false, false, '');
$pdf->writeHTML($header, false, false, false, false, '');
$pdf->writeHTML($items_fardo, false, false, false, false, '');
$pdf->writeHTML($items_cantidad, false, false, false, false, '');
$pdf->writeHTML($espacio, false, false, false, false, '');
$pdf->writeHTML($footer, false, false, false, false, '');

ob_end_clean();
$pdf->Output('uin.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+
  ?>
.php at master · Valeria55/dalihome
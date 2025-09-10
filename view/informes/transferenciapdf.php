<?php

require_once('plugins/tcpdf2/tcpdf.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetFont('freeserif', '', 10);
$pdf->AddPage('P', 'A4');



$mes = (!isset($_GET["mes"])) ? date("Y-m-d") : $_GET["mes"] . "-01";
$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$id_transferencia = $_GET['id'];
$id = $this->model->Listartransferencia_producto($id_transferencia);
$html1 = <<<EOF

    <table width="100%" style="font-size:12px;">
        <tr>
          <td width="30%" align="left"></td>  
        </tr>
       <tr>
          <td width="30%" align="left"><img src="assets/img/SCORECARLOGO.png" width="200"></td>  
          <td width="50%" align="left" style="font-size:15px;line-height: 95px;"><b>TRANSFERENCIA DE PRODUCTOS</b></td> 
          <td width="10%" align="left" style="font-size:15px; line-height: 95px;"><b>N°:</b> $id_transferencia</td>  
        </tr>
    </table>


EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

foreach ($this->model->Listartransferencia_producto($id_transferencia) as $r) {
    $cliente = $r->user;
    $fecha = date("d/m/Y", strtotime($r->fecha_transferencia_producto));
}

$html1 = <<<EOF

        
        <table width"100%" style=" font-size:12px; ">
             <tr >
                <td width="30%" align="left" style="font-size:12px"><b>Fecha</b></td>  
                <td width="5%" align="center" style=" font-size:12px"><b>:</b></td>  
                <td width="55%" align="left" style=" font-size:12px">$fecha</td> 
                
                
            </tr>
            
            <tr align="Left">
                <td width="30%" style="font-size:12px"><b>Transferencia realizada por</b></td>
                <td width="5%" align="center" style="font-size:12px"><b>:</b></td>  
                <td width="55%" align="left" style=" font-size:12px">$cliente</td>
                
            </tr>
           
        </table>
<br>
EOF;
$pdf->writeHTML($html1, false, false, false, false, '');

//$arr = $r->motivo;

//$detalle = str_replace("\n","<br>",$arr);

$html1 = <<<EOF
<br><br>
        <table width"100%" style=" font-size:10px">
           <tr align="center">
                 <td width="100%" ><b>DETALLE DE LA TRANSFERENCIA</b></td> 
           </tr>
           <tr align="Left"> 
                <td width="100%" align="left" style="font-size:2px"></td>
            </tr>
            
            
        </table>


EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF
<br><br>
        <table width"100%" style="border: 1px solid #333; font-size:9px">
           <tr align="center">
                 <td width="15%" style="border: 1px solid #333;"><b>CODIGO</b></td>
                  <td width="30%" style="border: 1px solid #333;"><b>PRODUCTOS</b></td> 
                  <td width="15%" style="border: 1px solid #333;"><b>CANTIDAD</b></td>
                  <td width="20%" style="border: 1px solid #333;"><b>PRECIO</b></td>
                  <td width="20%" style="border: 1px solid #333;"><b>SUB_TOTAL</b></td>
            </tr>
             
            
        </table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');
$sumaTotal = 0;
$id_transferencia = $_GET['id'];
foreach ($this->model->ListarDetalle($id_transferencia) as $r) {
    $unidad = number_format(($r->precio_venta), 0, ",", ".");
    $precio = $r->precio_venta * $r->cantidad;
    $precio1 = number_format(($precio), 0, ",", ".");
    $html1 = <<<EOF

        <table width"100%" style=" font-size:10px">
           <tr >
                 <td width="15%" align="left" style=" font-size:9px">$r->codigo</td>
                  <td width="39%" style=" font-size:7px">$r->producto</td> 
                  <td width="4%" align="center">$r->cantidad</td>
                  <td width="20%" align="rigth" >$unidad</td>
                  <td width="20%" align="rigth">$precio1</td>
            </tr>
             
            
        </table>

EOF;

    $pdf->writeHTML($html1, false, false, false, false, '');
    $sumaTotal += $precio;
    $total = number_format(($sumaTotal), 0, ",", ".");
}
$html1 = <<<EOF

        <table width"100%" style="border: 1px solid #333; font-size:11px">
           <tr align="rigth">
                 <td width="75%" ><b>TOTAL</b></td>
                  <td width="25%" ><b>$total</b></td> 
                 
            </tr>
             
           
        </table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$html1 = <<<EOF

         <br><br> <br><br>
        <table width"100%" style=" font-size:12px; ">
             <tr >
                <td width="50%" align="center" style="font-size:12px"><b>----------------------------</b></td>  
                <td width="50%" align="center" style="font-size:12px"><b>----------------------------</b></td> 
            </tr>
            
            <tr align="Left">
                <td width="15%" style="font-size:12px"><b>Emisor</b></td>
                <td width="35%" align="left" style="font-size:12px"><b>:</b></td>
                <td width="15%" style="font-size:12px"><b>Receptor</b></td>
                <td width="35%" align="left" style="font-size:12px"><b>:</b></td>
            </tr>
           
        </table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, '');

$pdf->Output('Transferencia.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+

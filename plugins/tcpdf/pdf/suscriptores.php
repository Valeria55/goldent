<?php



require_once('tcpdf_include.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->AddPage();


		if(isset($_GET['id'])){
	    	$id = $_GET['id'];    
			$stmt_edit = $DB_con->prepare('SELECT * FROM pedidos WHERE id =:uid');
			$stmt_edit->execute(array(':uid'=>$id));
			$row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
			extract($row);    
	    }
	


$html1 = <<<EOF
	
	
	<table width="100%" border="1">
		<tr>
			<td align="center">Código: <?php echo $_GET['id']; ?></td>
			<td align="center">Equipo: <?php echo $equipo ?></td>
			<td align="center">Entrega:<?php echo date("m-d-Y", strtotime("$fecha_entrega")) ?></td>
		</tr>
		<tr>
			<td colspan="3"><img src="<?php echo $img; ?>" class="img-rounded" width="100%" /></td>
		</tr>
	</table>

EOF;

$pdf->writeHTML($html1, false, false, false, false, ''); 






$pdf->Output('suscriptores.pdf');



?>
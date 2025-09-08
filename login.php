<?php

	require_once 'model/dbconfig.php';
    session_start();
    session_destroy();
    setcookie();
	if(isset($_POST['user'])){

		$stmt = $DB_con->prepare('SELECT * FROM usuario WHERE user = :user AND pass = :pass');
		$stmt->bindParam(':user',$_POST['user']);
		$stmt->bindParam(':pass',$_POST['pass']);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			session_start();
			$row=$stmt->fetch(PDO::FETCH_ASSOC);
			extract($row);
			$_SESSION["validar"]=true;
			$_SESSION["user_id"] = $id;
			$_SESSION["username"] = $user;
			$_SESSION["nivel"] = $nivel;
			$_SESSION["sucursal"] = $sucursal;
			$caja = $DB_con->prepare("SELECT * FROM cajas WHERE id_usuario = '$id'");
			$caja->execute();
			$row=$caja->fetch(PDO::FETCH_ASSOC);
			extract($row);
			$_SESSION["id_caja"] = $id;
			header("Location: index.php?c=presupuesto");
	    } else {
	    	header("Location: login.php?error=1");
	    	session_destroy();
	    }
	    
	}

	if(isset($_GET['logout'])){
		
	    session_start();
	    session_destroy();
	    $_SESSION["validar"]=false;
	    header("Location: login.php");
	    
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Ingresar</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/admin/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/admin/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/admin/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/admin/css/util.css">
	<link rel="stylesheet" type="text/css" href="assets/admin/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100" style="background-color:#000;">
			<div class="wrap-login100 p-t-30 p-b-50">
				<span class="login100-form-title p-b-41">
					Ingreso al sistema
				</span>
				<?php  if(isset($_GET["error"])){ ?>
		 	    <div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong> Error!</strong>  Usuario o contraseña incorrectos </div>
                <?php	} ?>
				<form class="login100-form validate-form p-b-33 p-t-5" method="post">
				    <br>
				    <div align="center">
                        <img src="assets/img/logo.png" width="200">
				    </div>
					<div class="wrap-input100 validate-input" data-validate = "Debe ingresar usuario">
						<input class="input100" type="text" name="user" placeholder="Usuario">
						<span class="focus-input100" data-placeholder="&#xe82a;"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Debe ingresar Contraseña">
						<input class="input100" type="password" name="pass" placeholder="Contraseña">
						<span class="focus-input100" data-placeholder="&#xe80f;"></span>
					</div>

					<div class="container-login100-form-btn m-t-32">
						<button class="login100-form-btn">
							Ingresar
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
	
<!--===============================================================================================-->
	<script src="assets/admin/js/jquery.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="assets/admin/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>
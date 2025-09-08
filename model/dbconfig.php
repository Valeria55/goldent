<?php

	$DB_HOST = 'localhost';
	$DB_USER = 'u832567584_golden';
	$DB_PASS = 'x=3Tm2&p';
	$DB_NAME = 'u832567584_golden';


	$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	
	try{
		$DB_con = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME}",$DB_USER,$DB_PASS);
		$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e){
		echo $e->getMessage();
	}

	
	


	
	

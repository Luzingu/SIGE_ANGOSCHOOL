<?php 
	$classe = isset($_GET["classe"])?$_GET["classe"]:0;

	if($classe<=6){
		include_once 'ensinoPrimario.php';
	}else if($classe<=9){
		include_once 'primeiroCiclo.php';
	}else{
		include_once 'segundoCiclo.php';
	}
 ?>
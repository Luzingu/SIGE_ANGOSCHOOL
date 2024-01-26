<?php 
	session_start();
	unset($_SESSION['posColeccao']);
    unset($_SESSION['coleccoes']);

    $afonsoluzingu = isset($_GET["afonsoluzingu"])?$_GET["afonsoluzingu"]:"";
    echo "<script>window.open('solicitBackup.php?afonsoluzingu=".$afonsoluzingu."', '_self')</script>";

 ?>
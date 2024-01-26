<?php 
	session_start();
    unset($_SESSION['posColeccao']);
    unset($_SESSION['coleccoes']);
    echo "<script>window.open('http://localhost/angoschool/areaEscolas/areaDirector/backupDados/backup.php', '_self')</script>";

 ?>
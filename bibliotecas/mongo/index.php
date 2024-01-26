<?php 
	
	require "vendor/autoload.php";

	$client = new MongoDB\Client;
	$db = $client->escola;

	include '../angoschool/manipulacaoDadosMae.php';

	$manipulacao = new manipulacaoDadosMae(__DIR__);

	include '../angoschoolMysql/manipulacaoDadosMae.php';

	$m = new manipulacaoDadosMae1(__DIR__);
	

	

    



 ?>
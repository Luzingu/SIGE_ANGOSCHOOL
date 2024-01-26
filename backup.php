 <?php 
 	include_once 'manipulacaoDadosMae.php';
	$m = new manipulacaoDadosMae();

	//$serv=["conOrigem"=>"mongodb://abigael:Renapol1..abigael@194.233.69.35:27019", "dbOrigem"=>"escola", "conDestino"=>"mongodb://localhost:27017", "dbDestino"=>"luzingu"];

	$serv=["conOrigem"=>"mongodb://abigael:Renapol1..abigael@194.233.69.35:27019", "dbOrigem"=>"escola", "conDestino"=>"mongodb://luzl:Renapol1..luzl@135.181.38.23:27019", "dbDestino"=>"escola"];

	$dataBackup = isset($_GET["data"])?$_GET["data"]:$m->dataSistema;
	$posicao = isset($_GET["posicao"])?$_GET["posicao"]:0;
	$dbOrigem = $serv["dbOrigem"];
	$dbDestino = $serv["dbDestino"];

	$m->conexaoDb = new MongoDB\Client($serv["conOrigem"]);
	$m->db = $m->conexaoDb->$dbOrigem;

	$joaquim = $m->db->listCollectionNames();
	$coleccoes=array();
	foreach($joaquim as $punhetero){
		$coleccoes[]=$punhetero;
	}

	$arrayDados = $m->selectArray($coleccoes[$posicao], [], ["update"=>array('$gte'=>$dataBackup), "dataBackup"=>array('$ne'=>$dataBackup)]);

	foreach($arrayDados as $dado){
		$erro="nao";
		$idPrincipal="";
		$camposPrincipais="";
		$valoresPrincipais=array();
		$i=0;

		foreach(retornarChaves($dado) as $chave){
			if(isset($dado[$chave]) && !is_object($dado[$chave]) && $chave!="_id"){
				$i++;
				if($i==1){
					$idPrincipal=$chave;
				}else if($chave!="update" && $chave!="dataBackup" && $chave!="timeUpdate" && $chave!="userUpdate"){

					if((!isset($dado["dataBackup"]) || (isset($dado["dataBackup"]) && $dado["dataBackup"]!=$dataBackup)) && isset($dado[$chave])){
						if($camposPrincipais!=""){
							$camposPrincipais.=",";
						}
						$camposPrincipais.=$chave;
						$valoresPrincipais[]=$dado[$chave];
					}
				}
			}
		}
		if($coleccoes[$posicao]=="entidadesprimaria"){
			$idPrincipal="idPEntidade";
		}

		if($camposPrincipais!=""){
			$m->conexaoDb = new MongoDB\Client($serv["conDestino"]);
			$m->db = $m->conexaoDb->$dbDestino;
			if(count($m->selectArray($coleccoes[$posicao], [$idPrincipal], [$idPrincipal=>$dado[$idPrincipal]]))<=0){

				if(is_numeric($dado[$idPrincipal])){
					$m->inserir($coleccoes[$posicao], $idPrincipal, $camposPrincipais, $valoresPrincipais, "sim", "nao", array(), ($dado[$idPrincipal]-1));
				}else{
					$erro="sim";
				}
			}else{
				$m->editar($coleccoes[$posicao], $camposPrincipais, $valoresPrincipais, [$idPrincipal=>$dado[$idPrincipal]]);
			}
		}
		$m->conexaoDb = new MongoDB\Client($serv["conOrigem"]);
		$m->db = $m->conexaoDb->$dbOrigem;
		$m->editar($coleccoes[$posicao], "dataBackup", [$dataBackup], [$idPrincipal=>$dado[$idPrincipal]]);
		$idPrincipal="idPMatricula";
		
		if($erro=="nao"){
			foreach(retornarObjectos($dado) as $chave){
				
				foreach($dado[$chave] as $subDado){
					$n=0;		
					$camposSubs="";
					$valoresSubs=array();
					$idSub="";

					if(isset($subDado["update"]) && $subDado["update"]>=$dataBackup){
						foreach(retornarChaves($subDado) as $subChave){
							$n++;
							if($n==1){
								$idSub=$subChave;
							}else if($subChave!="update" && $subChave!="dataBackup" && $subChave!="timeUpdate" && $subChave!="userUpdate"){

								if($camposSubs!=""){
									$camposSubs.=",";
								}
								$camposSubs.=$subChave;
								$valoresSubs[]=$subDado[$subChave];
							}
						}
						if($camposSubs!=""){

							$m->conexaoDb = new MongoDB\Client($serv["conDestino"]);
							$m->db = $m->conexaoDb->$dbDestino;
							if(count($m->selectArray($coleccoes[$posicao], [$idPrincipal], [$idPrincipal=>$dado[$idPrincipal], $chave.".".$idSub=>$subDado[$idSub]], [], 1))<=0){
								
								$m->inserirObjecto($coleccoes[$posicao], $chave, $idSub, $camposSubs, $valoresSubs, [$idPrincipal=>$dado[$idPrincipal]], "sim", "nao", $subDado[$idSub]);
							}else{
								$m->editarItemObjecto($coleccoes[$posicao], $chave, $camposSubs, $valoresSubs, [$idPrincipal=>$dado[$idPrincipal]], [$idSub=>$subDado[$idSub]]);
							}
						}
					}
				}
			}
		}
	}
	
	if(isset($coleccoes[($posicao+1)])){
		echo "<script>window.location='?posicao=".($posicao+1)."&data=".$dataBackup."'</script>";
	}
  ?>
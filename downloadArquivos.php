<?php 
	if(isset($_POST['btnSubmit'])){
		include_once 'manipulacaoDadosMae.php';
		$m = new manipulacaoDadosMae();

		$idUsuarioLogado = isset($_POST["idUsuarioLogado"])?$_POST["idUsuarioLogado"]:"";
		$idEscolaLogada = isset($_POST["idEscolaLogada"])?$_POST["idEscolaLogada"]:"";
		$dataInicial = isset($_POST["dataInicial"])?$_POST["dataInicial"]:"";
		$dataFinal = isset($_POST["dataFinal"])?$_POST["dataFinal"]:"";

		if(count($m->selectArray("entidadesprimaria", ["idPEntidade"], ["idPEntidade"=>$idUsuarioLogado, "escola.idEntidadeEscola"=>$idEscolaLogada, "escola.BACKUP"=>"V"], ["escola"]))>0){

			echo "<h3>1-Clique para Baixar as fotos modificadas nos últimos dias<br>Após isso, vá até à pasta de Downloads ou Transferencias, copie os arquivos no directório C:/xampp/htdocs/AngoSchoolOffline/fotoUsuarios</h3>";
			$directory = 'fotoUsuarios/';

			if (is_dir($directory)) {
			    $files = scandir($directory);

			    foreach ($files as $file) {
			        if ($file !== '.' && $file !== '..' && is_file($directory . $file)) {

            			$ultimaDataDeModificacao = date('Y-m-d', filemtime($directory.$file));


            			if($ultimaDataDeModificacao>=$dataInicial && $ultimaDataDeModificacao<=$dataFinal){

            				$array = $m->selectArray("entidadesprimaria", ["nomeEntidade"], ["escola.idEntidadeEscola"=>$idEscolaLogada, "fotoEntidade"=>$file], ["escola"]);

            				$nomeUsuario = valorArray($array, "nomeEntidade");

            				if($nomeUsuario=="" || $nomeUsuario==NULL){
            					$array = $m->selectArray("alunosmatriculados", ["nomeAluno"], ["escola.idMatEscola"=>$idEscolaLogada, "fotoAluno"=>$file], ["escola"]);
            					$nomeUsuario = valorArray($array, "nomeAluno");	
            				}
            				if($nomeUsuario!="" && $nomeUsuario!=NULL){
            					echo "<a href='$directory$file' download>".$nomeUsuario."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            				}            				
            			}			            
			        }
			    }
			} else {
			    echo "O diretório não existe.";
			}



		}

	}


 ?>

 <style type="text/css">
 	a{
 		background-color: darkblue;
 		text-decoration: none;
 		color: white;
 		border-radius: 10px;
 		padding: 10px;
 	}
 </style>
<?php 
	
	function inicializadorDaFuncaoGestaInscricao($manipulacaoDados){

		echo "<script>var cursosDaEscola=".json_encode($manipulacaoDados->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]))."</script>";

		echo "<script>var professoresDaEscola=".json_encode($manipulacaoDados->entidades())."</script>";
	}
?>

<script type="text/javascript">
	function nomeDoCurso(idPNomeCurso){
		nomeCurso="";
		cursosDaEscola.forEach(function(dado){
			if(dado.idPNomeCurso==idPNomeCurso){
				nomeCurso = dado.nomeCurso;
			}
		})
		return nomeCurso;
	}

	function abrevNomeDoCurso(idPNomeCurso){
		abrevCurso="";
		cursosDaEscola.forEach(function(dado){
			if(dado.idPNomeCurso==idPNomeCurso){
				abrevCurso = dado.abrevCurso;
			}
		})
		return abrevCurso;
	}

	function nomeDaEntidade(idPEntidade){
		nomeEntidade="";
		professoresDaEscola.forEach(function(dado){
			if(dado.idPEntidade==idPEntidade){
				nomeEntidade = dado.nomeEntidade;
			}
		})
		return nomeEntidade;
	}

</script>
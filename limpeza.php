<?php 
	session_start();
	if(!isset($_SESSION['idInstituicaoEntrar'])){
		echo "<script>window.location='http://localhost/login/entrar/index.php'</script>";
	}

	include_once 'manipulacaoDadosMae.php';
	$m = new manipulacaoDadosMae();

	$m->actualizacaoDados="on";
	foreach($m->selectArray("agrup_alunos", ["grupo"]) as $a){

		foreach($m->selectArray("alunos_".$a["grupo"], ["idPMatricula", "escola.idMatEscola"]) as $aluno){

			if(count(listarItensObjecto($aluno, "escola", ["idMatEscola=".$_SESSION['idInstituicaoEntrar']]))<=0){
				$m->excluir("alunos_".$a["grupo"], ["idPMatricula"=>$aluno["idPMatricula"]]);
			}
		}
	}

	foreach($m->selectArray("comissAvalDesempPessoalNaoDocente", ["idEscola", "idComAvalPessoal"]) as $dado){
		if($dado["idEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("comissAvalDesempPessoalNaoDocente", ["idComAvalPessoal"=>$dado["idComAvalPessoal"]]);
		}
	}

	foreach($m->selectArray("comissAvalDesempProfessor", ["idEscola", "idComAvalProf"]) as $dado){
		if($dado["idEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("comissAvalDesempProfessor", ["idComAvalProf"=>$dado["idComAvalProf"]]);
		}
	}

	foreach($m->selectArray("comunicados", ["idPEscola", "idPComunicado"]) as $dado){
		if($dado["idPEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("comunicados", ["idPComunicado"=>$dado["idPComunicado"]]);
		}
	}

	foreach($m->selectArray("definicoesConselhoNotas", ["idPEscola", "id"]) as $dado){
		if($dado["idPEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("definicoesConselhoNotas", ["id"=>$dado["id"]]);
		}
	}

	foreach($m->selectArray("divisaoprofessores", ["idPEscola", "idPDivisao"]) as $dado){
		if($dado["idPEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("divisaoprofessores", ["idPDivisao"=>$dado["idPDivisao"]]);
		}
	}

	foreach($m->selectArray("entidadesprimaria", ["idPEntidade", "escola.idEntidadeEscola"]) as $aluno){

		if(count(listarItensObjecto($aluno, "escola", ["idEntidadeEscola=".$_SESSION['idInstituicaoEntrar']]))<=0){
			$m->excluir("entidadesprimaria", ["idPEntidade"=>$aluno["idPEntidade"]]);
		}
	}

	foreach($m->selectArray("facturas", ["idFacturaEscola", "idPFactura"]) as $dado){
		if($dado["idFacturaEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("facturas", ["idPFactura"=>$dado["idPFactura"]]);
		}
	}

	foreach($m->selectArray("horario", ["idPEscola", "idPHorario"]) as $dado){
		if($dado["idPEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("horario", ["idPHorario"=>$dado["idPHorario"]]);
		}
	}

	foreach($m->selectArray("listaturmas", ["idPEscola", "idPListaTurma"]) as $dado){
		if($dado["idPEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("listaturmas", ["idPListaTurma"=>$dado["idPListaTurma"]]);
		}
	}

	foreach($m->selectArray("nomecursos", ["idPNomeCurso", "cursos.idCursoEscola"]) as $aluno){

		if(count(listarItensObjecto($aluno, "cursos", ["idCursoEscola=".$_SESSION['idInstituicaoEntrar']]))<=0){
			$m->excluir("nomecursos", ["idPNomeCurso"=>$aluno["idPNomeCurso"]]);
		}
	}

	foreach($m->selectArray("nomedisciplinas", ["idPNomeDisciplina", "disciplinas.idDiscEscola"]) as $aluno){

		if(count(listarItensObjecto($aluno, "disciplinas", ["idDiscEscola=".$_SESSION['idInstituicaoEntrar']]))<=0){
			$m->excluir("nomedisciplinas", ["idPNomeDisciplina"=>$aluno["idPNomeDisciplina"]]);
		}
	}

	foreach($m->selectArray("pagamentos_matricula_inscricao", ["idPagEscola", "idPPagamento"]) as $dado){
		if($dado["idPagEscola"]!=$_SESSION['idInstituicaoEntrar']){
			$m->excluir("pagamentos_matricula_inscricao", ["idPPagamento"=>$dado["idPPagamento"]]);
		}
	}


?>
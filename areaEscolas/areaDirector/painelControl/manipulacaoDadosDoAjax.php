<?php
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	function __construct($caminhoAbsoluto){
    		parent::__construct();

            if($this->accao=="manipularEstadoPeriodico"){
                if($this->verificacaoAcesso->verificarAcesso("", ["painelControl"])){
                    $this->manipularEstadoPeriodico();
                }
            }else if($this->accao=="manipularDireccao"){
            	$this->manipularDireccao();
            }else if($this->accao=="novoAnoLectivo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["painelControl"])){
                    $this->novoAnoLectivo();
                }
            }else if($this->accao=="transitarClasseAluno"){

               if($this->verificacaoAcesso->verificarAcesso("", ["painelControl"])){
                    $this->transitarClasseAluno();
                }
            }

    	}

    private function manipularEstadoPeriodico(){
        $estadoAlterar = $_GET["estadoAlterar"];

		$estado = valorArray(listarItensObjecto($this->sobreEscolaLogada, "estadoperiodico", ["objecto=".$estadoAlterar]), "estado");
		if($estado=="F"){
			$estado="V";
		}else{
			$estado="F";
		}
		$this->editarItemObjecto("escolas", "estadoperiodico", "estado", [$estado], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["objecto"=>$estadoAlterar]);
        echo json_encode($this->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["estadoperiodico"]));
    }



    private function novoAnoLectivo(){

        $idNovoAno = $this->selectUmElemento("anolectivo", "idPAno", ["estado"=>"V"]);

        if($idNovoAno==$this->idAnoActual){
            echo "FNão podes adicionar um novo ano lectivo, contacte os administradores do sistema.";
        }else{

            $this->editarItemObjecto("anolectivo", "anos_lectivos", "estadoAnoL", ["F"], ["idPAno"=>$this->idAnoActual], ["idAnoEscola"=>$_SESSION["idEscolaLogada"]]);

            if($this->inserirObjecto("anolectivo", "anos_lectivos", "idPAnoE", "idAnoEscola, idFAno, estadoAnoL", [$_SESSION["idEscolaLogada"], $idNovoAno, "V"], ["idPAno"=>$idNovoAno])=="sim"){

								foreach ($this->selectArray("agrup_alunos") as $grupo) {
									$this->editarItemObjecto("alunos_".$grupo["grupo"], "cadeiras_atraso", "estadoCadeira", ["V"], [], ["idCadEscola"=>$_SESSION['idEscolaLogada'], "cf"=>array('$gte'=>10)]);
								}
                echo "VO novo ano lectivo foi adicionado com sucesso.";
            }else{
                echo "FNão foi possível adicionar um novo ano lectivo.";
            }
        }
    }

    private function transitarClasseAluno(){
        $this->classeTrans = $_GET["classeTrans"];
        $this->idPCursoTrans = $_GET["idPCursoTrans"];

        $this->sobreCurso = $this->selectArray("nomecursos", ["cursos.modoPenalizacao", "cursos.paraDiscComNegativas", "ultimaClasse", "classes.identificador", "classes.notaMedia", "classes.ordem"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$this->idPCursoTrans], ["cursos"], 1);

        $this->ultimaClasse = valorArray($this->sobreCurso, "ultimaClasse");
        $this->notaMedia = valorArray(listarItensObjecto($this->sobreCurso, "classes", ["identificador=".$this->classeTrans]), "notaMedia");
        $this->classeSeguinte=$this->classeSeguinte($this->idPCursoTrans, $this->classeTrans);

        if(count(listarItensObjecto($this->sobreEscolaLogada, "trans_classes", ["idTransClAno=".$this->idAnoActual, "classeTrans=".$this->classeTrans, "idTransClCurso=".$this->idPCursoTrans]))<=0){

            $this->inserirObjecto("escolas", "trans_classes", "idPTransClasse", "idTransClAno, classeTrans, idTransClEscola, idTransClCurso", [$this->idAnoActual, $this->classeTrans, $_SESSION["idEscolaLogada"], $this->idPCursoTrans], ["idPEscola"=>$_SESSION['idEscolaLogada']]);

            $this->avancarClassesAnoLectivo();
        }

    }

    private function avancarClassesAnoLectivo(){

         $this->idAnoAnterior = $this->selectUmElemento("anolectivo", "idPAno", ["anos_lectivos.idAnoEscola"=>$_SESSION['idEscolaLogada'], "estado"=>"F"], ["anos_lectivos"], [], ["idPAno"=>-1]);


        foreach ($this->selectArray("alunosmatriculados", ["idPMatricula", "reconfirmacoes.observacaoF", "grupo", "escola.idCursos"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoAnterior, "reconfirmacoes.classeReconfirmacao"=>$this->classeTrans, "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.trasincaoAnual"=>['$in'=>array(null, "I")], "reconfirmacoes.idMatCurso"=>$this->idPCursoTrans], ["reconfirmacoes", "escola"]) as $aluno) {

            if($aluno["reconfirmacoes"]["observacaoF"]=="A" || $aluno["reconfirmacoes"]["observacaoF"]=="TR"){

                $this->aprovarAlunos($aluno["idPMatricula"],  $aluno["grupo"], valorArray($aluno, "idCursos", "escola"));
            }else{
                $this->reprovarAlunos($aluno["idPMatricula"],  $aluno["grupo"]);
            }
            $this->editarItemObjecto("alunos_".$aluno["grupo"], "escola", "trasincaoAnual", ["A"], ["idPMatricula"=>$aluno["idPMatricula"]],["idMatEscola"=>$_SESSION['idEscolaLogada']]);
        }
    }

    public function aprovarAlunos($idPMatricula, $grupo, $idCursos){

            if($this->classeTrans==$this->ultimaClasse){
                $idMatFAno=$this->idAnoAnterior;
                $estadoAluno="Y";
                $classeSeguinte=120;
            }else{
                $idMatFAno="";
                $estadoAluno="A";
                $classeSeguinte=$this->classeSeguinte;

                if(valorArray($this->sobreCurso, "paraDiscComNegativas", "cursos")=="cadeira"){

                    foreach ($this->selectArray("alunos_".$grupo, ["pautas.idPPauta", "pautas.idPautaDisciplina"], ["idPMatricula"=>$idPMatricula, "pautas.classePauta"=>$this->classeTrans, "pautas.idPautaCurso"=>$this->idPCursoTrans, "pautas.mf"=>array('$lt'=>$this->notaMedia)], ["pautas"]) as $disciplinas) {

                        $this->editarItemObjecto("alunos_".$grupo, "pautas", "obs", ["cad"], ["idPMatricula"=>$idPMatricula], ["idPPauta"=>$disciplinas["pautas"]["idPPauta"]]);
                        $this->inserirObjecto("alunos_".$grupo, "cadeiras_atraso", "idPCadeirantes", "idCadMatricula, idCadDisciplina, idCadCurso, classeCadeira, idCadAno, estadoCadeira, idCadEscola", [$idPMatricula, $disciplinas["pautas"]["idPautaDisciplina"], $this->idPCursoTrans, $this->classeTrans, $this->idAnoAnterior, "F", $_SESSION["idEscolaLogada"]], ["idPMatricula"=>$idPMatricula]);
                    }
                }
            }
            $arrayIds=array();
            $i=0;
            foreach($idCursos as $id){
                $arrayIds[$i]=$id;
                if($id["idMatCurso"]==$this->idPCursoTrans){
                    $arrayIds[$i]["idMatFAno"]=$idMatFAno;
                    $arrayIds[$i]["estadoAluno"]=$estadoAluno;
                    $arrayIds[$i]["classeActualAluno"]=$classeSeguinte;
                }
                $i++;
            }

            $this->editarItemObjecto("alunos_".$grupo, "escola", "idMatFAno, estadoAluno, classeActualAluno, idCursos", [$idMatFAno, $estadoAluno, $classeSeguinte, $arrayIds], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);
        }

        public function reprovarAlunos($idPMatricula, $grupo){

            $arrayPautasEliminar=array();
            if(valorArray($this->sobreCurso, "modoPenalizacao", "cursos")=="apenasNegativas"){

                $this->editarItemObjecto("alunos_".$grupo, "pautas", "obs", ["melh"], ["idPMatricula"=>$idPMatricula], ["classePauta"=>$this->classeTrans, "idPautaCurso"=>$this->idPCursoTrans, "mf"=>array('$gte'=>$this->notaMedia)]);

                $arrayPautasEliminar = $this->selectArray("alunosmatriculados", ["pautas.idPautaMatricula", "pautas.idPautaDisciplina", "pautas.idPautaEscola", "pautas.obs", "pautas.seFoiAoRecurso", "pautas.chavePauta", "pautas.idPautaAno", "pautas.classePauta", "pautas.idPautaCurso", "pautas.semestrePauta"], ["idPMatricula"=>$idPMatricula, "pautas.classePauta"=>$this->classeTrans, "pautas.idPautaCurso"=>$this->idPCursoTrans, "pautas.mf"=>array('$lt'=>$this->notaMedia)], ["pautas"]);

                $this->excluirItemObjecto("alunos_".$grupo, "pautas", ["idPMatricula"=>$idPMatricula], ["classePauta"=>$this->classeTrans, "idPautaCurso"=>$this->idPCursoTrans, "mf"=>array('$lt'=>$this->notaMedia)]);
            }else{
                $arrayPautasEliminar = $this->selectArray("alunosmatriculados", ["pautas.idPautaMatricula", "pautas.idPautaDisciplina", "pautas.idPautaEscola", "pautas.obs", "pautas.seFoiAoRecurso", "pautas.chavePauta", "pautas.idPautaAno", "pautas.classePauta", "pautas.idPautaCurso", "pautas.semestrePauta"], ["idPMatricula"=>$idPMatricula, "pautas.classePauta"=>$this->classeTrans, "pautas.idPautaCurso"=>$this->idPCursoTrans], ["pautas"]);

                $this->excluirItemObjecto("alunos_".$grupo, "pautas", ["idPMatricula"=>$idPMatricula], ["classePauta"=>$this->classeTrans, "idPautaCurso"=>$this->idPCursoTrans]);
            }

            //Acrescentar disciplina virgem que foram elimindadas acima...
            foreach($arrayPautasEliminar as $a){
                $this->inserirObjecto("alunos_".$grupo, "pautas", "idPPauta", "idPautaMatricula, idPautaDisciplina, idPautaEscola, obs, seFoiAoRecurso, chavePauta, idPautaAno, classePauta, idPautaCurso, semestrePauta", [$idPMatricula, $a["pautas"]["idPautaDisciplina"], $a["pautas"]["idPautaEscola"], $a["pautas"]["obs"], $a["pautas"]["seFoiAoRecurso"], $a["pautas"]["chavePauta"], $a["pautas"]["idPautaAno"], $a["pautas"]["classePauta"], $a["pautas"]["idPautaCurso"], $a["pautas"]["semestrePauta"]], ["idPMatricula"=>$idPMatricula]);
            }
        }

}
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>

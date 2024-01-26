<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();            
            if($this->accao=="gravarGestorVagas"){
                if($this->verificacaoAcesso->verificarAcesso("", ["gestorVagas"])){
                    $this->gravarGestorVagas();
                }                
            }else if($this->accao=="alterarGestao"){
                if($this->verificacaoAcesso->verificarAcesso("", ["gestorVagas"])){

                    $this->conDb("inscricao");
                    $this->alterarGestao();
                    
                }
            }
    	}

        private function alterarGestao(){

            $idPGestor = $_POST["idPGestor"];

            $numeroVagasPosLab = isset($_POST["numeroVagasPosLab"])?$_POST["numeroVagasPosLab"]:0;
            $numeroVagasReg = $_POST["numeroVagasReg"];
            $criterioTeste = isset($_POST["criterioTeste"])?$_POST["criterioTeste"]:"";
            $criterioEscolhaPeriodo = isset($_POST["criterioEscolhaPeriodo"])?$_POST["criterioEscolhaPeriodo"]:"";
            $periodoCurso = isset($_POST["periodoCurso"])?$_POST["periodoCurso"]:"";
            $notaMinDiscNucleares = isset($_POST["notaMinDiscNucleares"])?$_POST["notaMinDiscNucleares"]:0;
            $numeroProvas = isset($_POST["numeroProvas"])?$_POST["numeroProvas"]:0;
            $tipoAutenticacao = isset($_POST["tipoAutenticacao"])?$_POST["tipoAutenticacao"]:0;
            $seAvaliarApenasMF = isset($_POST["seAvaliarApenasMF"])?"sim":"nao";

            $percIdade=""; $perMedDiscNucleares=""; $percGenero=""; $percAlunosEmRegime="";
            $factor1=""; $factor2=""; $factor3="";$factor4="";

            $nomeProva1="";$nomeProva2="";$nomeProva3="";

            if($criterioTeste=="exameAptidao"){
                $factor1 = "exameAptidao";
                $factor2 = isset($_POST["procFactor2"])?$_POST["procFactor2"]:"";
                $factor3 = isset($_POST["procFactor3"])?$_POST["procFactor3"]:"";
                $factor4 = isset($_POST["procFactor4"])?$_POST["procFactor4"]:"";
                $nomeProva1 = isset($_POST["nomeProva1"])?$_POST["nomeProva1"]:"";
                $nomeProva2 = isset($_POST["nomeProva2"])?$_POST["nomeProva2"]:"";
                $nomeProva3 = isset($_POST["nomeProva3"])?$_POST["nomeProva3"]:"";
            }else if($criterioTeste=="criterio"){
                $factor1 = isset($_POST["avalFactor1"])?$_POST["avalFactor1"]:"";
                $factor2 = isset($_POST["avalFactor2"])?$_POST["avalFactor2"]:"";
                $factor3 = isset($_POST["avalFactor3"])?$_POST["avalFactor3"]:"";
                $factor4 = isset($_POST["avalFactor4"])?$_POST["avalFactor4"]:"";
            }else if($criterioTeste=="factor"){
                $percIdade = isset($_POST["percIdade"])?$_POST["percIdade"]:0;
                $perMedDiscNucleares = isset($_POST["perMedDiscNucleares"])?$_POST["perMedDiscNucleares"]:0;
                $percGenero = isset($_POST["percGenero"])?$_POST["percGenero"]:0;
                $percAlunosEmRegime = isset($_POST["percAlunosEmRegime"])?$_POST["percAlunosEmRegime"]:0;
            }
            
            $this->editar("gestorvagas", "vagasReg, vagasPos, factor1, factor2, factor3, factor4, percDataNascAluno, perMedDiscNucleares, percGenero, percAlunosEmRegime, criterioTeste, notaMinDiscNucleares, numeroProvas, nomeProva1, nomeProva2, nomeProva3, estadoTransicaoCurso, periodosCurso, criterioEscolhaPeriodo, tipoAutenticacao, seAvaliarApenasMF", [$numeroVagasReg, $numeroVagasPosLab, $factor1, $factor2, $factor3, $factor4, $percIdade, $perMedDiscNucleares, $percGenero, $percAlunosEmRegime, $criterioTeste, $notaMinDiscNucleares, $numeroProvas, $nomeProva1, $nomeProva2, $nomeProva3, "F", $periodoCurso, $criterioEscolhaPeriodo, $tipoAutenticacao, $seAvaliarApenasMF], ["idPGestor"=>$idPGestor]);

            echo json_encode($this->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idAnoActual]));
        }

        private function gravarGestorVagas(){
            $cursos = $this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"]);

            $this->conDb("inscricao");
            if(seEnsinoSecundario()){
                
                foreach ($cursos as $curso) {

                    $criterioTeste = "";
                    $factor1 = "";
                    $factor2 = "";
                    $factor3 = "";
                    $factor4 = "";
                    $percDataNascAluno=0;
                    $perMedDiscNucleares=10;
                    $percGenero=0;
                    $percAlunosEmRegime=0;
                    $notaMinDiscNucleares="";
                    if($curso["tipoCurso"]=="tecnico"){

                        $criterioTeste = "factor";
                        $percDataNascAluno=70;
                        $perMedDiscNucleares=25;
                        $percGenero=5;
                        $percAlunosEmRegime=0;                        
                        $notaMinDiscNucleares=13;

                    }else if($curso["tipoCurso"]=="pedagogico"){
                        $criterioTeste = "exameAptidao";
                        $factor1 = "exameAptidao";
                        $factor2 = "dataNascAluno";
                        $factor3 = "mediaDiscNuclear";
                        $factor4 = "sexoAluno";
                        $notaMinDiscNucleares=13;
                    } 
                    $this->inserir("gestorvagas", "idPGestor", "idGestEscola, chaveGestao, idGestCurso, idGestAno, estadoTransicaoCurso, criterioTeste, factor1, factor2, factor3, factor4, notaMinDiscNucleares, percDataNascAluno, perMedDiscNucleares, percGenero, percAlunosEmRegime", [$_SESSION["idEscolaLogada"], $_SESSION["idEscolaLogada"]."-".$this->idAnoActual.$curso["idPNomeCurso"], $curso["idPNomeCurso"], $this->idAnoActual, "F", $criterioTeste, $factor1, $factor2, $factor3, $factor4, $notaMinDiscNucleares, $percDataNascAluno, $perMedDiscNucleares, $percGenero, $percAlunosEmRegime]);                            
                }
            }
            echo json_encode($this->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idAnoActual]));
        }    	
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
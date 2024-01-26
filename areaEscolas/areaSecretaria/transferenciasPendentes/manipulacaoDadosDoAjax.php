<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
           if($this->accao=="rejeitarTransferencia"){
            
                if($this->verificacaoAcesso->verificarAcesso("", ["transferenciasPendentes"], [])){
                    $this->rejeitarTransferencia();
                }
           }elseif($this->accao=="aceitarTransferencia"){
                if($this->verificacaoAcesso->verificarAcesso("", ["transferenciasPendentes"], [])){

                    $this->confirmarTransferencia();
                }
           }
        }

        private function rejeitarTransferencia(){
            $idPTransferencia = isset($_POST["idPTransferencia"])?$_POST["idPTransferencia"]:null;
            $idPMatricula = isset($_POST["idPMatricula"])?$_POST["idPMatricula"]:null;

            foreach ($this->selectArray("alunosmatriculados", ["transferencia.idPTransferencia", "transferencia.idTransfEscolaOrigem", "grupo"], ["idPMatricula"=>$idPMatricula, "transferencia.idPTransferencia"=>$idPTransferencia, "transferencia.estadoTransferencia"=>"Y"], ["transferencia"]) as $transf) {

                    $this->excluirItemObjecto("alunos_".$transf["grupo"], "transferencia", ["idPMatricula"=>$idPMatricula], ["idPTransferencia"=>$idPTransferencia]);

                    $this->editarItemObjecto("alunos_".$transf["grupo"], "reconfirmacoes", "estadoReconfirmacao", ["A"], ["idPMatricula"=>$idPMatricula], ["idReconfEscola"=>$transf["idTransfEscolaOrigem"], "idReconfAno"=>$this->idAnoActual]);

                    $this->editarItemObjecto("alunos_".$transf["grupo"], "escola", "estadoAluno", ["A"], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$transf["idTransfEscolaOrigem"]]);

                    $luzinguLuame = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "transferencia.dataTransferencia", "transferencia.idPTransferencia", "transferencia.turmaTransferencia"], ["transferencia.idTransfEscolaDestino"=>$_SESSION['idEscolaLogada'], "transferencia.idTransfAno"=>$this->idAnoActual, "transferencia.estadoTransferencia"=>"Y"], ["transferencia"],"", [], ["nomeAluno"=>1]);
                    $luzinguLuame = $this->anexarTabela2($luzinguLuame, "escolas", "transferencia", "idPEscola", "idTransfEscolaOrigem");

                    echo json_encode($luzinguLuame);
            }
        }

        private function confirmarTransferencia(){
            $idPTransferencia = isset($_POST["idPTransferencia"])?$_POST["idPTransferencia"]:null;
            $idPMatricula = isset($_POST["idPMatricula"])?$_POST["idPMatricula"]:null;
            $lingEspecialidade = isset($_POST["lingEspecialidade"])?$_POST["lingEspecialidade"]:null;
            $discEspecialidade = isset($_POST["discEspecialidade"])?$_POST["discEspecialidade"]:null;

            foreach ($this->selectArray("alunosmatriculados", [], ["idPMatricula"=>$idPMatricula, "transferencia.idPTransferencia"=>$idPTransferencia, "transferencia.estadoTransferencia"=>"Y"], ["transferencia"]) as $transf) {

                $escola = listarItensObjecto($transf, "escola", ["idMatEscola=".$transf["transferencia"]["idTransfEscolaOrigem"]]);

                $art1="o";
                $art2="e";
                if($transf["sexoAluno"]=="F"){
                    $art1 = $art2="a";
                }

                $moledji = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>valorArray($escola, "idMatCurso"), "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

                if(valorArray($moledji, "tipoCurso")=="geral" && ($discEspecialidade==NULL || $discEspecialidade=="")){
                    echo "FDeves seleccionar a disciplina de opção d".$art1." alun".$art1.".";
                }else if($lingEspecialidade==NULL || $lingEspecialidade==""){
                    echo "FDeves seleccionar a língua de opção d".$art1." alun".$art1.".";
                }else{
                    $this->editarItemObjecto("alunos_".$transf["grupo"], "transferencia", "estadoTransferencia", ["V"], ["idPMatricula"=>$idPMatricula], ["idPTransferencia"=>$idPTransferencia]);

                    $idMatAnexo = isset($_POST["idMatAnexo"])?$_POST["idMatAnexo"]:"";
                    $periodoAluno = isset($_POST["periodoAluno"])?$_POST["periodoAluno"]:"";
                    $numeroProcesso = isset($_POST["numeroProcesso"])?$_POST["numeroProcesso"]:"";
                    $turnoAluno = isset($_POST["turnoAluno"])?$_POST["turnoAluno"]:"";

                    if($numeroProcesso==NULL){
                        $expl = explode("ANGOS", $transf["numeroInterno"]);
                        $numeroProcesso = $expl[0].$expl[1];
                    }

                    $this->inserirObjecto("alunos_".$transf["grupo"], "escola", "idPAlEscola", "idFMatricula, idMatAno, idMatEscola, idMatEntidade, estadoAluno, dataMatricula, horaMatricula, periodoAluno, numeroProcesso, idMatAnexo, idMatCurso, classeActualAluno, inscreveuSeAntes, estadoDeDesistenciaNaEscola, turnoAluno, tipoEntrada, idCursos", [$transf["idPMatricula"], $this->idAnoActual, $_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], "A", $this->dataSistema, $this->tempoSistema, $periodoAluno, $numeroProcesso, $idMatAnexo, valorArray($escola, "idMatCurso"), valorArray($escola, "classeActualAluno"), "Y", "A", $turnoAluno, "porTransferencia", valorArray($escola, "idMatCurso")."-".valorArray($escola, "classeActualAluno")], ["idPMatricula"=>$idPMatricula]);
                    
                    $this->tratarArrayDeCursos($idPMatricula);

                    $this->inserirObjecto("alunos_".$transf["grupo"], "reconfirmacoes", "idPReconf", "idReconfMatricula, dataReconf, horaReconf, idMatCurso, classeReconfirmacao, tipoEntrada, chaveReconf, idReconfProfessor, idReconfAno, estadoReconfirmacao, idReconfEscola, nomeTurma, designacaoTurma", [$idPMatricula, $this->dataSistema, $this->tempoSistema, valorArray($escola, "classeActualAluno"), valorArray($escola, "classeActualAluno"), "porTransferencia", $idPMatricula."-".$this->idAnoActual."-".$_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], $this->idAnoActual, "A", $_SESSION["idEscolaLogada"], "", ""], ["idPMatricula"=>$idPMatricula]);

                    $idGestDisEspecialidade = valorArray($escola, "idGestDisEspecialidade");
                    $idGestLinguaEspecialidade = valorArray($escola, "idGestLinguaEspecialidade");

                    if(valorArray($escola, "idMatCurso")==3){
                        if($idGestLinguaEspecialidade==20){
                            $idGestLinguaEspecialidade=22;
                        }
                        if($idGestLinguaEspecialidade==21){
                            $idGestLinguaEspecialidade=23;
                        }
                    }

                    $modLE = valorArray($moledji, "modLinguaEstrangeira", "cursos");

                    if(($idGestDisEspecialidade!=$discEspecialidade && valorArray($moledji, "tipoCurso")=="geral") || ($idGestLinguaEspecialidade!=$lingEspecialidade  && ($modLE=="opcional" || $modLE=="lingEsp" || valorArray($escola, "classeActualAluno")<=9))){

                        $this->seleccionadorEspecialidades(valorArray($escola, "idMatCurso"), valorArray($escola, "classeActualAluno"), $lingEspecialidade, $discEspecialidade, $transf["idPMatricula"], $transf["grupo"]);
                    }

                    $this->atualizarTurma($transf["idPMatricula"], valorArray($escola, "classeActualAluno"), valorArray($escola, "idMatCurso"), "", $transf["idPMatricula"]); 

                    $this->gravarPautasAluno($transf["idPMatricula"]);

                    $luzinguLuame = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "transferencia.dataTransferencia", "transferencia.idPTransferencia", "transferencia.turmaTransferencia", "transferencia.idTransfEscolaOrigem"], ["transferencia.idTransfEscolaDestino"=>$_SESSION['idEscolaLogada'], "transferencia.idTransfAno"=>$this->idAnoActual, "transferencia.estadoTransferencia"=>"Y"], ["transferencia"],"", [], ["nomeAluno"=>1]);
                    $luzinguLuame = $this->anexarTabela2($luzinguLuame, "escolas", "transferencia", "idPEscola", "idTransfEscolaOrigem");

                    echo json_encode($luzinguLuame);
                }
            }
        }
    	
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
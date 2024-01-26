<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
           if($this->accao=="transferirAluno"){


                $classe = isset($_POST["classe"])?$_POST["classe"]:null; 
                $idMatCurso = isset($_POST["idMatCurso"])?$_POST["idMatCurso"]:null;

                if($this->verificacaoAcesso->verificarAcesso("", ["novaTrasferencia"], [$classe, $idMatCurso])){

                    $array = $this->selectArray("alunosmatriculados", ["escola.beneficiosDaBolsa"], ["idPMatricula"=>$_POST["idPMatricula"], "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);
                    
                    $precoEmolumento = $this->preco("transferencia", $_POST["classe"], $_POST["idMatCurso"], "", $array);
                    $this->idPHistoricoConta = $this->pagamentoAnteriorDoAluno($_POST["idPMatricula"], "transferencia");

                    if(($this->idPHistoricoConta==NULL || $this->idPHistoricoConta=="") && $precoEmolumento>0){
                        echo "FO(a) aluno(a) ainda não fez pagamento deste documento.";
                    }else{
                        $this->transferirAluno();
                    }
                }    
           }
        }

        private function transferirAluno(){
            $idPMatricula = isset($_POST["idPMatricula"])?$_POST["idPMatricula"]:null;

            $nomeEscolaOption = isset($_POST["nomeEscolaOption"])?$_POST["nomeEscolaOption"]:null;
            $nomeEscolaCaixa = isset($_POST["nomeEscolaCaixa"])?$_POST["nomeEscolaCaixa"]:null;
            $seTransferenciaLocal = isset($_POST["seTransferenciaLocal"])?"sim":"nao";
            $nomeProvincia = isset($_POST["nomeProvincia"])?$_POST["nomeProvincia"]:null;
            $nomeMunicipio = isset($_POST["nomeMunicipio"])?$_POST["nomeMunicipio"]:null;
            $nomeComuna = isset($_POST["nomeComuna"])?$_POST["nomeComuna"]:null;
            $documentosAnexos = isset($_POST["documentosAnexos"])?$_POST["documentosAnexos"]:null;
            $turma = isset($_POST["turma"])?$_POST["turma"]:null;
            $classe = isset($_POST["classe"])?$_POST["classe"]:null; 
            $idMatCurso = isset($_POST["idMatCurso"])?$_POST["idMatCurso"]:null;
            
            $grupo = $this->selectUmElemento("alunosmatriculados", "grupo", ["idPMatricula"=>$idPMatricula]);

            if($seTransferenciaLocal=="sim"){
                $nomeEscolaCaixa = NULL;
                $nomeProvincia = NULL;
                $nomeMunicipio = NULL;
                $nomeComuna=NULL;
                $estadoTransferencia="Y";
            }else{
                $nomeEscolaOption = NULL;
                $estadoTransferencia="V";
            }

           if($this->inserirObjecto("alunos_".$grupo, "transferencia", "idPTransferencia", "idTransfMatricula, idTransfEscolaOrigem, idTransfEscolaDestino, nomeEscolaDestino, nomeProvinciaDestino, nomeMunicipioDestino, nomeComunaDestino, idTransfAno, dataTransferencia, horaTransferencia, estadoTransferencia, turmaTransferencia, idTransfEntidade, documentosAnexados, idPHistoricoConta, chaveTransf", [$idPMatricula, $_SESSION["idEscolaLogada"], $nomeEscolaOption, $nomeEscolaCaixa, $nomeProvincia, $nomeMunicipio, $nomeComuna, $this->idAnoActual, $this->dataSistema, $this->tempoSistema, $estadoTransferencia, $turma, $_SESSION["idUsuarioLogado"], $documentosAnexos, $this->idPHistoricoConta, $idPMatricula."-".$this->idAnoActual."-".$_SESSION['idEscolaLogada']], ["idPMatricula"=>$idPMatricula])=="sim"){

                $this->editarItemObjecto("alunos_".$grupo, "escola", "estadoAluno", ["T"], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$_SESSION["idEscolaLogada"]]);

                $this->editarItemObjecto("alunos_".$grupo, "reconfirmacoes", "estadoReconfirmacao", ["T"], ["idPMatricula"=>$idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada']]);

                $this->editarItemObjecto("alunos_".$grupo, "pagamentos", "estadoPagamento", ["A"], ["idPMatricula"=>$idPMatricula], ["idPHistoricoConta"=>$this->idPHistoricoConta]);

                $this->editarItemObjecto("payments", "itens", "estadoItem", ["A"], ["identificadorCliente"=>$idPMatricula], ["idPHistoricoConta"=>$this->idPHistoricoConta]);
                $this->actuazalizarReconfirmacaAluno($idPMatricula);
                $this->tratarArrayDeCursos($idPMatricula);

                echo $this->selectJson("alunosmatriculados", ["nomeAluno", "idPMatricula", "numeroInterno", "reconfirmacoes.designacaoTurma", "reconfirmacoes.nomeTurma","sexoAluno", "fotoAluno", "reconfirmacoes.classeReconfirmacao", "escola.idMatCurso"], ["reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.classeReconfirmacao"=>$classe, "escola.estadoAluno"=>"A", "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idMatCurso], ["escola", "reconfirmacoes"], "", [], array("nomeAluno"=>1), $this->matchMaeAlunos($this->idAnoActual, $idMatCurso, $classe));
            }else{
                echo "FNão foi possível processar a transferência do aluno.";
            }

        }
    	
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
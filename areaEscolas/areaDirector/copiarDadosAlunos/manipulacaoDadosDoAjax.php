<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            
            $classeAluno = isset($_POST["classeAlunoForm"])?$_POST["classeAlunoForm"]:"";
            $idPCurso = isset($_POST["idPCursoForm"])?$_POST["idPCursoForm"]:"";
            if($idPCurso=="EP" || $idPCurso=="EB"){
                $idPCurso="";
            }
            if($this->accao=="pesquisarAluno"){
                $this->pesquisarAluno();
            }else if($this->accao=="copiarDados"){
                if($this->verificacaoAcesso->verificarAcesso("", ["copiarDadosAlunos"])){
                    $this->copiarDados();
                } 
            }          
    	}

        private function pesquisarAluno(){
            $numeroInternoAluno = isset($_GET["numeroInternoAluno"])?$_GET["numeroInternoAluno"]:"";
            $bilheteIdentidade = isset($_GET["bilheteIdentidade"])?$_GET["bilheteIdentidade"]:"";

            $alunoActual = $this->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula"], ["numeroInterno"=>$numeroInternoAluno, "escola.idMatEscola"=>$_SESSION["idEscolaLogada"]], ["escola"], 1);

            $alunoEncontrado = $this->selectArray("alunosmatriculados", ["nomeAluno", "sexoAluno", "numeroInterno", "biAluno", "fotoAluno", "idPMatricula", "escola.classeActualAluno", "escola.idMatCurso", "escola.idMatEscola"], ['biAluno'=>$bilheteIdentidade, "idPMatricula"=>['$ne'=>intval(valorArray($alunoActual, "idPMatricula"))]], ["escola"], 1);
            $alunoEncontrado = $this->anexarTabela2($alunoEncontrado, "escolas", "escola", "idPEscola", "idMatEscola"); 
            $alunoEncontrado = $this->anexarTabela2($alunoEncontrado, "nomecursos", "escola", "idPEscola", "idMatCurso");


            if(count($alunoActual)<=0){
                $alunoEncontrado = array();
            }else{
                $informacao="";
                if(valorArray($alunoActual, "nomeAluno")!=valorArray($alunoEncontrado, "nomeAluno")){
                    $informacao="Os nomes dos alunos devem ser iguais.";
                }
                foreach($alunoEncontrado as $a){
                    $a["informacao"]=$informacao;
                    $a["nomeAlunoActual"] = valorArray($alunoActual, "nomeAluno");
                    $a["idAlunoActual"] = valorArray($alunoActual, "idPMatricula");
                }
            }
            echo json_encode($alunoEncontrado);
        }

        private function copiarDados (){
            $idAlunoActual = isset($_GET["idAlunoActual"])?$_GET["idAlunoActual"]:"";
            $idAlunoNovo = isset($_GET["idAlunoNovo"])?$_GET["idAlunoNovo"]:"";

            $idAlunoActual=(int)$idAlunoActual;
            $idAlunoNovo=(int)$idAlunoNovo;
    
            $sobreAluno = $this->selectArray("alunosmatriculados", [], ["idPMatricula"=>$idAlunoActual], [], 1);
            $grupoAlunoActual = valorArray($sobreAluno, "grupo");

            $grupoAlunoNovo = $this->selectUmElemento("alunosmatriculados", "grupo", ["idPMatricula"=>$idAlunoNovo], [], 1);

            $itensAListar = ["reconfirmacoes", "escola", "cadeiras_atraso", "dadosatraso", "pagamentos", "arquivo_pautas", "pautas", "transferencia", "alteracoes_notas"];

            foreach($itensAListar as $itemListar){

                foreach(isset($sobreAluno[0][$itemListar])?$sobreAluno[0][$itemListar]:array() as $a){

                    $contChave=0;
                    $chavePrincial="";

                    $campos="";
                    $valores=array();
                    foreach(retornarChaves($a) as $chave){
                        $contChave++;
                        if($contChave==1){
                            $chavePrincial=$chave;
                        }else{
                            if($campos!=""){
                                $campos .=",";
                            }
                            $campos .=$chave;
                            if($chave=="idReconfMatricula" || $chave=="idFMatricula" || $chave=="idArquivoAluno" || $chave=="idCadMatricula" || $chave=="idDAMatricula" || $chave=="idHistoricoMatricula" || $chave=="idPautaMatricula" || $chave=="idTransfMatricula"){
                                $valores[] = $idAlunoNovo;
                            }else if($chave=="chaveReconf"){
                                $valores[] = $idAlunoNovo."-".$a["idReconfAno"]."-".$a["idReconfEscola"];
                            }else if($chave=="chaveObs"){
                                $valores[] = $idAlunoNovo."-".$a["idAvalAno"]; 
                            }else if($chave=="chaveEA"){
                                $valores[] = $idAlunoNovo."-".$a["classeAnterior"]."-".$a["idDEscola"];
                            }else if($chave=="chaveTransf"){
                                $valores[] = $idAlunoNovo."-".$a["idTransfAno"]."-".$a["idTransfEscolaOrigem"];
                            }else if($chave=="chavePauta"){

                                if($itemListar=="arquivo_pautas"){
                                    if($a["classePauta"]<=9){
                                        $valores[] = $idAlunoNovo."-".$a["idPautaDisciplina"]."-".$a["classePauta"]."-".$a["idPautaEscola"]."-".$a["idPautaAno"]."-".$a["semestrePauta"];
                                    }else{
                                        $valores[] = $idAlunoNovo."-".$a["idPautaDisciplina"]."-".$a["classePauta"]."-".$a["idPautaCurso"]."-".$a["idPautaEscola"]."-".$a["idPautaAno"]."-".$a["semestrePauta"];
                                    }
                                }else{
                                    if($a["classePauta"]<=9){
                                        $valores[] = $idAlunoNovo."-".$a["idPautaDisciplina"]."-".$a["classePauta"]."-".$a["semestrePauta"];
                                    }else{
                                        $valores[] = $idAlunoNovo."-".$a["idPautaDisciplina"]."-".$a["classePauta"]."-".$a["idPautaCurso"]."-".$a["semestrePauta"];
                                    }
                                }
                            }else{
                                $valores[]=$a[$chave];
                            }
                        }
                    }

                    $this->inserirObjecto("alunos_".$grupoAlunoNovo, $itemListar, $chavePrincial, $campos, $valores, ["idPMatricula"=>$idAlunoNovo]);

                    $this->excluirItemObjecto("alunos_".$grupoAlunoActual, $itemListar, ["idPMatricula"=>$idAlunoActual], [$chavePrincial=>$a[$chavePrincial]]);

                }
            }

            foreach ($this->selectArray("arquivo_conselho_notas", [], ["idArquivoAluno"=>$idAlunoActual]) as $a) {
                $this->editar("arquivo_conselho_notas", "idArquivoAluno", [$idAlunoNovo], ["idPMatricula"=>$idAlunoActual, "idPArquivoNotas"=>$a["idPArquivoNotas"]]);
            }

            /*foreach ($this->selectArray("mensagens", ["idEmissorMat"=>$idAlunoActual]) as $a) {
                $this->editar("mensagens", "idEmissorMat", [$idAlunoNovo], ["idPMensagem"=>$a["idPMensagem"]]);
            }*/
            /*foreach ($this->selectArray("mensagens", ["idReceptorMat"=>$idAlunoActual]) as $a) {
                $this->editar("mensagens", "idReceptorMat", [$idAlunoNovo], ["idPMensagem"=>$a->idPMensagem]);
            }*/
            $this->excluir("alunos_".$grupoAlunoActual, ["idPMatricula"=>$idAlunoActual]);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
<?php 
	session_start(); 
	 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
                    
            if($this->accao=="listarUsuarios"){
                $this->listarUsuarios();
            }else if($this->accao=="enviarMensagens"){
                if($this->verificacaoAcesso->verificarAcesso("", ["novoComunicado"], [])){
                    $this->enviarMensagens();
                }
            }
    	}

        private function enviarMensagens(){
            $dadosEnviar = json_decode($_POST['dadosEnviar']);
            $textoMensagem = $_POST['textoMensagem'];
            $destinatario = $_POST['destinatario'];
            $precoTotal = floatval($_POST['precoTotSMS']);


            $sobreContrato = $this->selectArray("escolas", ["contrato.saldoParaPagamentoPosPago"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["contrato"]);
            $saldoParaPagamentoPosPago = valorArray($sobreContrato, "saldoParaPagamentoPosPago", "contrato");

            
            if($saldoParaPagamentoPosPago<$precoTotal){
                echo "FA escola não possuí saldo suficiente para enviar o comunicado.";
            }else{
                foreach($dadosEnviar as $a){
                    $this->inserir("comunicados", "idPComunicado", "id, user, nome, telefone, mensagem, precoSMS, autor, data, hora, idPEscola", [$a->id, $destinatario, $a->nome, $a->telefone, $textoMensagem, ($precoTotal/count($dadosEnviar)), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $this->dataSistema, $this->tempoSistema, $_SESSION['idEscolaLogada']]);

                    enviarSMS($a->telefone, mb_strtoupper(valorArray($this->sobreEscolaLogada, "abrevNomeEscola2"))."
".$textoMensagem); 

                }
                $this->editarItemObjecto("escolas", "contrato", "saldoParaPagamentoPosPago", [($saldoParaPagamentoPosPago-$precoTotal)], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idEscolaContrato"=>$_SESSION['idEscolaLogada']]); 
            }
            
        }

        private function listarUsuarios(){
            $seApenasAlunosReconf = $_GET['seApenasAlunosReconf'];
            $destinatario = $_GET['destinatario'];
            $luzingu = $_GET['luzingu'];

            if($destinatario=="professor"){
                $array = $this->selectArray("entidadesprimaria", ["idPEntidade", "numeroInternoEntidade", "numeroTelefoneEntidade", "emailEntidade", "nomeEntidade"], ["escola.idEntidadeEscola"=>$_SESSION["idEscolaLogada"], "escola.estadoActividadeEntidade"=>"A", "numeroTelefoneEntidade"=>array('$nin'=>[null, ""])], ["escola"], "", [], ["nomeEntidade"=>1]);
            }else{
                $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:"";
                
                if($seApenasAlunosReconf=="sim"){
                    if($luzingu==""){
                        $array = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "telefoneAluno", "emailAluno"], ["reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "telefoneAluno"=>array('$nin'=>[null, ""])], ["escola", "reconfirmacoes"], "", [], array("nomeAluno"=>1));
                    }else{
                        $luzingu = explode("-", $luzingu);
                        $idPCurso = isset($luzingu[2])?$luzingu[2]:"";
                        $classe = isset($luzingu[1])?$luzingu[1]:"";
                        $periodo = isset($luzingu[0])?$luzingu[0]:"";

                        $array = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "telefoneAluno", "emailAluno"], ["reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.periodoAluno"=>$periodo, "reconfirmacoes.classeReconfirmacao"=>$classe, "telefoneAluno"=>array('$nin'=>[null, ""]), "reconfirmacoes.idMatCurso"=>$idPCurso], ["escola", "reconfirmacoes"], "", [], array("nomeAluno"=>1));
                    }
                    
                }else{
                    if($luzingu==""){
                        $array = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "telefoneAluno", "emailAluno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>"A", "telefoneAluno"=>array('$nin'=>[null, ""])], ["escola"], "", [], ["nomeAluno"=>1]);
                    }else{
                        $luzingu = explode("-", $luzingu);
                        $idPCurso = isset($luzingu[2])?$luzingu[2]:"";
                        $classe = isset($luzingu[1])?$luzingu[1]:"";
                        $periodo = isset($luzingu[0])?$luzingu[0]:"";

                        $condicao =["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.periodoAluno"=>$periodo, "telefoneAluno"=>array('$nin'=>[null, ""])];
                        $expl = explode("_", $classe);

                        if(count($expl)>1){
                            $estadoAluno="Y";
                            $idAnoF = $expl[1];
                            $condicao["escola.idMatFAno"]=$idAnoF;
                            $condicao["escola.idMatCurso"] =$idPCurso;
                        }else{
                            $condicao["escola.estadoAluno"]="A";
                            $condicao2[] = "estadoAluno=A";
                            $condicao["escola.classeActualAluno"]=$classe;
                            if($classe>=10){
                              $condicao["escola.idMatCurso"] =$idPCurso;
                            }
                        }

                        $array = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "telefoneAluno", "emailAluno"], $condicao, ["escola"], "", [], ["nomeAluno"=>1]);
                    }
                }
            }
            echo json_encode($array);
        }

}
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
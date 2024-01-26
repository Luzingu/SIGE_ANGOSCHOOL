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
            $precoTotal = floatval($_POST['precoTotSMS']);


            $sobreContrato = $this->selectArray("escolas", ["contrato.saldoParaPagamentoPosPago"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["contrato"]);
            $saldoParaPagamentoPosPago = valorArray($sobreContrato, "saldoParaPagamentoPosPago", "contrato");

            
            if($saldoParaPagamentoPosPago<$precoTotal){
                echo "FA escola não possuí saldo suficiente para enviar o comunicado.";
            }else{
                foreach($dadosEnviar as $a){
                    $this->inserir("comunicados", "idPComunicado", "id, user, nome, telefone, mensagem, precoSMS, autor, data, hora, idPEscola", [$a->id, "alunos", $a->nome, $a->telefone, $textoMensagem, ($precoTotal/count($dadosEnviar)), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $this->dataSistema, $this->tempoSistema, $_SESSION['idEscolaLogada']]);

                    enviarSMS($a->telefone, mb_strtoupper(valorArray($this->sobreEscolaLogada, "abrevNomeEscola2"))."
".$textoMensagem); 

                }
                $this->editarItemObjecto("escolas", "contrato", "saldoParaPagamentoPosPago", [($saldoParaPagamentoPosPago-$precoTotal)], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idEscolaContrato"=>$_SESSION['idEscolaLogada']]); 
            }
            
        }

}
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
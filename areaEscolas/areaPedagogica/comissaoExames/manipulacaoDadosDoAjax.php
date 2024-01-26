<?php 
	session_start();
	 include_once ('../../funcoesAuxiliares.php');
  include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            if($this->accao=="alterarDados"){
              if($this->verificacaoAcesso->verificarAcesso("", ["comissaoExames"], [])){
                $this->alterarDados();
              }
            }
    	}

        private function alterarDados(){
            $dadosEnviar = isset($_GET["dadosEnviar"])?$_GET["dadosEnviar"]:"";

            $dadosEnviar = json_decode($dadosEnviar);
            foreach($dadosEnviar as $d){
              
              $this->editar("divisaoprofessores", "idPresidenteComissaoExame, estadoComissaoExame", [$d->idPresidenteComissaoExame, $d->estadoComissaoExame], ["idPDivisao"=>$d->idPDivisao]);
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
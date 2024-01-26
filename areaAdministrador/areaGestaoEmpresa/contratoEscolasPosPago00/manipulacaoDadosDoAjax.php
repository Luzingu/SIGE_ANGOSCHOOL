<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct(){
    		parent::__construct();
    		include_once $this->caminhoRetornar.'funcoesAuxiliares.php';
    	
        if($this->accao=="prorogarContrato"){
            if($this->verificacaoAcesso->verificarAcesso("", ["contratoEscolasPosPago00"])){
                $this->prorogarContrato();
            }
        }       
	        
    	}

      private function prorogarContrato(){
          $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:"";
          $this->prorogarContratoEscolasPosPago($idPEscola);
          echo json_encode($this->selectArray("escolas", [], ["contrato.tipoPagamento"=>"pos", "idEstadoEscola"=>['$nin'=>[4, 7]]], ["contrato"], "", [], array("nomeEscola"=>1)));
      }

      

    }
    new manipulacaoDadosDoAjaxInterno();
?>
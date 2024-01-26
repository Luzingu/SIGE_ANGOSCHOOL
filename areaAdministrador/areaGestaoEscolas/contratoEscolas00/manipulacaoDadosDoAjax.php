<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct(){
    		parent::__construct();
    		include_once $this->caminhoRetornar.'funcoesAuxiliares.php';
    		
        $this->idPEscola = trim(filter_input(INPUT_POST, "idPEscola", FILTER_SANITIZE_NUMBER_INT));

        $this->dataInicioContrato = trim(filter_input(INPUT_POST, "dataInicioContrato", FILTER_SANITIZE_STRING));
        $this->dataFimContrato = trim(filter_input(INPUT_POST, "dataFimContrato", FILTER_SANITIZE_STRING));
        $this->tipoPagamento = trim(filter_input(INPUT_POST, "tipoPagamento", FILTER_SANITIZE_STRING));
        $this->valorPagoPor15Dias = trim(filter_input(INPUT_POST, "valorPagoPor15Dias", FILTER_SANITIZE_STRING));
        $this->valorPorAluno = trim(filter_input(INPUT_POST, "valorPorAluno", FILTER_SANITIZE_NUMBER_INT));

        $this->inicioPrazoPosPago = trim(filter_input(INPUT_POST, "inicioPrazoPosPago", FILTER_SANITIZE_STRING));
        $this->modoPagamento = trim(filter_input(INPUT_POST, "modoPagamento", FILTER_SANITIZE_STRING));

        $this->fimPrazoPosPago = trim(filter_input(INPUT_POST, "fimPrazoPosPago", FILTER_SANITIZE_STRING));
        
        $this->mesesConsecutivosParaBloquear = trim(filter_input(INPUT_POST, "mesesConsecutivosParaBloquear", FILTER_SANITIZE_STRING));

        if($this->accao=="editarEscola"){

            if($this->verificacaoAcesso->verificarAcesso("", ["contratoEscolas00"])){
                $this->editarEscola();
            }

        }       
	        
    	}

      

      private function editarEscola(){

        $dadosAnterior = $this->selectArray("escolas", [], ["idPEscola"=>$this->idPEscola], ["contrato"]);

        $imgContrato = $this->upload("imgContrato", "Contrato", 'Ficheiros/Escola_'.$this->idPEscola.'/Icones', $this->caminhoRetornar, valorArray($dadosAnterior, "imgContrato"));

        if($this->editarItemObjecto("escolas", "contrato", "dataInicioContrato, tipoPagamento, inicioPrazoPosPago, fimPrazoPosPago, imgContrato, mesesConsecutivosParaBloquear, modoPagamento, valorPorAluno, valorPagoPor15Dias", [$this->dataInicioContrato, $this->tipoPagamento, $this->inicioPrazoPosPago, $this->fimPrazoPosPago, $imgContrato, $this->mesesConsecutivosParaBloquear, $this->modoPagamento, $this->valorPorAluno, $this->valorPagoPor15Dias], ["idPEscola"=>$this->idPEscola], ["idEscolaContrato"=>$this->idPEscola])=="sim"){

          $this->valorContratatualDasEscolas($this->idPEscola);
 
          echo json_encode($this->selectArray("escolas", [], ["idPEscola"=>['$nin'=>[4, 7]]], ["contrato"], "", [], array("nomeEscola"=>1)));
        }else{
          echo "FNão possível editar os dados da escola.";
        }
      }

      

    }
    new manipulacaoDadosDoAjaxInterno();
?>
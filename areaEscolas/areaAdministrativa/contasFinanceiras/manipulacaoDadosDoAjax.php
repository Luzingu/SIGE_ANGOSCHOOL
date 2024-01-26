<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	
    	function __construct(){
    		parent::__construct();

            $this->descricaoConta = isset($_POST['descricaoConta'])?$_POST['descricaoConta']:"";
            $this->bancoConta = isset($_POST['bancoConta'])?$_POST['bancoConta']:"";
            $this->numeroConta = isset($_POST['numeroConta'])?$_POST['numeroConta']:"";
            $this->ibanConta = isset($_POST['ibanConta'])?$_POST['ibanConta']:"";
            $this->categoriaTipoConta = isset($_POST['categoriaTipoConta'])?$_POST['categoriaTipoConta']:"";
            $this->hierarquia = isset($_POST['hierarquia'])?$_POST['hierarquia']:"";
            $this->idPContaFinanceira = isset($_POST['idPContaFinanceira'])?$_POST['idPContaFinanceira']:"";

            if($this->accao=="novaConta"){
                if($this->verificacaoAcesso->verificarAcesso("", ["contasFinanceiras"])){
                    $this->novaConta();
                }
            }else if($this->accao=="editarContaBancaria"){
                if($this->verificacaoAcesso->verificarAcesso("", ["contasFinanceiras"])){
                    $this->editarContaBancaria();
                }
            }else if($this->accao=="excluirContaBancaria"){
                if($this->verificacaoAcesso->verificarAcesso("", ["contasFinanceiras"])){
                    $this->excluirContaBancaria();
                }
            }
    	}

        private function novaConta(){
            if($this->inserir("contas_bancarias", "idPContaFinanceira", "idContaEscola, descricaoConta, bancoConta, numeroConta, ibanConta, categoriaTipoConta, hierarquia, estadoAlteracao", [$_SESSION['idEscolaLogada'], $this->descricaoConta, $this->bancoConta, $this->numeroConta, $this->ibanConta, $this->categoriaTipoConta, $this->hierarquia, "A"])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível inserir a conta.";
            }
        }

        private function excluirContaBancaria(){

            if($this->selectUmElemento("contas_bancarias", "estadoAlteracao", ["idPContaFinanceira"=>$this->idPContaFinanceira])!="A"){
                echo "FNão podes excluir a conta.";
            }else if($this->excluir("contas_bancarias", ["idPContaFinanceira"=>$this->idPContaFinanceira])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir a conta.";
            }
        }

        private function editarContaBancaria(){

            if($this->selectUmElemento("contas_bancarias", "estadoAlteracao", ["idPContaFinanceira"=>$this->idPContaFinanceira])!="A"){
                echo "FNão podes alterar os dados da conta.";
            }else if($this->editar("contas_bancarias", "descricaoConta, bancoConta, numeroConta, ibanConta, categoriaTipoConta, hierarquia", [$this->descricaoConta, $this->bancoConta, $this->numeroConta, $this->ibanConta, $this->categoriaTipoConta, $this->hierarquia], ["idPContaFinanceira"=>$this->idPContaFinanceira])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da conta.";
            }
        }

        private function listar(){
            echo $this->selectJson("contas_bancarias", [],["idContaEscola"=>$_SESSION['idEscolaLogada']],[], "", [], ["idPContaFinanceira"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>
<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	
    	function __construct(){
    		parent::__construct();

            $this->NIF = isset($_POST['NIF'])?$_POST['NIF']:"";
            $this->nomeEmpresa = isset($_POST['nomeEmpresa'])?$_POST['nomeEmpresa']:"";
            $this->enderecoEmpresa = isset($_POST['enderecoEmpresa'])?$_POST['enderecoEmpresa']:"";
            $this->cidadeEmpresa = isset($_POST['cidadeEmpresa'])?$_POST['cidadeEmpresa']:"";
            $this->paisEmpresa = isset($_POST['paisEmpresa'])?$_POST['paisEmpresa']:"";
            $this->idPFornecedor = isset($_POST['idPFornecedor'])?$_POST['idPFornecedor']:"";

            if($this->accao=="novoFornecedor"){
                if($this->verificacaoAcesso->verificarAcesso("", ["fornecedores"])){
                    $this->novoFornecedor();
                }
            }else if($this->accao=="editarFornecedor"){
                if($this->verificacaoAcesso->verificarAcesso("", ["fornecedores"])){
                    $this->editarFornecedor();
                }
            }else if($this->accao=="excluirFornecedor"){
                if($this->verificacaoAcesso->verificarAcesso("", ["fornecedores"])){
                    $this->excluirFornecedor();
                }
            }
    	}

        private function novoFornecedor(){
            if($this->inserirObjecto("escolas", "fornecedores", "idPFornecedor", "NIF, nomeEmpresa, enderecoEmpresa, cidadeEmpresa, paisEmpresa, estadoAlteracao", [$this->NIF, $this->nomeEmpresa, $this->enderecoEmpresa, $this->cidadeEmpresa, $this->paisEmpresa, "A"], ["idPEscola"=>$_SESSION['idEscolaLogada']])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível inserir a conta.";
            }
        }

        private function excluirFornecedor(){
            $estadoAlteracao = valorArray($this->selectArray("escolas", ["fornecedores.estadoAlteracao"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "fornecedores.idPFornecedor"=>$this->idPFornecedor], ["fornecedores"]), "estadoAlteracao", "fornecedores");

            if($estadoAlteracao!="A"){
                echo "FNão podes excluir o fornecedor.";
            }else if($this->excluirItemObjecto("escolas", "fornecedores", ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idPFornecedor"=>$this->idPFornecedor])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir a conta.";
            }
        }

        private function editarFornecedor(){

            $estadoAlteracao = valorArray($this->selectArray("escolas", ["fornecedores.estadoAlteracao"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "fornecedores.idPFornecedor"=>$this->idPFornecedor], ["fornecedores"]), "estadoAlteracao", "fornecedores");

            if($estadoAlteracao!="A"){
                echo "FNão podes alterar os dados do fornecedor.";
            }else if($this->editarItemObjecto("escolas", "fornecedores", "NIF, nomeEmpresa, enderecoEmpresa, cidadeEmpresa, paisEmpresa", [$this->NIF, $this->nomeEmpresa, $this->enderecoEmpresa, $this->cidadeEmpresa, $this->paisEmpresa], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idPFornecedor"=>$this->idPFornecedor])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados do fornecedor.";
            }
        }

        private function listar(){
            echo $this->selectJson("escolas", ["fornecedores.NIF", "fornecedores.idPFornecedor", "fornecedores.nomeEmpresa", "fornecedores.enderecoEmpresa", "fornecedores.cidadeEmpresa", "fornecedores.codigoConta", "fornecedores.paisEmpresa"],["idPEscola"=>$_SESSION['idEscolaLogada']],["fornecedores"], "", [], ["idPFornecedor"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>
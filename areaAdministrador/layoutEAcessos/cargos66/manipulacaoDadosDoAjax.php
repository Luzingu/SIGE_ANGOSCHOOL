<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
 
        function __construct($baseDeDados){
            parent::__construct();
            $this->idPCargo = isset($_POST["idPCargo"])?$_POST["idPCargo"]:"";
            $this->designacaoCargo = isset($_POST["designacaoCargo"])?$_POST["designacaoCargo"]:"";
            $this->instituicao = isset($_POST["instituicao"])?$_POST["instituicao"]:"";
            $this->baseDeDados=$baseDeDados;
            $this->conDb($baseDeDados, true);

            if($this->accao=="editarCargo"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->editarCargo();
                }
            }else if($this->accao=="novoCargo"){

                if($this->verificacaoAcesso->verificarAcesso(0)){
                    $this->novoCargo();
                }
            }else if ($this->accao=="excluirCargo"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->excluirCargo();
                }
            }
        }

        private function novoCargo(){
            if($this->inserir("cargos", "idPCargo", "designacaoCargo, instituicao", [$this->designacaoCargo, $this->instituicao])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar o cargo.";
            }
        }
        private function editarCargo(){
            if($this->editar("cargos", "designacaoCargo, instituicao", [$this->designacaoCargo, $this->instituicao], ["idPCargo"=>$this->idPCargo])=="sim"){
                $this->listar(); 
            }else{
                echo "FNão foi possível editar.";
            }
        }

        private function excluirCargo(){
            if($this->excluir("cargos", ["idPCargo"=>$this->idPCargo])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir.";
            }
        }
        private function listar(){
            if($this->baseDeDados=="escola"){
                echo $this->selectJson("cargos", [], [], [], "", [], array("designacaoCargo"=>1));
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno("teste");
    new manipulacaoDadosDoAjaxInterno("escola");
?>
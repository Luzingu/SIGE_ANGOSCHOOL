<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($tipoBaseDados){
            parent::__construct();
            $this->tipoBaseDados=$tipoBaseDados;

            $this->idPSubsistema = isset($_POST["idPSubsistema"])?$_POST["idPSubsistema"]:"";
            $this->categroria = isset($_POST["categroria"])?$_POST["categroria"]:"";
            $this->ordem = isset($_POST["ordem"])?$_POST["ordem"]:"";
            $this->designacaoSubistema = isset($_POST["designacaoSubistema"])?$_POST["designacaoSubistema"]:"";

            $this->conDb($tipoBaseDados);

            if($this->accao=="editarSubSistema"){
                if($this->verificacaoAcesso->verificarAcesso("", ["subSistemas00"])){
                  $this->editarSubSistema();
                }
            }else if($this->accao=="novoSubSistema"){

                if($this->verificacaoAcesso->verificarAcesso("", ["subSistemas00"])){
                      $this->novoSubSistema();
                }
            }else if ($this->accao=="excluirSubsistema"){
                if($this->verificacaoAcesso->verificarAcesso("", ["subSistemas00"])){
                  $this->excluirSubsistema();
                }
            }
        }

        private function novoSubSistema(){

            if($this->inserir("subsistemasDeEnsino", "idPSubsistema", "categroria, ordem, designacaoSubistema", [$this->categroria, $this->ordem, $this->designacaoSubistema])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar o SubSistema.";
            }
        }

        private function editarSubSistema(){
            if($this->editar("subsistemasDeEnsino", "categroria, ordem, designacaoSubistema", [$this->categroria, $this->ordem, $this->designacaoSubistema], ["idPSubsistema"=>$this->idPSubsistema])=="sim"){

                $this->listar();   
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirSubsistema(){
            if($this->editar("subsistemasDeEnsino", "categroria, ordem, designacaoSubistema", [null, null, null], ["idPSubsistema"=>$this->idPSubsistema])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir o curso.";
            }
        }

        private function listar(){
            if($this->tipoBaseDados=="escola"){
                echo $this->selectJson("subsistemasDeEnsino", [], ["designacaoSubistema"=>array('$ne'=>null)], [], "", [], array("ordem"=>1));
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno("escola");
    new manipulacaoDadosDoAjaxInterno("teste");
?>
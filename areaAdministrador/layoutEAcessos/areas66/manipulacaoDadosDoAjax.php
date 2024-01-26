<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
        function __construct($baseDeDados){
            parent::__construct();

            $this->idPArea = isset($_POST["idPArea"])?$_POST["idPArea"]:"";
            $this->designacaoArea = isset($_POST["designacaoArea"])?$_POST["designacaoArea"]:"";
            $this->instituicao = isset($_POST["instituicao"])?$_POST["instituicao"]:"";
            $this->icone = isset($_POST["icone"])?$_POST["icone"]:"";
            $this->ordenacao = isset($_POST["ordenacao"])?$_POST["ordenacao"]:"";
            $this->eGratuito = isset($_POST["eGratuito"])?$_POST["eGratuito"]:"";

            $this->baseDeDados=$baseDeDados;
            $this->conDb($baseDeDados, true);

            if($this->accao=="editarArea"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->editarArea();
                }
            }else if($this->accao=="novaArea"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                    $this->novaArea();
                }
            }else if ($this->accao=="excluirArea"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->excluirArea();
                }
            }
        }

        private function novaArea(){
            if($this->inserir("areas", "idPArea", "designacaoArea, instituicao, icone, ordenacao, eGratuito", [$this->designacaoArea, $this->instituicao, $this->icone, $this->ordenacao, $this->eGratuito])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar a área.";
            }
        }

        private function editarArea(){
            if($this->editar("areas", "designacaoArea, instituicao, icone, ordenacao, eGratuito", [$this->designacaoArea, $this->instituicao, $this->icone, $this->ordenacao, $this->eGratuito], ["idPArea"=>$this->idPArea])=="sim"){
                $this->listar();   
            }else{
                echo "FNão foi possível editar os dados do curso.";
            }
        }

        private function excluirArea(){
            if($this->excluir("areas", ["idPArea"=>$this->idPArea])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir o curso.";
            } 
        }
        private function listar(){
            if($this->baseDeDados=="escola"){
                echo $this->selectJson("areas", [], [], [], "", [], array("designacaoArea"=>1));
            }
        }         
    }
    new manipulacaoDadosDoAjaxInterno("teste");
    new manipulacaoDadosDoAjaxInterno("escola");
?>
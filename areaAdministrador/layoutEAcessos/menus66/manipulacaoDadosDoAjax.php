<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($baseDeDados){
            parent::__construct();
            $this->idPMenu = isset($_POST["idPMenu"])?$_POST["idPMenu"]:"";
            $this->designacaoMenu = isset($_POST["designacaoMenu"])?$_POST["designacaoMenu"]:"";
            $this->linkMenu = isset($_POST["linkMenu"])?$_POST["linkMenu"]:"";
            $this->instituicao = isset($_POST["instituicao"])?$_POST["instituicao"]:"";
            $this->icone = isset($_POST["icone"])?$_POST["icone"]:"";
            $this->eGratuito = isset($_POST["eGratuito"])?$_POST["eGratuito"]:"";
            $this->idAreaEspecifica = isset($_POST["idAreaEspecifica"])?$_POST["idAreaEspecifica"]:"";
            $this->idPArea = isset($_POST["idPArea"])?$_POST["idPArea"]:"";
            $this->ordemPorDefeito = isset($_POST["ordemPorDefeito"])?$_POST["ordemPorDefeito"]:"";
            $this->somenteOnline = isset($_POST["somenteOnline"])?$_POST["somenteOnline"]:"";

            $this->baseDeDados=$baseDeDados;
            $this->conDb($baseDeDados, true);

            $this->areaEspecifica = $this->selectUmElemento("areas", "designacaoArea", ["idPArea"=>$this->idAreaEspecifica]);

            $this->idAreaPorDefeito = isset($_POST["idAreaPorDefeito"])?$_POST["idAreaPorDefeito"]:"";
            $this->identificadorMenu = isset($_POST["identificadorMenu"])?$_POST["identificadorMenu"]:"";
            $this->areaPorDefeito = $this->selectUmElemento("areas", "designacaoArea", ["idPArea"=>$this->idAreaPorDefeito]);


            if($this->accao=="editarMenu"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->editarMenu();
                }
            }else if($this->accao=="novoMenu"){

                if($this->verificacaoAcesso->verificarAcesso(0)){
                    $this->novoMenu();
                }
            }else if ($this->accao=="excluirMenu"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->excluirMenu();
                }
            }
        }

        private function novoMenu(){
            if($this->inserir("menus", "idPMenu", "eGratuito, designacaoMenu, linkMenu, instituicao, icone, idAreaEspecifica, idAreaPorDefeito, areaPorDefeito, areaEspecifica, identificadorMenu, ordemPorDefeito, somenteOnline", [$this->eGratuito, $this->designacaoMenu, $this->linkMenu, $this->instituicao, $this->icone, $this->idAreaEspecifica, $this->idAreaPorDefeito, $this->areaPorDefeito, $this->areaEspecifica, $this->identificadorMenu, $this->ordemPorDefeito, $this->somenteOnline])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar o menu.";
            }
        }
        private function editarMenu(){
            if($this->editar("menus", "eGratuito, designacaoMenu, linkMenu, instituicao, icone, idAreaEspecifica, idAreaPorDefeito, areaPorDefeito, areaEspecifica, identificadorMenu, ordemPorDefeito, somenteOnline", [$this->eGratuito, $this->designacaoMenu, $this->linkMenu, $this->instituicao, $this->icone, $this->idAreaEspecifica, $this->idAreaPorDefeito, $this->areaPorDefeito, $this->areaEspecifica, $this->identificadorMenu, $this->ordemPorDefeito, $this->somenteOnline], ["idPMenu"=>$this->idPMenu])=="sim"){
                $this->listar(); 
            }else{
                echo "FNão foi possível editar os dados do curso.";
            }
        }

        private function excluirMenu(){
            if($this->excluir("menus", ["idPMenu"=>$this->idPMenu])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir o curso.";
            }
        }
        private function listar(){
            if($this->idPArea!=0){
                $condicao =["idAreaPorDefeito"=>$this->idPArea];
            }else{
                $condicao=array();
            }
            if($this->baseDeDados=="escola"){
                echo $this->selectJson("menus", [], $condicao, [], "", [], array("ordemPorDefeito"=>1));
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno("teste");
    new manipulacaoDadosDoAjaxInterno("escola");
?>
<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($tipoBaseDados){
            parent::__construct();
            $this->idPMenu = isset($_POST["idPMenu"])?$_POST["idPMenu"]:"";
            $this->idPSubMenu = isset($_POST["idPSubMenu"])?$_POST["idPSubMenu"]:"";

            $this->designacaoSubMenu = isset($_POST["designacaoSubMenu"])?$_POST["designacaoSubMenu"]:"";
            $this->linkSubMenu = isset($_POST["linkSubMenu"])?$_POST["linkSubMenu"]:"";
            $this->identificadorSubMenu = isset($_POST["identificadorSubMenu"])?$_POST["identificadorSubMenu"]:"";
            $this->somenteOnline = isset($_POST["somenteOnline"])?$_POST["somenteOnline"]:"";

            $this->tipoBaseDados=$tipoBaseDados;
            $this->conDb($tipoBaseDados, true);

            if($this->accao=="editarMenu"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->editarMenu();
                }
            }else if($this->accao=="novoSubMenu"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                    $this->novoSubMenu();
                }
            }else if ($this->accao=="excluirMenu"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->excluirMenu();
                }
            }
        }

        private function novoSubMenu(){
            if($this->inserirObjecto("menus", "subMenus", "idPSubMenu", "designacaoSubMenu, linkSubMenu, identificadorSubMenu, somenteOnline", [$this->designacaoSubMenu, $this->linkSubMenu, $this->identificadorSubMenu, $this->somenteOnline], ["idPMenu"=>$this->idPMenu])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar o menu.";
            }
        }
        
        private function editarMenu(){
            if($this->editarItemObjecto("menus", "subMenus", "designacaoSubMenu, linkSubMenu, identificadorSubMenu, somenteOnline", [$this->designacaoSubMenu, $this->linkSubMenu, $this->identificadorSubMenu, $this->somenteOnline], ["idPMenu"=>$this->idPMenu], ["idPSubMenu"=>$this->idPSubMenu])=="sim"){
                $this->listar(); 
            }else{
                echo "FNão foi possível editar os dados do curso.";
            }
        }

        private function excluirMenu(){
            if($this->excluirItemObjecto("menus", "subMenus", ["idPMenu"=>$this->idPMenu], ["idPSubMenu"=>$this->idPSubMenu])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir o curso.";
            }
        }
        private function listar(){
            if($this->tipoBaseDados=="escola"){
                echo $this->selectJson("menus", [], ["idPMenu"=>$this->idPMenu], ["subMenus"]);
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno("teste");
    new manipulacaoDadosDoAjaxInterno("escola");
?>
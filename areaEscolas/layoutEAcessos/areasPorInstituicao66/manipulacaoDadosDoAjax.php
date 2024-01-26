<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct(){
            parent::__construct();
            if($this->accao=="luzinguLuame"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                  $this->luzinguLuame();
                }
            }
        }

        private function luzinguLuame(){
            $areasMarcadas = isset($_GET["areasMarcadas"])?$_GET["areasMarcadas"]:array();

            $this->excluirItemObjecto("areas", "instituicoes", [], ["idEscola"=>$_SESSION['idEscolaLogada']]);
            foreach(json_decode($areasMarcadas) as $area){
                if($area->idPArea!=13 && $area->idPArea!=14){
                    $this->inserirObjecto("areas", "instituicoes", "id", "idEscola, acessos", [$_SESSION['idEscolaLogada'], $area->acessos], ["idPArea"=>$area->idPArea]);
                }
            }
            echo $this->selectJson("areas", [], ["instituicao"=>valorArray($this->sobreEscolaLogada, "tipoInstituicao"), "idPArea"=>array('$nin'=>[13, 14])], [], "", [], array("designacaoArea"=>1));
        }
        
    }
    new manipulacaoDadosDoAjaxInterno();
?>
<?php
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct(){
            parent::__construct();
            if($this->accao=="luzinguLuame"){
                if($this->verificacaoAcesso->verificarAcesso(0)){

                    $this->tipoInstituicao = $this->selectUmElemento("escolas","tipoInstituicao", ["idPEscola"=>$_GET["idPEscola"]]);
                    if($_GET["idPEscola"]==4){
                       $this->conDb("teste", true);
                    }
                  $this->luzinguLuame();
                }
            }
        }

        private function luzinguLuame(){
            $areasMarcadas = isset($_GET["areasMarcadas"])?$_GET["areasMarcadas"]:array();
            $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:"";

            $this->excluirItemObjecto("areas", "instituicoes", [], ["idEscola"=>$idPEscola]);
            foreach(json_decode($areasMarcadas) as $area){
                $this->inserirObjecto("areas", "instituicoes", "id", "idEscola, acessos", [$idPEscola, $area->acessos], ["idPArea"=>$area->idPArea]);
            }

            echo $this->selectJson("areas", [], ["instituicao"=>$this->tipoInstituicao], [], "", [], array("designacaoArea"=>1));
        }

    }
    new manipulacaoDadosDoAjaxInterno();
?>

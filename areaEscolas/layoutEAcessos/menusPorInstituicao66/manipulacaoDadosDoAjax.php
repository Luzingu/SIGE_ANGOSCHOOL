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
            $dadosEnviar = isset($_POST["dadosEnviar"])?$_POST["dadosEnviar"]:array();
            $dadosEnviar = json_decode($dadosEnviar);
            foreach($this->selectArray("areas", ["idPArea"], ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada'], "idPArea"=>array('$nin'=>[7,8])], ["instituicoes"], "", [], array("designacaoArea"=>1)) as $a){
                unset($_SESSION['layout_'.$a["idPArea"]]);
            }

            foreach($dadosEnviar as $area){ 

                
                $alterar="nao";
                if($area->idPArea==1 || $area->idPArea==2 || $area->idPArea==7 || $area->idPArea==8){
                    if($area->idPArea==$area->idAreaEspecifica || $area->idPArea==-1){
                        $alterar="sim";
                    }
                }else if($area->idPArea==-1 || $area->idPArea==$area->idAreaEspecifica || $area->idAreaEspecifica==NULL || $area->idAreaEspecifica=="undefined" || $area->idAreaEspecifica==""){
                    $alterar="sim";
                }
                if($alterar=="sim"){
                    $this->excluirItemObjecto("menus", "instituicoes", ["idPMenu"=>$area->idPMenu], ["idEscola"=>$_SESSION['idEscolaLogada']]);
                    $this->inserirObjecto("menus", "instituicoes", "id", "idEscola, idArea, ordemMenu", [$_SESSION['idEscolaLogada'], $area->idPArea, $area->ordemMenu], ["idPMenu"=>$area->idPMenu]);
                }

            }
            echo $this->selectJson("menus", [], ["instituicao"=>valorArray($this->sobreEscolaLogada, "tipoInstituicao")], [], "", [], array("ordemPorDefeito"=>1));
        }
        
    }
    new manipulacaoDadosDoAjaxInterno();
?>
<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct(){
            parent::__construct();

            if($this->accao=="luzinguLuame"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                    $this->tipoInstituicao = $this->selectUmElemento("escolas","tipoInstituicao", ["idPEscola"=>$_POST["idPEscola"]]);

                    if($_POST["idPEscola"]==4){
                        $this->conDb("teste", true);  
                    }
                  $this->luzinguLuame();
                }
            }
        }
        private function luzinguLuame(){
            $dadosEnviar = isset($_POST["dadosEnviar"])?$_POST["dadosEnviar"]:array();
            $dadosEnviar = json_decode($dadosEnviar);
            $idPEscola = isset($_POST["idPEscola"])?$_POST["idPEscola"]:"";

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
                    $this->excluirItemObjecto("menus", "instituicoes", ["idPMenu"=>$area->idPMenu], ["idEscola"=>$idPEscola]);
                    $this->inserirObjecto("menus", "instituicoes", "id", "idEscola, idArea, ordemMenu", [$idPEscola, $area->idPArea, $area->ordemMenu], ["idPMenu"=>$area->idPMenu]);
                }
            }
            echo $this->selectJson("menus", [], ["instituicao"=>$this->tipoInstituicao], [], "", [], array("ordemPorDefeito"=>1)); 
        }
        
    }
    new manipulacaoDadosDoAjaxInterno();
?>
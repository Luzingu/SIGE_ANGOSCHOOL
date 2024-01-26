<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct(){
            parent::__construct();

            if($this->accao=="luzinguLuame"){
                if($this->verificacaoAcesso->verificarAcesso(0)){
                    if($_GET["idPEscola"]==4){
                      $this->conDb("teste", true);
                    }
                    $this->luzinguLuame();
                }
            }
        }
        private function luzinguLuame(){
            $listaDados = isset($_GET["listaDados"])?$_GET["listaDados"]:array();
            $listaDados = json_decode($listaDados);

            $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:"";

            foreach($listaDados as $p){

                $LUZL="F";
                if($p->luzl==1){
                    $LUZL="V";
                }

                $BACKUP="F";
                if($p->backup==1){
                    $BACKUP="V";
                }
                $this->editarItemObjecto("entidadesprimaria", "escola", "LUZL, BACKUP", [$LUZL, $BACKUP], ["idPEntidade"=>$p->idPEntidade], ["idEntidadeEscola"=>$idPEscola]);
            } 
            
            echo $this->selectJson("entidadesprimaria", ["idPEntidade", "nomeEntidade", "tituloNomeEntidade", "escola.LUZL", "escola.BACKUP", "escola.nivelSistemaEntidade"], ["escola.idEntidadeEscola"=>$idPEscola, "escola.estadoActividadeEntidade"=>"A"], ["escola"], "", [], ["nomeEntidade"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>
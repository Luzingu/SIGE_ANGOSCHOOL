<?php 
    session_start();
     include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
  
        function __construct($caminhoAbsoluto){
            parent::__construct();      

            if($this->accao=="alterarNiveisAcesso"){
                $this->alterarNiveisAcesso();
            }
        }

        private function alterarNiveisAcesso(){
            $this->idPProfessor = $_GET["idPProfessor"];
            $this->acessosActivo = isset($_GET["acessosActivo"])?$_GET["acessosActivo"]:array();
            $this->acessosActivo = json_decode($this->acessosActivo);

            $this->acessosInactivo = isset($_GET["acessosInactivo"])?$_GET["acessosInactivo"]:array();
            $this->acessosInactivo = json_decode($this->acessosInactivo);

            foreach($this->acessosInactivo as $acesso){
                $this->excluirItemObjecto("entidadesprimaria", "acessos", ["idPEntidade"=>$this->idPProfessor], ["idPMenu"=>$acesso->idPMenu, "idEscola"=>7]);
            }

            foreach($this->acessosActivo as $acesso){
                $this->excluirItemObjecto("entidadesprimaria", "acessos", ["idPEntidade"=>$this->idPProfessor], ["idPMenu"=>$acesso->idPMenu, "idEscola"=>7]);
                $this->inserirObjecto("entidadesprimaria", "acessos", "idPNiveis", "idEscola, idPMenu, designacaoMenu", [7, $acesso->idPMenu, $acesso->designacaoMenu], ["idPEntidade"=>$this->idPProfessor]);
            }
            echo json_encode($this->entidades(["idPEntidade", "nomeEntidade", "numeroInternoEntidade", "fotoEntidade", "acessos.idEscola", "acessos.idPMenu", "acessos.designacaoMenu", "classes_aceso.classes", "classes_aceso.idPArea", "classes_aceso.idEscola"]));
        }
}
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
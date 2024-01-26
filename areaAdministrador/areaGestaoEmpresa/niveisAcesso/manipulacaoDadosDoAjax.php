<?php 
	session_start();
	 include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
 
    	function __construct($caminhoAbsoluto){
    		parent::__construct();      

            if($this->accao=="alterarNiveisAcesso"){
                $this->alterarNiveisAcesso();
            }else if($this->accao=="alterarAcessoClasse"){
                $this->alterarAcessoClasse();
            }

    	}

        private function alterarNiveisAcesso(){
            $this->idPProfessor = $_GET["idPProfessor"];
            $this->acessosActivo = isset($_GET["acessosActivo"])?$_GET["acessosActivo"]:array();
            $this->acessosActivo = json_decode($this->acessosActivo);

            $this->acessosInactivo = isset($_GET["acessosInactivo"])?$_GET["acessosInactivo"]:array();
            $this->acessosInactivo = json_decode($this->acessosInactivo);

            foreach($this->acessosInactivo as $acesso){
                $this->excluirItemObjecto("entidadesprimaria", "acessos", ["idPEntidade"=>$this->idPProfessor], ["idPMenu"=>$acesso->idPMenu, "idEscola"=>$_SESSION["idEscolaLogada"]]);
            }

            foreach($this->acessosActivo as $acesso){
                $this->excluirItemObjecto("entidadesprimaria", "acessos", ["idPEntidade"=>$this->idPProfessor], ["idPMenu"=>$acesso->idPMenu, "idEscola"=>$_SESSION["idEscolaLogada"]]);
                $this->inserirObjecto("entidadesprimaria", "acessos", "idPNiveis", "idEscola, idPMenu, designacaoMenu", [$_SESSION["idEscolaLogada"], $acesso->idPMenu, $acesso->designacaoMenu], ["idPEntidade"=>$this->idPProfessor]);
            }
            echo json_encode($this->entidades(["idPEntidade", "nomeEntidade", "numeroInternoEntidade", "fotoEntidade", "acessos.idEscola", "acessos.idPMenu", "acessos.designacaoMenu", "classes_aceso.classes", "classes_aceso.idPArea", "classes_aceso.idEscola"]));
        }

    private function alterarAcessoClasse(){
        $classesSeleccionadas = trim($_GET["classesSeleccionadas"]);
        $idPArea = isset($_GET["idPArea"])?$_GET["idPArea"]:"";
        $idPProfessor = $_GET["idPProfessor"];

        $this->excluirItemObjecto("entidadesprimaria", "classes_aceso", ["idPEntidade"=>$idPProfessor], ["idPArea"=>$idPArea, "idEscola"=>$_SESSION["idEscolaLogada"]]);

        $this->inserirObjecto("entidadesprimaria", "classes_aceso", "idPClasseAcesso", "classes, idClAcEntidade, idPArea, idEscola", [$classesSeleccionadas, $idPProfessor, $idPArea, $_SESSION["idEscolaLogada"]], ["idPEntidade"=>$idPProfessor]);

        echo json_encode($this->entidades(["idPEntidade", "nomeEntidade", "numeroInternoEntidade", "fotoEntidade", "acessos.idEscola", "acessos.idPMenu", "acessos.designacaoMenu", "classes_aceso.classes", "classes_aceso.idPArea", "classes_aceso.idEscola"]));
    }
}
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
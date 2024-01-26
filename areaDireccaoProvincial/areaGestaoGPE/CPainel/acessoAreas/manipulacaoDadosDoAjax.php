<?php 
	session_start();
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/manipulacaoDadosDoAjax.php';
     
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        private $numeroInterno = "";
        private $classe = "";
        private $idPCurso = "";           
    	function __construct($caminhoAbsoluto){
    		parent::__construct();
    		$this->caminhoRetornar = $caminhoRecuar = retornarCaminhoRecuarArquivosPhp(__DIR__);
            
            if($this->accao=="alterarAcesso"){
                if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE"], "", "", "FNão tens permissão de cadastrar um(a) funcionário(a).")){
                    $this->alterarAcesso();
                }else{
                    echo "FNão tens acesso a alterar esses dados.";
                }                
            } 
    	}

        private function alterarAcesso(){
            $idPAcessoArea = $_GET["idPAcessoArea"];
            $alteracao = $_GET["alteracao"];
            $visualizacao = $_GET["visualizacao"];
            $idPEscola = $_GET["idPEscola"];
            
            $this->editar("acessoareas", "acessoVisualizacao, acessoAlteracao", [$visualizacao, $alteracao], "idPAcessoArea=:idPAcessoArea", [$idPAcessoArea]);

            echo $this->selectJson("acessoareas", "*", "idAcessoEscola=:idAcessoEscola", [$idPEscola]);            
        }



}
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>




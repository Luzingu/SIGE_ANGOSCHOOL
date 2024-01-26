<?php 
	session_start();
	include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/manipulacaoDadosDoAjax.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/arquivosComunsEntreAreas/adicionarAgentes.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{


    	function __construct($caminhoAbsoluto){
    		parent::__construct();
    		$this->caminhoRetornar = $caminhoRecuar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    		
            if($this->accao=="pesquisarAgentesNaoOcupados"){
                pesquisarAgentesNaoOcupados($this);
            }else if($this->accao=="adicionarAgente"){
                if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE"], "", "", "FNão tens permissão de adicionar um agente.")){
                    adicionarAgente($this);
                }                
            }
    	}
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
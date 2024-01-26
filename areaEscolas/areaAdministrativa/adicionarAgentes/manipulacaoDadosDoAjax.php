<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/arquivosComunsEntreAreas/adicionarAgentes.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{


    	function __construct($caminhoAbsoluto){
    		parent::__construct();    		
            if($this->accao=="pesquisarAgentesNaoOcupados"){
                pesquisarAgentesNaoOcupados($this);
            }else if($this->accao=="adicionarAgente"){
                if($this->verificacaoAcesso->verificarAcesso("", ["adicionarAgentes"])){
                    adicionarAgente($this);
                }                
            }
    	}
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
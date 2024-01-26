<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/arquivosComunsEntreAreas/adicionarAgentes.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{


    	function __construct(){
    		parent::__construct();
    		
            if($this->accao=="pesquisarAgentesNaoOcupados"){
                pesquisarAgentesNaoOcupados($this);
            }else if($this->accao=="adicionarAgente"){
                if($this->verificacaoAcesso->verificarAcesso("", ["adicionarAgentes00"])){
                    adicionarAgente($this);
                }                
            }
    	}
    }
    new manipulacaoDadosDoAjaxInterno();
?>
<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjaxMae{
    	
    	function __construct(){
    		parent::__construct();
        if($this->accao=="pegarNovasMensagens"){
          $this->pegarNovasMensagens();
        }
    	}
      private function pegarNovasMensagens(){
        $usuarioLogado = $_SESSION['tbUsuario']."_".$_SESSION["idUsuarioLogado"];
        echo $this->selectJson("mensagens", [], ["receptor"=>$usuarioLogado, "estadoMensagem"=>"F"]);
      }    	
    }
    new manipulacaoDadosDoAjaxInterno();
        
?>
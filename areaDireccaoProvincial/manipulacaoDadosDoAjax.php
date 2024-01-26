<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/funcoesAuxiliares.php';

    curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
    curtina($_SESSION["directorioPaterno"].'angoschool/manipulacaoDadosDoAjax.php');
     
    class manipulacaoDadosAjax extends manipulacaoDadosAjaxMae{
        public $accao="";
        public $verificacaoAcesso="";
        public $valorDisponivelAluno;
        private $valorDisponivelEscola=0;
        public $idAdministrativo=0;
        
       function __construct(){
            parent::__construct(__DIR__);        

        }
    }

?>
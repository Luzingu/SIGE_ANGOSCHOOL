<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER["DOCUMENT_ROOT"].'/angoschool/funcoesAuxiliares.php';

    curtina($_SERVER["DOCUMENT_ROOT"].'/angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
    curtina($_SERVER["DOCUMENT_ROOT"].'/angoschool/manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosAjax extends manipulacaoDadosAjaxMae{

        function __construct(){
            parent::__construct(__DIR__);
        }
      
    }

?>
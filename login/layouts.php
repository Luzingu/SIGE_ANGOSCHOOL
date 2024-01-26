<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/manipulacaoDados.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php'; 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/verificadorAcesso.php';
        
    class layouts extends manipulacaoDados {
      
      
    } ?>
<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php';
  curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php');
  curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/htmlsMae.php');

class includarHtmls extends includarHtmlsMae{
   function __construct(){
        parent::__construct();
        
    }
} ?>
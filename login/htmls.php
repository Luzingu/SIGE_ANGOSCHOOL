<?php 
  if(session_status()!==PHP_SESSION_ACTIVE){
    session_start();
  }
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/htmlsMae.php';

class includarHtmls extends includarHtmlsMae{
   function __construct(){
      parent::__construct();
    }
} ?>
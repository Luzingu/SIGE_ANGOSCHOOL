<?php
if(session_status()!==PHP_SESSION_ACTIVE){
  session_start();
}
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/conexaoFolhasMae.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
class conexaoFolhas extends conexaoFolhasMae{
    function __construct(){
      parent::__construct();
    } 
    public function folhasCss(){
        $this->folhasCssMae(); ?>
    <?php } 

    public function folhasJs() {
        //$this->folhasJsMae(); ?>
    <?php  } 
   
   }
		

?>
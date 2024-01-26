<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php';

  class manipulacaoDados extends manipulacaoDadosMae{
    public $idAnoActualPesquisada=""; 
    public $numAnoActualPesquisada="";
    function __construct(){
      parent::__construct();
    }
}

?>
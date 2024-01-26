<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php';

  class manipulacaoDados extends manipulacaoDadosMae{
    public $idAnoActualPesquisada=""; public $numAnoActualPesquisada="";
    private $caminhoRetornar="";

    function __construct($areaVisualizada="", $identMenu=""){
         parent::__construct($areaVisualizada, $identMenu);
    }
}

?>
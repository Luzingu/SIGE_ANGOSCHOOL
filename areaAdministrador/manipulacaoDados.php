<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php';

    class manipulacaoDados extends manipulacaoDadosMae{
      function __construct($areaVisualizada="", $identMenu=""){
          $this->idAnoActual = null;
          $this->numAnoActual = null;
          $this->codigoTurma = null;
          parent::__construct($areaVisualizada, $identMenu);
      }
      
    }

?>
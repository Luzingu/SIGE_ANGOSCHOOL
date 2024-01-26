<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php';
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php');
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliaresDb.php';

    class funcoesAuxiliares extends funcoesAuxiliaresMae{
        function __construct($areaVisualizada=""){
            ini_set('memory_limit', '500M');
            parent::__construct($areaVisualizada);            
        }

    }

?>
<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->conDb("entretenimento", true);
            if($this->accao=="salvarLivro"){
                $this->salvarLivro();
            }else if ($this->accao=="excluirLivro"){
                $this->excluirLivro();
            }
        }

        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
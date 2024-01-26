<?php 
  session_start();
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/arquivosComunsEntreAreas/listaAgentes.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

      function __construct($caminhoAbsoluto){
        parent::__construct();
        $manipuladorDadosAgente = new manipuladorDadosAgente($this);

        if($this->accao=="adicionarAgente"){
          if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes11"])){
            $manipuladorDadosAgente->adicionarAgente();
          }else{
            echo "FNão tens permissão de adicionar um agente.";
          }
        }else if($this->accao=="editarAgente"){
          if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes11"])){
            $manipuladorDadosAgente->editarAgente();
          }else{
            echo "FNão tens permissão de alterar os dados.";
          }

        }else if($this->accao=="excluirAgente"){
          if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes11"])){
            $manipuladorDadosAgente->excluirAgente();
          }else{
            echo "FNão tens permissão de excluir o agente.";
          }
        }
      }      
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
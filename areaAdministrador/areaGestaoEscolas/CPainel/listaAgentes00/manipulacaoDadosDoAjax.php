<?php 
  session_start();
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/arquivosComunsEntreAreas/listaAgentes.php';
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

      function __construct(){
        parent::__construct();
        $manipuladorDadosAgente = new manipuladorDadosAgente($this);

        if($this->accao=="adicionarAgente"){
          if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes00"])){
            $manipuladorDadosAgente->adicionarAgente();
          }else{
            echo "FNão tens permissão de adicionar um agente.";
          }
        }else if($this->accao=="editarAgente"){
          if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes00"])){
            $manipuladorDadosAgente->editarAgente();
          }else{
            echo "FNão tens permissão de alterar os dados.";
          }

        }else if($this->accao=="excluirAgente"){
          if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes00"])){
            $manipuladorDadosAgente->excluirAgente();
          }else{
            echo "FNão tens permissão de excluir o agente.";
          }
        }
      }      
    }
    new manipulacaoDadosDoAjaxInterno();
?>
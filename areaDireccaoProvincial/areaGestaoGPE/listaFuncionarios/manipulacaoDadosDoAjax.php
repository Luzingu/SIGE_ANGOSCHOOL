<?php 
  session_start();
  include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
  include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/manipulacaoDadosDoAjax.php';
  include_once $_SESSION["directorioPaterno"].'angoschool/arquivosComunsEntreAreas/listaAgentes.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

      function __construct($caminhoAbsoluto){
        parent::__construct();
        $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);

        $manipuladorDadosAgente = new manipuladorDadosAgente($this, $this->caminhoRetornar);

        if($this->accao=="adicionarAgente"){

          if(substr(valorArray($this->sobreUsuarioLogado, "numeroInternoEntidade"), 0, 12)=="0000ANGOS200"){
            echo "FAs contas de teste não podem adicionar um agente.";

          }else if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE", "aAdministrativa", "aPedagogica"], "dadosAgentes")){
            $manipuladorDadosAgente->adicionarAgente();
          }else{
            echo "FNão tens permissão de adicionar um agente.";
          }
        }else if($this->accao=="editarAgente"){

          if(substr(valorArray($this->sobreUsuarioLogado, "numeroInternoEntidade"), 0, 12)=="0000ANGOS200"){
            echo "FAs contas de teste não podem editar dados dum agente.";
          }else if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE", "aAdministrativa", "aPedagogica"], "dadosAgentes")){
            $manipuladorDadosAgente->editarAgente();
          }else{
            echo "FNão tens permissão de alterar os dados.";
          }

        }else if($this->accao=="excluirAgente"){

          if(substr(valorArray($this->sobreUsuarioLogado, "numeroInternoEntidade"), 0, 12)=="0000ANGOS200"){
            echo "FAs contas de teste não podem excluir dum agente.";
          }else if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE", "aAdministrativa", "aPedagogica"], "dadosAgentes")){
            $manipuladorDadosAgente->excluirAgente();
          }else{
            echo "FNão tens permissão de excluir o agente.";
          }
        }
      }      
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
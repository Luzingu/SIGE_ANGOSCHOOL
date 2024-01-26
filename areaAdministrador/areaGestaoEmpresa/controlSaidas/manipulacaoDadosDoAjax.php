<?php 
  session_start();
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
     
  class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
    function __construct($caminhoAbsoluto){
      parent::__construct();
      $this->caminhoRetornar = "../../../";

      $this->descricaoSaida = isset($_POST['descricaoSaida'])?$_POST['descricaoSaida']:"";
      $this->valor = isset($_POST['valor'])?$_POST['valor']:0;
      $this->idPSaida = isset($_POST['idPSaida'])?$_POST['idPSaida']:0;
      $this->anoCivil = isset($_POST['anoCivil'])?$_POST['anoCivil']:0;
      $this->mesPagamento = isset($_POST['mesPagamento'])?$_POST['mesPagamento']:0;

      if($this->accao=="novaSaida"){
        if($this->verificacaoAcesso->verificarAcesso("", ["controlSaidas"])){
            $this->novaSaida();
        }
      }else if($this->accao=="excluirSaida"){
        if($this->verificacaoAcesso->verificarAcesso("", ["controlSaidas"])){
            $this->excluirSaida();
        }
      }
    }
    private function excluirSaida(){
      $array = $this->selectArray("saidas_luzl", [], ["idPSaida"=>$this->idPSaida]);

      if (valorArray($array, "dataSaida") != $this->dataSistema)
        echo "FNão podes anular esta saída.";
      if($this->excluir("saidas_luzl", ["idPSaida"=>$this->idPSaida]) == "sim")
        $this->listar();
      else
        echo "FNão foi possível excluir a saída.";
    }

    private function novaSaida(){
      
      $factura = $this->upload("factura", "arquivo_".$this->dia.$this->mes.$this->ano.date("H").date("s").date("i"), "Ficheiros/Escola_7/Facturas", "../../../", "", "", "");

      if ($this->inserir("saidas_luzl", "idPSaida", "idPFuncionario, nomeFuncionario, dataSaida, horaSaida, descricaoSaida, valor, factura", [$_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $this->dataSistema, $this->tempoSistema, $this->descricaoSaida, $this->valor, $factura]) == "sim")
      {
          $this->listar();
      }
      else
      {
        echo "FNão foi possível adicionar a saída.";
      }
    }

    private function listar()
    {
        echo $this->selectJson("saidas_luzl", [], ["dataSaida"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-")]);
    }
  }
  new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
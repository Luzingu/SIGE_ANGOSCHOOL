<?php 
  session_start();
  
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

      function __construct($caminhoAbsoluto){
        parent::__construct();

        $this->anoCivil = isset($_POST["anoCivil"])?$_POST["anoCivil"]:"";
        $this->mesPagamento = isset($_POST["mesPagamento"])?$_POST["mesPagamento"]:"";

        if($this->accao=="manipularSaida"){
          if($this->verificacaoAcesso->verificarAcesso("", ["saidasFinanceiras"])){
            $this->manipularSaida();
          } 
        }else if($this->accao==="pegarSaldoDisponivel"){
          $this->pegarSaldoDisponivel();
        }else if($this->accao=="cancelarSaida"){
          if($this->verificacaoAcesso->verificarAcesso("", ["saidasFinanceiras"])){
            $this->cancelarSaida();
          }
        }
      }

      private function cancelarSaida(){
        $motivoCancelamento = isset($_POST['motivoCancelamento'])?$_POST['motivoCancelamento']:"";
        $idPFactura = isset($_POST['idPFactura'])?$_POST['idPFactura']:"";

        $sobreFactura = $this->selectArray("facturas", [], ["idPFactura"=>$idPFactura, "idFacturaEscola"=>$_SESSION['idEscolaLogada']]);

        if(valorArray($sobreFactura, "estadoFactura")!="A" || valorArray($sobreFactura, "dataEmissao")!=$this->dataSistema){
          echo "FNão podes anular a factura.";
        }else{
          $this->editar("facturas", "estadoFactura, motivoCancelamento", ["I", $motivoCancelamento], ["idPFactura"=>$idPFactura]);

          $contaUsada="";
          foreach(listarItensObjecto($sobreFactura, "itens", []) as $a){
            $contaUsada = $a["contaUsada"];
            $this->actualizarContaInstituicao($a["contaUsada"], floatval($a["precoUnitario"])*floatval($a["quantidade"]), valorArray($sobreFactura, "identificador"));
          }
          $identificador = $this->dia.$this->mes.$this->ano.date("H").date("s").date("i").substr(str_shuffle("1234567890"),0, 3).$_SESSION['idEscolaLogada'];

          $this->inserir("facturas", "idPFactura", "idFacturaEscola, dataEmissao, horaEmissao, numeroFacturaAnulada, numeroFactura, idPParceira, nomeEmpresa, nifEmpresa, idFuncionario, nomeFuncionario, referenciaFactura, identificador, hash, tipoFactura, estadoFactura, motivoCancelamento, valorTotal", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, valorArray($sobreFactura, "numeroFactura"), $this->numeroFactura("RP/AN"), valorArray($sobreFactura, "idPParceira"), valorArray($sobreFactura, "nomeEmpresa"), valorArray($sobreFactura, "nifEmpresa"), $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), valorArray($sobreFactura, "identificador"), $identificador, md5($identificador), "RP/AN", "A", $motivoCancelamento, valorArray($sobreFactura, "valorTotal")]);

          $this->actualizarContaInstituicao($contaUsada, 0, $identificador);
          $this->listar();
        }
      }
      private function manipularSaida(){
        $idPParceira=isset($_POST['idPParceira'])?$_POST['idPParceira']:"";
        $idPItem=isset($_POST['idPItem'])?$_POST['idPItem']:"";
        $valorUnitario=isset($_POST['valorUnitario'])?$_POST['valorUnitario']:"";
        $quantidade=isset($_POST['quantidade'])?$_POST['quantidade']:"";
        $contaUsar=isset($_POST['contaUsar'])?$_POST['contaUsar']:"";


        $arrayParceira = listarItensObjecto($this->sobreEscolaLogada, "empresa_parceiras", ["idPParceira=".$idPParceira]);
        $nomeEmpresa = valorArray($arrayParceira, "nomeEmpresa");
        if($idPParceira=="0"){
          $nomeEmpresa="Desconhecida";
        }

        $arrayItem = listarItensObjecto($this->sobreEscolaLogada, "itens_financeiros", ["idPItem=".$idPItem]);

        $valorDisponivel = floatval(valorArray($this->sobreEscolaLogada, $contaUsar));
        if($valorDisponivel<($valorUnitario*$quantidade)){
          echo "FA conta financeira não possuí valor suficiente para processar este pagamento.";
        }else{
          $identificador = $this->selectUmElemento("facturas", "identificador", ["idFacturaEscola"=>$_SESSION['idEscolaLogada'], "tipoFactura"=>"RP", "idPParceira"=>$idPParceira, "dataEmissao"=>$this->dataSistema, "estadoFactura"=>"A"]);
          if($identificador=="" || $identificador==null){
            $identificador = $this->dia.$this->mes.$this->ano.date("H").date("s").date("i").substr(str_shuffle("1234567890"),0, 3).$_SESSION['idEscolaLogada'];

            $this->inserir("facturas", "idPFactura", "idFacturaEscola, dataEmissao, horaEmissao, numeroFactura, idPParceira, nomeEmpresa, nifEmpresa, idFuncionario, nomeFuncionario, identificador, hash, tipoFactura, estadoFactura", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, $this->numeroFactura("RP"), $idPParceira, $nomeEmpresa, valorArray($arrayParceira, "nifEmpresa"), $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $identificador, md5($identificador), "RP", "A"]);
          }
          $this->inserirObjecto("facturas", "itens", "idPItem", "contaUsada, idItem, descricaoItem, precoUnitario, unidade, quantidade, desconto, taxa, estadoItem", [$contaUsar, $idPItem, valorArray($arrayItem, "designacaoItem"), $valorUnitario, "", $quantidade, 0, 0, "I"], ["identificador"=>$identificador]);

          $this->totalizadorItensFactura($identificador);

          $this->actualizarContaInstituicao($contaUsar, -1*$valorUnitario*$quantidade, $identificador);
          $this->listar();
        }
      }

      private function pegarSaldoDisponivel(){
        $this->tipoConta = isset($_GET["tipoConta"])?$_GET["tipoConta"]:"";
        echo valorArray($this->sobreEscolaLogada, $this->tipoConta);
      }
      
      private function listar(){
        echo $this->selectJson("facturas", [], ["idFacturaEscola"=>$_SESSION['idEscolaLogada'], "tipoFactura"=>"RP", "dataEmissao"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-")], [], "", [], ["idPFactura"=>-1]);
      }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>

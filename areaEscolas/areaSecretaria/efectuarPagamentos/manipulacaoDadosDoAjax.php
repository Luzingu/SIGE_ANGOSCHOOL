<?php 
  session_start();
  
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{


      function __construct($caminhoAbsoluto){
        parent::__construct();
          $this->caminhoRetornar = "../../../";

          if($this->accao=="pegarPagarPagamentosJaEfectuados"){
            $this->pegarPagarPagamentosJaEfectuados();
          }else if($this->accao=="listar"){
            $this->listar();
          }else if($this->accao=="efectuarPagamento"){

            $this->idPMatricula = $_POST["idPMatricula"];
            $this->grupo = $_POST["grupo"];
            $this->idPTipoEmolumento = $_POST["idPTipoEmolumento"];
            $this->contaUsar = $_POST["contaUsar"];
            $this->meioPagamento = $_POST["meioPagamento"];
            $this->dataPagamento = $_POST["dataPagamento"];

            $this->descricaoConta = $this->selectUmElemento("contas_bancarias", "descricaoConta", ["idPContaFinanceira"=>$this->contaUsar]);

            $this->valorPago = $_POST["valorPago"];
            $this->referenciaPagamento = $_POST["referenciaPagamento"];

            if($this->verificacaoAcesso->verificarAcesso("", ["efectuarOutrosPagamentos"])){
              $this->efectuarPagamento();
            }
          }
      }

        private function efectuarPagamento(){

          $nomeCliente = isset($_POST["nomeCliente"])?$_POST["nomeCliente"]:"";
          $nifCliente = isset($_POST["nifCliente"])?$_POST["nifCliente"]:"";

          $array = $this->selectArray("tipos_emolumentos", [], ["idPTipoEmolumento"=>$this->idPTipoEmolumento]);

          $podeEfect="sim";          
          if(valorArray($array, "tipoPagamento")!="pagAberto"){
            if(count($this->selectArray("alunos_".$this->grupo, ["idPMatricula"], ["idPMatricula"=>$this->idPMatricula, "pagamentos.idHistoricoAno"=>$this->idAnoActual, "pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.idTipoEmolumento"=>$this->idPTipoEmolumento, "pagamentos.referenciaPagamento"=>$this->referenciaPagamento], ["pagamentos"], 1))>0){
              $podeEfect="nao";
            }
          }
          if($podeEfect=="nao"){
            echo "FO aluno já efectuou o pagamento deste emolumento.";
          }else{ 
            
              $identificacaoUnica = $this->selectUmElemento("payments", "identificacaoUnica", ["idDocEscola"=>$_SESSION['idEscolaLogada'], "tipoDocumento"=>"RC", "nifCliente"=>$nifCliente, "dataEmissao"=>$this->dataSistema, "identificadorCliente"=>$this->idPMatricula, "estadoDocumento"=>"N"]);

              if($identificacaoUnica=="" || $identificacaoUnica==null){

                $identificacaoUnica=$this->identificacaoUnica("payments", "RC"); 

                $this->inserir("payments", "idPDocumento", "idDocEscola, dataEmissao, horaEmissao, identificadorCliente, codigoContaCliente, nifCliente, nomeCliente, nomeEmpresaCliente, enderecoDetalhadoCliente, cidadeCliente, idFuncionario, nomeFuncionario, identificacaoUnica, numeroSequencial, tipoDocumento, estadoDocumento, serieDocumento, hash", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, $this->idPMatricula, "Desconhecido", $nifCliente, $nomeCliente, "Consumidor final", "Desconhecido", "Consumidor Final", valorArray($this->sobreUsuarioLogado, "idPEntidade"), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $identificacaoUnica, $this->numeroSequencial, "RC", "N", valorArray($this->sobreEscolaLogada, "serieFactura"), $this->assinaturaDigital]);
              }

              $this->inserirObjecto("alunos_".$this->grupo, "pagamentos", "idPHistoricoConta", "idHistoricoEscola, idHistoricoAno, dataPagamento, horaPagamento, idHistoricoMatricula, idHistoricoFuncionario, nomeFuncionario, idTipoEmolumento, codigoEmolumento, designacaoEmolumento, referenciaPagamento, precoInicial, precoMulta , precoDesconto, precoPago, estadoPagamento, idPTipoConta, descricaoConta, identFactura", [$_SESSION['idEscolaLogada'], $this->idAnoActual, $this->dataSistema, $this->tempoSistema,  $this->idPMatricula, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $this->idPTipoEmolumento, valorArray($array, "codigo"), valorArray($array, "designacaoEmolumento"), $this->referenciaPagamento, $this->valorPago, 0, 0, $this->valorPago, "I", $this->contaUsar, $this->descricaoConta, $identificacaoUnica], ["idPMatricula"=>$this->idPMatricula]);

              
              $idPHistoricoConta = valorArray($this->selectArray("alunos_".$this->grupo, ["pagamentos.idPHistoricoConta"], ["idPMatricula"=>$this->idPMatricula, "pagamentos.dataPagamento"=>$this->dataSistema, "pagamentos.horaPagamento"=>$this->tempoSistema, "pagamentos.idTipoEmolumento"=>$this->idPTipoEmolumento], ["pagamentos"]), "idPHistoricoConta", "pagamentos");

              $this->inserirObjecto("payments", "itens", "idPItem", "idPHistoricoConta, idPTipoConta, descricaoConta, indicadorProduto, idProduto, codigoProduto, descricaoProduto, referenciaPagamento, precoUnitario, unidade, quantidade, desconto, taxaIVA, valorIVA, valorTotSemImposto, valorTotComImposto, meioPagamento, dataPagamento, estadoItem", [$idPHistoricoConta, $this->contaUsar, $this->descricaoConta, "S", $this->idPTipoEmolumento, valorArray($array, "codigo"), valorArray($array, "designacaoEmolumento"), $this->referenciaPagamento, $this->valorPago, "UM", 1, 0, 0, 0, $this->valorPago, $this->valorPago, $this->meioPagamento, $this->dataPagamento, "I"], ["identificacaoUnica"=>$identificacaoUnica, "idDocEscola"=>$_SESSION['idEscolaLogada']]);

              $this->totalizadorItensDocumentos("payments", $identificacaoUnica);

              $this->manipularConta($this->contaUsar, "C", $this->valorPago);
          }

        }

        private function listar(){

          $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";
          $idPTipoEmolumento = $_GET["idPTipoEmolumento"];

          $condicoesPesquisa = [array("nomeAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("biAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("numeroInterno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("nomeAluno"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("nomeAluno"=>ucwords($valorPesquisado))];

          $alunos = $this->selectArray("alunosmatriculados", ["pagamentos.referenciaPagamento", "grupo", "pagamentos.idHistoricoAno", "biAluno", "pagamentos.idHistoricoEscola", "pagamentos.precoPago", "pagamentos.idTipoEmolumento", "pagamentos.idPHistoricoConta", "nomeAluno", "numeroInterno", "escola.seBolseiro", "escola.beneficiosDaBolsa", "idPMatricula", "escola.classeActualAluno", "escola.idMatCurso", "escola.periodoAluno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>['$in'=>array("A", "Y")], '$or'=>$condicoesPesquisa], ["escola"], 20, [], ["nomeAluno"=>1]);
          $alunos = $this->anexarTabela2($alunos, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");

          foreach($alunos as $a){
            $pagamentos = listarItensObjecto($a, "pagamentos", ["idHistoricoAno=".$this->idAnoActual, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "precoPago>0", "idTipoEmolumento=".$idPTipoEmolumento]);
            $a->seJaTemPagamento="";

            if(count($pagamentos)>0){
              $a->seJaTemPagamento = valorArray($pagamentos, "idPHistoricoConta");
            } 
          }

          echo json_encode($alunos);
        }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
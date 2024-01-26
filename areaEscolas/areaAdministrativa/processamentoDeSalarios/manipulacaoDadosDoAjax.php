<?php 
  session_start();
  
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

      function __construct($caminhoAbsoluto){
        parent::__construct();

        $this->anoCivil = isset($_POST["anoCivil"])?$_POST["anoCivil"]:"";
        $this->mesPagamento = isset($_POST["mesPagamento"])?$_POST["mesPagamento"]:"";
        $this->tipoConta = isset($_POST["tipoConta"])?$_POST["tipoConta"]:"";
        $this->dataDePagamento = isset($_POST["dataDePagamento"])?$_POST["dataDePagamento"]:"";

        if($this->accao=="excluirProcessamento"){
          if($this->verificacaoAcesso->verificarAcesso("", ["processamentoDeSalarios"])){
            $this->excluirProcessamento();
          } 
        }else if($this->accao=="processarSalario"){
          if($this->verificacaoAcesso->verificarAcesso("", ["processamentoDeSalarios"])){
            $this->processarSalario();
          } 
        }else if($this->accao==="pegarSaldoDisponivel"){
          $this->pegarSaldoDisponivel();
        }else if($this->accao=="pegarProfessores"){

          if($this->verificacaoAcesso->verificarAcesso("", ["processamentoDeSalarios"])){
            $this->pegarProfessores();
          }
        }
      }

      private function processarSalario(){
        $idPEntidade = isset($_POST["funcionario"])?$_POST["funcionario"]:0;

        $cargaHoraria = isset($_POST["cargaHoraria"])?$_POST["cargaHoraria"]:0;
        $tempoTotLeccionado = isset($_POST["tempoTotLeccionado"])?$_POST["tempoTotLeccionado"]:0;
        $tempoTotNaoLeccionado = isset($_POST["tempoTotNaoLeccionado"])?$_POST["tempoTotNaoLeccionado"]:0;
        $totalSubsidios = isset($_POST["totalSubsidios"])?$_POST["totalSubsidios"]:0;

        $outrosDescontos = isset($_POST["outrosDescontos"])?$_POST["outrosDescontos"]:0;
        $IRT = isset($_POST["IRT"])?$_POST["IRT"]:0;
        $segurancaSocial = isset($_POST["segurancaSocial"])?$_POST["segurancaSocial"]:0;
        $this->tipoConta = isset($_POST["contaUsar"])?$_POST["contaUsar"]:0;

        $this->descricaoConta = $this->selectUmElemento("contas_bancarias", "descricaoConta", ["idPContaFinanceira"=>$this->tipoConta]);

        $array = $this->selectArray("entidadesprimaria", ["escola.valorAuferidoNaInstituicao", "escola.pagamentoPorTempo"], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "idPEntidade"=>$idPEntidade], ["escola"]);

        $salarioTotal = floatval(valorArray($array, "valorAuferidoNaInstituicao", "escola"))+
        floatval(valorArray($array, "pagamentoPorTempo", "escola"))*intval($tempoTotLeccionado)+$totalSubsidios-$outrosDescontos-$IRT-$segurancaSocial;

        if(count($this->selectArray("entidadesprimaria", ["idPEntidade"], ["salarios.anoCivil"=>$this->anoCivil, "salarios.mesPagamento"=>$this->mesPagamento, "idPEntidade"=>$idPEntidade], ["salarios"]))>0){
            echo "FO funcionário já foi pago.";
        }else{

          $this->inserirObjecto("entidadesprimaria", "salarios", "idPSalario", "idEscola, anoCivil, mesPagamento, valorAuferidoNaInstituicao, pagamentoPorTempo, cargaHoraria, tempoTotLeccionado, tempoTotNaoLeccionado, totalSubsidios, IRT, segurancaSocial, outrosDescontos, salarioLiquido, contaDebitada, dataPagamento, horaPagamento, idFuncionarioProc, nomeFuncProc", [$_SESSION['idEscolaLogada'], $this->anoCivil, $this->mesPagamento, valorArray($array, "valorAuferidoNaInstituicao", "escola"), valorArray($array, "pagamentoPorTempo", "escola"), $cargaHoraria, $tempoTotLeccionado, $tempoTotNaoLeccionado, $totalSubsidios, $IRT, $segurancaSocial, $outrosDescontos, $salarioTotal, $this->tipoConta, $this->dataSistema, $this->tempoSistema, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade")], ["idPEntidade"=>$idPEntidade]);

          $lamborne = $this->selectArray("entidadesprimaria", ["salarios.idPSalario"], ["idPEntidade"=>$idPEntidade, "salarios.anoCivil"=>$this->anoCivil, "salarios.mesPagamento"=>$this->mesPagamento, "salarios.idEscola"=>$_SESSION['idEscolaLogada']], ["salarios"]);

          $idPSalario = valorArray($lamborne, "idPSalario", "salarios");
          $this->inserir("general_ledger_entries", "idPDocumento", "idDocEscola, dataEmissao, horaEmissao, descricaoMovimento, numArquivoDocumento, tipoMovimento, dataMovimentoContabilistico, identificadorDiario, descricaoDiario, movimento, contaLinha, descricaoContaLiha, valorLinha, descricaoLinhha, idHistoricoFuncionario, nomeFuncionario, idPSalario, sePagSalario", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, "Pagamento de Salário", "", "N", $this->dataDePagamento, "", ".asa", "Credito", $this->tipoConta, $this->descricaoConta, $salarioTotal, "Pagamento de Salário", $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $idPSalario, "A"]);

          $this->manipularConta($this->tipoConta, "D", $salarioTotal);
          
          $this->listar();
        }
      }
      private function excluirProcessamento(){
        $idPFuncionario = isset($_POST["idPFuncionario"])?$_POST["idPFuncionario"]:"";
        $idPSalario = isset($_POST["idPSalario"])?$_POST["idPSalario"]:"";
        $motivoCancelamento = isset($_POST["motivoCancelamento"])?$_POST["motivoCancelamento"]:"";

        $array = $this->selectArray("entidadesprimaria", ["idPEntidade", "nomeEntidade", "salarios.salarioLiquido", "salarios.idPSalario", "salarios.dataPagamento", "salarios.horaPagamento", "salarios.nomeFuncProc", "salarios.contaDebitada"], ["salarios.idPSalario"=>$idPSalario, "idPEntidade"=>$idPFuncionario], ["salarios"]);

        if(valorArray($array, "dataPagamento", "salarios")!=$this->dataSistema){
          echo "FNão podes anular este pagamento.";
        }else{
            if($this->excluirItemObjecto("entidadesprimaria", "salarios", ["idPEntidade"=>$idPFuncionario], ["idPSalario"=>$idPSalario])=="sim"){

              $this->excluir("general_ledger_entries", ["idDocEscola"=>$_SESSION['idEscolaLogada'], "idPSalario"=>$idPSalario]);

              $this->manipularConta(valorArray($array, "contaDebitada", "salarios"), "C", valorArray($array, "salarioLiquido", "salarios"));

              $this->listar();        
            }else{
              echo "FNão foi possível anular o pagamento.";
            }
        }

      }
      private function pegarProfessores(){
        $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:"";
        $this->mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:"";


        $this->horario = $this->selectArray("horario", ["dia", "idPEntidade"], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idHorAno"=>$this->idAnoActual]);

        $arrayEntidades=array();
        $i=0;
        foreach ($this->selectArray("entidadesprimaria", ["idPEntidade", "nomeEntidade", "escola.valorAuferidoNaInstituicao", "escola.pagamentoPorTempo", "salarios.anoCivil", "salarios.mesPagamento", "controlPresenca.idEscola", "contadorFaltas.idEscola", "contadorFaltas.mes", "contadorFaltas.anoCivil", "contadorFaltas.tempoTotLeccionado", "contadorFaltas.tempoTotNaoLeccionado"], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A"], ["escola"]) as $entidade){
          
          $contadorFaltas = listarItensObjecto($entidade, "contadorFaltas", ["anoCivil=".$this->anoCivil, "mes=".$this->mesPagamento, "idEscola=".$_SESSION['idEscolaLogada']]);

          if(count(listarItensObjecto($entidade, "salarios", ["anoCivil=".$this->anoCivil, "mesPagamento=".$this->mesPagamento]))<=0 && (nelson($entidade, "valorAuferidoNaInstituicao", "escola")>0 || nelson($entidade, "pagamentoPorTempo", "escola")>0)){
 
            $arrayEntidades[]=$entidade;

            $arrayEntidades[$i]["pagamentoPorTempo"]=floatval(valorArray($entidade, "pagamentoPorTempo", "escola"));

            $arrayEntidades[$i]["cargaHoraria"]=$this->contadorTempo($entidade["idPEntidade"]);
            $arrayEntidades[$i]["tempoTotLeccionado"]=intval(valorArray($contadorFaltas, "tempoTotLeccionado"));
            $arrayEntidades[$i]["tempoTotNaoLeccionado"]=intval(valorArray($contadorFaltas, "tempoTotNaoLeccionado"));
            $i++;
          }
        } 
        echo json_encode($arrayEntidades);
      }

      private function contadorTempo ($idPEntidade){
        $totTempos=0;

        $ultimoDia = date("t", strtotime($this->anoCivil."-".$this->mesPagamento."-01"));

        for($i=1; $i<=$ultimoDia; $i++){
          $semana = date("w", strtotime($this->anoCivil."-".$this->mesPagamento."-".completarNumero($i)));

          $totTempos += count(array_filter($this->horario, function ($m) use ($idPEntidade, $semana){
            return (nelson($m, "idPEntidade")==$idPEntidade && $m["dia"]==$semana );
          }));
        }
        return $totTempos;
      }

      private function pegarSaldoDisponivel(){
        $this->tipoConta = isset($_GET["tipoConta"])?$_GET["tipoConta"]:"";
        echo valorArray($this->sobreEscolaLogada, $this->tipoConta);
      }
      
      private function listar(){
        echo $this->selectJson("entidadesprimaria", ["idPEntidade", "nomeEntidade", "salarios.salarioLiquido", "salarios.idPSalario", "salarios.dataPagamento", "salarios.horaPagamento", "salarios.nomeFuncProc", "salarios.contaDebitada"], ["salarios.idEscola"=>$_SESSION['idEscolaLogada'], "salarios.anoCivil"=>$this->anoCivil, "salarios.mesPagamento"=>$this->mesPagamento], ["salarios"], "", [], ["nomeEntidade"=>-1]);
      }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>

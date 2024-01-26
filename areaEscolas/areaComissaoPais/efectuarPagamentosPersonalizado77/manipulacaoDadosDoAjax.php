<?php 
  session_start();
  include_once ('../../funcoesAuxiliares.php');
  include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{


      function __construct($caminhoAbsoluto){
        parent::__construct();
          $this->caminhoRetornar = "../../../";

          $this->classe = isset($_POST["classe"])?$_POST["classe"]:"";          
          $this->idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";   
          $this->idPMatricula = isset($_POST["idPMatricula"])?$_POST["idPMatricula"]:"";
          $this->meioPagamento = isset($_POST["meioPagamento"])?$_POST["meioPagamento"]:"";
          $this->dataPagamento = isset($_POST["dataPagamento"])?$_POST["dataPagamento"]:"";
          $this->valorPropina = isset($_POST["valorPropina"])?$_POST["valorPropina"]:"";

          $this->valorMulta = isset($_POST["valorMulta"])?$_POST["valorMulta"]:"";
          $this->valorPagar = isset($_POST["valorPagar"])?$_POST["valorPagar"]:"";
          $this->estadoDocumento = isset($_POST["sePagamentoParcelado"])?"P":"A";
          $this->contaUsar = isset($_POST["contaUsar"])?$_POST["contaUsar"]:"";
          $this->descricaoConta = $this->selectUmElemento("contas_bancarias", "descricaoConta", ["idPContaFinanceira"=>$this->contaUsar]);

          $this->sobreCurso = $this->selectArray("nomecursos", ["ultimaClasse"], ["idPNomeCurso"=>$this->idPCurso]);

          $this->cls = $this->classe;
          if($this->cls==120){
            $this->cls=valorArray($this->sobreCurso, "ultimaClasse");
          }
          if($this->accao=="listar"){
            $this->listar();
          }else if($this->accao=="pegarPagarPagamentosJaEfectuados"){
            $this->pegarPagarPagamentosJaEfectuados();
          }else if($this->accao=="efectuarPagamento"){

            $this->operacaoEfectuada = "mensalidades";

            $this->idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:$this->idAnoActual;

            $this->mesPagamento = $this->referenciaOperacao = isset($_POST["mesInicialContar"])?$_POST["mesInicialContar"]:0;
            
            $this->efectuarPagamento();
          }else if($this->accao=="pagarPagamentoPendente"){
              $this->pagarPagamentoPendente();
          }
      }

      private function pagarPagamentoPendente(){
          $nomeCliente = isset($_POST["nomeCliente"])?$_POST["nomeCliente"]:"";
          $nifCliente = isset($_POST["nifCliente"])?$_POST["nifCliente"]:"";

        $this->idPHistoricoConta = isset($_POST["idPHistoricoConta"])?$_POST["idPHistoricoConta"]:"";
        $this->idPMatricula = isset($_POST["idPMatricula"])?$_POST["idPMatricula"]:"";
        $erro="";
        if($this->verificacaoAcesso->verificarAcesso("", ["efectuarPagamentosPersonalizado77"], [$this->cls, $this->idPCurso], "")){

          $sobrePagAnterior = $this->selectArray("alunosmatriculados", ["pagamentos.precoPago", "grupo"], ["idPMatricula"=>$this->idPMatricula, "pagamentos.idPHistoricoConta"=>$this->idPHistoricoConta], ["pagamentos"]);
          
          if($this->editarItemObjecto("alunos_".valorArray($sobrePagAnterior, "grupo"), "pagamentos", "precoPago, estadoPagamento", [$this->valorPagar, $this->estadoDocumento], ["idPMatricula"=>$this->idPMatricula], ["idPHistoricoConta"=>$this->idPHistoricoConta])=="sim"){

             $identificacaoUnica = $this->selectUmElemento("payments", "identificacaoUnica", ["idDocEscola"=>$_SESSION['idEscolaLogada'], "identificadorCliente"=>$this->idPMatricula, "tipoDocumento"=>"RC", "nifCliente"=>$nifCliente, "dataEmissao"=>$this->dataSistema, "estadoDocumento"=>"N"]); 

            if($identificacaoUnica=="" || $identificacaoUnica==null){
              $identificacaoUnica=$this->identificacaoUnica("payments", "RC");

              $this->inserir("payments", "idPDocumento", "idDocEscola, dataEmissao, horaEmissao, identificadorCliente, codigoContaCliente, nifCliente, nomeCliente, nomeEmpresaCliente, enderecoDetalhadoCliente, cidadeCliente, idFuncionario, nomeFuncionario, identificacaoUnica, numeroSequencial, tipoDocumento, estadoDocumento, serieDocumento, hash", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, $this->idPMatricula, "Desconhecido", $nifCliente, $nomeCliente, "Consumidor final", "Desconhecido", "Consumidor Final", valorArray($this->sobreUsuarioLogado, "idPEntidade"), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $identificacaoUnica, $this->numeroSequencial, "RC", "N", valorArray($this->sobreEscolaLogada, "serieFactura"), $this->assinaturaDigital]);
            }

            $valorAumentado = isset($_POST["valorSomar"])?$_POST["valorSomar"]:0;

            $this->inserirObjecto("payments", "itens", "idPItem", "idPHistoricoConta, idPTipoConta, descricaoConta, indicadorProduto, idProduto, codigoProduto, descricaoProduto, referenciaPagamento, precoUnitario, unidade, quantidade, desconto, taxaIVA, valorIVA, valorTotSemImposto, valorTotComImposto, meioPagamento, dataPagamento, estadoItem", [$this->idPHistoricoConta, $this->contaUsar, $this->descricaoConta, "S", 1, "propinas", "Propinas", "Divida", $valorAumentado, "UM", 1, 0, 0, 0, $valorAumentado, $valorAumentado, $this->meioPagamento, $this->dataPagamento, "I"], ["identificacaoUnica"=>$identificacaoUnica]);

            $this->totalizadorItensDocumentos("payments", $identificacaoUnica);

          }else{
            $erro="FNão foi possível efectuar o pagamento.";
          }

        }else{
           $erro="FNão foi possível efectuar o pagamento ".$this->polina." porque não foste seleccionado nesta classe ou curso.";
        }
        echo $erro;
      }
      

        private function efectuarPagamento(){
          $nomeCliente = isset($_POST["nomeCliente"])?$_POST["nomeCliente"]:"";
          $nifCliente = isset($_POST["nifCliente"])?$_POST["nifCliente"]:"";

          
          
          $erro="";
          if($this->verificacaoAcesso->verificarAcesso("", ["efectuarPagamentosPersonalizado77"], [$this->cls, $this->idPCurso], "")){

              $this->sobreAluno($this->idPMatricula, ["pagamentos.idHistoricoAno", "pagamentos.idHistoricoEscola", "pagamentos.idTipoEmolumento", "pagamentos.estadoPagamento", "pagamentos.referenciaPagamento", "grupo"]);

              $pagamentoPendente = listarItensObjecto($this->sobreAluno, "pagamentos", ["idHistoricoAno=".$this->idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idTipoEmolumento=1", "estadoPagamento=P"]);

              if(count($pagamentoPendente)>0){
                $erro ="FO(a) aluno(a) ainda não concluiu o pagamento do mês de ".nomeMes(valorArray($pagamentoPendente, "referenciaPagamento", "pagamentos")).".";

              }else if(count(listarItensObjecto($this->sobreAluno, "pagamentos", ["idTipoEmolumento=1", "referenciaPagamento=".$this->mesPagamento, "idHistoricoAno=".$this->idPAno, "idHistoricoEscola=".$_SESSION["idEscolaLogada"]]))>0){
                $erro ="FO(a) aluno(a) já fez o pagamento do mês ".nomeMes($this->mesPagamento).".";
              }else{

                $identificacaoUnica = $this->selectUmElemento("payments", "identificacaoUnica", ["idDocEscola"=>$_SESSION['idEscolaLogada'], "tipoDocumento"=>"RC", "nifCliente"=>$nifCliente, "dataEmissao"=>$this->dataSistema, "estadoDocumento"=>"N"]);

                if($identificacaoUnica=="" || $identificacaoUnica==null){

                  $identificacaoUnica=$this->identificacaoUnica("payments", "RC");

                  $this->inserir("payments", "idPDocumento", "idDocEscola, dataEmissao, horaEmissao, identificadorCliente, codigoContaCliente, nifCliente, nomeCliente, nomeEmpresaCliente, enderecoDetalhadoCliente, cidadeCliente, idFuncionario, nomeFuncionario, identificacaoUnica, numeroSequencial, tipoDocumento, estadoDocumento", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, $this->idPMatricula, "Desconhecido", $nifCliente, $nomeCliente, "Consumidor final", "Desconhecido", "Consumidor Final", valorArray($this->sobreUsuarioLogado, "idPEntidade"), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $identificacaoUnica, $this->numeroSequencial, "RC", "N"]);
                }

                $this->inserirObjecto("alunos_".$this->grupoAluno, "pagamentos", "idPHistoricoConta", "idHistoricoEscola, idHistoricoAno, dataPagamento, horaPagamento, idHistoricoMatricula, idHistoricoFuncionario, nomeFuncionario, idTipoEmolumento, codigoEmolumento, designacaoEmolumento, referenciaPagamento, precoInicial, precoMulta , precoDesconto, precoPago, estadoPagamento, identFactura, idPTipoConta, descricaoConta", [$_SESSION['idEscolaLogada'], $this->idPAno, $this->dataSistema, $this->tempoSistema,  $this->idPMatricula, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), 1, "propinas", "Propinas", $this->referenciaOperacao, $this->valorPropina, $this->valorMulta, 0, $this->valorPagar, $this->estadoDocumento, $identificacaoUnica, $this->contaUsar, $this->descricaoConta], ["idPMatricula"=>$this->idPMatricula]);

                $this->manipularConta($this->contaUsar, "C", $this->valorPagar);

                $idPHistoricoConta = valorArray($this->selectArray("alunos_".$this->grupoAluno, ["pagamentos.idPHistoricoConta"], ["idPMatricula"=>$this->idPMatricula, "pagamentos.dataPagamento"=>$this->dataSistema, "pagamentos.horaPagamento"=>$this->tempoSistema, "pagamentos.idTipoEmolumento"=>1], ["pagamentos"]), "idPHistoricoConta", "pagamentos");

                $this->inserirObjecto("payments", "itens", "idPItem", "idPHistoricoConta, idPTipoConta, descricaoConta, indicadorProduto, idProduto, codigoProduto, descricaoProduto, referenciaPagamento, precoUnitario, unidade, quantidade, desconto, taxaIVA, valorIVA, valorTotSemImposto, valorTotComImposto, meioPagamento, dataPagamento, estadoItem", [$idPHistoricoConta, $this->contaUsar, $this->descricaoConta, "S", 1, "propinas", "Propinas", $this->referenciaOperacao, $this->valorPagar, "UM", 1, 0, 0, 0, $this->valorPagar, $this->valorPagar, $this->meioPagamento, $this->dataPagamento, "I"], ["identificacaoUnica"=>$identificacaoUnica, "idDocEscola"=>$_SESSION['idEscolaLogada']]);

                $this->totalizadorItensDocumentos("payments", $identificacaoUnica);               
              }
          }else{
              $erro="FNão foi possível efectuar o pagamento ".$this->polina." porque não foste seleccionado nesta classe ou curso.";
          }
          echo $erro;
        }
 
        private function listar(){
          $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";
          $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";

          
          $condicoesPesquisa = [array("nomeAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("biAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("numeroInterno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("nomeAluno"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("nomeAluno"=>ucwords($valorPesquisado))];

          $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "numeroInterno", "escola.idMatCurso", "escola.classeActualAluno", "biAluno", "fotoAluno", "escola.idMatFAno", "pagamentos.idHistoricoAno", "pagamentos.idHistoricoEscola", "pagamentos.idTipoEmolumento", "pagamentos.idPHistoricoConta", "pagamentos.precoInicial", "pagamentos.precoMulta", "pagamentos.precoFinal", "pagamentos.precoPago", "pagamentos.estadoPagamento", "pagamentos.referenciaPagamento", "escola.seBolseiro", "escola.beneficiosDaBolsa"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>['$in'=>array("A", "Y")], '$or'=>$condicoesPesquisa], ["escola"], 10, [], array("nomeAluno"=>1));

          $alunos = $this->anexarTabela2($alunos, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
 
          foreach($alunos as $a){

            $pagamentoPendente = listarItensObjecto($a, "pagamentos", ["idHistoricoAno=".$idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idTipoEmolumento=1", "estadoPagamento=P"]);

            $todosPagamentos = listarItensObjecto($a, "pagamentos", ["idHistoricoAno=".$idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idTipoEmolumento=1"], "nao", "dataPagamento DESC, horaPagamento DESC");

            $a["seTemPagamentoPendente"]="F";
            $a["precoInicial"]=0;
            $a["precoMulta"]=0;
            $precoFinal=0;
            $a["precoPago"]=0;
            $a["idPHistoricoConta"]=0;
            $a["seJaTemPagamento"]="";

            if(count($pagamentoPendente)>0){
              $a["seTemPagamentoPendente"]="V";
              $a["precoInicial"]=$pagamentoPendente[0]["precoInicial"];
              $a["precoMulta"]=$pagamentoPendente[0]["precoMulta"];
              $a["precoPago"]=$pagamentoPendente[0]["precoPago"];
              $a["idPHistoricoConta"]=$pagamentoPendente[0]["idPHistoricoConta"];
              $a["referenciaOperacao"]=$pagamentoPendente[0]["referenciaPagamento"];
            }
            $a["totalMesesJaPagos"]=count($todosPagamentos);
            if(count($todosPagamentos)>0){
              $a["seJaTemPagamento"]=$todosPagamentos[0]["idPHistoricoConta"];
            }

          }
          echo json_encode($alunos); 
        }
        
        private function pegarPagarPagamentosJaEfectuados(){
          $idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:"";
          $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";
          echo json_encode($this->selectArray("alunosmatriculados", ["pagamentos.referenciaPagamento"], ["idPMatricula"=>$idPMatricula, "pagamentos.idTipoEmolumento"=>1, "pagamentos.idHistoricoAno"=>$idPAno, "pagamentos.idHistoricoEscola"=>$_SESSION["idEscolaLogada"]], ["pagamentos"]));
        }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
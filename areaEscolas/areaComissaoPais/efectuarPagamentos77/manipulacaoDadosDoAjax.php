<?php 
  session_start();
  include_once ('../../funcoesAuxiliares.php');
include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
      function __construct($caminhoAbsoluto){
        parent::__construct();
          $this->caminhoRetornar = "../../../";

          if($this->accao=="listar"){
            $this->listar(); 
          }else if($this->accao=="pegarPagarPagamentosJaEfectuados"){
            $this->pegarPagarPagamentosJaEfectuados();
          }else if($this->accao=="efectuarPagamento"){

            $this->operacaoEfectuada = "mensalidades";
            
            $this->meioPagamento = isset($_POST["meioPagamento"])?$_POST["meioPagamento"]:"";
            $this->dataPagamento = isset($_POST["dataPagamento"])?$_POST["dataPagamento"]:"";
            $this->classe = isset($_POST["classe"])?$_POST["classe"]:"";

            $this->idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";   
            $this->idPMatricula = isset($_POST["idPMatricula"])?$_POST["idPMatricula"]:""; 
            $this->contaUsar = isset($_POST["contaUsar"])?$_POST["contaUsar"]:"";
            $this->descricaoConta = $this->selectUmElemento("contas_bancarias", "descricaoConta", ["idPContaFinanceira"=>$this->contaUsar]);

            $this->idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:$this->idAnoActual;

            $this->sobreCurso = $this->selectArray("nomecursos", ["ultimaClasse"], ["idPNomeCurso"=>$this->idPCurso]);
            
            $quantMes = isset($_POST["quantMes"])?$_POST["quantMes"]:0;

            $mesInicialContar = isset($_POST["mesInicialContar"])?$_POST["mesInicialContar"]:0;
            $valorMulta = isset($_POST["valorMulta"])?$_POST["valorMulta"]:0;

            $this->cls = $this->classe;
            if($this->cls==120){
              $this->cls=valorArray($this->sobreCurso, "ultimaClasse");
            }

            $posicaoMesInicialContar=-1;
            for($i=0; $i<=(count($this->mesesAnoLectivo)-1); $i++){
              if($this->mesesAnoLectivo[$i]==$mesInicialContar){
                $posicaoMesInicialContar=$i;
                break;
              }
            }



            $erroEncontrado="";
            for($i=$posicaoMesInicialContar; $i<($quantMes+$posicaoMesInicialContar); $i++){

              $array = listarItensObjecto($this->sobreEscolaLogada, "emolumentos", ["classe=".$this->cls, "mes=".$this->mesesAnoLectivo[$i], "idTipoEmolumento=1", "idCurso=".$this->idPCurso]);



              $this->valorPagar = valorArray($array, "valor");
              if($this->valorPagar<=0){
                $erroEncontrado="FOs preços dos meses não estão em sequência.";
                break;
              }
            }

            if($erroEncontrado!=""){
              echo $erroEncontrado;
            }else{

              $sobreAluno = $this->selectArray("alunosmatriculados", ["escola.beneficiosDaBolsa"], ["idPMatricula"=>$this->idPMatricula, "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);
              $beneficiosDaBolsa = valorArray($sobreAluno, "beneficiosDaBolsa", "escola");
              $beneficiosDaBolsa = (is_array($beneficiosDaBolsa) || is_object($beneficiosDaBolsa))?$beneficiosDaBolsa:array();


              for($i=$posicaoMesInicialContar; $i<($quantMes+$posicaoMesInicialContar); $i++){
                $this->mesPagamento = $this->referenciaOperacao = $this->mesesAnoLectivo[$i];


                if(count($beneficiosDaBolsa)>0){
                  $this->valorPropina=0;
                  foreach($beneficiosDaBolsa as $ben){
                    if($ben["idPTipoEmolumento"]==1 && $ben["mes"]==$this->mesPagamento){
                        $this->valorPropina = $ben["valorPreco"];
                        break;
                    }
                  }
                }else{
                  $array = listarItensObjecto($this->sobreEscolaLogada, "emolumentos", ["classe=".$this->cls, "mes=".$this->mesPagamento, "idTipoEmolumento=1", "idCurso=".$this->idPCurso]);
                  $this->valorPropina = valorArray($array, "valor");
                }

                if($this->valorPagar>0){

                  $this->valorMulta = $valorMulta/$quantMes;
                  $this->valorPagar = ($this->valorMulta+$this->valorPropina);
                  $resposta = $this->efectuarPagamento();
                  if($resposta!=""){
                    echo $resposta;
                    break;
                  }
                }
              }            
            }
          }
      }
        private function efectuarPagamento(){

          $erro="";
          $nomeCliente = isset($_POST["nomeCliente"])?$_POST["nomeCliente"]:"";
          $nifCliente = isset($_POST["nifCliente"])?$_POST["nifCliente"]:"";

         if($this->verificacaoAcesso->verificarAcesso("", ["efectuarPagamentos77"], [$this->cls, $this->idPCurso], "")){

            $this->sobreAluno($this->idPMatricula, ["pagamentos.idHistoricoAno", "pagamentos.idHistoricoEscola", "pagamentos.idTipoEmolumento", "pagamentos.estadoPagamento", "pagamentos.referenciaPagamento", "grupo"]);

            $pagamentoPendente = listarItensObjecto($this->sobreAluno, "pagamentos", ["idHistoricoAno=".$this->idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idTipoEmolumento=1", "estadoPagamento=P"]);

            if(count($pagamentoPendente)>0){
              $erro ="FO(a) aluno(a) ainda não concluiu o pagamento do mês de ".nomeMes(valorArray($pagamentoPendente, "referenciaPagamento", "pagamentos")).".";

            }else if(count(listarItensObjecto($this->sobreAluno, "pagamentos", ["idTipoEmolumento=1", "referenciaPagamento=".$this->mesPagamento, "idHistoricoAno=".$this->idPAno, "idHistoricoEscola=".$_SESSION["idEscolaLogada"]]))>0){
              $erro ="FO(a) aluno(a) já fez o pagamento do mês de ".nomeMes($this->mesPagamento).".";
            }else{
              
              $identificacaoUnica = $this->selectUmElemento("payments", "identificacaoUnica", ["idDocEscola"=>$_SESSION['idEscolaLogada'], "tipoDocumento"=>"RC", "nifCliente"=>$nifCliente, "dataEmissao"=>$this->dataSistema, "identificadorCliente"=>$this->idPMatricula, "estadoDocumento"=>"N"]);


              if($identificacaoUnica=="" || $identificacaoUnica==null){

                $identificacaoUnica=$this->identificacaoUnica("payments", "RC");

                $this->inserir("payments", "idPDocumento", "idDocEscola, dataEmissao, horaEmissao, identificadorCliente, codigoContaCliente, nifCliente, nomeCliente, nomeEmpresaCliente, enderecoDetalhadoCliente, cidadeCliente, idFuncionario, nomeFuncionario, identificacaoUnica, numeroSequencial, tipoDocumento, estadoDocumento, serieDocumento, hash", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, $this->idPMatricula, "Desconhecido", $nifCliente, $nomeCliente, "Consumidor final", "Desconhecido", "Consumidor Final", valorArray($this->sobreUsuarioLogado, "idPEntidade"), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $identificacaoUnica, $this->numeroSequencial, "RC", "N", valorArray($this->sobreEscolaLogada, "serieFactura"), $this->assinaturaDigital]);
              }
              $this->inserirObjecto("alunos_".$this->grupoAluno, "pagamentos", "idPHistoricoConta", "idHistoricoEscola, idHistoricoAno, dataPagamento, horaPagamento, idHistoricoMatricula, idHistoricoFuncionario, nomeFuncionario, idTipoEmolumento, codigoEmolumento, designacaoEmolumento, referenciaPagamento, precoInicial, precoMulta , precoDesconto, precoPago, estadoPagamento, identFactura, idPTipoConta, descricaoConta", [$_SESSION['idEscolaLogada'], $this->idPAno, $this->dataSistema, $this->tempoSistema,  $this->idPMatricula, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), 1, "propinas", "Propinas", $this->referenciaOperacao, $this->valorPropina, $this->valorMulta, 0, $this->valorPagar, "A", $identificacaoUnica, $this->contaUsar, $this->descricaoConta], ["idPMatricula"=>$this->idPMatricula]);

              $this->manipularConta($this->contaUsar, "C", $this->valorPagar);

              $idPHistoricoConta = valorArray($this->selectArray("alunos_".$this->grupoAluno, ["pagamentos.idPHistoricoConta"], ["idPMatricula"=>$this->idPMatricula, "pagamentos.dataPagamento"=>$this->dataSistema, "pagamentos.referenciaPagamento"=>$this->referenciaOperacao, "pagamentos.horaPagamento"=>$this->tempoSistema, "pagamentos.idTipoEmolumento"=>1], ["pagamentos"]), "idPHistoricoConta", "pagamentos");

              $this->inserirObjecto("payments", "itens", "idPItem", "idPHistoricoConta, idPTipoConta, descricaoConta, indicadorProduto, idProduto, codigoProduto, descricaoProduto, referenciaPagamento, precoUnitario, unidade, quantidade, desconto, taxaIVA, valorIVA, valorTotSemImposto, valorTotComImposto, meioPagamento, dataPagamento, estadoItem", [$idPHistoricoConta, $this->contaUsar, $this->descricaoConta, "S", 1, "propinas", "Propinas", $this->referenciaOperacao, $this->valorPagar, "UM", 1, 0, 0, 0, $this->valorPagar, $this->valorPagar, $this->meioPagamento, $this->dataPagamento, "I"], ["identificacaoUnica"=>$identificacaoUnica, "idDocEscola"=>$_SESSION['idEscolaLogada']]);

              $this->totalizadorItensDocumentos("payments", $identificacaoUnica);
            }
          }else{
              $erro="FNão foi possível efectuar o pagamento ".$this->polina." porque não foste seleccionado nesta classe ou curso.";
          }
          return $erro;
        }
 
        private function listar(){
          $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";
          $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";

           $condicoesPesquisa = [array("nomeAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("biAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("numeroInterno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("nomeAluno"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("nomeAluno"=>ucwords($valorPesquisado))];

          $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "numeroInterno", "escola.idMatCurso", "escola.classeActualAluno", "fotoAluno", "escola.idMatFAno", "pagamentos.idHistoricoAno", "escola.seBolseiro", "escola.beneficiosDaBolsa", "pagamentos.idHistoricoEscola", "pagamentos.idTipoEmolumento", "pagamentos.idPHistoricoConta", "pagamentos.estadoPagamento", "biAluno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>['$in'=>array("A", "Y")], '$or'=>$condicoesPesquisa], ["escola"], 10, [], array("nomeAluno"=>1));

          $alunos = $this->anexarTabela2($alunos, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");

          foreach($alunos as $a){

            $a["seJaTemPagamento"]="";
            $a["totalPagamentos"]="";

            $pagamentos = listarItensObjecto($a, "pagamentos", ["idHistoricoAno=".$idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idTipoEmolumento=1"], "nao", "idPHistoricoConta DESC");

            if(count($pagamentos)>0){ 
              $a["seJaTemPagamento"] = valorArray($pagamentos, "idPHistoricoConta");
              $a["totalPagamentos"] = count($pagamentos);
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
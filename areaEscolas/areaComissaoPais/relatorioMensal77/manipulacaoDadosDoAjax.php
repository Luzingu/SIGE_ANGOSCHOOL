<?php
	session_start();
	include_once ('../../funcoesAuxiliares.php');
include_once ('../../manipulacaoDadosDoAjax.php');

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            if($this->accao=="anularPagamentoMensalidade"){

                if($this->verificacaoAcesso->verificarAcesso("", ["relatorioMensal77"])){
                    $this->anularPagamentoMensalidade();
                }
            }
    	}

        private function anularPagamentoMensalidade(){
            $idPHistoricoConta = $_GET["idPHistoricoConta"];
            $idPMatricula = $_GET["idPMatricula"];
            $mesPagamento = $_GET["mesPagamento"];
            $anoCivil = $_GET["anoCivil"];

            $array = $this->selectArray("alunosmatriculados", ["grupo", "pagamentos.precoPago", "pagamentos.estadoPagamento", "pagamentos.idPTipoConta", "pagamentos.dataPagamento", "pagamentos.referenciaPagamento", "pagamentos.identFactura"], ["idPMatricula"=>$idPMatricula, "pagamentos.idPHistoricoConta"=>$idPHistoricoConta], ["pagamentos"], 1);

            $mesesPosterior="";
            $posicaoMesReferencia=-1;
            for($i=0; $i<=(count($this->mesesAnoLectivo)-1); $i++){
              if($this->mesesAnoLectivo[$i]==valorArray($array, "referenciaPagamento", "pagamentos")){
                $posicaoMesReferencia=$i;
                break;
              }
            }

            $mesPosterior=0;
            for($i=($posicaoMesReferencia+1); $i<=(count($this->mesesAnoLectivo)-1); $i++){
                if(count($this->selectArray("alunosmatriculados", ["idPMatricula"], ["idPMatricula"=>$idPMatricula, "pagamentos.idTipoEmolumento"=>1, "pagamentos.idHistoricoAno"=>$this->idAnoActual, "pagamentos.referenciaPagamento"=>$this->mesesAnoLectivo[$i]], ["pagamentos"], 1))>0){
                    $mesPosterior=$this->mesesAnoLectivo[$i];
                    break;
                }
            }

            if($mesPosterior>0){
                echo "FNão podes anular o pagamento deste mês, porque este aluno já fez o pagamento do mês de ".nomeMes($mesPosterior).".";
            }else if((valorArray($array, "dataPagamento", "pagamentos")!=$this->dataSistema && valorArray($this->sobreUsuarioLogado, "nivelSistemaEntidade", "escola") != 0)  || count($this->selectArray("payments", ["idPDocumento"], ["identificacaoUnica"=>valorArray($array, "identFactura", "pagamentos"), "idDocEscola"=>$_SESSION['idEscolaLogada'], "estadoDocumento"=>"N"], ["itens"], 2))==1){
                echo "FNão podes anular este pagamento.";
            }else{
                if($this->excluirItemObjecto("alunos_".valorArray($array, "grupo"), "pagamentos", ["idPMatricula"=>$idPMatricula], ["idPHistoricoConta"=>$idPHistoricoConta])=="sim"){

                    $this->excluirItemObjecto("payments", "itens", ["identificacaoUnica"=>valorArray($array, "identFactura", "pagamentos")], ["idPHistoricoConta"=>$idPHistoricoConta]);

                    $this->totalizadorItensDocumentos("payments", valorArray($array, "identFactura", "pagamentos"));

                    $this->manipularConta(valorArray($array, "idPTipoConta", "pagamentos"), "D", valorArray($array, "precoPago", "pagamentos"));

                    echo $this->selectJson("alunosmatriculados", ["nomeAluno", "numeroInterno", "idPMatricula", "grupo", "fotoAluno", "pagamentos.idHistoricoEscola", "pagamentos.dataPagamento", "pagamentos.horaPagamento", "pagamentos.nomeFuncionario", "pagamentos.idTipoEmolumento", "pagamentos.designacaoEmolumento", "pagamentos.referenciaPagamento", "pagamentos.idPHistoricoConta", "pagamentos.precoPago", "pagamentos.estadoPagamento"], ["pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.idHistoricoAno"=>$this->idAnoActual, "pagamentos.dataPagamento"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-"), "pagamentos.idTipoEmolumento"=>1], ["pagamentos"], "", [], ["pagamentos.dataPagamento"=>1, "pagamentos.horaPagamento"=>1]);
                }else{
                    echo "FNão foi possível anular o pagamento.";
                }
            }
        }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>

<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	
    	function __construct(){
    		parent::__construct();

            $this->descricaoMovimento = isset($_POST['descricaoMovimento'])?$_POST['descricaoMovimento']:"";
            $this->numArquivoDocumento = isset($_POST['numArquivoDocumento'])?$_POST['numArquivoDocumento']:"";
            $this->tipoMovimento = isset($_POST['tipoMovimento'])?$_POST['tipoMovimento']:"";
            $this->dataMovimentoContabilistico = isset($_POST['dataMovimentoContabilistico'])?$_POST['dataMovimentoContabilistico']:"";
            $this->identificadorDiario = isset($_POST['identificadorDiario'])?$_POST['identificadorDiario']:"";
            $this->descricaoDiario = isset($_POST['descricaoDiario'])?$_POST['descricaoDiario']:"";
            $this->movimento = isset($_POST['movimento'])?$_POST['movimento']:"";
            $this->contaLinha = isset($_POST['contaLinha'])?$_POST['contaLinha']:"";
            $this->descricaoContaLiha = $this->selectUmElemento("contas_bancarias", "descricaoConta", ["idPContaFinanceira"=>$this->contaLinha]);

            $this->valorLinha = isset($_POST['valorLinha'])?$_POST['valorLinha']:"";
            $this->descricaoLinhha = isset($_POST['descricaoLinhha'])?$_POST['descricaoLinhha']:"";
            $this->idPDocumento = isset($_POST['idPDocumento'])?$_POST['idPDocumento']:"";
            $this->mesPagamento = isset($_POST['mesPagamento'])?$_POST['mesPagamento']:"";
            $this->anoCivil = isset($_POST['anoCivil'])?$_POST['anoCivil']:"";


            if($this->accao=="novoMovimento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["movimentosContabilisticos"])){
                    $this->novoMovimento();
                }
            }else if($this->accao=="excluirMovimento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["movimentosContabilisticos"])){
                    $this->excluirMovimento();
                }
            }
    	}

        private function novoMovimento(){

            if($this->inserir("general_ledger_entries", "idPDocumento", "idDocEscola, dataEmissao, horaEmissao, descricaoMovimento, numArquivoDocumento, tipoMovimento, dataMovimentoContabilistico, identificadorDiario, descricaoDiario, movimento, contaLinha, descricaoContaLiha, valorLinha, descricaoLinhha, idHistoricoFuncionario, nomeFuncionario, sePagSalario", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, $this->descricaoMovimento, $this->numArquivoDocumento, $this->tipoMovimento, $this->dataMovimentoContabilistico, $this->identificadorDiario, $this->descricaoDiario, $this->movimento, $this->contaLinha, $this->descricaoContaLiha, $this->valorLinha, $this->descricaoLinhha, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), "I"])=="sim"){

                $mvt="";
                if($this->movimento=="Credito"){
                    $mvt="C";
                }else if($this->movimento=="Debito"){
                    $mvt="D";
                }                
                $this->manipularConta($this->contaLinha, $mvt, $this->valorLinha);
                $this->listar();
            }else{
                echo "FNão foi possível inserir a conta.";
            }
        }
        private function excluirMovimento(){  

            $array = $this->selectArray("general_ledger_entries", [], ["idPDocumento"=>$this->idPDocumento]);

            if($this->excluir("general_ledger_entries", ["idPDocumento"=>$this->idPDocumento])=="sim"){

                $mvt="";
                if(valorArray($array, "movimento")=="Credito"){
                    $mvt="D";
                }else if(valorArray($array, "movimento")=="Debito"){
                    $mvt="C";
                } 
                $this->manipularConta(valorArray($array, "contaLinha"), $mvt, floatval(valorArray($array, "valorLinha")));
                $this->listar();
            }else{
                echo "FNão foi possível excluir a conta.";
            }
        }
        private function listar(){
            echo $this->selectJson("general_ledger_entries", [],["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-"), "sePagSalario"=>"I"],[], "", [], ["idPDocumento"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>
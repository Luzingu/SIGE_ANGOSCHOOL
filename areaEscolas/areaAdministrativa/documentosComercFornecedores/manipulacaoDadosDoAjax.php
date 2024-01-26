<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	
    	function __construct(){
    		parent::__construct();

            $this->idPDocumento = isset($_POST['idPDocumento'])?$_POST['idPDocumento']:"";
            $this->idFornecedor = isset($_POST['idFornecedor'])?$_POST['idFornecedor']:"";
            $this->valorLiquidado = isset($_POST['valorLiquidado'])?$_POST['valorLiquidado']:"";
            $this->referenciaDocumento = isset($_POST['referenciaDocumento'])?$_POST['referenciaDocumento']:"";
            $this->dataDocCompra = isset($_POST['dataDocCompra'])?$_POST['dataDocCompra']:"";
            $this->tipoDocumento = isset($_POST['tipoDocumento'])?$_POST['tipoDocumento']:"";
            $this->valorLiquidado = isset($_POST['valorLiquidado'])?$_POST['valorLiquidado']:"";
            $this->IVA = isset($_POST['IVA'])?$_POST['IVA']:"";
            $this->totalLiquidado = isset($_POST['totalLiquidado'])?$_POST['totalLiquidado']:"";
            $this->contaUsar = isset($_POST['contaUsar'])?$_POST['contaUsar']:"";
            $this->codigoTipoImpostoRetido = isset($_POST['codigoTipoImpostoRetido'])?$_POST['codigoTipoImpostoRetido']:"";
            $this->montanteImpostoRetido = isset($_POST['montanteImpostoRetido'])?$_POST['montanteImpostoRetido']:"";
            $this->motivoDaRetencao = isset($_POST['motivoDaRetencao'])?$_POST['motivoDaRetencao']:"";
            $this->mesPagamento = isset($_POST['mesPagamento'])?$_POST['mesPagamento']:"";
            $this->anoCivil = isset($_POST['anoCivil'])?$_POST['anoCivil']:"";

            if(floatval($this->IVA)>0){
                $this->codigoTipoImpostoRetido="";
                $this->montanteImpostoRetido="";
                $this->motivoDaRetencao="";
            }

            if($this->accao=="novoDocumento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["documentosComercFornecedores"])){
                    $this->novoDocumento();
                }
            }else if($this->accao=="excluirDocumento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["documentosComercFornecedores"])){
                    $this->excluirDocumento2();
                }
            }
    	}

        private function novoDocumento(){

            $nomeEmpresa = valorArray(listarItensObjecto($this->sobreEscolaLogada, "fornecedores", ["idPFornecedor=".$this->idFornecedor]), "nomeEmpresa");

            if($this->inserir("purchase_invoices", "idPDocumento", "idDocEscola, dataEmissao, horaEmissao, idFuncionario, nomeFuncionario, idFornecedor, nomeEmpresa, valorLiquidado, referenciaDocumento, dataDocCompra, tipoDocumento, IVA, totalLiquidado, contaUsar, descricaoConta, codigoTipoImpostoRetido, montanteImpostoRetido, motivoDaRetencao", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $this->idFornecedor, $nomeEmpresa, $this->valorLiquidado, $this->referenciaDocumento, $this->dataDocCompra, $this->tipoDocumento, $this->IVA, $this->totalLiquidado, $this->contaUsar, $this->selectUmElemento("contas_bancarias", "descricaoConta", ["idPContaFinanceira"=>$this->contaUsar]), $this->codigoTipoImpostoRetido, $this->montanteImpostoRetido, $this->motivoDaRetencao])=="sim"){

                $this->manipularConta($this->contaUsar, "D", $this->totalLiquidado);

                $this->listar();
            }else{
                echo "FNão foi possível efectuar o pagamento.";
            }
        }

        private function excluirDocumento2(){

            $array = $this->selectArray("purchase_invoices", [], ["idPDocumento"=>$this->idPDocumento]);
            /*if($dataPagamento!=$this->dataSistema){
                echo "FNão podes excluir o pagamento.";
            }else{*/
                $this->excluir("purchase_invoices", ["idPDocumento"=>$this->idPDocumento]);

                $this->manipularConta(valorArray($array, "contaUsar"), "C", floatval(valorArray($array, "totalLiquidado")));
                $this->listar();
            //}
        }

        private function listar(){
            echo $this->selectJson("purchase_invoices", [],["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-")],[], "", [], ["idPDocumento"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>
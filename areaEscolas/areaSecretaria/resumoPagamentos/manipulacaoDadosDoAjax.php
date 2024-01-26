<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	
    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            if($this->accao=="anularPagamento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["resumoPagamentos"])){
                    $this->anularPagamento();
                }
            }
    	}

        private function anularPagamento(){
            $idPHistoricoConta = $_GET["idPHistoricoConta"];
            $idPMatricula = $_GET["idPMatricula"];
            $mesPagamento = $_GET["mesPagamento"];
            $anoCivil = $_GET["anoCivil"];

            $array = $this->selectArray("alunosmatriculados", ["grupo", "pagamentos.precoPago", "pagamentos.estadoPagamento", "pagamentos.idPTipoConta", "pagamentos.identFactura", "pagamentos.dataPagamento"], ["idPMatricula"=>$idPMatricula, "pagamentos.idPHistoricoConta"=>$idPHistoricoConta], ["pagamentos"], 1);

            if(valorArray($array, "estadoPagamento", "pagamentos")!="I" || valorArray($array, "dataPagamento", "pagamentos")!=$this->dataSistema || count($this->selectArray("payments", ["idPDocumento"], ["identificacaoUnica"=>valorArray($array, "identFactura", "pagamentos"), "idDocEscola"=>$_SESSION['idEscolaLogada'], "estadoDocumento"=>"N"], ["itens"], 2))==1 ){
                echo "FNão podes anular este pagamento.";
            }else{
                if($this->excluirItemObjecto("alunos_".valorArray($array, "grupo"), "pagamentos", ["idPMatricula"=>$idPMatricula], ["idPHistoricoConta"=>$idPHistoricoConta])=="sim"){

                    $this->excluirItemObjecto("payments", "itens", ["identificacaoUnica"=>valorArray($array, "identFactura", "pagamentos")], ["idPHistoricoConta"=>$idPHistoricoConta]);

                    $this->totalizadorItensDocumentos("payments", valorArray($array, "identFactura", "pagamentos"));

                    $this->manipularConta(valorArray($array, "idPTipoConta", "pagamentos"), "D", valorArray($array, "precoPago", "pagamentos"));

                    echo $this->selectJson("alunosmatriculados", ["nomeAluno", "numeroInterno", "idPMatricula", "grupo", "fotoAluno", "pagamentos.idHistoricoEscola", "pagamentos.dataPagamento", "pagamentos.horaPagamento", "pagamentos.nomeFuncionario", "pagamentos.idTipoEmolumento", "pagamentos.designacaoEmolumento", "pagamentos.referenciaPagamento", "pagamentos.idPHistoricoConta", "pagamentos.precoPago", "pagamentos.estadoPagamento"], ["pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.idHistoricoAno"=>$this->idAnoActual, "pagamentos.dataPagamento"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-"), "pagamentos.idTipoEmolumento"=>array('$ne'=>1)], ["pagamentos"], "", [], ["pagamentos.dataPagamento"=>1, "pagamentos.horaPagamento"=>1]);
                }else{
                    echo "FNão foi possível anular o pagamento.";
                }
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
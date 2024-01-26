<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	
    	function __construct($caminhoAbsoluto){
    		parent::__construct();

            if($this->accao=="anularFactura"){
                if($this->verificacaoAcesso->verificarAcesso("", ["recibosNormais"])){
                    $this->anularFactura();
                }
            }
    	}

        private function anularFactura(){
            $idPDocumento = isset($_POST["idPDocumento"])?$_POST["idPDocumento"]:"";
            $mesPagamento = isset($_POST["mesPagamento"])?$_POST["mesPagamento"]:"";
            $anoCivil = isset($_POST["anoCivil"])?$_POST["anoCivil"]:"";
            $motivoCancelamento = isset($_POST["motivoCancelamento"])?$_POST["motivoCancelamento"]:"";

            $payments = $this->selectArray("payments", [], ["idPDocumento"=>$idPDocumento, "idDocEscola"=>$_SESSION['idEscolaLogada']]);

            $grupoAluno = $this->selectUmElemento("alunosmatriculados", "grupo", ["idPMatricula"=>valorArray($payments, "identificadorCliente")]);

            if(valorArray($payments, "estadoDocumento")!="N" || valorArray($payments, "dataEmissao")!=$this->dataSistema || count(listarItensObjecto($payments, "itens", ["estadoItem=A"]))>0){
                echo "FNão podes anular a factura.";
            }else{

                $this->editar("payments", "estadoDocumento, motivoCancelamento", ["A", $motivoCancelamento], ["idPDocumento"=>$idPDocumento, "idDocEscola"=>$_SESSION['idEscolaLogada']]);

                foreach(listarItensObjecto($payments, "itens", []) as $a){
                    
                    $this->excluirItemObjecto("alunos_".$grupoAluno, "pagamentos", ["idPMatricula"=>valorArray($payments, "identificadorCliente")], ["idPHistoricoConta"=>nelson($a, "idPHistoricoConta")]);

                    $this->editarItemObjecto("payments", "itens", "idPHistoricoConta", [""], ["idPDocumento"=>valorArray($payments, "idPDocumento")], ["idPItem"=>$a["idPItem"]]);

                    if($a["idProduto"]==9 || $a["idProduto"]==8){
                        $this->excluir("pagamentos_matricula_inscricao", ["idPPagamento"=>valorArray($a, "idPPagamento")]);
                    }
                    $this->manipularConta($a["idPTipoConta"], "D", $a["valorTotComImposto"]);
                }

                $identificacaoUnica=$this->identificacaoUnica("payments", "NC");
                

                $this->inserir("payments", "idPDocumento", "idDocEscola, referenciaFactura, dataEmissao, horaEmissao, identificadorCliente, codigoContaCliente, nifCliente, nomeCliente, nomeEmpresaCliente, enderecoDetalhadoCliente, cidadeCliente, idFuncionario, nomeFuncionario, identificacaoUnica, numeroSequencial, tipoDocumento, estadoDocumento, valorTotSemImposto, valorTotComImposto, motivoCancelamento, serieDocumento, hash", [$_SESSION['idEscolaLogada'], valorArray($payments, "identificacaoUnica"), $this->dataSistema, $this->tempoSistema, valorArray($payments, "identificadorCliente"), "Desconhecido", valorArray($payments, "nifCliente"), valorArray($payments, "nomeCliente"), "Consumidor final", "Desconhecido", "Consumidor Final", valorArray($this->sobreUsuarioLogado, "idPEntidade"), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $identificacaoUnica, $this->numeroSequencial, "NC", "N", valorArray($payments, "valorTotSemImposto"), valorArray($payments, "valorTotComImposto"), $motivoCancelamento, valorArray($payments, "serieDocumento"), $this->assinaturaDigital]);


                echo $this->selectJson("payments", ["idPDocumento", "identificacaoUnica", "dataEmissao", "horaEmissao", "nomeFuncionario", "nomeCliente", "valorTotComImposto"], ["idDocEscola"=>$_SESSION['idEscolaLogada'], "tipoDocumento"=>"RC", "estadoDocumento"=>"N", "dataEmissao"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-")], [], "", [], ["idPDocumento"=>-1]);
            }


        }
        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	
    	function __construct($caminhoAbsoluto){
    		parent::__construct();

            $this->anoCivil = isset($_POST["anoCivil"])?$_POST["anoCivil"]:"";
            $this->mesPagamento = isset($_POST["mesPagamento"])?$_POST["mesPagamento"]:"";

            if($this->accao=="novoPagamento"){

                $luzingu = isset($_POST["luzingu"])?$_POST["luzingu"]:"";
                $luzingu = explode("-", $luzingu);

                $classe = $luzingu[1];
                $idCurso = $luzingu[2];

                if($this->verificacaoAcesso->verificarAcesso("", ["pagamentosMatriculaInscricao"], [$classe, $idCurso])){
                    $this->novoPagamento();
                }
            }else if($this->accao=="anularPagamento"){
                 if($this->verificacaoAcesso->verificarAcesso("", ["pagamentosMatriculaInscricao"])){

                    $this->anularPagamento();
                }
            }
    	}


        private function anularPagamento(){
            $idPPagamento = isset($_POST["idPPagamento"])?$_POST["idPPagamento"]:"";
            $motivoCancelamento = isset($_POST["motivoCancelamento"])?$_POST["motivoCancelamento"]:"";

            $array = $this->selectArray("pagamentos_matricula_inscricao", [], ["idPPagamento"=>$idPPagamento]);

            if(valorArray($array, "estadoPagamento")!="I" || valorArray($array, "dataPagamento")!=$this->dataSistema){
                echo "FNão podes anular este pagamento.";
            }else{
                if($this->excluir("pagamentos_matricula_inscricao", ["idPPagamento"=>$idPPagamento])=="sim"){

                    $this->editar("payments", "estadoDocumento, motivoCancelamento", ["A", $motivoCancelamento], ["identificacaoUnica"=>valorArray($array, "identFactura"), "idDocEscola"=>$_SESSION['idEscolaLogada']]);

                    $sobreFactura = $this->selectArray("payments",[], ["identificacaoUnica"=>valorArray($array, "identFactura"), "idDocEscola"=>$_SESSION['idEscolaLogada']]);

                    $identificacaoUnica=$this->identificacaoUnica("payments", "NC", valorArray($sobreFactura, "valorTotComImposto"));

                    $this->inserir("payments", "idPDocumento", "idDocEscola, referenciaFactura, dataEmissao, horaEmissao, identificadorCliente, codigoContaCliente, nifCliente, nomeCliente, nomeEmpresaCliente, enderecoDetalhadoCliente, cidadeCliente, idFuncionario, nomeFuncionario, identificacaoUnica, numeroSequencial, tipoDocumento, estadoDocumento, valorTotSemImposto, valorTotComImposto, motivoCancelamento, serieDocumento, hash", [$_SESSION['idEscolaLogada'], valorArray($sobreFactura, "identificacaoUnica"), $this->dataSistema, $this->tempoSistema, valorArray($sobreFactura, "identificadorCliente"), "Desconhecido", valorArray($sobreFactura, "nifCliente"), valorArray($sobreFactura, "nomeCliente"), "Consumidor final", "Desconhecido", "Consumidor Final", valorArray($this->sobreUsuarioLogado, "idPEntidade"), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $identificacaoUnica, $this->numeroSequencial, "NC", "N", valorArray($sobreFactura, "valorTotSemImposto"), valorArray($sobreFactura, "valorTotComImposto"), $motivoCancelamento, valorArray($sobreFactura, "serieDocumento"), $this->assinaturaDigital]);

                    $this->manipularConta(valorArray($array, "contaUsar"), "D", valorArray($array, "valorPago"));

                    $this->listar();
                }else{
                    echo "FNão foi anular o pagamento.";
                }
            }
        }
        private function novoPagamento(){
            $luzingu = isset($_POST["luzingu"])?$_POST["luzingu"]:"";
            $referenciaPagamento = isset($_POST["referenciaPagamento"])?$_POST["referenciaPagamento"]:"";
            $contaUsar = isset($_POST["contaUsar"])?$_POST["contaUsar"]:"";

            $meioPagamento = isset($_POST["meioPagamento"])?$_POST["meioPagamento"]:"";
            $dataPagamento = isset($_POST["dataPagamento"])?$_POST["dataPagamento"]:"";

            $descricaoConta = $this->selectUmElemento("contas_bancarias", "descricaoConta", ["idPContaFinanceira"=>$contaUsar]);

            $nomeCliente = isset($_POST["nomeCliente"])?$_POST["nomeCliente"]:"";
            $nifCliente = isset($_POST["nifCliente"])?$_POST["nifCliente"]:"";

            $luzingu = explode("-", $luzingu);
            $classe = $luzingu[1];
            $idCurso = $luzingu[2]; 

            $array = $this->selectCondClasseCurso("array", "escolas",["emolumentos.valor"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "emolumentos.codigoEmolumento"=>$referenciaPagamento, "emolumentos.classe"=>$classe], $classe, ["emolumentos.idCurso"=>$idCurso], ["emolumentos"]);

            if(valorArray($array, "valor", "emolumentos")<=0){
                echo "FNão podes processar este pagamento.";
            }else{
                $jaExistemNumero="V";
                $rpm="";
                while ($jaExistemNumero=="V"){
                    $rpm = substr(str_shuffle("1234567890"),0, 6);
                    if(count($this->selectArray("pagamentos_matricula_inscricao", ["rpm"], ["rpm"=>$rpm],[], 1))<=0){
                        $jaExistemNumero="F";
                    }   
                }

                $id=9;
                $pepe="Matricula";
                if($referenciaPagamento=="inscricao"){
                    $id=8;
                    $pepe="Inscrição";
                }
                $identificacaoUnica=$this->identificacaoUnica("payments", "RC"); 

                if($this->inserir("pagamentos_matricula_inscricao", "idPPagamento", "idPagEscola, idPagAno, dataPagamento, horaPagamento, idFuncionarioProc, nomeFuncProc, referenciaPagamento, contaUsar, valorPago, classe, idCurso, rpm, estadoPagamento, identFactura", [$_SESSION['idEscolaLogada'], $this->idAnoActual, $this->dataSistema, $this->tempoSistema, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $referenciaPagamento, $contaUsar, valorArray($array, "valor", "emolumentos"), $classe, $idCurso, $rpm, "I", $identificacaoUnica])=="sim"){

                    $idPPagamento = $this->selectUmElemento("pagamentos_matricula_inscricao", "idPPagamento", ["idPagEscola"=>$_SESSION['idEscolaLogada'], "idPagAno"=>$this->idAnoActual, "dataPagamento"=>$this->dataSistema, "horaPagamento"=>$this->tempoSistema, "idFuncionarioProc"=>$_SESSION['idUsuarioLogado']], [], [], ["idPPagamento"=>-1]);

                    $this->inserir("payments", "idPDocumento", "idDocEscola, dataEmissao, horaEmissao, identificadorCliente, codigoContaCliente, nifCliente, nomeCliente, nomeEmpresaCliente, enderecoDetalhadoCliente, cidadeCliente, idFuncionario, nomeFuncionario, identificacaoUnica, numeroSequencial, serieDocumento, tipoDocumento, hash, estadoDocumento", [$_SESSION['idEscolaLogada'], $this->dataSistema, $this->tempoSistema, "USER-".$rpm, "Desconhecido", $nifCliente, $nomeCliente, "Consumidor final", "Desconhecido", "Consumidor Final", valorArray($this->sobreUsuarioLogado, "idPEntidade"), valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $identificacaoUnica, $this->numeroSequencial, valorArray($this->sobreEscolaLogada, "serieFactura"), "RC", $this->assinaturaDigital, "N"]);

                    $this->inserirObjecto("payments", "itens", "idPItem", "idPPagamento, idPTipoConta, descricaoConta, indicadorProduto, idProduto, codigoProduto, descricaoProduto, referenciaPagamento, precoUnitario, unidade, quantidade, desconto, taxaIVA, valorIVA, valorTotSemImposto, valorTotComImposto, meioPagamento, dataPagamento, estadoItem", [$idPPagamento, $contaUsar, $descricaoConta, "S", $id, $referenciaPagamento, $pepe, "", valorArray($array, "valor", "emolumentos"), "UM", 1, 0, 0, 0, valorArray($array, "valor", "emolumentos"), valorArray($array, "valor", "emolumentos"), $meioPagamento, $dataPagamento, "I"], ["identificacaoUnica"=>$identificacaoUnica, "idDocEscola"=>$_SESSION['idEscolaLogada']]);

                    $this->manipularConta($contaUsar, "C", valorArray($array, "valor", "emolumentos"));

                    $this->totalizadorItensDocumentos("payments", $identificacaoUnica);

                    $this->listar();
                }else{
                    echo "FNão foi possível processar o pagamento.";
                }
            }
        }
        private function listar(){
            echo $this->selectJson("pagamentos_matricula_inscricao", [], ["idPagEscola"=>$_SESSION['idEscolaLogada'], "dataPagamento"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-")], [], "", [], ["idPPagamento"=>-1]);
        }
        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
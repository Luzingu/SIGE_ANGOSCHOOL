<?php 
	session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	

    	function __construct(){
    		parent::__construct();

            if($this->accao=="aceitarPagamento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["pagamentoSistemaPosPago00"])){
                    $this->aceitarPagamento();
                 } 
                
            }else if($this->accao=="recusarPagamento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["pagamentoSistemaPosPago00"])){
                    $this->recusarPagamento();
                }
            }
    	}

        private function aceitarPagamento(){
            $idPPagamento = isset($_POST["idPPagamento"])?$_POST["idPPagamento"]:"";
            $idPEscola = isset($_POST["idPEscola"])?$_POST["idPEscola"]:"";
            $sobrePagamento = $this->selectArray("escolas", ["pagamentos.estadoPagamento", "pagamentos.idEntRequerente", "pagamentos.valorTotalPago"], ["pagamentos.idPPagamento"=>$idPPagamento, "idPEscola"=>$idPEscola], ["pagamentos"]);
            $argumentoResposta = isset($_POST["argumentoResposta"])?$_POST["argumentoResposta"]:"";

            if(valorArray($sobrePagamento, "estadoPagamento", "pagamentos")!="Y"){
                echo "FEste pagamento já está vencido.";
            }else{
                if($this->editarItemObjecto("escolas", "pagamentos", "estadoPagamento, dataRespPagamento, horaRespPagamento, argumentoResposta, idEntResposta, estadoRecepcaoValor", ["V", $this->dataSistema, $this->tempoSistema, $argumentoResposta, $_SESSION['idUsuarioLogado'], "Y"], ["idPEscola"=>$idPEscola], ["idPPagamento"=>$idPPagamento])=="sim"){

                    if(trim($argumentoResposta)!="" && trim($argumentoResposta)!=NULL){

                        $chaida = $this->selectArray("entidadesprimaria", ["nomeEntidade", "fotoEntidade"], ["idPEntidade"=>$_SESSION["idUsuarioLogado"]]);
                        $nomeEmissor = valorArray($chaida, "nomeEntidade");
                        $fotoEmissor = valorArray($chaida, "fotoEntidade");

                        $chaida = $this->selectArray("entidadesprimaria", ["nomeEntidade", "fotoEntidade"], ["idPEntidade"=>valorArray($sobrePagamento, "idEntRequerente", "pagamentos")]);
                        $nomeReceptor = valorArray($chaida, "nomeEntidade");
                        $fotoReceptor = valorArray($chaida, "fotoEntidade");

                        $this->inserir("mensagens", "idPMensagem", "idReceptor, receptor, nomeReceptor, fotoReceptor, idEmissor, emissor, nomeEmissor, fotoEmissor, textoMensagem, estadoMensagem, dataMensagem, horaMensagem", [valorArray($sobrePagamento, "idEntRequerente", "pagamentos"), "entidade_".valorArray($sobrePagamento, "idEntRequerente", "pagamentos"), $nomeReceptor, $fotoReceptor, $_SESSION['idUsuarioLogado'], $_SESSION['tbUsuario']."_".$_SESSION['idUsuarioLogado'], $nomeEmissor, $fotoEmissor, $argumentoRequerente, "F", $this->dataSistema, $this->tempoSistema]);
                    }

                    $sobreContrato = $this->selectArray("escolas", ["contrato.saldoParaPagamentoPosPago"],["idPEscola"=>$idPEscola], ["contrato"]);

                    $this->editarItemObjecto("escolas", "contrato", "saldoParaPagamentoPosPago", [(double) valorArray($sobreContrato, "saldoParaPagamentoPosPago", "contrato")+(double)valorArray($sobrePagamento, "valorTotalPago", "pagamentos")], ["idPEscola"=>$idPEscola], ["idPContrato"=>array('$ne'=>null)]);

                    echo $this->selectJson("escolas", ["pagamentos.idPPagamento", "pagamentos.tempoTotalExtender", "pagamentos.dataReqPagamento", "pagamentos.argumentoRequerente", "pagamentos.valorTotalPago", "pagamentos.estadoPagamento", "pagamentos.horaReqPagamento", "pagamentos.valorDescontado", "pagamentos.horaRespPagamento", "pagamentos.dataRespPagamento", "pagamentos.argumentoResposta", "idPEscola", "pagamentos.imgBolderon"], ["idPEscola"=>$idPEscola], ["pagamentos"], 100, [], array("pagamentos.idPPagamento"=>-1));
                }else{  
                    echo "FNão foi possível aceitar o pagamento.";
                }

            }
        }

        private function recusarPagamento(){
            $idPEscola = isset($_POST["idPEscola"])?$_POST["idPEscola"]:"";
            $idPPagamento = isset($_POST["idPPagamento"])?$_POST["idPPagamento"]:"";
           $sobrePagamento = $this->selectArray("escolas", ["pagamentos.estadoPagamento", "pagamentos.idEntRequerente", "pagamentos.valorTotalPago"], ["pagamentos.idPPagamento"=>$idPPagamento, "idPEscola"=>$idPEscola], ["pagamentos"]);

            $argumentoResposta = isset($_POST["argumentoResposta"])?$_POST["argumentoResposta"]:"";

            if(valorArray($sobrePagamento, "estadoPagamento", "pagamentos")!="Y"){
                echo "FEste pagamento já está vencido.";
            }else{
                if($this->editarItemObjecto("escolas", "pagamentos", "estadoPagamento, dataRespPagamento, horaRespPagamento, argumentoResposta, idEntResposta", ["F", $this->dataSistema, $this->tempoSistema, $argumentoResposta, $_SESSION['idUsuarioLogado']], ["idPEscola"=>$idPEscola], ["idPPagamento"=>$idPPagamento])=="sim"){

                    if(trim($argumentoResposta)!="" && trim($argumentoResposta)!=NULL){
                     $this->inserir("mensagens", "idEmissorEnt, idReceptorEnt, textoMensagem, estadoMensagem, dataMensagem, horaMensagem", [$_SESSION["idUsuarioLogado"], valorArray($sobrePagamento, "idEntRequerente", "pagamentos"), $argumentoResposta, "F", $this->dataSistema, $this->tempoSistema]);
                    }

                    echo $this->selectJson("escolas", ["pagamentos.idPPagamento", "pagamentos.tempoTotalExtender", "pagamentos.argumentoRequerente", "pagamentos.valorTotalPago", "pagamentos.estadoPagamento", "pagamentos.horaReqPagamento", "pagamentos.valorDescontado", "pagamentos.horaRespPagamento", "pagamentos.dataRespPagamento", "pagamentos.argumentoResposta", "idPEscola", "pagamentos.imgBolderon"], ["idPEscola"=>$idPEscola], ["pagamentos"], 100, [], array("pagamentos.dataReqPagamento"=>-1));
                    
                }else{  
                    echo "FNão foi possível recusar o pagamento.";
                }

            }
        }

    }
    new manipulacaoDadosDoAjaxInterno();
?>
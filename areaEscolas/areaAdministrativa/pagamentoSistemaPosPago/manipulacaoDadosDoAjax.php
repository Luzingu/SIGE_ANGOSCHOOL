<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	

    	function __construct($caminhoAbsoluto){
    		parent::__construct();

            if($this->accao=="processarPagamento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["pagamentoSistemaPosPago"], array(), "FNão tens permissão de alterar os dados", "sim")){
                    $this->processarPagamento();
                 }
                
            }else if($this->accao=="anularProcessarPagamento"){

                if($this->verificacaoAcesso->verificarAcesso("", ["pagamentoSistemaPosPago"], array(), "FNão tens permissão de alterar os dados", "sim")){
                    $this->anularProcessarPagamento();
                }
            }
    	}

        private function processarPagamento(){

            $valorTotalPago = isset($_POST["valorTotalPago"])?$_POST["valorTotalPago"]:"";
            $argumentoRequerente = isset($_POST["argumentoRequerente"])?$_POST["argumentoRequerente"]:"";

            if(isset($_FILES["imgBolderon"]) && $_FILES['imgBolderon']['size']>0){

                $extensao = pathinfo($_FILES["imgBolderon"]["name"], PATHINFO_EXTENSION);
          
                $nomeProvisorio = $this->dia.$this->mes.$this->ano.date("H").date("s").date("i");

                $nomeImagem = $this->upload("imgBolderon", $nomeProvisorio, "Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Bolderon_Pag_Sistema", "../../../", "", 400, 500);

                if($this->inserirObjecto("escolas", "pagamentos", "idPPagamento", "idPagamentoEscola, dataReqPagamento, horaReqPagamento, argumentoRequerente, idEntRequerente, valorTotalPago, imgBolderon, estadoPagamento", [$_SESSION["idEscolaLogada"], $this->dataSistema, $this->tempoSistema, "(Pag) ".$argumentoRequerente, $_SESSION['idUsuarioLogado'], $valorTotalPago, $nomeImagem, "Y"], ["idPEscola"=>$_SESSION['idEscolaLogada']])=="sim"){

                    $chaida = $this->selectArray("entidadesprimaria", ["nomeEntidade", "fotoEntidade"], ["idPEntidade"=>$_SESSION["idUsuarioLogado"]]);
                    $nomeEmissor = valorArray($chaida, "nomeEntidade");
                    $fotoEmissor = valorArray($chaida, "fotoEntidade");

                    $chaida = $this->selectArray("entidadesprimaria", ["nomeEntidade", "fotoEntidade"], ["idPEntidade"=>35]);
                    $nomeReceptor = valorArray($chaida, "nomeEntidade");
                    $fotoReceptor = valorArray($chaida, "fotoEntidade");

                    $this->inserir("mensagens", "idPMensagem", "idReceptor, receptor, nomeReceptor, fotoReceptor, idEmissor, emissor, nomeEmissor, fotoEmissor, textoMensagem, estadoMensagem, dataMensagem, horaMensagem", [35, "entidade_35", $nomeReceptor, $fotoReceptor, $_SESSION['idUsuarioLogado'], $_SESSION['tbUsuario']."_".$_SESSION['idUsuarioLogado'], $nomeEmissor, $fotoEmissor, $argumentoRequerente, "F", $this->dataSistema, $this->tempoSistema]);

                    echo $this->selectJson("escolas", ["pagamentos.idPPagamento", "pagamentos.tempoTotalExtender", "pagamentos.argumentoRequerente", "pagamentos.valorTotalPago", "pagamentos.estadoPagamento", "pagamentos.dataReqPagamento", "pagamentos.horaReqPagamento", "pagamentos.valorDescontado", "pagamentos.horaRespPagamento", "pagamentos.dataRespPagamento", "pagamentos.argumentoResposta", "pagamentos.imgBolderon", "idPEscola"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["pagamentos"], 100, [], array("pagamentos.dataReqPagamento"=>-1));
                }else{
                    echo "FNão foi possível processar o pagamento.";
                }            
            }

        }

        private function anularProcessarPagamento(){
            $idPPagamento = isset($_POST["idPPagamento"])?$_POST["idPPagamento"]:"";

            if($this->excluirItemObjecto("escolas", "pagamentos", ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idPPagamento"=>$idPPagamento, "estadoPagamento"=>"Y"])=="sim"){

                echo $this->selectJson("escolas", ["pagamentos.idPPagamento", "pagamentos.tempoTotalExtender", "pagamentos.argumentoRequerente", "pagamentos.valorTotalPago", "pagamentos.estadoPagamento", "pagamentos.horaReqPagamento", "pagamentos.valorDescontado", "pagamentos.horaRespPagamento", "pagamentos.dataRespPagamento", "pagamentos.argumentoResposta", "pagamentos.imgBolderon", "idPEscola"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["pagamentos"], 100, [], array("pagamentos.idPPagamento"=>-1));
            }else{
                echo "FNão foi possível anular o processamento do pagamento.";
            }
        }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
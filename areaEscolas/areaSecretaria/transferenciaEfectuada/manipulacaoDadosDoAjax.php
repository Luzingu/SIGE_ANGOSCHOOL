<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
           if($this->accao=="cancelarTransferencia"){

                
                if($this->verificacaoAcesso->verificarAcesso("", ["transferenciaEfectuada"], [])){
                    $this->cancelarTransferencia();
                }
           }
        }

        private function cancelarTransferencia(){
            $idPTransferencia = isset($_GET["idPTransferencia"])?$_GET["idPTransferencia"]:null;
            $idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:null;

            foreach ($this->selectArray("alunosmatriculados", [], ["idPMatricula"=>$idPMatricula, "transferencia.idPTransferencia"=>$idPTransferencia], ["transferencia"]) as $transf) {

                                    
                if($transf["transferencia"]["idTransfAno"]!=$this->idAnoActual){
                    echo "FNão podes cancelar transferencia deste ano lectivo.";
                }else if($transf["transferencia"]["estadoTransferencia"]=="V" && $transf["transferencia"]["idTransfEscolaDestino"]!=NULL){
                    echo "FNão podes cancelar esta transferência.";
                }else{ 

                    if($this->excluirItemObjecto("alunos_".$transf["grupo"], "transferencia", ["idPMatricula"=>$idPMatricula], ["idPTransferencia"=>$idPTransferencia])=="sim"){
                        

                        $this->editarItemObjecto("alunos_".$transf["grupo"], "pagamentos", "estadoPagamento", ["I"], ["idPMatricula"=>$idPMatricula], [valorArray($transf, "idPHistoricoConta", "pagamentos")]);

                        $this->editarItemObjecto("facturas", "itens", "estadoItem", ["I"], ["idPMatricula"=>$idPMatricula], [valorArray($transf, "idPHistoricoConta", "pagamentos")]);

                        $this->editarItemObjecto("alunos_".$transf["grupo"], "reconfirmacoes", "estadoReconfirmacao", ["A"], ["idPMatricula"=>$idPMatricula], ["idReconfEscola"=>$_SESSION['idEscolaLogada'], "idReconfAno"=>$this->idAnoActual]);

                        $this->editarItemObjecto("alunos_".$transf["grupo"], "escola", "estadoAluno", ["A"], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$_SESSION["idEscolaLogada"]]);

                        $this->actuazalizarReconfirmacaAluno($idPMatricula);

                        $luzinguLuame = $this->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "numeroInterno", "transferencia.idTransfEscolaDestino", "transferencia.idPTransferencia", "fotoAluno", "transferencia.estadoTransferencia"], ["transferencia.idTransfEscolaOrigem"=>$_SESSION['idEscolaLogada'], "transferencia.idTransfAno"=>$this->idAnoActual], ["transferencia"], "", [], ["nomeAluno"=>1]); 
                        $luzinguLuame = $this->anexarTabela2($luzinguLuame, "escolas", "transferencia", "idPEscola", "idTransfEscolaDestino");
                        echo json_encode($luzinguLuame);
                    }else{
                        echo "FNão foi possível cancelar a transferência.";
                    }
               }
            }
        }
    	
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
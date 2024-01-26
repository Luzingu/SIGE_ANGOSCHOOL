<?php 
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
        function __construct($caminhoAbsoluto){
            parent::__construct();
            if($this->accao=="manipularGerenciadorTurma"){

                $this->classe = isset($_POST["classe"])?$_POST["classe"]:"";
                $this->idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";
                $this->idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:"";
                
                if($this->verificacaoAcesso->verificarAcesso("", ["conselhoNotas"], [$this->classe, $this->idPCurso])){
                    $this->alterarGerenciador();
                } 
            }
        }

        private function alterarGerenciador(){

            $idPListaTurma = filter_input(INPUT_POST, "idPListaTurma", FILTER_SANITIZE_NUMBER_INT);

            $idPresidenteConselho = filter_input(INPUT_POST, "idPresidenteConselho", FILTER_SANITIZE_NUMBER_INT);
            $salaReunidoConselho = filter_input(INPUT_POST, "salaReunidoConselho", FILTER_SANITIZE_NUMBER_INT);
            $dataConselhoNotas = filter_input(INPUT_POST, "dataConselhoNotas", FILTER_SANITIZE_STRING);
            $horaConselhoNotas = filter_input(INPUT_POST, "horaConselhoNotas", FILTER_SANITIZE_STRING);     

            if($this->editar("listaturmas", "idPresidenteConselho, salaReunidoConselho, dataConselhoNotas, horaConselhoNotas", [$idPresidenteConselho, $salaReunidoConselho, $dataConselhoNotas, $horaConselhoNotas], ["idPListaTurma"=>$idPListaTurma])=="sim"){

                $array = $this->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idListaAno"=>$this->idPAno, "classe"=>$this->classe, "idPNomeCurso"=>$this->idPCurso]);
                $array = $this->anexarTabela($array, "entidadesprimaria", "idPEntidade", "idPresidenteConselho");
                echo json_encode($array);

            }else{
                echo "FNão Foi Possível Alterar os Dados.";
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
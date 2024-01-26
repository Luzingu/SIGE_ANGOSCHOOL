<?php 
    session_start();
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        private $idPCurso="";
        private $classe="";
        private $valoresAlteradosPrecos="";
        private $nomeValoresAlteradosPrecos="";
        private $tipoPreco="";

        function __construct($caminhoAbsoluto){
            parent::__construct();
            if($this->accao=="gravarGerenciadorPeriodos"){

                if($this->verificacaoAcesso->verificarAcesso("", ["gerenciadorPeriodos"], [])){
                    $this->gravarGerenciadorPeriodos();
                }
            }else if($this->accao=="alterarGerenciarPeriodo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["gerenciadorPeriodos"], [])){
                    $idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:null;

                    $this->alterarGerenciarPeriodo();
                }
            }

        }

        private function alterarGerenciarPeriodo(){
            $horaEntrada = $_POST["horaEntrada"];

            if(count(explode(":", $horaEntrada))<3){
                echo "FFormato da hora de entrada inválida (h:m:s)";
            }else if($this->editarItemObjecto("escolas", "gerencPerido", "horaEntrada, duracaoPorTempo, intevaloDepoisDoTempo, duracaoIntervalo, idCoordernadorPeriodo, numeroTempos, numeroDias", [$horaEntrada, $_POST["duracaoPorTempo"], $_POST["intevaloDepoisDoTempo"], $_POST["duracaoIntervalo"], $_POST["idCoordernadorPeriodo"], $_POST["numeroTempos"], $_POST["numeroDias"]], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idPGerPeriodo"=>$_POST["idPGerPeriodo"]])=="sim"){

                $this->listar();
            }else{
                echo "FNão foi possível alterar os dados.";
            }
        }

        private function gravarGerenciadorPeriodos(){
            $periodos =["Matinal", "Vespertino", "Noturno"];

            foreach ($periodos as $periodo) {
                $chavePeriodo = $_SESSION["idEscolaLogada"]."-".$this->idAnoActual."-".$periodo;

                $this->inserirObjecto("escolas", "gerencPerido", "idPGerPeriodo", "idGerPerEscola, idGerPerAno, periodoGerenciador, chaveGerPerido", [$_SESSION["idEscolaLogada"], $this->idAnoActual, $periodo, $chavePeriodo], ["idPEscola"=>$_SESSION['idEscolaLogada']]);   
            }
            $this->listar();
        }

        private function listar(){
            $array = $this->selectArray("escolas", ["gerencPerido.idPGerPeriodo", "gerencPerido.horaEntrada", "gerencPerido.periodoGerenciador", "gerencPerido.duracaoPorTempo", "gerencPerido.intevaloDepoisDoTempo", "gerencPerido.duracaoIntervalo", "gerencPerido.numeroTempos", "gerencPerido.numeroDias", "gerencPerido.idCoordernadorPeriodo", "gerencPerido.idGerPerEscola", "gerencPerido.idGerPerAno", "gerencPerido.chaveGerPerido"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "gerencPerido.idGerPerAno"=>$this->idAnoActual], ["gerencPerido"]);
          $array = $this->anexarTabela2($array, "entidadesprimaria", "gerencPerido", "idPEntidade", "idCoordernadorPeriodo");

            echo json_encode($array);
        }

        

        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
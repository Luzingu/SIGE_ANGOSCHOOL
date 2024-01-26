<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
     
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->idProvPais = filter_input(INPUT_POST, "idProvPais", FILTER_SANITIZE_NUMBER_INT);
            $this->idPProvincia = filter_input(INPUT_POST, "idPProvincia", FILTER_SANITIZE_NUMBER_INT);

            $this->nomeProvincia = isset($_POST['nomeProvincia'])?$_POST['nomeProvincia']:"";
            $this->preposicaoProvincia = isset($_POST['preposicaoProvincia'])?$_POST['preposicaoProvincia']:"";
            $this->preposicaoProvincia2 = isset($_POST['preposicaoProvincia2'])?$_POST['preposicaoProvincia2']:"";


            if($this->accao=="editarProvincia"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->editarProvincia();
                }
            }else if($this->accao=="salvarProvincia"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->salvarProvincia();
                }
            }else if ($this->accao=="excluirProvincia"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->excluirProvincia();
                }
            }
        }

        private function salvarProvincia(){

            if($this->inserir("div_terit_provincias", "idPProvincia", "idProvPais, nomeProvincia, preposicaoProvincia, preposicaoProvincia2, idEntCad", [$this->idProvPais, $this->nomeProvincia, $this->preposicaoProvincia, $this->preposicaoProvincia2, $_SESSION['idUsuarioLogado']])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar o pais.";
            }            
        }

        private function editarProvincia(){
            $sobre = $this->selectArray("div_terit_provincias", [], ["idPProvincia"=>$this->idPProvincia]);
            
            if($this->editar("div_terit_provincias", "nomeProvincia, preposicaoProvincia, preposicaoProvincia2", [$this->nomeProvincia, $this->preposicaoProvincia, $this->preposicaoProvincia2], ["idPProvincia"=>$this->idPProvincia])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirProvincia(){
            $sobre = $this->selectArray("div_terit_provincias", [], ["idPProvincia"=>$this->idPProvincia]);

            if($this->excluir("div_terit_provincias", ["idPProvincia"=>$this->idPProvincia])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }
        private function listar(){
            echo $this->selectJson("div_terit_provincias", [], ["idProvPais"=>$this->idProvPais], [], "", [], ["nomeProvincia"=>1]);
        }       
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
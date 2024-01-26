<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
        function __construct($caminhoAbsoluto){
            parent::__construct();

            $this->idPPais = filter_input(INPUT_POST, "idPPais", FILTER_SANITIZE_NUMBER_INT);
            $this->continentePais = isset($_POST['continentePais'])?$_POST['continentePais']:"";
            $this->nomePais = isset($_POST['nomePais'])?$_POST['nomePais']:"";
            $this->preposicaoPais = isset($_POST['preposicaoPais'])?$_POST['preposicaoPais']:"";
            $this->preposicaoPais2 = isset($_POST['preposicaoPais2'])?$_POST['preposicaoPais2']:"";


            if($this->accao=="editarPais"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->editarpais();
                }
            }else if($this->accao=="salvarPais"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->salvarPais();
                }
            }else if ($this->accao=="excluirPais"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->excluirpais();
                }
            }
        }

        private function salvarPais(){
            if($this->inserir("div_terit_paises", "idPPais", "continentePais, nomePais, preposicaoPais, preposicaoPais2, idEntCad", [$this->continentePais, $this->nomePais, $this->preposicaoPais, $this->preposicaoPais2, $_SESSION['idUsuarioLogado']])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar o pais.";
            }            
        }

        private function editarpais(){
            $sobre = $this->selectArray("div_terit_paises", ["idPPais"=>$this->idPPais]);

            if($this->editar("div_terit_paises", "continentePais, nomePais, preposicaoPais, preposicaoPais2", [$this->continentePais, $this->nomePais, $this->preposicaoPais, $this->preposicaoPais2], ["idPPais"=>$this->idPPais])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirpais(){
            $sobre = $this->selectArray("div_terit_paises", ["idPPais"=>$this->idPPais]);

            if($this->excluir("div_terit_paises", ["idPPais"=>$this->idPPais])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }
        private function listar(){
            echo $this->selectJson("div_terit_paises", [], [], [], "", [], array("nomePais"=>1));
        }       
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
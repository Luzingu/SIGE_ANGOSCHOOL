<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->idPComuna = filter_input(INPUT_POST, "idPComuna", FILTER_SANITIZE_NUMBER_INT);
            $this->idComunMunicipio = filter_input(INPUT_POST, "idComunMunicipio", FILTER_SANITIZE_NUMBER_INT);

            $this->nomeComuna = isset($_POST['nomeComuna'])?$_POST['nomeComuna']:"";
            $this->preposicaoComuna = isset($_POST['preposicaoComuna'])?$_POST['preposicaoComuna']:"";
            $this->preposicaoComuna2 = isset($_POST['preposicaoComuna2'])?$_POST['preposicaoComuna2']:"";
            
            if($this->accao=="editarComuna"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->editarComuna();
                }
            }else if($this->accao=="salvarComuna"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->salvarComuna();
                }
            }else if ($this->accao=="excluirComuna"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->excluirComuna();
                }
            }
        }

        private function salvarComuna(){

            if($this->inserir("div_terit_comunas", "idPComuna", "idComunMunicipio, nomeComuna, preposicaoComuna,preposicaoComuna2, idEntCad", [$this->idComunMunicipio, $this->nomeComuna, $this->preposicaoComuna, $this->preposicaoComuna2, $_SESSION['idUsuarioLogado']])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar o pais.";
            }            
        }

        private function editarComuna(){
            $sobre = $this->selectArray("div_terit_comunas", [], ["idPComuna"=>$this->idPComuna]);
            
            if($this->editar("div_terit_comunas", "nomeComuna, preposicaoComuna, preposicaoComuna2", [$this->nomeComuna, $this->preposicaoComuna, $this->preposicaoComuna2], ["idPComuna"=>$this->idPComuna])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirComuna(){
            $sobre = $this->selectArray("div_terit_comunas", [], ["idPComuna"=>$this->idPComuna]);

            if($this->excluir("div_terit_comunas", ["idPComuna"=>$this->idPComuna])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }
        private function listar(){
            echo $this->selectJson("div_terit_comunas", [], ["idComunMunicipio"=>$this->idComunMunicipio], [],"", [], ["nomeComuna"=>1]);
        }       
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
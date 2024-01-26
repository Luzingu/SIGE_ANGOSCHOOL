<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->idPMunicipio = filter_input(INPUT_POST, "idPMunicipio", FILTER_SANITIZE_NUMBER_INT);
            $this->idMunProvincia = filter_input(INPUT_POST, "idMunProvincia", FILTER_SANITIZE_NUMBER_INT);

            $this->nomeMunicipio = isset($_POST['nomeMunicipio'])?$_POST['nomeMunicipio']:"";
            $this->preposicaoMunicipio = isset($_POST['preposicaoMunicipio'])?$_POST['preposicaoMunicipio']:"";
            $this->preposicaoMunicipio2 = isset($_POST['preposicaoMunicipio2'])?$_POST['preposicaoMunicipio2']:"";


            if($this->accao=="editarMunicipio"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->editarMunicipio();
                }
            }else if($this->accao=="salvarMunicipio"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->salvarMunicipio();
                }
            }else if ($this->accao=="excluirMunicipio"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array())){
                    $this->excluirMunicipio();
                }
            }
        }

        private function salvarMunicipio(){

            if($this->inserir("div_terit_municipios", "idPMunicipio", "idMunProvincia, nomeMunicipio, preposicaoMunicipio, preposicaoMunicipio2, idEntCad", [$this->idMunProvincia, $this->nomeMunicipio, $this->preposicaoMunicipio, $this->preposicaoMunicipio2, $_SESSION['idUsuarioLogado']])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar o pais.";
            }            
        }

        private function editarMunicipio(){
            $sobre = $this->selectArray("div_terit_municipios", [], ["idPMunicipio"=>$this->idPMunicipio]);
            
             if($this->editar("div_terit_municipios", "nomeMunicipio, preposicaoMunicipio, preposicaoMunicipio2", [$this->nomeMunicipio, $this->preposicaoMunicipio, $this->preposicaoMunicipio2], ["idPMunicipio"=>$this->idPMunicipio])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirMunicipio(){
            $sobre = $this->selectArray("div_terit_municipios", [], ["idPMunicipio"=>$this->idPMunicipio]);

            if($this->excluir("div_terit_municipios", ["idPMunicipio"=>$this->idPMunicipio])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }
        private function listar(){
            echo $this->selectJson("div_terit_municipios", [], ["idMunProvincia"=>$this->idMunProvincia], [], "", [], array("nomeMunicipio"=>1));
            
        }       
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
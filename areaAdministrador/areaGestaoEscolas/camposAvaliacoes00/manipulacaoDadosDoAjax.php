<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct(){
            parent::__construct();

            $this->ordenacao = isset($_POST["ordenacao"])?$_POST["ordenacao"]:"";
            $this->identUnicaDb = isset($_POST["identUnicaDb"])?$_POST["identUnicaDb"]:"";
            $this->designacao1 = isset($_POST["designacao1"])?$_POST["designacao1"]:"";
            $this->designacao2 = isset($_POST["designacao2"])?$_POST["designacao2"]:"";
            $this->periodo = isset($_POST["periodo"])?$_POST["periodo"]:"";
            $this->tipoCampo = isset($_POST["tipoCampo"])?$_POST["tipoCampo"]:"";
            $this->idCampoAvaliacao = isset($_POST["idCampoAvaliacao"])?$_POST["idCampoAvaliacao"]:"";
            $this->seApenasLeitura = isset($_POST["seApenasLeitura"])?"V":"F";
            $this->notaMaxima = isset($_POST["notaMaxima"])?$_POST["notaMaxima"]:"";
            $this->notaMedia = isset($_POST["notaMedia"])?$_POST["notaMedia"]:"";
            $this->notaMinima = isset($_POST["notaMinima"])?$_POST["notaMinima"]:"";
            $this->numeroCasasDecimais = isset($_POST["numeroCasasDecimais"])?$_POST["numeroCasasDecimais"]:"";
            
            if($this->accao=="novaAvaliacao"){
                if($this->verificacaoAcesso->verificarAcesso("", ["camposAvaliacoes00"])){
                  $this->novaAvaliacao();
                }
            }else if($this->accao=="editarAvaliacao"){
                if($this->verificacaoAcesso->verificarAcesso("", ["camposAvaliacoes00"])){
                      $this->editarAvaliacao();
                }
            }else if($this->accao=="excluirAvaliacao"){
                if($this->verificacaoAcesso->verificarAcesso("", ["camposAvaliacoes00"])){
                      $this->excluirAvaliacao();
                }
            }
        }

        private function novaAvaliacao(){
            if($this->inserir("campos_avaliacao", "idCampoAvaliacao", "ordenacao, identUnicaDb, designacao1, designacao2, tipoCampo, seApenasLeitura, notaMaxima, notaMedia, notaMinima, numeroCasasDecimais, dataInsericao, horaInsericao, idUserInsericao", [$this->ordenacao, $this->identUnicaDb, $this->designacao1, $this->designacao2, $this->tipoCampo, $this->seApenasLeitura, $this->notaMaxima, $this->notaMedia, $this->notaMinima, $this->numeroCasasDecimais, $this->dataSistema, $this->tempoSistema, $_SESSION['idUsuarioLogado']])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar novo campo de avaliação.";
            }
        }

        private function editarAvaliacao(){

            if($this->editar("campos_avaliacao", "ordenacao, identUnicaDb, designacao1, designacao2, tipoCampo, seApenasLeitura, notaMaxima, notaMedia, notaMinima, numeroCasasDecimais", [$this->ordenacao, $this->identUnicaDb, $this->designacao1, $this->designacao2, $this->tipoCampo, $this->seApenasLeitura, $this->notaMaxima, $this->notaMedia, $this->notaMinima, $this->numeroCasasDecimais], ["idCampoAvaliacao"=>$this->idCampoAvaliacao])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar novo campo de avaliação.";
            }
        }
        private function excluirAvaliacao(){
            if($this->excluir("campos_avaliacao", ["idCampoAvaliacao"=>$this->idCampoAvaliacao])=="sim"){
                $this->listar();   
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }
        private function listar(){
            $dados  = ordenar($this->selectArray("campos_avaliacao"), "ordenacao ASC");
            echo json_encode($dados);
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>
<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->idPAnexo = $_POST['idPAnexo'];

            $this->identidadeAnexo = filter_input(INPUT_POST, "identidadeAnexo", FILTER_SANITIZE_STRING);
            $this->ordenacaoAnexo = isset($_POST["ordenacaoAnexo"])?$_POST["ordenacaoAnexo"]:"";

            if($this->accao=="editarAnexo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["anexosEscolas"])){
                    $this->editarAnexo();
                }
            }else if($this->accao=="salvarAnexo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["anexosEscolas"])){
                    $this->salvarAnexo();
                }
            }else if ($this->accao=="excluirAnexo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["anexosEscolas"])){
                    $this->excluirAnexo();
                }
            }
        }

        private function salvarAnexo(){
            if($this->inserirObjecto("escolas", "anexos", "idPAnexo", "identidadeAnexo, ordenacaoAnexo,idAnexoEscola", [$this->identidadeAnexo, $this->ordenacaoAnexo,  $_SESSION["idEscolaLogada"]], ["idPEscola"=>$_SESSION['idEscolaLogada']])=="sim"){

                echo json_encode($this->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]));
            }else{
                echo "FNão foi possível cadastrar o anexo.";
            }            
        }

        private function editarAnexo(){

            if($this->editarItemObjecto("escolas", "anexos", "identidadeAnexo, ordenacaoAnexo", [$this->identidadeAnexo, $this->ordenacaoAnexo], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idPAnexo"=>$this->idPAnexo])=="sim"){

                echo json_encode($this->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]));
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirAnexo(){
            
            if(count($this->selectArray("alunosmatriculados", ["nomeAluno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatAnexo"=>$this->idPAnexo], ["escola"], 1))<=0){
                if($this->excluirItemObjecto("escolas", "anexos", ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idPAnexo"=>$this->idPAnexo])=="sim"){
                    echo json_encode($this->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]));
                }else{
                    echo "FNão foi possível excluir os dados.";
                }
            }else{
                echo "FNão podes excluir este anexo.";
            }
            
            
        }       
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
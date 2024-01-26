<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            
            $this->classeDisciplinaOriginal = filter_input(INPUT_POST, "classeDisciplinaOriginal", FILTER_SANITIZE_NUMBER_INT);             
             $this->periodoDisciplina = filter_input(INPUT_POST, "periodoDisciplina", FILTER_SANITIZE_STRING);
             $this->semestreDisciplina = filter_input(INPUT_POST, "semestreDisciplina", FILTER_SANITIZE_STRING);

            $this->idPNomeDisciplina = filter_input(INPUT_POST, "idPNomeDisciplina", FILTER_SANITIZE_NUMBER_INT);
            $this->idPCurso = filter_input(INPUT_POST, "idPCurso", FILTER_SANITIZE_NUMBER_INT);
            $this->anosLectivos =isset($_POST['anosLectivos'])?$_POST['anosLectivos']:"";
            $this->idPExcepcao =isset($_POST['idPExcepcao'])?$_POST['idPExcepcao']:"";

            if($this->accao=="editarExcepcoes" || $this->accao=="novaExcepcao" || $this->accao=="excluirExcepcao")
            {
                
                if($this->verificacaoAcesso->verificarAcesso("", ["curriculoExcepcoes"])){
                    if($this->accao=="editarExcepcoes"){
                        $this->editarExcepcoes();
                    }else if($this->accao=="novaExcepcao"){
                        $this->novaExcepcao();
                    }else if ($this->accao=="excluirExcepcao"){
                        $this->excluirExcepcao();
                    }
                }
            }
        }

        private function novaExcepcao(){
            $chaveDisciplina = $this->periodoDisciplina."-".$this->idPNomeDisciplina."-".$this->classeDisciplinaOriginal."-".$this->idPCurso."-".$_SESSION["idEscolaLogada"]."-".$this->semestreDisciplina;

            if($this->inserir("excepcoes_curriculares", "idPExcepcao", "idPNomeDisciplina, classeDisciplina, chaveDisciplina, idDiscCurso, idDiscEscola, periodoDisciplina, semestreDisciplina, anosLectivos", [$this->idPNomeDisciplina, $this->classeDisciplinaOriginal, $chaveDisciplina,  $this->idPCurso, $_SESSION["idEscolaLogada"], $this->periodoDisciplina, $this->semestreDisciplina, $this->anosLectivos])=="sim")
            {
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar a disciplina.";
            }
        }

        private function editarExcepcoes(){
            if($this->editar("excepcoes_curriculares", "anosLectivos", [$this->anosLectivos], ["idPExcepcao"=>$this->idPExcepcao])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da disciplina.";
            }
        }

        private function excluirExcepcao(){
            if($this->excluir("excepcoes_curriculares", ["idPExcepcao"=>$this->idPExcepcao])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da disciplina.";
            }  
        }
        private function listar(){      
            $listaExcepcoes = $this->selectArray("excepcoes_curriculares", [], ["idDiscEscola"=>$_SESSION['idEscolaLogada'], "classeDisciplina"=>$this->classeDisciplinaOriginal, "periodoDisciplina"=>$this->periodoDisciplina, "idDiscCurso"=>$this->idPCurso]);
          
          $listaExcepcoes = $this->anexarTabela($listaExcepcoes, "nomedisciplinas", "idPNomeDisciplina", "idPNomeDisciplina");
          echo json_encode($listaExcepcoes);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
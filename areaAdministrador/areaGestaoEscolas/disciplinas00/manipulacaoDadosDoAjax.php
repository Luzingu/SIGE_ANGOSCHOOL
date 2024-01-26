<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($tipoBaseDados){
    		parent::__construct();
            $this->tipoBaseDados=$tipoBaseDados;
            $this->idPDisciplina = filter_input(INPUT_POST, "idPDisciplina", FILTER_SANITIZE_NUMBER_INT);

            $this->nomeDisciplina = limpadorEspacosDuplicados(filter_input(INPUT_POST, "nomeDisciplina", FILTER_SANITIZE_STRING));

            $this->abreviacaoDisciplina1 = limpadorEspacosDuplicados(filter_input(INPUT_POST, "abreviacaoDisciplina1", FILTER_SANITIZE_STRING));
            $this->abreviacaoDisciplina2 = limpadorEspacosDuplicados(filter_input(INPUT_POST, "abreviacaoDisciplina2", FILTER_SANITIZE_STRING));

            $this->nivelDisciplina =  filter_input(INPUT_POST, "nivelDisciplina", FILTER_SANITIZE_STRING);

	        if($this->accao=="editarDisciplina"){
                if($this->verificacaoAcesso->verificarAcesso("", ["disciplinas00"])){
	        	    $this->editarDisciplina();
                }
	        }else if($this->accao=="salvarDisciplina"){
                if($this->verificacaoAcesso->verificarAcesso("", ["disciplinas00"])){
	        	  $this->salvarDisciplina();
                }
	        }else if ($this->accao=="excluirDisciplina"){
                if($this->verificacaoAcesso->verificarAcesso("", ["disciplinas00"])){
	        	  $this->excluirDisciplina();
                }
	        }
    	}

        private function salvarDisciplina(){
            if($this->inserir("nomedisciplinas", "idPNomeDisciplina", "nomeDisciplina, nivelDisciplina, abreviacaoDisciplina1, abreviacaoDisciplina2", [$this->nomeDisciplina, $this->nivelDisciplina, $this->abreviacaoDisciplina1, $this->abreviacaoDisciplina2])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar a disciplina.";
            }
        }

        private function editarDisciplina(){
            if($this->editar("nomedisciplinas", "nomeDisciplina, nivelDisciplina, abreviacaoDisciplina1, abreviacaoDisciplina2", [$this->nomeDisciplina, $this->nivelDisciplina, $this->abreviacaoDisciplina1, $this->abreviacaoDisciplina2], ["idPNomeDisciplina"=>$this->idPDisciplina])=="sim"){

                $this->editar("horario", "nomeDisciplina, abreviacaoDisciplina2, abreviacaoDisciplina1", [$this->nomeDisciplina, $this->abreviacaoDisciplina2, $this->abreviacaoDisciplina1], ["idPNomeDisciplina"=>$this->idPDisciplina]);
                $this->editar("divisaoprofessores", "nomeDisciplina, abreviacaoDisciplina2, abreviacaoDisciplina1", [$this->nomeDisciplina, $this->abreviacaoDisciplina2, $this->abreviacaoDisciplina1], ["idPNomeDisciplina"=>$this->idPDisciplina]);

                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da disciplina.";
            }
        }

        private function excluirDisciplina(){
            if(count($this->selectArray("nomedisciplinas", ["idPNomeDisciplina"], ["disciplinas.idFNomeDisciplina"=>$this->idPDisciplina]))>0){
                echo "FNão podes excluir esta disciplina";
            }else if($this->editar("nomedisciplinas", "idPNomeDisciplina", "nomeDisciplina, nivelDisciplina, abreviacaoDisciplina1, abreviacaoDisciplina2", [null, null, null, null], ["idPNomeDisciplina"=>$this->idPDisciplina])=="sim"){

                $this->listar();
            }else{
                echo "FNão foi possível excluir a disciplina.";
            }
        }

        private function listar(){
            if($this->tipoBaseDados=="escola"){
                echo $this->selectJson("nomedisciplinas", [], ["idPNomeDisciplina"=>array('$ne'=>null)], [], "", [], array("nomeDisciplina"=>1));
            }
        }
    	
    }
    new manipulacaoDadosDoAjaxInterno("escola");
    new manipulacaoDadosDoAjaxInterno("teste");
?>
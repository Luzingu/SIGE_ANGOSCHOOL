<?php 
	session_start();
	include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            
            if ($this->accao=="aletrarEstados"){
                if($this->verificacaoAcesso->verificarAcesso("", ["gerenciadorTrimestres"], [])){
                    $this->aletrarEstados();
                }
	        }
    	}

        private function aletrarEstados(){
            $input = isset($_GET["input"])?$_GET["input"]:"";
            $estado = isset($_GET["estado"])?$_GET["estado"]:"";
            $classeP = isset($_GET["classeP"])?$_GET["classeP"]:"";
            $idCursoP = isset($_GET["idCursoP"])?$_GET["idCursoP"]:"";
            $turma = isset($_GET["turma"])?$_GET["turma"]:"";

            $itemAfetar = isset($_GET["itemAfetar"])?$_GET["itemAfetar"]:"";

            $exploder = explode("-", $input);           
            $idPDivisao = isset($exploder[0])?$exploder[0]:"";
            $trimestre = isset($exploder[1])?$exploder[1]:"";

            if($estado=="nao"){
                $trimestre="";
            }
            $condicao = ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$this->idAnoActual];
            if($turma!="luzingu"){
                $condicao["nomeTurmaDiv"]=$turma;
                $condicao["classe"]=$classeP;
                if($classeP>=10){              
                  $condicao["idPNomeCurso"]=$idCursoP;
                }
              }

            if($idPDivisao==0){
                $this->editar("divisaoprofessores", "periodoTrimestre", [$trimestre], $condicao);             
            }else{
                $this->editar("divisaoprofessores", "periodoTrimestre", [$trimestre], ["idPDivisao"=>$idPDivisao]);
            }            
            echo $this->selectJson("divisaoprofessores", ["abrevCurso", "classe", "designacaoTurmaDiv", "abreviacaoDisciplina2", "periodoTrimestre", "idPDivisao", "sePorSemestre"], $condicao);
        }

        

    	
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
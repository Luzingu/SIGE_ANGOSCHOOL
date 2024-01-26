<?php 
	if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/areaSecretaria/novaMatricula/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	
    	function __construct($caminhoAbsoluto){
    		parent::__construct();

	        $this->classeAluno = isset($_POST["classeAluno"])?$_POST["classeAluno"]:"";

	        $this->idPCurso = filter_input(INPUT_POST, "idPCurso", FILTER_SANITIZE_NUMBER_INT);

            $manipulacaoDadosMatricula = new manipulacaoDadosMatricula(__DIR__);
            $manipulacaoDadosMatricula->accao = $this->accao;
	
            if($this->accao=="editarMatriculaInsc"){
                if($this->verificacaoAcesso->verificarAcesso("", ["novaMatriculaInscricao"], [$this->classeAluno, $this->idPCurso])){ 

                    $manipulacaoDadosMatricula->accao="editarMatricula";
                    $manipulacaoDadosMatricula->executarMatricula();
                }else{
                    echo "FNão tens permissão de editar dados dum(a) aluno(a).";
                }

            }else if($this->accao=="salvarMatriculaInsc"){

                

                if(count(listarItensObjecto($this->sobreEscolaLogada, "trans_classes", ["idTransClAno=".$this->idAnoActual, "classeTrans=".$this->classeAluno, "idTransClCurso=".$this->idPCurso]))<0){
                    echo "FAinda não se fez transição dos alunos nesta classe.";
                }else if($this->verificacaoAcesso->verificarAcesso("", ["novaMatriculaInscricao"], [$this->classeAluno, $this->idPCurso], "FNão tens permissão efetuar uma nova matricula dos inscritos nesta classe.")){
                    $manipulacaoDadosMatricula->accao="salvarMatricula";
                    $manipulacaoDadosMatricula->inscreveuSeAntes="V";
                    $manipulacaoDadosMatricula->executarMatricula();
                }
            }else if ($this->accao=="excluirMatriculaInsc"){
                if($this->verificacaoAcesso->verificarAcesso("", ["novaMatriculaInscricao"], [$this->classeAluno, $this->idPCurso])){
                    $manipulacaoDadosMatricula->accao="excluirMatricula";
                   $manipulacaoDadosMatricula->executarMatricula();
                }else{
                    echo "FNão tens permissão de excluir uma matricula.";
                }
            }else if($this->accao=="listarMatriculadosInsc"){
                $this->listarMatriculadosInsc();
            }else if($this->accao=="pegarAlunosQueAindaNaoFizeramMatricula"){
                $this->pegarAlunosQueAindaNaoFizeramMatricula();
            }
    	}

        private function pegarAlunosQueAindaNaoFizeramMatricula(){
            $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->conDb("inscricao");

            echo $this->selectJson("alunos", [], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.idInscricaoCurso"=>$idPCurso, "inscricao.estadoMatricula"=>"F", "inscricao.obsApuramento"=>"A"], ["inscricao"], "", [], ["nomeAluno"=>1]);
        }

        private function listarMatriculadosInsc(){

            $idPCurso = $_GET["idPCurso"];
            $array = $this->selectArray("alunosmatriculados", [], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>"A", "escola.idMatEntidade"=>$_SESSION['idUsuarioLogado'], "escola.idMatCurso"=>$idPCurso, "escola.idMatAno"=>$this->idAnoActual, "escola.inscreveuSeAntes"=>"V"], ["escola"], 100, [], ["idPMatricula"=>-1]);
            $array = $this->anexarTabela2($array, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");

            echo json_encode($array);
       }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
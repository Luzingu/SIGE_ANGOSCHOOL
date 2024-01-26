<?php 
  session_start();
   include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    include_once ('../../manipuladorPauta.php');

    class manipulacaoDadosDoAjaxInternoAvaliacaoAnual extends manipulacaoDadosAjax{

      function __construct($caminhoAbsoluto){
        parent::__construct();
        $this->manipuladorPautas = new manipuladorPauta(); 
        if($this->accao=="actualizarPautas"){

          if($this->verificacaoAcesso->verificarAcesso("", ["actualizarPautas"])){
            $this->actualizarPautas();
          }
        }  
      }

      private function actualizarPautas(){
        $idAlunosVisualizar = isset($_GET["idAlunosVisualizar"])?$_GET["idAlunosVisualizar"]:"";
        $classe = isset($_GET["classe"])?$_GET["classe"]:"";
        $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
        $turma = isset($_GET["turma"])?$_GET["turma"]:"";

        $periodo = retornarPeriodoTurma($this, $idPCurso, $classe, $turma);
        $disciplinas = $this->disciplinas($idPCurso, $classe, $periodo, "", array(), array(), ["idPNomeDisciplina", "disciplinas.semestreDisciplina"]);
        
        $sobreCurso = $this->selectArray("nomecursos", ["cursos.modLinguaEstrangeira", "tipoCurso"], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

        $arrayAluno=array();
        foreach(explode(",", $idAlunosVisualizar) as $aluno){
          $arrayAluno[]=intval($aluno);
        }
        if(trim($idAlunosVisualizar)==""){
          $arrayAluno = array();
        }
        
        foreach($this->alunosPorTurma($idPCurso, $classe, $turma, $this->idAnoActual, $arrayAluno, ["idPMatricula", "escola.classeActualAluno", "grupo", "escola.idGestLinguaEspecialidade", "escola.idGestDisEspecialidade", "escola.idMatCurso"]) as $aluno){
          $this->gravarPautasAluno($aluno["idPMatricula"], $aluno["escola"]["classeActualAluno"], "todas", $disciplinas, $aluno, $sobreCurso);
          $this->manipuladorPautas->calcularObservacaoFinalDoAluno($aluno["idPMatricula"]);
        }
        ;
        echo json_encode($this->alunosPorTurma($idPCurso, $classe, $turma, $this->idAnoActual, array(), ["idPMatricula", "nomeAluno", "numeroInterno", "biAluno", "sexoAluno", "pautas.idPPauta", "fotoAluno", "pautas.classePauta", "pautas.idPautaCurso"]));


      }      
    }
    new manipulacaoDadosDoAjaxInternoAvaliacaoAnual(__DIR__);
?>
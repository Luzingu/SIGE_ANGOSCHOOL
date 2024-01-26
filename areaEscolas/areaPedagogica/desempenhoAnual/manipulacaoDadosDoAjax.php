<?php 
  session_start();
  
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
     include_once ('../../manipuladorPauta.php');
    class manipulacaoDadosDoAjaxInternoAvaliacaoAnual extends manipulacaoDadosAjax{           
      function __construct($caminhoAbsoluto){
        parent::__construct();
        $this->manipuladorPautas = new manipuladorPauta(); 
        if($this->accao=="gravarAvaliacaoAnualAluno"){
          $this->classe = isset($_GET["classe"])?$_GET["classe"]:"";
          $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
          $this->turma = isset($_GET["turma"])?$_GET["turma"]:"";
          $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";
          
           if($this->verificacaoAcesso->verificarAcesso("", ["desempenhoAnual"], [$this->classe, $this->idPCurso])){   

              if($this->idPAno!=$this->idAnoActual){
                echo "FNão podes alterar dados deste ano lectivo.";
              }else{
                $this->gravarAvaliacaoAnualAluno($this->classe, $this->idPCurso, $this->turma);
                $this->listar();                  
              }           
          }         
        }else if($this->accao=="manipularAvaliacaoAnual"){
          $this->classe = $_POST["classe"];
          $this->idPCurso = $_POST["idPCurso"];
          $this->turma = $_POST["turma"];
          $this->idPAno = $_POST["idPAno"];

          if($this->verificacaoAcesso->verificarAcesso("", ["desempenhoAnual"], [$this->classe, $this->idPCurso])){ 
            $this->manipularAvaliacaoAnual();
          }
        }
      }

      
      private function manipularAvaliacaoAnual (){

        $estadoAluno = $_POST["estadoAluno"];
        $idPReconf = $_POST["idPReconf"];
        $idPMatricula = $_POST["idPMatricula"];
        $grupoAluno = $_POST["grupoAluno"];

        $this->editarItemObjecto("alunos_".$grupoAluno, "escola", "estadoDeDesistenciaNaEscola", [$estadoAluno], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);

        if($this->editarItemObjecto("alunos_".$grupoAluno, "reconfirmacoes", "estadoDesistencia", [$estadoAluno], ["idPMatricula"=>$idPMatricula], ["idPReconf"=>$idPReconf])=="sim"){

          $this->manipuladorPautas->calcularObservacaoFinalDoAluno($idPMatricula);
          
          $this->listar();
        }else{
            echo "FNão foi possível alterar os dados.";
        }
      }

      private function listar(){
          echo json_encode($this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idAnoActual, array(), ["nomeAluno", "numeroInterno", "fotoAluno", "reconfirmacoes.observacaoF", "avaliacao_anual.seAlunoFoiAoRecurso", "reconfirmacoes.estadoDesistencia", "reconfirmacoes.mfT1", "reconfirmacoes.mfT2", "reconfirmacoes.mfT3", "grupo", "idPMatricula", "reconfirmacoes.mfT4", "reconfirmacoes.idPReconf"]));
      }
    }
    new manipulacaoDadosDoAjaxInternoAvaliacaoAnual(__DIR__);
?>
<?php 
  session_start();
   include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    include_once ('../../manipuladorPauta.php');

    class manipulacaoDadosDoAjaxInternoAvaliacaoAnual extends manipulacaoDadosAjax{

      function __construct($caminhoAbsoluto){
        parent::__construct();
        $this->manipuladorPautas = new manipuladorPauta(); 
        if($this->accao=="manipularAvalFinalCurso"){

            $this->idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";
            $this->idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:"";
            
            if($this->verificacaoAcesso->verificarAcesso("", ["avalFinalCurso"], [$this->ultimaClasse($this->idPCurso), $this->idPCurso])){
              $this->manipularAvalFinalCurso();
            } 
        }  
      }

      private function manipularAvalFinalCurso(){
        $idPMatricula = $_POST["idPMatricula"];
        $grupoAluno = isset($_POST["grupoAluno"])?$_POST["grupoAluno"]:0;
        $notaExpoTrabalho = isset($_POST["notaExpoTrabalho"])?$_POST["notaExpoTrabalho"]:0;
        $notaTrabEscrito = isset($_POST["notaTrabEscrito"])?$_POST["notaTrabEscrito"]:0;
        $notaEstagio = isset($_POST["notaEstagio"])?$_POST["notaEstagio"]:0;
        $notaRelatorioEstagio = isset($_POST["notaRelatorioEstagio"])?$_POST["notaRelatorioEstagio"]:0;

        $numeroActa = isset($_POST["numeroActa"])?$_POST["numeroActa"]:null;
        $numeroFolha = isset($_POST["numeroFolha"])?$_POST["numeroFolha"]:null;
        $dataDefesa = isset($_POST["dataDefesa"])?$_POST["dataDefesa"]:null;
        $horaDefesa = isset($_POST["horaDefesa"])?$_POST["horaDefesa"]:null;
        $temaTrabalho = isset($_POST["temaTrabalho"])?$_POST["temaTrabalho"]:null;

        $casoPratico = isset($_POST["casoPratico"])?$_POST["casoPratico"]:null;
        $membrosJuri = isset($_POST["membrosJuri"])?$_POST["membrosJuri"]:null;
        $dataConclusao = isset($_POST["dataConclusao"])?$_POST["dataConclusao"]:null;

        $this->idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:null;
        $classe = isset($_POST["classe"])?$_POST["classe"]:null;
        $periodo = isset($_POST["periodo"])?$_POST["periodo"]:null;
        
        $PAP="";
        if(is_numeric($notaExpoTrabalho) && is_numeric($notaExpoTrabalho)){
            $PAP = $notaExpoTrabalho+$notaTrabEscrito;
          $PAP = number_format($PAP, 0);
        }
        
        $notaEstagio = (int)$notaEstagio;
        $notaEstagio = number_format($notaEstagio, 0);
        if(count(explode(":", $horaDefesa))==2){
          $horaDefesa .=":00";
        }

       $this->editarItemObjecto("alunos_".$grupoAluno, "escola", "provAptidao, notaEstagio, notaExposicaoW, notaAvalTrabEscrito, numeroActa, numeroFolha, dataDefesa, horaDefesa, membrosJuriDefesa, temaTrabalho, casoPratico, dataConclusaoCurso, notaRelatorioEstagio", [$PAP, $notaEstagio, $notaExpoTrabalho, $notaTrabEscrito, $numeroActa, $numeroFolha, $dataDefesa, $horaDefesa, $membrosJuri, $temaTrabalho, $casoPratico, $dataConclusao, $notaRelatorioEstagio], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);
        
        $this->manipuladorPautas->calcularObservacaoFinalDoAluno($idPMatricula);
       
        $condicao["escola.idMatEscola"]=$_SESSION['idEscolaLogada'];
        $condicao["escola.idMatCurso"]=$this->idPCurso;

        $expl = explode("_", $classe);

        if(count($expl)>1){
          $condicao["escola.idMatFAno"]=$expl[1];
        }else{
          $condicao["escola.classeActualAluno"]=$classe;
        }

        $array = $this->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "escola.notaExposicaoW", "escola.notaAvalTrabEscrito", "escola.notaEstagio", "escola.notaRelatorioEstagio", "escola.numeroActa", "escola.numeroFolha", "escola.dataDefesa", "escola.horaDefesa", "escola.membrosJuriDefesa", "grupo", "escola.temaTrabalho", "escola.casoPratico", "escola.dataConclusaoCurso", "escola.numeroLivroRegistro", "escola.numeroFolhaRegistro", "escola.numeroPauta", "escola.provAptidao", "escola.notaEstagio"], $condicao, ["escola"], "", [], ["nomeAluno"=>1]);
        echo json_encode($array);
      }      
    }
    new manipulacaoDadosDoAjaxInternoAvaliacaoAnual(__DIR__);
?>
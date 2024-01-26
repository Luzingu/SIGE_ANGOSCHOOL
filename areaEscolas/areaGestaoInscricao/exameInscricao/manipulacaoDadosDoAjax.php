<?php 
  session_start();
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        private $numeroInterno = "";
        private $classe = "";
        private $idPCurso = "";
            

      function __construct($caminhoAbsoluto){
        parent::__construct();

         $estadoInscricao = $this->selectUmElemento("escolas", "estado", ["idPEscola"=>$_SESSION["idEscolaLogada"], "estadoperiodico.objecto"=>"inscricao"], ["estadoperiodico"]);

        if($this->accao=="alterarNotaExame"){
            if($this->verificacaoAcesso->verificarAcesso("", ["divisaoGrupos"], [10, $_POST["idPCurso"]])){
              if($estadoInscricao!="V"){
                echo "FAs inscrições encontram-se encerradas.";
                }else{
                  $this->conDb("inscricao");
                  $this->editar("gestorvagas", "estadoTransicaoCurso", ["F"], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idAnoActual, "idGestCurso"=>$_POST["idPCurso"]]);
                    
                    $this->alterarNotaExame();
                }                    
            }
        }
      }

      private function alterarNotaExame(){
          $numeroProvas = isset($_POST["numeroProvas"])?$_POST["numeroProvas"]:0;
          $idPInscrito = isset($_POST["idPInscrito"])?$_POST["idPInscrito"]:0;
          $idPAluno = isset($_POST["idPAluno"])?$_POST["idPAluno"]:0;
          $idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";

          $nota1 = isset($_POST["nota1"])?$_POST["nota1"]:null;
          $nota2 = isset($_POST["nota2"])?$_POST["nota2"]:null;
          $nota3 = isset($_POST["nota3"])?$_POST["nota3"]:null;

          $acumulador=0;
          $contador=0;
          $mediaFinal=0;
          for($i=1; $i<=$numeroProvas; $i++){
              $contador++;
              $nota = isset($_POST["nota".$i])?$_POST["nota".$i]:0;
              $acumulador +=$nota;
          }
          if($contador==0){
            $mediaFinal=0;
          }else{
            $mediaFinal = $acumulador/$contador;
            $mediaFinal = number_format($mediaFinal, 2);
          }

          $criterioTeste = $this->selectUmElemento("gestorvagas", "criterioTeste", ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idAnoActual, "idGestCurso"=>$idPCurso]);

        if($criterioTeste=="exameAptidao"){

          $this->editarItemObjecto("alunos", "inscricao", "notaExame1, notaExame2, notaExame3, mediaExames, idExameLancProfessor", [$nota1, $nota2, $nota3, $mediaFinal, $_SESSION["idUsuarioLogado"]], ["idPAluno"=>$idPAluno], ["idPInscrito"=>$idPInscrito]);

          echo json_encode($this->selectArray("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$this->idAnoActual, "inscricao.idInscricaoCurso"=>$idPCurso], ["inscricao"], "", [], ["nomeAluno"=>1]));
        }else{
          echo json_encode(array());
        }

      }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
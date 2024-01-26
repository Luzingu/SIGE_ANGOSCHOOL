<?php
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->idCursoCoordenador = filter_input(INPUT_POST, "nomeCoordenador", FILTER_SANITIZE_NUMBER_INT);

            $this->idPNomeCurso = filter_input(INPUT_POST, "idPNomeCurso", FILTER_SANITIZE_NUMBER_INT);

            $this->estadoCurso = filter_input(INPUT_POST, "estadoCurso", FILTER_SANITIZE_STRING);

            $this->modLinguaEst = filter_input(INPUT_POST, "modLinguaEst", FILTER_SANITIZE_STRING);
            $this->campoAvaliar = filter_input(INPUT_POST, "campoAvaliar", FILTER_SANITIZE_STRING);
            $this->semestreActivo = filter_input(INPUT_POST, "semestreActivo", FILTER_SANITIZE_STRING);
            $this->modoPenalizacao = isset($_POST["modoPenalizacao"])?$_POST["modoPenalizacao"]:"";
            $this->paraDiscComNegativas = isset($_POST["paraDiscComNegativas"])?$_POST["paraDiscComNegativas"]:"";
            $this->tipoCurriculo = isset($_POST["tipoCurriculo"])?$_POST["tipoCurriculo"]:"";
            if($this->idCursoCoordenador==-1){
                $this->idCursoCoordenador=NULL;
            }
            if($this->accao=="editarCurso"){
                if($this->verificacaoAcesso->verificarAcesso("", ["cursos"]))
                    $this->editarCurso();
            }else if($this->accao=="salvarCurso"){
                if($this->verificacaoAcesso->verificarAcesso("", ["cursos"]))
                    $this->salvarCurso();
            }else if ($this->accao=="excluirCurso"){
                if($this->verificacaoAcesso->verificarAcesso("", ["cursos"]))
                    $this>excluirCurso();
            }
        }

        private function salvarCurso(){
            $chave = $this->idPNomeCurso."-".$_SESSION["idEscolaLogada"];
            if (!$this->seModoPenValido())
              echo "FModo de Penalização inválida";
            else if($this->idPNomeCurso==3 && ($this->modLinguaEst=="opcional" || $this->modLinguaEst=="naoOpcional"))
                echo "FLíngua de especialidade inválida para este curso.";
            else if($this->idPNomeCurso!=3 && $this->modLinguaEst=="lingEsp")
                echo "FLíngua de especialidade inválida para este curso.";
            else if($this->inserirObjecto("nomecursos", "cursos", "idPCurso", "idFNomeCurso, estadoCurso, idCursoEntidade, idCursoEscola, chaveCurso, modLinguaEstrangeira, campoAvaliar, semestreActivo, modoPenalizacao, paraDiscComNegativas, tipoCurriculo", [$this->idPNomeCurso, $this->estadoCurso, $this->idCursoCoordenador, $_SESSION["idEscolaLogada"], $chave, $this->modLinguaEst, $this->campoAvaliar, $this->semestreActivo, $this->modoPenalizacao, $this->paraDiscComNegativas, $this->tipoCurriculo], ["idPNomeCurso"=>$this->idPNomeCurso])=="sim")
            {
                unset($_SESSION['classesPorCursoPeriodo']);
                unset($_SESSION['classesPorCursoPeriodoFinalista']);
                unset($_SESSION['classesPorCurso']);
                $this->listar();
            }
            else
                echo "FNão foi possível cadastrar o curso.";
        }

        private function editarCurso(){
            if (!$this->seModoPenValido())
              echo "FModo de Penalização inválida";
            else if($this->idPNomeCurso==3 && ($this->modLinguaEst=="opcional" || $this->modLinguaEst=="naoOpcional"))
                echo "FLíngua de especialidade inválida para este curso.";
            else if($this->idPNomeCurso!=3 && $this->modLinguaEst=="lingEsp")
                echo "FLíngua de especialidade inválida para este curso.";
            else if($this->editarItemObjecto("nomecursos", "cursos", "estadoCurso, idCursoEntidade, modLinguaEstrangeira, campoAvaliar, semestreActivo, tipoCurriculo, modoPenalizacao, paraDiscComNegativas", [$this->estadoCurso, $this->idCursoCoordenador, $this->modLinguaEst, $this->campoAvaliar, $this->semestreActivo, $this->tipoCurriculo, $this->modoPenalizacao, $this->paraDiscComNegativas], ["idPNomeCurso"=>$this->idPNomeCurso], ["idCursoEscola"=>$_SESSION["idEscolaLogada"]])=="sim")
                $this->listar();
            else
                echo "FNão foi possível editar os dados do curso.";
        }
        private function excluirCurso(){
            if($this->excluirItemObjecto("nomecursos", "cursos", ["idPNomeCurso"=>$this->idPNomeCurso], ["idCursoEscola"=>$_SESSION["idEscolaLogada"]])=="sim")
                $this->listar();
            else
                echo "FNão foi possível excluir o curso.";
        }
        private function listar(){
            echo $this->selectJson("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"], "", [], ["nomeCurso"=>1]);
        }

        private function seModoPenValido()
        {
          $array = $this->selectArray("nomecursos", ["tipoCurso"], ["idPNomeCurso"=>$this->idPNomeCurso]);
          if($_SESSION["idUsuarioLogado"] != 34)
          {
                if ($this->modoPenalizacao == "apenasNegativas" && valorArray($array, "tipoCurso") != "geral")
                  return (0);
                else if ($this->paraDiscComNegativas == "cadeira" && valorArray($array, "tipoCurso") != "geral")
                  return (0);
                else if ($this->modoPenalizacao != "apenasNegativas" && valorArray($array, "tipoCurso") == "geral")
                  return (0);
                else if ($this->paraDiscComNegativas != "cadeira" && valorArray($array, "tipoCurso") == "geral")
                  return (0);
          }
          return (1);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>

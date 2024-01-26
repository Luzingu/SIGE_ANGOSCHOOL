<?php
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();

            $this->classeDisciplinaOriginal = filter_input(INPUT_POST, "classeDisciplinaOriginal", FILTER_SANITIZE_NUMBER_INT);

            $this->ordemDisciplina = filter_input(INPUT_POST, "ordemDisciplina", FILTER_SANITIZE_NUMBER_INT);

             $this->tipoDisciplina = filter_input(INPUT_POST, "tipoDisciplina", FILTER_SANITIZE_STRING);

             $this->periodoDisciplina = filter_input(INPUT_POST, "periodoDisciplina", FILTER_SANITIZE_STRING);
             $this->semestreDisciplina = filter_input(INPUT_POST, "semestreDisciplina", FILTER_SANITIZE_STRING);

            $this->idPNomeDisciplina = filter_input(INPUT_POST, "idPNomeDisciplina", FILTER_SANITIZE_NUMBER_INT);
            $this->idPCurso = filter_input(INPUT_POST, "idPCurso", FILTER_SANITIZE_NUMBER_INT);
            $this->anosLectivos =isset($_POST['anosLectivos'])?$_POST['anosLectivos']:"";
            $this->curriculo =isset($_POST['curriculo'])?$_POST['curriculo']:"";
            $this->seAdicionarEmTodasEscolas = isset($_POST["seAdicionarEmTodasEscolas"])?"A":"I";

            $this->condicao2 = ["classeDisciplina"=>$this->classeDisciplinaOriginal, "idDiscCurriculo"=>$this->curriculo, "semestreDisciplina"=>$this->semestreDisciplina, "idDiscCurso"=>$this->idPCurso];

            if($this->accao=="editarDisciplina" || $this->accao=="salvarDisciplina" || $this->accao=="excluirDisciplina" || $this->accao=="copiarCurriculo")
            {
                if ($this->accao=="copiarCurriculo"){

                    $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";

                    $array = $this->selectArray("nomecursos", [ "curriculo1", "curriculo2", "curriculo3"], ["idPNomeCurso"=>$idPCurso]);

                    if (valorArray($array, "curriculo1") == $_SESSION['idEscolaLogada'] || valorArray($array, "curriculo2") == $_SESSION['idEscolaLogada'] || valorArray($array, "curriculo3") == $_SESSION['idEscolaLogada'] || valorArray($array, "curriculo1") == 0)
                        $this->copiarCurriculo();
                    else if ($this->verificacaoAcesso->verificarAcesso(["Usuário_Master"]))
                        echo "FNão podes copiar o curriculo na escola.";
                }
                else
                {
                    $array = $this->selectArray("nomecursos", ["curriculo1", "curriculo2", "curriculo3"], ["idPNomeCurso"=>$this->idPCurso]);

                    if (valorArray($array, "curriculo1") == $_SESSION['idEscolaLogada'] || valorArray($array, "curriculo2") == $_SESSION['idEscolaLogada'] || valorArray($array, "curriculo3") == $_SESSION['idEscolaLogada'] || valorArray($array, "curriculo1") == 0)
                    {
                        if($this->accao=="editarDisciplina"){
                            $this->editarDisciplina();
                        }else if($this->accao=="salvarDisciplina"){
                            $this->salvarDisciplina();
                        }else if ($this->accao=="excluirDisciplina"){
                            $this->excluirDisciplina();
                        }
                    }
                    else
                        echo "FNão podes realizar nenhuma operacao no curriculo desta escola.";
                }
            }
        }

        private function salvarDisciplina(){
            //Pegar os campos de avaliacoes...
            $array = $this->selectArray("nomedisciplinas", ["disciplinas.camposAvaliacoes-".$this->idAnoActual, "disciplinas.camposAvaliacoesExt-".$this->idAnoActual], ["disciplinas.idDiscCurriculo"=>$this->curriculo, "disciplinas.classeDisciplina"=>$this->classeDisciplinaOriginal, "disciplinas.idDiscCurso"=>$this->idPCurso], ["disciplinas"], 1);


            $chaveDisciplina = $this->idPNomeDisciplina."-".$this->classeDisciplinaOriginal."-".$this->idPCurso."-".$this->curriculo."-".$this->semestreDisciplina;

            if($this->inserirObjecto("nomedisciplinas", "disciplinas", "idPDisciplina", "idFNomeDisciplina, classeDisciplina, chaveDisciplina, tipoDisciplina, idDiscCurso, idDiscCurriculo, ordenacao, semestreDisciplina, anosLectivos, camposAvaliacoes-".$this->idAnoActual.", camposAvaliacoesExt-".$this->idAnoActual, [$this->idPNomeDisciplina, $this->classeDisciplinaOriginal, $chaveDisciplina, $this->tipoDisciplina,  $this->idPCurso, $this->curriculo, $this->ordemDisciplina, $this->semestreDisciplina, $this->anosLectivos, valorArray($array, "camposAvaliacoes-".$this->idAnoActual), valorArray($array, "camposAvaliacoesExt-".$this->idAnoActual, "disciplinas")], ["idPNomeDisciplina"=>$this->idPNomeDisciplina])=="sim"){

              if($this->seAdicionarEmTodasEscolas == "I")
              {
                foreach ($this->selectArray("nomecursos", ["cursos.idCursoEscola"], ["idPNomeCurso"=>$this->idPCurso, "cursos.tipoCurriculo"=>$this->curriculo], ["cursos"]) as $a) {
                
                  if($a["cursos"]["idCursoEscola"] != $_SESSION["idEscolaLogada"])
                  {
                      foreach (["reg", "pos"] as $p) {

                        $chaveDisciplina = $p."-".$this->idPNomeDisciplina."-".$this->classeDisciplinaOriginal."-".$this->idPCurso."-".$a["cursos"]["idCursoEscola"]."-".$this->semestreDisciplina;

                        $this->inserir("excepcoes_curriculares", "idPExcepcao", "idPNomeDisciplina, classeDisciplina, chaveDisciplina, idDiscCurso, idDiscEscola, periodoDisciplina, semestreDisciplina, anosLectivos", [$this->idPNomeDisciplina, $this->classeDisciplinaOriginal, $chaveDisciplina,  $this->idPCurso, $a["cursos"]["idCursoEscola"], $p, $this->semestreDisciplina, ""]);
                      }
                  }
                }
              }
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar a disciplina.";
            }
        }

        private function editarDisciplina(){
            if($this->editarItemObjecto("nomedisciplinas", "disciplinas", "anosLectivos, tipoDisciplina, ordenacao", [$this->anosLectivos, $this->tipoDisciplina, $this->ordemDisciplina], ["idPNomeDisciplina"=>$this->idPNomeDisciplina], $this->condicao2)=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da disciplina.";
            }
        }

        private function excluirDisciplina(){

            if($this->excluirItemObjecto("nomedisciplinas", "disciplinas", ["idPNomeDisciplina"=>$this->idPNomeDisciplina], $this->condicao2) == "sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da disciplina.";
            }
        }

        private function copiarCurriculo(){
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->classeDisciplinaOriginal = isset($_GET['classe'])?$_GET['classe']:"";
            $this->periodoDisciplina = isset($_GET['periodo'])?$_GET['periodo']:"";
            $idCurriculoCopiar = isset($_GET['idCurriculoCopiar'])?$_GET['idCurriculoCopiar']:"";
            $this->curriculo = isset($_GET['idCurriculoCurso'])?$_GET['idCurriculoCurso']:"";

            if (count($arrayDisciplinas = $this->selectArray("nomedisciplinas", ["idPNomeDisciplina"], ["disciplinas.idDiscCurriculo"=>$idCurriculoCopiar, "disciplinas.idDiscCurso"=>$this->idPCurso], ["disciplinas"], 2)) <= 0)
            {
                echo "FO curriculo que pretendes copiar esta vazia.";
                return (0);
            }

            $arrayDisciplinas = $this->selectArray("nomedisciplinas", ["disciplinas.idPDisciplina", "idPNomeDisciplina"], ["disciplinas.idDiscCurriculo"=>$this->curriculo, "disciplinas.classeDisciplina"=>$this->classeDisciplinaOriginal, "disciplinas.idDiscCurso"=>$this->idPCurso], ["disciplinas"]);

            foreach($arrayDisciplinas as $a){
                $this->excluirItemObjecto("nomedisciplinas", "disciplinas", ["idPNomeDisciplina"=>$a["idPNomeDisciplina"]], ["idPDisciplina"=>$a["disciplinas"]["idPDisciplina"]]);
            }
            $arrayDisciplinas = $this->selectArray("nomedisciplinas", [], ["disciplinas.idDiscCurriculo"=>$idCurriculoCopiar, "disciplinas.idDiscCurso"=>$this->idPCurso], ["disciplinas"]);

            foreach($arrayDisciplinas as $a){

                $chaveDisciplina = $a["idPNomeDisciplina"]."-".$a["disciplinas"]["classeDisciplina"]."-".$this->idPCurso."-".$this->curriculo."-".$a["disciplinas"]["semestreDisciplina"];

                $this->inserirObjecto("nomedisciplinas", "disciplinas", "idPDisciplina", "idFNomeDisciplina, anosLectivos, classeDisciplina, chaveDisciplina, tipoDisciplina, idDiscCurso, idDiscCurriculo, ordenacao, semestreDisciplina", [$a["idPNomeDisciplina"], valorArray($a, "anosLectivos", "disciplinas"), $a["disciplinas"]["classeDisciplina"], $chaveDisciplina, $a["disciplinas"]["tipoDisciplina"],  $this->idPCurso, $this->curriculo, $a["disciplinas"]["ordenacao"], $a["disciplinas"]["semestreDisciplina"]], ["idPNomeDisciplina"=>$a["idPNomeDisciplina"]]);
            }
            $this->listar();
        }

        private function listar(){
          echo $this->selectJson("nomedisciplinas", [], ["disciplinas.idDiscCurriculo"=>$this->curriculo, "disciplinas.classeDisciplina"=>$this->classeDisciplinaOriginal, "disciplinas.idDiscCurso"=>$this->idPCurso], ["disciplinas"], "", [], ["disciplinas.ordenacao"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>

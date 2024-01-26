<?php
     if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    include_once ('../../manipuladorPauta.php');

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->manipuladorPautas = new manipuladorPauta();

            $this->idPCurso = "";
            if(isset($_GET["idPCurso"]))
              $this->idPCurso = $_GET["idPCurso"];
            else if(isset($_POST["idPCurso"]))
              $this->idPCurso = $_POST["idPCurso"];

            $this->classe = "";
            if(isset($_GET["classe"]))
              $this->classe = $_GET["classe"];
            else if(isset($_POST["classe"]))
              $this->classe = $_POST["classe"];

            $this->turma = "";
            if(isset($_GET["turma"]))
              $this->turma = $_GET["turma"];
            else if(isset($_POST["turma"]))
              $this->turma = $_POST["turma"];

            $this->trimestre = "";
            if(isset($_GET["trimestre"]))
              $this->trimestre = $_GET["trimestre"];
            else if(isset($_POST["trimestre"]))
              $this->trimestre = $_POST["trimestre"];

            $this->tipoCurso = "";
            if(isset($_GET["tipoCurso"]))
              $this->tipoCurso = $_GET["tipoCurso"];
            else if(isset($_POST["tipoCurso"]))
              $this->tipoCurso = $_POST["tipoCurso"];

            $this->periodoTurma = "";
            if(isset($_GET["periodoTurma"]))
              $this->periodoTurma = $_GET["periodoTurma"];
            else if(isset($_POST["periodoTurma"]))
              $this->periodoTurma = $_POST["periodoTurma"];

            $this->manipuladorPautas->estadoConselhoTurma="V";
            $this->planoCurricular = $this->disciplinas($this->idPCurso, $this->classe, $this->periodoTurma, "", array(), array(), ["idPNomeDisciplina", "disciplinas.classeDisciplina", "nomeDisciplina", "disciplinas.semestreDisciplina", "disciplinas.semestreDisciplina", "disciplinas.continuidadeDisciplina", "disciplinas.tipoDisciplina"]);
            $this->manipuladorPautas->curriculoDaClasse = $this->disciplinas;

            $this->semestreActivo = retornarSemestreActivo($this, $this->idPCurso, $this->classe);

            if($this->accao=="alterarNotas"){

                if($this->verificacaoAcesso->verificarAcesso("", ["pautaConselhoNotas1"], [], "") || count($this->selectArray("listaturmas", ["nomeTurma"], ["classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "nomeTurma"=>$this->turma, "idPresidenteConselho"=>$_SESSION["idUsuarioLogado"], "idPNomeCurso"=>$this->idPCurso]))>0){


                    $this->manipuladorPautas->estadoConselhoTurma="V";
                    $this->alterarNotas();
                }else{
                    echo "FNão tens permissão de alterar notas desta turma.";
                }
           }else if($this->accao=="buscarDadosAluno"){
                $this->idPMatricula = isset($_GET["idAlunoSeleccionado"])?$_GET["idAlunoSeleccionado"]:null;
                echo json_encode($this->retornarPautas());
           }
        }
        private function alterarNotas(){
            $notas = json_decode($_POST["dados"]);
            $this->idPMatricula = "";
            if(isset($_GET['idAlunoSeleccionado']))
              $this->idPMatricula = $_GET['idAlunoSeleccionado'];
            else if(isset($_POST['idAlunoSeleccionado']))
              $this->idPMatricula = $_POST['idAlunoSeleccionado'];

            $estadoAluno = "";
            if(isset($_GET['estadoAluno']))
              $estadoAluno = $_GET['estadoAluno'];
            else if(isset($_POST['estadoAluno']))
              $estadoAluno = $_POST['estadoAluno'];

            $grupoAluno = "";
            if(isset($_GET['grupoAluno']))
              $grupoAluno = $_GET['grupoAluno'];
            else if(isset($_POST['grupoAluno']))
              $grupoAluno = $_POST['grupoAluno'];

            $observacaoF="NA";
            if($estadoAluno=="D" || $estadoAluno=="N" || $estadoAluno=="F" || $estadoAluno=="RI" || $estadoAluno=="NA/TRANSF" || $estadoAluno=="RFN" || $estadoAluno=="A/TRANSF"){
                $observacaoF=$estadoAluno;
            }
            $this->editarItemObjecto("alunos_".$grupoAluno, "reconfirmacoes", "estadoDesistencia, observacaoF", [$estadoAluno, $observacaoF], ["idPMatricula"=>$this->idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada']]);

            $this->editarItemObjecto("alunos_".$grupoAluno, "escola", "estadoDeDesistenciaNaEscola", [$estadoAluno], ["idPMatricula"=>$this->idPMatricula], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);
            $msgRetorno=array();
            $this->periodo = retornarPeriodoTurma($this, $this->idPCurso,  $this->classe, $this->turma);
            $i=0;
            foreach ($notas as $nota) {
                $i++;
                $idPDisciplina = isset($nota->idPDisciplina)?$nota->idPDisciplina:null;
                $this->camposAvaliacaoAlunos($this->idAnoActual, $this->idPCurso, $this->classe, $this->periodo, $idPDisciplina, "");
                $this->manipuladorPautas->camposPautas = $this->camposPautas;
                $this->manipuladorPautas->camposArquivoPautas = $this->camposArquivoPautas;
                $this->manipuladorPautas->camposAvaliacao = $this->camposAvaliacao;
                $this->manipuladorPautas->trimestres = $this->trimestres;

                $this->array = $this->selectArray("divisaoprofessores", ["periodoTrimestre", "avaliacoesContinuas"], ["nomeTurmaDiv"=>$this->turma, "classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idAnoActual, "idPNomeDisciplina"=>$idPDisciplina, "semestre"=>$this->semestreActivo, "idPNomeCurso"=>$this->idPCurso]);

                $retorno = $this->manipuladorPautas->alterPautaMod_2020($this->classe, $this->idPMatricula, $idPDisciplina, "--", "conselho", $this->turma, $this->semestreActivo, $this->array, $nota->avaliacoesQuantitativas, array());
                $msgRetorno[] = array('msg'=>$retorno);
            }
            $this->manipuladorPautas->calcularObservacaoFinalDoAluno($this->idPMatricula, array());
            $humbert[0] = $msgRetorno;
            $humbert[1] = $this->retornarPautas();
            echo json_encode($humbert);
        }

        function retornarPautas(){

            $condicaoAdicional = ["reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "pautas.classePauta"=>$this->classe, "pautas.semestrePauta"=>$this->semestreActivo, "pautas.idPautaCurso"=>$this->idPCurso];
            if($this->tipoCurso=="pedagogico"){
                $condicaoAdicional["pautas.idPautaDisciplina"]=['$nin'=>array(51, 140)];
            }

            $retorno = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idAnoActual, [intval($this->idPMatricula)], [], ["reconfirmacoes", "pautas"], $condicaoAdicional);
            $i=0;
            $pautaAluno=array();
            foreach($retorno as $nota){
                foreach($this->planoCurricular as $curriculo){
                    if($curriculo["disciplinas"]["classeDisciplina"]==$nota["pautas"]["classePauta"] && $curriculo["disciplinas"]["semestreDisciplina"]==$nota["pautas"]["semestrePauta"] && $curriculo["idPNomeDisciplina"]==$nota["pautas"]["idPautaDisciplina"]){

                        $pautaAluno[$i]=$nota;
                        $pautaAluno[$i]["nomeDisciplina"]=$curriculo["nomeDisciplina"];
                        $pautaAluno[$i]["idPNomeDisciplina"]=$curriculo["idPNomeDisciplina"];
                        $pautaAluno[$i]["semestreDisciplina"]=$curriculo["disciplinas"]["semestreDisciplina"];
                        $pautaAluno[$i]["continuidadeDisciplina"]=$curriculo["disciplinas"]["continuidadeDisciplina"];
                        $pautaAluno[$i]["tipoDisciplina"]=$curriculo["disciplinas"]["tipoDisciplina"];
                        $i++;
                    }
                }
            }
            $pautaAluno = ordenar($pautaAluno,"mf ASC");
            return $pautaAluno;
        }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);

?>

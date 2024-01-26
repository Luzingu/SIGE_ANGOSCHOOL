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


            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:$_POST["idPCurso"];
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:$_POST["classe"];
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:$_POST["turma"];
            $this->tipoCurso = isset($_GET["tipoCurso"])?$_GET["tipoCurso"]:$_POST["tipoCurso"];
            $this->periodoTurma = isset($_GET["periodoTurma"])?$_GET["periodoTurma"]:$_POST["periodoTurma"];
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$_POST["idPAno"];

            $this->manipuladorPautas->idAnoActual = $this->idPAno;

            $this->manipuladorPautas->estadoConselhoTurma="V";

            //Curriculo deste ano lectivo...
            $this->manipuladorPautas->curriculoDaClasse = $this->planoCurricular = $this->disciplinas($this->idPCurso, $this->classe, $this->periodoTurma, "", array(), array(), ["idPNomeDisciplina", "disciplinas.classeDisciplina", "nomeDisciplina", "disciplinas.semestreDisciplina", "disciplinas.semestreDisciplina", "disciplinas.continuidadeDisciplina", "disciplinas.periodoDisciplina", "disciplinas.tipoDisciplina"], "todas");

            $this->semestreActivo = retornarSemestreActivo($this, $this->idPCurso, $this->classe);

            if($this->accao=="alterarNotas"){

                if($this->verificacaoAcesso->verificarAcesso("", ["transicaoManual"], [])){
                    $this->alterarNotas();
                }else{
                    echo "FNão tens permissão de alterar notas desta turma.";
                }
           }else if($this->accao=="carregarPautas"){

                if($this->verificacaoAcesso->verificarAcesso("", ["transicaoManual"], [])){
                    $this->carregarPautas();
                }else{
                    echo "FNão tens permissão de alterar notas desta turma.";
                }
           }else if($this->accao=="copiarDados"){

                if($this->verificacaoAcesso->verificarAcesso("", ["transicaoManual"], [])){
                    $this->copiarDados();
                }else{
                    echo "FNão tens permissão de alterar notas desta turma.";
                }
           }else if($this->accao=="buscarDadosAluno"){
                $this->idPMatricula = isset($_GET["idAlunoSeleccionado"])?$_GET["idAlunoSeleccionado"]:null;
                echo json_encode($this->retornarPautas());
           }else if($this->accao=="actualizarEstadoAluno"){
                if($this->verificacaoAcesso->verificarAcesso("", ["transicaoManual"], [])){
                    $this->actualizarEstadoAluno();
                }
           }
        }

        private function carregarPautas(){
            $idAlunoSeleccionado = isset($_GET["idAlunoSeleccionado"])?$_GET["idAlunoSeleccionado"]:"";
            $this->gravarPautasAluno($idAlunoSeleccionado, $this->classe, "nao");
        }

        private  function actualizarEstadoAluno(){
            $idAlunoSeleccionado = isset($_GET["idAlunoSeleccionado"])?$_GET["idAlunoSeleccionado"]:"";

            $array = $this->selectArray("alunosmatriculados", ["reconfirmacoes.observacaoF", "grupo", "escola.classeActualAluno", "escola.idCursos"], ["idPMatricula"=>$idAlunoSeleccionado,  "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["reconfirmacoes", "escola"]);

            $sobreCurso = $this->selectArray("nomecursos", ["cursos.modoPenalizacao", "cursos.paraDiscComNegativas", "ultimaClasse", "classes.identificador", "classes.notaMedia", "classes.ordem"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$this->idPCurso], ["cursos"], 1);

            $grupo = valorArray($array, "grupo");
            $ultimaClasse = valorArray($sobreCurso, "ultimaClasse");
            $notaMedia = valorArray(listarItensObjecto($sobreCurso, "classes", ["identificador=".valorArray($array, "classeActualAluno", "escola")]), "notaMedia");

            $novaClasse="";
            $classesOrdenadas =ordenar(listarItensObjecto($sobreCurso, "classes"), "ordem ASC");
            $posicao=0;
            foreach($classesOrdenadas as $classe){
                $posicao++;
                if($classe["identificador"]==valorArray($array, "classeActualAluno", "escola")){
                    break;
                }
            }
            $i=0;
            foreach($classesOrdenadas as $classe){
                $i++;
                if($i == ($posicao + 1))
                {
                    $novaClasse = $classe["identificador"];
                    break;
                }
            }

            if(valorArray($array, "classeActualAluno", "escola")==$ultimaClasse){
                $idMatFAno = $this->idAnoActual;
                $estadoAluno="Y";
                $classeSeguinte=120;
            }else{
                $idMatFAno="";
                $estadoAluno="A";
                $classeSeguinte=$novaClasse;

                if(valorArray($sobreCurso, "paraDiscComNegativas", "cursos")=="cadeira"){

                    foreach ($this->selectArray("alunos_".$grupo, ["pautas.idPPauta", "pautas.idPautaDisciplina"], ["idPMatricula"=>$idAlunoSeleccionado, "pautas.classePauta"=>valorArray($array, "classeActualAluno", "escola"), "pautas.idPautaCurso"=>$this->idPCurso, "pautas.mf"=>array('$lt'=>$notaMedia)], ["pautas"]) as $disciplinas) {

                        $this->editarItemObjecto("alunos_".$grupo, "pautas", "obs", ["cad"], ["idPMatricula"=>$idAlunoSeleccionado], ["idPPauta"=>$disciplinas["pautas"]["idPPauta"]]);
                        $this->inserirObjecto("alunos_".$grupo, "cadeiras_atraso", "idPCadeirantes", "idCadMatricula, idCadDisciplina, idCadCurso, classeCadeira, idCadAno, estadoCadeira, idCadEscola", [$idPMatricula, $disciplinas["pautas"]["idPautaDisciplina"], $this->idPCurso, $this->classeTrans, $this->idAnoActual, "F", $_SESSION["idEscolaLogada"]], ["idPMatricula"=>$idAlunoSeleccionado]);
                    }
                }
            }
            $idCursos = valorArray($array, "idCursos", "escola");
            $arrayIds=array();
            $i=0;
            foreach($idCursos as $id){
                $arrayIds[$i]=$id;
                if($id["idMatCurso"]==$this->idPCurso){
                    $arrayIds[$i]["idMatFAno"]=$idMatFAno;
                    $arrayIds[$i]["estadoAluno"]=$estadoAluno;
                    $arrayIds[$i]["classeActualAluno"]=$classeSeguinte;
                }
                $i++;
            }
            $this->editarItemObjecto("alunos_".$grupo, "escola", "idMatFAno, estadoAluno, classeActualAluno, idCursos", [$idMatFAno, $estadoAluno, $classeSeguinte, $arrayIds], ["idPMatricula"=>$idAlunoSeleccionado], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);
            echo "V";
        }
        private  function copiarDados(){
            $idAlunoSeleccionado = isset($_GET["idAlunoSeleccionado"])?$_GET["idAlunoSeleccionado"]:"";
            $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $classe = isset($_GET["classe"])?$_GET["classe"]:"";
            $turma = isset($_GET["turma"])?$_GET["turma"]:"";

            $periodo = retornarPeriodoTurma($this, $idPCurso,  $classe, $turma, $this->idPAno);

            $array = $this->selectArray("alunosmatriculados", [], ["idPMatricula"=>$idAlunoSeleccionado]);

            $condicao=["classePauta=".$classe, "idPautaCurso=".$idPCurso];
            foreach(listarItensObjecto($array, "pautas", $condicao) as $pauta){
                $condicao=["classePauta=".$classe, "idPautaCurso=".$idPCurso];
                $condicao[]="idPautaDisciplina=".$pauta["idPautaDisciplina"];
                $condicao[]="idPautaEscola=".$_SESSION['idEscolaLogada'];
                $condicao[]="idPautaAno=".$this->idPAno;

                $arquivo_pauta = listarItensObjecto($array, "arquivo_pautas", $condicao);
                foreach($arquivo_pauta as $arquivo){
                   $pt=$arquivo;
                }

                if(count($arquivo_pauta)>1){
                    $pt = $arquivo_pauta[0];
                    if(valorArray($arquivo_pauta[0], "mf")<valorArray($arquivo_pauta[1], "mf")){
                        $pt = $arquivo_pauta[1];
                    }
                }

                $stringsCampos ="";
                $valorCampos=array();

                $this->camposAvaliacaoAlunos($this->idPAno, $idPCurso, $classe, $periodo, $pauta["idPautaDisciplina"]);
                foreach($this->camposAvaliacao as $campo){
                    if($stringsCampos!=""){
                        $stringsCampos .=",";
                    }
                    $stringsCampos .=trim($campo["identUnicaDb"]);
                    $valorCampos[]=valorArray($pt, trim($campo["identUnicaDb"]));
                }
                $this->editarItemObjecto("alunos_".valorArray($array, "grupo"), "pautas", $stringsCampos, $valorCampos, ["idPMatricula"=>valorArray($array, "idPMatricula")], ["idPPauta"=>$pauta["idPPauta"]]);
            }

        }
        private function alterarNotas(){
            $notas = json_decode($_POST["dados"]);
            $this->idPMatricula = isset($_POST['idAlunoSeleccionado'])?$_POST['idAlunoSeleccionado']:"";
            $estadoAluno = isset($_POST['estadoAluno'])?$_POST['estadoAluno']:"";
            $grupoAluno = isset($_POST['grupoAluno'])?$_POST['grupoAluno']:"";
            $idPAnoPassado = valorArray($this->selectArray("anolectivo", ["idPAno", "numAno"], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"], "anos_lectivos.estadoAnoL"=>array('$ne'=>"V")], ["anos_lectivos"], 1, [], ["numAno"=>-1]), "idPAno");

            if ($this->idPAno==$idPAnoPassado){
                $observacaoF="NA";
                if($estadoAluno=="D" || $estadoAluno=="N" || $estadoAluno=="F" || $estadoAluno=="RI" || $estadoAluno=="NA/TRANSF" || $estadoAluno=="RFN" || $estadoAluno=="A/TRANSF"){
                    $observacaoF=$estadoAluno;
                }
                $this->editarItemObjecto("alunos_".$grupoAluno, "reconfirmacoes", "estadoDesistencia, observacaoF", [$estadoAluno, $observacaoF], ["idPMatricula"=>$this->idPMatricula], ["idReconfAno"=>$this->idPAno, "idReconfEscola"=>$_SESSION['idEscolaLogada']]);

                $this->editarItemObjecto("alunos_".$grupoAluno, "escola", "estadoDeDesistenciaNaEscola", [$estadoAluno], ["idPMatricula"=>$this->idPMatricula], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);
            }
            $msgRetorno=array();
            $this->periodo = retornarPeriodoTurma($this, $this->idPCurso,  $this->classe, $this->turma, $this->idPAno);
            $i=0;

            foreach ($notas as $nota) {
                $i++;
                $idPDisciplina = isset($nota->idPDisciplina)?$nota->idPDisciplina:null;
                $this->camposAvaliacaoAlunos($this->idPAno, $this->idPCurso, $this->classe, $this->periodo, $idPDisciplina, "");
                $this->manipuladorPautas->camposPautas = $this->camposPautas;
                $this->manipuladorPautas->camposArquivoPautas = $this->camposArquivoPautas;
                $this->manipuladorPautas->camposAvaliacao = $this->camposAvaliacao;
                $this->manipuladorPautas->trimestres = $this->trimestres;

                $this->array = $this->selectCondClasseCurso("array", "divisaoprofessores", ["periodoTrimestre", "avaliacoesContinuas"], ["nomeTurmaDiv"=>$this->turma, "classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idPAno, "idPNomeDisciplina"=>$idPDisciplina, "semestre"=>$this->semestreActivo], $this->classe,  ["idPNomeCurso"=>$this->idPCurso]);
                $retorno = $this->manipuladorPautas->alterPautaMod_2020($this->classe, $this->idPMatricula, $idPDisciplina, "--", "transicao", $this->turma, $this->semestreActivo, $this->array, $nota->avaliacoesQuantitativas, array());
                $msgRetorno[] = array('msg'=>$retorno);
            }
            if ($this->idPAno==$idPAnoPassado){
                $this->manipuladorPautas->curriculoDaClasse = $this->planoCurricular = $this->disciplinas($this->idPCurso, $this->classe, $this->periodoTurma, "", array(), array(), ["idPNomeDisciplina", "disciplinas.classeDisciplina", "nomeDisciplina", "disciplinas.semestreDisciplina", "disciplinas.semestreDisciplina", "disciplinas.continuidadeDisciplina", "disciplinas.periodoDisciplina", "disciplinas.tipoDisciplina"], $this->idPAno);
                $this->manipuladorPautas->calcularObservacaoFinalDoAluno($this->idPMatricula);
            }
            $humbert[0] = $msgRetorno;
            $humbert[1] = $this->retornarPautas();
            echo json_encode($humbert);
        }

        function retornarPautas(){

            $condicaoAdicional = ["pautas.classePauta"=>$this->classe, "pautas.semestrePauta"=>$this->semestreActivo, "pautas.idPautaCurso"=>$this->idPCurso];
            if($this->tipoCurso=="pedagogico"){
                $condicaoAdicional["pautas.idPautaDisciplina"]=['$nin'=>array(51, 140)];
            }
            $retorno = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, [intval($this->idPMatricula)], array(), ["pautas"], $condicaoAdicional);

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

<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
        session_cache_expire(60);
      session_start();
    }
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');

    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosDoAjax.php');

    class manipulacaoDadosAjax extends manipulacaoDadosAjaxMae{

       function __construct(){
            parent::__construct();
        }
        public function seEPossivelEditarClasseCurso($classeNovaAluno, $idCursoNovo, $grupo){

            $sobreAprovacaoEmOutraClasse = array();
            $notasPositivas = array();
            $retorno="sim";

            if(valorArray($this->sobreAluno, "classeActualAluno", "escola")!=$classeNovaAluno || valorArray($this->sobreAluno, "idMatCurso", "escola")!=$idCursoNovo){

                if(valorArray($this->sobreAluno, "classeActualAluno", "escola")!=$classeNovaAluno && valorArray($this->sobreAluno, "idMatCurso", "escola")==$idCursoNovo){

                    if(count(listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno!=".$this->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada'], "estadoReconfirmacao=A", "idMatCurso=".$idCursoNovo]))>0){
                        echo $retorno ="FNão podes editar classe ou curso do aluno.";
                    }else{
                        $idMatFAno="";
                        if($classeNovaAluno==120){
                            $riti = isset($_POST["classeAluno"])?$_POST["classeAluno"]:"";
                            $idMatFAno = isset(explode("_", $riti)[1])?explode("_", $riti)[1]:"";
                            $idMatFAno = trim($idMatFAno);
                        }
                        $this->editarItemObjecto("alunos_".$grupo, "escola", "classeActualAluno, idMatFAno", [$classeNovaAluno, $idMatFAno], ["idPMatricula"=>valorArray($this->sobreAluno, "idPMatricula")],["idMatEscola"=>$_SESSION['idEscolaLogada']]);

                        if(!isset($_POST['seCursoProvisorio']) || (isset($_POST['seCursoProvisorio']) && $_POST['seCursoProvisorio']=="nao") ){

                            if($classeNovaAluno==120){
                                $this->excluirItemObjecto("alunos_".$grupo, "reconfirmacoes", ["idPMatricula"=>valorArray($this->sobreAluno, "idPMatricula")],["idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>$idCursoNovo, "idReconfAno"=>$this->idAnoActual]);
                            }else{
                                $this->editarItemObjecto("alunos_".$grupo, "reconfirmacoes", "classeReconfirmacao", [$classeNovaAluno], ["idPMatricula"=>valorArray($this->sobreAluno, "idPMatricula")],["idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>$idCursoNovo, "idReconfAno"=>$this->idAnoActual]);
                                $this->gravarPautasAluno(valorArray($this->sobreAluno, "idPMatricula"));
                                $this->atualizarTurma(valorArray($this->sobreAluno, "idPMatricula"), $classeNovaAluno, $idCursoNovo, "", $grupo);
                            }
                            $this->actuazalizarReconfirmacaAluno(valorArray($this->sobreAluno, "idPMatricula"));
                        }
                    }
                }else{
                    $idMatFAno="";
                    if($classeNovaAluno==120){
                        $riti = isset($_POST["classeAluno"])?$_POST["classeAluno"]:"";
                        $idMatFAno = isset(explode("_", $riti)[1])?explode("_", $riti)[1]:"";
                        $idMatFAno = trim($idMatFAno);
                    }

                    $idCursoNaoAdicionar=valorArray($this->sobreAluno, "idMatCurso", "escola");
                    //verificar se tem uma reconfirmação dos anos anteriores nestre curso...
                    if(count(listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno!=".$this->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada'], "estadoReconfirmacao=A", "idMatCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]))>0){
                        $idCursoNaoAdicionar="";
                        $cursoEntrar="sim";
                    }
                    $this->editarItemObjecto("alunos_".$grupo, "escola", "classeActualAluno, idMatCurso, idMatFAno", [$classeNovaAluno, $idCursoNovo, $idMatFAno], ["idPMatricula"=>valorArray($this->sobreAluno, "idPMatricula")],["idMatEscola"=>$_SESSION['idEscolaLogada']]);

                    $this->tratarArrayDeCursos($this->idPMatricula, $idCursoNovo, $idCursoNaoAdicionar);

                    if(!isset($_POST['seCursoProvisorio']) || (isset($_POST['seCursoProvisorio']) && $_POST['seCursoProvisorio']=="nao")){
                        if($classeNovaAluno==120){
                            $this->editarItemObjecto("alunos_".$grupo, "reconfirmacoes", "estadoReconfirmacao, classeReconfirmacao, idMatCurso, chaveReconf", ["A", "".$classeNovaAluno."", $idCursoNovo, valorArray($this->sobreAluno, "idPMatricula")."-".$idCursoNovo."-".$this->idAnoActual."-".$_SESSION["idEscolaLogada"]], ["idPMatricula"=>valorArray($this->sobreAluno, "idPMatricula")], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada']]);
                            $this->gravarPautasAluno(valorArray($this->sobreAluno, "idPMatricula"));
                            $this->atualizarTurma(valorArray($this->sobreAluno, "idPMatricula"), $classeNovaAluno, $idCursoNovo, "", $grupo);
                        }else{
                            $this->excluirItemObjecto("alunos_".$grupo, "reconfirmacoes", ["idPMatricula"=>valorArray($this->sobreAluno, "idPMatricula")],["idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>valorArray($this->sobreAluno, "classeActualAluno", "escola"), "idReconfAno"=>$this->idAnoActual]);
                        }
                        $this->actuazalizarReconfirmacaAluno(valorArray($this->sobreAluno, "idPMatricula"));
                    }
                }
            }
            return $retorno;
       }

       public function atualizarTurma($idPMatricula, $classe, $idPCurso, $turma="", $grupo){

        $this->sobreAluno($idPMatricula, ["escola.periodoAluno", "escola.idMatFAno", "escola.idGestLinguaEspecialidade", "escola.idGestDisEspecialidade", "escola.idMatAnexo", "escola.turnoAluno"], $grupo);

        $periodoAluno = valorArray($this->sobreAluno, "periodoAluno", "escola");
        $idMatAnexo = valorArray($this->sobreAluno, "idMatAnexo", "escola");

        $idGestLinguaEspecialidade = valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola");
        if($idGestLinguaEspecialidade==22){
           $idGestLinguaEspecialidade=20;
        }else if($idGestLinguaEspecialidade==23){
           $idGestLinguaEspecialidade=21;
        }

        $idGestDisEspecialidade = valorArray($this->sobreAluno, "idGestDisEspecialidade", "escola");

        $turmas=array();
        $designacoesTurma = array();
        $condicaoTurma = ["idPEscola"=>$_SESSION["idEscolaLogada"], "classe"=>$classe, "idListaAno"=>$this->idAnoActual, "periodoTurma"=>$periodoAluno, "idAnexoTurma"=>$idMatAnexo, "idPNomeCurso"=>$idPCurso];

        if(valorArray($this->sobreEscolaLogada, "criterioEscolhaTurno")=="opcional"){
            $condicaoTurma["periodoT"]=valorArray($this->sobreAluno, "turnoAluno", "escola");
        }
        $listaTurmasAvaliar=array();
        foreach ($this->selectArray("listaturmas", ["atributoTurma", "designacaoTurma", "nomeTurma"], $condicaoTurma) as $t) {

            $entrar="nao";
            if($t["atributoTurma"]==NULL || $t["atributoTurma"]==""){
                $entrar="sim";
            }else if(count(explode("-", $t["atributoTurma"]))==2){
                if(trim($t["atributoTurma"])==trim($idGestLinguaEspecialidade."-".$idGestDisEspecialidade)){
                    $entrar="sim";
                }
            }else{
                if($t["atributoTurma"]==$idGestLinguaEspecialidade || $t["atributoTurma"]==$idGestLinguaEspecialidade."-"

                || $t["atributoTurma"]==$idGestDisEspecialidade || $t["atributoTurma"]=="-".$idGestDisEspecialidade){
                    $entrar="sim";
                }
            }
            if($entrar=="sim"){
                $totAlunos = count($this->alunosPorTurma($idPCurso, $classe, $t["nomeTurma"], $this->idAnoActual, [], ["nomeAluno"]));

                $listaTurmasAvaliar[]=["nomeTurma"=>$t["nomeTurma"], "designacaoTurma"=>$t["designacaoTurma"], "totalAlunos"=>$totAlunos];
            }
        }

        $numeroInicial=0; $turmaAluno=""; $designacaoTurma="";
        $i=0;
        foreach($listaTurmasAvaliar as $turma){
            if($i==0){
                $numeroInicial = $turma["totalAlunos"];
                $turmaAluno = $turma["nomeTurma"];
                $designacaoTurma=$turma["designacaoTurma"];
            }else{
                if($turma["totalAlunos"]<$numeroInicial){
                    $numeroInicial = $turma["totalAlunos"];
                    $turmaAluno = $turma["nomeTurma"];
                    $designacaoTurma=$turma["designacaoTurma"];
                }
            }
            $i++;
        }

        $this->editarItemObjecto("alunos_".$grupo, "reconfirmacoes", "nomeTurma, designacaoTurma", [$turmaAluno, $designacaoTurma], ["idPMatricula"=>$idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION["idEscolaLogada"], "idMatCurso"=>$idPCurso]);

        $this->actuazalizarReconfirmacaAluno($idPMatricula);
    }

    public function actuazalizarReconfirmacaAluno($idPMatricula){
        $array = $this->selectArray("alunosmatriculados", ["idPMatricula", "grupo", "reconfirmacoes.idReconfAno", "reconfirmacoes.idMatCurso", "reconfirmacoes.idReconfEscola", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.estadoReconfirmacao", "reconfirmacoes.nomeTurma", "escolasReconfirmacao", "idCursosReconfirmacao", "classesReconfirmacao", "turmasReconfirmacao"], ["idPMatricula"=>$idPMatricula],[], 1);

        $reconfirmacoes = listarItensObjecto($array, "reconfirmacoes", ["idReconfEscola=".$_SESSION['idEscolaLogada'], "idReconfAno=".$this->idAnoActual, "estadoReconfirmacao=A"]);


        //Eliminando os dados anteriores da escola....
        $escolasReconfirmacao="";
        foreach(explode(",", valorArray($array, "escolasReconfirmacao")) as $escola){

            if(trim($escola)!=$_SESSION['idEscolaLogada']."_".$this->idAnoActual && trim($escola)!=""){
                $escolasReconfirmacao .=", ".trim($escola);
            }
        }
        $escolasReconfirmacao .=", ".$_SESSION["idEscolaLogada"]."_".valorArray($reconfirmacoes, "idReconfAno");


        $idCursosReconfirmacao="";
        foreach(explode(",", valorArray($array, "idCursosReconfirmacao")) as $curso){
            if(explode("=", trim($curso))[0]!=$_SESSION['idEscolaLogada']."_".$this->idAnoActual && trim($curso)!=""){
                $idCursosReconfirmacao .=", ".trim($curso);
            }
        }
        $idCursosReconfirmacao .=", ".$_SESSION['idEscolaLogada']."_".$this->idAnoActual."=".valorArray($reconfirmacoes, "idMatCurso");


        $classesReconfirmacao="";
        foreach(explode(",", valorArray($array, "classesReconfirmacao")) as $classe){
            if(explode("=", trim($classe))[0]!=$_SESSION['idEscolaLogada']."_".$this->idAnoActual && trim($classe)!=""){
                $classesReconfirmacao .=", ".trim($classe);
            }
        }
        $classesReconfirmacao .=", ".$_SESSION['idEscolaLogada']."_".$this->idAnoActual."=".valorArray($reconfirmacoes, "classeReconfirmacao");


        $turmasReconfirmacao="";
        foreach(explode(",", valorArray($array, "turmasReconfirmacao")) as $turma){
            if(explode("=", trim($turma))[0]!=$_SESSION['idEscolaLogada']."_".$this->idAnoActual && trim($turma)!=""){
                $turmasReconfirmacao .=", ".trim($turma);
            }
        }
        $turmasReconfirmacao .=", ".$_SESSION['idEscolaLogada']."_".$this->idAnoActual."=".valorArray($reconfirmacoes, "nomeTurma");

        $this->editar("alunos_".valorArray($array, "grupo"), "escolasReconfirmacao, idCursosReconfirmacao, classesReconfirmacao, turmasReconfirmacao", [$escolasReconfirmacao, $idCursosReconfirmacao, $classesReconfirmacao, $turmasReconfirmacao], ["idPMatricula"=>$idPMatricula]);

    }
    public function tratarArrayDeCursos($idPMatricula, $idCursoAdicionar="", $idCursoCursoNaoAdd=""){

        $array = $this->selectArray("alunosmatriculados", [], ["idPMatricula"=>$idPMatricula, "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"], 1);

        $idCursos = valorArray($array, "idCursos", "escola");
        $idCursos = (is_array($idCursos) || is_object($idCursos))?$idCursos:array();

        $novoIdCursos=array();
        $i=0;
        foreach($idCursos as $curso){
            if($curso["idMatCurso"]!=$idCursoAdicionar && $curso["idMatCurso"]!=$idCursoCursoNaoAdd){
                $novoIdCursos[]=$curso;
                $i++;
            }
        }
        $bala = isset($array[0]["escola"])?$array[0]["escola"]:array();
        foreach(retornarChaves($bala) as $chave){
            if($chave!="idCursos" && $chave!="idPAlEscola" && $chave!="idFMatricula" && $chave!="idMatEscola" && $chave!="update" && $chave!="backup_local_1" && $chave!="backup_local" && $chave!="timeUpdate" && $chave!="userUpdate" && isset($bala[$chave])){
                $novoIdCursos[$i][$chave] = $bala[$chave];
            }
        }
        $this->editarItemObjecto("alunos_".valorArray($array, "grupo"), "escola", "idCursos", [$novoIdCursos], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);
    }

    public function multa($tipoPreco, $classe, $idPCurso, $mes=NULL){
        return 0;
    }

    public function cotacaoSistema ($tipoPreco, $classe, $idPCurso, $mes=NULL){
        return 0;
    }



    public function verificarSePodeTratarDeclaracao($idPMatricula, $nomeDocumento, $classeAluno, $idPCurso, $grupo=""){

        return "permitido";
    }

    public function gravarPautasAluno($idPMatricula, $classeReconfirmacao="", $tabelasActualizar="todas", $disciplinas=array(), $sobreAluno=array(), $sobreCurso=array()){

        if(count($sobreAluno)<=0){
            $this->sobreAluno = $this->sobreAluno($idPMatricula, ["escola.periodoAluno", "escola.idMatCurso", "escola.classeActualAluno", "grupo", "escola.idGestLinguaEspecialidade", "escola.idGestDisEspecialidade"]);
        }else{
            $this->sobreAluno = $sobreAluno;
        }
        $periodo = valorArray($this->sobreAluno, "periodoAluno", "escola");
        $grupo = valorArray($this->sobreAluno, "grupo");
        $idPCurso = valorArray($this->sobreAluno, "idMatCurso", "escola");

        if($classeReconfirmacao==""){
            $classe = valorArray($this->sobreAluno, "classeActualAluno", "escola");
        }else{
            $classe = $classeReconfirmacao;
        }

        $idPAno="todas";
        if($tabelasActualizar=="todas"){
            $idPAno=$this->idAnoActual;
        }

        if(count($disciplinas)<=0){
            $disciplinas = $this->disciplinas($idPCurso, $classe, $periodo, "", array(), array(), ["idPNomeDisciplina", "disciplinas.semestreDisciplina"], $idPAno);
        }

        if(count($sobreCurso)<=0){
            $sobreCurso = $this->selectArray("nomecursos", ["cursos.modLinguaEstrangeira", "tipoCurso"], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
        }
        $modLinguaEstrangeira = valorArray($sobreCurso, "modLinguaEstrangeira", "cursos");
        $tipoCurso = valorArray($sobreCurso, "tipoCurso");

        foreach($disciplinas as $disciplina){

            $gravarDisciplina = $this->sePossoGravarDisciplina($idPCurso, $tipoCurso, $disciplina["idPNomeDisciplina"], $modLinguaEstrangeira, $classe);

            if($gravarDisciplina=="sim" && $idPCurso!="" && $idPCurso!=NULL){

                $chavePauta = $idPMatricula."-".$disciplina["idPNomeDisciplina"]."-".$classe."-".$idPCurso."-".$disciplina["disciplinas"]["semestreDisciplina"];
                $chavePautaArq = $idPMatricula."-".$disciplina["idPNomeDisciplina"]."-".$classe."-".$idPCurso."-".$_SESSION["idEscolaLogada"]."-".$this->idAnoActual."-".$disciplina["disciplinas"]["semestreDisciplina"];

                $this->inserirObjecto("alunos_".$grupo, "pautas", "idPPauta", "idPautaMatricula, idPautaDisciplina, idPautaEscola, obs, seFoiAoRecurso, chavePauta, idPautaAno, classePauta, idPautaCurso, semestrePauta", [$idPMatricula, $disciplina["idPNomeDisciplina"], $_SESSION["idEscolaLogada"], "NA", "I", $chavePauta, $this->idAnoActual, $classe, $idPCurso, $disciplina["disciplinas"]["semestreDisciplina"]], ["idPMatricula"=>$idPMatricula]);

                if($tabelasActualizar=="todas"){
                    $this->inserirObjecto("alunos_".$grupo, "arquivo_pautas", "idPPauta", "idPautaMatricula, idPautaDisciplina, idPautaEscola, obs, chavePauta, idPautaAno, classePauta, idPautaCurso, semestrePauta", [$idPMatricula, $disciplina["idPNomeDisciplina"], $_SESSION["idEscolaLogada"], "NA", $chavePautaArq, $this->idAnoActual, $classe, $idPCurso, $disciplina["disciplinas"]["semestreDisciplina"]], ["idPMatricula"=>$idPMatricula]);
                }
            }
        }
    }

    public function sePossoGravarDisciplina($idPCurso, $tipoCurso, $idPNomeDisciplina, $modLinguaEstrangeira, $classe){


        $gravarDisciplina="sim";
        if($idPCurso==3 && ($idPNomeDisciplina==20 || $idPNomeDisciplina==21 || $idPNomeDisciplina==22 || $idPNomeDisciplina==23)){

            if($modLinguaEstrangeira=="lingEsp"){
                if(valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola")==22 || valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola") == 20 /*Inglês(F.E)*/){
                    if($idPNomeDisciplina==23 /*Francês (F.E)*/ || $idPNomeDisciplina==20 /*Inglês(F.G)*/){
                        $gravarDisciplina="nao";
                    }
                }else if(valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola") ==23 || valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola") == 21 /*Francês(F.E)*/){
                    if($idPNomeDisciplina==22 /*Inglês (F.E)*/ || $idPNomeDisciplina==21 /*Francês(F.G)*/){
                        $gravarDisciplina="nao";
                    }
                }
            }else if($modLinguaEstrangeira=="lingEspUnica"){
                if(valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola")==22 || valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola") == 20 /*Inglês(F.E)*/){
                    if($idPNomeDisciplina==23 /*Francês (F.E)*/ || $idPNomeDisciplina==21 /*Francês(F.G)*/){
                        $gravarDisciplina="nao";
                    }
                }else if(valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola")==23 /*Francês(F.E)*/ || valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola") == 21){
                    if($idPNomeDisciplina==22 /*Inglês (F.E)*/ || $idPNomeDisciplina==20 /*Inglês(F.G)*/){
                        $gravarDisciplina="nao";
                    }
                }
            }

        }else if($idPNomeDisciplina==20 || $idPNomeDisciplina==21){
            if($modLinguaEstrangeira=="opcional"){
                if(valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola")!=$idPNomeDisciplina){
                    $gravarDisciplina="nao";
                }
            }
        }else if($tipoCurso=="geral" && ($idPNomeDisciplina==17 || $idPNomeDisciplina==14 || $idPNomeDisciplina==9 || $idPNomeDisciplina==122)){

            //Negar as disciplinas que não são da especialidade do aluno...
            if(valorArray($this->sobreAluno, "idGestDisEspecialidade", "escola")!=$idPNomeDisciplina){
                $gravarDisciplina="nao";
            }

            //Excepto disciplina de GD no curso de Artes Visuais
            if($idPCurso==30 && $idPNomeDisciplina==9){
                $gravarDisciplina="sim";
            }
        }
        return $gravarDisciplina;
    }

    public function seleccionadorEspecialidades($idPCurso, $classe, $idGestLinguaEspecialidadeNovo, $idGestDisEspecialidadeNovo, $idPMatricula, $grupo){

        $idLE1="";
        $idLE1Contraria="";

        $array = $this->selectArray("alunos_".$grupo, ["idPMatricula"], ["idPMatricula"=>$idPMatricula, "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);

        $idGestMatricula = valorArray($array, "idPMatricula");

        if($idPCurso==3 && (($idGestLinguaEspecialidadeNovo==20 || $idGestLinguaEspecialidadeNovo==21) || ($idGestLinguaEspecialidadeNovo==22 || $idGestLinguaEspecialidadeNovo==23))){

            //Para o curso de Ciências Humanas
            $moledji = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
            $modLinguaEstrangeira = valorArray($moledji, "modLinguaEstrangeira", "cursos");

            $idLE2="";
            $idLE2Contraria="";

            if($modLinguaEstrangeira=="lingEspUnica"){

                if($idGestLinguaEspecialidadeNovo==20 || $idGestLinguaEspecialidadeNovo==22 /*Inglês*/){

                    $idLE1=22; //Inglês (F.E)
                    $idLE2=20; //Inglês (F.G)

                    $idLE1Contraria=23; //Francês(F.E)
                    $idLE2Contraria = 21; //Francês(F.G)

                }else if($idGestLinguaEspecialidadeNovo==21 || $idGestLinguaEspecialidadeNovo==23 /*Francês*/){
                    $idLE1=23; //Francês (F.E)
                    $idLE2=21; //Francês (F.G)

                    $idLE1Contraria=22; //Inglês (F.E)
                    $idLE2Contraria = 20; //Inglês (F.G)
                }
            }else if($modLinguaEstrangeira=="lingEsp"){

                if($idGestLinguaEspecialidadeNovo==20 || $idGestLinguaEspecialidadeNovo==22 /*Inglês*/){

                    $idLE1=22; // Inglês (F.E)
                    $idLE2=21; // Francês (F.G)

                    $idLE1Contraria=23; //Francês (F.E)
                    $idLE2Contraria = 20; //Inglês
                }else if($idGestLinguaEspecialidadeNovo==21 || $idGestLinguaEspecialidadeNovo==23 /*Francês*/){

                    $idLE1=23; // Francês (F.E)
                    $idLE2=20; // Inglês (F.G)

                    $idLE1Contraria=22; //Inglês (F.E)
                    $idLE2Contraria = 21; //Francês (F.G)
                }
            }

            foreach ($this->selectArray("alunos_".$grupo, ["pautas.classePauta", "pautas.idPautaCurso", "pautas.semestrePauta", "pautas.idPPauta"], ["idPMatricula"=>$idGestMatricula, "pautas.idPautaDisciplina"=>$idLE2Contraria, "pautas.idPautaCurso"=>$idPCurso], ["pautas"]) as $pauta) {

                $chavePauta = $idGestMatricula."-".$idLE2."-".$pauta["pautas"]["classePauta"]."-".$pauta["pautas"]["idPautaCurso"]."-".$pauta["pautas"]["semestrePauta"];
                $this->editarItemObjecto("alunos_".$grupo, "pautas", "idPautaDisciplina, chavePauta", [$idLE2, $chavePauta], ["idPMatricula"=>$idGestMatricula], ["idPPauta"=>$pauta["pautas"]["idPPauta"]]);
            }

            foreach ($this->selectArray("alunos_".$grupo, ["arquivo_pautas.classePauta", "arquivo_pautas.idPautaAno", "arquivo_pautas.idPautaEscola", "arquivo_pautas.idPautaAno", "arquivo_pautas.idPautaEscola", "arquivo_pautas.idPPauta"], ["idPMatricula"=>$idGestMatricula, "arquivo_pautas.idPautaDisciplina"=>$idLE2Contraria, "arquivo_pautas.idPautaCurso="=>$idPCurso, "arquivo_pautas.idPautaEscola"=>$_SESSION['idEscolaLogada']], ["arquivo_pautas"]) as $pauta) {

                $chavePautaArq = $idGestMatricula."-".$idLE2."-".$pauta["arquivo_pautas"]["classePauta"]."-".$pauta["arquivo_pautas"]["idPautaCurso"]."-".$pauta["arquivo_pautas"]["idPautaEscola"]."-".$pauta["arquivo_pautas"]["idPautaAno"]."-".$pauta["arquivo_pautas"]["semestrePauta"];

                $this->editarItemObjecto("alunos_".$grupo, "arquivo_pautas", "idPautaDisciplina, chavePauta", [$idLE2, $chavePauta], ["idPMatricula"=>$idGestMatricula], ["idPPauta"=>$pauta["arquivo_pautas"]["idPPauta"]]);
            }

            foreach ($this->selectArray("alunos_".$grupo, ["cadeiras_atraso.idPCadeirantes"], ["idPMatricula"=>$idGestMatricula, "cadeiras_atraso.idCadDisciplina"=>$idLE2Contraria], ["cadeiras_atraso"]) as $cad) {

                $this->editarItemObjecto("alunos_".$grupo, "cadeiras_atraso", "idCadDisciplina", [$idLE2], ["idPMatricula"=>$idGestMatricula], ["idPCadeirantes"=>$cad["idPCadeirantes"]]);
            }

        }else{
            if($idGestLinguaEspecialidadeNovo==20){
                $idLE1=20;
                $idLE1Contraria=21;
            }else if($idGestLinguaEspecialidadeNovo==21){
                $idLE1=21;
                $idLE1Contraria=20;
            }
        }
        foreach ($this->selectArray("alunos_".$grupo, ["pautas.classePauta", "pautas.semestrePauta", "pautas.idPautaCurso", "pautas.idPPauta"], ["idPMatricula"=>$idGestMatricula, "pautas.idPautaDisciplina"=>$idLE1Contraria, "pautas.idPautaCurso"=>$idPCurso], ["pautas"]) as $pauta) {

            $chavePauta = $idGestMatricula."-".$idLE1."-".$pauta["pautas"]["classePauta"]."-".$pauta["pautas"]["idPautaCurso"]."-".$pauta["pautas"]["semestrePauta"];
            $this->editarItemObjecto("alunos_".$grupo, "pautas", "idPautaDisciplina, chavePauta", [$idLE1, $chavePauta], ["idPMatricula"=>$idGestMatricula], ["idPPauta"=>$pauta["pautas"]["idPPauta"]]);
        }

        foreach ($this->selectArray("alunos_".$grupo, ["arquivo_pautas.idPautaAno", "arquivo_pautas.classePauta", "arquivo_pautas.idPautaAno", "arquivo_pautas.semestrePauta", "arquivo_pautas.idPPauta"], ["idPMatricula"=>$idGestMatricula, "arquivo_pautas.idPautaDisciplina"=>$idLE1Contraria, "arquivo_pautas.idPautaEscola"=>$_SESSION['idEscolaLogada'], "arquivo_pautas.idPautaCurso"=>$idPCurso], ["arquivo_pautas"]) as $pauta) {

            $chavePautaArq = $idGestMatricula."-".$idLE1."-".$pauta["arquivo_pautas"]["classePauta"]."-".$pauta["arquivo_pautas"]["idPautaCurso"]."-".$pauta["arquivo_pautas"]["idPautaEscola"]."-".$pauta["arquivo_pautas"]["idPautaAno"]."-".$pauta["arquivo_pautas"]["semestrePauta"];

            $this->editarItemObjecto("alunos_".$grupo, "arquivo_pautas", "idPautaDisciplina, chavePauta", [$idLE1, $chavePautaArq], ["idPMatricula"=>$idGestMatricula], ["idPPauta"=>$pauta["arquivo_pautas"]["idPPauta"]]);
        }


        foreach ($this->selectArray("alunos_".$grupo, ["cadeiras_atraso.idPCadeirantes"], ["idPMatricula"=>$idGestMatricula, "cadeiras_atraso.idCadDisciplina"=>$idLE1Contraria], ["cadeiras_atraso"]) as $cad) {

            $this->editarItemObjecto("alunos_".$grupo, "cadeiras_atraso", "idCadDisciplina", [$idLE1], ["idPMatricula"=>$idGestMatricula], ["idPCadeirantes"=>$cad["cadeiras_atraso"]["idPCadeirantes"]]);
        }

        if($idGestDisEspecialidadeNovo!="" && $idGestDisEspecialidadeNovo!=NULL && $this->selectUmElemento("nomecursos", "tipoCurso", ["idPNomeCurso"=>$idPCurso])=="geral"){

            foreach ($this->selectArray("alunos_".$grupo, ["pautas.idPautaDisciplina", "pautas.semestrePauta", "pautas.semestrePauta", "pautas.idPautaCurso", "pautas.classePauta", "pautas.idPPauta"], ["idPMatricula"=>$idGestMatricula, "pautas.idPautaCurso"=>$idPCurso, "pautas.idPautaDisciplina"=>['$in'=>array(14,122,17,9)]], ["pautas"]) as $pauta) {
                if($idPCurso!=30 || ($idPCurso==30 && $nota["pautas"]["idPautaDisciplina"]!=9)){

                    $chavePauta = $idGestMatricula."-".$idGestDisEspecialidadeNovo."-".$pauta["pautas"]["classePauta"]."-".$pauta["pautas"]["idPautaCurso"]."-".$pauta["pautas"]["semestrePauta"];
                    $this->editarItemObjecto("alunos_".$grupo, "pautas", "idPautaDisciplina, chavePauta", [$idGestDisEspecialidadeNovo, $chavePauta], ["idPMatricula"=>$idGestMatricula], ["idPPauta"=>$pauta["pautas"]["idPPauta"]]);
                }
            }

            foreach ($this->selectArray("alunos_".$grupo, ["arquivo_pautas.idPautaDisciplina", "arquivo_pautas.idPautaAno", "arquivo_pautas.classePauta", "arquivo_pautas.idPautaCurso", "arquivo_pautas.semestrePauta", "arquivo_pautas.idPPauta"], ["idPMatricula"=>$idGestMatricula, "arquivo_pautas.idPautaCurso"=>$idPCurso, "arquivo_pautas.idPautaEscola"=>$_SESSION["idEscolaLogada"], "arquivo_pautas.idPautaDisciplina"=>['$in'=>array(14,122,17,9)]], ["arquivo_pautas"]) as $pauta) {

                if($idPCurso!=30 || ($idPCurso==30 && $nota["arquivo_pautas"]["idPautaDisciplina"]!=9)){

                    $chavePautaArq = $idGestMatricula."-".$idGestDisEspecialidadeNovo."-".$pauta["arquivo_pautas"]["classePauta"]."-".$pauta["arquivo_pautas"]["idPautaCurso"]."-".$pauta["arquivo_pautas"]["idPautaEscola"]."-".$pauta["arquivo_pautas"]["idPautaAno"]."-".$pauta["arquivo_pautas"]["semestrePauta"];
                    $this->editarItemObjecto("alunos_".$grupo, "arquivo_pautas", "idPautaDisciplina, chavePauta", [$idGestDisEspecialidadeNovo, $chavePautaArq], ["idPMatricula"=>$idGestMatricula], ["idPPauta"=>$pauta["arquivo_pautas"]["idPPauta"]]);
                }
            }


            foreach ($this->selectArray("alunos_".$grupo, ["cadeiras_atraso.idCadDisciplina", "cadeiras_atraso.idPCadeirantes"], ["idPMatricula"=>$idGestMatricula, "cadeiras_atraso.idCadDisciplina"=>$idLE1Contraria, "cadeiras_atraso.idCadCurso"=>$idPCurso, "cadeiras_atraso.idCadDisciplina"=>['$in'=>array(14,122,17,9)]], ["cadeiras_atraso"]) as $cad){

                if($idPCurso!=30 || ($idPCurso==30 && $nota["cadeiras_atraso"]["idCadDisciplina"]!=9)){

                    $this->editarItemObjecto("alunos_".$grupo, "cadeiras_atraso", "idCadDisciplina", [$cad["cadeiras_atraso"]["idCadDisciplina"]], ["idPMatricula"=>$idGestMatricula], ["idPCadeirantes"=>$cad["cadeiras_atraso"]["idPCadeirantes"]]);
                }
            }

        }
         $this->editarItemObjecto("alunos_".$grupo, "escola", "idGestDisEspecialidade, idGestLinguaEspecialidade", [$idGestDisEspecialidadeNovo, $idLE1], ["idPMatricula"=>$idPMatricula],["idMatEscola"=>$_SESSION["idEscolaLogada"]]);
    }

    public function editarDadosAluno($idPMatricula, $caminhoRetornar, $msgSim="nao"){

        $this->sobreAluno = $this->selectArray("alunosmatriculados", [], ["idPMatricula"=>$idPMatricula, "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);

        $grupo = valorArray($this->sobreAluno, "grupo");
        $nomeAluno = isset($_POST["nomeAluno"])?limpadorEspacosDuplicados($_POST['nomeAluno']):valorArray($this->sobreAluno, "nomeAluno");
        $sexoAluno = isset($_POST["sexoAluno"])?$_POST['sexoAluno']:valorArray($this->sobreAluno, "sexoAluno");

        $seBolseiro = isset($_POST["seBolseiro"])?"V":"F";
        $beneficiosDaBolsa = isset($_POST["beneficiosDaBolsa"])?$_POST["beneficiosDaBolsa"]:array();
        if($seBolseiro=="F"){
            $beneficiosDaBolsa=array();
        }else{
            $beneficiosDaBolsa = json_decode($beneficiosDaBolsa);
        }

        $art1="o";
        $art2="e";
        $art3="";
        if($sexoAluno=="F"){
            $art1 = $art2 = $art3="a";
        }

        $dataNascAluno = isset($_POST["dataNascAluno"])?$_POST['dataNascAluno']:valorArray($this->sobreAluno, "dataNascAluno");

        $turnoAluno = isset($_POST["turnoAluno"])?$_POST['turnoAluno']:valorArray($this->sobreAluno, "turnoAluno");

        $municipio =isset($_POST["municipio"])?$_POST['municipio']:valorArray($this->sobreAluno, "municNascAluno");

        $comuna =isset($_POST["comuna"])?$_POST['comuna']:valorArray($this->sobreAluno, "comunaNascAluno");

        $provincia =isset($_POST["provincia"])?$_POST['provincia']:valorArray($this->sobreAluno, "provNascAluno");
        $numBI =isset($_POST["numBI"])?$_POST['numBI']:valorArray($this->sobreAluno, "biAluno");
        $dataEmissaoBI =isset($_POST["dataEmissaoBI"])?$_POST['dataEmissaoBI']:valorArray($this->sobreAluno, "dataEBIAluno");
        $nomePai =isset($_POST["nomePai"])?limpadorEspacosDuplicados($_POST['nomePai']):valorArray($this->sobreAluno, "paiAluno");
        $nomeMae =isset($_POST["nomeMae"])?limpadorEspacosDuplicados($_POST['nomeMae']):valorArray($this->sobreAluno, "maeAluno");
        $nomeEncarregado =isset($_POST["nomeEncarregado"])?limpadorEspacosDuplicados($_POST['nomeEncarregado']):valorArray($this->sobreAluno, "encarregadoEducacao");
        $numTelefone =isset($_POST["numTelefone"])?$_POST['numTelefone']:valorArray($this->sobreAluno, "telefoneAluno"); trim(filter_input(INPUT_POST, "numTelefone", FILTER_SANITIZE_NUMBER_INT));
        $classeAluno =isset($_POST["classeAluno"])?$_POST['classeAluno']:valorArray($this->sobreAluno, "classeActualAluno", "escola");
        $pais =isset($_POST["pais"])?$_POST['pais']:valorArray($this->sobreAluno, "paisNascAluno");
        $deficiencia =isset($_POST["deficiencia"])?$_POST['deficiencia']:valorArray($this->sobreAluno, "deficienciaAluno");

        $tipoDeficiencia =isset($_POST["tipoDeficiencia"])?$_POST['tipoDeficiencia']:valorArray($this->sobreAluno, "tipoDeficienciaAluno");
        $emailAluno =isset($_POST["emailAluno"])?$_POST['emailAluno']:valorArray($this->sobreAluno, "emailAluno");
        $acessoConta =isset($_POST["acessoConta"])?$_POST['acessoConta']:valorArray($this->sobreAluno, "estadoAcessoAluno");
        $periodoAluno =isset($_POST["periodoAluno"])?$_POST['periodoAluno']:valorArray($this->sobreAluno, "periodoAluno", "escola");

        $tipoDocumento =isset($_POST["tipoDocumento"])?$_POST['tipoDocumento']:valorArray($this->sobreAluno, "tipoDocumento");
        $localEmissao =isset($_POST["localEmissao"])?$_POST['localEmissao']:valorArray($this->sobreAluno, "localEmissao");

        $numeroProcesso = isset($_POST["numeroProcesso"])?$_POST['numeroProcesso']:valorArray($this->sobreAluno, "numeroProcesso", "escola");
        $idMatAnexo =isset($_POST["idMatAnexo"])?$_POST['idMatAnexo']:valorArray($this->sobreAluno, "idMatAnexo", "escola");
        $idPCurso =isset($_POST["idPCurso"])?$_POST['idPCurso']:valorArray($this->sobreAluno, "idMatCurso", "escola");

        $idGestLinguaEspecialidadeNovo = isset($_POST["lingEspecialidade"])?$_POST["lingEspecialidade"]:valorArray($this->sobreAluno, "idGestLinguaEspecialidade", "escola");
        $idGestDisEspecialidadeNovo = isset($_POST["discEspecialidade"])?$_POST["discEspecialidade"]:valorArray($this->sobreAluno, "idGestDisEspecialidade", "escola");
        $dataCaducidadeBI = isset($_POST["dataCaducidadeBI"])?$_POST["dataCaducidadeBI"]:valorArray($this->sobreAluno, "dataCaducidadeBI");
        $estadoDeDesistenciaNaEscola = isset($_POST["estadoDeDesistenciaNaEscola"])?$_POST["estadoDeDesistenciaNaEscola"]:valorArray($this->sobreAluno, "estadoDeDesistenciaNaEscola", "escola");

        $reconfirmacao = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno=".$this->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada'], "idMatCurso=".$idPCurso]);

        $tipoEntrada = isset($_POST["tipoEntrada"])?$_POST["tipoEntrada"]:valorArray($reconfirmacao, "tipoEntrada");
        $this->sobreCursoAluno= $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$idPCurso]);

        $idMatFAno=NULL;
        $classeVerificarAcesso=$classeAluno;
        $expl = explode("_", $classeAluno);

        if(count($expl)>=2){
            $classeVerificarAcesso=valorArray($this->sobreCursoAluno, "ultimaClasse");;
            $classeAluno=120;
            $idMatFAno=$expl[1];
        }

        $retorno="";
        if($numeroProcesso==NULL || $numeroProcesso==""){
            $expl = explode("ANGOS", valorArray($this->sobreAluno, "numeroInterno"));
            $numeroProcesso = $expl[0].(isset($expl[1])?$expl[1]:"");
        }
        $arrayAlunoNoSistema=array();
        if($numBI!=""){
            $arrayAlunoNoSistema = $this->selectArray("alunosmatriculados", [], ["biAluno"=>$numBI, "idPMatricula"=>['$ne'=>(int)$idPMatricula]], ["escola"], 1);
        }

        $gerenciador_matriculas = listarItensObjecto($this->sobreEscolaLogada, "gerencMatricula", ["classe=".$classeVerificarAcesso, "periodoClasse=".$periodoAluno, "idCurso=".$idPCurso]);

        if(count($arrayAlunoNoSistema)>0){

            echo "FJá há um".$art3." alun".$art1." cadastrad".$art1." n".$this->art1Escola." <i>".$this->selectUmElemento("escolas", "nomeEscola", ["idPEscola"=>valorArray($arrayAlunoNoSistema, "idMatEscola", "escola")])."</i> com estes dados.";
            if(valorArray($arrayAlunoNoSistema, "idMatEscola", "escola")!=$_SESSION['idEscolaLogada']){
                echo "Vai no menu Matriculas (Adicionar Aluno) para poder adicioná-l".$art1." na instituição.";
            }
        }else if(seTudoMaiuscula($nomeAluno) || seTudoMaiuscula($nomeMae) || seTudoMaiuscula($nomePai) || seTudoMaiuscula($nomeEncarregado)){
            $retorno= "FOs dados não podem ser todos em letras maiúsculas. Digite bem os dados.";
        }else if($idPCurso==NULL){
                $retorno="FDeves seleccionar o curso(opção) d".$art1." alun".$art1.".";

        }else if(valorArray($this->sobreCursoAluno, "tipoCurso")=="geral" && ($idGestDisEspecialidadeNovo==NULL || $idGestDisEspecialidadeNovo=="")){
            $retorno="FDeves seleccionar a disciplina de opção d".$art1." alun".$art1.".";
        }else if(!seTemValorNoArray(explode(",", valorArray($gerenciador_matriculas, "idsLinguasEtrang")), $idGestLinguaEspecialidadeNovo) && $classeVerificarAcesso>=7){
            $retorno = "FSelecciones outra língua de opção.";

        }else if(!seTemValorNoArray(explode(",", valorArray($gerenciador_matriculas, "idsDisciplOpcao")), $idGestDisEspecialidadeNovo) && valorArray($this->sobreCursoAluno, "tipoCurso")=="geral"){
            $retorno="FSelecciones outra disciplina de opção.";
        }else if(($idGestLinguaEspecialidadeNovo==NULL || $idGestLinguaEspecialidadeNovo=="") && $classeVerificarAcesso>=7){
            $retorno="FDeves seleccionar a língua de opção d".$art1." alun".$art1.".";
        }else if($this->seEPossivelEditarClasseCurso($classeAluno, $idPCurso, $grupo)=="sim"){

            $fotoAluno = valorArray($this->sobreAluno, "fotoAluno");
            if(isset($_FILES['fotoAluno']) && $_FILES['fotoAluno']['size'] > 0){
                $fotoAluno = $this->upload("fotoAluno", valorArray($this->sobreAluno, "numeroInterno").$this->segundos, 'fotoUsuarios', $caminhoRetornar, $fotoAluno);
            }
            if($this->editar("alunos_".$grupo, "nomeAluno, sexoAluno, dataNascAluno, provNascAluno, municNascAluno, comunaNascAluno, paiAluno, maeAluno, biAluno, paisNascAluno, dataEBIAluno, telefoneAluno, encarregadoEducacao, emailAluno, estadoAcessoAluno, deficienciaAluno, tipoDeficienciaAluno, fotoAluno, dataCaducidadeBI, tipoDocumento, localEmissao", [$nomeAluno, $sexoAluno, $dataNascAluno, $provincia, $municipio, $comuna, $nomePai, $nomeMae, $numBI, $pais, $dataEmissaoBI, $numTelefone, $nomeEncarregado, $emailAluno, $acessoConta, $deficiencia, $tipoDeficiencia, $fotoAluno, $dataCaducidadeBI, $tipoDocumento, $localEmissao], ["idPMatricula"=>$idPMatricula])=="sim"){

                $this->editarItemObjecto("alunos_".$grupo, "reconfirmacoes", "tipoEntrada", [$tipoEntrada], ["idPMatricula"=>$idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>$idPCurso]);

                $this->editarItemObjecto("alunos_".$grupo, "escola", "numeroProcesso, idMatAnexo, periodoAluno, estadoDeDesistenciaNaEscola, turnoAluno, seBolseiro, beneficiosDaBolsa", [$numeroProcesso, $idMatAnexo, $periodoAluno, $estadoDeDesistenciaNaEscola, $turnoAluno, $seBolseiro, $beneficiosDaBolsa], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$_SESSION["idEscolaLogada"]]);

                $idGestDisEspecialidade = valorArray($this->sobreAluno, "idGestDisEspecialidade", "escola");
                $idGestLinguaEspecialidade = valorArray($this->sobreAluno, "     idGestLinguaEspecialidade", "escola");

                if($idPCurso==3){
                    if($idGestLinguaEspecialidade==20){
                        $idGestLinguaEspecialidade=22;
                    }
                    if($idGestLinguaEspecialidade==21){
                        $idGestLinguaEspecialidade=23;
                    }
                }

                //Para o curso de Ciências Humanas
                $moledji = $this->selectArray("nomecursos", ["cursos.modLinguaEstrangeira", "tipoCurso"], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
                $modLE = valorArray($moledji, "modLinguaEstrangeira", "cursos");


                if(($idGestDisEspecialidade!=$idGestDisEspecialidadeNovo && valorArray($moledji, "tipoCurso")=="geral") || ($idGestLinguaEspecialidade!=$idGestLinguaEspecialidadeNovo  && ($modLE=="opcional" || $modLE=="lingEsp") )){

                    $this->seleccionadorEspecialidades($idPCurso, $classeAluno, $idGestLinguaEspecialidadeNovo, $idGestDisEspecialidadeNovo, $idPMatricula, $grupo);
                }
                if(valorArray($this->sobreAluno, "classeActualAluno", "escola")!=$classeAluno || valorArray($this->sobreAluno, "idMatCurso", "escola")!=$idPCurso || valorArray($this->sobreAluno, "periodoAluno", "escola")!=$periodoAluno){
                    $this->gravarPautasAluno($idPMatricula);
                }


                if((valorArray($this->sobreAluno, "classeActualAluno", "escola")!=$classeAluno || valorArray($this->sobreAluno, "idMatCurso", "escola")!=$idPCurso || valorArray($this->sobreAluno, "periodoAluno", "escola")!=$periodoAluno || valorArray($this->sobreAluno, "idMatAnexo", "escola")!=$idMatAnexo || (valorArray($this->sobreAluno, "turnoAluno", "escola")!=$turnoAluno && $turnoAluno!="Automático") || $this->sePodeTrocarTurmaPorTrocarDiscLingOpcao($idPMatricula, $classeAluno, $idPCurso, $idGestDisEspecialidade, $idGestDisEspecialidadeNovo, $idGestLinguaEspecialidade, $idGestLinguaEspecialidadeNovo)) && count($reconfirmacao)>0 && (!isset($_POST['seCursoProvisorio']) || (isset($_POST['seCursoProvisorio']) && $_POST['seCursoProvisorio']=="nao"))){

                    $this->atualizarTurma($idPMatricula, $classeAluno, $idPCurso, "", $grupo);
                }
                $this->tratarArrayDeCursos($idPMatricula, $idPCurso);
                if($msgSim=="sim"){
                    $retorno="sim";
                }else{
                    $retorno = "VOs dados d".$art1." alun".$art1." foram editados com sucesso.";
                }
            }else{
                $retorno = "FNão foi possível editar os dados d".$art1." alun".$art1.".";
            }
        }
        return $retorno;
    }

    private function sePodeTrocarTurmaPorTrocarDiscLingOpcao($idPMatricula, $classeAluno, $idPCurso, $idGestDisEspecialidade, $idGestDisEspecialidadeNovo, $idGestLinguaEspecialidade, $idGestLinguaEspecialidadeNovo){

        $retorno=false;
        $this->sobreAluno($idPMatricula);

        $turmaAnterior = valorArray(listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno=".$this->idAnoActual, "idMatCurso=".$idPCurso, "idReconfEscola=".$_SESSION['idEscolaLogada']]), "nomeTurma");

        $sobreTurma = $this->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "nomeTurma"=>$turmaAnterior, "idListaAno"=>$this->idAnoActual, "classe"=>$classeAluno, "idPNomeCurso"=>$idPCurso]);

        $atributoTurma = explode("-", valorArray($sobreTurma, "atributoTurma"));
        $atributoTurma1 = $atributoTurma[0];
        $atributoTurma2 = isset($atributoTurma[1])?$atributoTurma[1]:"";

        if(($idGestLinguaEspecialidade!=$idGestLinguaEspecialidadeNovo && ($atributoTurma1==20 || $atributoTurma1==21 || $atributoTurma2==20 || $atributoTurma2==21)) || ($idGestDisEspecialidade!=$idGestDisEspecialidadeNovo && ($atributoTurma1==122 || $atributoTurma1==14 || $atributoTurma1==17 || $atributoTurma1==9 || $atributoTurma2==122 || $atributoTurma2==14 || $atributoTurma2==17 || $atributoTurma2==9))){
            $retorno=true;
        }
        return $retorno;
    }
}

?>

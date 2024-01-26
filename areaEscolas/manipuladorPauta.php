<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
        session_cache_expire(60);
      session_start();
    }
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php');
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php');

     class manipuladorPauta extends manipulacaoDadosAjax{

        public $estadoConselhoTurma ="F";

        function __construct(){
            parent::__construct();
            $this->mediaFinalDisciplinaAntesAlteracao="not";
            $this->mediaFinalDisciplinaAposAlteracao="not";
        }

        public function alterPautaMod_2020($classe, $idPMatricula, $idPDisciplina, $idPProfessor, $trimestre, $turma, $semestrePauta, $sobreDiv, $avaliacoesQuantitativas=array(), $avaliacoesQualitativas=array(), $avalContinua=""){

            $nomeAluno="";
            $msgRetorno="";

            $camposCelestina = ['idPMatricula', 'nomeAluno', 'numeroInterno', 'fotoAluno', 'pautas.idPPauta','pautas.idPautaMatricula','pautas.idPautaDisciplina','pautas.recurso','pautas.obs','pautas.seFoiAoRecurso','pautas.classePauta','pautas.semestrePauta','pautas.idPautaCurso','pautas.chavePauta','pautas.idPautaAno','pautas.idPautaEscola', 'escola.idMatCurso', 'escola.classeActualAluno', 'reconfirmacoes.idReconfAno', 'reconfirmacoes.estadoDesistencia', 'reconfirmacoes.idReconfEscola', 'arquivo_pautas.idPPauta', 'escola.periodoAluno', "grupo", "arquivo_pautas.idPautaDisciplina", "arquivo_pautas.idPautaAno", "arquivo_pautas.idPautaEscola", "arquivo_pautas.classePauta", "arquivo_pautas.semestrePauta", "arquivo_pautas.idPautaCurso"];
            $camposCelestina = array_merge($camposCelestina, $this->camposPautas);

            $this->sobreAluno($idPMatricula, $camposCelestina);
            foreach (listarItensObjecto($this->sobreAluno, "pautas", ["classePauta=".$classe, "idPautaDisciplina=".$idPDisciplina, "semestrePauta=".$semestrePauta, "idPautaCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]) as $nota) {

                $nomeAluno=valorArray($this->sobreAluno, "nomeAluno");
                $idPautaMatricula = $nota["idPautaMatricula"];
                $idPautaAno = $nota["idPautaAno"];

                $jipe=0;
                foreach($this->camposAvaliacao as $campo){
                    $this->camposAvaliacao[$jipe]["valor"]=isset($nota[$campo["identUnicaDb"]])?$nota[$campo["identUnicaDb"]]:null;
                    $jipe++;
                }
                $obsPauta = isset($nota["obs"])?$nota["obs"]:null;

                $sobreDisciplina = array_filter($this->curriculoDaClasse, function ($mamale) use ($nota, $semestrePauta){
                    return $mamale["disciplinas"]["classeDisciplina"]==$nota["classePauta"] && $mamale["disciplinas"]["semestreDisciplina"]==$semestrePauta && $mamale["idPNomeDisciplina"]==$nota["idPautaDisciplina"];
                });

                if(count($sobreDisciplina)>0){
                    foreach($sobreDisciplina as $disc){
                        $continuidadeDisciplina = $disc["disciplinas"]["continuidadeDisciplina"];
                    }
                }else{
                    return "Não foi possível alterar as notas por que a disciplina não consta no curriculo.";
                    exit();
                }

                if($this->estadoConselhoTurma=="V"){
                    if(valorArray($this->sobreCursoAluno, "tipoCurso")=="tecnico" && $continuidadeDisciplina=="T"){

                        if(valorArray($this->sobreCursoAluno, "campoAvaliar", "cursos")=="cfd"){
                            $this->mediaFinalDisciplinaAntesAlteracao =isset($nota["cf"])?$nota["cf"]:0;
                        }else{
                            $this->mediaFinalDisciplinaAntesAlteracao =isset($nota["mf"])?$nota["mf"]:0;
                        }
                    }else{
                        $this->mediaFinalDisciplinaAntesAlteracao =isset($nota["mf"])?$nota["mf"]:0;
                    }
                }
            }
            $camposAvaliacaoInicial = $this->camposAvaliacao;

            $valorMAC=0;
            foreach($avaliacoesQuantitativas as $av){
                $jipe=0;
                foreach($this->camposAvaliacao as $campo){
                    $nelson = isset($av->idCampoAvaliacao)?$av->idCampoAvaliacao:$av["idCampoAvaliacao"];

                    if($nelson==$campo["idCampoAvaliacao"]){
                        $this->camposAvaliacao[$jipe]["valor"]=isset($av->valor)?$av->valor:$av["valor"];
                    }
                    $jipe++;
                }
                $nelson = isset($av->name)?$av->name:$av["name"];
                if($nelson=="macI" || $nelson=="macII" || $nelson=="macIII"){
                    $valorMAC = isset($av->valor)?$av->valor:$av["valor"];
                }
            }

            $this->camposAvaliacao = array_values($this->camposAvaliacao);

            foreach($this->trimestres as $trim){
                $this->calcularMtMod2020($trim["identificador"]);
            }
            $this->calcularMfdMod20();
            $this->calcularMediaFinalMod2020(valorArray($this->sobreCursoAluno, "sePorSemestre"));

            $valoresEnviar = array();
            $nomeValoresEnviar="";

            foreach($this->camposAvaliacao as $a){
                if($nomeValoresEnviar!=""){
                    $nomeValoresEnviar .=",";
                }
                $nomeValoresEnviar .=$a["identUnicaDb"];
                $valoresEnviar[]=$a["valor"];
            }

            if($trimestre!="IV" && $trimestre!="conselho" && $trimestre!="transicao"){
                if($avalContinua!=""){
                    $nomeValoresEnviar .=", avaliacoesContinuas".$trimestre;
                    $valoresEnviar[] =$avalContinua;
                }
            }

            foreach($avaliacoesQualitativas as $a){
                $nomeValoresEnviar .=", ".$a->name.$trimestre;
                $valoresEnviar[] = isset($a->valor)?$a->valor:$a["valor"];
            }

            $fatima = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno=".$this->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada'], "idMatCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]);

            $estadoAluno = valorArray($fatima, "estadoDesistencia");
            if($obsPauta=="melh" && valorArray($this->sobreCursoAluno, "tipoCurso") == "geral"){
                $msgRetorno ="As notas do(a) <strong><i>".$nomeAluno."</i></strong> não podem ser alteradas, porque já eliminou a cadeira.";
            }else if($estadoAluno=="D"){
                $msgRetorno ="Não foi possível alterar as notas do(a) <strong><i>".$nomeAluno."</i></strong> porque já desistiu.";
            }else if($estadoAluno=="N"){
                $msgRetorno ="Não foi possível alterar as notas do(a) <strong><i>".$nomeAluno."</i></strong> porque já anulou a matricula.";
            }else if($estadoAluno=="F"){
                $msgRetorno ="Não foi possível alterar as notas do(a) <strong><i>".$nomeAluno."</i></strong> porque já foi excluido(a) por faltas.";
            }else if($estadoAluno=="RI"){
                $msgRetorno ="Não foi possível alterar as notas do(a) <strong><i>".$nomeAluno."</i></strong> porque reprovou por indisciplina.";
            }else if($estadoAluno=="RFN"){
                $msgRetorno ="Não foi possível alterar as notas do(a) <strong><i>".$nomeAluno."</i></strong> porque reprovou por falta de notas.";
            }else if($estadoAluno=="NA/TRANSF" || $estadoAluno=="A/TRANSF"){
                $msgRetorno ="Não foi possível alterar as notas do(a) <strong><i>".$nomeAluno."</i></strong> porque já foi transferido.";
            }else{

               if($this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", $nomeValoresEnviar, $valoresEnviar, ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$idPDisciplina, "classePauta"=>$classe, "semestrePauta"=>$semestrePauta, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 1)=="sim"){

                    if(count(listarItensObjecto($this->sobreAluno, "arquivo_pautas", ["idPautaDisciplina=".$idPDisciplina, "idPautaAno=".$this->idAnoActual, "idPautaEscola=".$_SESSION["idEscolaLogada"], "classePauta=".$classe, "semestrePauta=".$semestrePauta, "idPautaCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]))<=0){
                        //Gravar disciplinas caso não existir...
                        $this->gravarPautasAluno($idPautaMatricula, $classe);
                    }

                   $this->editarItemObjecto("alunos_".$this->grupoAluno, "arquivo_pautas", $nomeValoresEnviar, $valoresEnviar, ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$idPDisciplina, "idPautaAno"=>$this->idAnoActual, "idPautaEscola"=>$_SESSION["idEscolaLogada"], "classePauta"=>$classe, "semestrePauta"=>$semestrePauta, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 1);

                    $stringAlteracoes="";
                    $stringAlteracoes2="";
                    $jipe=0;
                    foreach($camposAvaliacaoInicial as $campo){

                        foreach($this->camposAvaliacao as $av){

                            if($av["idCampoAvaliacao"]==$campo["idCampoAvaliacao"]){

                                if($camposAvaliacaoInicial[$jipe]["valor"]!=$av["valor"]){

                                    if($stringAlteracoes!=""){
                                        $stringAlteracoes .="; ";
                                    }
                                    $stringAlteracoes .=$campo["designacao1"]." [".$camposAvaliacaoInicial[$jipe]["valor"]."-".$av["valor"]."]";

                                    if($stringAlteracoes2!=""){
                                        $stringAlteracoes2 .="; ";
                                    }
                                    $stringAlteracoes2 .=$campo["identUnicaDb"]."[".$camposAvaliacaoInicial[$jipe]["valor"]."-".$av["valor"]."]";
                                }
                                break;
                            }
                        }
                        $jipe++;
                    }


                    if($stringAlteracoes!=""){
                        $this->actualizacaoDados="on";
                        $this->inserirObjecto("alunos_".$this->grupoAluno, "alteracoes_notas", "idPHistorial", "idHistAlterador, nomeAlterador, idHistTitular, idHistMatricula, nomeAluno, idHistDisciplina, idHistAno, idHistEscola, dataAlteracao, horaAlteracao, alteracoes, alteracoes2, estadoConselhoTurma", [$_SESSION["idUsuarioLogado"], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $idPProfessor, $idPautaMatricula, $nomeAluno, $idPDisciplina, $this->idAnoActual, $_SESSION["idEscolaLogada"], $this->dataSistema, $this->tempoSistema, $stringAlteracoes, $stringAlteracoes2, $this->estadoConselhoTurma], ["idPMatricula"=>$idPMatricula]);
                    }
                    //Tratar Disciplina as Expressões no Magistério
                    if(($idPDisciplina==58 || $idPDisciplina==59 || $idPDisciplina==60 || $idPDisciplina==231 || $idPDisciplina==232 || $idPDisciplina==233) && valorArray($this->sobreCursoAluno, "tipoCurso")=="pedagogico"){
                        $this->tratarDisciplinaExpressao($idPDisciplina, $semestrePauta, $classe, $idPautaMatricula);
                    }
                    if(valorArray($this->sobreCursoAluno, "tipoCurso")=="tecnico"){
                        $this->calcularClassificacaoFinalDaDisciplina($idPautaMatricula, $idPDisciplina, $classe, $semestrePauta, "T");
                    }
                    $this->calcularObservacaoFinalDoAluno($idPautaMatricula);
                    $msgRetorno ="VAs notas do(a) <strong><i>".$nomeAluno."</i></strong> foram alteradas com sucesso.";
                }else{
                    $msgRetorno ="Não foi possível alterar as notas do(a) <strong><i>".$nomeAluno."</i></strong>.";
               }
            }
            return $msgRetorno;
        }

        public function calcularObservacaoFinalDoAluno($idPMatricula, $array=array()){

            if(count($array)<=0){
                $this->sobreAluno($idPMatricula, ["escola.periodoAluno", "escola.idMatCurso", "escola.classeActualAluno",'pautas.idPautaDisciplina','pautas.classePauta','pautas.semestrePauta','pautas.idPautaCurso','pautas.mf','pautas.recurso','pautas.cf', 'pautas.mtI', 'pautas.mtII', 'pautas.mtIII','pautas.exameEspecial', 'reconfirmacoes.idReconfAno', 'reconfirmacoes.idReconfEscola', 'reconfirmacoes.estadoDesistencia', 'pautas.idPautaDisciplina', 'escola.provAptidao', 'escola.notaEstagio', 'grupo']);
            }else{
                $this->sobreAluno = $array;
                $this->sobreCursoAluno = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola"), "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
                $this->grupoAluno= valorArray($this->sobreAluno, "grupo");
            }

            if(!isset($this->curriculoDaClasse)){
                $this->curriculoDaClasse = $this->disciplinas (valorArray($this->sobreAluno, "idMatCurso", "escola"), valorArray($this->sobreAluno, "classeActualAluno", "escola"), valorArray($this->sobreAluno, "periodoAluno", "escola"), "", array(), array(), ["idPNomeDisciplina", "disciplinas.classeDisciplina", "disciplinas.semestreDisciplina", "disciplinas.semestreDisciplina", "disciplinas.tipoDisciplina"]);
            }
            $classe = isset($this->classeAlunoAnalizar)?$this->classeAlunoAnalizar:valorArray($this->sobreAluno, "classeActualAluno", "escola");

            $tipoCurso = valorArray($this->sobreCursoAluno, "tipoCurso");
            $periodoAluno = valorArray($this->sobreAluno, "periodoAluno", "escola");

            if($classe==120){
                $classe=valorArray($this->sobreCursoAluno, "ultimaClasse");
            }

            $luzinguLuame = listarItensObjecto($this->sobreAluno, "pautas", ["classePauta=".$classe, "idPautaCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]);

            $pautas =array();
            $i=0;
            foreach($luzinguLuame as $luzl){
                $entrar="sim";

                $sobreDisciplina = array_filter($this->curriculoDaClasse, function ($mamale) use ($luzl){
                    return $mamale["disciplinas"]["classeDisciplina"]==$luzl["classePauta"] && $mamale["disciplinas"]["semestreDisciplina"]==$luzl["semestrePauta"] && $mamale["idPNomeDisciplina"]==$luzl["idPautaDisciplina"];
                });

                foreach($sobreDisciplina as $sobre){
                    if(valorArray($this->sobreCursoAluno, "sePorSemestre")=="sim" && $sobre["disciplinas"]["continuidadeDisciplina"]!="T"){
                        $entrar="nao";
                    }
                }
                if(valorArray($this->sobreCursoAluno, "tipoCurso")=="pedagogico" && ($luzl["idPautaDisciplina"]==58 || $luzl["idPautaDisciplina"]==59 || $luzl["idPautaDisciplina"]==60 || $luzl["idPautaDisciplina"]==231 || $luzl["idPautaDisciplina"]==232 || $luzl["idPautaDisciplina"]==233)){
                    $entrar="nao";
                }
                if(count($sobreDisciplina)>0 && $entrar=="sim"){
                    $pautas[$i]=$luzl;

                    foreach($sobreDisciplina as $sobre){

                        foreach(retornarChaves($sobre) as $tony){
                            if(isset($sobre[$tony]) && !is_object($sobre[$tony])){

                              $pautas[$i][trim($tony)]=$sobre[trim($tony)];
                            }
                        }
                        foreach(retornarChaves($sobre["disciplinas"]) as $tony){
                          if(isset($sobre["disciplinas"][$tony]) && !is_object($sobre["disciplinas"][$tony])){

                              $pautas[$i][trim($tony)]=$sobre["disciplinas"][trim($tony)];
                          }
                        }
                    }
                    $i++;
                }
            }

            $fatima = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno=".$this->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada'], "idMatCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]);
            $estadoDesistencia = valorArray($fatima, "estadoDesistencia");

            $observacaoF="NA";
            $seAlunoFoiAoRecurso="I";

            if($estadoDesistencia=="D" || $estadoDesistencia=="N" || $estadoDesistencia=="F" || $estadoDesistencia=="RI" || $estadoDesistencia=="RFN" || $estadoDesistencia=="NA/TRANSF" || $estadoDesistencia=="A/TRANSF"){
                $observacaoF=$estadoDesistencia;
            }else{
                if($classe<=6){

                    $seAlunoFoiAoRecurso = $this->calcularObservacaoFinalPrimaria($pautas, $idPMatricula, $classe, "nao");

                    $observacaoF = $this->calcularObservacaoFinalPrimaria($pautas, $idPMatricula, $classe, $periodoAluno, valorArray($this->sobreAluno, "idMatCurso", "escola"), "sim");
                }else if($classe<=9){

                    $seAlunoFoiAoRecurso = $this->calcularObservacaoFinalICiclo($pautas, $idPMatricula, $classe, "nao");

                    $observacaoF = $this->calcularObservacaoFinalICiclo($pautas, $idPMatricula, $classe, "sim");

                }else{
                    if($tipoCurso=="geral"){
                        $seAlunoFoiAoRecurso = $this->calcularObservacaoFinalLiceu($pautas, $idPMatricula, $classe, "nao");
                        $observacaoF = $this->calcularObservacaoFinalLiceu($pautas, $idPMatricula, $classe, "sim");

                    }else if($tipoCurso=="pedagogico"){
                        $seAlunoFoiAoRecurso = $this->calcularObservacaoFinalMagisterio($pautas, $idPMatricula, $classe, "nao");
                        $observacaoF = $this->calcularObservacaoFinalMagisterio($pautas, $idPMatricula, $classe, "sim");
                    }else if($tipoCurso=="tecnico"){

                        $seAlunoFoiAoRecurso = $this->calcularObservacaoFinalTecnica($pautas, $idPMatricula, $classe, "nao");
                        $observacaoF = $this->calcularObservacaoFinalTecnica($pautas, $idPMatricula, $classe, "sim");

                    }
                }
            }

            $total=0;
            $somaTrim1=0;
            $somaTrim2=0;
            $somaTrim3=0;
            $somaTrim4=0;

            foreach ($pautas as $nota) {
                $total++;
                $somaTrim1 +=floatval(isset($nota["mtI"])?$nota["mtI"]:0);
                $somaTrim2 +=floatval(isset($nota["mtII"])?$nota["mtII"]:0);
                $somaTrim3 +=floatval(isset($nota["mtIII"])?$nota["mtIII"]:0);
                $somaTrim4 +=floatval(isset($nota["mf"])?$nota["mf"]:0);
            }

            if($total==0){
                $mfT1=0;
            }else{
                $mfT1 = $somaTrim1/$total;
            }

            if($total==0){
                $mfT2=0;
            }else{
                $mfT2 = $somaTrim2/$total;
            }

            if($total==0){
                $mfT3=0;
            }else{
                $mfT3 = $somaTrim3/$total;
            }

            if($total==0){
                $mfT4=0;
            }else{
                $mfT4 = $somaTrim4/$total;
            }

            if($seAlunoFoiAoRecurso=="REC"){
                $seAlunoFoiAoRecurso="A";
            }else{
                $seAlunoFoiAoRecurso="I";
            }
            if($observacaoF=="REC"){
                $observacaoF="NA";
            }
            $this->editarItemObjecto("alunos_".$this->grupoAluno, "reconfirmacoes", "mfT1, mfT2, mfT3, mfT4, seAlunoFoiAoRecurso, observacaoF, estadoCalcObs", [number_format($mfT1, 2), number_format($mfT2, 2), number_format($mfT3, 2), number_format($mfT4, 0), $seAlunoFoiAoRecurso, $observacaoF, "V"], ["idPMatricula"=>$idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idMatCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola"), "idReconfEscola"=>$_SESSION['idEscolaLogada']], "sim", "nao", 1);
        }

        private function calcularObservacaoFinalLiceu($pautas, $idPMatricula, $classe="", $considerarNotaDoRecurso="sim"){

            $repLinguaP="nao";
            $totalRepFE=0;
            $totalRepOp=0;
            $totalRepFGMenor8=0;
            $totalRepFGMenor10=0;
            $observacaoF="NA";
            $discFGNeg = array();

            foreach ($pautas as $pauta) {
                $notaConsiderar = isset($pauta["mf"])?$pauta["mf"]:0;

                if(isset($pauta["recurso"]) && $pauta["recurso"]!=NULL && $pauta["recurso"]!="" && $considerarNotaDoRecurso=="sim"){
                    $notaConsiderar = $pauta["recurso"];
                }

                if($pauta["idPNomeDisciplina"]==1){
                    if($notaConsiderar<10){
                        $repLinguaP="sim";
                    }
                }

                if($pauta["tipoDisciplina"]=="FG" && $notaConsiderar<8){
                    $totalRepFGMenor8++;
                }

                if($pauta["tipoDisciplina"]=="FG" && $notaConsiderar<9.5){
                    $totalRepFGMenor10++;
                }
                if($pauta["tipoDisciplina"]=="FE" && $notaConsiderar<9.5){
                    $totalRepFE++;
                }

                if($pauta["tipoDisciplina"]=="Op" && $notaConsiderar<9.5){
                    $totalRepOp++;
                }

                if($pauta["tipoDisciplina"]=="FG" && $notaConsiderar>=8 && $notaConsiderar<9.5){
                    $discFGNeg[] = ["semestrePauta"=>$pauta["semestrePauta"], "idPNomeDisciplina"=>$pauta["idPNomeDisciplina"]];
                }
            }

            if($classe==12){
                if($totalRepFE==0 && $totalRepFGMenor8==0 && $totalRepOp==0 && $totalRepFGMenor10<=3 && $repLinguaP=="nao"){

                    $observacaoF="REC";
                    if($totalRepFGMenor10==0){
                        $observacaoF="A";
                    }
                }
            }else{
                if($totalRepFE==0 && $totalRepFGMenor8==0 && $totalRepOp==0 && $totalRepFGMenor10<=2 && $repLinguaP=="nao"){

                    $observacaoF="TR";
                    if($totalRepFGMenor10==0){
                        $observacaoF="A";
                    }
                }
            }

            if($considerarNotaDoRecurso=="nao"){
                $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["I"], ["idPMatricula"=>$idPMatricula], ["classePauta"=>$classe, "idPautaMatricula"=>$idPMatricula, "seFoiAoRecurso"=>"A", "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 10000);

                if($observacaoF=="REC"){
                    //Marcando disciplinas como recurso
                    foreach($discFGNeg as $disc){
                        $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["A"], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$disc["idPNomeDisciplina"], "semestrePauta"=>$disc["semestrePauta"], "classePauta"=>$classe, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")]);
                    }
                }
            }
            return $observacaoF;
        }



        private function calcularObservacaoFinalMagisterio($pautas, $idPMatricula, $classe, $considerarNotaDoRecurso="sim"){

            $repLinguaP="nao";
            $totalRepFP=0;
            $totalRepFE=0;
            $totalRepOp=0;
            $totalRepFGMenor8=0;
            $totalRepFGMenor10=0;
            $observacaoF="NA";
            $discFGNeg = array();

            foreach ($pautas as $pauta) {

                $notaConsiderar = isset($pauta["mf"])?$pauta["mf"]:0;
                if(isset($pauta["recurso"]) && $pauta["recurso"]!=NULL && $pauta["recurso"]!="" && $considerarNotaDoRecurso=="sim"){
                    $notaConsiderar = $pauta["recurso"];
                }

                if($pauta["idPNomeDisciplina"]==1){
                    if($notaConsiderar<10){
                        $repLinguaP="sim";
                    }
                }else if($pauta["tipoDisciplina"]=="FG" && $notaConsiderar<8){
                    $totalRepFGMenor8++;
                }else if($pauta["tipoDisciplina"]=="FG" && $notaConsiderar<10){
                    $totalRepFGMenor10++;
                }else if($pauta["tipoDisciplina"]=="FE" && $notaConsiderar<10){
                    $totalRepFE++;
                }else if($pauta["tipoDisciplina"]=="FP" && $notaConsiderar<10){
                    $totalRepFP++;
                }else if($pauta["tipoDisciplina"]=="Op" && $notaConsiderar<10){
                    $totalRepOp++;
                }
                if($pauta["tipoDisciplina"]=="FG" && $notaConsiderar>=8 && $notaConsiderar<10){
                    $discFGNeg[] = ["semestrePauta"=>$pauta["semestrePauta"], "idPNomeDisciplina"=>$pauta["idPNomeDisciplina"]];
                }
            }

            $observacaoF="NA";
            if($classe<=11){
                if($totalRepFE==0 && $totalRepFP==0 && $totalRepFGMenor8==0 && $totalRepFGMenor10<=2 && $repLinguaP=="nao"){

                    if($totalRepFGMenor10==0){
                        $observacaoF="A";
                    }else{
                        $observacaoF="TR";
                    }
                }
            }else if($classe==12){
                if($totalRepFE==0 && $totalRepFP==0 && $totalRepFGMenor8==0 && $totalRepFGMenor10<=2 && $repLinguaP=="nao"){

                    if($totalRepFGMenor10==0){
                        $observacaoF="A";
                    }else{
                        $observacaoF="REC";
                    }
                }
            }else if($classe==13){
                $pap = (int) valorArray($this->sobreAluno, "provAptidao", "escola");
                $nec = (int) valorArray($this->sobreAluno, "notaEstagio", "escola");
                if($totalRepFE==0 && $pap>=10 && $nec>=10 && $totalRepFP==0 && $totalRepFGMenor8==0 && $totalRepFGMenor10==0 && $repLinguaP=="nao"){
                    $observacaoF="A";
                }
            }

            if($considerarNotaDoRecurso=="nao"){
                $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["I"], ["idPMatricula"=>$idPMatricula], ["classePauta"=>$classe, "idPautaMatricula"=>$idPMatricula, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 10000);
                if($observacaoF=="REC"){
                    //Marcando disciplinas como recurso
                    foreach($discFGNeg as $disc){
                        $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["A"], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$disc["idPNomeDisciplina"], "semestrePauta"=>$disc["semestrePauta"], "classePauta"=>$classe, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")]);
                    }
                }
            }
            return $observacaoF;
        }

        private function calcularObservacaoFinalTecnica ($pautas, $idPMatricula, $classe="", $considerarNotaDoRecurso="sim"){
            $totalRepC=0;
            $totalRepT=0;
            $totalRepCMenor7=0;
            $totalRepTMenor7=0;
            $observacaoF="NA";

            $discContinuasNeg = array();
            $discTerminaisNeg = array();

            foreach ($pautas as $pauta){
                if(valorArray($this->sobreCursoAluno, "campoAvaliar", "cursos")=="mfd"){
                    $notaConsiderar = isset($pauta["mf"])?$pauta["mf"]:0;
                }else{
                    $notaConsiderar = isset($pauta["cf"])?$pauta["cf"]:0;
                }
                if(isset($pauta["recurso"]) && $pauta["recurso"]!=NULL && $pauta["recurso"]!="" && $considerarNotaDoRecurso=="sim"){
                    $notaConsiderar = $pauta["recurso"];
                }

                if($pauta["continuidadeDisciplina"]=="C" && $notaConsiderar<9.5){
                    $totalRepC++;
                }

                if($pauta["continuidadeDisciplina"]=="T" && $notaConsiderar<9.5){
                    $totalRepT++;
                }

                if($pauta["continuidadeDisciplina"]=="T" && $notaConsiderar<6.5){
                    $totalRepTMenor7++;
                }

                if($pauta["continuidadeDisciplina"]=="T" && $notaConsiderar<9.5 && $notaConsiderar>=7){
                    $discTerminaisNeg[] = ["semestrePauta"=>$pauta["semestrePauta"], "idPNomeDisciplina"=>$pauta["idPNomeDisciplina"]];
                }
                if($pauta["continuidadeDisciplina"]=="C" && $notaConsiderar<6.5){
                    $discContinuasNeg[] = ["semestrePauta"=>$pauta["semestrePauta"], "idPNomeDisciplina"=>$pauta["idPNomeDisciplina"]];
                    $totalRepCMenor7++;
                }
            }

            if($totalRepT==0 && $totalRepC==0){
                $observacaoF="A";
            }else if($totalRepT==0 && $totalRepC<=2 && $totalRepCMenor7==0){
                $observacaoF="TR";
            }else if(($totalRepT<=2 && $totalRepC<=2 && $totalRepCMenor7==0 && $totalRepTMenor7==0)

                || ($totalRepT<=3 && $totalRepC<=2 && $totalRepCMenor7==0 && $totalRepTMenor7==0 && ($classe==12 || $classe==11) && $_SESSION['idEscolaLogada']==10) ){

                $observacaoF="REC";

            }else if(($totalRepT<=4 && $totalRepC==0 && $totalRepTMenor7==0 && ($classe==10 || $classe==13)  && $_SESSION['idEscolaLogada']==10) || ($totalRepT<=5 && $totalRepC==0 && $totalRepCMenor7==0 && $totalRepTMenor7==0 && ($classe==12 || $classe==11) && $_SESSION['idEscolaLogada']==10)){
                $observacaoF="REC";
            }

            if($classe==(9+valorArray($this->sobreCursoAluno, "duracao")) && $_SESSION['idEscolaLogada']==16 && $observacaoF=="A"){

                $pap = valorArray($this->sobreAluno, "provAptidao", "escola");
                $nec = valorArray($this->sobreAluno, "notaEstagio", "escola");
                if(valorArray($this->sobreCursoAluno, "sePorSemestre")=="sim"){
                    if($nec<10){
                        $observacaoF="REC";
                    }
                }else{
                    $observacaoF="NA";
                    if(is_numeric($pap) && is_numeric($nec)){
                        if($pap>=10 && $nec>=10){
                            $observacaoF="A";
                        }else{
                            $observacaoF="NA";
                        }
                    }else if(is_numeric($nec)){
                        if($nec>=10){
                            $observacaoF="TR";
                        }
                    }
                }
            }
            if($considerarNotaDoRecurso=="nao"){
                $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["I"], ["idPMatricula"=>$idPMatricula], ["classePauta"=>$classe, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 10000);
                if($observacaoF=="REC"){
                    //Marcando disciplinas como recurso
                    foreach($discTerminaisNeg as $disc){
                        $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["A"], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$disc["idPNomeDisciplina"], "semestrePauta"=>$disc["semestrePauta"], "classePauta"=>$classe, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")]);
                    }
                }
            }
            return $observacaoF;
        }

        private function calcularObservacaoFinalPrimaria($pautas, $idPMatricula, $classe="", $considerarNotaDoRecurso="sim"){
            $observacaoF="NA";
            $totalDisciplinas=0;
            $totalNotas=0;
            $media=0;
            $repMatematicaLingua="nao";
            $totalRep3=0;
            $totalRep5=0;
            foreach ($pautas as $pauta) {

                $totalDisciplinas++;
                $notaConsiderar = nelson($pauta, "mf");

                if(isset($pauta["recurso"]) && $pauta["recurso"]!=NULL && $pauta["recurso"]!="" && $considerarNotaDoRecurso=="sim"){
                    $notaConsiderar = $pauta["recurso"];
                }
                $totalNotas += floatval($notaConsiderar);


                if(($pauta["idPNomeDisciplina"]==1 || $pauta["idPNomeDisciplina"]==2) && $notaConsiderar<5){
                    $repMatematicaLingua="sim";
                }else{
                    if($notaConsiderar<3){
                        $totalRep3++;
                    }else if($notaConsiderar<5){
                        $discNegMaior3[]=["semestrePauta"=>$pauta["semestrePauta"], "idPNomeDisciplina"=>$pauta["idPNomeDisciplina"]];
                        $totalRep5++;
                    }
                }
            }

            if($totalDisciplinas>0){
                $media = $totalNotas/$totalDisciplinas;
            }


            if($classe==6){
                if($repMatematicaLingua=="nao" && $totalRep3==0 && $totalRep5==0){
                   $observacaoF="A";
                }else if($repMatematicaLingua=="nao" && $totalRep3==0 && $totalRep5<=2){
                   $observacaoF="REC";
                }
            }else{
                if($media>=4.5 && $repMatematicaLingua=="nao"){
                   $observacaoF="A";
                }
            }


            if($considerarNotaDoRecurso=="nao"){
                $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["I"], ["idPMatricula"=>$idPMatricula], ["classePauta"=>$classe, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 10000);

                if($observacaoF=="REC"){
                    //Marcando disciplinas como recurso
                    foreach($discTerminaisNeg as $disc){
                        $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["A"], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$disc["idPNomeDisciplina"], "semestrePauta"=>$disc["semestrePauta"], "classePauta"=>$classe, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 1);
                    }
                }
            }
            return $observacaoF;
        }

        private function calcularObservacaoFinalICiclo($pautas, $idPMatricula, $classe="", $considerarNotaDoRecurso="sim"){
            $observacaoF="NA";
            $totalRep8=0;
            $totalRep10=0;
            $repMatematicaLingua="nao";

            foreach ($pautas as $pauta) {
                $notaConsiderar = nelson($pauta, "mf");
                if(isset($pauta["recurso"]) && $pauta["recurso"]!=NULL && $pauta["recurso"]!="" && $considerarNotaDoRecurso=="sim"){
                    $notaConsiderar = $pauta["recurso"];
                }
                if(($pauta["idPNomeDisciplina"]==1 || $pauta["idPNomeDisciplina"]==2) && $notaConsiderar<10){
                    $repMatematicaLingua="sim";
                }else{
                    if($notaConsiderar<8){
                        $totalRep8++;
                    }else if($notaConsiderar<10){
                        $discNegMaior8[]=["semestrePauta"=>$pauta["semestrePauta"], "idPNomeDisciplina"=>$pauta["idPNomeDisciplina"]];
                        $totalRep10++;
                    }
                }
            }

            if($classe==9){
                if($repMatematicaLingua=="nao" && $totalRep10<=3 && $totalRep8==0){
                    $observacaoF="A";
                    if($totalRep10>=1){
                        $observacaoF="REC";
                    }
                }
            }else{
                if($repMatematicaLingua=="nao" && $totalRep10<=2 && $totalRep8==0){
                    $observacaoF="A";
                }
            }


            if($considerarNotaDoRecurso=="nao"){
                $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["I"], ["idPMatricula"=>$idPMatricula], ["classePauta"=>$classe, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 10000);
                if($observacaoF=="REC"){
                    //Marcando disciplinas como recurso
                    foreach($discNegMaior8 as $disc){
                        $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "seFoiAoRecurso", ["A"], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$disc["idPNomeDisciplina"], "semestrePauta"=>$disc["semestrePauta"], "classePauta"=>$classe, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 1);
                    }
                }
            }
            return $observacaoF;
        }

         public function calcularClassificacaoFinalDaDisciplina($idPMatricula, $idPDisciplina, $classePauta, $semestrePauta, $continuidadeDisciplina, $idAnoPauta=""){

            $this->sobreAluno($idPMatricula, ["escola.idMatCurso", "pautas.mf", "pautas.classePauta", "pautas.idPautaDisciplina", "pautas.idPautaCurso", "pautas.semestrePauta", "pautas.classePauta", "pautas.idPautaCurso", "pautas.idPautaDisciplina", "grupo"]);

            $i=0;
            $total=0;

            $listaClasses=array();
            foreach (listarItensObjecto($this->sobreAluno, "pautas", ["idPautaDisciplina=".$idPDisciplina, "mf>0", "idPautaCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]) as $nota) {
                $mediaF = isset($nota["mf"])?$nota["mf"]:0;
                $i++;
                $total += floatval($mediaF);
                $listaClasses[]=array("classe"=>$nota["classePauta"]);
            }
            if($i<=0){
                $cfd=0;
            }else{
                $cfd = $total/$i;
            }
            $cfd = number_format($cfd, 0);

            //Zerar toda cf
            $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "cf", [null], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$idPDisciplina, "semestrePauta"=>$semestrePauta, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")]);

            $this->editarItemObjecto("alunos_".$this->grupoAluno, "arquivo_pautas", "cf", [null], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$idPDisciplina, "semestrePauta"=>$semestrePauta, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola"), "idPautaAno"=>$idAnoPauta, "idPautaEscola"=>$_SESSION['idEscolaLogada']]);

            $ultimaClasse = valorArray(ordenar($listaClasses, "classe DESC"), "classe");

            //Actualizar CF apenas na última classes...
            $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", "cf", [$cfd], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$idPDisciplina, "classePauta"=>$ultimaClasse, "semestrePauta"=>$semestrePauta, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")], "sim", "nao", 1);
            $this->editarItemObjecto("alunos_".$this->grupoAluno, "arquivo_pautas", "cf", [$cfd], ["idPMatricula"=>$idPMatricula], ["idPautaDisciplina"=>$idPDisciplina, "classePauta"=>$ultimaClasse, "semestrePauta"=>$semestrePauta, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola"), "idPautaAno"=>$idAnoPauta, "idPautaEscola"=>$_SESSION['idEscolaLogada']], "sim", "nao", 1);
        }

        public function tratarDisciplinaExpressao($idPautaDisciplina, $semestrePauta, $classe, $idPMatricula){

            if($idPautaDisciplina==58 || $idPautaDisciplina==59 || $idPautaDisciplina==60){
                $disciplinaMae=51;
            }else if($idPautaDisciplina==231 || $idPautaDisciplina==232 || $idPautaDisciplina==233){
                $disciplinaMae=140;
            }

            $camposPautas = ['idPMatricula', 'nomeAluno', 'numeroInterno', "grupo", "arquivo_pautas.idPPauta", "pautas.idPPauta", "pautas.classePauta", "pautas.idPautaCurso", "pautas.idPautaDisciplina", "escola.idMatCurso"];
            foreach($this->camposAvaliacao as $pppp){
                $camposPautas[] = "pautas.".$pppp["identUnicaDb"];
            }
            $this->sobreAluno($idPMatricula, $camposPautas);

            //Tratar Disciplina as Expressões no Magistério
            $disciplinasDasExpressoes=array();
            foreach (listarItensObjecto($this->sobreAluno, "pautas", ["classePauta=".$classe, "idPautaCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]) as $nota) {
                if($disciplinaMae==51 && ($nota["idPautaDisciplina"]==58 || $nota["idPautaDisciplina"]==59 || $nota["idPautaDisciplina"]==60) ){
                    $disciplinasDasExpressoes[] =$nota;

                }else if($disciplinaMae==140 && ($nota["idPautaDisciplina"]==231 || $nota["idPautaDisciplina"]==232 || $nota["idPautaDisciplina"]==233)){
                    $disciplinasDasExpressoes[] =$nota;
                }
            }

            $acumuladorDados=array();
            foreach($this->camposAvaliacao as $pppp){
                $acumuladorDados[] = array('titulo'=>$pppp["identUnicaDb"], "valor"=>null);
            }
            foreach ($disciplinasDasExpressoes as $nota) {
                $i=0;
                foreach ($acumuladorDados as $a) {
                    $valorP = $a["titulo"];

                    if(isset($nota[$valorP]) && $nota[$valorP]!=NULL){
                        $acumuladorDados[$i]["valor"] +=floatval(isset($nota[$valorP])?$nota[$valorP]:0);
                    }
                    $i++;
                }
            }

            //Calcular Médias
            $i=0;
            foreach ($acumuladorDados as $a) {

                if(count($disciplinasDasExpressoes)>0){

                    if($acumuladorDados[$i]["valor"]!=NULL){
                        $acumuladorDados[$i]["valor"] = $acumuladorDados[$i]["valor"]/count($disciplinasDasExpressoes);

                        if($acumuladorDados[$i]["titulo"]=="mf" || $acumuladorDados[$i]["titulo"]=="cf" || $acumuladorDados[$i]["titulo"]=="recurso"){
                            $acumuladorDados[$i]["valor"] = number_format($acumuladorDados[$i]["valor"], 0);
                        }else{
                            $acumuladorDados[$i]["valor"] = number_format($acumuladorDados[$i]["valor"], 2);
                        }
                    }
                }else{
                    $acumuladorDados[$i]["valor"]=null;
                }
                $i++;
            }

            $valores =array();
            $campos="";
            foreach ($acumuladorDados as $d) {
                if($campos!=""){
                    $campos .=", ";
                }
                $campos .=$d["titulo"];
                $valores[] = $d["valor"];
            }

            $this->editarItemObjecto("alunos_".$this->grupoAluno, "pautas", $campos, $valores, ["idPMatricula"=>$idPMatricula], ["classePauta"=>$classe, "idPautaDisciplina"=>$disciplinaMae, "semestrePauta"=>$semestrePauta, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")]);

            $this->editarItemObjecto("alunos_".$this->grupoAluno, "arquivo_pautas", $campos, $valores, ["idPMatricula"=>$idPMatricula], ["classePauta"=>$classe, "idPautaDisciplina"=>$disciplinaMae, "idPautaAno"=>$this->idAnoActual, "semestrePauta"=>$semestrePauta, "idPautaCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola")]);
        }

        private function valKey ($array, $key){
            $valorRetor =0;
            foreach ($array as $a) {
                if($a["titulo"]==$key){
                    $valorRetor = $a["valor"];
                    break;
                }
            }
            return $valorRetor;
        }

        private function calcularMtMod2020($trimestre){
            $contador=0;
            $somador=0;
            $media=0;
            $posicao=0;
            $posicaoMT=-1;
            $nomeMT="";

            $aa =""; $tcp="";
            foreach($this->camposAvaliacao as $campo){

                if($campo["periodo"]==$trimestre){
                    $idU = $campo["identUnicaDb"];
                    if($campo["tipoCampo"]=="avaliacao" && ($idU!="aaI" && $idU!="tcpI" && $idU!="aaII" && $idU!="tcpII" && $idU!="aaIII" && $idU!="tcpIII")){
                       $contador++;
                       $somador +=floatval($campo["valor"]);
                    }else if($campo["tipoCampo"]=="mediaTrim"){
                        $posicaoMT=$posicao;
                        $nomeMT=$campo["identUnicaDb"];
                    }
                    if($idU=="aaI" || $idU=="aaII" || $idU=="aaIII")
                    {
                        $aa = floatval($campo["valor"]);
                    }
                    else if($idU=="tcpI" || $idU=="tcpII" || $idU=="tcpIII"){
                        $tcp = floatval($campo["valor"]);
                    }
                }
                $posicao++;
            }

            if($contador>0){
                $media = number_format(($somador/$contador), 2);
                if($aa !="" || $tcp !="")
                {
                    $media = $media + $aa + $tcp;
                }
            }

            if($posicaoMT>-1){
                $this->camposAvaliacao[$posicaoMT]["valor"]=$media;
            }
        }

        public function calcularMfdMod20(){
            $contador=0;
            $somador=0;
            $media=0;
            $posicao=0;
            $posicaoMFD=-1;
            foreach($this->camposAvaliacao as $campo){

                $tipoCampo = is_array($campo)?$campo["tipoCampo"]:$campo->tipoCampo;

                $valor = is_array($campo)?$campo["valor"]:$campo->valor;
                if($tipoCampo=="mediaTrim"){
                   $contador++;
                   $somador +=floatval($valor);
                }else if($tipoCampo=="mfd"){
                    $posicaoMFD=$posicao;
                }
                $posicao++;
            }
            if($contador>0){
                $media = number_format(($somador/$contador), 2);
            }
            if($posicaoMFD>=0){
                if(is_array($campo)){
                    $this->camposAvaliacao[$posicaoMFD]["valor"]=$media;
                }else{
                    $this->camposAvaliacao[$posicaoMFD]->valor=$media;
                }
            }
        }

        public function calcularMediaFinalMod2020(){

            $mfd=0;
            $exame=0;
            $seTemExame="nao";
            $posicaoMf=-1; $posicaoCfd=-1;
            $contador=0;
            foreach($this->camposAvaliacao as $campo){
                $tipoCampo = is_array($campo)?$campo["tipoCampo"]:$campo->tipoCampo;
                $valor = is_array($campo)?$campo["valor"]:$campo->valor;

                if($tipoCampo=="mfd"){
                    $mfd=$valor;
                }
                if($tipoCampo=="exame"){
                    $exame=$valor;
                    $seTemExame="sim";
                }

                if($tipoCampo=="mediaFinal"){
                    $posicaoMf=$contador;
                }
                if($tipoCampo=="classificaDisciplina"){
                    $posicaoCfd=$contador;
                }
                $contador++;
            }

            if($seTemExame=="sim"){
                $mf = floatval($mfd)*0.4+floatval($exame)*0.6;
            }else{
              $mf = $mfd;
            }
            $mf = number_format($mf, 0);
            if($posicaoMf>-1){
                if(is_array($campo)){
                    $this->camposAvaliacao[$posicaoMf]["valor"]=$mf;
                }else{
                    $this->camposAvaliacao[$posicaoMf]->valor=$mf;
                }
            }

            if($posicaoCfd>-1){
                if(is_array($campo)){
                    $this->camposAvaliacao[$posicaoCfd]["valor"]="";
                }else{
                    $this->camposAvaliacao[$posicaoCfd]->valor="";
                }
            }
        }
     }


?>

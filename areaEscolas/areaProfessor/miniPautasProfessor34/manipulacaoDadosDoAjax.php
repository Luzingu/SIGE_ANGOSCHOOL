<?php 
    session_cache_expire(60);
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipuladorPauta.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
           
        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->manipuladorPautas = new manipuladorPauta();

            $this->idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:null;
            $this->classe = isset($_POST["classe"])?$_POST["classe"]:null;
            $this->turma = isset($_POST["turma"])?$_POST["turma"]:null;
            $this->trimestre = isset($_POST["trimestre"])?$_POST["trimestre"]:null;
            $this->tipoCurso = isset($_POST["tipoCurso"])?$_POST["tipoCurso"]:null;
            $this->idPNomeDisciplina = isset($_POST["idPNomeDisciplina"])?$_POST["idPNomeDisciplina"]:null;
            $this->semestreActivo = isset($_POST["semestreActivo"])?$_POST["semestreActivo"]:null;
            $this->periodo = retornarPeriodoTurma($this, $this->idPCurso,  $this->classe, $this->turma);
            if($this->accao=="alterarNotas" || $this->accao=="alterarAvaliacoesContinuas"){ 
                $this->idPProfessor = $_SESSION["idUsuarioLogado"];
                $this->manipularPauta();      
            }else if($this->accao=="buscarNotas"){
                $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
                $classe = isset($_GET["classe"])?$_GET["classe"]:"";
                $turma = isset($_GET["turma"])?$_GET["turma"]:"";
                $idPDisciplina = isset($_GET["idPDisciplina"])?$_GET["idPDisciplina"]:"";
                $this->trimestre = isset($_GET["trimestre"])?$_GET["trimestre"]:"";
                $areaEmExecucao = isset($_GET["areaEmExecucao"])?$_GET["areaEmExecucao"]:"";

                $this->retornarPautas($idPCurso, $classe, $turma, $idPDisciplina, $areaEmExecucao);
                echo json_encode($this->humberto);
            }
        }

        private function manipularPauta(){

            if($this->trimestre=="I"){
                $trimestre="trimestre1";
            }else if($this->trimestre=="II"){
                $trimestre="trimestre2";
            }else if($this->trimestre=="III"){
                 $trimestre="trimestre3";
            }else if($this->trimestre=="IV"){
                 $trimestre="exame";
            }else{
                $trimestre="trimestre1";
            }

            $this->array = $this->selectArray("divisaoprofessores", ["periodoTrimestre", "avaliacoesContinuas"], ["nomeTurmaDiv"=>$this->turma, "classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idAnoActual, "idPNomeDisciplina"=>$this->idPNomeDisciplina, "semestre"=>$this->semestreActivo, "idPNomeCurso"=>$this->idPCurso]); 

            if( valorArray($this->array, "periodoTrimestre")==$trimestre ||  valorArray($this->array, "periodoTrimestre")=="todos"){
                if($this->accao=="alterarNotas"){
                    $this->alterPauta();
                }else if($this->accao=="alterarAvaliacoesContinuas"){
                    $this->alterarAvaliacoesContinuas();
                }
                
            }else{                
                echo "FNão podes alterar os dados deste trimestre, porque encontra-se bloqueado.";
            }
        }

        private function alterPauta(){
            $this->disciplinas ($this->idPCurso, $this->classe, $this->periodo, "", array(), array(), ["idPNomeDisciplina", "disciplinas.classeDisciplina", "disciplinas.semestreDisciplina", "disciplinas.tipoDisciplina"]);
            $this->manipuladorPautas->curriculoDaClasse = $this->disciplinas;

            $this->camposAvaliacaoAlunos($this->idAnoActual, $this->idPCurso, $this->classe, $this->periodo, $this->idPNomeDisciplina);
            $this->manipuladorPautas->camposPautas = $this->camposPautas;
            $this->manipuladorPautas->camposArquivoPautas = $this->camposArquivoPautas;
            $this->manipuladorPautas->camposAvaliacao = $this->camposAvaliacao;
            $this->manipuladorPautas->trimestres = $this->trimestres;

            
            $notas = json_decode($_POST["dados"]);
            $msgRetorno=array();
            foreach ($notas as $nota) {

                $avaliacoesQuantitativas = isset($nota->avaliacoesQuantitativas)?$nota->avaliacoesQuantitativas:array();
                $avaliacoesQualitativas = isset($nota->avaliacoesQualitativas)?$nota->avaliacoesQualitativas:array();

                $idPDisciplina = isset($nota->idPDisciplina)?$nota->idPDisciplina:null;
                $idPMatricula = isset($nota->idPMatricula)?$nota->idPMatricula:null;

               $retorno = $this->manipuladorPautas->alterPautaMod_2020($this->classe, $idPMatricula, $idPDisciplina, $this->idPProfessor, $this->trimestre, $this->turma, $this->semestreActivo, $this->array, $avaliacoesQuantitativas, $avaliacoesQualitativas); 

               $msgRetorno[] = array('msg'=>$retorno);
            } 
            if($this->trimestre=="IV"){
                $this->retornarPautas($this->idPCurso, $this->classe, $this->turma, $this->idPNomeDisciplina, "exame");    
            }else{
                $this->retornarPautas($this->idPCurso, $this->classe, $this->turma, $this->idPNomeDisciplina, "miniPauta");
            }
            $this->humberto[2] = $msgRetorno;          
            echo json_encode($this->humberto);
        }

        private function alterarAvaliacoesContinuas(){
            $this->disciplinas ($this->idPCurso, $this->classe, $this->periodo, "", array(), array(), ["idPNomeDisciplina", "disciplinas.classeDisciplina", "disciplinas.semestreDisciplina", "disciplinas.tipoDisciplina"]);
            $this->manipuladorPautas->curriculoDaClasse = $this->disciplinas;

             $this->camposAvaliacaoAlunos($this->idAnoActual, $this->idPCurso, $this->classe, $this->periodo, $this->idPNomeDisciplina);

            $this->manipuladorPautas->camposPautas = $this->camposPautas;
            $this->manipuladorPautas->camposArquivoPautas = $this->camposArquivoPautas;
            $this->manipuladorPautas->camposAvaliacao = $this->camposAvaliacao;
            $this->manipuladorPautas->trimestres = $this->trimestres;

            $campos[]="pautas.avaliacoesContinuas".$this->trimestre;
            foreach($this->camposAvaliacao as $campo){
                $campos[] = 'pautas.'.trim($campo["identUnicaDb"]);
            }

            $notas = json_decode($_POST["dados"]);

            $msgRetorno=array();
            foreach ($notas as $nota) {

                $idPMatricula = isset($nota->idPMatricula)?$nota->idPMatricula:null;
                $idPDisciplina = isset($nota->idPDisciplina)?$nota->idPDisciplina:null;
                $numeroAvalContinuas = isset($nota->numeroAvalContinuas)?$nota->numeroAvalContinuas:null;

                $aval[1] = isset($nota->aval1)?$nota->aval1:null;
                $aval[2] = isset($nota->aval2)?$nota->aval2:null;
                $aval[3] = isset($nota->aval3)?$nota->aval3:null;
                $aval[4] = isset($nota->aval4)?$nota->aval4:null;
                $aval[5] = isset($nota->aval5)?$nota->aval5:null;
                $aval[6] = isset($nota->aval6)?$nota->aval6:null;
                $aval[7] = isset($nota->aval7)?$nota->aval7:null;
                $aval[8] = isset($nota->aval8)?$nota->aval8:null;
                $aval[9] = isset($nota->aval9)?$nota->aval9:null;
                $aval[10] = isset($nota->aval10)?$nota->aval10:null;
                $aval[11] = isset($nota->aval11)?$nota->aval11:null;
                $aval[12] = isset($nota->aval12)?$nota->aval12:null;

                $condicaoPauta = ["idPMatricula"=>$idPMatricula, "pautas.idPautaDisciplina"=>$idPDisciplina, "pautas.classePauta"=>$this->classe, "pautas.semestrePauta"=>$this->semestreActivo, "pautas.idPautaCurso"=>$this->idPCurso];               

                $array = $this->selectArray("alunosmatriculados", $campos, $condicaoPauta, ["pautas"]);

                $dadosAnterior = explode("-", valorArray($array, "avaliacoesContinuas".$this->trimestre, "pautas"));

                $somador=0;
                $mac=0;
                for($t=1; $t<=12; $t++){
                    if($aval[$t]==null){
                        $aval[$t]=isset($dadosAnterior[$t])?$dadosAnterior[$t]:"";
                    }else if(is_numeric($aval[$t])){
                        $aval[$t]=number_format($aval[$t], 2);
                    }
                    if($t<=$numeroAvalContinuas){
                        $somador +=(double)$aval[$t];
                    }
                }
                $mac = $somador/$numeroAvalContinuas;
                $mac = number_format($mac, 2);

                $avalContinua = $numeroAvalContinuas."-".$aval[1]."-".$aval[2]."-".$aval[3]."-".$aval[4]."-".$aval[5]."-".$aval[6]."-".$aval[7]."-".$aval[8]."-".$aval[9]."-".$aval[10]."-".$aval[11]."-".$aval[12];


                $avaliacoesQuantitativas = array();
                foreach($this->camposAvaliacao as $campo){
                    if($campo["tipoCampo"]=="avaliacao"){
                        $valor=valorArray($array, $campo["identUnicaDb"], "pautas");
                        if(($campo["identUnicaDb"]=="macI" && $this->trimestre=="I") || ($campo["identUnicaDb"]=="macII" && $this->trimestre=="II") || ($campo["identUnicaDb"]=="macIII" && $this->trimestre=="III")){
                            $valor=$mac;
                        }
                        $avaliacoesQuantitativas[]=array("name"=>$campo["identUnicaDb"], "valor"=>$valor, "idCampoAvaliacao"=>$campo["idCampoAvaliacao"]);
                    }
                }

                $retorno = $this->manipuladorPautas->alterPautaMod_2020($this->classe, $idPMatricula, $idPDisciplina, $this->idPProfessor, $this->trimestre, $this->turma, $this->semestreActivo, $this->array, $avaliacoesQuantitativas, array(), $avalContinua);

               $msgRetorno[] = array('msg'=>$retorno);
            }
            $this->retornarPautas($this->idPCurso, $this->classe, $this->turma, $this->idPNomeDisciplina, "avalContinuas");
            $this->humberto[2] = $msgRetorno;

            echo json_encode($this->humberto);
        }

        function retornarPautas($idPCurso, $classe, $nomeTurma, $idDisciplina, $areaEmExecucao){
            
            $this->periodo = retornarPeriodoTurma($this, $idPCurso,  $classe, $nomeTurma);
            $this->humberto[0] = $this->camposAvaliacaoAlunos($this->idAnoActual, $idPCurso, $classe, $this->periodo, $idDisciplina, $this->trimestre);

            $campos = ['idPMatricula', 'sexoAluno', 'nomeAluno', 'numeroInterno', 'fotoAluno', 'pautas.idPPauta','pautas.idPautaMatricula','pautas.idPautaDisciplina'];

            if($areaEmExecucao=="avalContinuas"){
                $campos[]='pautas.avaliacoesContinuas'.$this->trimestre;
            }

            foreach($this->humberto[0] as $humb){
                $campos[] = 'pautas.'.trim($humb["identUnicaDb"]);
            }
            $campos[] = 'pautas.numeroFaltas'.$this->trimestre;
            $campos[] = 'pautas.comportamento'.$this->trimestre;
            $campos[] = 'pautas.assiduidade'.$this->trimestre;
            $this->humberto[1] = $this->miniPautas($idPCurso, $classe, $nomeTurma, [], $idDisciplina, "pautas", $this->idAnoActual, $campos, $this->semestreActivo);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>




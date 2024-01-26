<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();            
            if($this->accao=="processarResultado"){
                
                if($this->verificacaoAcesso->verificarAcesso("", ["processarResultados"])){

                    $this->conDb("inscricao");
                    
                    $this->idPCurso = isset($_GET["idGestCurso"])?$_GET["idGestCurso"]:"";
                    
                    $tipoResultado = isset($_GET["tipoResultado"])?$_GET["tipoResultado"]:"";

                    if($tipoResultado=="provisorio"){
                        $this->editar("gestorvagas", "estadoTransicaoCurso", ["Y"], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestCurso"=>$this->idPCurso, "idGestAno"=>$this->idAnoActual]);
                    }else{
                        $this->editar("gestorvagas", "estadoTransicaoCurso", ["V"], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestCurso"=>$this->idPCurso, "idGestAno"=>$this->idAnoActual]);
                    }
                    
                    $this->processarResultado();
                    echo $this->selectJson("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idAnoActual]);

                }
            }
            
        }

        private function processarResultado(){
            $this->gestorvagas = $this->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idAnoActual, "idGestCurso"=>$this->idPCurso]);
            $criterioTeste = valorArray($this->gestorvagas, "criterioTeste");
            $arrayAlunos=array();
            $ordemArray="";
            if($criterioTeste=="factor"){
                $idadeMedia=15*360;

                

                foreach ($this->selectArray("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$this->idAnoActual, "inscricao.idInscricaoCurso"=>$this->idPCurso], ["inscricao"]) as $aluno) {
                    
                    $percMedia = ((double)$aluno["inscricao"]["mediaDiscNuclear"]/20)*valorArray($this->gestorvagas, "perMedDiscNucleares");

                        $idadeAluno = calcularIdade(explode("-", $this->dataSistema)[0], $aluno["dataNascAluno"]);

                        $dataS = explode("-", $this->dataSistema);
                        $dataSistema = new DateTime(date("Y-m-d H:i:s", mktime(0, 0, 0, $dataS[1], $dataS[2], $dataS[0])));

                        $dataAluno = explode("-", $aluno["dataNascAluno"]);
                        $dataNascAluno = new DateTime(date("Y-m-d H:i:s", mktime(0, 0, 0, $dataAluno[1], $dataAluno[2], $dataAluno[0])));                    
                        $intervalo = $dataSistema->diff($dataNascAluno);

                        $totalDiasAlunos = $intervalo->d+$intervalo->y*360+$intervalo->m*30;

                    if(valorArray($this->gestorvagas, "percDataNascAluno")<=0 || valorArray($this->gestorvagas, "percDataNascAluno")==NULL || valorArray($this->gestorvagas, "percDataNascAluno")==""){
                        $percDataNascAluno =0;
                    }else if($totalDiasAlunos<=$idadeMedia){
                        $percDataNascAluno = valorArray($this->gestorvagas, "percDataNascAluno");
                    }else{
                        $diferenca = $totalDiasAlunos - $idadeMedia;
                        $percDataNascAluno = ((100-$diferenca*0.0125)/100)*valorArray($this->gestorvagas, "percDataNascAluno");
                        if($percDataNascAluno<=10){
                            $percDataNascAluno=10;
                        }
                    }
                    if(valorArray($this->gestorvagas, "percGenero")<=0 || valorArray($this->gestorvagas, "percGenero")==NULL){
                        $percGenero = 0;
                    }else{
                        if($aluno["sexoAluno"]=="M"){
                            $percGenero=0;
                        }else{
                            $percGenero = valorArray($this->gestorvagas, "percGenero");
                        }
                    }
                    $percAlunosEmRegime =0;

                    $totalPercentagemAluno = $percAlunosEmRegime+$percDataNascAluno+$percMedia;
                    $totalPercentagemAluno = number_format($totalPercentagemAluno, 3);

                    $arrayAlunos[] =array('idPAluno'=>$aluno["idPAluno"], "nomeAluno"=>$aluno["nomeAluno"], "sexoAluno"=>$aluno["sexoAluno"], "dataNascAluno"=>$aluno["dataNascAluno"], "totalPercentagemAluno"=>$totalPercentagemAluno, "mediaDiscNuclear"=>(double)$aluno["inscricao"]["mediaDiscNuclear"], "numeroOpcao"=>$aluno["inscricao"]["numeroOpcaoCurso"], "nota1"=>0, "nota2"=>0, "nota3"=>0, "mediaFinal"=>0, "alunosEmRegime"=>"", "periodoInscricao"=>$aluno["inscricao"]["periodoInscricao"]);
                }
                $ordemArray="totalPercentagemAluno DESC, mediaDiscNuclear DESC, dataNascAluno DESC";

            }else if($criterioTeste=="criterio"){
                foreach ($this->selectArray("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$this->idAnoActual, "inscricao.idInscricaoCurso"=>$this->idPCurso], ["inscricao"]) as $aluno) {
                    $arrayAlunos[] =array('idPAluno'=>$aluno["idPAluno"], "nomeAluno"=>$aluno["nomeAluno"], "sexoAluno"=>$aluno["sexoAluno"], "dataNascAluno"=>$aluno["dataNascAluno"], "totalPercentagemAluno"=>0, "mediaDiscNuclear"=>(double)$aluno["inscricao"]["mediaDiscNuclear"], "numeroOpcao"=>nelson($aluno, "numeroOpcaoCurso"), "nota1"=>0, "nota2"=>0, "nota3"=>0, "mediaFinal"=>0, "alunosEmRegime"=>"", "periodoInscricao"=>$aluno["inscricao"]["periodoInscricao"]);
                }
                $ordemArray=ordemFactor(valorArray($this->gestorvagas, "factor1")).", ".ordemFactor(valorArray($this->gestorvagas, "factor2")).", ".ordemFactor(valorArray($this->gestorvagas, "factor3")).", ".ordemFactor(valorArray($this->gestorvagas, "factor4"));

            }else if($criterioTeste=="exameAptidao"){

                foreach ($this->selectArray("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$this->idAnoActual, "inscricao.idInscricaoCurso"=>$this->idPCurso], ["inscricao"]) as $aluno) {

                    $arrayAlunos[] =array('idPAluno'=>$aluno["idPAluno"], "nomeAluno"=>$aluno["nomeAluno"], "sexoAluno"=>$aluno["sexoAluno"], "dataNascAluno"=>$aluno["dataNascAluno"], "totalPercentagemAluno"=>0, "mediaDiscNuclear"=>(double)nelson($aluno, "mediaDiscNuclear", "inscricao"), "numeroOpcao"=>nelson($aluno, "numeroOpcaoCurso", "inscricao"), "nota1"=>nelson($aluno, "notaExame1", "inscricao"), "nota2"=>nelson($aluno, "notaExame2", "inscricao"), "nota3"=>nelson($aluno, "notaExame3", "inscricao"), "mediaFinal"=>nelson($aluno, "mediaExames", "inscricao"), "alunosEmRegime"=>"", "periodoInscricao"=>$aluno["inscricao"]["periodoInscricao"]);
                }
                $ordemArray="mediaFinal DESC, ".ordemFactor(valorArray($this->gestorvagas, "factor2")).", ".ordemFactor(valorArray($this->gestorvagas, "factor3")).", ".ordemFactor(valorArray($this->gestorvagas, "factor4"));
            }
            $arrayAlunos = ordenar($arrayAlunos, $ordemArray);

            $alunosAprovados = array();
            $alunosReprovados = array();
            foreach ($arrayAlunos as $aluno) {
                $passou="nao";
                if($criterioTeste=="exameAptidao" && $aluno["mediaFinal"]>=10 && (valorArray($this->gestorvagas, "seAvaliarApenasMF")=="sim" || ((valorArray($this->gestorvagas, "numeroProvas")==1 && $aluno["nota1"]>=10) || (valorArray($this->gestorvagas, "numeroProvas")==2 && $aluno["nota1"]>=10 && $aluno["nota2"]>=10) || (valorArray($this->gestorvagas, "numeroProvas")==3 && $aluno["nota1"]>=10 && $aluno["nota2"]>=10 && $aluno["nota3"]>=10))) ){
                    $passou="sim";
                }else if($criterioTeste=="criterio"){
                    $passou="sim";
                }else if($criterioTeste=="factor"){
                    $passou="sim";
                }

                if($passou=="sim"){
                    $alunosAprovados[] = $aluno;
                }else{
                    $alunosReprovados[]=$aluno;
                }
            }
            $alunosAprovados = ordenar($alunosAprovados, $ordemArray);
            $alunosReprovados = ordenar($alunosReprovados, $ordemArray);

            $totalVagas = (int)valorArray($this->gestorvagas, "vagasReg")+(int)valorArray($this->gestorvagas, "vagasPos");
            
            $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento", ["R", "F", 0], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso], "sim", "nao", 1000000000000000000);

            $posicao=0;
            foreach ($alunosAprovados as $aluno) {
                $posicao++;

                if(valorArray($this->gestorvagas, "criterioEscolhaPeriodo")=="auto"){
                    if($posicao<=$totalVagas){

                        if($posicao<=valorArray($this->gestorvagas, "vagasReg")){

                            $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento, periodoApuramento, percentagemAcumulada", ["A", "F", $posicao, "reg", $aluno["totalPercentagemAluno"]], ["idPAluno"=>$aluno["idPAluno"], "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);
                        }else{
                            $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento, periodoApuramento, percentagemAcumulada", ["A", "F", $posicao, "pos", $aluno["totalPercentagemAluno"]], ["idPAluno"=>$aluno["idPAluno"], "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);                           
                        }
                    }else{
                        $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento, periodoApuramento, percentagemAcumulada", ["R", "V", $posicao, "reg", $aluno["totalPercentagemAluno"]], ["idPAluno"=>$aluno["idPAluno"], "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);
                    }

                }else if($aluno["periodoInscricao"]=="reg"){

                    $angolania = count($this->selectArray("alunos", ["idPAluno"], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.idInscricaoCurso"=>$this->idPCurso, "inscricao.obsApuramento"=>"A", "inscricao.periodoApuramento"=>"reg"], ["inscricao"]));

                    if($angolania<valorArray($this->gestorvagas, "vagasReg")){

                        $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento, periodoApuramento, percentagemAcumulada", ["A", "F", ($angolania+1), "reg", $aluno["totalPercentagemAluno"]], ["idPAluno"=>$aluno["idPAluno"], "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);
                    }else{
                        $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento, periodoApuramento, percentagemAcumulada", ["R", "V", ($angolania+1), "reg", $aluno["totalPercentagemAluno"]], ["idPAluno"=>$aluno["idPAluno"], "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);
                    }
                }else if($aluno["periodoInscricao"]=="pos"){

                    $angolania = count($this->selectArray("alunos", ["idPAluno"], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.idInscricaoCurso"=>$this->idPCurso, "inscricao.obsApuramento"=>"A", "inscricao.periodoApuramento"=>"pos"], ["inscricao"]));

                    if($angolania<valorArray($this->gestorvagas, "vagasPos")){

                       $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento, periodoApuramento, percentagemAcumulada", ["A", "F", ($angolania+1), "pos", $aluno["totalPercentagemAluno"]], ["idPAluno"=>$aluno["idPAluno"], "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);
                    }else{
                        $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento, periodoApuramento, percentagemAcumulada", ["R", "V", ($angolania+1), "pos", $aluno["totalPercentagemAluno"]], ["idPAluno"=>$aluno["idPAluno"], "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);
                    }
                }
            }

            foreach ($alunosReprovados as $aluno) {
                $posicao++;
                 $this->editarItemObjecto("alunos", "inscricao", "obsApuramento, seRepPorVagas, posicaoApuramento, percentagemAcumulada", ["R", "V", $posicao, $aluno["totalPercentagemAluno"]], ["idPAluno"=>$aluno["idPAluno"], "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);
            }

            foreach ($this->selectArray("alunos", ["idPAluno", "inscricao.numeroOpcaoCurso"], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.idInscricaoCurso"=>$this->idPCurso, "inscricao.obsApuramento"=>"A"], ["inscricao"]) as $result){
                if($result["inscricao"]["numeroOpcaoCurso"]>1){
                    $this->afectar($result["idPAluno"], $result["inscricao"]["numeroOpcaoCurso"]);    
                }                
            }
        }

        private function afectar($idPAluno, $numeroOpcaoCurso){


            for($i=1; $i<$numeroOpcaoCurso; $i++){
                //verificar se nas opções anteriores aprovou..
                if(count($this->selectArray("alunos", ["idPAluno"], ["idPAluno"=>$idPAluno, "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.obsApuramento"=>"A", "inscricao.numeroOpcaoCurso"=>$i], ["inscricao"], []))>0){

                    $this->editarItemObjecto("alunos", "inscricao", "obsApuramento", ["P"], ["idPAluno"=>$idPAluno, "idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["idInscricaoCurso"=>$this->idPCurso]);
                    $this->modificarCurso();
                    break;
                }
            }
        }

        private function modificarCurso(){

            $i=0;
            foreach ($this->selectArray("alunos", ["idPAluno", "inscricao.numeroOpcaoCurso", "inscricao.idPInscrito", "inscricao.periodoApuramento"], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.seRepPorVagas"=>"V", "inscricao.idInscricaoCurso"=>$this->idPCurso], ["inscricao"], "", [], array("inscricao.posicaoApuramento"=>1)) as $aluno) {

                $i++;
                if($i==1){
                    $periodoApuramento = $aluno["inscricao"]["periodoApuramento"];

                    if($periodoApuramento=="auto"){
                        if(count($this->selectArray("alunos", ["idPMatricula"], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.idInscricaoCurso"=>$this->idPCurso, "inscricao.obsApuramento"=>"A", "inscricao.periodoApuramento"=>"reg"], ["inscricao"]))<valorArray($this->gestorvagas, "vagasReg")){
                            $periodoApuramento="reg";
                        }else{
                            $periodoApuramento="pos";
                        }
                    }

                    $this->editarItemObjecto("alunos", "inscricao", "seRepPorVagas, obsApuramento, periodoApuramento", ["F", "A", $periodoApuramento], ["idPAluno"=>$aluno["idPAluno"]], ["idPInscrito"=>$aluno["inscricao"]["idPInscrito"]]);

                    if($aluno["numeroOpcaoCurso"]>1){
                        $this->afectar($aluno["idPAluno"], $aluno["inscricao"]["numeroOpcaoCurso"]);    
                    }                    
                    break;
                }
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);


    function ordemFactor($fact){
        if($fact=="dataNascAluno"){
            $fact =$fact." DESC";
        }else if($fact=="sexoAluno"){
            $fact =$fact." ASC";
        }else if($fact=="mediaDiscNuclear"){
            $fact =$fact." DESC";
        }else if($fact=="alunosEmRegime"){
            $fact =$fact." DESC";
        }
        return $fact;
    }

?>
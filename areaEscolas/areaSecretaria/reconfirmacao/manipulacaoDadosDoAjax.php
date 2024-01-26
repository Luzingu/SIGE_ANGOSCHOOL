<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();           
            
            if(isset($_POST["idPMatricula"])){
                $this->idPMatricula = $_POST['idPMatricula'];
            }else{
                $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:"";
            }
            $this->art1="o";
            $this->art2="e";

            $this->aluno = $this->selectArray("alunosmatriculados", ["sexoAluno", "grupo"], ["idPMatricula"=>$this->idPMatricula]);
            $this->grupoAluno = valorArray($this->aluno, "grupo");

            if(valorArray($this->aluno, "sexoAluno")=="F"){
                $this->art1 = $this->art2="a";
            }

            if(isset($_POST["classe"])){
                $this->classe = $_POST['classe'];
            }else{
                $this->classe = isset($_GET["classe"])?$_GET["classe"]:"";
            }

            if(isset($_POST["idPCurso"])){
                $this->idPCurso = $_POST['idPCurso'];
            }else{
                $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            }

            if(isset($_POST["idPAno"])){
                $this->idPAno = $_POST['idPAno'];
            }else{
                $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            }   
            
            
            
            if($this->accao=="anularReconfirmacao"){
                 if($this->verificacaoAcesso->verificarAcesso("", ["reconfirmacao"], [$this->classe, $this->idPCurso])){

                    if($this->idPAno==$this->idAnoActual){
                        $this->anularReconfirmacao();    
                    }else{
                        echo "FNão podes anular reconfirmação deste ano lectivo.";
                    }
                    
                }
            }else if($this->accao=="suspenderMatricula"){
                if($this->verificacaoAcesso->verificarAcesso("", ["reconfirmacao"], [$this->classe, $this->idPCurso])){
                    $this->suspenderMatricula();
                }
            } else if($this->accao=="reconfirmar"){

                /*if(count(listarItensObjecto($this->sobreEscolaLogada, "trans_classes", ["idTransClAno=".$this->idAnoActual, "classeTrans=".$this->classe, "idTransClCurso=".$this->idPCurso]))<=0){
                    echo "FAinda não se fez transição dos alunos nesta classe."; 
                }else */if($this->verificacaoAcesso->verificarAcesso("", ["reconfirmacao"], [$this->classe, $this->idPCurso])){
                    $this->reconfirmar();
                }
            }else if($this->accao=="actualizarLista"){
                $this->actualizarLista();
            }else if($this->accao=="pegarAlunosNaoReconfirmados"){

                 $luzinguLuame = $this->selectArray("alunosmatriculados", ["nomeAluno", "sexoAluno", "dataNascAluno", "biAluno", "dataEBIAluno", "paiAluno", "maeAluno", "encarregadoEducacao", "telefoneAluno", "fotoAluno", "emailAluno", "dataCaducidadeBI", "estadoAcessoAluno", "tipoDocumento", "localEmissao", "numeroProcesso", "deficienciaAluno", "numeroInterno", "deficienciaAluno", "tipoDeficienciaAluno", "paisNascAluno", "provNascAluno", "municNascAluno", "comunaNascAluno", "idPMatricula", "escola.estadoDeDesistenciaNaEscola","escola.idMatAnexo","escola.idGestLinguaEspecialidade","escola.idGestDisEspecialidade","escola.periodoAluno","escola.numeroProcesso","escola.idMatCurso","escola.classeActualAluno", "escola.turnoAluno", "reconfirmacoes.tipoEntrada", "reconfirmacoes.idMatCurso", "reconfirmacoes.idReconfAno", "reconfirmacoes.idReconfEscola", "reconfirmacoes.estadoReconfirmacao"], ["escola.classeActualAluno"=>$this->classe, "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>"A", "escola.idMatCurso"=>$this->idPCurso], ["escola"], "", [], array("nomeAluno"=>1));

                $alunosNaoReconfirmados = array();
                foreach ($luzinguLuame as $aluno) {
                    if(count(listarItensObjecto($aluno, "reconfirmacoes", ["idReconfAno=".$this->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada'], "idMatCurso=".$this->idPCurso, "estadoReconfirmacao=A"]))<=0){
                       $alunosNaoReconfirmados[]=$aluno;
                    }
                }
                echo json_encode($alunosNaoReconfirmados);

            }else if($this->accao=="pegarAlunosReconfirmados"){

              $array = $this->selectArray("alunosmatriculados", ["escola.classeActualAluno", "reconfirmacoes.dataReconf", "nomeAluno", "numeroInterno", "idPMatricula", "reconfirmacoes.idPReconf", "fotoAluno", "escola.idMatAno", "reconfirmacoes.horaReconf"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.classeReconfirmacao"=>$this->classe, "reconfirmacoes.idReconfProfessor"=>$_SESSION['idUsuarioLogado'], "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$this->idPCurso], ["escola", "reconfirmacoes"], "", [], array("nomeAluno"=>1), $this->matchMaeAlunos($this->idAnoActual, $this->idPCurso, $this->classe));

              $array = $this->anexarTabela2($array, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
                echo json_encode($array);
            }
        }

        private function actualizarLista(){

            $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $classe = isset($_GET["classe"])?$_GET["classe"]:"";
            $periodo = isset($_GET["periodo"])?$_GET["periodo"]:"";

            $array = $this->selectArray("alunosmatriculados", ["idPMatricula", "reconfirmacoes.dataReconf", "escola.idMatAno", "nomeAluno", "numeroInterno", "escola.classeActualAluno", "reconfirmacoes.idReconfAno", "sexoAluno", "fotoAluno", "reconfirmacoes.designacaoTurma"], ["reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.periodoAluno"=>$periodo, "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.idMatCurso"=>$idPCurso], ["escola", "reconfirmacoes"], "", [], array("nomeAluno"=>1), $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe));
            echo json_encode($array);         
        }

        private function anularReconfirmacao(){

            if($this->editarItemObjecto("alunos_".$this->grupoAluno, "reconfirmacoes", "estadoReconfirmacao, nomeTurma, designacaoTurma", ["I", "", ""], ["idPMatricula"=>$this->idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>$this->idPCurso])=="sim"){

                $this->actuazalizarReconfirmacaAluno($this->idPMatricula);
                $this->editarItemObjecto("alunos_".$this->grupoAluno, "pagamentos", "estadoPagamento", ["I"], ["idPMatricula"=>$this->idPMatricula], ["idHistoricoAno"=>$this->idAnoActual, "codigoEmolumento"=>"reconfirmacao", "idHistoricoEscola"=>$_SESSION['idEscolaLogada']]);

                echo "VA reconfirmação foi anulada com sucesso.";
            }else{
                echo "FNão foi possível anular a reconfirmação do(a) aluno(a).";
            }
        }

        private function suspenderMatricula(){
            if($this->editarItemObjecto("alunos_".$this->grupoAluno, "escola", "estadoAluno", ["F"], ["idPMatricula"=>$this->idPMatricula], ["idMatEscola"=>$_SESSION["idEscolaLogada"]])=="sim"){
                echo "V".strtoupper($this->art1)." alun".$this->art1." foi suspens".$this->art1." com sucesso.";
            }else{
                echo "FNão foi possível suspender o".$this->art1." alun".$this->art1.".";
            }
        }

        function reconfirmar(){

            $this->idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";
            $this->classeAluno = isset($_POST["classeAluno"])?$_POST["classeAluno"]:"";
            $this->sexoAluno = isset($_POST["sexoAluno"])?$_POST['sexoAluno']:"";

            $this->art1="o";
            $this->art2="e";
            if($this->sexoAluno=="F"){
                $this->art1 = $this->art2="a";
            }

            $idAnoPassado=1;
            $mesVerificar=6;

            //Verificar Pagamentos...
            $arrayEstadoCompart = $this->selectArray("escolas", ["pagamentos.estado"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "estadoperiodico.objecto"=>"verfComparticipacoes"], ["estadoperiodico"]);

            $verfComparticipacoes = valorArray($arrayEstadoCompart, "estado", "estadoperiodico");            

            $verfComparticipacoes="F";
            if($verfComparticipacoes=="V" && count($this->selectArray("alunos_".$this->grupoAluno, ["idPMatricula"], ["idPMatricula"=>$this->idPMatricula, "pagamentos.idTipoEmolumento"=>1, "pagamentos.idHistoricoEscola"=>$_SESSION["idEscolaLogada"], "pagamentos.idHistoricoAno"=>$this->idAnoActual], ["pagamentos"]))<10){ 

                echo "FEst".$this->art2." alun".$this->art1." ainda não concluiu o pagamento das comparticipações";

            }else if($this->classeAluno>=10 && ($this->idPCurso==NULL || $this->idPCurso=="")){
                echo "FDeves actualizar o curso d".$this->art1." alun".$this->art1;
            }else{

                $array = $this->selectArray("alunosmatriculados", ["escola.idMatAno", "escola.beneficiosDaBolsa"], ["idPMatricula"=>$this->idPMatricula, "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"], 1);

                if(valorArray($array, "idMatAno", "escola")==$this->idAnoActual){
                    $idPHistoricoConta = $this->pagamentoAnteriorDoAluno($this->idPMatricula, "matricula");
                    $precoEmolumento = $this->preco("matricula", $this->classeAluno, $this->idPCurso, "", $array);           
                }else{
                    $precoEmolumento = $this->preco("reconfirmacao", $this->classeAluno, $this->idPCurso, "", $array);
                    $idPHistoricoConta = $this->pagamentoAnteriorDoAluno($this->idPMatricula, "reconfirmacao");
                }

                if(($idPHistoricoConta==NULL || $idPHistoricoConta=="") && $precoEmolumento>0){
                    echo "FEst".$this->art2." aluno ainda não fez o pagamento da reconfirmação";
                }else{
                    $grupoAluno = $this->grupoAluno;

                    $reto = $this->editarDadosAluno($this->idPMatricula, "../../../", "sim");
                    if($reto=="sim"){

                        $this->editarItemObjecto("alunos_".$grupoAluno, "reconfirmacoes", "estadoReconfirmacao, classeReconfirmacao, idMatCurso, chaveReconf", ["A", "".$this->classeAluno."", $this->idPCurso, $this->idPMatricula."-".$this->idPCurso."-".$this->idAnoActual."-".$_SESSION["idEscolaLogada"]], ["idPMatricula"=>$this->idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada']]);

                        $this->inserirObjecto("alunos_".$grupoAluno, "reconfirmacoes", "idPReconf", "idmatricula, dataReconf, horaReconf, idMatCurso, classeReconfirmacao, chaveReconf, idReconfProfessor, idReconfAno, idReconfEscola, tipoEntrada, nomeTurma, designacaoTurma, estadoReconfirmacao", [$this->idPMatricula, $this->dataSistema, $this->tempoSistema, $this->idPCurso, "".$this->classeAluno."", $this->idPMatricula."-".$this->idPCurso."-".$this->idAnoActual."-".$_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], $this->idAnoActual, $_SESSION["idEscolaLogada"], "novaMatricula", "", "", "A"], ["idPMatricula"=>$this->idPMatricula]);

                        $this->actuazalizarReconfirmacaAluno($this->idPMatricula);
                        
                        $this->editarItemObjecto("alunos_".$grupoAluno, "pagamentos", "estadoPagamento", ["A"], ["idPMatricula"=>$this->idPMatricula], ["idHistoricoAno"=>$this->idAnoActual, "idTipoEmolumento"=>2, "idHistoricoEscola"=>$_SESSION['idEscolaLogada']]);

                        $this->gravarPautasAluno($this->idPMatricula);
                        $this->atualizarTurma($this->idPMatricula, $this->classeAluno, $this->idPCurso, "", $grupoAluno);
                        echo "V".strtoupper($this->art1)." alun".$this->art1." foi reconfirmada com sucesso.";
                       
                    }else{
                        echo $reto;
                    }
                }
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
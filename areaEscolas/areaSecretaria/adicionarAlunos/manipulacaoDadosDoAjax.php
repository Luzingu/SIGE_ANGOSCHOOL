<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $classeAluno = isset($_POST["classeAluno"])?$_POST["classeAluno"]:"";
            $idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";

            if($this->accao=="pesquisarAluno"){
                $this->pesquisarAluno();
            }else if($this->accao=="adicionarAluno"){
                if($this->verificacaoAcesso->verificarAcesso("", ["adicionarAlunos"], [$classeAluno, $idPCurso],  "FNão tens permissão de adiconar um aluno.")){
                    $this->adicionarAluno();
                }
            }          
        }

        private function pesquisarAluno(){
            $valorPesq = isset($_GET["valorPesq"])?$_GET["valorPesq"]:"";

            $condicoesPesquisa = [array("nomeAluno"=>new \MongoDB\BSON\Regex($valorPesq)), array("biAluno"=>new \MongoDB\BSON\Regex($valorPesq)), array("numeroInterno"=>new \MongoDB\BSON\Regex($valorPesq)), array("nomeAluno"=>new \MongoDB\BSON\Regex(ucwords($valorPesq)))];

            $array = $this->selectArray("alunosmatriculados", ["nomeAluno", "sexoAluno", "dataNascAluno", "biAluno", "dataEBIAluno", "paiAluno", "maeAluno", "encarregadoEducacao", "telefoneAluno", "emailAluno", "dataCaducidadeBI", "estadoAcessoAluno", "tipoDocumento", "localEmissao", "fotoAluno", "deficienciaAluno", "numeroInterno", "deficienciaAluno", "tipoDeficienciaAluno", "paisNascAluno", "provNascAluno", "municNascAluno", "comunaNascAluno", "idPMatricula", "escola.estadoDeDesistenciaNaEscola", "escola.idMatEscola", "escola.estadoAluno", "escola.idMatCurso", "escola.classeActualAluno"], ['$or'=>$condicoesPesquisa], [], 20, [], ["nomeAluno"=>1]);

            $arrayEntrar=array();
            $contador=0;
            foreach($array as $a){
                $escola = listarItensObjecto($a, "escola", ["idMatEscola=".$_SESSION['idEscolaLogada']]);   
                if(count($escola)<=0 || (valorArray($escola, "estadoAluno")!="A" && valorArray($escola, "estadoAluno")!="Y")){
                    $arrayEntrar[]=$a;
                    $contador++;
                }
            }
            echo json_encode($arrayEntrar);
        }

        private function adicionarAluno (){

            $idPMatricula = isset($_POST["idPMatricula"])?$_POST["idPMatricula"]:"";

            $arrayAluno = $this->selectArray("alunosmatriculados", ["escola.estadoAluno", "grupo", "escola.idMatEscola", "nomeAluno", "numeroInterno", "biAluno", "escola.idCursos"], ["idPMatricula"=>$idPMatricula]);

            $alunoNaEscola =listarItensObjecto($arrayAluno, "escola", ["idMatEscola=".$_SESSION['idEscolaLogada']]);

            $grupo = valorArray($arrayAluno, "grupo");

            $sexoAluno = isset($_POST["sexoAluno"])?$_POST['sexoAluno']:"M";
            $periodoAluno = isset($_POST["periodoAluno"])?$_POST['periodoAluno']:"";
            $numeroProcesso = isset($_POST["numeroProcesso"])?$_POST['numeroProcesso']:"";
            $idMatAnexo = isset($_POST["idMatAnexo"])?$_POST['idMatAnexo']:"";
            $idPCurso = isset($_POST["idPCurso"])?$_POST['idPCurso']:"";

            $classeAluno = isset($_POST["classeAluno"])?$_POST['classeAluno']:"";
            $this->rpm = isset($_POST["rpm"])?$_POST['rpm']:"";

            $dataCaducidadeBI = isset($_POST["dataCaducidadeBI"])?$_POST['dataCaducidadeBI']:"";
            $idGestDisEspecialidadeNovo = isset($_POST["discEspecialidade"])?$_POST["discEspecialidade"]:"";
            $idGestLinguaEspecialidadeNovo = isset($_POST["lingEspecialidade"])?$_POST["lingEspecialidade"]:"";

            $tipoEntrada = isset($_POST["tipoEntrada"])?$_POST["tipoEntrada"]:"";
            $turnoAluno = isset($_POST["turnoAluno"])?$_POST["turnoAluno"]:"";
            $art1="o";
            $art2="e";
            if($sexoAluno=="F"){
                $art1 = $art2="a";
            }

            $precoEmolumento = $this->preco("matricula", $classeAluno, $idPCurso);
            $rpm = $this->selectArray("pagamentos_matricula_inscricao", [], ["rpm"=>$this->rpm]);

            if($precoEmolumento>0 && valorArray($rpm, "estadoPagamento")!="I"){
                echo "FA referência do pagamento de matrícula é inválida.";
            }else if(valorArray($alunoNaEscola, "estadoAluno")=="Y"){
                echo "FNão podes adicionar est".$art2." alun".$art1." porque já finalizou nesta instituição.";
            }else if(valorArray($alunoNaEscola, "estadoAluno")=="A"){
                echo "FEst".$art2." alun".$art1." já está adicionad".$art1." na instituição.";
            }else{

                $reto =$this->editarDadosAluno($idPMatricula, "../../../", "sim");
                if($reto=="sim"){
                    $explode = explode("_", $_POST['classeAluno']);

                    $flysquad="";
                    if(count($explode)>1){
                        $classeAluno=$this->ultimaClasse($idPCurso);
                        $idMatFAno=$explode[1];
                    }else{
                        $classeAluno=$_POST['classeAluno'];
                        $idMatFAno="";
                    }

                    if(count($alunoNaEscola)>0){
                        $this->editarItemObjecto("alunos_".$grupo, "escola", "estadoAluno, periodoAluno, numeroProcesso, idMatAnexo, idMatCurso, classeActualAluno, turnoAluno, idMatFAno", ["A", $periodoAluno, $numeroProcesso, $idMatAnexo, $idPCurso, "".$classeAluno."", $turnoAluno, $idMatFAno], ["idPMatricula"=>$idPMatricula], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);
                        
                    }else{
                        $this->inserirObjecto("alunos_".$grupo, "escola", "idPAlEscola", "idFMatricula, idMatAno, idMatEscola, idMatEntidade, estadoAluno, dataMatricula, horaMatricula, periodoAluno, numeroProcesso, inscreveuSeAntes, idTabelaInscricao, idMatAnexo, idMatCurso, classeActualAluno, estadoDeDesistenciaNaEscola, turnoAluno, rpm, idMatFAno", [$idPMatricula, $this->idAnoActual, $_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], "A", $this->dataSistema, $this->tempoSistema, $periodoAluno, $numeroProcesso, "Y", NULL, $idMatAnexo, $idPCurso, "".$classeAluno."", "A", $turnoAluno, $this->rpm, $idMatFAno], ["idPMatricula"=>$idPMatricula]); 
                    }

                    if(count(explode("_", $_POST['classeAluno']))<=1){
                        $this->inserirObjecto("alunos_".$grupo, "reconfirmacoes", "idPReconf", "idReconfMatricula, dataReconf, horaReconf, idMatCurso, classeReconfirmacao, chaveReconf, idReconfProfessor, idReconfAno, idReconfEscola, tipoEntrada, estadoReconfirmacao", [$idPMatricula, $this->dataSistema, $this->tempoSistema, $idPCurso, "".$classeAluno."", $idPMatricula."-".$idPCurso."-".$this->idAnoActual."-".$_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], $this->idAnoActual, $_SESSION["idEscolaLogada"], $tipoEntrada, "A"], ["idPMatricula"=>$idPMatricula]);

                        $this->actuazalizarReconfirmacaAluno($idPMatricula);

                        $this->atualizarTurma($idPMatricula, $classeAluno, $idPCurso, "", $grupo); 
                        $this->gravarPautasAluno($idPMatricula);

                        if($precoEmolumento>0){
                            $this->editar("pagamentos_matricula_inscricao", "estadoPagamento, nomeAluno, biAluno, numeroInterno", ["A", valorArray($arrayAluno, "nomeAluno"), valorArray($arrayAluno, "biAluno"), valorArray($arrayAluno, "numeroInterno")], ["rpm"=>$this->rpm]);

                            $this->inserirObjecto("alunos_".$grupo, "pagamentos", "idPHistoricoConta", "idHistoricoEscola, idHistoricoAno, dataPagamento, horaPagamento, idHistoricoMatricula, idHistoricoFuncionario, nomeFuncionario, idTipoEmolumento, codigoEmolumento, designacaoEmolumento, referenciaPagamento, precoInicial, precoMulta , precoDesconto, precoPago, estadoPagamento, contaUsada", [$_SESSION['idEscolaLogada'], $this->idAnoActual, $this->dataSistema, $this->tempoSistema,  $idPMatricula, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), "", "matricula", "Matricula", $classeAluno, valorArray($rpm, "valorPago"), 0, 0, valorArray($rpm, "valorPago"), "A", valorArray($rpm, "contaUsar")], ["idPMatricula"=>$idPMatricula]);
                        }
                    }
                    $this->seleccionadorEspecialidades($idPCurso, $classeAluno, $idGestLinguaEspecialidadeNovo, $idGestDisEspecialidadeNovo, $idPMatricula, $grupo);
                    $this->tratarArrayDeCursos($idPMatricula, $idPCurso);
                    
                    echo "V".strtoupper($art1)." alun".$art1." foi adicionad".$art1." com sucesso."; 
                }else{
                    echo $reto;
                }
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
	        $this->idPAluno = filter_input(INPUT_POST, "idPAluno", FILTER_SANITIZE_NUMBER_INT);            
	        $this->nomeAluno = trim(filter_input(INPUT_POST, "nomeAluno", FILTER_SANITIZE_STRING));
	        $this->sexoAluno = filter_input(INPUT_POST, "sexoAluno", FILTER_SANITIZE_STRING);
	        $this->dataNascAluno = trim(filter_input(INPUT_POST, "dataNascAluno", FILTER_SANITIZE_STRING));
	        $this->municipio = trim(filter_input(INPUT_POST, "municipio", FILTER_SANITIZE_NUMBER_INT));
            $this->provincia = trim(filter_input(INPUT_POST, "provincia", FILTER_SANITIZE_NUMBER_INT));
            $this->comuna = trim(filter_input(INPUT_POST, "comuna", FILTER_SANITIZE_NUMBER_INT));
	        $this->numBI = trim(filter_input(INPUT_POST, "numBI", FILTER_SANITIZE_STRING));
	        $this->dataEmissaoBI = trim(filter_input(INPUT_POST, "dataEmissaoBI", FILTER_SANITIZE_STRING));
	        $this->nomePai = trim(filter_input(INPUT_POST, "nomePai", FILTER_SANITIZE_STRING));
	        $this->nomeMae = trim(filter_input(INPUT_POST, "nomeMae", FILTER_SANITIZE_STRING));
	        $this->numTelefone = trim(filter_input(INPUT_POST, "numTelefone", FILTER_SANITIZE_NUMBER_INT));
	       $this->pais = filter_input(INPUT_POST, "pais", FILTER_SANITIZE_NUMBER_INT);
            $this->emailAluno = filter_input(INPUT_POST, "emailAluno", FILTER_SANITIZE_STRING);
            $this->rpm = filter_input(INPUT_POST, "rpm", FILTER_SANITIZE_STRING);

            $this->estadoInscricao = filter_input(INPUT_POST, "estadoInscricao", FILTER_SANITIZE_STRING);

            $this->periodoInscricao = filter_input(INPUT_POST, "periodoInscricao", FILTER_SANITIZE_STRING);
            $this->mediaDiscNuclear = isset($_POST['mediaDiscNuclear'])?$_POST['mediaDiscNuclear']:0;

            $this->idPCurso = isset($_POST['idPCurso'])?$_POST['idPCurso']:0;

            
            $this->areaEmExecucao = filter_input(INPUT_POST, "areaEmExecucao", FILTER_SANITIZE_STRING);

            if($this->accao=="salvarCadastro"){
                if($this->verificacaoAcesso->verificarAcesso("", ["inscricaoNovaInscricao"], [10, $this->idPCurso])){
                    $this->salvarCadastro();
                }
            }else if($this->accao=="editarCadastro"){
                if($this->verificacaoAcesso->verificarAcesso("", ["inscricaoNovaInscricao"])){
                    $this->editarCadastro();
                }
            }else if($this->accao=="excluirCadastro"){
                if($this->verificacaoAcesso->verificarAcesso("", ["inscricaoNovaInscricao"])){
                    $this->excluirCadastro();
                }
            }else if($this->accao=="trocarCursoAluno"){
                if($this->verificacaoAcesso->verificarAcesso("", ["inscricaoNovaInscricao"])){
                    $this->trocarCursoAluno();
                }
            }
    	}

        private function trocarCursoAluno(){
            $idPCurso=isset($_POST['idNovoCurso'])?$_POST['idNovoCurso']:"";
            $this->idPAluno=isset($_POST['idPAluno'])?$_POST['idPAluno']:"";

            $this->conDb("inscricao");
            $sobreInscricao = $this->selectArray("alunos", ["inscricao.idInscricaoCurso", "inscricao.mediaDiscNuclear", "inscricao.periodoInscricao"], ["idPAluno"=>$this->idPAluno], ["inscricao"]);
            $this->idPCurso = valorArray($sobreInscricao, "idInscricaoCurso", "inscricao");
            if($idPCurso==$this->idPCurso){
                echo "FNão alterou o curso do aluno";
            }else if($this->inserirObjecto("alunos", "inscricao", "idPInscrito", "idFAluno, periodoInscricao, mediaDiscNuclear, idInscricaoAno, idInscricaoCurso, idInscricaoEscola, idInscricaoProfessor, chaveInscricao, dataInscricao, tempoInscricao, numeroOpcaoCurso, estadoMatricula, mediaExames", [$this->idPAluno, valorArray($sobreInscricao, "periodoInscricao", "inscricao"), valorArray($sobreInscricao, "mediaDiscNuclear", "inscricao"), $this->idAnoActual, $idPCurso, $_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], $this->idPAluno."-".$idPCurso."-".$this->idAnoActual."-".$_SESSION["idEscolaLogada"], $this->dataSistema, $this->tempoSistema, 1, "F", 0], ["idPAluno"=>$this->idPAluno])=="sim"){

                $this->excluirItemObjecto("alunos", "inscricao", ["idPAluno"=>$this->idPAluno], ["idInscricaoCurso"=>$this->idPCurso]);
                $this->atualizarGrupo($this->idPAluno, $this->idPCurso);
                $this->listar();
           }
        }

    	private function salvarCadastro(){

            $this->conDb("inscricao");
            $jaExistemNumero="V";
            while ($jaExistemNumero=="V"){
                $codigoAluno = substr(str_shuffle("1234567890"),0, 4).valorArray($this->sobreUsuarioLogado, "abrevNomeEscola").substr(str_shuffle("1234567890"),0, 4);
                if(count($this->selectArray("alunos", ["idPAluno"], ["codigoAluno"=>$codigoAluno]))<=0){
                    $jaExistemNumero="F";
                    break;
                }      
            }

            $this->conDb();
            $precoEmolumento = $this->preco("inscricao", 10, $this->idPCurso);

            $rpm = $this->selectArray("pagamentos_matricula_inscricao", [], ["rpm"=>$this->rpm, "referenciaPagamento"=>"inscricao"]);

            $this->conDb("inscricao");

            if($precoEmolumento>0 && valorArray($rpm, "estadoPagamento")!="I"){
                echo "FA referência do pagamento de matrícula é inválida.";
            }else if(seTudoMaiuscula($this->nomeAluno) || seTudoMaiuscula($this->nomeMae) || seTudoMaiuscula($this->nomePai)){
                echo "FOs dados não podem ser todos em letras maiúsculas. Digite bem os dados.";
            }else if(count($this->selectArray("alunos", ["idPAluno"], ["biAluno"=>$this->numBI, "idAlunoAno"=>$this->idAnoActual,"idAlunoEscola"=>$_SESSION['idEscolaLogada']]))>0 && $this->numBI!=""){
                echo "Este(a) aluno(a) já está cadastrado na escola.";

            }else if($this->inserir("alunos", "idPAluno", "nomeAluno, sexoAluno, dataNascAluno, comunaNascAluno, municNascAluno, provNascAluno, paisNascAluno, paiAluno, maeAluno, biAluno, dataEBIAluno, telefoneAluno, codigoAluno, idAlunoEntidade, idAlunoAno, idAlunoEscola",  [$this->nomeAluno, $this->sexoAluno, $this->dataNascAluno, $this->comuna, $this->municipio, $this->provincia, $this->pais, $this->nomePai, $this->nomeMae, $this->numBI, $this->dataEmissaoBI, $this->numTelefone, $codigoAluno, $_SESSION["idUsuarioLogado"], $this->idAnoActual, $_SESSION["idEscolaLogada"]])=="sim"){

                $idPAluno = $this->selectUmElemento("alunos", "idPAluno", ["codigoAluno"=>$codigoAluno]);
                
                $this->inserirObjecto("alunos", "inscricao", "idPInscrito", "idFAluno, periodoInscricao, mediaDiscNuclear, idInscricaoAno, idInscricaoCurso, idInscricaoEscola, idInscricaoProfessor, chaveInscricao, dataInscricao, tempoInscricao, numeroOpcaoCurso, estadoMatricula, rpm", [$idPAluno, $this->periodoInscricao, $this->mediaDiscNuclear, $this->idAnoActual, $this->idPCurso, $_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], $idPAluno."-".$this->idPCurso."-".$this->idAnoActual."-".$_SESSION["idEscolaLogada"], $this->dataSistema, $this->tempoSistema, 1, "F", $this->rpm], ["idPAluno"=>$idPAluno]);

                $this->atualizarGrupo($idPAluno, $this->idPCurso);
                $this->conDb();
                if($precoEmolumento>0){
                    $this->editar("pagamentos_matricula_inscricao", "estadoPagamento, nomeAluno, biAluno, numeroInterno", ["A", $this->nomeAluno, $this->numBI, $codigoAluno], ["rpm"=>$this->rpm]);
                }
                $this->conDb("inscricao");
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar o(a) aluno(a).";
            }
        }

        private function editarCadastro(){

            $this->conDb("inscricao");

            if(seTudoMaiuscula($this->nomeAluno) || seTudoMaiuscula($this->nomeMae) || seTudoMaiuscula($this->nomePai)){
                echo "FOs dados não podem ser todos em letras maiúsculas. Digite bem os dados.";
            }else if($this->editar("alunos", "nomeAluno, sexoAluno, dataNascAluno, municNascAluno, provNascAluno, paisNascAluno, paiAluno, maeAluno, biAluno, dataEBIAluno, telefoneAluno, emailAluno, estadoInscricao, comunaNascAluno",  [$this->nomeAluno, $this->sexoAluno, $this->dataNascAluno, $this->municipio, $this->provincia, $this->pais, $this->nomePai, $this->nomeMae, $this->numBI, $this->dataEmissaoBI, $this->numTelefone, $this->emailAluno, $this->estadoInscricao, $this->comuna], ["idPAluno"=>$this->idPAluno])=="sim"){

                $this->editarItemObjecto("alunos", "inscricao", "periodoInscricao, mediaDiscNuclear", [$this->periodoInscricao, $this->mediaDiscNuclear], ["idPAluno"=>$this->idPAluno], ["idInscricaoAno"=>$this->idAnoActual]);

                $this->listar();
            }else{
                echo "FNão foi possível editar os dados do(a) aluno(a).";
            }
        }

        private function excluirCadastro(){

            $this->conDb("inscricao");

            $array = $this->selectArray("alunos", ["inscricao.rpm"], ["idPAluno"=>$this->idPAluno], ["inscricao"]);

            if($this->excluir("alunos", ["idPAluno"=>$this->idPAluno])=="sim"){
                $this->conDb();

                $this->editar("pagamentos_matricula_inscricao", "estadoPagamento, nomeAluno, biAluno, numeroInterno", ["I", "", "", ""], ["rpm"=>valorArray($array, "rpm", "inscricao")]);
                $this->conDb("inscricao");
                $this->listar();                
            }else{
                echo "FNão foi possível excluir os dados do(a) aluno(a).";
            }
        } 

        private function listar(){
            if($this->areaEmExecucao=="inscricaoNovaInscricao"){
                echo $this->selectJson("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$this->idAnoActual, "inscricao.idInscricaoProfessor"=>$_SESSION['idUsuarioLogado'], "inscricao.idInscricaoCurso"=>$this->idPCurso, "inscricao.idInscricaoAno"=>$this->idAnoActual], ["inscricao"], "", [], ["nomeAluno"=>1]);
            }else{
                echo $this->selectJson("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$this->idAnoActual, "inscricao.idInscricaoCurso"=>$this->idPCurso], ["inscricao"], "", [], ["nomeAluno"=>1]);
            }
        }


        public function atualizarGrupo($idPAluno, $idPCurso){

            $this->conDb("inscricao");

            $this->excluirItemObjecto("alunosmatriculados", "grupo", ["idPAluno"=>$idPAluno], ["idGrupoCurso"=>$idPCurso]);               

            $grupoMenor="";
            $numeroInicial=0;
            $i=0;
            foreach ($this->selectArray("lista_grupos", [], ["idListaEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "idListaCurso"=>$idPCurso]) as $grupo) {

                $totalAlunos = count($this->selectArray("alunos", ["idPAluno"], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "grupo.idGrupoCurso"=>$idPCurso, "grupo.grupoNumero"=>$grupo["numeroGrupo"]], ["grupo"]));
                if($i==0){
                    $numeroInicial=$totalAlunos;
                    $grupoMenor = $grupo["numeroGrupo"];
                }else{
                    if($totalAlunos<=$numeroInicial){
                        $numeroInicial = $totAlunos;
                        $grupoMenor = $grupo["numeroGrupo"];
                    }
                }

                $i++;
            }

            if($grupoMenor!=""){
                $this->inserirObjecto("alunos", "grupo", "idPGrupo", "idGrupoAluno, idGrupoEscola, idGrupoCurso, grupoNumero, idGrupoAno", [$idPAluno, $_SESSION["idEscolaLogada"], $idPCurso, $grupoMenor, $this->idAnoActual], ["idPAluno"=>$idPAluno]);
            }
        }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
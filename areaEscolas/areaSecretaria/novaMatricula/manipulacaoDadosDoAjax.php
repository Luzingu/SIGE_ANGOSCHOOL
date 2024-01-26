<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosMatricula extends manipulacaoDadosAjax{


        function __construct($caminhoAbsoluto){
            parent::__construct();

            $this->classeAluno = filter_input(INPUT_POST, "classeAluno", FILTER_SANITIZE_STRING);

            $this->idPCurso = filter_input(INPUT_POST, "idPCurso", FILTER_SANITIZE_NUMBER_INT);
             $this->areaEmExecucao = filter_input(INPUT_POST, "areaEmExecucao", FILTER_SANITIZE_STRING);
            $this->idGestLinguaEspecialidadeNovo = isset($_POST["lingEspecialidade"])?$_POST["lingEspecialidade"]:"";
            $this->idGestDisEspecialidadeNovo = isset($_POST["discEspecialidade"])?$_POST["discEspecialidade"]:"";

            $this->sobreCursoAluno = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso]);
            
            $this->classeVerificarAcesso = $this->classeAluno;
            if(count(explode("_", $this->classeVerificarAcesso))>1){
                $this->classeVerificarAcesso=valorArray($this->sobreCursoAluno, "ultimaClasse");
            }
            $this->inscreveuSeAntes ="F";
    
            if($this->accao=="editarMatricula"){


                if($this->verificacaoAcesso->verificarAcesso("", ["novaMatricula"], [$this->classeVerificarAcesso, $this->idPCurso], "") || $this->verificacaoAcesso->verificarAcesso("", ["relatorioAluno"], [$this->classeVerificarAcesso, $this->idPCurso], "")){ 
                    
                    $this->executarMatricula();                     
                }else{
                    echo "FNão tens permissão de editar dados do aluno.";
                } 

            }else if($this->accao=="salvarMatricula"){

                /*if(count(listarItensObjecto($this->sobreEscolaLogada, "trans_classes", ["idTransClAno=".$this->idAnoActual, "classeTrans=".$this->classeAluno, "idTransClCurso=".$this->idPCurso]))<0){

                    echo "FAinda não se fez transição dos alunos nesta classe.";
                }else */if($this->verificacaoAcesso->verificarAcesso("", ["novaMatricula"], [$this->classeVerificarAcesso, $this->idPCurso], "FNão tens permissão efetuar uma nova matricula.") ){
                   $this->executarMatricula();
                }
            }else if ($this->accao=="excluirMatricula"){

                if($this->verificacaoAcesso->verificarAcesso("", ["novaMatricula"], [$this->classeVerificarAcesso, $this->idPCurso], "") || $this->verificacaoAcesso->verificarAcesso("", ["relatorioAluno"], [$this->classeVerificarAcesso, $this->idPCurso], "")){
                   $this->executarMatricula();
                }else{
                    echo "FNão tens permissão de excluir uma matricula.";
                }
            }else if($this->accao=="listarMatriculados"){
                $this->listarMatriculados();
            }
        }

        public function executarMatricula(){
           
            $this->classeAluno = filter_input(INPUT_POST, "classeAluno", FILTER_SANITIZE_STRING);

            $this->idPCurso = filter_input(INPUT_POST, "idPCurso", FILTER_SANITIZE_NUMBER_INT);

            $this->totPagar = $this->valPreco = $this->preco("matricula", $this->classeAluno, $this->idPCurso);

            $this->idPMatricula = filter_input(INPUT_POST, "idPMatricula", FILTER_SANITIZE_NUMBER_INT);

            $this->idPAluno = filter_input(INPUT_POST, "idPAluno", FILTER_SANITIZE_NUMBER_INT);
            
            $this->nomeAluno = limpadorEspacosDuplicados(trim(filter_input(INPUT_POST, "nomeAluno", FILTER_SANITIZE_STRING)));

 
            $this->sexoAluno = filter_input(INPUT_POST, "sexoAluno", FILTER_SANITIZE_STRING);

            $this->art1="o";
            $this->art2="e";
            $this->art3="";
            if($this->sexoAluno=="F"){
                $this->art1 = $this->art3 = $this->art2="a";
            }
            $this->dataNascAluno = trim(filter_input(INPUT_POST, "dataNascAluno", FILTER_SANITIZE_STRING));

            $this->municipio = trim(filter_input(INPUT_POST, "municipio", FILTER_SANITIZE_STRING));
            $this->comuna = trim(filter_input(INPUT_POST, "comuna", FILTER_SANITIZE_STRING));
            $this->provincia = trim(filter_input(INPUT_POST, "provincia", FILTER_SANITIZE_STRING));
            $this->numBI = trim(filter_input(INPUT_POST, "numBI", FILTER_SANITIZE_STRING));
            $this->dataEmissaoBI = trim(filter_input(INPUT_POST, "dataEmissaoBI", FILTER_SANITIZE_STRING));
            $this->dataCaducidadeBI = trim(filter_input(INPUT_POST, "dataCaducidadeBI", FILTER_SANITIZE_STRING));

            $this->nomePai = limpadorEspacosDuplicados(trim(filter_input(INPUT_POST, "nomePai", FILTER_SANITIZE_STRING)));
            $this->nomeMae = limpadorEspacosDuplicados(trim(filter_input(INPUT_POST, "nomeMae", FILTER_SANITIZE_STRING)));
            $this->nomeEncarregado = limpadorEspacosDuplicados(trim(filter_input(INPUT_POST, "nomeEncarregado", FILTER_SANITIZE_STRING)));
            $this->numTelefone = trim(filter_input(INPUT_POST, "numTelefone", FILTER_SANITIZE_NUMBER_INT));
            $this->classeAluno = filter_input(INPUT_POST, "classeAluno", FILTER_SANITIZE_STRING);
            $this->pais = filter_input(INPUT_POST, "pais", FILTER_SANITIZE_STRING);
            $this->deficiencia = filter_input(INPUT_POST, "deficiencia", FILTER_SANITIZE_STRING);
            $this->tipoDeficiencia = filter_input(INPUT_POST, "tipoDeficiencia", FILTER_SANITIZE_STRING);
            $this->emailAluno = filter_input(INPUT_POST, "emailAluno", FILTER_SANITIZE_STRING);
            $this->acessoConta = filter_input(INPUT_POST, "acessoConta", FILTER_SANITIZE_STRING);
            $this->turma = filter_input(INPUT_POST, "turma", FILTER_SANITIZE_STRING);
            $this->periodoAluno = filter_input(INPUT_POST, "periodoAluno", FILTER_SANITIZE_STRING);
            $this->turnoAluno = filter_input(INPUT_POST, "turnoAluno", FILTER_SANITIZE_STRING);

            $this->localEmissao = filter_input(INPUT_POST, "localEmissao", FILTER_SANITIZE_STRING);
            $this->tipoDocumento = filter_input(INPUT_POST, "tipoDocumento", FILTER_SANITIZE_STRING);

            $this->numeroProcesso = filter_input(INPUT_POST, "numeroProcesso", FILTER_SANITIZE_STRING);
            $this->idMatAnexo = isset($_POST['idMatAnexo'])?$_POST['idMatAnexo']:"";
            
            $this->rpm = filter_input(INPUT_POST, "rpm", FILTER_SANITIZE_STRING);
            $this->turmaAluno = filter_input(INPUT_POST, "turmaAluno", FILTER_SANITIZE_STRING);
            $this->estadoDeDesistenciaNaEscola = filter_input(INPUT_POST, "estadoDeDesistenciaNaEscola", FILTER_SANITIZE_STRING);

            $this->tipoEntrada = filter_input(INPUT_POST, "tipoEntrada", FILTER_SANITIZE_STRING);
            $this->idPCurso = filter_input(INPUT_POST, "idPCurso", FILTER_SANITIZE_NUMBER_INT);

            if(count(explode("_", $this->classeAluno))>1){
                $this->classeAluno=120;
                $this->idMatFAno=explode("_", $this->classeAluno)[0];
            }
            $this->sobreCursoAluno = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso]);

            if($this->accao=="salvarMatricula"){
                $this->salvarMatricula();
            }else if($this->accao=="editarMatricula"){
                $this->editarMatricula();
            }else if($this->accao=="excluirMatricula"){
                $this->excluirMatricula();
            }
        }

        public function salvarMatricula($msg1="auto", $msg2="auto"){
            
            if($msg1=="auto"){
                $msg1="V".strtoupper($this->art1)." alun".$this->art1." foi matriculad".$this->art1." com sucesso.";
                $msg2="FNão foi possível matricular ".$this->art1." alun".$this->art1.".";
            }    
            $jaExistemNumero="V";
            while ($jaExistemNumero=="V"){
                $characters= "1234567890";
                $numeroUnico = substr(str_shuffle($characters),0, 4)."ANGOS2".substr(str_shuffle($characters),0, 3);
                if(count($this->selectArray("alunosmatriculados", ["numeroInterno"], ["numeroInterno"=>$numeroUnico],[], 1))<=0){
                    $jaExistemNumero="F";
                }   
            } 

            $fotoAluno="usuario_default.png";
            if(isset($_FILES['fotoAluno']) && $_FILES['fotoAluno']['size'] > 0){
              $fotoAluno = $this->upload("fotoAluno", $numeroUnico, 'fotoUsuarios', "../../../", $fotoAluno);
            }

            $arrayAlunoNoSistema=array();
            if($this->numBI!=""){
                $arrayAlunoNoSistema = $this->selectArray("alunosmatriculados", [], ["biAluno"=>$this->numBI, "numeroInterno"=>array('$ne'=>$numeroUnico)], ["escola"], 1);
            }

            $gerenciador_matriculas = listarItensObjecto($this->sobreEscolaLogada, "gerencMatricula", ["classe=".$this->classeAluno, "periodoClasse=".$this->periodoAluno, "idCurso=".$this->idPCurso]);
            
            $precoEmolumento = $this->preco("matricula", $this->classeAluno, $this->idPCurso);

            $rpm = $this->selectArray("pagamentos_matricula_inscricao", [], ["rpm"=>$this->rpm, "referenciaPagamento"=>"matricula"]);

            if($precoEmolumento>0 && valorArray($rpm, "estadoPagamento")!="I"){
                echo "FA referência do pagamento de matrícula é inválida.";
            }else if(count($arrayAlunoNoSistema)>0){
                echo "FJá há um".$this->art3." alun".$this->art1." cadastrad".$this->art1." n".$this->art1Escola." <i>".$this->selectUmElemento("escolas", "nomeEscola", ["idPEscola"=>valorArray($arrayAlunoNoSistema, "idMatEscola", "escola")])."</i> com estes dados.";

                if(valorArray($arrayAlunoNoSistema, "idMatEscola", "escola")!=$_SESSION['idEscolaLogada']){
                    echo "Vai no menu Matriculas (Adicionar Aluno) para poder adicioná-l".$this->art1." na instituição.";
                }
            }else if(seTudoMaiuscula($this->nomeAluno) || seTudoMaiuscula($this->nomeMae) || seTudoMaiuscula($this->nomePai) || seTudoMaiuscula($this->nomeEncarregado)){
                echo "FOs dados não podem ser todos em letras maiúsculas. Digite bem os dados.";
            }else if($this->idPCurso==NULL){
                echo "FDeves seleccionar um curso (opção) do aluno.";
            }else if($this->classeAluno>(9+(int)valorArray($this->sobreCursoAluno, "duracao"))){
                echo "FClasse inválida para este curso.";
            }else if(valorArray($this->sobreCursoAluno, "tipoCurso")=="geral" && $this->classeAluno>=10 && ($this->idGestDisEspecialidadeNovo==NULL || $this->idGestDisEspecialidadeNovo=="")){
                echo "FDeves seleccionar a disciplina de opção d".$this->art1." alun".$this->art1.".";
            }else if(($this->idGestLinguaEspecialidadeNovo==NULL || $this->idGestLinguaEspecialidadeNovo=="") && $this->classeAluno>=7){
                echo "FDeves seleccionar a língua de opção d".$this->art1." alun".$this->art1.".";
            }else if(!seTemValorNoArray(explode(",", valorArray($gerenciador_matriculas, "idsLinguasEtrang")), $this->idGestLinguaEspecialidadeNovo) && $this->classeAluno>=7){
                echo "FSelecciones outra língua de opção.";
                
            }else if(!seTemValorNoArray(explode(",", valorArray($gerenciador_matriculas, "idsDisciplOpcao")), $this->idGestDisEspecialidadeNovo) && valorArray($this->sobreCursoAluno, "tipoCurso")=="geral" && $this->classeAluno>=10){
                echo "FSelecciones outra disciplina de opção.";
                
            }else if($this->classeAluno>=10 && ($this->idPCurso==NULL || $this->idPCurso=="")){
                echo "FDeves seleccionar o curso(opção) d".$this->art1." alun".$this->art1."."; 
            }else{
                $agrupador = $this->selectArray("agrup_alunos", ["idPGrupo"]);
                $grupo = count($agrupador)-1;

                $idMatriculaPorDefeito="";
                if(valorArray($this->selectArray("alunos_".$grupo, ["soma"], [], [], "", ["soma"=>['$sum'=>1]]), "soma")>=15000){

                    //Criando uma nova colecção modelo...
                    $ultimoRegisto=$this->selectArray("alunos_".$grupo, ["idPMatricula"], ["idPMatricula"=>array('$ne'=>"modelo")], [], 1, [], ["idPMatricula"=>-1]);
                    $idMatriculaPorDefeito=valorArray($ultimoRegisto, "idPMatricula");
                    $grupo++;
                    $this->inserir("agrup_alunos", "idPGrupo", "grupo", [$grupo]);
                }
                if($this->inserir("alunos_".$grupo, "idPMatricula", "nomeAluno, sexoAluno, dataNascAluno, comunaNascAluno, municNascAluno, provNascAluno, paisNascAluno, paiAluno, maeAluno, biAluno, dataEBIAluno, encarregadoEducacao, telefoneAluno, numeroInterno, fotoAluno, estadoActividade, emailAluno, estadoAcessoAluno, deficienciaAluno, tipoDeficienciaAluno, senhaAluno, dataCaducidadeBI, tipoDocumento, localEmissao, grupo",  [$this->nomeAluno, $this->sexoAluno, $this->dataNascAluno, $this->comuna, $this->municipio, $this->provincia, $this->pais, $this->nomePai, $this->nomeMae, $this->numBI, $this->dataEmissaoBI, $this->nomeEncarregado, $this->numTelefone, $numeroUnico, $fotoAluno, "A", $this->emailAluno, "A", $this->deficiencia, $this->tipoDeficiencia, "0c7".criptografarMd5("0000")."ab", $this->dataCaducidadeBI,$this->tipoDocumento, $this->localEmissao, $grupo], "sim", "nao", [], $idMatriculaPorDefeito)=="sim"){

                    $this->conDb("inscricao");
                    $this->editarItemObjecto("alunos", "inscricao", "estadoMatricula", ["V"], ["idPAluno"=>$this->idPAluno], []);
                    $this->conDb();

                     $this->idPMatricula = $this->selectUmElemento("alunos_".$grupo, "idPMatricula", ["numeroInterno"=>$numeroUnico]);

                     if($this->numeroProcesso==NULL || $this->numeroProcesso==""){
                        $expl = explode("ANGOS", $numeroUnico);
                        $this->numeroProcesso = $expl[0].$expl[1];
                     }

                    $this->inserirObjecto("alunos_".$grupo, "escola", "idPAlEscola", "idFMatricula, idMatAno, idMatEscola, idMatEntidade, estadoAluno, dataMatricula, horaMatricula, periodoAluno, numeroProcesso, inscreveuSeAntes, idTabelaInscricao, idMatAnexo, idMatCurso, classeActualAluno, estadoDeDesistenciaNaEscola, turnoAluno, rpm", [$this->idPMatricula, $this->idAnoActual, $_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], "A", $this->dataSistema, $this->tempoSistema, $this->periodoAluno, $this->numeroProcesso, $this->inscreveuSeAntes, $this->idPAluno, $this->idMatAnexo, $this->idPCurso, "".$this->classeAluno."", $this->estadoDeDesistenciaNaEscola, $this->turnoAluno, $this->rpm], ["idPMatricula"=>$this->idPMatricula]);
                    $this->tratarArrayDeCursos($this->idPMatricula, $this->idPCurso);

                    $this->inserirObjecto("alunos_".$grupo, "reconfirmacoes", "idPReconf", "idReconfMatricula, dataReconf, horaReconf, idMatCurso, classeReconfirmacao, tipoEntrada, chaveReconf, idReconfProfessor, idReconfAno, estadoReconfirmacao, idReconfEscola, nomeTurma, designacaoTurma", [$this->idPMatricula, $this->dataSistema, $this->tempoSistema, $this->idPCurso, $this->classeAluno, $this->tipoEntrada, $this->idPMatricula."-".$this->idPCurso."-".$this->idAnoActual."-".$_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], $this->idAnoActual, "A", $_SESSION["idEscolaLogada"], "", ""], ["idPMatricula"=>$this->idPMatricula]);

                    $this->seleccionadorEspecialidades($this->idPCurso, $this->classeAluno, $this->idGestLinguaEspecialidadeNovo, $this->idGestDisEspecialidadeNovo, $this->idPMatricula, $grupo);

                     if(isset($_POST["turmaAluno"])){

                        if(count($this->selectCondClasseCurso("array", "listaturmas", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "classe"=>$this->classeAluno, "nomeTurma"=>$this->turmaAluno, "idListaAno"=>$this->idAnoActual], $this->classeAluno, ["idPNomeCurso"=>$this->idPCurso]))<=0){
        
                            $this->inserir("listaturmas", "idPListaTurma", "nomeTurma, designacaoTurma, classe, idPEscola, idListaAno, periodoTurma, idAnexoTurma, idPNomeCurso, periodoT", [$this->turmaAluno, $this->turmaAluno, "".$this->classeAluno."", $_SESSION["idEscolaLogada"], $this->idAnoActual, $this->periodoAluno, $this->idMatAnexo, $this->idPCurso, $this->turnoAluno]);
                        }
                        $this->editarItemObjecto("alunos_".$grupo, "reconfirmacoes", "nomeTurma, designacaoTurma", [$this->turmaAluno, $this->turmaAluno], ["idPMatricula"=>$this->idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>$this->idPCurso]);

                        $this->actuazalizarReconfirmacaAluno($this->idPMatricula);
                    }else{
                        $this->atualizarTurma($this->idPMatricula, $this->classeAluno, $this->idPCurso, $this->turmaAluno, $grupo);
                    }
                    $this->gravarPautasAluno($this->idPMatricula);

                    if($precoEmolumento>0){
                        $this->editar("pagamentos_matricula_inscricao", "estadoPagamento, nomeAluno, biAluno, numeroInterno", ["A", $this->nomeAluno, $this->numBI, $numeroUnico], ["rpm"=>$this->rpm]);

                        $this->inserirObjecto("alunos_".$grupo, "pagamentos", "idPHistoricoConta", "idHistoricoEscola, idHistoricoAno, dataPagamento, horaPagamento, idHistoricoMatricula, idHistoricoFuncionario, nomeFuncionario, idTipoEmolumento, codigoEmolumento, designacaoEmolumento, referenciaPagamento, precoInicial, precoMulta , precoDesconto, precoPago, estadoPagamento, contaUsada", [$_SESSION['idEscolaLogada'], $this->idAnoActual, $this->dataSistema, $this->tempoSistema,  $this->idPMatricula, $_SESSION['idUsuarioLogado'], valorArray($this->sobreUsuarioLogado, "nomeEntidade"), "", "matricula", "Matricula", $this->classeAluno, valorArray($rpm, "valorPago"), 0, 0, valorArray($rpm, "valorPago"), "A", valorArray($rpm, "contaUsar")], ["idPMatricula"=>$this->idPMatricula]);
                    }
                    echo $msg1;
                }else{
                    echo $msg2;
                }
            }
        }
        private function editarMatricula(){
           echo $this->editarDadosAluno($this->idPMatricula, "../../../");
        }


        private function excluirMatricula (){
            

            $idPEscola = $_SESSION['idEscolaLogada'];
            $this->sobreAluno($this->idPMatricula);

            $this->idTabelaInscricao = valorArray($this->sobreAluno, "idTabelaInscricao", "escola");
            
            if(count(listarItensObjecto($this->sobreAluno, "pautas", ["mf>=7"]))>0 || count(listarItensObjecto($this->sobreAluno, "escola", []))>1){

                $this->excluirItemObjecto("alunos_".$this->grupoAluno, "reconfirmacoes", ["idPMatricula"=>$this->idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada']]);

                $this->actuazalizarReconfirmacaAluno($this->idPMatricula);
                $this->editarItemObjecto("alunos_".$this->grupoAluno, "escola", "estadoAluno", ["F"], ["idPMatricula"=>$this->idPMatricula], ["idMatEscola"=>$_SESSION["idEscolaLogada"]]);

                $this->editar("pagamentos_matricula_inscricao", "estadoPagamento, nomeAluno, biAluno, numeroInterno", ["I", "", "", ""], ["rpm"=>valorArray($this->sobreAluno, "rpm", "escola")]);

                echo "V".strtoupper($this->art1)." alun".$this->art1." foi excluid".$this->art1." com sucesso.";
            }else{
                if($this->editar("alunos_".$this->grupoAluno, "nomeAluno, sexoAluno, dataNascAluno, comunaNascAluno, municNascAluno, provNascAluno, paisNascAluno, paiAluno, maeAluno, biAluno, dataEBIAluno, encarregadoEducacao, telefoneAluno, fotoAluno, estadoActividade, emailAluno, estadoAcessoAluno, deficienciaAluno, tipoDeficienciaAluno, senhaAluno, dataCaducidadeBI, tipoDocumento, localEmissao, reconfirmacoes, avaliacao_anual, turmas, notas_finais, pautas, pagamentos, dadosatraso, alteracoes_notas, cadeiras_atraso, online, escola", [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null, null, null, null, null, null, null, null, null, null, null, null], ["idPMatricula"=>$this->idPMatricula])=="sim"){

                    $this->editar("alunos_".$this->grupoAluno, "idMatCurso_".$idPEscola.",classe_".$idPEscola.",turma_".$idPEscola, ["---", "---", "---"], ["idPMatricula"=>$this->idPMatricula]);
                    $this->excluirItemObjecto("alunos_".$this->grupoAluno, "reconfirmacoes", ["idPMatricula"=>$this->idPMatricula], []);
                    $this->excluirItemObjecto("alunos_".$this->grupoAluno, "pagamentos", ["idPMatricula"=>$this->idPMatricula], []);
                    $this->excluirItemObjecto("alunos_".$this->grupoAluno, "pautas", ["idPMatricula"=>$this->idPMatricula], []);
                    $this->excluirItemObjecto("alunos_".$this->grupoAluno, "arquivo_pautas", ["idPMatricula"=>$this->idPMatricula], []);
                    $this->actuazalizarReconfirmacaAluno($this->idPMatricula);
                    $this->editar("pagamentos_matricula_inscricao", "estadoPagamento, nomeAluno, biAluno, numeroInterno", ["I", "", "", ""], ["rpm"=>valorArray($this->sobreAluno, "rpm", "escola")]);
                    
                    $this->conDb("inscricao");
                    //Marcando como matriculado, caso o aluno tinha feito inscrição...
                     $this->editarItemObjecto("alunos", "inscricao", "estadoMatricula", ["V"], ["idPAluno"=>$this->idTabelaInscricao], []);
                     $this->conDb();

                    echo "V".strtoupper($this->art1)." alun".$this->art1." foi excluid".$this->art1." com sucesso.";
                }else{
                    echo "FNão foi possível excluir ".$this->art1." d".$this->art1." alun".$this->art1.".";
                }
            }
        }
        
        public function listarMatriculados(){
            $todos = $_GET["todos"];
            $dataMatricula = isset($_GET["dataMatricula"])?$_GET["dataMatricula"]:"";
            $idEscola=$_SESSION['idEscolaLogada'];
            if($todos=="nao"){

                $array = $this->selectArray("alunosmatriculados", ["nomeAluno", "numeroInterno", "sexoAluno", "dataNascAluno", "biAluno", "dataEBIAluno", "paiAluno", "fotoAluno", "maeAluno", "encarregadoEducacao", "telefoneAluno", "emailAluno", "dataCaducidadeBI", "estadoAcessoAluno", "tipoDocumento", "localEmissao", "numeroProcesso", "deficienciaAluno", "deficienciaAluno", "tipoDeficienciaAluno", "paisNascAluno", "provNascAluno", "municNascAluno", "comunaNascAluno", "idPMatricula", "escola.estadoDeDesistenciaNaEscola","escola.idMatAnexo","escola.idGestLinguaEspecialidade","escola.idGestDisEspecialidade","escola.periodoAluno","escola.numeroProcesso","escola.idMatCurso","escola.classeActualAluno", "escola.turnoAluno", "reconfirmacoes.tipoEntrada"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatAno"=>$this->idAnoActual, "escola.idMatEntidade"=>$_SESSION['idUsuarioLogado'], "escola.estadoAluno"=>"A", "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual], ["escola", "reconfirmacoes"], "", [], ["idPMatricula"=>-1]);
                $novoArray = $this->anexarTabela2($array, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
                
                echo json_encode($novoArray); 

            }else if($todos=="sim"){

                $classe = isset($_GET["classe"])?$_GET["classe"]:"";
                $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
                $periodo = isset($_GET["periodo"])?$_GET["periodo"]:"";

                $condicao =["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.periodoAluno"=>$periodo];
                $expl = explode("_", $classe);
                  
                if(count($expl)>1){
                    $estadoAluno="Y";
                    $idAnoF = $expl[1];
                    $condicao["escola.idMatFAno"]=$idAnoF;
                    $condicao["escola.idMatCurso"] =$idPCurso;
                    $condicao["escola.classeActualAluno"]=120;
                }else{
                    $condicao["escola.estadoAluno"]="A";
                    $condicao["escola.classeActualAluno"]=$classe;
                    $condicao["escola.idMatCurso"] =$idPCurso;
                } 
                $array = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "telefoneAluno", "estadoAcessoAluno", "sexoAluno"], $condicao, ["escola"], "", [], ["nomeAluno"=>1]);
                echo json_encode($array);
            }
       }
    }
    new manipulacaoDadosMatricula(__DIR__);
?>
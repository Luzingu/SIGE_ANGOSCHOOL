<?php 
  if(session_status()!==PHP_SESSION_ACTIVE){
      session_start(); 
    }
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
      
      function __construct($caminhoAbsoluto){
        parent::__construct();
          $this->classeAluno = isset($_POST["classeAluno"])?$_POST["classeAluno"]:"";

            $this->idPCurso = filter_input(INPUT_POST, "idPCurso", FILTER_SANITIZE_STRING);
 
            $this->idPMatricula = filter_input(INPUT_POST, "idPMatricula", FILTER_SANITIZE_NUMBER_INT);

            $this->idPInscricao = filter_input(INPUT_POST, "idPInscricao", FILTER_SANITIZE_NUMBER_INT);
            
            $this->nomeAluno = trim(filter_input(INPUT_POST, "nomeAluno", FILTER_SANITIZE_STRING));
 
            $this->sexoAluno = filter_input(INPUT_POST, "sexoAluno", FILTER_SANITIZE_STRING);
            $this->art1="o";
            $this->art2="e";
            $this->art3="";
            if($this->sexoAluno=="F"){
                $this->art1 = $this->art3 = $this->art2="a";
            } 
            $this->dataNascAluno = trim(filter_input(INPUT_POST, "dataNascAluno", FILTER_SANITIZE_STRING));

            $this->municipio = trim(filter_input(INPUT_POST, "municipio", FILTER_SANITIZE_STRING));
            $this->provincia = trim(filter_input(INPUT_POST, "provincia", FILTER_SANITIZE_STRING));
            $this->comuna = trim(filter_input(INPUT_POST, "comuna", FILTER_SANITIZE_STRING));
            $this->numBI = trim(filter_input(INPUT_POST, "numBI", FILTER_SANITIZE_STRING));
            $this->dataEmissaoBI = trim(filter_input(INPUT_POST, "dataEmissaoBI", FILTER_SANITIZE_STRING));

            $this->nomePai = trim(filter_input(INPUT_POST, "nomePai", FILTER_SANITIZE_STRING));
            $this->nomeMae = trim(filter_input(INPUT_POST, "nomeMae", FILTER_SANITIZE_STRING));
            $this->nomeEncarregado = trim(filter_input(INPUT_POST, "nomeEncarregado", FILTER_SANITIZE_STRING));
            $this->numTelefone = trim(filter_input(INPUT_POST, "numTelefone", FILTER_SANITIZE_NUMBER_INT));
            $this->classeAluno = filter_input(INPUT_POST, "classeAluno", FILTER_SANITIZE_STRING);
            $this->pais = filter_input(INPUT_POST, "pais", FILTER_SANITIZE_STRING);
            $this->deficiencia = filter_input(INPUT_POST, "deficiencia", FILTER_SANITIZE_STRING);
            $this->tipoDeficiencia = filter_input(INPUT_POST, "tipoDeficiencia", FILTER_SANITIZE_STRING);
            $this->emailAluno = filter_input(INPUT_POST, "emailAluno", FILTER_SANITIZE_STRING);
            $this->acessoConta = "I";
            $this->turma = filter_input(INPUT_POST, "turma", FILTER_SANITIZE_STRING);
            $this->periodoAluno = filter_input(INPUT_POST, "periodoAluno", FILTER_SANITIZE_STRING);
            $this->turnoAluno = filter_input(INPUT_POST, "turnoAluno", FILTER_SANITIZE_STRING);

            $this->tipoDocumento = filter_input(INPUT_POST, "tipoDocumento", FILTER_SANITIZE_STRING);
            $this->localEmissao = filter_input(INPUT_POST, "localEmissao", FILTER_SANITIZE_STRING);

            $this->numeroProcesso = filter_input(INPUT_POST, "numeroProcesso", FILTER_SANITIZE_STRING);
            $this->idMatAnexo = filter_input(INPUT_POST, "idMatAnexo", FILTER_SANITIZE_NUMBER_INT);

            $this->idGestLinguaEspecialidadeNovo = isset($_POST["lingEspecialidade"])?$_POST["lingEspecialidade"]:"";
            $this->idGestDisEspecialidadeNovo = isset($_POST["discEspecialidade"])?$_POST["discEspecialidade"]:"";
            $this->sobreCursoAluno = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso]);
            
            $this->idMatFAno=NULL;
            $this->classeVerificarAcesso=NULL;
            $expl = explode("_", $this->classeAluno);

            if(count($expl)>=2){
                $this->classeVerificarAcesso=valorArray($this->sobreCursoAluno, "ultimaClasse");
                $this->classeAluno=120;
                $this->idMatFAno=$expl[1];
            }            

            if($this->accao=="editarRegistro"){
                if( $this->verificacaoAcesso->verificarAcesso("", ["backupNovoRegistro"], [$this->classeVerificarAcesso, $this->idPCurso], "")){
                  $this->editarRegistro();
                }

            }else if($this->accao=="salvarRegistro"){
                if( $this->verificacaoAcesso->verificarAcesso("", ["backupNovoRegistro"], [$this->classeVerificarAcesso, $this->idPCurso], "FNão tens permissão de efectuar um novo registro nesta classe.")){

                  $this->salvarRegistro();
                }
            }else if ($this->accao=="excluirRegistro"){

                if( $this->verificacaoAcesso->verificarAcesso("", ["backupNovoRegistro"], [$this->classeVerificarAcesso, $this->idPCurso], "")){
                    
                    $this->excluirRegistro();
                }else{
                    echo "FNão tens permissão de excluir registro dum aluno.";
                }
            }else if($this->accao=="listarRegistrados"){
                $this->listarRegistrados();
            }
      }

      private function salvarRegistro(){

            $jaExistemNumero="V";
            while ($jaExistemNumero=="V"){
                $characters= "1234567890";
                  $numeroUnico = substr(str_shuffle($characters),0, 4)."ANGOS2".substr(str_shuffle($characters),0, 3);

                if(count($this->selectArray("alunosmatriculados", ["numeroInterno"], ["numeroInterno"=>$numeroUnico], [], 1))<=0){
                    $jaExistemNumero="F";
                }       
            }

            $arrayAlunoNoSistema=array();
            if($this->numBI!=""){
                $arrayAlunoNoSistema = $this->selectArray("alunosmatriculados", ["nomeAluno"], ["biAluno"=>$this->numBI, "numeroInterno"=>array('$ne'=>$numeroUnico)], ["escola"], 1);

                $arrayAlunoNoSistema = $this->anexarTabela2($arrayAlunoNoSistema, "escolas", "escola", "idPEscola", "idMatEscola");
            }

            $gerenciador_matriculas = listarItensObjecto($this->sobreEscolaLogada, "gerencMatricula", ["classe=".$this->classeVerificarAcesso, "periodoClasse=".$this->periodoAluno, "idCurso=".$this->idPCurso]);

            $idEscola=$_SESSION['idEscolaLogada'];

            if(count($arrayAlunoNoSistema)>0){
                echo "FJá há um".$this->art3." alun".$this->art1." cadastrad".$this->art1." n".$this->art1Escola." <i>".valorArray($arrayAlunoNoSistema, "nomeEscola")."</i> com estes dados.";
                if(valorArray($arrayAlunoNoSistema, "idPEscola")!=$_SESSION['idEscolaLogada']){
                    echo "Vai no menu Matriculas (Adicionar Aluno) para poder adicioná-l".$this->art1." na instituição.";
                }
            }else if(seTudoMaiuscula($this->nomeAluno) || seTudoMaiuscula($this->nomeMae) || seTudoMaiuscula($this->nomePai) || seTudoMaiuscula($this->nomeEncarregado)){
                echo "FOs dados não podem ser todos em letras maiúsculas. Digite bem os dados.";
            }else if(valorArray($this->sobreCursoAluno, "tipoCurso")=="geral" && ($this->idGestDisEspecialidadeNovo==NULL || $this->idGestDisEspecialidadeNovo=="")){
                echo "FDeves seleccionar a disciplina de opção d".$this->art1." alun".$this->art1.".";
            }else if(($this->idGestLinguaEspecialidadeNovo==NULL || $this->idGestLinguaEspecialidadeNovo=="") && $this->classeVerificarAcesso>=7){
                echo "FDeves seleccionar a língua de opção d".$this->art1." alun".$this->art1.".";
            }else if(!seTemValorNoArray(explode(",", valorArray($gerenciador_matriculas, "idsLinguasEtrang")), $this->idGestLinguaEspecialidadeNovo) && $this->classeVerificarAcesso>=7){
                echo "FSelecciones outra língua de opção.";
                
            }else if(!seTemValorNoArray(explode(",", valorArray($gerenciador_matriculas, "idsDisciplOpcao")), $this->idGestDisEspecialidadeNovo) && valorArray($this->sobreCursoAluno, "tipoCurso")=="geral"){
                echo "FSelecciones outra disciplina de opção.";
                
            }else{
                $agrupador = $this->selectArray("agrup_alunos");
                $grupo = count($agrupador)-1;

                $idMatriculaPorDefeito="";

                if(valorArray($this->selectArray("alunos_".$grupo, ["soma"], [], [], "", ["soma"=>['$sum'=>1]]), "soma")>=15000){

                    //Criando uma nova colecção modelo...
                    $ultimoRegisto=$this->selectArray("alunos_".$grupo, ["idPMatricula"], ["idPMatricula"=>array('$ne'=>"modelo")], [], 1, [], ["idPMatricula"=>-1]);
                    $idMatriculaPorDefeito=valorArray($ultimoRegisto, "idPMatricula");
                    $grupo++;
                    $this->inserir("agrup_alunos", "idPGrupo", "grupo", [$grupo]);
                }
                
                if($this->inserir("alunos_".$grupo, "idPMatricula", "nomeAluno, sexoAluno, dataNascAluno, comunaNascAluno, municNascAluno, provNascAluno, paisNascAluno, paiAluno, maeAluno, tipoDocumento, localEmissao, biAluno, dataEBIAluno, encarregadoEducacao, telefoneAluno, numeroInterno, fotoAluno, estadoActividade, emailAluno, estadoAcessoAluno, deficienciaAluno, tipoDeficienciaAluno, senhaAluno, grupo",  [$this->nomeAluno, $this->sexoAluno, $this->dataNascAluno, $this->comuna, $this->municipio, $this->provincia, $this->pais, $this->nomePai, $this->nomeMae, $this->tipoDocumento, $this->localEmissao, $this->numBI, $this->dataEmissaoBI, $this->nomeEncarregado, $this->numTelefone, $numeroUnico, "usuario_default.png", "A", $this->emailAluno, "I", $this->deficiencia, $this->tipoDeficiencia,  "0c7".criptografarMd5("0000")."ab", $grupo], "sim", "nao", [], $idMatriculaPorDefeito)=="sim"){

                    $this->idPMatricula = $this->selectUmElemento("alunos_".$grupo, "idPMatricula", ["numeroInterno"=>$numeroUnico]);

                    if($this->numeroProcesso==NULL || $this->numeroProcesso==""){
                        $expl = explode("ANGOS", $numeroUnico);
                        $this->numeroProcesso = $expl[0].$expl[1];
                    } 
 
                    $this->inserirObjecto("alunos_".$grupo, "escola", "idPAlEscola", "idFMatricula, idMatAno, idMatFAno, idMatEscola, idMatEntidade, estadoAluno, dataMatricula, horaMatricula, periodoAluno, turnoAluno, numeroProcesso, inscreveuSeAntes, idTabelaInscricao, idMatCurso, classeActualAluno, idMatAnexo", [$this->idPMatricula, $this->idAnoActual, $this->idMatFAno, $_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], "Y", $this->dataSistema, $this->tempoSistema, $this->periodoAluno, $this->turnoAluno, $this->numeroProcesso, "Y", null, $this->idPCurso, $this->classeAluno, $this->idMatAnexo], ["idPMatricula"=>$this->idPMatricula]);

                    $this->seleccionadorEspecialidades($this->idPCurso, "", $this->idGestLinguaEspecialidadeNovo, $this->idGestDisEspecialidadeNovo, $this->idPMatricula, $grupo);

                    $this->tratarArrayDeCursos($this->idPMatricula, $this->idPCurso);
                    echo "VO registro foi salvo com sucesso.";
                }else{
                    echo "FNão foi possível salvas o registro.";
                }
            }
        }


        private function editarRegistro(){
            echo $this->editarDadosAluno($this->idPMatricula, "../../../");
        }

        private function excluirRegistro (){
            $idPEscola = $_SESSION['idEscolaLogada'];
            $this->sobreAluno($this->idPMatricula);

            if(count(listarItensObjecto($this->sobreAluno, "pautas", ["mf>=7"]))>0 || count(listarItensObjecto($this->sobreAluno, "escola", ["idMatEscola!=".$_SESSION['idEscolaLogada']]))>0){

                $this->editarItemObjecto("alunos_".$this->grupoAluno, "escola", "estadoAluno", ["F"], ["idPMatricula"=>$this->idPMatricula], ["idMatEscola"=>$_SESSION["idEscolaLogada"]]);

                $this->tratarArrayDeCursos($this->idPMatricula);

                echo "V".strtoupper($this->art1)." alun".$this->art1." foi excluid".$this->art1." com sucesso.";
            }else{

                if($this->editar("alunos_".$this->grupoAluno, "nomeAluno, sexoAluno, dataNascAluno, comunaNascAluno, municNascAluno, provNascAluno, paisNascAluno, paiAluno, maeAluno, biAluno, dataEBIAluno, encarregadoEducacao, telefoneAluno, fotoAluno, estadoActividade, emailAluno, estadoAcessoAluno, deficienciaAluno, tipoDeficienciaAluno, senhaAluno, dataCaducidadeBI, tipoDocumento, localEmissao, reconfirmacoes, pautas, pagamentos, dadosatraso, alteracoes_notas, cadeiras_atraso, online, escola", [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,null, null, null, null, null, null, null, null, null, null, null, null], ["idPMatricula"=>$this->idPMatricula])=="sim"){
                    echo "V".strtoupper($this->art1)." alun".$this->art1." foi excluid".$this->art1." com sucesso.";
                }else{
                    echo "FNão foi possível excluir ".$this->art1." d".$this->art1." alun".$this->art1.".";
                }
            }
            
        }

        private function listarRegistrados(){
            $luzinguLuame = $this->selectArray("alunosmatriculados", [], ["escola.idMatAno"=>$this->idAnoActual, "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEntidade"=>$_SESSION['idUsuarioLogado'], "escola.estadoAluno"=>"Y", "escola.inscreveuSeAntes"=>"Y"], ["escola"], "", [], ["idPMatricula"=>-1]);
            $luzinguLuame = $this->anexarTabela($luzinguLuame, "nomecursos", "idPNomeCurso", "idMatCurso");
            echo json_encode($luzinguLuame);
            
       }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
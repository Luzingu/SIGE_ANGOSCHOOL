<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
        session_cache_expire(60);
      session_start();
    }
    include_once ($_SERVER["DOCUMENT_ROOT"].'/angoschool/funcoesAuxiliares.php');
    include_once ($_SERVER["DOCUMENT_ROOT"].'/angoschool/verificadorAcesso.php');
    include_once ($_SERVER["DOCUMENT_ROOT"].'/angoschool/msgPagamentoDoSistema.php');

    include_once ($_SERVER["DOCUMENT_ROOT"].'/angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
    include_once ($_SERVER["DOCUMENT_ROOT"].'/angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');

    class manipulacaoDadosAjaxMae extends manipulacaoDados{
        public $accao="";
        public $verificacaoAcesso="";

        function __construct(){
            parent::__construct();
            if(isset($_GET["tipoAcesso"])){
                $this->accao = $_GET["tipoAcesso"];
            }else if(filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING)!=null){
                $this->accao = filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING );
            }
            $this->verificacaoAcesso = new verificacaoAcesso();
            
            $this->executar();
        }

        public function executar (){
            if(isset($_SESSION["tipoUsuario"]) && $_SESSION["tipoUsuario"]=="aluno"){ 
                $this->nomeUsuario = valorArray($this->sobreUsuarioLogado, "nomeAluno");
            }else{
                $this->nomeUsuario = valorArray($this->sobreUsuarioLogado, "nomeEntidade");
            }
            if($this->hora<12){
                $this->saudacao="Bom dia ";
            }else if($this->hora<18){
                $this->saudacao="Boa tarde ";
            }else{
                $this->saudacao="Boa noite ";
            }

            if($this->accao=="trocarSenha"){
                $this->trocarSenha();
            }else if($this->accao=="retornarHora"){
                $this->retornarHora();
            }else if($this->accao=="terminarSessao"){
                $this->terminarSessao();
            }else if($this->accao=="verificarSeASessaoEValida"){
                $this->verificarSeASessaoEValida();
            }else if($this->accao=="verficarPrazoEscola"){
                verificacaoPrazoPagamentoInstituicoes($this, $this->saudacao, $this->nomeUsuario);
            }else if($this->accao=="editarPerfilEntidade"){
                include_once $_SERVER["DOCUMENT_ROOT"].'/angoschool/arquivosComunsEntreAreas/listaAgentes.php';
                $manipuladorDadosAgente = new manipuladorDadosAgente($this, "../../");
                $manipuladorDadosAgente->editarAgente();
            }else if($this->accao=="pegarProvMunicComunDoPais"){
                $this->pegarProvMunicComunDoPais();
            }
        }

        private function trocarSenha(){
            $antigaSenha = filter_input(INPUT_POST, "antigaSenha", FILTER_SANITIZE_STRING);
            $novaSenha = filter_input(INPUT_POST, "novaSenha", FILTER_SANITIZE_STRING);
            $confirmarSenha = filter_input(INPUT_POST, "confirmarSenha", FILTER_SANITIZE_STRING);

            if($_SESSION["tipoUsuario"]=="aluno"){
                $passeDb = $this->selectUmElemento("alunosmatriculados", "senhaAluno",["idPMatricula"=>$_SESSION["idUsuarioLogado"]]);
            }else{
                $passeDb = $this->selectUmElemento("entidadesprimaria", "senhaEntidade",["idPEntidade"=>$_SESSION["idUsuarioLogado"]]);
            }

            if(atenticarMd5($passeDb, $antigaSenha)=="nao"){
                echo "XA antiga senha está incorrecta.";
            }else{
                if($novaSenha!=$confirmarSenha){ 
                    echo "PA antiga e a nova senha devem ser iguais.";
                }else if(strlen($novaSenha)<8){
                    echo "PA nova senha deve ter no mínimo 8 caracteres.";
                }else{
                    if($_SESSION["tipoUsuario"]=="aluno"){
                        $this->editar("alunosmatriculados", "senhaAluno", ["0c7".criptografarMd5($novaSenha)."ab"], ["idPMatricula"=>$_SESSION["idUsuarioLogado"]], "VA Senha foi alterada com sucesso.", "FNão foi possível alterar a senha.");
                    }else{
                        $this->editar("entidadesprimaria", "senhaEntidade", ["0c7".criptografarMd5($novaSenha)."ab"], ["idPEntidade"=>$_SESSION["idUsuarioLogado"]], "VA Senha foi alterada com sucesso.", "FNão foi possível alterar a senha.");
                    }
                }        
            }
        }

        function pegarProvMunicComunDoPais(){
            $idPPais = isset($_GET["idPPais"])?$_GET["idPPais"]:"";

            $resutados[0] = $this->selectArray("div_terit_provincias", [], ["idProvPais"=>$idPPais], [], "", [], ["nomeProvincia"=>1]);
            $resutados[1] = $this->selectArray("div_terit_municipios", [], [], [], "", [], ["nomeMunicipio"=>1]);
            $resutados[2] = $this->selectArray("div_terit_comunas", [], [], [], "", [], ["nomeComuna"=>1]);
            echo json_encode($resutados);
        }                
    }

?>
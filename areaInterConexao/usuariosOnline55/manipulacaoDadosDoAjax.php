<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/manipulacaoDadosDoAjax.php';
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
                 
    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            
	        $this->caminhoRetornar = $caminhoRecuar = retornarCaminhoRecuarArquivosPhp(__DIR__);
            if($this->accao=="expulsarUsuario"){
                if($this->verificacaoAcesso->verificarAcessoAlteracao("CEO", "", "", "FSomente CEO Tem Permissão Expulsar um Usuário!")){

                    $this->expulsarUsuario();
                }
            }            
    	}  

        function listar(){

            $dataSaida = strtotime($this->dataSistema.$this->tempoSistema." - 1200 seconds");

            $condicao = ["estadoExpulsao"=>"A", "dataSaida"=>date("Y-m-d", $dataSaida), "horaSaida"=>array('$gt'=>date("H:i:s", $dataSaida))];

            if($_SESSION["tipoUsuario"]=="aluno"){
              $condicao["idOnlineMat"]=array('$ne'=>(int)$_SESSION['idUsuarioLogado']);
            }else{
              $condicao["idOnlineEnt"]=array('$ne'=>(int)$_SESSION['idUsuarioLogado']);
            }

            $entidades = $this->selectArray("entidadesonline", $condicao);
            $entidades = $this->anexarTabela($entidades, "alunosmatriculados", "idPMatricula", "idOnlineMat");
            $entidades = $this->anexarTabela($entidades, "entidadesprimaria", "idPEntidade", "idOnlineEnt");
            $entidades = $this->anexarTabela($entidades, "escolas", "idPEscola", "idOnlineEntEscola");

            echo json_encode($entidades);

        }

        function expulsarUsuario(){
            $idPOnline = $_GET["idPOnline"];
            $this->editar("entidadesonline", "estadoExpulsao", "I", ["idPOnline"=>$idPOnline]);
            $this->listar();
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>




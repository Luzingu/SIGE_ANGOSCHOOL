<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
        if($this->accao=="enviarMensagem"){
          $this->enviarMensagem();
        }       
    	}

      function enviarMensagem(){
        $mensagem = isset($_POST["mensagem"])?$_POST["mensagem"]:"";
        $idPUsuario = isset($_POST["idPUsuario"])?$_POST["idPUsuario"]:"";
        $tipoUsuario = isset($_POST["tipoUsuario"])?$_POST["tipoUsuario"]:"";
        $idUsuarioLogado = $_SESSION["idUsuarioLogado"];

        if($_SESSION['tbUsuario']=="entidade" || $tipoUsuario=="entidade"){

          if($_SESSION['tbUsuario']=="entidade"){
            $chaida = $this->selectArray("entidadesprimaria", ["nomeEntidade", "fotoEntidade"], ["idPEntidade"=>$_SESSION["idUsuarioLogado"]]);
            $nomeEmissor = valorArray($chaida, "nomeEntidade");
            $fotoEmissor = valorArray($chaida, "fotoEntidade");
          }

          if($tipoUsuario=="entidade"){
            $chaida = $this->selectArray("entidadesprimaria", ["nomeEntidade", "fotoEntidade"], ["idPEntidade"=>$idPUsuario]);
            $nomeReceptor = valorArray($chaida, "nomeEntidade");
            $fotoReceptor = valorArray($chaida, "fotoEntidade");
          }
        }

        if($_SESSION['tbUsuario']=="aluno" || $tipoUsuario=="aluno"){

          if($_SESSION['tbUsuario']=="aluno"){
            $chaida = $this->selectArray("alunosmatriculados", ["nomeAluno", "fotoAluno"], ["idPEntidade"=>$_SESSION["idUsuarioLogado"]]);
            $nomeEmissor = valorArray($chaida, "nomeAluno");
            $fotoEmissor = valorArray($chaida, "fotoAluno");
          }

          if($tipoUsuario=="aluno"){
            $chaida = $this->selectArray("alunosmatriculados", ["nomeAluno", "fotoAluno"], ["idPEntidade"=>$idPUsuario]);
            $nomeReceptor = valorArray($chaida, "nomeAluno");
            $fotoReceptor = valorArray($chaida, "fotoAluno");
          }
        }
        
        $this->inserir("mensagens", "idPMensagem", "idReceptor, receptor, nomeReceptor, fotoReceptor, idEmissor, emissor, nomeEmissor, fotoEmissor, textoMensagem, estadoMensagem, dataMensagem, horaMensagem", [$idPUsuario, $tipoUsuario."_".$idPUsuario, $nomeReceptor, $fotoReceptor, $_SESSION['idUsuarioLogado'], $_SESSION['tbUsuario']."_".$_SESSION['idUsuarioLogado'], $nomeEmissor, $fotoEmissor, $mensagem, "F", $this->dataSistema, $this->tempoSistema]);

        $usuarioLogado = $_SESSION['tbUsuario']."_".$_SESSION["idUsuarioLogado"];
        echo $this->selectJson("mensagens", [], ["emissor"=>['$in'=>[$usuarioLogado, $tipoUsuario."_".$idPUsuario]], "receptor"=>['$in'=>[$usuarioLogado, $tipoUsuario."_".$idPUsuario]]], [], "", [], ["idPMensagem"=>-1]);
      }

      

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
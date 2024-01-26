<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }
    
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjaxMae{
    	 
    	function __construct(){
    		parent::__construct();

        if($this->accao=="envoirMensagem"){
             $this->envoirMensagem();
        }else if($this->accao=="markMensagemAsRead"){
            $idPMensagem = isset($_GET["idPMensagemClicada"])?$_GET["idPMensagemClicada"]:"";
            $this->editar("mensagens", "estadoMensagem", ["V"], ["idPMensagem"=>$idPMensagem]);
        }

    	}

      private function envoirMensagem(){

          $mensagem = isset($_POST["mensagemCaixa"])?$_POST["mensagemCaixa"]:"";
          $idUsuarioLogado = $_SESSION["idUsuarioLogado"];
          $idPMensagem = isset($_POST["idPMensagem"])?$_POST["idPMensagem"]:"";

          $entidade = isset($_POST["usuario"])?$_POST["usuario"]:"";
          $entidade = explode("_", $entidade);
          $tipoUsuario = $entidade[0];
          $idPUsuario = isset($entidade[1])?$entidade[1]:"";

          $this->editar("mensagens", "estadoMensagem", ["V"], ["idPMensagem"=>$idPMensagem]);
 
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
      }           	
    }
    new manipulacaoDadosDoAjaxInterno();
        
?>
<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/funcoesAuxiliares.php';

    curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');
    curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
    curtina($_SESSION["directorioPaterno"].'angoschool/verificadorAcesso.php');
    curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/header.php');
    curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/layoutAreaEstatERels.php');
    curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/layoutAreaGestaoGPE.php');
    
    class layouts extends manipulacaoDados {
      
       function __construct($caminhoAbsoluto){
        $caminho = explode(umaOuDuasBarras(), $caminhoAbsoluto);
        $this->caminhoRetornar = "";
        for($i=1; $i<=count($caminho)-$_SESSION["numeroRecursividade"]; $i++){
          $this->caminhoRetornar .="../";
        }
        
        parent::__construct($caminhoAbsoluto);
        $this->verificadorAcesso = new verificacaoAcesso(__DIR__);

        if(!isset($_SESSION["idUsuarioLogado"]) || !isset($_SESSION["tipoUsuario"])){
          session_unset();
            if(file_exists("../login/entrar")){
                echo "<script>window.location='../../login/entrar'</script>";  
            }else{
              echo "<script>window.location='".$this->caminhoRetornar."login/entrar'</script>";
            }
        }else{
          //Verificar se realmente é usuário desta escola...
          
          $condEstadoActividade =" AND estadoActividadeEntidade=:estadoActividadeEntidade";
          if($_SESSION["idUsuarioLogado"]==35){
            $condEstadoActividade="";
          }
          if(count($this->selectArray("entidade_escola", "*", "idFEntidade=:idFEntidade AND idEntidadeEscola=:idEntidadeEscola AND estadoActividadeEntidade=:estadoActividadeEntidade".$condEstadoActividade, [$_SESSION["idUsuarioLogado"], $_SESSION["idEscolaLogada"], "A"]))<=0){
            
            $this->editar("entidadesonline", "estadoExpulsao", ["I"], "idPOnline=:idPOnline", [$_SESSION["idOnlineUsuario"]], "sim", "nao", 0);
            session_unset();                    
            if(file_exists("../login/entrar")){
                echo "<script>window.location='../../login/entrar'</script>";  
            }else{
              echo "<script>window.location='".$this->caminhoRetornar."login/entrar'</script>";
            }

          }
          
          if(isset($_SESSION["idOnlineUsuario"])){
              if(!$this->verificarSeParaExpulasar()){
                if(file_exists("../login/entrar")){
                    echo "<script>window.location='../../login/entrar'</script>";  
                }else{
                  echo "<script>window.location='".$this->caminhoRetornar."login/entrar'</script>";
                }                   
                 
              }
          }
        }      
    }

    function headerUsuario(){ 
      headerUsuario($this, $this->caminhoRetornar, $this->verificadorAcesso); 
    }
    function areaGestaoGPE (){
      areaGestaoGPE ($this, $this->caminhoRetornar, $this->verificadorAcesso, $this->idAnoActual);
    }
    function areaEstatERelat (){
      areaEstatERelat ($this, $this->caminhoRetornar, $this->verificadorAcesso, $this->idAnoActual);
    }



} ?>


<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
    	function __construct(){
    		parent::__construct();
            if($this->accao=="confirmarCodigo"){
                $this->confirmarCodigo();
            }
    	}

        private function confirmarCodigo(){
            $codigo = $_POST["codigo"];
            
            if(isset($_SESSION["idUsuarioLogado"])){
                if($_SESSION["tipoUsuario"]=="aluno"){
                    $arr = $this->selectArray("backupsenha", [], ["idBackMatricula"=>$_SESSION["idUsuarioLogado"]]);
                }else{
                    $arr = $this->selectArray("backupsenha", [], ["idBackEntidade"=>$_SESSION["idUsuarioLogado"]]);
                }               

                if(valorArray($arr, "dataEnvio")==$this->dataSistema && $codigo==valorArray($arr, "codigo")){
                    $_SESSION["recuperacaoSenha"]="sim";
                    echo "pode";
                }else if(valorArray($arr, "dataEnvio")!=$this->dataSistema && $codigo==valorArray($arr, "codigo")){
                    echo "0O Código já expirou.";
                }else{
                    echo "0Código incorrecto.";
                }
            }else{
                echo "0Código incorrecto.";
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>
<?php 
	session_start();
	include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();            
            if ($this->accao=="aletrarEstados"){
                if($this->verificacaoAcesso->verificarAcesso("", ["publicacaoPautas"], [])){
                    $this->aletrarEstados();
                }
	        }
    	}

        private function aletrarEstados(){
            $input = isset($_GET["input"])?$_GET["input"]:"";
            $estado = isset($_GET["estado"])?$_GET["estado"]:"";
            $mesApartirPublicado = isset($_GET["mesApartirPublicado"])?$_GET["mesApartirPublicado"]:"";  

            $exploder = explode("-", $input);
            $idPListaTurma = isset($exploder[0])?$exploder[0]:"";
            $trimestre = isset($exploder[1])?$exploder[1]:"";

            if($estado=="nao"){
                $trimestre--;
            }
            if($idPListaTurma==0){
                $this->editar("listaturmas", "trimestrePublicado, mesApartirPublicado", [$trimestre, $mesApartirPublicado], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual]);
            }else{
                $this->editar("listaturmas", "trimestrePublicado, mesApartirPublicado", [$trimestre, $mesApartirPublicado], ["idPListaTurma"=>$idPListaTurma]);
            }
            echo json_encode($this->turmasEscola());
        }

        

    	
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
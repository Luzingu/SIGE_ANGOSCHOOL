<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($tipoBaseDados){
    		parent::__construct();
            $this->tipoBaseDados=$tipoBaseDados;        
	        if($this->accao=="adicionarAnoLectivo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["anosLectivos00"])){
	        	    $this->adicionarAnoLectivo();
                }
	        }else if($this->accao=="editarAnoLectivo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["anosLectivos00"])){
                    $this->editarAnoLectivo();
                }
            }else if($this->accao=="excluirAnoLectivo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["anosLectivos00"])){
                    $this->excluirAnoLectivo();
                }
            }
    	}

        private function adicionarAnoLectivo(){
            $numAno = isset($_POST["numAno"])?$_POST["numAno"]:"";
            $this->editar("anolectivo", "estado", ["F"], ["estado"=>"V"]);
            $this->inserir("anolectivo", "idPAno", "numAno, estado", [$numAno, "V"]);
            foreach($this->selectArray("agrup_alunos", ["grupo"]) as $a){
                $this->editar("alunos_".$a["grupo"], "trasincaoAnual", ["I"], []);
            }
            $this->listarAnos();
        }

        private function editarAnoLectivo(){

            $numAno = isset($_POST["numAno"])?$_POST["numAno"]:"";
            $idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:"";
            $estado = isset($_POST["estado"])?$_POST["estado"]:"";
            $this->editar("anolectivo", "estado, numAno", [$estado, $numAno], ["idPAno"=>$idPAno]);
            $this->listarAnos();
        }

        private function excluirAnoLectivo(){
            $idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:"";

            $estado = $this->selectUmElemento("anolectivo", "estado", ["idPAno"=>$idPAno]);

            if($estado=="F"){
                echo "FNão podes excluir este ano lectivo.";
            }else{
                if(count($this->selectArray("anolectivo", [], ["idPAno"=>$idPAno], ["anos_lectivos"], 1))>0 || count($this->selectArray("alunosmatriculados", ['$or'=>[array("escola.idMatFAnoEP"=>$idPAno), array("escola.idMatFAnoEB"=>$idPAno), array("escola.idMatFAno"=>$idPAno)]]))>0){
                    echo "FNão podes excluir este ano.";
                }else{
                    if($this->editar("anolectivo", "numAno, estado", [null, null], ["idPAno"=>$idPAno])=="sim"){
                        $this->listarAnos(); 
                    }else{
                        echo "FNão podes excluir este ano lectivo.";
                    }
                }
            } 
             
        }

        private function listarAnos(){
            if($this->tipoBaseDados=="escola"){
                echo $this->selectJson("anolectivo", [], [], [], "", [], array("numAno"=>-1));
            }
        }
    	
    }
    new manipulacaoDadosDoAjaxInterno("escola");
    new manipulacaoDadosDoAjaxInterno("teste");
?>
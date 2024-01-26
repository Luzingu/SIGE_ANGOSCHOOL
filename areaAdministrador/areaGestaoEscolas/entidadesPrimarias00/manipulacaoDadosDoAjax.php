<?php
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($tipoBaseDados){
    		parent::__construct();
            $this->tipoBaseDados=$tipoBaseDados;

          	$this->idPEntidade = filter_input(INPUT_POST, "idPEntidade", FILTER_SANITIZE_NUMBER_INT);
      		$this->nomeEntidade = filter_input(INPUT_POST, "nomeEntidade", FILTER_SANITIZE_STRING);
          $this->tituloNomeEntidade = filter_input(INPUT_POST, "tituloNomeEntidade", FILTER_SANITIZE_STRING);
          $this->ninjaF5 = isset($_POST['ninjaF5'])?"A":"I";

            if(trim($this->tituloNomeEntidade)=="" || trim($this->tituloNomeEntidade)==NULL){
              $this->tituloNomeEntidade=$this->nomeEntidade;
            }

      		$this->sexoEntidade = filter_input(INPUT_POST, "sexoEntidade", FILTER_SANITIZE_STRING);
      		$this->estadoEntidade = filter_input(INPUT_POST, "estadoEntidade", FILTER_SANITIZE_STRING);
            $this->dataNascEntidade = trim(filter_input(INPUT_POST, "dataNascEntidade", FILTER_SANITIZE_STRING));

            $this->estadoAcesso = trim(filter_input(INPUT_POST, "estadoAcesso", FILTER_SANITIZE_STRING));

            $this->conDb($tipoBaseDados);

            if($this->accao=="editarEntidade"){
                if($this->verificacaoAcesso->verificarAcesso("", ["entidadesPrimarias00"])){
                        $this->editarEntidade();
                }
            }else if($this->accao=="salvarEntidade"){
                if($this->verificacaoAcesso->verificarAcesso("", ["entidadesPrimarias00"])){
    	        	  $this->salvarEntidade();
                }
            }else if ($this->accao=="excluirEntidade"){
                if($this->verificacaoAcesso->verificarAcesso("", ["entidadesPrimarias00"])){
    	        	    $this->excluirEntidade();
                }
            }
    	}


       private function salvarEntidade(){
       		 $jaExistemNumero="V";
            while ($jaExistemNumero=="V"){
                  $characters= "123456789";
                  $numeroUnico = substr(str_shuffle($characters),0, 4)."ANGOS00".substr(str_shuffle($characters),0, 2);
                  if(count($this->selectArray("entidadesprimaria", ["idPEntidade"], ["numeroInternoEntidade"=>$numeroUnico], [], 1))<=0){
                    $jaExistemNumero="F";
                  }
            }

       		if($this->inserir("entidadesprimaria", "idPEntidade", "nomeEntidade, numeroInternoEntidade, fotoEntidade, dataNascEntidade, generoEntidade, tituloNomeEntidade, senhaEntidade, estadoAcessoEntidade, ninjaF5", [$this->nomeEntidade, $numeroUnico, "usuario_default.png", $this->dataNascEntidade, $this->sexoEntidade, $this->tituloNomeEntidade, "0c7".criptografarMd5("0000")."ab", "A", $this->ninjaF5])=="sim"){

                $this->listar();

       		}else{
       			echo "FNão foi possível cadastrar a entidade.";
       		}
    	}

    	private function editarEntidade(){

         if($this->editar("entidadesprimaria", "nomeEntidade, dataNascEntidade, estadoAcessoEntidade, generoEntidade, tituloNomeEntidade, ninjaF5", [$this->nomeEntidade, $this->dataNascEntidade, $this->estadoAcesso, $this->sexoEntidade, $this->tituloNomeEntidade, $this->ninjaF5], ["idPEntidade"=>$this->idPEntidade])=="sim"){
            $this->editar("horario", "nomeEntidade", [$this->nomeEntidade], ["idPEntidade"=>$this->idPEntidade]);
            $this->editar("divisaoprofessores", "nomeEntidade", [$this->nomeEntidade], ["idPEntidade"=>$this->idPEntidade]);
            $this->editar("entidadesonline", "nomeEntidade", [$this->nomeEntidade], ["idPEntidade"=>$this->idPEntidade]);
						$this->ninjaF5();
            $this->listar();
         }else{
            echo "FNão foi possível editar a entidade.";
         }
      }


    	private function excluirEntidade(){
            if(count($this->selectArray("entidadesprimaria", ["idPEntidade"], ["idPEntidade"=>$this->idPEntidade, "escola.idEntidadeEscola"=>array('$ne'=>null)], ["escola"], []))>0){
                echo "FNão podes excluir esta entidade.";
            }else{
                if($this->editar("entidadesprimaria", "nomeEntidade, numeroInternoEntidade, fotoEntidade, dataNascEntidade, generoEntidade, tituloNomeEntidade, senhaEntidade, estadoAcessoEntidade", [null, null, null, null, null, null, null, null, null, null], ["idPEntidade"=>$this->idPEntidade])=="sim"){

                    $this->listar();
                }else{
                    echo "FNão foi possível excluir a entidade.";
                }
            }
    	}

        private function listar (){
            if($this->tipoBaseDados=="escola"){
                $cargo = $_POST["cargo"];

                $limite="";
                $ordenacao =array("nomeEntidade"=>1);
                if($cargo==100){
                  $limite=100;
                  $ordenacao =array("idPEntidade"=>-1);
                }
                echo $this->selectJson("entidadesprimaria", ["idPEntidade", "nomeEntidade", "numeroInternoEntidade", "generoEntidade", "fotoEntidade", "ninjaF5", "tituloNomeEntidade", "numeroAgenteEntidade", "dataNascEntidade", "estadoAcessoEntidade"], ["nomeEntidade"=>array('$ne'=>null)], [], $limite, [], $ordenacao);
            }
        }

		private function ninjaF5()
		{
            
    		foreach ($this->selectArray("escolas", ["idPEscola"], ["nomeEscola"=>array('$ne'=>null)]) as $a) {
    			if($this->ninjaF5 == "A")
    				$this->inserirObjecto("entidadesprimaria", "escola", "idP_Escola", "idFEntidade, idEntidadeEscola, nivelSistemaEntidade, chaveEnt, estadoActividadeEntidade", [$this->idPEntidade, $a["idPEscola"], 0, $this->idPEntidade."-".$a["idPEscola"], "I"], ["idPEntidade"=>$this->idPEntidade]);
    		}
    		if($this->ninjaF5 == "I")
    			$this->excluirItemObjecto("entidadesprimaria", "escola", ["idPEntidade"=>$this->idPEntidade], ["estadoActividadeEntidade"=>"I"]);
		}
    }
    new manipulacaoDadosDoAjaxInterno("escola");
    new manipulacaoDadosDoAjaxInterno("teste");
?>

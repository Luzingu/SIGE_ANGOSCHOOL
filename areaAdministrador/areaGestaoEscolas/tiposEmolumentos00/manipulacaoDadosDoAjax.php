<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($tipoBaseDados){
            parent::__construct();
            $this->designacaoEmolumento = isset($_POST["designacaoEmolumento"])?$_POST["designacaoEmolumento"]:"";
            $this->tipoPagamento = isset($_POST["tipoPagamento"])?$_POST["tipoPagamento"]:"";
            $this->idPTipoEmolumento = isset($_POST["idPTipoEmolumento"])?$_POST["idPTipoEmolumento"]:"";
            $this->codigo = isset($_POST["codigo"])?$_POST["codigo"]:"";
            $this->tipoBaseDados=$tipoBaseDados;
            $this->conDb($tipoBaseDados, true);

            if($this->accao=="editarCodigoEmolumento"){
                if($this->verificacaoAcesso->verificarAcesso("", ["tiposEmolumentos00"])){
                  $this->editarCodigoEmolumento();
                }
            }else if($this->accao=="novoTipoEmolumento"){

                if($this->verificacaoAcesso->verificarAcesso("", ["tiposEmolumentos00"])){
                      $this->novoTipoEmolumento();
                }
            }
        }

        private function novoTipoEmolumento(){

            if($this->inserir("tipos_emolumentos", "idPTipoEmolumento", "codigo, designacaoEmolumento, tipoPagamento", [$this->codigo, $this->designacaoEmolumento, $this->tipoPagamento])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar o emolumento.";
            }
        }

        private function editarCodigoEmolumento(){
            if($this->editar("tipos_emolumentos", "codigo, designacaoEmolumento, tipoPagamento", [$this->codigo, $this->designacaoEmolumento, $this->tipoPagamento], ["idPTipoEmolumento"=>$this->idPTipoEmolumento])=="sim"){
                
                $this->listar();   
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }
        private function listar(){
            if($this->tipoBaseDados=="escola"){
                echo $this->selectJson("tipos_emolumentos");
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno("escola");
    new manipulacaoDadosDoAjaxInterno("teste");
?>
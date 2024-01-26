<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($tipoBaseDados){
            parent::__construct();
            $this->tipoBaseDados=$tipoBaseDados;
            
            $this->ordem = isset($_POST["ordem"])?$_POST["ordem"]:"";
            $this->identificador = isset($_POST["identificador"])?$_POST["identificador"]:"";
            $this->designacao = isset($_POST["designacao"])?$_POST["designacao"]:"";
            $this->abreviacao1 = isset($_POST["abreviacao1"])?$_POST["abreviacao1"]:"";
            $this->abreviacao2 = isset($_POST["abreviacao2"])?$_POST["abreviacao2"]:"";
            $this->idPPeriodo = isset($_POST["idPPeriodo"])?$_POST["idPPeriodo"]:"";
            $this->idPNomeCurso = isset($_POST["idPNomeCurso"])?$_POST["idPNomeCurso"]:"";

            $this->conDb($tipoBaseDados);

            if($this->accao=="editarPeriodo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["periodosCursos00"])){
                  $this->editarPeriodo();
                }
            }else if($this->accao=="novoPeriodo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["periodosCursos00"])){
                      $this->novoPeriodo();
                }
            }else if ($this->accao=="excluirPeriodo"){
                if($this->verificacaoAcesso->verificarAcesso("", ["periodosCursos00"])){
                  $this->excluirPeriodo();
                }
            }
        }

        private function novoPeriodo(){
            if($this->inserirObjecto("nomecursos", "periodos", "idPPeriodo", "ordem, identificador, designacao, abreviacao1, abreviacao2", [$this->ordem, $this->identificador, $this->designacao, $this->abreviacao1, $this->abreviacao2], ["idPNomeCurso"=>$this->idPNomeCurso])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar a classe.";
            }
        }

        private function editarPeriodo(){
            if($this->editarItemObjecto("nomecursos", "periodos", "ordem, identificador, designacao, abreviacao1, abreviacao2", [$this->ordem, $this->identificador, $this->designacao, $this->abreviacao1, $this->abreviacao2], ["idPNomeCurso"=>$this->idPNomeCurso], ["idPPeriodo"=>$this->idPPeriodo])=="sim"){

                $this->listar();   
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirPeriodo(){
            if($this->excluirItemObjecto("nomecursos", "periodos", ["idPNomeCurso"=>$this->idPNomeCurso], ["idPPeriodo"=>$this->idPPeriodo])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir a classe.";
            }
        }
        private function listar(){
            if($this->tipoBaseDados=="escola"){
                
                $array = listarItensObjecto($this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPNomeCurso]), "periodos");
                echo json_encode(ordenar($array, "ordem ASC"));
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno("escola");
    new manipulacaoDadosDoAjaxInterno("teste");
?>
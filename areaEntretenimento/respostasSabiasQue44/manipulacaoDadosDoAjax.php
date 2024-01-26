<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->conDb("entretenimento", true);

            if($this->accao=="editarSabiasQue"){
                $this->editarSabiasQue();
            }else if($this->accao=="salvarSabiasQue"){
                $this->salvarSabiasQue();
            }else if ($this->accao=="excluirSabiasQue"){
                $this->excluirSabiasQue();
            }
        }

        private function salvarSabiasQue(){
            $resposta = isset($_POST["resposta"])?$_POST["resposta"]:"";

            $arquivo = $this->upload("arquivo", $this->dia.$this->mes.$this->ano.date("H").date("s").date("i"), 'livraria', "../../../");

            if($this->inserir("sabias_que", "idPSabiasQue", "resposta,arquivo, autor", [$resposta, $arquivo, valorArray($this->sobreUsuarioLogado, "nomeEntidade")])=="sim"){
                $this->listar();
            }else{
                    echo "FNão foi possível adicionar a resposta.";
            }
           
        }

        private function editarSabiasQue(){
            $resposta = isset($_POST["resposta"])?$_POST["resposta"]:"";

            $idPSabiasQue = isset($_POST["idPSabiasQue"])?$_POST["idPSabiasQue"]:"";
            $array = $this->selectArray("sabias_que", [], ["idPSabiasQue"=>$idPSabiasQue]);

            if(isset($_FILES["arquivo"])){
                if(file_exists("../../../livraria/".valorArray($array, "arquivo")) && is_file("../../../livraria/".valorArray($array, "arquivo"))){
                    unlink("../../../livraria/".valorArray($array, "arquivo"));
                }
            }
            $arquivo = $this->upload("arquivo", $this->dia.$this->mes.$this->ano.date("H").date("s").date("i"), 'livraria', "../../../", valorArray($array, "arquivo"));

            if($this->editar("sabias_que", "resposta,arquivo", [$resposta, $arquivo], ["idPSabiasQue"=>$idPSabiasQue])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirSabiasQue(){
            $idPSabiasQue = isset($_POST["idPSabiasQue"])?$_POST["idPSabiasQue"]:"";
            $array = $this->selectArray("sabias_que", [], ["idPSabiasQue"=>$idPSabiasQue]);


            if($this->excluir("sabias_que", ["idPSabiasQue"=>$idPSabiasQue])=="sim"){
                if(file_exists("../../../livraria/".valorArray($array, "arquivo")) && is_file("../../../livraria/".valorArray($array, "arquivo"))){
                    unlink("../../../livraria/".valorArray($array, "arquivo"));
                }
                $this->listar();
            }else{
                echo "FNão foi possível excluir.";
            }  
        }
        private function listar(){
          echo $this->selectJson("sabias_que", [], [], [], "", [], ["idPSabiasQue"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
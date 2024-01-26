<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->conDb("entretenimento", true);
            $this->questao = isset($_POST['questao'])?$_POST['questao']:"";
            $this->tipoResposta = isset($_POST['tipoResposta'])?$_POST['tipoResposta']:"";
            $this->resposta1 = isset($_POST['resposta1'])?$_POST['resposta1']:"";
            $this->resposta2 = isset($_POST['resposta2'])?$_POST['resposta2']:"";
            $this->resposta3 = isset($_POST['resposta3'])?$_POST['resposta3']:"";
            $this->resposta4 = isset($_POST['resposta4'])?$_POST['resposta4']:"";
            $this->idPQuestao = isset($_POST['idPQuestao'])?$_POST['idPQuestao']:"";
            $this->pontuacao = isset($_POST['pontuacao'])?$_POST['pontuacao']:""; 

            if($this->accao=="editarQuestao"){
                $this->editarQuestao();
            }else if($this->accao=="salvarQuestao"){
                $this->salvarQuestao();
            }else if ($this->accao=="excluirQuestao"){
                $this->excluirQuestao();
            }
        }

        private function salvarQuestao(){
            if($this->tipoResposta=="Texto"){
                if($this->inserir("questoes", "idPQuestao", "questao, tipoResposta, resposta1, resposta2, resposta3, resposta4, pontuacao, autor", [$this->questao, $this->tipoResposta, $this->resposta1, $this->resposta2, $this->resposta3, $this->resposta4, $this->pontuacao, valorArray($this->sobreUsuarioLogado, "nomeEntidade")])=="sim"){
                    $this->listar();
               }else{
                echo "FNão foi possível adicionar a categoria.";
               } 
            }else{
                $resposta1 = $this->upload("respostaImg1", "1_".$this->dia.$this->mes.$this->ano.date("H").date("s").date("i"), 'livraria', "../../../");
                $resposta2 = $this->upload("respostaImg2", "2_".$this->dia.$this->mes.$this->ano.date("H").date("s").date("i"), 'livraria', "../../../");

                if($this->inserir("questoes", "idPQuestao", "questao, tipoResposta, resposta1, resposta2, pontuacao, autor", [$this->questao, $this->tipoResposta, $resposta1, $resposta2, $this->pontuacao, valorArray($this->sobreUsuarioLogado, "nomeEntidade")])=="sim"){
                    $this->listar();
               }else{
                echo "FNão foi possível adicionar a categoria.";
               }
            }
           
        }

        private function editarQuestao(){
            if($this->editar("questoes", "questao, resposta1, resposta2, resposta3, resposta4, pontuacao", [$this->questao, $this->resposta1, $this->resposta2, $this->resposta3, $this->resposta4, $this->pontuacao], ["idPQuestao"=>$this->idPQuestao])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirQuestao(){
            $arr =$this->selectArray("questoes", [], ["idPQuestao"=>$this->idPQuestao]);

            if($this->excluir("questoes", ["idPQuestao"=>$this->idPQuestao])=="sim"){
                if(file_exists("../../../livraria/".valorArray($arr, "resposta1")) && is_file("../../../livraria/".valorArray($arr, "resposta1"))){
                    unlink("../../../livraria/".valorArray($arr, "resposta1"));
                }
                if(file_exists("../../../livraria/".valorArray($arr, "resposta2")) && is_file("../../../livraria/".valorArray($arr, "resposta2"))){
                    unlink("../../../livraria/".valorArray($arr, "resposta2"));
                }
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados.";
            }  
        }
        private function listar(){
          echo $this->selectJson("questoes", [], [], [], "", [], ["idPQuestao"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->conDb("entretenimento", true);
            $this->categoriaLivro = isset($_POST['categoriaLivro'])?$_POST['categoriaLivro']:"";
            $this->idPCategoria = isset($_POST['idPCategoria'])?$_POST['idPCategoria']:"";

            if($this->accao=="editarCategoria"){
                $this->editarCategoria();
            }else if($this->accao=="salvarCategoria"){
                $this->salvarCategoria();
            }else if ($this->accao=="excluirCategoria"){
                $this->excluirCategoria();
            }
        }

        private function salvarCategoria(){
           if($this->inserir("categoriaLivros", "idPCategoria", "nomeCategoria, autor", [$this->categoriaLivro, valorArray($this->sobreUsuarioLogado, "nomeEntidade")])=="sim"){
                $this->listar();
           }else{
            echo "FNão foi possível adicionar a categoria.";
           }
        }

        private function editarCategoria(){
            if($this->editar("categoriaLivros", "nomeCategoria", [$this->categoriaLivro], ["idPCategoria"=>$this->idPCategoria])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da categoria.";
            }
        }

        private function excluirCategoria(){
            if($this->excluir("categoriaLivros", ["idPCategoria"=>$this->idPCategoria])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da categoria.";
            }  
        }

        private function listar(){
          echo json_encode($this->selectArray("categoriaLivros", [], [], [], "", [], ["idPCategoria"=>1]));
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
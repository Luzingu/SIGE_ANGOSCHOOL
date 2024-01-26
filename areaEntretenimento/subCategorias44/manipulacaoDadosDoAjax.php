<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->conDb("entretenimento", true);
            $this->subCategoriaLivro = isset($_POST['subCategoriaLivro'])?$_POST['subCategoriaLivro']:"";
            $this->idPCategoria = isset($_POST['idPCategoria'])?$_POST['idPCategoria']:"";
            $this->idPSubCategoria = isset($_POST['idPSubCategoria'])?$_POST['idPSubCategoria']:"";

            if($this->accao=="editarSubCategoria"){
                $this->editarSubCategoria();
            }else if($this->accao=="salvarSubCategoria"){
                $this->salvarSubCategoria();
            }else if ($this->accao=="excluirSubCategoria"){
                $this->excluirSubCategoria();
            }
        }

        private function salvarSubCategoria(){

           if($this->inserirObjecto("categoriaLivros", "subCategoria", "idPSubCategoria", "nomeSubCategoria, autor", [$this->subCategoriaLivro, valorArray($this->sobreUsuarioLogado, "nomeEntidade")], ["idPCategoria"=>$this->idPCategoria])=="sim"){
                $this->listar();
           }else{
                echo "FNão foi possível adicionar a sub categoria.";
           }
        }

        private function editarSubCategoria(){

            if($this->editarItemObjecto("categoriaLivros", "subCategoria", "nomeSubCategoria", [$this->subCategoriaLivro], ["idPCategoria"=>$this->idPCategoria], ["idPSubCategoria"=>$this->idPSubCategoria])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da sub categoria.";
            }
        }

        private function excluirSubCategoria(){
            if($this->excluirItemObjecto("categoriaLivros", "subCategoria", ["idPCategoria"=>$this->idPCategoria], ["idPSubCategoria"=>$this->idPSubCategoria])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível editar os dados da sub categoria.";
            }  
        }

        private function listar(){
          echo $this->selectJson("categoriaLivros", [], ["idPCategoria"=>$this->idPCategoria], ["subCategoria"], "", [], ["subCategoria.nomeSubCategoria"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
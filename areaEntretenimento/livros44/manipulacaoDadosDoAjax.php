<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->conDb("entretenimento", true);
            $this->tituloLivro = isset($_POST['tituloLivro'])?$_POST['tituloLivro']:"";
            $this->autoresLivro = isset($_POST['autoresLivro'])?$_POST['autoresLivro']:"";
            $this->idSubCategoria = isset($_POST['idSubCategoria'])?$_POST['idSubCategoria']:"";
            $this->idPCategoria = isset($_POST['idPCategoria'])?$_POST['idPCategoria']:"";
            $this->idPLivro = isset($_POST['idPLivro'])?$_POST['idPLivro']:"";

            if($this->accao=="salvarLivro"){
                $this->salvarLivro();
            }else if ($this->accao=="excluirLivro"){
                $this->excluirLivro();
            }
        }

        private function salvarLivro(){
            
            $arquivo = $this->upload("arquivo", $this->tituloLivro, 'livraria', "../../../");
            $nomeSubCategoria = $this->selectUmElemento("categoriaLivros", "nomeSubCategoria", ["idPCategoria"=>$this->idPCategoria, "subCategoria.idPSubCategoria"=>$this->idSubCategoria], ["subCategoria"]);

           if($this->inserir("livraria", "idPLivro", "tituloLivro, autoresLivro, idCategoria, idSubCategoria, subCategoria, arquivo, publicador, dataPublicacao", [$this->tituloLivro, $this->autoresLivro, $this->idPCategoria, $this->idSubCategoria, $nomeSubCategoria, $arquivo, valorArray($this->sobreUsuarioLogado, "nomeEntidade"), $this->dataSistema])=="sim"){

                
                $this->listar();
           }else{
            echo "FNão foi possível adicionar o livro.";
           }
        }

        private function excluirLivro(){
            $arquivo = $this->selectUmElemento("livraria", "arquivo", ["idPLivro"=>$this->idPLivro]);
            if($this->excluir("livraria", ["idPLivro"=>$this->idPLivro])=="sim"){
                if(file_exists("../../../livraria/".$arquivo) && is_file("../../../livraria/".$arquivo)){
                    unlink("../../../livraria/".$arquivo);
                }
                $this->listar();
            }else{
                echo "FNão foi possível excluir o livro.";
            } 
        }

        private function listar(){
          echo $this->selectJson("livraria", [], ["idCategoria"=>$this->idPCategoria], [], "", [], ["tituloLivro"=>1]);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
<?php 
  session_start();
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
     
  class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
    function __construct($caminhoAbsoluto){
      parent::__construct();
      $this->caminhoRetornar = "../../../";

      $this->legendaFoto = filter_input(INPUT_POST, "legendaFoto", FILTER_SANITIZE_STRING);
      $this->idPGaleria = filter_input(INPUT_POST, "idPFoto", FILTER_SANITIZE_NUMBER_INT);

      if($this->accao=="adicionarFoto"){
        if($this->verificacaoAcesso->verificarAcesso("", ["galeriaFotos11"])){
            $this->adicionarFoto();
        }
      }else if($this->accao=="excluirFoto"){
        if($this->verificacaoAcesso->verificarAcesso("", ["galeriaFotos11"])){
            $this->excluirFoto();
        }
      }else if($this->accao=="marcarFP"){
        if($this->verificacaoAcesso->verificarAcesso("", ["galeriaFotos11"])){
            $this->marcarFP();
        }
      }
    }

    private function excluirFoto(){
        $foto = $this->selectUmElemento("escolas", "fotos.fotoGaleria", ["fotos.idPGaleria"=>$this->idPGaleria, "idPEscola"=>$_SESSION["idEscolaLogada"]]);

        if(file_exists($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Galeria/".$foto)){
          unlink($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Galeria/".$foto);
        }
        if($this->excluirItemObjecto("escolas", "fotos", ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idPGaleria"=>$this->idPGaleria])=="sim"){
          echo $this->selectJson("escolas",["fotos.idPGaleria", "fotos.fotoGaleria", "fotos.legendaFoto", "fotos.imgPrincipal", "idPEscola"],["idPEscola"=>$_SESSION['idEscolaLogada']], ["fotos"]);
        }else{
          echo "FNão Foi Possível Excluir a Foto!";
        }
    }

    private function adicionarFoto(){
      $erro=false;
      if(isset($_FILES['imagem']) && $_FILES['imagem']['size'] > 0){

        $extensoes_aceitas = array('bmp' ,'png', 'svg', 'jpeg', 'jpg');
        $array_extensoes   = explode('.', $_FILES['imagem']['name']);
        $extensao = strtolower(end($array_extensoes));

         // Validamos se a extensão do arquivo é aceita
        if (array_search(strtolower(end($array_extensoes)), $extensoes_aceitas) == false){
           echo "FA Extensão inválida, troque outra foto.";
        }else{

          $extensao = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
          
          $nomeProvisorio = $this->dia.$this->mes.$this->ano.date("H").date("s").date("i");


          $nomeImagem = $this->upload("imagem", $nomeProvisorio, "Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Galeria", $this->caminhoRetornar, "", "", ""); 

          $this->inserirObjecto("escolas", "fotos", "idPGaleria", "fotoGaleria, legendaFoto, idGaleriaEscola, imgPrincipal", [$nomeImagem, $this->legendaFoto, $_SESSION["idEscolaLogada"], "F"], ["idPEscola"=>$_SESSION['idEscolaLogada']]);

          echo $this->selectJson("escolas",["fotos.idPGaleria", "fotos.fotoGaleria", "fotos.legendaFoto", "fotos.imgPrincipal", "idPEscola"],["idPEscola"=>$_SESSION['idEscolaLogada']], ["fotos"]);
        }
      }
    }
  }
  new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
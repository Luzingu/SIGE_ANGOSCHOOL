<?php 
  session_start();
  include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{           

      function __construct($caminhoAbsoluto){
        parent::__construct();
        
          $this->caminhoRetornar ="../../../";
        include_once $this->caminhoRetornar.'funcoesAuxiliares.php';

        if($this->accao=="actualizarDefinicoesConta"){
          if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE"], "", "", "FNão tens permissão de alterar os dados.")){
            $this->actualizarDefinicoesConta();
          }
        }
        
      }
       private function actualizarDefinicoesConta (){
          $codOrganismo = trim(filter_input(INPUT_POST, "codOrganismo", FILTER_SANITIZE_NUMBER_INT));

          $numeroTelefone = trim(filter_input(INPUT_POST, "numeroTelefone", FILTER_SANITIZE_STRING));

          $valEmail = trim(filter_input(INPUT_POST, "valEmail", FILTER_SANITIZE_STRING));
          
          $valEmail = trim(filter_input(INPUT_POST, "valEmail", FILTER_SANITIZE_STRING));
          
          $decretoCriacaoInstituicao = trim(filter_input(INPUT_POST, "decretoCriacaoInstituicao", FILTER_SANITIZE_STRING));
          
          $acercaEscola = $_POST["acercaEscola"];

          $corCabecalhoTabelas = $_POST["corCabecalhoTabelas"];
          $corLetrasCabecalhoTabelas = $_POST["corLetrasCabecalhoTabelas"];
          
          $luzingu = $this->selectArray("escolas", "*", "idPEscola=:idPEscola", [$_SESSION["idEscolaLogada"]]);

          $encontrouErroNosUpload="nao";
          $logotipo=valorArray($luzingu, "logoEscola");
          $assinatura1=valorArray($luzingu, "assinatura1");
          $assinatura2=valorArray($luzingu, "assinatura2");
          $assinatura3=valorArray($luzingu, "assinatura3");

          if(isset($_FILES["logoEscola"]) && $_FILES['logoEscola']['size']>0){
             $r1 = $this->fazerUpload($_FILES["logoEscola"], "logotipoEscola_".$_SESSION["idEscolaLogada"], " do Logotipo da Escola");
            
             if($r1!="falha"){
              $_SESSION["logoEscola"]=$r1;
              $logotipo = $r1;
             }else{
                $encontrouErroNosUpload="sim";
             }
          }

          if(isset($_FILES["assinatura1"]) && $_FILES['assinatura1']['size']>0 && $encontrouErroNosUpload=="nao"){

             $r1 = $this->fazerUpload($_FILES["assinatura1"], "assinatura1_".$_SESSION["idEscolaLogada"], " da Assinatura do DG.");
            
             if($r1!="falha"){
              $assinatura1 = $r1;
             }else{
                $encontrouErroNosUpload="sim";
             }
          }

          if(isset($_FILES["assinatura2"]) && $_FILES['assinatura2']['size']>0 && $encontrouErroNosUpload=="nao"){

             $r1 = $this->fazerUpload($_FILES["assinatura2"], "assinatura2_".$_SESSION["idEscolaLogada"], " da Assinatura do DP.");
        
             if($r1!="falha"){
              $assinatura2 =$r1;
             }else{
                $encontrouErroNosUpload="sim";
             }
          }

          if(isset($_FILES["assinatura3"]) && $_FILES['assinatura3']['size']>0 && $encontrouErroNosUpload=="nao"){

             $r1 = $this->fazerUpload($_FILES["assinatura3"], "assinatura3_".$_SESSION["idEscolaLogada"], " da Assinatura da DA.");
        
             if($r1!="falha"){
              $assinatura3 = $r1;
             }else{
                $encontrouErroNosUpload="sim";
             }
          }

          
          if($encontrouErroNosUpload=="nao"){

            $this->editar("escolas", "email, acercaEscola, codOrganismo, decretoCriacaoInstituicao, numeroTelefone, logoEscola, assinatura1, assinatura2, assinatura3, corCabecalhoTabelas, corLetrasCabecalhoTabelas", [$valEmail, $acercaEscola, $codOrganismo, $decretoCriacaoInstituicao, $numeroTelefone, $logotipo, $assinatura1, $assinatura2, $assinatura3, $corCabecalhoTabelas, $corLetrasCabecalhoTabelas], "idPEscola=:idPEscola", [$_SESSION["idEscolaLogada"]]);

            echo $this->selectJson("escolas", "*", "idPEscola=:idPEscola", [$_SESSION["idEscolaLogada"]]);
          }
          
       }

       private function fazerUpload($variavel, $nomeArquivo, $title=""){
          $retorno="";

            $extensoes_aceitas = array('bmp' ,'png', 'svg', 'jpeg', 'jpg');
            $array_extensoes   = explode('.', $variavel['name']);
            $extensao = strtolower(end($array_extensoes));
     
            if(array_search($extensao, $extensoes_aceitas) == false){
              $retorno="falha";
              echo "FAExtensão ".$title." é inválida, troque outra foto!";
            }
     
             // Verifica se o upload foi enviado via POST   
             if(is_uploaded_file($variavel['tmp_name'])){
                  // Verifica se o diretório de destino existe, senão existir cria o diretório  
                  if(!file_exists($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"])){
                        mkdir($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]); 
                  }

                  if(!file_exists($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones")){
                    mkdir($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones"); 
                  }

                  // Monta o caminho de destino com o nome do arquivo  
                  $extensao = pathinfo($variavel["name"], PATHINFO_EXTENSION);
                  $nomeImagem = $nomeArquivo.".".$extensao;
                    
                  if(!move_uploaded_file($variavel['tmp_name'], $this->caminhoRetornar.'Ficheiros/Escola_'.$_SESSION["idEscolaLogada"].'/Icones/'.$nomeImagem)){ 
                    $retorno="falha";
                     echo "FHouve um erro ao gravar o arquivo na pasta de destino!";
                  } 
             }
          if($retorno==""){
            return $nomeImagem;
          }else{
            return $retorno;
          }
       }
        

        

      
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
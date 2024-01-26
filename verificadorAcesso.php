<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php');
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');
  abrirSessao();

class verificacaoAcesso extends manipulacaoDadosMae{
  
    function __construct (){
      parent::__construct();
      $this->acessoareas=array();
      $this->niveisAcessoEntidade=array();
      if(isset($_SESSION["idEscolaLogada"]) && isset($_SESSION["idUsuarioLogado"])){
        
        $this->acessoareas= $this->selectArray("areas", ["idPArea", "instituicoes.acessos"], ["instituicoes.idEscola"=>$_SESSION["idEscolaLogada"]], ["instituicoes"]);
        $this->niveisAcessoEntidade= listarItensObjecto($this->sobreUsuarioLogado, "acessos", ["idEscola=".$_SESSION['idEscolaLogada']]);
      }
    }
    
    function verificarAcesso($idPArea, $carlitos=array(), $classeVerificarAcesso=array(), $mensagem="FNão tens permissão de alterar os dados", $sePodeSerExecutadoGratuitamente="nao", $condicaoLogica=true){
      $retorno=false;

      if(!is_array($carlitos)){
        $niveisAcesso[]=$carlitos;
      }else{
        $niveisAcesso=$carlitos;
      }
      if($idPArea==""){
        foreach($niveisAcesso as $acesso){
          if($acesso=="biblioteca44" || $acesso=="categorias44" || $acesso=="dicasSabiasQue44" || $acesso=="historicoJogo44" || $acesso=="jogarEuSeique44" || $acesso=="livros44" || $acesso=="perguntas44" || $acesso=="rackingPontos44" || $acesso=="respostasSabiasQue44" || $acesso=="subCategorias44")
            $idPArea=8;
        }
      }

      if($idPArea=="backup"){
        $retorno = valorArray($this->sobreUsuarioLogado, "BACKUP", "escola")=="V" || $_SESSION['idUsuarioLogado']==35;

      }else if($condicaoLogica==false || !$this->verficarPrazoEscola($sePodeSerExecutadoGratuitamente)){
        $retorno=false;
      }else if($condicaoLogica==false){
        $retorno=false;
      }else if($idPArea==1){
        if($_SESSION['tipoUsuario']=="aluno"){
          $retorno=true;
        }
      }else if($idPArea==0){
        if($_SESSION['tipoUsuario']=="administrador" || valorArray($this->sobreUsuarioLogado, "LUZL", "escola")=="V" || valorArray($this->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")==0 || $_SESSION['idUsuarioLogado']==35){
          $retorno=true;
        }
      }else if($idPArea==2){
        if($_SESSION['tipoUsuario']=="professor"){
          $retorno=true;
        }
      }else if($idPArea==8){
        if($_SESSION['idUsuarioLogado']==35 || $_SESSION['tipoUsuario']=="administrador"){
          $retorno=true;
        }
      }else if($idPArea==7){
        if($_SESSION['idUsuarioLogado']==35 || $_SESSION['tipoUsuario']=="administrador"){
          $retorno=true;
        }
      }else if(valorArray($this->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")==0){
        $retorno=true;
      }else{

        if($idPArea==""){
          foreach($niveisAcesso as $acesso){
            $idPArea = valorArray($this->selectArray("menus", ["instituicoes.idArea"], ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada'], '$or'=>[array("identificadorMenu"=>$acesso), array("subMenus.identificadorSubMenu"=>$acesso)] ], ["instituicoes"]), "idArea", "instituicoes");
          }
        }

        $acessos = "";
        foreach($this->acessoareas as $acesso){
          if($acesso["idPArea"]==$idPArea){
            $acessos = valorArray($acesso, "acessos", "instituicoes");
            break;
          }
        }
        
        foreach(explode(",", $acessos) as $a){
          if($a==valorArray($this->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")){
            $retorno=true;
            break;
          }
        }

        if($retorno==false){
          foreach($niveisAcesso as $acesso){

            if($acesso=="qualquerAcesso"){
              foreach($this->niveisAcessoEntidade as $nivel){

                $idArea = valorArray($this->selectArray("menus", ["instituicoes.idArea"], ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada'], '$or'=>[array("idPMenu"=>$nivel["idPMenu"]), array("subMenus.idPSubMenu"=>$nivel["idPMenu"])] ], ["instituicoes"]), "idArea", "instituicoes");
                if($idPArea==$idArea){
                  $retorno=true;
                  break;
                }
              }
            }else{

              

              $idPMenu = $this->selectUmElemento("menus", "idPMenu", ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada'], "instituicoes.idArea"=>$idPArea, "identificadorMenu"=>$acesso], ["instituicoes"]);

              $sebastiao = $this->selectArray("menus", ["subMenus.idPSubMenu"], ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada'], "instituicoes.idArea"=>$idPArea, "subMenus.identificadorSubMenu"=>$acesso], ["instituicoes", "subMenus"]);
              $idPSubMenu = valorArray($sebastiao, "idPSubMenu", "subMenus");

              foreach($this->niveisAcessoEntidade as $nivel){
                if($idPMenu==$nivel["idPMenu"] || $idPSubMenu==$nivel["idPMenu"]){
                  $retorno=true;
                  break;
                }
              }
              if(count($classeVerificarAcesso)>0 && $retorno==true){
                $retorno=false;

                $classesAcesso = valorArray(listarItensObjecto($this->sobreUsuarioLogado, "classes_aceso", ["idEscola=".$_SESSION['idEscolaLogada'], "idPArea=".$idPArea]), "classes");
                
                //Pegar a Classe e Curso Enviado...
                $classeC = $classeVerificarAcesso[0];
                $cursoC = isset($classeVerificarAcesso[1])?$classeVerificarAcesso[1]:"";

                if($classeC<=9 || $cursoC==null || $cursoC==""){
                  $classeEnviada =$classeC;
                }else{
                  $classeEnviada =$classeC."_".$cursoC;
                }               
                $classesAcesso = explode(",", $classesAcesso);


                foreach($classesAcesso as $mengi){
                  if($mengi==$classeEnviada){
                    $retorno=true;
                    break;
                  }
                }
              }
            }
          }
        }
      }

      if(valorArray($this->sobreUsuarioLogado, "BACKUP", "escola")=="V" && $idPArea!="backup"){
        $diferenca = calcularDiferencaEntreDatas($this->dataSistema, valorArray($this->sobreEscolaLogada, "dataBackup1"));
        if($diferenca>7){
          $retorno=true;
        }
      }
      if($retorno==false){
        if($mensagem=="msg"){
          $this->estilos();
          $this->negarAcesso();
        }else if($mensagem!=""){
          echo $mensagem;
        }
      }
      return $retorno;      
   } function negarAcesso(){ ?>
    <div id="acessoNegado" style="min-height: 700px; padding-top: 60px;">
        <div class="row">
          <div class="col-lg-5 col-md-5 col-lg-offset-2 col-md-offset-2">
             <h1 class="lead vermelha">Acesso Negado</h1>
            <p class="lead">Os dados requesitados não podem ser visualizados por que não tens devida autorização.</p>

            <p class="lead">Essas informações estão protegidas de modo que sejam visualizadas ou alterados apenas pelo responsável desta Área. Para mais informações, contacte o departamento técnico e da segurança do <strong> AngoSchool</strong>.</p>

            <p class="lead text-danger text-right"><i>926 930 664</i><p>
            
          </div>
          <div class="col-lg-3 col-md-3 visible-md visible-lg">
            <img src="<?php echo $this->caminhoRecuar; ?>icones/roboSeguranca.png">
          </div><br/>
        </div>
    </div>


  <?php } private function verficarPrazoEscola($sePodeSerExecutadoGratuitamente="nao"){

    //if(isset($_SESSION["idEscolaLogada"]) && !isset($_SESSION["seJaTentouRenovarPagamentoPosPago"])){
    //echo "OK";
      $this->prorogarContratoEscolasPosPago($_SESSION['idEscolaLogada']);
      $_SESSION["seJaTentouRenovarPagamentoPosPago"]="sim";
    //}
        
    $sobreContrato = $this->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["contrato"]);

    $retorno=true;
    if($sePodeSerExecutadoGratuitamente=="sim" || $_SESSION['idEscolaLogada']==4 || $_SESSION['idEscolaLogada']==7){
      $retorno = true;
    }else if(valorArray($sobreContrato, "tipoPagamento", "contrato")=="nao"){
       $retorno=true;
    }else if(valorArray($sobreContrato, "tipoPagamento", "contrato")=="pos"){

      if(valorArray($sobreContrato, "fimPrazoPosPago", "contrato")=="" || valorArray($sobreContrato, "fimPrazoPosPago", "contrato")==NULL){
        $retorno=true;
      }else if(valorArray($sobreContrato, "fimPrazoPosPago", "contrato")<$this->dataSistema){
        $retorno=false;
      }
    }else if(valorArray($sobreContrato, "tipoPagamento", "contrato")=="pre"){
        if(valorArray($sobreContrato, "dataExpiracaoValidade", "contrato")=="" || valorArray($sobreContrato, "dataExpiracaoValidade", "contrato")==NULL){
          $retorno=true;
        }else if(valorArray($sobreContrato, "dataExpiracaoValidade", "contrato")<$this->dataSistema){
          $retorno=false;
        }
    }
    return $retorno;
  } 


  private function estilos(){?>
      <style type="text/css">
  #acessoNegado{
    margin-top: 0px;

  }

  #acessoNegado h1{
    margin-top: -10px;
    font-size: 3em;
    letter-spacing: 2px;
    font-family: sans-serif;
  }

  #acessoNegado p{
    margin-top: 15px;
    font-size: 18pt;
    text-align: justify;
  }

  #acessoNegado p:nth-child(1){
    margin-top: 5px;
  }

  #acessoNegado h2{
    font-size: 22pt;
    font-style: italic;
  }

  #acessoNegado img{
    height: 380px;

  }
</style>
   <?php }

  }
?>
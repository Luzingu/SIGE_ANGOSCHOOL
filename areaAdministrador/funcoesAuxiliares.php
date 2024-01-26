<?php  
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php';


   function criarSessaoDeNiveisAcessosDoAdministrador(){
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }

     unset($_SESSION["niveisAcessosGestaoEmpresa"]);
     unset($_SESSION["niveisAcessosGestaoEscolas"]);

     $_SESSION["niveisAcessosGestaoEmpresa"][] = array("label"=>"Área da Tesouraria", "id"=>"admAreaTesouraria");
     $_SESSION["niveisAcessosGestaoEmpresa"][] = array("label"=>"Autor. Saida Val.", "id"=>"admAutorSaidaValor");
     $_SESSION["niveisAcessosGestaoEmpresa"][] = array("label"=>"Adicionar Func.", "id"=>"admAdicFuncion");
     $_SESSION["niveisAcessosGestaoEmpresa"][] = array("label"=>"Dados dos Func.", "id"=>"admDadosFuncion");
     $_SESSION["niveisAcessosGestaoEmpresa"][] = array("label"=>"Galeria de Fotos", "id"=>"admGaleriaFotos");
     $_SESSION["niveisAcessosGestaoEmpresa"][] = array("label"=>"Tutoriais", "id"=>"admTutoriais");
     $_SESSION["niveisAcessosGestaoEmpresa"][] = array("label"=>"Pesq. Funcionários", "id"=>"pesqFuncionarios");


     $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Cad. Escolas", "id"=>"admCadEscolas");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Ger. Contrato", "id"=>"admContratoEscolas");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Cad. Cursos", "id"=>"admCadCursos");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Cad. Anos Lect.", "id"=>"admCadAnoLect");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Cad. Disciplinas", "id"=>"admCadDisciplinas");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Cad. Entidades", "id"=>"admCadEntidades");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Contr. Senhas", "id"=>"admContrSenhas");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Cad. Func. Escolas", "id"=>"admCadFuncEscolas");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Contr. Acesso Área", "id"=>"admContrAcessoArea");
      $_SESSION["niveisAcessosGestaoEscolas"][] = array("label"=>"Pag. Sistema", "id"=>"admPagSistema");

     $_SESSION["idNiveisAcessosGestaoEmpresa"]=array();
      foreach ($_SESSION["niveisAcessosGestaoEmpresa"] as $ids) {
        $_SESSION["idNiveisAcessosGestaoEmpresa"][]=$ids["id"];
      }

      $_SESSION["idNiveisAcessosGestaoEscolas"]=array();
      foreach ($_SESSION["niveisAcessosGestaoEscolas"] as $ids) {
        $_SESSION["idNiveisAcessosGestaoEscolas"][]=$ids["id"];
      }
   }

?>
<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaEscolas/funcoesAuxiliaresDb.php';

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);

            parent::__construct("Rel-Número Internos de Professores");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
            $this->tipoDisciplina = isset($_GET["tipoDisciplina"])?$_GET["tipoDisciplina"]:"FE";

            $this->html="<html style='margin:0px; margin-bottom:10px;'>
            <head>
                <title>Números Internos</title>
            </head>
            <body>";

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria", "aAdministrativa", "aPedagogica"], "", "", "")){                   
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
             
            $i=0;
            foreach ($this->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND idEntidadeEscola=:idEntidadeEscola", ["A", $_SESSION["idEscolaLogada"]], "nomeEntidade ASC") as $p) {
               
               $i++;
                $margin="";
               if($i%2==0){
                $margin="margin-top:-220px; margin-left:51%;";
               }

               if($i%10==0){
                 $this->html .="<div style='page-break-after: always;'>";
               }
               $this->html .="<div style=' margin:5px; margin-bottom:20px;".$this->text_center.$margin." width:46%; height:180px; border:solid black 1px; font-size:18pt; padding:5px;'>
               <i>angoschool.com</i>

               <br/><br/><strong>".$p->nomeEntidade."</strong>
               <br/><br/><strong style='".$this->vermelha."'>".$p->numeroInternoEntidade."</strong>

               </div>";

               if($i%10==0){
                 $this->html .="</div>";
               }
            }

            $this->exibir("", "Números Internos dos Professores");
        }      
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>
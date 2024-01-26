<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
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

            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){              
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
             
            $i=0;
            foreach ($this->entidades(["numeroInternoEntidade", "nomeEntidade"]) as $p) {
               
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

               <br/><br/><strong>".$p["nomeEntidade"]."</strong>
               <br/><br/><strong style='".$this->vermelha."'>".$p["numeroInternoEntidade"]."</strong>

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
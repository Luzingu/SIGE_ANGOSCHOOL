<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Professor por Tipo de Disciplina");
            $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:2022;
 
            $this->html="<html style='margin-left:20px; margin-right:20px;'>
            <head>
                <title>Mapa de Faltas<title>
            </head>
            <body>";
            if($this->verificacaoAcesso->verificarAcesso(2, "controlPresenca", array(), "")){                   
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){

            $this->html .=$this->cabecalho()."
            <p style='".$this->text_center.$this->bolder.$this->maiuscula.$this->miniParagrafo."'>Mapa de FALTAS REFERENTE AO ANO ".$this->anoCivil."<p/>

            <p style='".$this->miniParagrafo."'>AGENTE: ".valorArray($this->sobreUsuarioLogado, "numeroAgenteEntidade")."</p>
            <p style='".$this->maiuscula."'>NOME COMPLETO: <strong>".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."</strong></p>

            <table style='".$this->tabela."font-size:10pt; width:100%;margin-top:-10px;'>
              <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
              <td style='".$this->border()."'></td>";

            for($i=1;$i<=31; $i++){
              $this->html .="<td style='".$this->border()."'>".completarNumero($i)."</td>";
            }
            $this->html .="<td style='".$this->border()."'>TOT</td><td style='".$this->border()."'>N.º F</td>";

            $this->html .="</tr>";

            $controlPresenca =listarItensObjecto($this->sobreUsuarioLogado, "controlPresenca", ["idEscola=".$_SESSION['idEscolaLogada']]);

            for($mes=1;$mes<=12; $mes++){
                $this->html .="<tr><td rowspan='2' style='".$this->border()."border-top: black solid 2px;'>".nomeMes($mes)."</td>";

                $htmlPresencas="";
                $htmlFaltas="";

                $contFaltas=0;
                $contAusenciasSemImplicacao=0;
                $contAusencias=0;
                $contPresenca=0;

                for($dia=1;$dia<=31; $dia++){

                    $data=$this->anoCivil."-".completarNumero($mes)."-".completarNumero($dia);
                    $semana = date("w", strtotime($data));

                  $faltas="";
                  $presencas="";
                  if($semana!=0){
                    foreach($controlPresenca as $presenca){
                      if($presenca["data"]==$data && $data<=$this->dataSistema){
                        $faltas = isset($presenca["faltas"])?$presenca["faltas"]:"";
                        if($faltas==0){
                            $faltas="";
                        }
                        $contAusencias +=intval($faltas);

                        $presencas = isset($presenca["presencas"])?$presenca["presencas"]:"";
                        if($presencas==0){
                            $presencas="";
                        }
                        $contPresenca +=intval($presencas);

                        if(valorArray($presenca, "implicacao", "controlPresenca")=="T"){
                          $contFaltas++;
                        }else{
                          $contAusenciasSemImplicacao +=intval($faltas);
                        }
                        break;
                      }
                    }
                  }

                  $htmlPresencas .="<td style='".$this->border().$this->text_center."color:green;border-top: black solid 2px;'>".$presencas."</td>";

                  $htmlFaltas .="<td style='".$this->border().$this->text_center."color:red;'>".$faltas."</td>";
                }
                
                  $contFaltas += intdiv($contAusenciasSemImplicacao, 6);

                $this->html .=$htmlPresencas."<td style='".$this->border().$this->text_center."color:green;color:green;border-top: black solid 2px;'>".$contPresenca."</td><td style='".$this->border().$this->text_center."color:red;color:red;border-top: black solid 2px;' rowspan='2'>".$contFaltas."</td></tr><tr>".$htmlFaltas."<td style='".$this->border().$this->text_center."color:red;'>".$contAusencias."</td></tr>";
            }
               

                $this->html .="</table>"; 

            $this->exibir("", "Control de Faltas ".nomeMes($this->mes)." de ".$this->anoCivil, "A4", "landscape");
        }
      
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>
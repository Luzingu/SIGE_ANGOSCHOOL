<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista de Pessoal Docente por Idade");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){                   
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
          $this->lista = $this->entidades([], "docente", "V");
           $this->html="<html>
            <head>
                <title>Lista de Pessoal Docente por Idade</title>
                <style>
                  table tr td{
                    padding:3px;
                  }
                </style>
            </head>
            <body>
            <div class='cabecalho'>
            <div><div style='margin-top:-20px; margin-left:50px; width:200px; position:absolute;' style='".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho()."<p style='".$this->text_center.$this->maiuscula.$this->sublinhado.$this->bolder."'>Idade do Pessoal Docente</p>                
            </div>
            <table style='".$this->tabela." width:100%;'>
              <tr style='".$this->corDanger.$this->bolder."'><td style='".$this->border().$this->text_center."' rowspan='2'></td><td style='".$this->border().$this->text_center."' colspan='2'>Com Formação<br/>Pedagógica</td><td style='".$this->border().$this->text_center."' colspan='2'>Sem Formação<br/>Pedagógica</td>
              <td style='".$this->border().$this->text_center."' colspan='2'>TOTAL</td>
              </tr>
              <tr style='".$this->corDanger.$this->bolder."'>
                <td style='".$this->border().$this->text_center."'>MF</td>
                <td style='".$this->border().$this->text_center."'>F</td>
                <td style='".$this->border().$this->text_center."'>MF</td>
                <td style='".$this->border().$this->text_center."'>F</td>
                <td style='".$this->border().$this->text_center."'>MF</td>
                <td style='".$this->border().$this->text_center."'>F</td>                
              </tr>";

            $linhasR[] = array('label'=>"19 Anos ou menos", "sinalIgual1"=>">=", "valor1"=>19, "sinalIgual2"=>"", "valor2"=>"TOT");
            $linhasR[] = array('label'=>"20-24 Anos", "sinalIgual1"=>"<=", "valor1"=>20, "sinalIgual2"=>">=", "valor2"=>24);
            $linhasR[] = array('label'=>"25-29 Anos", "sinalIgual1"=>"<=", "valor1"=>25, "sinalIgual2"=>">=", "valor2"=>29);
            $linhasR[] = array('label'=>"30-34 Anos", "sinalIgual1"=>"<=", "valor1"=>30, "sinalIgual2"=>">=", "valor2"=>34);
            $linhasR[] = array('label'=>"35-39 Anos", "sinalIgual1"=>"<=", "valor1"=>35, "sinalIgual2"=>">=", "valor2"=>39);
            $linhasR[] = array('label'=>"40 Anos ou mais", "sinalIgual1"=>"<=", "valor1"=>40, "sinalIgual2"=>"", "valor2"=>"TOT");
            $linhasR[] = array('label'=>"Total", "sinalIgual1"=>"", "valor1"=>"TOT", "sinalIgual2"=>"", "valor2"=>"TOT");

            foreach ($linhasR as $a) {

              $retornoPorIdade = $this->contadorDadosPorIdade($this->lista, "TOT", $a["sinalIgual1"], $a["valor1"], $a["sinalIgual2"], $a["valor2"]);

              $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>".$a["label"]."</td><td style='".$this->border().$this->text_center."'>".$this->numeroComFormPedag($retornoPorIdade, "V", "TOT")."</td><td style='".$this->border().$this->text_center."'>".$this->numeroComFormPedag($retornoPorIdade, "V", "F")."</td><td style='".$this->border().$this->text_center."'>".$this->numeroComFormPedag($retornoPorIdade, "F", "TOT")."</td><td style='".$this->border().$this->text_center."'>".$this->numeroComFormPedag($retornoPorIdade, "F", "F")."</td><td style='".$this->border().$this->text_center."'>".$this->numeroComFormPedag($retornoPorIdade, "TOT", "TOT")."</td><td style='".$this->border().$this->text_center."'>".$this->numeroComFormPedag($retornoPorIdade, "TOT", "F")."</td></tr>";
            }

            $this->html .="</table>".$this->assinaturaDirigentes("mengi");

            $this->exibir("", "Lista de Pessoal Docente por Idade-".$this->numAno);
        }
        private function numeroComFormPedag($retornoPorIdadeAnalizar, $comForm, $sexo){
            $contador=0;
            foreach ($retornoPorIdadeAnalizar as $prof) {
                if(seComparador($sexo, $prof["generoEntidade"]) && seComparador($comForm, $prof["comFormPedag"])){
                    $contador++;
                }
            }
            return $contador;
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>
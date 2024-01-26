<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }   
   
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista de Alunos");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", "gerenciadorTurmas", array(), "")){

                $this->listaTurmasLiceu();
            }else{
              $this->negarAcesso();
            }
        }

         private function listaTurmasLiceu(){
            $this->html .="<html style='margin-left:0px; margin-right:0px;'>
            <head>
                <title>Lista dos Cadeirante</title>
                <style>
                  table tr td{
                      padding:3px;
                  }
                  table, p{
                    font-size:11pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:50px; margin-right:50px;'>".$this->fundoDocumento("../../")."

            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho()."
            <p style='".$this->text_center.$this->bolder.$this->sublinhado."'>TURMAS - ".$this->numAno."</p>
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->bolder.$this->corDanger.$this->text_center."'>
                    <td style='".$this->border()."'>N.º/O</td>
                    <td style='".$this->border()."'>Turma</td>
                    <td style='".$this->border()."'>Período</td>
                    <td style='".$this->border()."'>N.º Sala</td>
                    <td style='".$this->border()."'>Director(a)</td>
                </tr>";
            
            $i=0;
            foreach ($this->turmasEscola() as $tur) {
                $i++;
                $forCurso="";
                if($tur["classe"]>=10){
                    $forCurso=nelson($tur, "abrevCurso")."-";
                }
                if($tur["periodoT"]=="Automático"){
                    $tur["periodoT"]="";
                }
                $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border()."'>".$forCurso.classeExtensa($this, $tur["idPNomeCurso"], $tur["classe"])."-".$tur["designacaoTurma"]."</td>
                    <td style='".$this->border()."'>".$tur["periodoT"]."</td>
                    <td style='".$this->border().$this->text_center."'>".completarNumero(nelson($tur, "numeroSalaTurma"))."</td>
                    <td style='".$this->border()."'>".nelson($tur, "nomeEntidade")."</td>
                </tr>";
            }
            $this->html .="</table>";
            $this->html .="<p style='padding-left:10px; padding-right:10px;".$this->maiuscula."'>".$this->rodape().".</p>
            <div style='".$this->maiuscula."'>".$this->assinaturaDirigentes("mengi")."</div>";
            
            $this->exibir("", "Directores de Turma-".$this->numAno);
        }
    }
    new lista(__DIR__);
?>
<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class listaTurmas extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->nomeTurmaAluno="";
            $this->designacaoTurmaAluno ="";
            $this->perioTurmaAluno="";
            $this->numSalaTurmaAluno ="";
            parent::__construct("Rel-Lista dos Alunos Reconfirmados");

            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:null;

            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;

            $this->numAno();
            $this->nomeCurso();

            if($this->verificacaoAcesso->verificarAcesso("", "reconfirmados", array(), "")){                                
                $this->visualizar();
            }else{
              $this->negarAcesso();
            }

        }

         private function visualizar(){

            $this->html .="<html style='margin-left:0px; margin-right:0px;'>
            <head>
                <title>Lista geral dos alunos reconfirmados</title>
                <style>
                  table tr td{
                      padding:3px;
                  }
                  table, p{
                    font-size:11pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:40px; margin-right:40px;'>
            ".$this->fundoDocumento("../../", "horizontal").$this->cabecalho()."
            <p style='".$this->bolder.$this->text_center."'>RELAÇÃO NOMINAL DOS ALUNOS FINALISTAS<br/>PROMOÇÃO - ".$this->numAno."</p>";

            if($this->tipoCurso=="pedagogico"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->miniParagrafo.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
            }else if($this->tipoCurso=="tecnico"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }else{
                $top=190;
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }

            $this->html .="<br/><br/><table style='".$this->tabela." width:100%;'>
            <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                <td style='".$this->border()."' rowspan='2'>N.º</td>
                <td style='".$this->border()."' rowspan='2'>NOME COMPLETO</td>
                <td style='".$this->border()."' rowspan='2'>SEXO</td>
                <td style='".$this->border()."' colspan='2'>ACTIVIDADE A PAGAR</td>
                <td style='".$this->border()."width:120px;' rowspan='2'>OBS:</td>
            </tr>
            <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                <td style='".$this->border()."'>CERIMÓNIA</td>
                <td style='".$this->border()."'>GALA</td>
            </tr>";

            $i=0;

            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "dataNascAluno", "sexoAluno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatCurso"=>$this->idPCurso, "reconfirmacoes.classeReconfirmacao"=>13], ["escola", "reconfirmacoes"], "", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idPAno, $this->idPCurso));

            foreach($alunos as $a){

                $i++;
                $this->html .="
                <tr style='"."'>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border()."'>".$a["nomeAluno"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["sexoAluno"]."</td>
                    <td style='".$this->border()."'></td>
                    <td style='".$this->border()."'></td>
                    <td style='".$this->border()."'></td>
                </tr>";
            }
            

            
            $this->html .="</table>
            </div><div><br/>
            <div>".$this->porAssinatura("O(a) Responsável", "", "", 25)."</div>";

            $this->exibir("", "Lita geral dos alunos matricula-".$this->numAno); 
        }
    }

new listaTurmas(__DIR__);
    
    
  
?>
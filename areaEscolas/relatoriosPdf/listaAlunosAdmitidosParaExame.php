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
             <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>

            ".$this->fundoDocumento("../../").$this->cabecalho()."
            <p style='".$this->bolder.$this->text_center."'>ALUNOS ADMITIDOS PARA EXAME - ".$this->numAno."</p>";

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
                <td style='".$this->border()."'>N.º</td>
                <td style='".$this->border()."'>NOME COMPLETO</td>
                <td style='".$this->border()."'>N.º INTERNO</td>
                <td style='".$this->border()."'>SEXO</td>
                <td style='".$this->border()."'>TURMA</td>
            </tr>";

            $i=0;

            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "numeroInterno", "sexoAluno", "reconfirmacoes.designacaoTurma"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatCurso"=>$this->idPCurso, "reconfirmacoes.classeReconfirmacao"=>12, "escola.estadoDeDesistenciaNaEscola"=>"A", "reconfirmacoes.estadoReconfirmacao"=>"A"], ["reconfirmacoes", "escola"],"", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idPAno, $this->idPCurso));

            foreach($alunos as $a){
                $i++;
                $this->html .="
                <tr style='"."'>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border()."'>".$a["nomeAluno"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["numeroInterno"]."</td>
                    <td style='".$this->border().$this->text_center."'>".generoExtenso($a["sexoAluno"])."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["reconfirmacoes"]["designacaoTurma"]."</td>
                </tr>";
            }
            

            
            $this->html .="</table>
            </div><p style='".$this->text_center."'>".$this->rodape()."</p>".$this->assinaturaDirigentes("mengi");

            $this->exibir("", "Lita geral dos alunos matricula-".$this->numAno); 
        }
    }

new listaTurmas(__DIR__);
    
    
  
?>
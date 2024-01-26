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
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            
            
            $this->numAno();

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
                    font-size:16pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:40px; margin-right:40px;'>
            ".$this->fundoDocumento("../../", "horizontal").$this->cabecalho()."
            <p style='".$this->bolder.$this->text_center."'>LISTA GERAL DOS ALUNOS MATRICULADOS PARA O ANO LECTIVO ".$this->numAno."</p>

            <table style='".$this->tabela." width:100%;'>
            <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                <td style='".$this->border()."'>N.º</td>
                <td style='".$this->border()."'>NOME COMPLETO</td>
                <td style='".$this->border()."'>IDADE</td>
                <td style='".$this->border()."'>SEXO</td>
                <td style='".$this->border()."'>CURSO</td>
                <td style='".$this->border()."'>CLASSE</td>
                <td style='".$this->border()."'>TURMA</td>
            </tr>"; 

            $i=0;
            $condicao2=array();
            

            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "dataNascAluno", "sexoAluno", "reconfirmacoes.idMatCurso", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idMatCurso"=>$this->idPCurso, "reconfirmacoes.estadoReconfirmacao"=>"A"], ["reconfirmacoes"], "", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idPAno, $idPCurso));

            $alunos = $this->anexarTabela2($alunos, "nomecursos", "reconfirmacoes", "idPNomeCurso", "idMatCurso");

            foreach($alunos as $a){

                $i++;
                $this->html .="
                <tr style='"."'>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border()."'>".$a->nomeAluno."</td>
                    <td style='".$this->border().$this->text_center."'>".calcularIdade($this->ano, $a["dataNascAluno"] )."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["sexoAluno"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["abrevCurso"]."</td>
                    <td style='".$this->border().$this->text_center."'>".classeExtensa($this, $a["reconfirmacoes"]["idMatCurso"], $a["reconfirmacoes"]["classeReconfirmacao"])."</td>
                    <td style='".$this->border().$this->text_center."'>".valorArray($a, "designacaoTurma", "reconfirmacoes")."</td>
                </tr>";
            }
            

            
            $this->html .="</table>
            </div><div>
            <p style='padding-left:10px; padding-right:10px;'>".$this->rodape().".</p>
            <div>".$this->assinaturaDirigentes(7)."</div>";

            $this->exibir("", "Lita geral dos alunos matricula-".$this->numAno, "", "A4", "landscape"); 
        }

        private function dadosDeTurma($idPCurso, $classe, $turma){
            foreach ($this->sobreTurma as $l){
                if(($l["idPNomeCurso"]==$idPCurso || $classe<=9) && $l["classe"]==$classe && $l["nomeTurma"]==$turma){

                    $this->perioTurmaAluno=$l["periodoT"];
                    $this->numSalaTurmaAluno =$l["numeroSalaTurma"];
                    break;
                }
            }
        }
    }

new listaTurmas(__DIR__);
    
    
  
?>
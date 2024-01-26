<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class listaTurmas extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Relação Nominal");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null; 

                      
            $this->nomeCurso();
            $this->numAno();
            if($this->verificacaoAcesso->verificarAcesso("", ["relatorioDasTurmas"], [$this->classe, $this->idPCurso], "")){
                $this->listaTurmasLiceu();
            }else{
                $this->negarAcesso();
            } 
        }

         private function listaTurmasLiceu(){
            $lista = $alunos =$this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), ["nomeAluno", "numeroInterno", "sexoAluno", "escola.idGestLinguaEspecialidade", "escola.idGestDisEspecialidade", "idPMatricula", "reconfirmacoes.estadoDesistencia"]);
            $this->nomeTurma("", "", "", $this->idPAno);
             
             
            $parte1=array();
            $parte2=array();

            $totF=0;
            $i=0;
            $alunos=array();
              foreach ($lista as $aluno) {

                $estadoDesistencia = valorArray($aluno, "estadoDesistencia", "reconfirmacoes");
                 
                if(!($estadoDesistencia=="N" || $estadoDesistencia=="D" || $estadoDesistencia=="F")){
                    $i++;
                    if($aluno["sexoAluno"]=="F"){
                        $totF++;
                    }
                    if($i<=30){
                        $parte1[]=$aluno;
                    }else{
                        $parte2[]=$aluno;
                    }
                    $alunos[]=$aluno;
                }
                
              }

            $this->html .="<html>
            <head>
                <title>Relação Nominal dos Alunos</title>
                <style>
                  p, td{
                      font-size:11pt !important;
                  }
                
                </style>
            </head>
            <body style='margin-bottom:-20px; margin-top:-30px;'>".$this->fundoDocumento("../../../")."
            <div style='position: absolute;'>
                <div style='margin-top: 0px; width:270px;'><br/>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho("sim", $this->text_center, "")."
            <p style='".$this->text_center.$this->bolder.$this->sublinhado."margin-top:-10px;'>Relação Nominal dos Alunos - ".$this->numAno."</p>"; 
           
            
            if($this->classe>=10){
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="
                 <p style='".$this->miniParagrafo."'>Curso: <strong>".$this->areaFormacaoCurso."</strong></p>
                 <p style='".$this->miniParagrafo."'>Opção: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="
                 <p style='".$this->miniParagrafo."'>Área de Formação: <strong>".$this->areaFormacaoCurso."</strong></p>
                 <p style='".$this->miniParagrafo."'>Curso: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $this->html .="<p style='".$this->miniParagrafo."'>Curso: <strong>".$this->nomeCurso."</strong></p>";
                }
            }
            $this->html .="<p style='".$this->miniParagrafo."'>Classe: <strong>".$this->classeExt." / ".$this->nomeTurma()."</strong></p>
            <p style='".$this->miniParagrafo."'>Período: <strong>".valorArray($this->sobreTurma, "periodoT")."</strong> / Sala n.º: <strong> ".completarNumero(valorArray($this->sobreTurma, "numeroSalaTurma"))."</strong></p>
            
            <p style='".$this->miniParagrafo."'>Director: <strong>".valorArray($this->sobreTurma, "nomeEntidade")."</p>
            <p style='"."'>Total: <strong> ".completarNumero(count($alunos))."</strong> / F: <strong>".completarNumero($totF)."</strong></p>";
            
            $per="";
            if($this->periodoTurma=="reg"){
                $per="Regular";
            }else {
                $per="Pós-Laboral";
            }

            if(count($alunos)<=30){
                $this->html .="<div style='margin-left:10%; width:80%; height:660px;'>";
            }else{
                $this->html .="<div style='width:49%; height:660px;'>";
            }
            
            $this->html .="
            <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>
                        <td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."width:20px;'>N.º</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."width:230px;'>Nome Completo</td><td style='".$this->text_center.$this->bolder.$this->border()."width:70px;'>Sexo</td>
                    </tr>
                ";

            $n=0;
            foreach ($parte1 as $aluno) {
                $n++;
                $this->html .="<tr><td style='".$this->text_center.$this->border()."'>".completarNumero($n)."</td><td style='".$this->border()."'>".$aluno["nomeAluno"]."</td><td style='".$this->text_center.$this->border()."'>".$aluno["sexoAluno"]."</td></tr>";
            } 

            $this->html .="</table>
            </div>";

            if(count($alunos)>=31){
                $this->html .="<div style='width:49%; height:660px; margin-top:-660px; margin-left:51%;'>
            <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>
                        <td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."width:20px;'>N.º</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."width:230px;'>Nome Completo</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."width:70px;'>Sexo</td>
                    </tr>";

                 foreach ($parte2 as $aluno) {
                     $n++;
                     $this->html .="<tr><td style='".$this->text_center.$this->border()."'>".completarNumero($n)."</td><td style='".$this->border()."'>".$aluno["nomeAluno"]."</td><td style='".$this->text_center.$this->border()."'>".$aluno["sexoAluno"]."</td></tr>";
                 } 

                 $this->html .="</table>
                </div>";
            }

            $this->html .="
            <div><p style='".$this->text_center."margin-top:20px; margin-bottom:15px;'>".$this->rodape()."</p>".$this->assinaturaDirigentes("mengi")."</div>
            </div>"; 
            

           $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Lista_Turmas", "Relação Nominal-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->numAno, "Relacao_Nominal-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPAno);
        }
    }

new listaTurmas(__DIR__);
?>
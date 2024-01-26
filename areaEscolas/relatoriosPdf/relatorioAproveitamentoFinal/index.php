<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:null;
            $this->idPCurso = isset($_GET["idCurso"])?$_GET["idCurso"]:null;
            $this->obs = isset($_GET["obs"])?$_GET["obs"]:null; 

            parent::__construct("Rel-Relatório de Aproveitamento Final");          
            $this->nomeCurso();
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", ["pautaGeral1"], [], "")){
                $this->exibirLista();
            }else{
                $this->negarAcesso();
            }
            
        }

         private function exibirLista(){

            $condAlunos = array();
            $label="";
            if($this->obs!="aprovado" && $this->obs!="reprovado" && $this->obs!="desistente" && $this->obs!="exclFalta" && $this->obs!="matAnulada"){
              $this->obs="aprovado";
            }
            $label1="";

            $condicaoAluno = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "escola.idMatCurso"=>$this->idPCurso];

            if($this->obs=="aprovado"){
              $condAlunos = ["reconfirmacoes.observacaoF"=>['$in'=>array('A', 'TR')]];
              $label="Alunos Aprovados";
              $label1="Alunos_Aprovados";
            }else if($this->obs=="reprovado"){
              $condAlunos = ["reconfirmacoes.observacaoF"=>['$in'=>array('NA')]];
              $label="Alunos Reprovados";
              $label1="Alunos_Reprovados";
            }else if($this->obs=="desistente"){
              $condAlunos = ["reconfirmacoes.observacaoF"=>['$in'=>array('D')]];
              $label="Alunos Desistentes";
              $label1="Alunos_Desistentes";
            }else if($this->obs=="exclFalta"){
              $condAlunos = ["reconfirmacoes.observacaoF"=>['$in'=>array('F')]];
              $label="Alunos Excluídos por Faltas";
              $label1="Alunos_Excluídos_por_Faltas";
            }else if($this->obs=="matAnulada"){
              $label ="Alunos que Anularam Matriculas";
              $label1 ="Alunos_que_Anularam-Matriculas";
              $condAlunos = ["reconfirmacoes.observacaoF"=>['$in'=>array('N')]];
            }

            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "reconfirmacoes.observacaoF", "nomeAluno", "numeroInterno", "reconfirmacoes.classeReconfirmacao", "escola.classeActualAluno", "reconfirmacoes.designacaoTurma", "sexoAluno", "dataNascAluno"], $condicaoAluno, ["reconfirmacoes", "escola"], "", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idPAno, $this->idPCurso));

            $arrayAluno =array();
            $totF=0;
            foreach($alunos as $aluno){
                if($aluno["sexoAluno"]=="F"){
                    $totF++;
                }
              if($this->obs=="aprovado" && (nelson($aluno, "observacaoF", "reconfirmacoes")=="A" || nelson($aluno, "observacaoF", "reconfirmacoes")=="TR")){
                $arrayAluno[]=$aluno;
              }else if($this->obs=="reprovado" && nelson($aluno, "observacaoF", "reconfirmacoes")=="NA"){
                $arrayAluno[]=$aluno;
              }else if($this->obs=="desistente" && nelson($aluno, "observacaoF", "reconfirmacoes")=="D"){
                $arrayAluno[]=$aluno;
              }else if($this->obs=="exclFalta" && nelson($aluno, "observacaoF", "reconfirmacoes")=="F"){
                $arrayAluno[]=$aluno;
              }else if($this->obs=="matAnulada" && nelson($aluno, "observacaoF", "reconfirmacoes")=="N"){
                $arrayAluno[]=$aluno;
              }
            }
            $arrayAluno = $this->anexarTabela2($arrayAluno, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");



            $this->html .="<html>
            <head>
                <title>".$label."</title>
            </head>
            <body><div class='cabecalho'>
            <div style='position: absolute;'><div style='margin-top: 0px; width:250px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho()."<p style='".$this->text_center.$this->bolder.$this->maiuscula."'>".$label." - ".$this->numAno."</p>";

            $cabecalho[] = array("titulo"=>"Nº", "tituloDb"=>"numero", "css"=>$this->text_center);
            $cabecalho[] = array("titulo"=>"Nome Completo", "tituloDb"=>"nomeAluno", "css"=>"");
            $cabecalho[] = array("titulo"=>"Número Interno", "tituloDb"=>"numeroInterno", "css"=>$this->text_center);  
            $cabecalho[] = array("titulo"=>"Idade", "tituloDb"=>"idade", "css"=>$this->text_center);            
            $cabecalho[] = array("titulo"=>"Classe", "tituloDb"=>"classeAnterior", "css"=>$this->text_center);
            $cabecalho[] = array("titulo"=>"Turma", "tituloDb"=>"designacaoTurma", "css"=>$this->text_center);
            
            if($this->idPCurso=="EP"){
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>ENSINO PRIMÁRIO</p>";
            }else if($this->idPCurso=="EB"){
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>ENSINO BÁSICO</p>";
            }else if($this->tipoCurso=="pedagogico"){
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
            }else if($this->tipoCurso=="tecnico"){
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }else{
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }
            

            $this->html .="<p style='".$this->maiuscula."'>TOTAL: <strong> ".completarNumero(count($arrayAluno))."</strong> / F: <strong>".completarNumero($totF)."</strong></p>
            <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>";
                    foreach ($cabecalho as $cab) {
                        $this->html .="<td style='".$cab["css"].$this->border()."'>".$cab["titulo"]."</td>";
                    }                        
                   $this->html .="</tr>";

            $n=0;
            foreach ($arrayAluno as $aluno) {
                $n++;
                $this->html .="<tr>";
                foreach ($cabecalho as $cab) {
                    $tituloDb = $cab["tituloDb"];
                    $valor="";
                    if($tituloDb=="numero"){
                        $valor=$n;
                    }else if($tituloDb=="classeAnterior"){
                        $valor=classeExtensa($this, $this->idPCurso, $aluno["reconfirmacoes"]["classeReconfirmacao"]);
                    }else if($tituloDb=="designacaoTurma"){
                        $valor=$aluno["reconfirmacoes"]["designacaoTurma"];
                    }else if($tituloDb=="idade"){
                        $valor=calcularIdade($this->ano, $aluno["dataNascAluno"])." Anos";
                    }else{
                       $valor=$aluno[$tituloDb]; 
                    }
                    $this->html .="<td style='".$cab["css"].$this->border()."'>".$valor."</td>";
                }
                $this->html .="</tr>";
            } 

            $this->html .="</table>
            <div>".$this->assinaturaDirigentes(["Pedagógico", "Administrativo"])."</div>";
            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", $label."-".$this->nomeCursoAbr."-".$this->numAno, $label1."-".$this->idPAno);

        }
    }

new lista(__DIR__);
    
    
  
?>
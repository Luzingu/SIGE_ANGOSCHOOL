<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class pautas extends funcoesAuxiliares{

        function __construct(){
            $this->mesPagamentoApartir = isset($_GET["mesPagamentoApartir"])?$_GET["mesPagamentoApartir"]:"";
            $this->trimestreApartir = 1;
            $this->seListarTodaPauta = "";
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null; 
            $this->tamanhoFolha =isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
            if($this->tamanhoFolha!="A4" && $this->tamanhoFolha!="A3"){
                $this->tamanhoFolha="A3";
            }

            parent::__construct("Rel-Exemplar Mapa de Avaliação de Alunos");

            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->tipoPauta ="anoActual";

            $this->nomeCurso();
            $this->numAno();           

            if($this->verificacaoAcesso->verificarAcesso("", ["relatorioDasTurmas"], [$this->classe, $this->idPCurso], "")){  
                $this->mapa();                          
                
            }else{
              $this->negarAcesso();
            }
        }


        private function mapa(){
            $alunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), ["nomeAluno", "numeroInterno", "sexoAluno", "idPMatricula"]);
            $this->nomeTurma("", "", "", $this->idPAno);
             
             
            $totF=0;
            foreach ($alunos as $aluno) {
                if($aluno["sexoAluno"]=="F"){
                    $totF++;
                }
            }

            $this->html .="<html style='margin:0px;'>
            <head>
                <title>Lista de Turma</title>
                <style>
                  table tr td{
                      padding:3px;
                      font-size:11pt;
                  }
                
                </style>
            </head>
            <body style='margin:20px;'>".$this->fundoDocumento("../../../", "horizontal");

            $mesPorTrimestre[1][] = 10;
            $mesPorTrimestre[1][] = 11;
            $mesPorTrimestre[1][] = 12;

            $mesPorTrimestre[2][] = 1;
            $mesPorTrimestre[2][] = 2;
            $mesPorTrimestre[2][] = 3;

            $mesPorTrimestre[3][] = 4;
            $mesPorTrimestre[3][] = 5;
            $mesPorTrimestre[3][] = 6;

            $trimestres[1]="I";
            $trimestres[2]="II";
            $trimestres[3]="III";

            for ($pl=1; $pl<=3; $pl++){

                $this->html .="<div style='page-break-after: always;'>".$this->cabecalho()."

                <p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE AVALIAÇÃO DOS ALUNOS DO ".$trimestres[$pl]." TRIMESTRE - ".$this->numAno."</p>";                 
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }  
                

                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CLASSE: <strong>".$this->classeExt."</strong> / ".$this->nomeTurma()."</p>

                <p style='".$this->maiuscula.$this->miniParagrafo."'>PERÍODO: <strong>".valorArray($this->sobreTurma, "periodoT")."</strong> / SALA N.º: <strong> ".completarNumero(valorArray($this->sobreTurma, "numeroSalaTurma"))."</strong></p>

                <p style='".$this->maiuscula."'>TOTAL: <strong> ".completarNumero(count($alunos))."</strong> / F: <strong>".completarNumero($totF)."</strong></p>

                <p style='".$this->maiuscula."'>DISCIPLINA: ________________________________________________</p><br/>";
                
                $per="";
                if($this->periodoTurma=="reg"){
                    $per="Regular";
                }else {
                    $per="Pós-Laboral";
                }
                
                $this->html .="
                <table style='".$this->tabela." width:100%;'>
                        <tr style='".$this->corDanger.$this->bolder."'>
                    <td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."width:20px;' rowspan='2'>N.º</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."' rowspan='2'>Nome Completo</td>";
                for($i=1; $i<=3; $i++){
                    $this->html .="<td style='".$this->text_center.$this->border()."' colspan='4'>".nomeMes($mesPorTrimestre[$pl][($i-1)])."</td>";
                }
                $this->html .="<td colspan='4' style='".$this->bolder.$this->text_center.$this->border()."'>CLASSIFICAÇÃO</td></tr>";


                $this->html .="<tr style='".$this->corDanger.$this->bolder."'>";

                for($t=1; $t<=3; $t++){
                  for($i=1; $i<=4; $i++){
                    $this->html .="<td style='".$this->text_center.$this->border()."'>".$i.".ª AVAL</td>";
                  }
                }
                $this->html .="<td style='".$this->text_center.$this->border()."'>MAC</td><td style='".$this->text_center.$this->border()."'>NPP</td><td style='".$this->text_center.$this->border()."'>NPT</td><td style='".$this->text_center.$this->border()."'>MT</td></tr>";
                $n=0;
                foreach ($alunos as $aluno) {
                    $n++;
                    $this->html .="<tr><td style='".$this->text_center.$this->border()."'>".completarNumero($n)."</td><td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>";

                    for($t=1; $t<=3; $t++){
                          for($i=1; $i<=4; $i++){
                            $this->html .="<td style='".$this->text_center.$this->border()."'></td>";
                          }
                    }
                    $this->html .="<td style='".$this->text_center.$this->border()."'></td><td style='".$this->text_center.$this->border()."'></td><td style='".$this->text_center.$this->border()."'></td><td style='".$this->text_center.$this->border()."'></td>";
                    $this->html .="</tr>";
                } 
                $this->html .="</table><p style='".$this->text_center."'>O(a) Professor(a)</p>
                <p style='".$this->text_center."'>______________________________</p></div>";
            }

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Lista_Turmas", "Mapa de Avaliação dos Alunos-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->numAno, "Mapa_Avaliacao-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPAno, "A4", "landscape");
        }       
    }
    new pautas();  
?>
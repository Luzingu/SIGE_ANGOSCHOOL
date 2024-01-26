<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class pautas extends funcoesAuxiliares{
        

        function __construct($caminhoAbsoluto){
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

            if($this->classe<=6){
                $this->notaMinima=5;
            }else{
                $this->notaMinima=10;
            }
            parent::__construct("Rel-Mapa de Control de Faltas");

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

            $this->html .="<html>
            <head>
                <title>Lista de Turma</title>
                <style>
                  table tr td{
                      padding:3px;
                      font-size:11pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:-17px; '>".$this->fundoDocumento("../../../").$this->cabecalho()."

            <p style='".$this->text_center.$this->bolder.$this->sublinhado."'>LISTA DAS FALTAS DOS ALUNOS DO MÊS DE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; A &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DE ".$this->numAno."</p>"; 
            

            if($this->classe>=10){
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }  
            }           

            $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CLASSE: <strong>".$this->classeExt."</strong> / ".$this->nomeTurma()."</p>

            <p style='".$this->maiuscula.$this->miniParagrafo."'>PERÍODO: <strong>".valorArray($this->sobreTurma, "periodoT")."</strong> / SALA N.º: <strong> ".completarNumero(valorArray($this->sobreTurma, "numeroSalaTurma"))."</strong></p>

            <p style='".$this->maiuscula."'>TOTAL: <strong> ".completarNumero(count($alunos))."</strong> / F: <strong>".completarNumero($totF)."</strong></p>";
            
            $per="";
            if($this->periodoTurma=="reg"){
                $per="Regular";
            }else {
                $per="Pós-Laboral";
            }
            
            $this->html .="<div>
            <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger.$this->bolder."'>
                        <td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."width:20px;' rowspan='2'>N.º</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."width:200px;' rowspan='2'>Nome Completo</td>";
                    for($i=1; $i<=5; $i++){
                        $this->html .="<td style='".$this->text_center.$this->border()."' colspan='5'>".diaSemana($i)."</td>";
                    }
                    $this->html .="</tr>";


                    $this->html .="<tr style='".$this->corDanger.$this->bolder."'>";

                    for($t=1; $t<=5; $t++){
                      for($i=1; $i<=5; $i++){
                        $this->html .="<td style='".$this->text_center.$this->border()."'>".$i."º</td>";
                      }
                    }
                    $this->html .="</tr>";

            $n=0;
            foreach ($alunos as $aluno) {
                $n++;
                $this->html .="<tr><td style='".$this->text_center.$this->border()."'>".completarNumero($n)."</td><td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>";

                for($t=1; $t<=5; $t++){
                      for($i=1; $i<=5; $i++){
                        $this->html .="<td style='".$this->text_center.$this->border()."'></td>";
                      }
                }
                $this->html .="</tr>";
            } 

            $this->html .="</table>
            </div>
            <div></div>";

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Lista_Turmas", "Mapa de Controlo de Faltas-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->numAno, "Mapa_Controlo_Faltas-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPAno); 
        }
        
        private function retornaNotas($idPMatricula){
            $retorno="";
            $this->totalNotas=0;
            foreach ($this->disciplinas as $disciplina) {
                $retorno .="<td style='".$this->border()."'></td><td style='".$this->border()."'></td>";
            }
            return $retorno;
        }       
    }

new pautas(__DIR__);
  
?>
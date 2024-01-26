<?php 

    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class listaTurmas extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Exemplar de Mini Pautas");

            $this->idPAno =  $this->idAnoActual;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null; 

            $this->nomeCurso();
            $this->numAno();

            $this->html="<html>
            <head>
                <title>Exemplar de  Mini-Pauta</title>
            </head>
            <body>".$this->fundoDocumento("../../../", "horizontal");
            $this->listaTurmasLiceu();            
        }

         private function listaTurmasLiceu(){

            $alunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), ["nomeAluno", "numeroInterno", "sexoAluno", "idPMatricula"]);
            $this->nomeTurma("", "", "", $this->idPAno);

            $disc = $this->disciplinas ($this->idPCurso, $this->classe, $this->periodoTurma, "", array(), array(), ["idPNomeDisciplina"]);

            $camposAvaliacao = $this->camposAvaliacaoAlunos($this->idAnoActual, $this->idPCurso, $this->classe, $this->periodoTurma, valorArray($disc, "idPNomeDisciplina"));

            $htmlAluno="";
            $totM=0;
            $totF=0;
            foreach ($alunos as $aluno){       
                if($aluno["sexoAluno"]=="M"){
                    $totM++;
                }else{
                    $totF++;
                }
            }


            $this->html .="
            <div style='position: absolute;'><div style='margin-top: 50px; width:250px;'>".$this->assinaturaDirigentes(8)."</div></div>
           ".$this->cabecalho()."
             <p style='".$this->bolder.$this->sublinhado.$this->text_center."'> MINI-PAUTA - ".$this->numAno."</p>";
                        
             $this->html .="<p>PROFESSOR(A): ___________________________________________</p>
             <p>DISCIPLINA: _____________________________________________</p>";
            
            $topMapaEstatisco=-105;
            if($this->classe>=10){

                $topMapaEstatisco=-145;
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $topMapaEstatisco=-125;
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }            
                
            } 

            $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CLASSE: <strong>".$this->classe.".ª</strong></p>
            <p style='".$this->maiuscula."'>TURMA: <strong>".$this->nomeTurma()."</strong></p></div>";
    

            $this->html .="<div style='position:absolute;'>
            <div style='margin-top:".$topMapaEstatisco."px; width:250px; margin-left:780px; '>
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->border()."'></td><td style='".$this->border().$this->text_center."'>MF</td><td style='".$this->border().$this->text_center."'>F</td>
                </tr>
                <tr>
                    <td style='".$this->border()." width:110px;'>TOTAL</td><td style='".$this->text_center.$this->border()."'>".completarNumero(count($alunos))."</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totF)."</td>
                </tr>
                <tr>
                    <td style='".$this->border()."'>APROVADOS</td><td style='".$this->text_center.$this->border()."'></td><td style='".$this->text_center.$this->border()."'></td>
                </tr>
                <tr>
                    <td style='".$this->border()."'>REPROVADOS</td><td style='".$this->text_center.$this->border()."'></td><td style='".$this->text_center.$this->border()."'></td>
                </tr>
            </table>
            </div></div>

            <table style='".$this->tabela." width:100%;'>
            <tr style='".$this->corDanger."'><td style='".$this->border().$this->text_center."' rowspan='2'>Nº</td><td style='".$this->border().$this->text_center."' rowspan='2' style='width:250px;'>Nome Completo</td><td style='".$this->border().$this->text_center."' rowspan='2'>S</td>";

            foreach($this->trimestres as $p){
                $this->html .="<td colspan='".count(array_filter($camposAvaliacao, function ($mamale) use ($p){
                        return $mamale["periodo"]==$p["identificador"];
                    }))."' style='".$this->bolder.$this->border().$this->text_center."'>".$p["designacao"]."</td>";
            }
            $this->html .="</tr>
            <tr style='".$this->corDanger."'>";
            foreach ($camposAvaliacao as $cabeca) {
                $this->html .="<td style='".$this->bolder.$this->border().$this->text_center."'>".$cabeca["designacao2"]."</td>";
            }
            $this->html .="</tr>";

            $i=0;
            foreach ($alunos as $aluno) {
                $i++;
                if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$aluno["nomeAluno"]."</td><td style='".$this->text_center.$this->border()."'>".$aluno["sexoAluno"]."</td>";
                foreach ($camposAvaliacao as $cab) {
                        $this->html .="<td style='".$this->border()."'></td>";
                }
                $this->html .="</tr>";
            }

            $this->html .="</table>
            <p style='".$this->text_center."'>O(a) Professor(a)</p>
            <p style='".$this->text_center."'>______________________________</p>";

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Lista_Turmas", "Exemplar de Mini-Pautas-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->numAno, "Exemplar-Mini-Pautas-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPAno, "A4", "landscape");
        }
    }



new listaTurmas(__DIR__);
    
    
  
?>
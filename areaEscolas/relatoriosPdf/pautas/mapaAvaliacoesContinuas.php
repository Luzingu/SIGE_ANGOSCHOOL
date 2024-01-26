<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class listaTurmas extends funcoesAuxiliares{
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa de Aval. Cont."); 
            if(isset($_GET["idPAno"])){
                $this->idPAno = $_GET["idPAno"];
            }else{
                $this->idPAno = $this->idAnoActual;
            }
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null;
            $this->trimestreApartir = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;
            $this->idPDisciplina = isset($_GET["idPDisciplina"])?$_GET["idPDisciplina"]:null;

            if($this->trimestreApartir==1){
                $this->trimestre="I";
            }else if($this->trimestreApartir==2){
                $this->trimestre="II";
            }else{
                $this->trimestre="III";
            }
            $this->nomeCurso();
            $this->numAno();

            $this->html="<html style='margin: 10px; margin-right: 20px; margin-left: 20px;'>
            <head>
                <title>Mini-Pauta</title>
            </head>
            <body>".$this->fundoDocumento("../../../", "horizontal");

             $this->dadosProfessor = $this->selectCondClasseCurso("array", "divisaoprofessores", [], ["classe"=>$this->classe, "nomeTurmaDiv"=>$this->turma, "idDivAno"=>$this->idPAno, "idPNomeDisciplina"=>$this->idPDisciplina, "idPEscola"=>$_SESSION["idEscolaLogada"]], $this->classe, ["idPNomeCurso"=>$this->idPCurso]);

            if($this->verificacaoAcesso->verificarAcesso(3, [""], [$this->classe, $this->idPCurso], "")){
                $this->exibirRelatorio();
            }else{
                if(count($this->dadosProfessor)>0){
                    $this->exibirRelatorio();
                }else{
                    $this->negarAcesso();
                }
            }
        }

         private function exibirRelatorio(){
            if($this->classe<=6){
                $this->notaMinima=5;
            }else{
                $this->notaMinima=10;
            }
            $this->nomeTurma();
            $disciplina = $this->selectArray("nomedisciplinas", ["nomeDisciplina"], ["idPNomeDisciplina"=>$this->idPDisciplina]);

            $tipo="pautas";
            if($this->idPAno!=$this->idAnoActual){
                $tipo="arquivo_pautas";
            }
            $camposAvaliacao = $this->camposAvaliacaoAlunos($this->idPAno, $this->idPCurso, $this->classe, $this->periodoTurma, $this->idPDisciplina, $this->trimestre);
            $campos=[$tipo.".avaliacoesContinuas".$this->trimestre, "nomeAluno", "sexoAluno"];

            foreach($camposAvaliacao as $campo){
                $campos[]=$tipo.".".$campo["identUnicaDb"];
            }

            $alunos = $this->miniPautas($this->idPCurso, $this->classe, $this->turma, "", $this->idPDisciplina, $tipo, $this->idPAno, $campos); 

            $n=0; $totM=0; $totMA=0; $totMD=0; $totMR=0; $totF=0; $totFA=0; $totFD=0; $totFR=0;

            foreach ($alunos as $aluno){
                $n++;
                if($aluno["sexoAluno"]=="M"){
                    $totM++;
                    if(nelson($aluno, "mt".$this->trimestre, $tipo)>=$this->notaMinima){
                        $totMA++;
                    }else if(nelson($aluno, "mt".$this->trimestre, $tipo)==0 || nelson($aluno, "mt".$this->trimestre, $tipo)==NULL || nelson($aluno, "mt".$this->trimestre, $tipo)==""){
                         $totMD++;
                    }else{
                        $totMR++;
                    }
                }else{
                    $totF++;
                    if(nelson($aluno, "mt".$this->trimestre, $tipo)>=$this->notaMinima){
                        $totFA++;
                    }else if(nelson($aluno, "mt".$this->trimestre, $tipo)==0 || nelson($aluno, "mt".$this->trimestre, $tipo)==NULL || nelson($aluno, "mt".$this->trimestre, $tipo)==""){
                        $totFD++;
                    }else{
                        $totFR++;
                    }
                }
            }

            $this->html .="<div class='cabecalho'>
            <div style='position: absolute;'><div style='margin-top: 50px; width:250px;'>".$this->assinaturaDirigentes(8)."</div></div>
            ".$this->cabecalho()."
             <p style='".$this->bolder.$this->sublinhado.$this->text_center.$this->maiuscula."'>MAPA DE AVALIAÇÃO DOS ALUNOS DO - ".$this->trimestre." TRIMESTRE - ".$this->numAno."</p>";
   
            $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>DISCIPLINA: <strong>".valorArray($disciplina, "nomeDisciplina")."</strong></p>
            <p style='".$this->miniParagrafo.$this->maiuscula."'>PROFESSOR(A): <strong>".valorArray($this->dadosProfessor, "nomeEntidade")."</strong></p>";

            $topMapaEstatisco=-90;
          
            if($this->classe>9){

                $topMapaEstatisco=-130;
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $topMapaEstatisco=-110;
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }
            }
            $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CLASSE: <strong>".$this->classe.".ª</strong></p>
            <p style='".$this->maiuscula."'>TURMA: <strong>".$this->nomeTurma()."</strong></p></div>";

            $this->html .="<div style='margin-top:".$topMapaEstatisco."px; width: 30%;margin-left: 70%;margin-bottom: 30px;'>
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->border()."'></td><td style='".$this->text_center.$this->bolder.$this->border()."width:20%;'>MF</td><td style='".$this->text_center.$this->bolder.$this->border()."width:20%;'>F</td>
                </tr>
                
                <tr>
                    <td style='".$this->border()."'>APROVADOS</td><td style='".$this->text_center.$this->border()."'>".completarNumero(($totMA+$totFA))."</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totFA)."</td>
                </tr>
                <tr>
                    <td style='".$this->border()."'>REPROVADOS</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totMR+$totFR)."</td><td style='".$this->text_center.$this->border()."'>".completarNumero(($totFR))."</td>
                </tr>
                <tr>
                    <td style='".$this->border()."'>NÃO AVALIADOS</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totMD+$totFD)."</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totFD)."</td>
                </tr>
                <tr>
                    <td style='".$this->border()."'><strong>TOTAL</strong></td><td style='".$this->text_center.$this->border()."'>".completarNumero($totM+$totF)."</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totF)."</td>
                </tr>
            </table>
            </div>";
        $numAval = valorArray($alunos, "avaliacoesContinuas".$this->trimestre, $tipo);
        $numAval = explode("-", $numAval)[0];

        $this->html .= "<table style='".$this->tabela."width:100%;'>
        <tr style='".$this->corDanger.$this->text_center.$this->bolder."'>
            <td style='".$this->bolder.$this->border().$this->text_center."' rowspan='2'>Nº</td>
            <td style='".$this->bolder.$this->border().$this->text_center."' rowspan='2'>NOME COMPLETO</td>
            <td style='".$this->border()."' colspan='".$numAval."'>AVALIAÇÕES</td>
            <td colspan='".count($camposAvaliacao)."' style='".$this->bolder.$this->text_center.$this->border()."'>CLASSIFICAÇÃO</td>
        </tr>

        <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>";
        for ($i=1; $i<=$numAval; $i++) { 
            $this->html .=" <td style='".$this->bolder.$this->border().$this->text_center."'>".$i.".ª<br/>AVAL</td>";
        }
        foreach($camposAvaliacao as $campo){
            $this->html .="<td style='".$this->bolder.$this->border().$this->text_center."'>".$campo["designacao2"]."</td>";
        }
        $this->html .="</tr>";

        $notaMedia=10;
        if($this->classe<=9){
            $notaMedia=5;
        }

        $contador=0;
        foreach ($alunos as $aluno){
            $contador++;

            $avaliacoesContinuas = explode("-", valorArray($aluno, "avaliacoesContinuas".$this->trimestre, $tipo));
            
            if($contador%2==0){
                $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
            }else{
                $this->html .="<tr>";
            }

            $this->html .="<td style='".$this->border().$this->text_center."'>".completarNumero($contador)."</td><td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>";
            for($i=1; $i<=$numAval; $i++){
                $this->html .=$this->tratarVermelha(isset($avaliacoesContinuas[$i])?$avaliacoesContinuas[$i]:"", "", $notaMedia);
            }
            foreach($camposAvaliacao as $campo){
                $this->html .=$this->tratarVermelha(nelson($aluno, $campo["identUnicaDb"], $tipo), "", $notaMedia);
            }
                   
            $this->html .="</tr>";
        }

        $label = "O Professor";
        if(valorArray($this->dadosProfessor, "sexoEntidade")=="F"){
            $label ="A Professora";
        }
        $this->html .="</table>
         <p style='".$this->maiuscula."'>".$this->rodape()."</p>

        <div class='rodape' style='margin-top:-13px;'>
            <div class='assPofessor'>".$this->porAssinatura($label, valorArray($this->dadosProfessor, "nomeEntidade"))."</div>                
        </div>";


        $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Pautas", "Mapa de Avaliações dos Alunos-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".valorArray($disciplina, "nomeDisciplina")."-".$this->numAno, "Mapa-de-Aval-Alunos-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPDisciplina."-".$this->idPAno, "A4", "landscape");
        }
    }



new listaTurmas(__DIR__);
    
    
  
?>
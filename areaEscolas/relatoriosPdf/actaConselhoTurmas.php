<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class conselhoTurmas extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Conselho de Turmas");
            $this->numAno=0;

            $this->turma = isset($_GET["turma"])?$_GET["turma"]:"";
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:"";
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;

            $this->sobreTurma =  $this->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "classe"=>$this->classe, "idPNomeCurso"=>$this->idPCurso, "nomeTurma"=>$this->turma, "idListaAno"=>$this->idPAno]);
             $this->numAno();
             $this->nomeCurso();

            $this->html="<html style='margin-left:50px; margin-right:50px; margin-top:20px; margin-bottom:0px;'>
            <head>
                <title>Acta de Conselho de Turmas</title>
            </head>
            <body>".$this->fundoDocumento("../../")."
            <div style='margin-top: 0px; width:250px;'>".$this->assinaturaDirigentes(7)."</div></div>";

            if($this->verificacaoAcesso->verificarAcesso("", "conselhoTurmas", array(), "")){                   
                $this->conselho();
            }else{
              $this->negarAcesso();
            }
        }
        private function conselho(){
            $this->idPAno = valorArray($this->sobreTurma, "idListaAno");      
 
             $this->todosMatriculados = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma);

            $this->alunosTrans=$this->selectCondClasseCurso("array", "alunosmatriculados", ["sexoAluno", "dataNascAluno", "nomeAluno"], ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.nomeTurma"=>$this->turma, "reconfirmacoes.classeReconfirmacao"=>$this->classe, "reconfirmacoes.estadoReconfirmacao"=>"T"], $this->classe, ["escola.idMatCurso"=>$this->idPCurso], ["reconfirmacoes", "escola"]); 

            $resultadoDosAlunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), ["idPMatricula", "nomeAluno", "dataNascAluno", "sexoAluno", "reconfirmacoes.observacaoF", "reconfirmacoes.seAlunoFoiAoRecurso"]);

             $totalM=0;
             $totalF=0;
             foreach ($this->todosMatriculados as $tot) {
                if($tot["sexoAluno"]=="M"){
                    $totalM++;
                }else{
                    $totalF++;
                }
             }
             foreach ($this->alunosTrans as $tot) {
                if($tot["sexoAluno"]=="M"){
                    $totalM++;
                }else{
                    $totalF++;
                }
             }
             
             $dataExplode = explode("-", valorArray($this->sobreTurma, "dataConselhoNotas"));
            $diaConselho = isset($dataExplode[2])?$dataExplode[2]:"";
            $mesConselho = isset($dataExplode[1])?$dataExplode[1]:"";
            $anoConselho = isset($dataExplode[0])?$dataExplode[0]:"";

            $horaExplode = explode(":", valorArray($this->sobreTurma, "horaConselhoNotas"));
            $horaConselho = isset($horaExplode[2])?$horaExplode[0]:"";
            $minutoConselho = isset($horaExplode[1])?$horaExplode[1]:"";
            if($minutoConselho==0){
                $minutoConselho="";
            }else if($minutoConselho==1){
                $minutoConselho ="e ".$minutoConselho." minuto";
            }else{
                $minutoConselho ="e ".$minutoConselho." minutos";
            }

            $this->html .=$this->cabecalho()."
                <p style='".$this->text_center.$this->bolder."'>ACTA N.º ______/".valorArray($this->sobreUsuarioLogado, "abrevNomeEscola")."/".explode("-", $this->dataSistema)[0]."</p>";

            $this->html .="<p style='".$this->bolder.$this->text_center."'>REUNIÃO DO CONSELHO DE TURMA</p>
            <p style='".$this->text_right."'>ANO LECTIVO: ".$this->numAno."</p>

            <div style='border:solid black 1px; padding:5px;'>";
            if(valorArray($this->sobreTurma, "tipoCurso")=="tecnico"){
                $this->html .="<p style='".$this->maiuscula."'>ESPECIALIDADE: <strong>".valorArray($this->sobreTurma, "areaFormacaoCurso")."</strong></p>";
            }

            if($this->classe>=10){
               if(valorArray($this->sobreTurma, "tipoCurso")=="tecnico"){
                 
                $this->html .="
                    <p style='".$this->maiuscula.$this->miniParagrafo."'>ÁREA DE FORMAÇÃO: <strong>".valorArray($this->sobreTurma, "areaFormacaoCurso")."</strong></p>
                    <p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".valorArray($this->sobreTurma, "nomeCurso")."</strong></p>";
               }else if(valorArray($this->sobreTurma, "tipoCurso")=="pedagogico"){
                 
                $this->html .="
                    <p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".valorArray($this->sobreTurma, "areaFormacaoCurso")."</strong></p>
                    <p style='".$this->maiuscula.$this->miniParagrafo."'>OPÇÃO: <strong>".valorArray($this->sobreTurma, "nomeCurso")."</strong></p>";
               }else{
                    $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".valorArray($this->sobreTurma, "nomeCurso")."</strong></p>";
               }
            }
            

            $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CLASSE: <strong>".$this->classeExt."</strong>&nbsp;&nbsp;TURMA: <strong>".$this->nomeTurma()."</strong>&nbsp;&nbsp;TURNO: <strong>".valorArray($this->sobreTurma, "periodoT")."</strong></p>

            <p style='".$this->maiuscula."'>ALUNOS MATRICULADOS: <strong>".completarNumero((count($this->todosMatriculados)+count($this->alunosTrans)))."</strong>&nbsp;&nbsp;&nbsp;&nbsp;S/M: <strong>".completarNumero($totalM)."</strong>&nbsp;&nbsp;&nbsp;&nbsp;S/F: <strong>".completarNumero($totalF)."</strong></p>
            </div>
            <p style='".$this->text_justify."line-height:30px;'>Aos ".$diaConselho." do mês de ".nomeMes($mesConselho)." de ".$this->retornarNotaExtensa($anoConselho).", pelas ".$horaConselho." hora ".$minutoConselho.", reuniu o Conselho de Turma acima citada, na sala nº ".valorArray($this->sobreTurma, "salaReunidoConselho").".</p>

            <table style='width:100%;".$this->tabela."'>
                <tr><td style='".$this->bolder.$this->border().$this->text_center."'>INTERVENIENTES</td><td style='".$this->bolder.$this->border().$this->text_center."'>NOMES</td><td style='".$this->bolder.$this->border().$this->text_center."'>ASSINATURA</td></tr>";

            foreach ($this->selectCondClasseCurso("array", "divisaoprofessores", ["nomeDisciplina", "nomeEntidade"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$this->idPAno, "classe"=>$this->classe, "nomeTurmaDiv"=>$this->turma], $this->classe, ["idPNomeCurso"=>$this->idPCurso]) as $disciplinas) {
                     
                $this->html .="<tr><td style='".$this->border()."padding:1px;'>".$disciplinas["nomeDisciplina"]."</td><td style='".$this->border()."padding:1px;'>".nelson($disciplinas, "nomeEntidade")."</td><td style='".$this->border()."padding:3px;'></td></tr>";
            }   

            $this->html .="</table>

            <div style='page-break-before: always;'>";  
            
            if($this->tipoCurso=="tecnico"){
                $this->html .="<p style='".$this->bolder."'>1) ALUNOS APROVADOS E TRANSITAM</p>";
            }else{
                $this->html .="<p style='".$this->bolder."'>1) ALUNOS APROVADOS</p>";
            }

            $this->html .="
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Nº</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Nome Completo</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Data de Nascimento</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Sexo</td>
                </tr>"; 
                $i=0;
            foreach ($resultadoDosAlunos as $aluno) {
                if((nelson($aluno, "observacaoF", "reconfirmacoes")=="A" || nelson($aluno, "observacaoF", "reconfirmacoes")=="TR") && nelson($aluno, "seAlunoFoiAoRecurso", "reconfirmacoes")!="A"){
                    $i++;
                   $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>
                    <td style='".$this->border().$this->text_center."'>".converterData($aluno["dataNascAluno"])."</td>
                    <td style='".$this->border().$this->text_center."'>".$aluno["sexoAluno"]."</td>
                    </tr>";
                }
            }
            $this->html .="</table>

            <p style='".$this->bolder."'>2) ALUNOS REPROVADOS</p>
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Nº</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Nome Completo</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Data de Nascimento</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Sexo</td>
                </tr>";
                $i=0;
            foreach ($resultadoDosAlunos as $aluno) {
                if(nelson($aluno, "observacaoF", "reconfirmacoes")=="NA" && nelson($aluno, "seAlunoFoiAoRecurso", "reconfirmacoes")!="A"){

                    $i++;
                   $this->html .="<tr>
                        <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                        <td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>
                        <td style='".$this->border().$this->text_center."'>".converterData($aluno["dataNascAluno"])."</td>
                        <td style='".$this->border().$this->text_center."'>".$aluno["sexoAluno"]."</td>
                    </tr>";
                }
            }
            $this->html .="</table>
            <p style='".$this->bolder."'>3) ALUNOS DESISTENTES</p>
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Nº</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Nome Completo</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Data de Nascimento</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Sexo</td>
                </tr>";
                $i=0;
            foreach ($resultadoDosAlunos as $aluno) {
                

                if(!(nelson($aluno, "observacaoF", "reconfirmacoes")=="NA" || nelson($aluno, "observacaoF", "reconfirmacoes")=="TR" || nelson($aluno, "observacaoF", "reconfirmacoes")=="A")){
                    $i++;
                   $this->html .="<tr>
                        <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                        <td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>
                        <td style='".$this->border().$this->text_center."'>".converterData($aluno["dataNascAluno"])."</td>
                        <td style='".$this->border().$this->text_center."'>".$aluno["sexoAluno"]."</td>
                    </tr>";
                }
            }
            $this->html .="</table>

            <p style='".$this->bolder."'>4) ALUNOS TRANSFERIDOS</p>
            <table style='".$this->tabela."width:100%;'>
            <tr style='".$this->corDanger."'>
                <td style='".$this->border().$this->bolder.$this->text_center."'>Nº</td>
                <td style='".$this->border().$this->bolder.$this->text_center."'>Nome Completo</td>
                <td style='".$this->border().$this->bolder.$this->text_center."'>Data de Nascimento</td>
                <td style='".$this->border().$this->bolder.$this->text_center."'>Sexo</td>
            </tr>";
                $i=0;
            foreach ($this->alunosTrans as $aluno) {
                    $i++;
                   $this->html .="<tr>
                        <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                        <td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>
                        <td style='".$this->border().$this->text_center."'>".converterData($aluno["dataNascAluno"])."</td>
                        <td style='".$this->border().$this->text_center."'>".$aluno["sexoAluno"]."</td>
                    </tr>";
            }
            $this->html .="</table>

            <p style='".$this->bolder."'>5) ALUNOS PARA O RECURSO</p>
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Nº</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Nome Completo</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Data de Nascimento</td>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Sexo</td>
                </tr>";
                $i=0;
            foreach ($resultadoDosAlunos as $aluno) {
                if(nelson($aluno, "seAlunoFoiAoRecurso", "reconfirmacoes")=="A"){
                    $i++;
                   $this->html .="<tr>
                        <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                        <td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>
                        <td style='".$this->border().$this->text_center."'>".converterData($aluno["dataNascAluno"])."</td>
                        <td style='".$this->border().$this->text_center."'>".$aluno["sexoAluno"]."</td>
                    </tr>";
                }
            }
            $this->html .="</table>
                <div style='margin-top: 10px;".$this->text_center."'>
                    <div style='width: 50%;'>
                        <p>O(a) Secretário(a)</p>
                        <p style='".$this->miniParagrafo.$this->text_center."'>_______________________________</p>
                        <p style='".$this->miniParagrafo.$this->text_center."'>&nbsp;</p>
                    </div>
                    <div style='width: 50%;margin-top:-300px; margin-left:50%;'>
                        <p>O(a) Presidente do Conselho</p>
                        <p style='".$this->miniParagrafo.$this->text_center."'>_______________________________</p>
                        <p>".valorArray($this->sobreTurma, "nomeEntidade")."</p>
                    </div>
                </div>
                <div style='".$this->text_center."'>".$this->assinaturaDirigentes(8)."</div>
            </div>
            ";

         $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Actas_Conselho_Notas", "Acta de Conselho de Turma-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->numAno, "Acta de Conselho de Turma-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPAno);
        }        
    }

new conselhoTurmas(__DIR__);
    
    
  
?>
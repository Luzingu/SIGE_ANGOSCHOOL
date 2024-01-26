<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class modeloGeral extends funcoesAuxiliares{
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mini Pautas");
        }

        public function exibirRelatorio(){

            $this->nomeCurso();
            $this->numAno();
            $this->nomeTurma();

            $disciplina = $this->selectArray("nomedisciplinas", [], ["idPNomeDisciplina"=>$this->idPDisciplina]);

            $nomeDisciplina = valorArray($disciplina, "nomeDisciplina");
            $this->tipoDisciplina = valorArray($disciplina, "tipoDisciplina");
            $this->continuidadeDisciplina = valorArray($disciplina, "continuidadeDisciplina");

            $camposAvaliacao = $this->camposAvaliacaoAlunos($this->idPAno, $this->idPCurso, $this->classe, $this->periodoTurma, $this->idPDisciplina);

            $tipo="pautas";
            if($this->idPAno!=$this->idAnoActual){
                $tipo="arquivo_pautas";
            }

            $campos = ["nomeAluno", "sexoAluno"];
            foreach($camposAvaliacao as $humb){
                $campos[] = $tipo.'.'.trim($humb["identUnicaDb"]);
            } 
            $alunos = $this->miniPautas($this->idPCurso, $this->classe, $this->turma, "", $this->idPDisciplina, $tipo, $this->idPAno, $campos);
            $n=0; $totM=0; $totMA=0; $totMD=0; $totMR=0; $totF=0; $totFA=0; $totFD=0; $totFR=0;

            $notaConsiderar="mtI";
            if($this->trimestreApartir==1){
                $notaConsiderar="mtI";
            }else if($this->trimestreApartir==2){
                $notaConsiderar="mtII";
            }else{
                $notaConsiderar="mf";
            }

            foreach ($alunos as $aluno){
                $n++;
                if($aluno["sexoAluno"]=="M"){
                    $totM++;
                    if(nelson($aluno, $notaConsiderar, $tipo)>=$this->notaMinima){
                        $totMA++;
                    }else if(nelson($aluno, $notaConsiderar, $tipo)==0 || nelson($aluno, $notaConsiderar, $tipo)==NULL || nelson($aluno, $notaConsiderar, $tipo)==""){
                         $totMD++;
                    }else{
                        $totMR++;
                    }
                }else{
                    $totF++;
                    if(nelson($aluno, $notaConsiderar, $tipo)>=$this->notaMinima){
                        $totFA++;
                    }else if(nelson($aluno, $notaConsiderar, $tipo)==0 || nelson($aluno, $notaConsiderar, $tipo)==NULL || nelson($aluno, $notaConsiderar, $tipo)==""){
                        $totFD++;
                    }else{
                        $totFR++;
                    }
                }
            }
            $this->html .="<div class='cabecalho'>
            <div style='position: absolute;'><div style='margin-top: 50px; width:250px;'>".$this->assinaturaDirigentes(8)."</div></div>
            ".$this->cabecalho()."
             <p style='".$this->bolder.$this->sublinhado.$this->text_center.$this->maiuscula."'>MINI-PAUTA DE ".$nomeDisciplina." - ".$this->numAno."</p>";
 
            $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>PROFESSOR(A): <strong>".valorArray($this->dadosProfessor, "nomeEntidade")."</strong></p>";

            $topMapaEstatisco=-60;
          
            if($this->classe>9){

                $topMapaEstatisco=-100;
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $topMapaEstatisco=-80;
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }            
                
            }
            $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CLASSE: <strong>".$this->classe.".ª</strong></p>
            <p style='".$this->miniParagrafo.$this->maiuscula."'>TURMA: <strong>".$this->designacaoTurma."</strong></p></div>";


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
            
            $this->html .= "<table style='".$this->tabela."width:100%;'>
            <tr style='".$this->corDanger."'><td style='".$this->bolder.$this->border().$this->text_center."' rowspan='2'>N.º</td><td style='".$this->bolder.$this->border().$this->text_center."' rowspan='2' style='".$this->bolder.$this->border().$this->text_center."width:180px;'>Nome Completo</td>";
            foreach($this->trimestres as $p){

                $this->html .="<td colspan='".count(array_filter($camposAvaliacao, function ($mamale) use ($p){
                        return trim($mamale["periodo"])==$p["identificador"];
                    }))."' style='".$this->bolder.$this->border().$this->text_center."'>".$p["designacao"]."</td>";
            }
            $this->html .="</tr>";

            $this->html .="<tr style='".$this->corDanger."'>";
            foreach ($camposAvaliacao as $cabeca) {
                $this->html .="<td style='".$this->bolder.$this->border().$this->text_center."'>".$cabeca["designacao2"]."</td>";
            }  
            $this->html .="</tr>";

            $i=0;
            foreach ($alunos as $aluno){
                $i++;            
                if($i%2==0){
                    $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$aluno["nomeAluno"]."</td>";
                foreach ($camposAvaliacao as $cabeca) {
                    $this->html .=$this->tratarVermelha(nelson($aluno, $cabeca["identUnicaDb"], $tipo), "", $cabeca["notaMedia"], $cabeca["cd"]);
                }
                $this->html .="</tr>";
            }

            $label = "O Professor";
            if(valorArray($this->dadosProfessor, "generoEntidade")=="F"){
                $label ="A Professora";
            }
            $this->html .="</table>
             <p style='".$this->maiuscula."'>".$this->rodape()."</p>
            <div class='rodape'>
                <div class='assPofessor'>".$this->porAssinatura($label, valorArray($this->dadosProfessor, "nomeEntidade"))."</div>                
            </div>";
            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Pautas", "Mini-Pauta-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$nomeDisciplina."-".$this->numAno, "Mini-Pauta-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPDisciplina."-".$this->idPAno, "A4", "landscape");
        }
    }
    
  
?>
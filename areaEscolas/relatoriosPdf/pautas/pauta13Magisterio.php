<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class pauta13Magisterio extends funcoesAuxiliares{
        
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Pauta Geral Mod1");
        }
        public function exibirPauta(){
            $this->definicoesConselhoNotas = $this->selectArray("definicoesConselhoNotas", ["exprParaAprovado", "exprParaAprovadoComDef", "exprParaAprovadoComRecurso", "exprParaNaoAprovado"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idPAno"=>$this->idPAno]);
            $this->nomeCurso();
            $this->numAno();

            $this->html .="<html>
            <head>
                <title>Pauta Geral</title>
            </head>
            <body style='margin: -10px; margin-left: 0px; margin-right: 0px;><div class='cabecalho'>
            <div style='position: absolute;'><div style='margin-top: 20px; width:250px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();
            
            $tpm ="";
            if($this->tipoCurso=="tecnico"){
                $tpm="TÉCNICO";
            }else if($this->tipoCurso=="pedagogico"){
                $tpm="PEDAGÓGICO";
            }else{
                $tpm="GERAL";
            }

            $this->html .="<p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>PAUTA FINAL N.º ____ - ".$this->numAno."</p>";
            
            $mTop =-133;
            if($this->classe>=10){
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $mTop =-110;
                    $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }
            }
            
            $this->html .="<p style='".$this->maiuscula."'>CLASSE: <strong>".$this->classe.".ª</strong></p><p style='".$this->maiuscula."'>TURMA: <strong>".$this->nomeTurma()."</strong></p><div>";
        
        
        $this->listaAlunos = array();

        foreach($this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno) as $a){ 

            $this->notas = listarItensObjecto($a, "pautas",  ["idPautaCurso=".$this->idPCurso, "mf>0"]);

            $media10 = $this->calculadorMediaPorClasse(10);
            $media11 = $this->calculadorMediaPorClasse(11);
            $media12 = $this->calculadorMediaPorClasse(12);
            $media13 = $this->calculadorMediaPorClasse(13);

            $nec = (double) valorArray($a, "notaEstagio", "escola");
            $pap = (double) valorArray($a, "provAptidao", "escola");
            $mc = ($media10+$media11+$media12)/3;
            $mc = number_format($mc, 0);

            $mfc = (3*$mc+$nec+$pap)/5;
            $mfc = number_format($mfc, 0);

            $observacaoF="NA"; 
            if(valorArray($a, "estadoDesistencia", "reconfirmacoes")=="D" || valorArray($a, "estadoDesistencia", "reconfirmacoes")=="N" || valorArray($a, "estadoDesistencia", "reconfirmacoes")=="F"){
                $observacaoF=valorArray($a, "estadoDesistencia", "reconfirmacoes");
            }else{
                if($mfc>=10 && $nec>=10 && $pap>=10){
                    $observacaoF="A";
                }else{
                    $observacaoF="NA";
                }                
            }
            $this->editarItemObjecto("alunosmatriculados", "reconfirmacoes", "mfT4, observacaoF", [$mfc, $observacaoF], ["idPMatricula"=>$a["idPMatricula"]], ["idReconfEscola"=>$_SESSION['idEscolaLogada'], "idReconfAno"=>$this->idAnoActual]);

            $this->listaAlunos[]=array("nomeAluno"=>$a["nomeAluno"], "sexoAluno"=>$a["sexoAluno"], "m10"=>$media10, "m11"=>$media11, "m12"=>$media12, "mc"=>$mc, "pap"=>$pap, "nec"=>$nec, "mfc"=>$mfc, "observacaoF"=>$observacaoF);
        }

        $total=0; $totalF=0; $totalApr=0; $totalAprF=0; $totalRep=0; $totalRepF=0; $totalDes=0; $totalDesF=0;

        foreach ($this->listaAlunos as $aluno) {
            $total++;
            if($aluno["observacaoF"]=="A" || $aluno["observacaoF"]=="TR"){
                $totalApr++;
            }else if($aluno["observacaoF"]=="D"){
                $totalDes++;
            }else{
                $totalRep++;
            }
            if($aluno["sexoAluno"]=="F"){
                $totalF++;
                if($aluno["observacaoF"]=="A" || $aluno["observacaoF"]=="TR"){
                    $totalAprF++;
                }else if($aluno["observacaoF"]=="D"){
                    $totalDesF++;
                }else{
                    $totalRepF++;
                }
            }
        }

        //Exbir Mapa Estatístico...
        $this->html .="<div style='margin-top:".$mTop."px; width:250px; margin-left: 450px; margin-bottom:20px;'>
        <table style='".$this->tabela." width:100%;'>
            <tr style='".$this->corDanger."'>
                <td style='".$this->border()." width:70px;'></td><td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
            </tr>
            <tr >
                <td style='".$this->border()." width:70px;'>TOTAL</td><td style='".$this->border().$this->text_center."'>".completarNumero($total)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalF)."</td>
            </tr>
            <tr>
                <td style='".$this->border()." width:70px;'>DESISTENTES</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalDes)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalDesF)."</td>
            </tr>
            <tr>
                <td style='".$this->border()." width:70px;'>APROVADOS</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalApr)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalAprF)."</td>
            </tr>
            <tr>
                <td style='".$this->border()." width:70px;'>REPROVADOS</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalRep)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalRepF)."</td>
            </tr>
        </table>
        </div>";
            
            //Fazendo a tabela da pauta geral...
            $this->html .="
            <table  style='margin-top:-5px;".$this->tabela." width:100%; font-size:11pt;'>
                

                <tr style='".$this->corDanger.$this->bolder."'>
                    <td rowspan='2' style='".$this->border().$this->text_center."'>N.º</td><td rowspan='2' style='".$this->border().$this->text_center."'>NOME COMPLETO</td>
                    <td colspan='3' style='".$this->border().$this->text_center."'>MÉDIAS POR CLASSE</td>
                    <td rowspan='2' style='".$this->border().$this->text_center."'>MA</td>
                    <td rowspan='2' style='".$this->border().$this->text_center."'>PAP</td>
                    <td rowspan='2' style='".$this->border().$this->text_center."'>NEC</td>
                    <td rowspan='2' style='".$this->border().$this->text_center."'>MFC</td>
                    <td rowspan='2' style='".$this->border().$this->text_center."'>OBS</td>
                </tr>
                <tr style='".$this->corDanger.$this->bolder."'>
                    <td style='".$this->border().$this->text_center."'>10.ª<br/>CLASSE</td><td style='".$this->border().$this->text_center."'>11.ª<br/>CLASSE</td><td style='".$this->border().$this->text_center."'>12.ª<br/>CLASSE</td>
                </tr>";

            $n=0;
            foreach ($this->listaAlunos as $b) {
                $n++;
                $this->html .="<tr><td style='".$this->border().$this->text_center."'>".completarNumero($n)."</td><td style='".$this->border()."'>".$b["nomeAluno"]."</td>".$this->tratarVermelha($b["m10"], "", 10).$this->tratarVermelha($b["m11"], "", 10).$this->tratarVermelha($b["m12"], "", 10).$this->tratarVermelha($b["mc"], "", 10).$this->tratarVermelha($b["pap"], "", 10).$this->tratarVermelha($b["nec"], "", 10).$this->tratarVermelha($b["mfc"], "", 10).$this->observacaoF($b["observacaoF"], "", $b["sexoAluno"])."</tr>";
            } 

            $this->html .="
            </table>";
                    
            $this->html .="<div style='margin-top:0px'>
             <p style='".$this->text_center.$this->maiuscula."'>".$this->rodape()."</p><br/>";
 
            $this->html .="<div style='margin-top: -30px;'>".$this->assinaturaDirigentes(8)."</div>";
 
            
            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Pautas", "Pata Geral-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-IV-".$this->numAno, "Pauta_Geral-Mod1-".$this->idPCurso."-".$this->classe."-".$this->turma."-IV-".$this->idPAno);
            
        }

        private function calculadorMediaPorClasse($classe){
            $totalDisc=0;
            $totalNotas=0;
            foreach ($this->notas as $nota) {
                if($nota["classePauta"]==$classe){
                    $nota["mf"] = nelson($nota, "mf");
                    if(nelson($nota, "recurso")!=NULL && nelson($nota, "recurso")!=""){
                        $nota["mf"]=nelson($nota, "recurso");
                    }

                    if(nelson($nota, "exameEspecial")!=NULL && nelson($nota, "exameEspecial")!=""){
                        $nota["mf"]=nelson($nota, "exameEspecial");
                    }
                    $nota["mf"] = number_format((double)$nota["mf"], 0);
                    $totalNotas +=$nota["mf"];
                    $totalDisc++;
                }
            }
            if($totalNotas<=0){
                return 0;
            }else{
                return number_format(($totalNotas/$totalDisc), 0);
            }
        }

    }  
?>
<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class pautaMod1 extends funcoesAuxiliares{
        public $mesPagamentoApartir="";
        public $trimestreApartir=0;
        public $trimestreAbr="I";
        public $trimestreApartirExtensa="";
        public $notaMinima="";
        public $possoGravar="sim";
        public $listaAlunos="";
        public $tamanhoFolha="A4";
        public $estadoPagamentoAluno="F";
        public $idGestDisEspecialidade="";
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Pauta Geral Mod1");
        }

         public function exibirPauta(){
            $this->definicoesConselhoNotas = $this->selectArray("definicoesConselhoNotas", ["exprParaAprovado", "exprParaAprovadoComDef", "exprParaAprovadoComRecurso", "exprParaNaoAprovado"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idPAno"=>$this->idPAno]);

            $notaMedia=10;
            if($this->classe<=9){
                $notaMedia=5;
            }

            $this->nomeCurso();
            $this->numAno();

            $this->html .="<html>
            <head>
                <title>Pauta Geral</title>
            </head>
            <body style='margin: -10px; margin-left: -30px; margin-right: -30px;>".$this->cabecalho();
            
            if($_SESSION['idEscolaLogada']==21 && $this->classe==6){
                $this->html .="<div style='position:absolute;'>
                        <div style='".$this->text_center." width:250px;margin-top:-150px;' >
                            <p class='text-center' style='font-size:10pt;'>Visto pela<br/>Directora do Centro</p>

                            <p class='text-center bolder' style='font-size:10pt;  margin-top:20px;'>___________________________</p>
                        </div>
                    </div>";
            } else if($_SESSION['idEscolaLogada']==26){
                $this->html .="<div style='position:absolute;'>
                        <div style='".$this->text_center." width:250px;margin-top:-150px;' >
                            <p class='text-center' style='font-size:10pt;'>O Director do IMTS-MBK</p>

                            <p class='text-center bolder' style='font-size:10pt;  margin-top:20px;'>João André</p>
                        </div>
                    </div>";
            }
            $tpm ="";
            if($this->tipoCurso=="tecnico"){
                $tpm="TÉCNICO";
            }else if($this->tipoCurso=="pedagogico"){
                $tpm="PEDAGÓGICO";
            }else{
                $tpm="GERAL";
            }
            
            $observacaoConsiderar="observacaoF";
            $designacaoAproveitamento = ["NÃO AVALIADOS", "BOM APROVEIT.", "MAU APROVEIT."];
            if($this->trimestreApartir==1){ 
                $this->trimestreAbr="I";
                $observacaoConsiderar="mfT1";
            }else if($this->trimestreApartir==2){
                $this->trimestreAbr="II";
                $observacaoConsiderar="mfT2";
            }else if($this->trimestreApartir==3){
                $this->trimestreAbr="III";
                $observacaoConsiderar="mfT3";
            }else{
                $this->trimestreAbr="IV";
                $designacaoAproveitamento = ["DESISTENTES", "APROVADOS", "REPROVADOS"];
            }

            if($this->trimestreApartir==4){

                if($this->classe==12){                    
                    $this->html .="<p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>PAUTAS FINAIS PARA AS CLASSES FINALISTAS DO ENSINO SECUNDÁRIO ".$tpm." N.º ______ - ".$this->numAno."</p>";
                }else{
                    $this->html .="<p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>PAUTAS FINAIS PARA AS CLASSES DE TRANSIÇÃO DO ENSINO SECUNDÁRIO ".$tpm." N.º ______ - ".$this->numAno."</p>";
                }
            }else{
                if($this->classe==12){                    
                    $this->html .="<p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>PAUTAS ".$this->trimestreApartirExtensa." PARA AS CLASSES FINALISTAS DO ENSINO SECUNDÁRIO ".$tpm." N.º ______ - ".$this->numAno."</p>";
                }else{
                    $this->html .="<p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>PAUTAS ".$this->trimestreApartirExtensa." PARA AS CLASSES DE TRANSIÇÃO DO ENSINO SECUNDÁRIO ".$tpm." N.º ______ - ".$this->numAno."</p>";
                }
            }
            
            $mTop =-130;
            if($this->tipoCurso=="pedagogico"){
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
            }else if($this->tipoCurso=="tecnico"){
                $this->html .="<p style='".$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }else{
                $mTop =-105;
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }           
            
            $this->html .="<p style='".$this->maiuscula."'>CLASSE: <strong>".$this->classe.".ª</strong></p><p style='".$this->maiuscula."'>TURMA: <strong>".$this->nomeTurma()."</strong></p>";

            if($_SESSION["idEscolaLogada"]==22){
                $this->html .="<p style='".$this->maiuscula."'>SALA N.º: <strong>".completarNumero($this->numeroSalaTurma)."</strong></p><div>";
            }
            
            $this->html .="<div>";

            $tipoPauta="pautas";
            $condicaoPauta = ["classePauta=".$this->classe, "idPautaCurso=".$this->idPCurso];
            if($this->idPAno!=$this->idAnoActual){
                $tipoPauta="arquivo_pautas";
                $condicaoPauta[]="idPautaAno=".$this->idPAno;
                $condicaoPauta[]="idPautaEscola=".$_SESSION['idEscolaLogada'];
            }

            $campos = ["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "sexoAluno", "reconfirmacoes.observacaoF", $tipoPauta.'.idPPauta',$tipoPauta.'.idPautaMatricula',$tipoPauta.'.idPautaDisciplina', $tipoPauta.'.obs',$tipoPauta.'.seFoiAoRecurso',$tipoPauta.'.classePauta',$tipoPauta.'.semestrePauta',$tipoPauta.'.idPautaCurso',$tipoPauta.'.chavePauta',$tipoPauta.'.idPautaAno',$tipoPauta.'.idPautaEscola', "reconfirmacoes.mfT1", "reconfirmacoes.mfT2", "reconfirmacoes.mfT3", "reconfirmacoes.mfT4", "reconfirmacoes.seAlunoFoiAoRecurso", "escola.idGestDisEspecialidade", "escola.idGestLinguaEspecialidade", "escola.beneficiosDaBolsa", "escola.provAptidao", "escola.notaEstagio", "pagamentos.codigoEmolumento", "pagamentos.idHistoricoEscola", "pagamentos.idHistoricoAno", "pagamentos.referenciaPagamento"];
            
            foreach($this->selectArray("campos_avaliacao", ["identUnicaDb"]) as $humb){
                $campos[] = $tipoPauta.'.'.trim($humb["identUnicaDb"]);
            } 
            $alunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), $campos);
            $tiposDisciplinas = distinct2($this->disciplinasDaClasse, "tipoDisciplina");

        $this->cabecalhos=array();
        foreach($this->disciplinasDaClasse as $disc){

            foreach($this->camposAvaliacaoAlunos($this->idPAno, $this->idPCurso, $this->classe, $this->periodoTurma, $disc["idPNomeDisciplina"], $this->trimestreAbr, "cabecalho") as $humb){
                $css = "font-weight: bolder !important;".$this->backGround("rgb(119,136,153, 0.5)");
                $css="";
                $this->cabecalhos[] = array('titulo'=>$humb["designacao2"], 'notaMedia'=>$humb["notaMedia"], "tituloDb"=>$humb["identUnicaDb"], "cd"=>$humb["cd"], "classeCss"=>$css, "idPNomeDisciplina"=>$disc["idPNomeDisciplina"], "atributoDisciplina"=>$disc["atributoDisciplina"], "tipoDisciplina"=>$disc["tipoDisciplina"], "continuidadeDisciplina"=>$disc["continuidadeDisciplina"], "nomeDisciplina"=>$disc["nomeDisciplina"]);
            }
        }
        
        $total=0;$totalF=0;
        $totalApr=0;$totalAprF=0;
        $totalRep=0;$totalRepF=0;
        $totalDes=0;$totalDesF=0;

        foreach ($alunos as $aluno) {

            $total++;
            
            if($this->trimestreApartir==4){
                if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="A" || valorArray($aluno, "observacaoF", "reconfirmacoes")=="TR"){
                    $totalApr++;
                }else if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="D"){
                    $totalDes++;
                }else{
                    $totalRep++;
                }
                if($aluno["sexoAluno"]=="F"){
                    $totalF++;
                    if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="A" || valorArray($aluno, "observacaoF", "reconfirmacoes")=="TR"){
                        $totalAprF++;
                    }else if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="D"){
                        $totalDesF++;
                    }else{
                        $totalRepF++;
                    }
                }
            }else{
                if(number_format((double)valorArray($aluno, $observacaoConsiderar, "reconfirmacoes"), 0)>=$this->notaMinima){
                    $totalApr++;
                }else if((int)valorArray($aluno, $observacaoConsiderar, "reconfirmacoes")<=0){
                    $totalDes++;
                }else{
                    $totalRep++;
                }
                if($aluno["sexoAluno"]=="F"){
                    $totalF++;
                    if(number_format((int)valorArray($aluno, $observacaoConsiderar, "reconfirmacoes"), 0)>=$this->notaMinima){
                        $totalAprF++;
                    }else if((double)valorArray($aluno, $observacaoConsiderar, "reconfirmacoes")<=0){
                        $totalDesF++;
                    }else{
                        $totalRepF++;
                    }
                }
            }
        }
            //Exbir Mapa Estatístico...
            $this->html .="<div style='margin-top:".$mTop."px; width:20%; margin-left: 80%; margin-bottom:20px;'>
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->border()." width:144px;'></td><td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
                </tr>
                <tr >
                    <td style='".$this->border()." width:144px;'>TOTAL</td><td style='".$this->border().$this->text_center."'>".completarNumero($total)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalF)."</td>
                </tr>
                <tr>
                    <td style='".$this->border()." width:144px;'>".$designacaoAproveitamento[0]."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalDes)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalDesF)."</td>
                </tr>
                <tr>
                    <td style='".$this->border()." width:144px;'>".$designacaoAproveitamento[1]."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalApr)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalAprF)."</td>
                </tr>
                <tr>
                    <td style='".$this->border()." width:144px;'>".$designacaoAproveitamento[2]."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalRep)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totalRepF)."</td>
                </tr>
            </table>
            </div>";
            
            $tamanhoLetras=8;
            if(count($this->cabecalhos)>36){
                $tamanhoLetras=7;
            }
            //Fazendo a tabela da pauta geral...
            $this->html .="<table  style='margin-top:-5px;".$this->tabela." width:100%; font-size:".$tamanhoLetras."pt;'>
            <tr style='".$this->corDanger."'><td style='".$this->bolder.$this->border().$this->text_center."' rowspan='3'>N.º</td><td style='".$this->bolder.$this->border().$this->maiuscula.$this->text_center."' rowspan='3'>Nome Completo</td>";
            foreach ($tiposDisciplinas as $tipo) {
                $this->html .="<td style='".$this->text_center.$this->bolder.$this->border().$this->maiuscula."  border-right:solid black 2px;' colspan='".$this->totalCabecalhosPorTipo($tipo)."'>".tipoDisciplina2($tipo)."</td>";
            }
            if($this->classe==13 && $this->trimestreApartir==4){
                $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."' rowspan='3'>NEC</td>";
                $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."' rowspan='3'>PAP</td>";
            }
            if($this->classe<=6 && $this->trimestreApartir==4){
                $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."' rowspan='3'>MÉDIA</td>";
            }
            
            if(($_SESSION['idEscolaLogada']==16 || $_SESSION['idEscolaLogada']==27) && $this->classe>=9 && $this->trimestreApartir==4){
                $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."' rowspan='3'>DEFICIÊNCIAS</td>";
            }

            $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."' rowspan='3'>R<br>E<br>S<br>U<br>L<br>T</td>";
            

            $this->html .="</tr>";

            //Disciplinas
            $this->html .="<tr style='".$this->corDanger."'>";
            foreach ($this->disciplinasDaClasse as $disc) {
               $this->html .="<td colspan='".$this->totalCabecalhosPorDisciplina($disc["idPNomeDisciplina"])."' style='".$this->border().$this->text_center.$this->maiuscula.$this->bolder.$this->maiuscula." border-right:solid black 2px;'>".$disc["nomeDisciplina"]."</td>";
            }
            $this->html .="</tr>";

            //Label....
            $this->html .="<tr style='".$this->corDanger."'>";
            foreach ($this->cabecalhos as $cab) {
                $this->html .="<td style='".$this->bolder.$this->border().$this->text_center."'>".$cab["titulo"]."</td>";
            }
            $this->html .="</tr>";

            $i=0;
            foreach ($alunos as $aluno) {
                $i++;

                $this->verificarPagamentos($aluno);

                //Pegando a Disciplina de Opção do aluno, neste caso para os alunos que só estudam no Puniv...
                $this->idGestDisEspecialidade = valorArray($aluno, "idGestDisEspecialidade", "escola");
              
                //Pegar o Id da Língua Estrangeira do Aluno...
                $this->idGestLinguaEspecialidade = valorArray($aluno, "idGestLinguaEspecialidade", "escola");
                if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border().$this->maiuscula."padding:2px; width:220px;'>".$aluno["nomeAluno"]."</td>";

                $deficiencias ="";
                foreach ($this->cabecalhos as $cab) {

                    $nota=null;
                    if($this->estadoPagamentoAluno=="F"){
                       $this->html .="<td style='".$this->border()."background-color:transparent;'></td>";
                    }else{
                        
                        $pautas = listarItensObjecto($aluno, $tipoPauta,  $condicaoPauta);
                        $nota = $this->retornarNota($pautas, $cab["idPNomeDisciplina"], $cab["atributoDisciplina"], $cab["tituloDb"], $cab["classeCss"], $cab["cd"]);

                        $this->html .=$this->tratarVermelha($nota, $cab["classeCss"], $cab["notaMedia"], $cab["cd"]);
                    }

                    if($this->tipoCurso=="tecnico"){
                        if(nelson($aluno, "seAlunoFoiAoRecurso", "reconfirmacoes")=="A" && $cab["continuidadeDisciplina"]=="T" && $cab["tituloDb"]=="cf" && $nota<10){
                            if($deficiencias!=""){
                                $deficiencias.=", ";
                            }
                            $deficiencias .=$cab["nomeDisciplina"];
                            
                        }else if(nelson($aluno, $observacaoConsiderar, "reconfirmacoes")=="TR" && $cab["continuidadeDisciplina"]=="C" && $cab["tituloDb"]=="mf" && $nota<10){
                            if($deficiencias!=""){
                                $deficiencias.=", ";
                            }
                            $deficiencias .=$cab["nomeDisciplina"];
                        }
                    }else{
                        if(nelson($aluno, $observacaoConsiderar, "reconfirmacoes")=="TR" || nelson($aluno, "seAlunoFoiAoRecurso", "reconfirmacoes")=="A"){
                            if($cab["tituloDb"]=="mf"){
                                if($cab["tipoDisciplina"]=="FG" && $nota<10){
                                    if($deficiencias!=""){
                                        $deficiencias.=", ";
                                    }
                                    $deficiencias .=$cab["nomeDisciplina"];
                                }
                            }
                        }
                    }
                }

                if($this->classe==13 && $this->trimestreApartir==4){
                    $this->html .=$this->notaAptidaoEstagio($aluno);
                }

                if($this->classe<=6 && $this->trimestreApartir==4){
                    $this->html .=$this->tratarVermelha(valorArray($aluno, "mfT4", "reconfirmacoes"), "", $notaMedia, 0);   
                }
                
                if($this->estadoPagamentoAluno=="F"){

                    if(($_SESSION['idEscolaLogada']==16 || $_SESSION['idEscolaLogada']==27) && $this->classe>=9 && $this->trimestreApartir==4){
                        $this->html .="<td style='".$this->border()."background-color:transparent;'></td>";
                    }
                    $this->html .="<td style='".$this->border().$this->text_center."background-color:transparent;'>PENDENTE</td>";
                }else{
                    if(($_SESSION['idEscolaLogada']==16 || $_SESSION['idEscolaLogada']==27) && $this->classe>=9 && $this->trimestreApartir==4){
                        $this->html .="<td style='".$this->border()."'>".$deficiencias."</td>";
                    }
                    if($this->trimestreApartir==4){
                        $this->html .=$this->observacaoF(valorArray($aluno, "observacaoF", "reconfirmacoes"), valorArray($aluno, "seAlunoFoiAoRecurso", "reconfirmacoes"), $aluno["sexoAluno"]);    
                    }else{
                        $this->html .=$this->tratarVermelha(valorArray($aluno, $observacaoConsiderar, "reconfirmacoes"), "", $notaMedia, 0);
                    }
                     
                }
                    
                               
                $this->html .="</tr>";
            }
            $this->html .="</table>";
            
            if((count($alunos)>=26 && count($alunos)<=32 && $tamanhoLetras==8) || (count($alunos)>=31 && count($alunos)<=38 && $tamanhoLetras==7)){
                $this->html .="<div style='page-break-before: always;'>";
            }else{
                $this->html .="<div>";
            }

            $this->html .="
             <p style='".$this->text_center.$this->maiuscula."'>".$this->rodape()."</p><br/>";

            $this->html .="<div class='assinaturaComissão' style='width:33%; margin-top:-30px;'>
                <p  style='".$this->text_center.$this->maiuscula."'>O CONSELHO DE NOTAS</p>
                <p style='".$this->text_center.$this->maiuscula."'>__________________________________________</p>
                <p style='".$this->text_center.$this->maiuscula."'>__________________________________________</p>
                <p style='".$this->text_center.$this->maiuscula."'>__________________________________________</p>
            </div>";
            $this->html .="<div style='margin-top: -300px;
            margin-left: 33%; width:33%;".$this->maiuscula."'>".$this->assinaturaDirigentes(8)."</div><div style='margin-top: -600px;
            margin-left: 66%; width:33%;".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div>";
 
            
           $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Pautas", "Pata Geral-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->trimestreAbr."-".$this->numAno, "Pauta_Geral-Mod1-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->trimestreAbr."-".$this->idPAno, $this->tamanhoFolha, "landscape");
            
        }

        private function totalCabecalhosPorTipo($tipo){
            $contador=0;
            foreach ($this->cabecalhos as $cab) {
                if($cab["tipoDisciplina"]==$tipo){
                    $contador++;
                }
            }
            return $contador;
        }
        private function totalCabecalhosPorDisciplina($idPNomeDisciplina){
            $contador=0;
            foreach ($this->cabecalhos as $cab) {
                if($cab["idPNomeDisciplina"]==$idPNomeDisciplina){
                    $contador++;
                }
            }
            return $contador;
        }

        private function retornarNota($pautas, $idPNomeDisciplina, $atributoDisciplina, $campo, $css=""){

            //Aqui são casos particulares para algumas disciplinas em que o aluno terá que fazer escolha de discplinas
            if($atributoDisciplina=="OP"){
                //Aqui é para disciplinas de Opção para Puniv...(GD, Psicologia, Sociologia)
                $idPNomeDisciplina = $this->idGestDisEspecialidade; 
            }else if($atributoDisciplina=="LE" || $atributoDisciplina=="LE Esp"){
                //Aqui é para disciplinas de Línguas estrangeiras.
                $idPNomeDisciplina = $this->idGestLinguaEspecialidade;

            }else if($atributoDisciplina=="LE Geral"){
                //Para alunos da ciências humanas que fazem duas linguas estrangeiras ao mesmo tempo.
                //Aqui o sistema verifica se qual é a disciplina de opção do aluno, e qual modo desta disciplina
                if($this->modLinguaEstrangeira=="lingEspUnica"){
                    if($this->idGestLinguaEspecialidade==22){
                        $idPNomeDisciplina = 20;
                    }else{
                        $idPNomeDisciplina = 21;
                    }                    
                }else{
                    if($this->idGestLinguaEspecialidade==22){
                        $idPNomeDisciplina = 21;
                    }else{
                        $idPNomeDisciplina = 20;
                    }
                }
            }
            $nota="";
            foreach ($pautas as $pauta) {
               if($pauta["idPautaDisciplina"]==$idPNomeDisciplina){
                    $nota = nelson($pauta, $campo);
                    break;
               }
            }
            return $nota;
        }

        private function verificarPagamentos($objecto){

            $this->estadoPagamentoAluno="F";
            if($this->seListarTodaPauta=="YXM" || $this->mesPagamentoApartir==0){
                $this->estadoPagamentoAluno="V";
            }else{
                if(count(listarItensObjecto($objecto, "pagamentos", ["codigoEmolumento=propinas", "idHistoricoEscola=".$_SESSION["idEscolaLogada"], "idHistoricoAno=".$this->idPAno, "referenciaPagamento=".$this->mesPagamentoApartir]))>0 ){
                    $this->estadoPagamentoAluno="V";
                }else if($this->preco("propina", $this->classe, $this->idPCurso, $this->mesPagamentoApartir, $objecto)<=0){
                    $this->estadoPagamentoAluno="V";
                }
            }
        }
    }  
?>
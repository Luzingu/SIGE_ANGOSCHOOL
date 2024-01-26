<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
     
    class pautaMod2 extends funcoesAuxiliares{
        public $mesPagamentoApartir="";
        public $trimestreApartir=0;
        public $trimestreAbr="I";
        public $trimestreApartirExtensa="";
        public $notaMinima="";
        public $possoGravar="sim";
        public $listaAlunos="";
        public $tamanhoFolha="A4";

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Pauta Geral Mod2");
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
                <style>
                    td[colspan=0]{
                      display: none;
                    }
                </style>
            </head>
            <body style='margin: -10px; margin-left: -30px; margin-right: -30px;>".$this->cabecalho()."
             <p style='".$this->text_center.$this->sublinhado.$this->bolder."'>PAUTA DE APROVEITAMENTO ".$this->trimestreApartirExtensa."- ".$this->numAno."</p>";

            
            $mTop =-130;
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
              $this->html .="<p style='".$this->maiuscula."'>CLASSE: <strong>".$this->classe.".ª</strong> </p>
              <p>TURMA: <strong>".$this->nomeTurma()."</strong></p>";

            if($this->trimestreApartir==1){
                $this->trimestreAbr="I";
            }else if($this->trimestreApartir==2){
                $this->trimestreAbr="II";
            }else if($this->trimestreApartir==3){
                $this->trimestreAbr="III";
            }else{
                $this->trimestreAbr="IV";
            }

            $tipoPauta="pautas";
            $condicaoPauta = ["classePauta=".$this->classe, "idPautaCurso=".$this->idPCurso];

            if($this->idPAno!=$this->idAnoActual){
                $tipoPauta="arquivo_pautas";
                $condicaoPauta[]="idPautaAno=".$this->idPAno;
                $condicaoPauta[]="idPautaEscola=".$_SESSION['idEscolaLogada'];
            }
            $campos = ["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "sexoAluno", "reconfirmacoes.observacaoF", $tipoPauta.'.idPPauta',$tipoPauta.'.idPautaMatricula',$tipoPauta.'.idPautaDisciplina',$tipoPauta.'.obs',$tipoPauta.'.seFoiAoRecurso',$tipoPauta.'.classePauta',$tipoPauta.'.semestrePauta',$tipoPauta.'.idPautaCurso',$tipoPauta.'.chavePauta',$tipoPauta.'.idPautaAno',$tipoPauta.'.idPautaEscola', "reconfirmacoes.mfT1", "reconfirmacoes.mfT2", "reconfirmacoes.mfT3", "reconfirmacoes.seAlunoFoiAoRecurso", "escola.idGestDisEspecialidade", "escola.idGestLinguaEspecialidade", "escola.idGestLinguaEspecialidade", "escola.provAptidao", "escola.notaEstagio", "pautas.classePauta", "pautas.idPautaDisciplina", "pautas.cf", "notas_finais.recurso", "pautas.idPautaCurso"];
            foreach($this->selectArray("campos_avaliacao", ["identUnicaDb"]) as $humb){
                $campos[] = $tipoPauta.'.'.trim($humb["identUnicaDb"]);
            }

            $alunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), $campos);

            $total=0; $totalF=0; $totalApr=0; $totalAprF=0; $totalRep=0; $totalRepF=0; $totalDes=0; $totalDesF=0;
            
            foreach ($alunos as $aluno) {

                $total++;
                if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="A" || valorArray ($aluno, "observacaoF", "reconfirmacoes")=="TR"){
                    $totalApr++;
                }else if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="D"){
                    $totalDes++;
                }else{
                    $totalRep++;
                }
                if($aluno["sexoAluno"]=="F"){
                    $totalF++;
                    if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="A" || valorArray ($aluno, "observacaoF", "reconfirmacoes")=="TR"){
                        $totalAprF++;
                    }else if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="D"){
                        $totalDesF++;
                    }else{
                        $totalRepF++;
                    }
                }
            }             

            $cabecalho = array();

            $this->cabecalhoDisciplinas=array();

            foreach ($this->disciplinasDaClasse as $disciplina) {

                //Pegar as disciplinas anteriores na classe anterior...
                $existemNaClasseAnterior="nao";
               for ($i=($this->classe-1); $i>=10; $i--) {
                   foreach ($this->disciplinas($this->idPCurso, $i, $this->periodoTurma, "", [$disciplina["idPNomeDisciplina"]], [58, 59, 60, 231, 232, 233])  as $contD) {

                       $cabecalho[]=$i."ª";
                       $existemNaClasseAnterior="sim";
                       $this->cabecalhoDisciplinas[] = array('titulo' =>$i.".ª", 'nomeDisciplina'=>$disciplina["nomeDisciplina"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'notaMedia'=>$notaMedia, 'atributoDisciplina'=>$disciplina["atributoDisciplina"], 'tituloDb'=>$i, "classeCss"=>"", "nenhumaCasa"=>"sim");
                   }
               }
               foreach($this->camposAvaliacaoAlunos($this->idPAno, $this->idPCurso, $this->classe, $this->periodoTurma, $disciplina["idPNomeDisciplina"], $this->trimestreAbr, "cabecalho") as $humb){

                $this->cabecalhoDisciplinas[] = array('titulo' =>$humb["designacao2"], 'notaMedia' =>$humb["notaMedia"], "cd"=>$humb["cd"], 'nomeDisciplina'=>$disciplina["nomeDisciplina"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'atributoDisciplina'=>$disciplina["atributoDisciplina"], 'tituloDb'=>$humb["identUnicaDb"], "classeCss"=>"");
               }
            }

            //Pegar Disciplinas Terminais das classes anteriores...
            $this->disciplinasQueTerminamNaClasseAnterior=array();
            $this->cabecalhosDisciplinasQueTerminamNaClasseAnterior = array();
            for ($i=10; $i<$this->classe; $i++) { 
               foreach ($this->disciplinas($this->idPCurso, $i, $this->periodoTurma, "T", array(), [58, 59, 60, 231, 232, 233]) as $discT){

                    foreach (array_filter($this->todasDisciplinasDoCurso, function ($mamale) use ($discT){
                        return $mamale["idPNomeDisciplina"]==$discT["idPNomeDisciplina"];
                    }) as $disciplina ) {

                        $titulo="M<br/>F<br/>D";
                        if($i>=12){
                          $titulo="M<br/>F";
                        }
                        //Verificar se a disciplina antes existiu na classe anterior...
                        
                        if(count($this->disciplinas($this->idPCurso, ($i-1), $this->periodoTurma, "", [$discT["idPNomeDisciplina"]], array(), [58, 59, 60, 231, 232, 233]))>0){
                            $titulo="C<br/>F<br/>D";
                        }

                        $this->disciplinasQueTerminamNaClasseAnterior[] =array('idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'classe'=>$i, 'nomeDisciplina'=>$discT["abreviacaoDisciplina2"], 'atributoDisciplina'=>$disciplina["atributoDisciplina"], "classeCss"=>"", "titulo"=>$titulo, "nenhumaCasa"=>"sim");

                        $this->cabecalhosDisciplinasQueTerminamNaClasseAnterior[] =array('idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'classe'=>$i, "buscar"=>"recurso", 'nomeDisciplina'=>$discT["abreviacaoDisciplina2"], 'atributoDisciplina'=>$disciplina["atributoDisciplina"], "classeCss"=>"", "titulo"=>"R<br/>E<br/>C");

                        $this->cabecalhosDisciplinasQueTerminamNaClasseAnterior[] =array('idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'classe'=>$i, "buscar"=>"cfd", 'nomeDisciplina'=>$discT["abreviacaoDisciplina2"], 'atributoDisciplina'=>$disciplina["atributoDisciplina"], "classeCss"=>"", "titulo"=>$titulo, "nenhumaCasa"=>"sim");
                    }
               }
            }

            //Exbir Mapa Estatístico...
                $this->html .="
                <div style='margin-top:".$mTop."px; width:20%; margin-left: 80%;'>
                <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>
                        <td style='".$this->border()." width:160px;'></td><td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
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
         $this->html .="<table style='".$this->tabela." font-size:8.5pt; margin-top:25px; width:100%;'>
         <tr style='".$this->corDanger."'>
            <td rowspan='3' style='width:10px;".$this->border().$this->bolder.$this->text_center."'>Nº</td><td rowspan='3' style='".$this->border().$this->text_center.$this->bolder."' >NOME COMPLETO</td>";
            //<td rowspan='3' style='width:30px;".$this->border().$this->text_center."'>Nº PROC</td>


        //Introduzir classes as disciplinas terminais das classes anterior
        for ($i=10; $i<$this->classe; $i++) {
            $this->html .="<td style='".$this->text_center.$this->border().$this->bolder.$this->maiuscula."' colspan='".($this->quantasDisciplinasTerminaisNaClasseAnterior($i)*2)."'>".classeExtensa($this, $this->idPCurso, $i)."</td>";
        }

        if($this->classe==13){
           $this->html .="<td colspan='".(count($this->cabecalhoDisciplinas)+2)."' style='".$this->text_center.$this->border().$this->maiuscula."'><strong>".classeExtensa($this, $this->idPCurso, $this->classe)."</strong></td>";
        }else{
           $this->html .="<td colspan='".count($this->cabecalhoDisciplinas)."' style='".$this->text_center.$this->border().$this->maiuscula."'><strong>".classeExtensa($this, $this->idPCurso, $this->classe)."</strong></td>";
        }
        $this->html .="<td rowspan='3' style='".$this->text_center.$this->border()."'>DEFICIÊNCIAS</td><td rowspan='3' style='".$this->text_center.$this->border()."'>RESULT</td>";

         $this->html .="</tr>
         <tr style='".$this->corDanger."'>";

        //Introduzir nome disciplinas terminais das classes anterior
        foreach ($this->disciplinasQueTerminamNaClasseAnterior as $discTermina){
            $this->html .="<td style='".$this->text_center.$this->border().$this->maiuscula."' colspan='2'>".$discTermina["nomeDisciplina"]."</td>";
        }
          //Disciplinas deste ano...
         foreach ($this->disciplinasDaClasse as $apl) {
             $this->html .="<td colspan='".$this->quantasColunasOcupar($apl["idPNomeDisciplina"])."' style='".$this->text_center.$this->border().$this->bolder.$this->maiuscula."'>".$apl["nomeDisciplina"]."</td>";
         }
         if($this->classe==13){
            $this->html .="<td rowspan='2' style='".$this->text_center.$this->border().$this->bolder."'>ECS</td><td rowspan='2' style='".$this->text_center.$this->border().$this->bolder."'>PAP</td>";
        }
         $this->html .="</tr>
         <tr style='".$this->corDanger."'>";

         //Introduzir cabecalhos das disciplinas terminais das classes anterior
        foreach ($this->cabecalhosDisciplinasQueTerminamNaClasseAnterior as $discTermina){
            $this->html .="<td style='".$this->text_center.$this->border().$this->bolder.$this->maiuscula."'>".$discTermina["titulo"]."</td>";
        }

        foreach ($this->cabecalhoDisciplinas as $cabecalho) {
            $this->html .="<td style='".$this->text_center.$this->border().$this->bolder.$this->maiuscula."'>".$cabecalho["titulo"]."</td>";    
        }
        $this->html .="</tr>";

        $i=0;
        foreach ($alunos as $aluno) {
            $pautas = listarItensObjecto($aluno, $tipoPauta,  $condicaoPauta);
            $cfds = listarItensObjecto($aluno, "pautas",  ["idPautaCurso=".$this->idPCurso]);

            $this->idGestDisEspecialidade = valorArray($aluno, "idGestDisEspecialidade", "escola");
            $this->idGestLinguaEspecialidade = valorArray($aluno, "idGestLinguaEspecialidade", "escola");

            $i++;
            if($i%2==0){
                $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
            }else{
                $this->html .="<tr>";
            }

            $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($i)."</td><td style='".$this->border().$this->maiuscula."padding:3px;'>".$aluno->nomeAluno."</td>";
            //<td style='".$this->border()."'>".$aluno->numeroProcesso."</td>

            //Introduzir notas das disciplinas terminais das classes anterior
            foreach ($this->cabecalhosDisciplinasQueTerminamNaClasseAnterior as $discTermina){


                $notaRetorno = $this->retornaNotas($pautas, $cfds, $discTermina["idPNomeDisciplina"], "discTermClAnt", $discTermina["classe"], $discTermina["buscar"], $discTermina["classeCss"], 0, $discTermina["atributoDisciplina"]);

                $this->html .=$this->tratarVermelha($notaRetorno, "", $notaMedia, 0); 
            }
            $cadeirasComDeficiencia="";
            

            //Exibir notas desta classe
            foreach ($this->cabecalhoDisciplinas as $cabecalho) {

                $cd = isset($cabecalho["cd"])?$cabecalho["cd"]:"";

              $notaRetorno =  $this->retornaNotas($pautas, $cfds, $cabecalho["idPNomeDisciplina"], "estaClasse",  "", $cabecalho["tituloDb"], $cabecalho["classeCss"], $numeCasasDecimais, $cabecalho["atributoDisciplina"]);

              $continuidadeDisciplina = isset($cabecalho["continuidadeDisciplina"])?$cabecalho["continuidadeDisciplina"]:"";

              if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="TR"){
                  if($continuidadeDisciplina=="C" && ($cabecalho["tituloDb"]=="cf" || $cabecalho["tituloDb"]=="mf")){

                    if((double)$notaRetorno<10){
                      if($cadeirasComDeficiencia==""){
                        $cadeirasComDeficiencia .=$cabecalho["nomeDisciplina"];
                      }else{
                        $cadeirasComDeficiencia .=", ".$cabecalho["nomeDisciplina"];
                      }
                    } 
                  }
              }else if(valorArray($aluno, "observacaoF", "reconfirmacoes")=="REC"){

                $notaTerminal =  $this->retornaNotas($pautas, $cfds, $cabecalho["idPNomeDisciplina"], "estaClasse",  "", "cf", $cabecalho["classeCss"], $numeCasasDecimais, $cabecalho["atributoDisciplina"]);

                if((double)$notaTerminal<10){
                  if($continuidadeDisciplina=="T" && ($cabecalho["tituloDb"]=="cf" || $cabecalho["tituloDb"]=="mf")){
                    if($cadeirasComDeficiencia==""){
                        $cadeirasComDeficiencia .=$cabecalho["nomeDisciplina"];
                    }else{
                        $cadeirasComDeficiencia .=", ".$cabecalho["nomeDisciplina"];
                    }
                  }
                }
              }

                $this->html .= $this->tratarVermelha($notaRetorno, "", $cabecalho["notaMedia"], $cd);
            }
 
            if($this->classe==13){
                $this->html .=$this->notaAptidaoEstagio($aluno);
            }
            $this->html .="<td style='".$this->border()."'>".$cadeirasComDeficiencia."</td>".$this->observacaoF(valorArray($aluno, "observacaoF", "reconfirmacoes"), valorArray($aluno, "seAlunoFoiAoRecurso", "reconfirmacoes"), $aluno["sexoAluno"]);

            $this->html .="</tr>";
        }
        //Rodapé...
        //Nome das disciplinas
        $this->html .="<tr><td colspan='".(2+count($this->disciplinasQueTerminamNaClasseAnterior)*2)."' style='border:none !important; padding-right:10px;".$this->text_right.$this->border()."' class='text-right bolder'>DISCIPLINA</td>";


        foreach ($this->disciplinasDaClasse as $apl) {
            $this->html .="<td colspan='".$this->quantasColunasOcupar($apl["idPNomeDisciplina"])."' style='".$this->text_center.$this->border()."'>".$apl["nomeDisciplina"]."</td>";
        }
        $this->html.="</tr>";

        //Nome dos Profesores
        $this->html .="<tr><td colspan='".(2+count($this->disciplinasQueTerminamNaClasseAnterior)*2)."' style='border:none !important; padding-right:10px;".$this->text_center.$this->border().$this->text_right."' >PROFESSOR</td>";
        foreach ($this->disciplinasDaClasse as $apl) {
            $this->html .="<td colspan='".$this->quantasColunasOcupar($apl["idPNomeDisciplina"])."' style='".$this->text_center.$this->border()."'>".$this->retornarNomeDoProfessorDaDisciplina($apl["idPNomeDisciplina"])."</td>";
        }
        $this->html.="</tr>";

        //Assinatura dos Professores
        $this->html .="<tr><td colspan='".(2+count($this->disciplinasQueTerminamNaClasseAnterior)*2)."' style='border:none !important; padding-right:10px; ".$this->text_right.$this->border()."' >ASSINATURA</td>";
        foreach ($this->disciplinasDaClasse as $apl) {
            $this->html .="<td colspan='".$this->quantasColunasOcupar($apl["idPNomeDisciplina"])."' style='".$this->text_center.$this->bolder.$this->border()."'></td>";
        }
        $this->html.="</tr>";


        $this->html .="</table>
        <br/>
        <p style='".$this->text_center.$this->maiuscula."'>".$this->rodape()."</p>

        <div style='width:50%;".$this->text_center.$this->maiuscula."'>
        ".$this->assinaturaDirigentes(8)."</div>

        <div style='width:50%; margin-top:-390px; margin-left:50%;".$this->text_center.$this->maiuscula."' >".$this->assinaturaDirigentes(7)."</div>";

         $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Pautas", "Pata Geral-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->trimestreAbr."-".$this->numAno, "Pauta_Geral-Mod2-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->trimestreAbr."-".$this->idPAno, $this->tamanhoFolha, "landscape");
        }


        private function retornaNotas($pautas, $cfds, $idPNomeDisciplina, $tipoNota, $classe="", $campo="", $classeCss="", $numeCasasDecimais=2, $atributoDisciplina){

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
            if($tipoNota=="estaClasse"){

                if($campo==10 || $campo==11 || $campo==12 || $campo==13){
                    foreach($cfds as $cfd){
                        if($cfd["classePauta"]==$campo && $cfd["idPautaDisciplina"]==$idPNomeDisciplina){
                            $nota = nelson($cfd, "cf");
                            break;
                        }
                    }
                }else{
                    foreach ($pautas as $pauta) {
                        if($pauta["idPautaDisciplina"]==$idPNomeDisciplina){
                            $nota = nelson($pauta, $campo);
                            break;
                        }
                    }
                }
            }else{

              if($campo=="cfd"){
                foreach($cfds as $cfd){

                    if($cfd["classePauta"]==$classe && $cfd["idPautaDisciplina"]==$idPNomeDisciplina){
                        $nota = nelson($cfd, "cf");
                        break;
                    }
                }
              }else{

                foreach($cfds as $cfd){
                    if($cfd["classePauta"]==$classe && $cfd["idPautaDisciplina"]==$idPNomeDisciplina){
                        $nota = nelson($cfd, "recurso");
                        break;
                    }
                }
              }
            }
            return $nota;
        }

        private function retornarNomeDoProfessorDaDisciplina($idPNomeDisciplina){
            $array  = $this->selectArray("divisaoprofessores", ["nomeEntidade"], ["classe"=>$this->classe, "idPNomeDisciplina"=>$idPNomeDisciplina, "nomeTurmaDiv"=>$this->turma, "idDivAno"=>$this->idPAno, "idPEscola"=>$_SESSION["idEscolaLogada"], "idPNomeCurso"=>$this->idPCurso]);
            return abreviarDoisNomes(valorArray($array, "nomeEntidade"));
        }
        private function quantasColunasOcupar($idPNomeDisciplina){
            $contador=0;
            foreach ($this->cabecalhoDisciplinas as $cabecalho) {
                if($cabecalho["idPNomeDisciplina"]==$idPNomeDisciplina){
                    $contador++;
                }
            }
            return $contador;
        }
        private function quantasDisciplinasTerminaisNaClasseAnterior($classe){
            $contador=0;
            foreach ($this->disciplinasQueTerminamNaClasseAnterior as $dis) {
                if($dis["classe"]==$classe){
                    $contador++;
                }
            }
            return $contador;
        }

    }
    
    
  
?>
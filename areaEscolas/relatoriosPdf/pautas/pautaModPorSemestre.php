<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php'); 
    class pautaModPorSemestre extends funcoesAuxiliares{
        public $mesPagamentoApartir="";
        public $trimestreApartir=0;
        public $trimestreAbr="I";
        public $trimestreApartirExtensa="";
        public $notaMinima="";
        public $possoGravar="sim";
        public $listaAlunos="";
        public $tamanhoFolha="A4";
        public $tipoPauta="anoActual";

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Pauta Geral Mod2");
        }

         public function exibirPauta(){
            $this->definicoesConselhoNotas = $this->selectArray("definicoesConselhoNotas", ["exprParaAprovado", "exprParaAprovadoComDef", "exprParaAprovadoComRecurso", "exprParaNaoAprovado"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idPAno"=>$this->idPAno]);
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
             <p style='".$this->text_center.$this->sublinhado.$this->bolder."'>PAUTA FINAL - ".$this->numAno."</p>";

            
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
              $this->html .="<p style='".$this->maiuscula."'><strong>".classeExtensa($this->classe, "sim")."</strong> </p>
              <p>TURMA: <strong>".$this->nomeTurma()."</strong></p>";

            $this->disciplinasTerminais = $this->disciplinas($this->idPCurso, $this->classe, $this->periodoTurma, "T", [58, 59, 60, 231, 232, 233]);

            $tipoPauta="pautas";
            $condicaoPauta = ["classePauta=".$this->classe];
            if($this->classe>=10){
                $condicaoPauta[]="idPautaCurso=".$this->idPCurso;
            }
            if($this->idPAno!=$this->idAnoActual){
                $tipoPauta="arquivo_pautas";
                $condicaoPauta[]="idPautaAno=".$this->idPAno;
                $condicaoPauta[]="idPautaEscola=".$_SESSION['idEscolaLogada'];
            
            }
            $alunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), ["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "sexoAluno", "reconfirmacoes.observacaoF", $tipoPauta.'.idPPauta',$tipoPauta.'.idPautaMatricula',$tipoPauta.'.idPautaDisciplina',$tipoPauta.'.macI',$tipoPauta.'.nppI',$tipoPauta.'.nptI',$tipoPauta.'.mtI',$tipoPauta.'.macII',$tipoPauta.'.nppII',$tipoPauta.'.nptII',$tipoPauta.'.mtII',$tipoPauta.'.macIII',$tipoPauta.'.nppIII',$tipoPauta.'.nptIII',$tipoPauta.'.mtIII',$tipoPauta.'.mfd',$tipoPauta.'.exame',$tipoPauta.'.mf',$tipoPauta.'.recurso',$tipoPauta.'.cf',$tipoPauta.'.obs',$tipoPauta.'.seFoiAoRecurso',$tipoPauta.'.classePauta',$tipoPauta.'.semestrePauta',$tipoPauta.'.idPautaCurso',$tipoPauta.'.chavePauta',$tipoPauta.'.idPautaAno',$tipoPauta.'.idPautaEscola', "reconfirmacoes.mfT1", "reconfirmacoes.mfT2", "reconfirmacoes.mfT3", "reconfirmacoes.seAlunoFoiAoRecurso", "escola.idGestDisEspecialidade", "escola.idGestLinguaEspecialidade", "escola.idGestLinguaEspecialidade", "escola.provAptidao", "escola.notaEstagio"]); 
             
            $total=0; $totalF=0; $totalApr=0; $totalAprF=0; $totalRep=0; $totalRepF=0; $totalDes=0; $totalDesF=0;

            foreach ($alunos as $aluno) {

                $total++;
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
            }            

            $cabecalho = array();
            $this->cabecalhoDisciplinas=array();

            foreach ($this->disciplinasTerminais as $disciplina) {

                //Pegar as disciplinas anteriores na classe anterior...
                $existemDisciplinaNoSemestrePassado="nao";

                if($disciplina["disciplinas"]["semestreDisciplina"]=="II"){
                    if(count($this->disciplinas($this->idPCurso, $this->classe, $this->periodoTurma, "", [$disciplina["idPNomeDisciplina"]], [58, 59, 60, 231, 232, 233]))>0){

                        $existemDisciplinaNoSemestrePassado="sim";

                       $this->cabecalhoDisciplinas[] = array('titulo'=>"I", 'nomeDisciplina'=>$disciplina["abreviacaoDisciplina2"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'tituloDb'=>"mf", "classeCss"=>"", "nenhumaCasa"=>"sim", "semestre"=>"I");
                    }
                }

                $this->cabecalhoDisciplinas[] = array('titulo' =>"M<br/>A<br/>C", 'nomeDisciplina'=>$disciplina["abreviacaoDisciplina2"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'tituloDb'=>"macI", "classeCss"=>"", "semestre"=>$disciplina["disciplinas"]["semestreDisciplina"]);
                $this->cabecalhoDisciplinas[] = array('titulo' =>"N<br/>P<br/>P", 'nomeDisciplina'=>$disciplina["abreviacaoDisciplina2"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'tituloDb'=>"nppI", "classeCss"=>"", "semestre"=>$disciplina["disciplinas"]["semestreDisciplina"]);

                $this->cabecalhoDisciplinas[] = array('titulo' =>"M<br/>F<br>D", 'nomeDisciplina'=>$disciplina["abreviacaoDisciplina2"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'tituloDb'=>"mfd", "classeCss"=>"", "semestre"=>$disciplina["disciplinas"]["semestreDisciplina"]); 

                $this->cabecalhoDisciplinas[] = array('titulo' =>"N<br/>E", 'nomeDisciplina'=>$disciplina["abreviacaoDisciplina2"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'tituloDb'=>"exame", "classeCss"=>"", "semestre"=>$disciplina["disciplinas"]["semestreDisciplina"]);

                if($existemDisciplinaNoSemestrePassado=="sim"){

                    $this->cabecalhoDisciplinas[] = array('titulo' =>"M<br/>F", 'nomeDisciplina'=>$disciplina["abreviacaoDisciplina2"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'tituloDb'=>"mf", "classeCss"=>"", "semestre"=>$disciplina["disciplinas"]["semestreDisciplina"]);

                    $this->cabecalhoDisciplinas[] = array('titulo' =>"C<br/>F<br/>D", 'nomeDisciplina'=>$disciplina["abreviacaoDisciplina2"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'tituloDb'=>"cf", "classeCss"=>"border-right:solid black 2px;", "semestre"=>$disciplina["disciplinas"]["semestreDisciplina"]);                        
                }else{
                    $this->cabecalhoDisciplinas[] = array('titulo' =>"M<br/>F", 'nomeDisciplina'=>$disciplina["abreviacaoDisciplina2"], 'idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], 'tituloDb'=>"mf", "classeCss"=>"border-right:solid black 2px;", "semestre"=>$disciplina["disciplinas"]["semestreDisciplina"]);
                }
            }

             
            //Exbir Mapa Estatístico...
            $this->html .="
            <div style='margin-top:-134px; width:20%; margin-left: 83%; position:absolute;'>
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

         $this->html .="<table style='".$this->tabela." font-size:8pt; margin-top:25px; width:100%;'>
         <tr style='".$this->corDanger."'>
            <td rowspan='2' style='width:10px;".$this->border().$this->bolder.$this->text_center."'>N.º</td><td rowspan='2' style='".$this->border().$this->text_center.$this->bolder."' >NOME COMPLETO</td>";

          //Disciplinas deste ano...
        foreach ($this->disciplinasTerminais as $apl) {
            $this->html .="<td colspan='".$this->quantasColunasOcupar($apl["idPNomeDisciplina"])."' style='".$this->text_center.$this->border().$this->bolder.$this->maiuscula."'>".$apl["abreviacaoDisciplina2"]."</td>";
        }
         if($this->classe==(9+$this->duracaoCurso)){
            $this->html .="<td rowspan='2' style='".$this->text_center.$this->border().$this->bolder."'>ECS</td>";
        }

         $this->html .="<td rowspan='2' style='".$this->border().$this->bolder.$this->text_center."'>RESULT</td>

         </tr>
         <tr style='".$this->corDanger."'>";

        foreach ($this->cabecalhoDisciplinas as $cabecalho) {
            $this->html .="<td style='".$this->text_center.$this->border().$this->bolder.$this->maiuscula."'>".$cabecalho["titulo"]."</td>";    
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
            $pautas = listarItensObjecto($aluno, $tipoPauta,  $condicaoPauta);

            $this->idGestDisEspecialidade = valorArray($aluno, "idGestDisEspecialidade", "escola");
            $this->idGestLinguaEspecialidade = valorArray($aluno, "idGestLinguaEspecialidade", "escola");


            $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($i)."</td><td style='".$this->border().$this->maiuscula."padding:3px;'>".$aluno["nomeAluno"]."</td>";
            //<td style='".$this->border()."'>".$aluno->numeroProcesso."</td>

            $cadeirasComDeficiencia="";
            //Exibir notas desta classe
            foreach ($this->cabecalhoDisciplinas as $cabecalho) {

                $numeCasasDecimais = isset($cabecalho["nenhumaCasa"])?0:1;

              $notaRetorno =  $this->retornaNotas($pautas, $cabecalho["idPNomeDisciplina"], $cabecalho["semestre"], $cabecalho["tituloDb"], $cabecalho["classeCss"], $numeCasasDecimais, "");
                $this->html .= $this->tratarVermelha($notaRetorno, "", 10, $numeCasasDecimais);
            }

            if($this->classe==(9+$this->duracaoCurso)){
                $this->html .= $this->tratarVermelha(valorArray($aluno, "notaEstagio", "escola"), "", 10, 0);
            }
            $this->html .=$this->observacaoF(valorArray($aluno, "observacaoF", "reconfirmacoes"), valorArray($aluno, "seAlunoFoiAoRecurso", "reconfirmacoes"), $aluno["sexoAluno"])."</tr>";
        }


        $this->html .="</table>
        <br/>
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

         $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Pautas", "Pata Geral-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->trimestreAbr."-".$this->numAno, "Pauta_Geral-Mod2-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->trimestreAbr."-".$this->idPAno, $this->tamanhoFolha, "landscape");
        }


        private function retornaNotas($pautas, $idPNomeDisciplina, $semestre="", $campo="", $classeCss="", $numeCasasDecimais=2, $atributoDisciplina){

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

                if($pauta["idPautaDisciplina"]==$idPNomeDisciplina && $pauta["semestrePauta"]==$semestre){
                    $nota = nelson($pauta, $campo);
                    break;
                }
            }
            return $nota;
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
    }
    
    
  
?>
<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class relatorio extends funcoesAuxiliares{
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Horário de Turmas"); 
             $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null;
         
            $this->nomeCurso();
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", ["horarioTurmas"], [$this->classe, $this->idPCurso], "") || count($this->selectArray("listaturmas", ["nomeTurma"], ["idListaAno"=>$this->idAnoActual, "idPEscola"=>$_SESSION['idEscolaLogada'], "idCoordenadorTurma"=>$_SESSION['idUsuarioLogado'], "classe"=>$this->classe, "nomeTurma"=>$this->turma, "idPNomeCurso"=>$this->idPCurso]))>0){

                $this->html="<html style='margin:30px;'>
                <head>
                    <title>Horário da Turma</title>
                </head>
                <body>".$this->fundoDocumento("../../../");
                $this->relatorio();
            }else{
              $this->negarAcesso();
            }
        }

         private function relatorio(){
            $this->nomeTurma();

            $semestreActivo = retornarSemestreActivo($this, $this->idPCurso, $this->classe);

            $this->divisaoProfessor = $this->selectCondClasseCurso("array", "divisaoprofessores", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$this->idAnoActual, "classe"=>$this->classe, "nomeTurmaDiv"=>$this->turma, "semestre"=>$semestreActivo], $this->classe, ["idPNomeCurso"=>$this->idPCurso]);

            $this->gerenciador_periodo = listarItensObjecto($this->sobreEscolaLogada, "gerencPerido", ["idGerPerAno=".$this->idAnoActual, "periodoGerenciador=".valorArray($this->sobreTurma, "periodoT")]);

           
                $this->html .=$this->cabecalho()."<p style='".$this->bolder.$this->sublinhado.$this->text_center."'>HORÁRIO - ".$this->numAno."</p>";

                $topMapaEstatisco=-510;
                if($this->classe>=10){

                    $topMapaEstatisco=-530;
                    if($this->tipoCurso=="pedagogico"){
                        $this->html .="<p style='".$this->miniParagrafo."'>Curso: <strong>".$this->areaFormacaoCurso."</strong></p>
                        <p style='".$this->miniParagrafo."'>Opção: <strong>".$this->nomeCurso."</strong></p>";
                    }else if($this->tipoCurso=="tecnico"){
                        $this->html .="<p style='".$this->miniParagrafo."'>Área de Formação: <strong>".$this->areaFormacaoCurso."</strong></p>
                        <p style='".$this->miniParagrafo."'>Curso: <strong>".$this->nomeCurso."</strong></p>";
                    }else{
                        $topMapaEstatisco=-510;
                        $this->html .="<p style='".$this->miniParagrafo."'>Curso: <strong>".$this->nomeCurso."</strong></p>";
                    }            
                }
                $this->html .="<p style='".$this->miniParagrafo."'>Classe: <strong>".$this->classeExt." / ".$this->designacaoTurma."</p>

                <p style='".$this->miniParagrafo."'>Período: <strong>".valorArray($this->sobreTurma, "periodoT")." / Sala n.º: ".completarNumero(valorArray($this->sobreTurma, "numeroSalaTurma"))."</p>
                <p>Director: <strong>".valorArray($this->sobreTurma, "nomeEntidade")."</p>";    
            

           $this->html .="<table style='".$this->tabela." width:100%;'>
                <thead>
                    <tr style='".$this->corDanger."'>
                    <td style='".$this->border().$this->bolder.$this->text_center."'>Horas</td>";
                    for($dia=1; $dia<=valorArray($this->gerenciador_periodo, "numeroDias"); $dia++){
                        $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".diaSemana2($dia)."</td>";
                    }
                        
                    $this->html .="</tr>
                </thead>
                <tbody>
            ";

         for($tempo=1; $tempo<=valorArray($this->gerenciador_periodo, "numeroTempos"); $tempo++){
            $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$this->retornarHora($tempo)."</td>";
            for($dia=1; $dia<=valorArray($this->gerenciador_periodo, "numeroDias"); $dia++){
                $this->html .=$this->retornarNomeDisciplina($dia, $tempo);
            }            
            $this->html .="</tr>";
            if($tempo==valorArray($this->gerenciador_periodo, "intevaloDepoisDoTempo")){
                $this->html .="<tr><td colspan='6' style='".$this->text_center.$this->bolder.$this->border()."'>Intervalo</td></tr>";
            }
        }
        $this->html .="</tbody></table><br/>";

        $this->html .="<div style='width:90%; margin-left:5%;'>
        <table style='width:100%;".$this->tabela."' >
        <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."width:50%;'>Disciplina</td><td style='".$this->border().$this->bolder.$this->text_center."'>Professor</td></tr>";

            foreach ($this->divisaoProfessor as $horario) {
                
                $this->html .="<tr><td style='".$this->border()."'>".$horario["nomeDisciplina"]."</td><td style='".$this->border()."'>".nelson($horario, "nomeEntidade")."</td></tr>";
            }

            $this->html .="</table></div>
            <p>".$this->rodape().".</p>
        <div>".$this->assinaturaDirigentes(8)."</div>";

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Horarios_Turmas", "Horário de Turma-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->numAno, "Horario_Turma-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPAno); 
        }

        private function retornarHora($tempo){

            $duracaoPorTempo = valorArray($this->gerenciador_periodo, "duracaoPorTempo");

            $duracaoIntervalo = valorArray($this->gerenciador_periodo, "duracaoIntervalo");

            $tempoEntrada = valorArray($this->gerenciador_periodo, "horaEntrada");
            
            $tempoEntrada = explode(":", $tempoEntrada);

            $horaEntrada = intval(isset($tempoEntrada[0])?$tempoEntrada[0]:0);
            
            $minutoEntrada = intval(isset($tempoEntrada[1])?$tempoEntrada[1]:0);

            $parteA=0;
            if($tempo==1){
                $parteA = mktime($horaEntrada, $minutoEntrada, 0, 0,0,0);
            }else{
                if($tempo<= valorArray($this->gerenciador_periodo, "intevaloDepoisDoTempo")){
                    $parteA = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*($tempo-1)), 0, 0,0,0);
                }else{
                    $parteA = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*($tempo-1) + $duracaoIntervalo), 0, 0,0,0);
                }
            }
            

            $parteB=0;
            if($tempo<=valorArray($this->gerenciador_periodo, "intevaloDepoisDoTempo")){
                $parteB = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*$tempo), 0, 0,0,0);
            }else{
                $parteB = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*$tempo + $duracaoIntervalo), 0, 0,0,0);
            }
            return date("H:i", $parteA)."-".date("H:i", $parteB);
        }

        

        private function retornarNomeDisciplina($dia, $tempo){
            $array = $this->selectCondClasseCurso("array", "horario", ["abreviacaoDisciplina2"], ["turma"=>$this->turma, "classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idHorAno"=>$this->idPAno, "dia"=>$dia, "tempo"=>$tempo], $this->classe, ["idPNomeCurso"=>$this->idPCurso]);
            
            return "<td style='".$this->border().$this->text_center."'>".valorArray($array, "abreviacaoDisciplina2")."</td>";

           
        }
    }

new relatorio(__DIR__);
    
    
  
?>
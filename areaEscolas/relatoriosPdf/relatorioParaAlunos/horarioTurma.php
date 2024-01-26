<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    } 
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class relatorio extends funcoesAuxiliares{
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Horário de Turmas");
            
            $vetor = $this->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso LEFT JOIN turmas ON idTurmaMatricula=idPMatricula", "*", "idPMatricula=:idPMatricula AND idMatEscola=:idMatEscola AND estadoAluno in ('A', 'Y') AND (idTurmaAno=".$this->idAnoActual." OR idTurmaAno IS NULL) AND (idTurmaEscola=".$_SESSION["idEscolaLogada"]." OR idTurmaEscola IS NULL)", [$_SESSION["idUsuarioLogado"], $_SESSION["idEscolaLogada"]]);

            $this->idPAno = $this->idAnoActual;
            $this->idPCurso = valorArray($vetor, "idMatCurso");
            $this->classe = valorArray($vetor, "classeActualAluno");
            $this->turma = valorArray($vetor, "nomeTurma");

                      
            $this->nomeCurso();
            $this->numAno();

            $this->html="<html style='margin:30px;'>
            <head>
                <title>Horário da Turma</title>
            </head>
            <body>
            ".$this->fundoDocumento("../../../").$this->cabecalho();
            if($this->verificacaoAcesso->verificarAcesso(1, [], [], "")){
                $this->relatorio();
            }else{
                $this->negarAcesso();
            }
        }

         private function relatorio(){
            $sobreTurma = $this->selectCondClasseCurso("array", "listaturmas LEFT JOIN entidadesprimaria ON idCoordenadorTurma=idPEntidade", "*", "classe=:classe AND nomeTurma=:nomeTurma AND idListaAno=:idListaAno AND idListaEscola=:idListaEscola", [$this->classe, $this->turma, $this->idPAno, $_SESSION["idEscolaLogada"], $this->idPCurso], $this->classe, " AND idListaCurso=:idListaCurso");

            $divisaoprofessores = $this->selectCondClasseCurso("json", "divisaoprofessores LEFT JOIN nomedisciplinas ON idDivDisciplina=idPNomeDisciplina LEFT JOIN entidadesprimaria ON idDivEntidade=idPEntidade LEFT JOIN disciplinas ON idPNomeDisciplina=idFNomeDisciplina", "*", "idDivEscola=:idDivEscola AND idDivAno=:idDivAno AND idDiscEscola=idDivEscola AND classeDisciplina=classe AND classe=:classe AND nomeTurmaDiv=:nomeTurmaDiv AND periodoDisciplina=:periodoDisciplina", [$_SESSION["idEscolaLogada"], $this->idPAno, $this->classe, $this->classe, $this->periodoTurma, $this->idPCurso], $this->classe, " AND idDiscCurso=idDivCurso  AND idDiscCurso=:idDiscCurso", "ordenacao ASC");


            $this->selectCondClasseCurso("array", "divisaoprofessores LEFT JOIN entidadesprimaria ON idDivEntidade=idPEntidade LEFT JOIN nomedisciplinas ON idDivDisciplina=idPNomeDisciplina", "nomeDisciplina, nomeEntidade", "idDivAno=:idDivAno AND idDivEscola=:idDivEscola AND classe=:classe AND nomeTurmaDiv=:nomeTurmaDiv", [$this->idPAno, $_SESSION["idEscolaLogada"], $this->classe, $this->turma, $this->idPCurso], $this->classe, " AND idDivCurso=:idDivCurso");

            $this->gerenciador_periodo = $this->selectArray("gerenciador_periodo", "*", "idGerPerEscola=:idGerPerEscola AND idGerPerAno=:idGerPerAno AND periodoGerenciador=:periodoGerenciador", [$_SESSION["idEscolaLogada"], $this->idPAno, valorArray($sobreTurma, "periodoT")]);
            

            $this->html .="
            <p style='".$this->bolder.$this->sublinhado.$this->text_center."'>HORÁRIO - ".$this->numAno."</p>
            ";

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


        $this->html .="<p style='".$this->miniParagrafo."'>Classe: <strong>".$this->classeExt." / ".$this->nomeTurma()."</p>

            <p style='".$this->miniParagrafo."'>Período: <strong>".valorArray($sobreTurma, "periodoT")." / Sala n.º: ".completarNumero(valorArray($sobreTurma, "numeroSalaTurma"))."</p>";
            if($this->classe<=4){
                $this->html .="<p>Professor: <strong>".valorArray($divisaoprofessores, "nomeEntidade")."</p>";
            }else{
                $this->html .="<p>Director: <strong>".valorArray($sobreTurma, "nomeEntidade")."</p>";
            }

        $this->html .="        
                <table style='".$this->tabela." width:100%;'>
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
                $this->html .="<tr><td colspan='6' style='".$this->text_center.$this->bolder.$this->border()."'>INTERVALO</td></tr>";
            }
        }
        $this->html .="</tbody></table><br/>";

        if($this->classe>4){
        $this->html .="<div style='width:90%; margin-left:5%;'>
        <table style='width:100%;".$this->tabela."' >
        <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."width:50%;'>Disciplina</td><td style='".$this->border().$this->bolder.$this->text_center."'>Professor</td></tr>";

        foreach ($this->selectCondClasseCurso("array", "divisaoprofessores LEFT JOIN nomedisciplinas ON idDivDisciplina=idPNomeDisciplina LEFT JOIN entidadesprimaria ON idDivEntidade=idPEntidade LEFT JOIN disciplinas ON idPNomeDisciplina=idFNomeDisciplina", "*", "idDivEscola=:idDivEscola AND idDivAno=:idDivAno AND idDiscEscola=idDivEscola AND classeDisciplina=classe AND classe=:classe AND nomeTurmaDiv=:nomeTurmaDiv AND periodoDisciplina=:periodoDisciplina", [$_SESSION["idEscolaLogada"], $this->idPAno, $this->classe, $this->turma, valorArray($sobreTurma, "periodoTurma"), $this->idPCurso], $this->classe, " AND idDiscCurso=idDivCurso AND idDiscCurso=:idDiscCurso", "ordenacao ASC") as $horario) {
            
            $this->html .="<tr><td style='".$this->border()."'>".$horario->nomeDisciplina."</td><td style='".$this->border()."'>".$horario->nomeEntidade."</td></tr>";
        }

        $this->html .="</table>
        </div>";
        }
            $this->exibir("", "Horário da Turma-".$this->numAno);
        }

        private function retornarHora($tempo){

            $duracaoPorTempo = valorArray($this->gerenciador_periodo, "duracaoPorTempo");

            $duracaoIntervalo = valorArray($this->gerenciador_periodo, "duracaoIntervalo");

            $tempoEntrada = valorArray($this->gerenciador_periodo, "horaEntrada");
            
            $tempoEntrada = explode(":", $tempoEntrada);

            $horaEntrada = isset($tempoEntrada[0])?$tempoEntrada[0]:0;
            
            $minutoEntrada = isset($tempoEntrada[1])?$tempoEntrada[1]:0;

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
            $ret = $this->selectCondClasseCurso("um", "horario LEFT JOIN nomedisciplinas ON idHorDisc=idPNomeDisciplina", "abreviacaoDisciplina2", "turma=:turma AND classe=:classe AND idHorEscola=:idHorEscola AND idHorAno=:idHorAno AND dia=:dia AND tempo=:tempo", [$this->turma, $this->classe, $_SESSION["idEscolaLogada"], $this->idPAno, $dia, $tempo, $this->idPCurso], $this->classe, " AND idHorCurso=:idHorCurso");
            if($ret=="" || $ret==NULL){
                return "<td style='".$this->border().$this->text_center."'>&nbsp;</td>";
            }else{
                return "<td style='".$this->border().$this->text_center."'>".$ret."</td>";
            }
        }
    }

new relatorio(__DIR__);
    
    
  
?>
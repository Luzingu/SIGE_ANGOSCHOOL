<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class relatorio extends funcoesAuxiliares{
        function __construct($caminhoAbsoluto){
           parent::__construct("Rel-Horário do Professor"); 
            $this->idPAno = $this->idAnoActual;
            $this->numAno();

            $tipoVisualizacao="professor";
            $this->comRubrica="nao";
            if(isset($_GET["idPProfessor"])){
                $tipoVisualizacao="administrador";
                $this->idPProfessor = $_GET["idPProfessor"];
                $this->comRubrica="sim";
            }else{
                $this->idPProfessor = $_SESSION["idUsuarioLogado"];
            } 
            $this->gerenciador_periodo = listarItensObjecto($this->sobreEscolaLogada, "gerencPerido", ["idGerPerEscola=".$_SESSION['idEscolaLogada'], "idGerPerAno=".$this->idAnoActual]);

            $this->dados = $this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$this->idPProfessor, "escola.idEntidadeEscola"=>$_SESSION["idEscolaLogada"]], ["escola"]);
            
            $this->html="<html >
            <head>
                <title>Horário do Professor</title>
            </head>
            <body style='margin-top:-40px; margin-bottom:-40px;font-family: Times new Roman !important;margin-left: -20px;margin-right: -20px;'>".$this->fundoDocumento("../../../").$this->cabecalho();
            if($tipoVisualizacao=="professor"){
                $this->modeloIPAG();  
            }else if($this->verificacaoAcesso->verificarAcesso("", "relatorioFuncionario", array(), "")){
                $this->modeloIPAG();                  
                
            }else{
              $this->negarAcesso();
            }
            
        }

         private function modeloIPAG(){

            $this->horarioProfesor = $this->selectArray("horario", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idHorAno"=>$this->idPAno, "idPEntidade"=>$this->idPProfessor]);

           $turnosProfessor=array();
           foreach (distinct2($this->horarioProfesor, "periodoT") as $turno) {
                $turnosProfessor[] = $turno;
           }

           $temposPorTurnoProfessor=array();
           foreach ($turnosProfessor as $turno) {
                foreach(distinct2(condicionadorArray($this->horarioProfesor, ["periodoT=".$turno]), "tempo") as $tempo){
                    $temposPorTurnoProfessor[]=["turno"=>$turno, "tempo"=>$tempo];
                }
           }
            $temposPorTurnoProfessor = ordenar($temposPorTurnoProfessor, "tempo ASC");

           $diasProfessor = array();
            foreach (distinct2($this->horarioProfesor, "dia") as $dia) {
               $diasProfessor[] = $dia;
            }
            usort($diasProfessor, "usortTest");

           if(valorArray($this->dados, "nivelAcademicoEntidade")=="Médio"){
               $areaFormacao = valorArray($this->dados, "cursoEnsinoMedio");
            }else if(valorArray($this->dados, "nivelAcademicoEntidade")=="Licenciado" || valorArray($this->dados, "nivelAcademicoEntidade")=="Bacharel"){
                $areaFormacao = valorArray($this->dados, "cursoLicenciatura");
            }else if(valorArray($this->dados, "nivelAcademicoEntidade")=="Mestre"){
                $areaFormacao = valorArray($this->dados, "cursoMestrado");
            }else if(valorArray($this->dados, "nivelAcademicoEntidade")=="Doutor"){
               $areaFormacao = valorArray($this->dados, "cursoDoutoramento");
            }else{
                $areaFormacao="";
            }

        $fontoEntidade = valorArray($this->dados, "fotoEntidade");

        $formatoFoto = isset(explode(".", $fontoEntidade)[1])?explode(".", $fontoEntidade)[1]:"";
        if($formatoFoto=="gif"){
            $fontoEntidade ="default.png";
        }
           $this->html .="<div style='position: absolute;'><div style='margin-top: -155px; margin-left: -500px;'>".$this->assinaturaDirigentes(7, "", "", "", $this->comRubrica)."</div></div>

          <p style='margin-top:30px;".$this->text_center.$this->bolder.$this->sublinhado."'>HORÁRIO INDIVIDUAL DO PROFESSOR</p>
        <div>
          <div id='fotoAluno' style='margin-top: -110px; text-align: right; width: 100%;'>
             <img src='../../../fotoUsuarios/".$fontoEntidade."' style='border:solid #428bca 1px; border-radius: 10px; width: 100px; height: 110px;'>
             </div>
        </div>

        <div style='margin-top:-12px;'>
        <p style='".$this->miniParagrafo."'>Nome: <strong>".valorArray($this->dados, "nomeEntidade")."</strong></p>                                                   
        <p style='".$this->miniParagrafo."'>Unidade Orgânica: <strong>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</strong></p>                                                    
        <p style='".$this->miniParagrafo."'>Categoria: <strong>".valorArray($this->dados, "categoriaEntidade")."</strong></p>";

      
        $this->html .="<p style='".$this->miniParagrafo."'>Cursos que Leciona:";
        $cur="";
        foreach (distinct2($this->horarioProfesor, "nomeCurso") as $curso) {
            if($cur!=""){
                $cur .=", ";
            }
            $cur.=$curso;
        }
        $this->html .=" <strong>".$cur."</strong></p>

        <p style='".$this->miniParagrafo."'>Classes: <strong>";

        $cl="";
        $classsesProfessor=array();
        foreach (distinct2($this->horarioProfesor, "classe") as $classes) {
            $classsesProfessor[] = $classes;
            if($cl!=""){
                $cl .=", ";
            }
            $cl .= $classes.".ª";
        }

        $this->html .=$cl."</strong>&nbsp;&nbsp;&nbsp; Nº de Turmas: ";
        $tur="";
        $totalTurmas=0;
        foreach (distinct2($this->horarioProfesor, "idPNomeCurso") as $curso) {
            foreach ($classsesProfessor as $classe) {
               foreach (distinct2(condicionadorArray($this->horarioProfesor, ["idPNomeCurso=".$curso, "classe=".$classe]), "designacaoTurmaHor") as $turma) {
                    $totalTurmas++;

                     $jp = $this->selectUmElemento("nomecursos", "abrevCurso", ["idPNomeCurso"=>$curso]).$classe.$turma;
                    if($tur!=""){
                        $tur .=", ";
                    }
                    $tur .=$jp;
                }
            }
        }
        
        $this->html .="<strong>".completarNumero($totalTurmas)."</strong> &nbsp; &nbsp; Turmas: <strong>".$tur."</strong></p>
        <p style='".$this->text_justify.$this->miniParagrafo."'>Disciplinas: <strong>";

        $disc="";
        foreach (distinct2($this->horarioProfesor, "nomeDisciplina") as $disciplina) {
            if($disc!=""){
                $disc .=", ";
            }
            $disc .=$disciplina;
        }
        
        /*Guardando as linhas tabela do professor para que possam ser lançadas na tabela e aproveitando calcular o número de tempos...*/
        $contadorTemposProfessor=0;
        $linhasTabelaHorarioProfessor="";
        foreach ($diasProfessor as $dia) {
            $linhasTabelaHorarioProfessor .="<td style='".$this->maiuscula.$this->border().$this->text_center."' colspan='3'>".diaSemana2($dia)."</td>";
        }
        $linhasTabelaHorarioProfessor .="</tr><tr style='".$this->bolder."'>";
        foreach ($diasProfessor as $dia) {
            $linhasTabelaHorarioProfessor .="<td style='".$this->maiuscula.$this->border().$this->text_center."'>Turma</td><td style='".$this->maiuscula.$this->border().$this->text_center."'>Disc.</td><td style='".$this->maiuscula.$this->border().$this->text_center."'>Sala</td></td>";
        }
        $linhasTabelaHorarioProfessor .="</tr>";

        foreach ($turnosProfessor as $turno) {
            $linhasTabelaHorarioProfessor .="<tr><td style='".$this->maiuscula.$this->border().$this->text_center.$this->corDanger."' colspan='".(2+count($diasProfessor)*3)."'>HORÁRIO ".$turno."</td></tr>";

            foreach ($temposPorTurnoProfessor as $tempo) {
               if($tempo["turno"]==$turno){
                    $linhasTabelaHorarioProfessor .="<tr><td style='".$this->maiuscula.$this->border().$this->text_center."'>".$tempo["tempo"]."º</td><td style='".$this->maiuscula.$this->border().$this->text_center."'>".$this->retornarHora($turno, $tempo["tempo"])."</td>";
                    foreach ($diasProfessor as $dia) {
                        $linhasTabelaHorarioProfessor .="<td style='".$this->maiuscula.$this->border().$this->text_center."'>".$this->retornarCampo($tempo["tempo"], $tempo["turno"], $dia, "turma")."</td><td style='".$this->maiuscula.$this->border().$this->text_center."'>".$this->retornarCampo($tempo["tempo"], $tempo["turno"], $dia, "abreviacaoDisciplina2")."</td><td style='".$this->maiuscula.$this->border().$this->text_center."'>";

                       $numSala = $this->retornarCampo($tempo["tempo"], $tempo["turno"], $dia, "numeroSalaTurma");
                       
                       if($this->retornarCampo($tempo["tempo"], $tempo["turno"], $dia, "abreviacaoDisciplina2")!=""){
                           $contadorTemposProfessor++;
                       }
                       
                       if($numSala=="" || $numSala==NULL){
                            $numSala="";
                       }else{
                            $numSala = completarNumero($numSala);
                       }
                        $linhasTabelaHorarioProfessor .=$numSala."</td>";
                    }
                    $linhasTabelaHorarioProfessor .="</tr>";
                    if($tempo["tempo"]==$this->retornarCampoPorPeriodo($turno, "intevaloDepoisDoTempo")){
                        $linhasTabelaHorarioProfessor .="<tr><td style='".$this->maiuscula.$this->border().$this->text_center."' colspan='".(2+count($diasProfessor)*3)."'>INTERVALO</td></tr>";
                    }
               }
            }
        }
        
        /*Fim linhas da tabela do horário do professor*/
        
        $this->html .="".$disc.".</strong></p>                                                  
        <p style='".$this->miniParagrafo."'>Formação do Ensino Médio: <strong>".valorArray($this->dados, "cursoEnsinoMedio")."</strong>, &nbsp;&nbsp;Carga Horária:  <strong>".completarNumero($contadorTemposProfessor)."</strong> Tempos, Total: <strong>".completarNumero(($contadorTemposProfessor) )."</strong> Tempos</p>                           
        <p style='".$this->miniParagrafo."'>Hab.Liter.Ens.Sup.: <strong>".valorArray($this->dados, "nivelAcademicoEntidade")."</strong>, &nbsp;&nbsp;Especialidade: <strong>".$areaFormacao."</strong></p>
        <p style='".$this->miniParagrafo."'>Cargo/Função: <strong>".valorArray($this->dados, "funcaoEnt", "escola")."</strong> &nbsp;&nbsp;&nbsp; Turnos: <strong>";
        $tur="";
        foreach ($turnosProfessor as $turno) {
           if($tur==""){
            $tur .=$turno;
           }else{
                $tur .=", ".$turno;
           }
        }
        $this->html .=$tur."</strong></p>
        <p class=''>Contáctos Telefonicos: <strong>".valorArray($this->dados, "numeroTelefoneEntidade")."</strong> &nbsp;&nbsp; Correio Electrónico: <strong>".valorArray($this->dados, "emailEntidade")."</strong></p></div>
        <table style='".$this->tabela."width:100%; font-size:7.5pt;'>";
        $this->html .="<tr style='".$this->bolder."'><td style='".$this->maiuscula.$this->border().$this->text_center."' rowspan='2'>T</td><td style='".$this->maiuscula.$this->border().$this->text_center."' rowspan='2'>Horas</td>";
        
         
        $this->html .=$linhasTabelaHorarioProfessor."</table>
        <div style='width:50%;'>
        <p style='".$this->text_center."'>O(a) Professor(a)</p>
        <p style='".$this->text_center.$this->miniParagrafo."'>______________________________</p><p style='".$this->text_center."'>".valorArray($this->dados, "nomeEntidade")."</p></div>
            <div style='margin-left:50%; width:50%; margin-top:-200px;'>".$this->assinaturaDirigentes(8)."</div>";
            
        $this->exibir("", "Horário-".valorArray($this->dados, "nomeEntidade")."-".$this->numAno);
        }



        function retornarCampo($tempo, $turno, $dia, $campoPesquisado=""){
            $retorno="";
            foreach ($this->horarioProfesor as $horario) {
                if($horario->tempo==$tempo && $horario->periodoT==$turno && $horario->dia==$dia){

                    if($campoPesquisado=="turma"){
                        
                        $matondo=$horario["designacaoTurmaHor"];
                        
                        if(trim($matondo)==NULL || trim($matondo)==""){
                            $matondo = $horario["designacaoTurmaHor"];
                        }
                        $retorno = $matondo;
                        if($_SESSION["idEscolaLogada"]!=10){
                            $retorno = $horario["abrevCurso"].$horario["classe"].$matondo;
                        }
                    }else{
                        $retorno = $horario[$campoPesquisado];
                    }
                    break;
                }
            }
            return $retorno;
        }

        function retornarCampoPorPeriodo($periodo, $campo){
            $retorno="";
            foreach ($this->gerenciador_periodo as $ger) {
               if($ger["periodoGerenciador"]==$periodo){
                     $retorno= $ger[$campo];
                    break;
               }
            }
            return $retorno;
        }

        private function retornarHora($periodo, $tempo){

            $duracaoPorTempo = $this->retornarCampoPorPeriodo($periodo, "duracaoPorTempo");

            $duracaoIntervalo = $this->retornarCampoPorPeriodo($periodo, "duracaoIntervalo");

            $tempoEntrada = explode(":", $this->retornarCampoPorPeriodo($periodo, "horaEntrada"));

            $horaEntrada = intval(isset($tempoEntrada[0])?$tempoEntrada[0]:0);
            $minutoEntrada = intval(isset($tempoEntrada[1])?$tempoEntrada[1]:0);

            $parteA=0;
            if($tempo==1){
                $parteA = mktime($horaEntrada, $minutoEntrada, 0, 0,0,0);
            }else{
                if($tempo<=$this->retornarCampoPorPeriodo($periodo, "intevaloDepoisDoTempo")){
                    $parteA = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*($tempo-1)), 0, 0,0,0);
                }else{
                    $parteA = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*($tempo-1) + $duracaoIntervalo), 0, 0,0,0);
                }
            }
            

            $parteB=0;
            if($tempo<=$this->retornarCampoPorPeriodo($periodo, "intevaloDepoisDoTempo")){
                $parteB = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*$tempo), 0, 0,0,0);
            }else{
                $parteB = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*$tempo + $duracaoIntervalo), 0, 0,0,0);
            }
            return date("H:i", $parteA)."-".date("H:i", $parteB);
        }

        
       
    }

    new relatorio(__DIR__);
?>
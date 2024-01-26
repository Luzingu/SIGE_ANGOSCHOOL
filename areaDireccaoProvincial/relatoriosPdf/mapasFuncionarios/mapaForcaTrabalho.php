<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliaresDb.php';

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);

            parent::__construct("Rel-Mapa de Força de Trabalho");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
            $this->periodoProfessor = isset($_GET["periodoProfessor"])?$_GET["periodoProfessor"]:"todos";
            
            $this->html .="<html>
                        <head>
                            <title>Mapa de Levantamento da Força de Trabalho</title>
                            <style>
                                table tr td{
                                    padding:5px;
                                }
                            </style>
                        </head>
                        <body>";

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aRelEstatistica"])){
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

        private function mapa(){
            $listaProfessores=array();
            foreach($this->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPEscola", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), "Pública", "A"], "idPEscola ASC") as $a){
                $listaProfessores = array_merge($listaProfessores, $this->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND idEntidadeEscola=:idEntidadeEscola AND tipoPessoal=:tipoPessoal AND efectividade=:efectividade", ["A", $a->idPEscola, "docente", "V"], "nomeEntidade ASC"));
            }

            $this->html .="<div style='position: absolute;'><div style='margin-top: 10px; width:280px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho()."<br/>
            <p style='".$this->text_center.$this->bolder.$this->maiuscula."'>MAPA DE LEVANTAMENTO DA FORÇA DE TRABALO";
            if($this->periodoProfessor!="todos" && $this->periodoProfessor!=""){
                $this->html .=" DOS PROFESSORES DO PERÍODO ". $this->periodoProfessor;
            }
            
            $this->html .=" - ".$this->numAno."</p>
            
            <br/><table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>N.º</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Agente</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Nome Completo</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Escola</td><td colspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Especialidade</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Disciplinas(S) <br/>a Leccionar</td><td colspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Tempos Lectivos</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Total</td>
                </tr>
                <tr style='".$this->corDanger."'> 
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>Médio</td><td style='".$this->bolder.$this->text_center.$this->border()."'>Superior</td><td style='".$this->bolder.$this->text_center.$this->border()."'>Professor</td><td style='".$this->bolder.$this->text_center.$this->border()."'>Cargo<br/>Pedagógico</td>
                </tr>";

                $i=0;
                foreach ($listaProfessores as $professor) {
                    
                    if(count($this->selectArray("horario LEFT JOIN divisaoprofessores ON idDivDisciplina=idHorDisc LEFT JOIN nomedisciplinas ON idDivDisciplina=idPNomeDisciplina LEFT JOIN nomecursos ON idHorCurso=idPNomeCurso LEFT JOIN listaturmas ON idListaEscola=idDivEscola LEFT JOIN cursos ON idPNomeCurso=idFNomeCurso", "*", "idHorEscola=:idHorEscola AND idHorAno=:idHorAno AND idDivEntidade=:idDivEntidade AND idDivEscola=idHorEscola AND divisaoprofessores.classe=horario.classe AND nomeTurmaDiv=turma AND idDivAno=idHorAno AND (idHorCurso=idDivCurso OR idDivCurso IS NULL) AND idListaAno=idDivAno AND listaturmas.classe=horario.classe AND nomeTurma=nomeTurmaDiv AND (idListaCurso=idDivCurso OR idListaCurso IS NULL) AND (idCursoEscola=idHorEscola OR classe<=9) AND (horario.semestre=divisaoprofessores.semestre) AND (semestreActivo=semestre OR classe<=9) AND periodoT=:periodoT", [$professor->idPEscola, $this->idPAno, $professor->idPEntidade, $this->periodoProfessor], "idPListaTurma ASC LIMIT 1"))>0 || $this->periodoProfessor=="todos"){
                    
                    
                        $i++;
                        $cargoSuperior="";
                        if($professor->nivelAcademicoEntidade=="Bacharel" || $professor->nivelAcademicoEntidade=="Licenciado"){
                            $cargoSuperior = $professor->cursoLicenciatura;
                        }else if($professor->nivelAcademicoEntidade=="Mestre"){
                            $cargoSuperior = $professor->cursoMestrado;
                        }else if($professor->nivelAcademicoEntidade=="Doutor"){
                            $cargoSuperior = $professor->cursoDoutoramento;
                        }
    
                        $tCargo = $this->retornarTempoLectivoProfessor($professor->idPEntidade, $professor->idPEscola); 
                        
                        $todasDisciplinas = $this->selectArray("divisaoprofessores LEFT JOIN nomedisciplinas ON idDivDisciplina=idPNomeDisciplina", "DISTINCT abreviacaoDisciplina2", "idDivEscola=:idDivEscola AND idDivEntidade=:idDivEntidade AND idDivAno=:idDivAno", [$professor->idPEscola, $professor->idPEntidade, $this->idPAno]);
    
                        $disciplinasQueLecciona="";
                        $nDisc=0;
                        foreach ($todasDisciplinas as $d) {
                            $nDisc++;
                            if($disciplinasQueLecciona==""){
                                $disciplinasQueLecciona .=$d->abreviacaoDisciplina2;
                            }else{
                                $disciplinasQueLecciona .=", ".$d->abreviacaoDisciplina2;
                            }                              
                        }
    
                        $totCargoPedagogico = $professor->cargoPedagogicoEnt;
    
                        if($i%2==0){
                           $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                        }else{
                            $this->html .="<tr>";
                        }
    
                        $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$professor->numeroAgenteEntidade."</td><td style='".$this->border()."'>".$professor->nomeEntidade."</td><td style='".$this->border()."'>".$professor->nomeEscola."</td><td style='".$this->border()."'>".$professor->cursoEnsinoMedio."</td><td style='".$this->border()."'>".$cargoSuperior."</td><td style='".$this->border()."'>".$disciplinasQueLecciona."</td><td style='".$this->text_center.$this->border()."'>".completarNumero($tCargo)."</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totCargoPedagogico)."</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totCargoPedagogico+$tCargo)."</td></tr>";
                    }
                        
                }
                
                $this->html .="</table>
                <p style='font-size:16pt;".$this->bolder.$this->text_center.$this->maiuscula."'>".$this->rodape()."</p><div style='".$this->maiuscula."'>".$this->assinaturaDirigentes("CDARH")."
                </div>";

            $this->exibir("", "Mapa de Levantamento da Força de Trabalho-".$this->numAno, "", $this->tamanhoFolha, "landscape");
        }

        private function retornarTempoLectivoProfessor($idPEntidade, $idPEscola){
            $this->horarioProfesor = $this->selectArray("horario LEFT JOIN divisaoprofessores ON idDivDisciplina=idHorDisc LEFT JOIN nomedisciplinas ON idDivDisciplina=idPNomeDisciplina LEFT JOIN nomecursos ON idHorCurso=idPNomeCurso LEFT JOIN listaturmas ON idListaEscola=idDivEscola LEFT JOIN cursos ON idFNomeCurso=idDivCurso", "*", "idHorEscola=:idHorEscola AND idHorAno=:idHorAno AND idDivEntidade=:idDivEntidade AND idDivEscola=idHorEscola AND divisaoprofessores.classe=horario.classe AND nomeTurmaDiv=turma AND idDivAno=idHorAno AND (idHorCurso=idDivCurso OR idDivCurso IS NULL) AND idListaAno=idDivAno AND listaturmas.classe=horario.classe AND nomeTurma=nomeTurmaDiv AND (idListaCurso=idDivCurso OR idListaCurso IS NULL)  AND (idCursoEscola=idHorEscola OR classe<=9) AND (horario.semestre=divisaoprofessores.semestre) AND (semestreActivo=semestre OR classe<=9)", [$idPEscola, $this->idPAno, $idPEntidade]);
            
            
            $turnosProfessor=array();
           foreach ($this->horarioProfesor as $turno) {
                if(!seTemValorNoArray($turnosProfessor, $turno->periodoT)){
                   $turnosProfessor[] = $turno->periodoT;
                }
           }
           
           
           $temposPorTurnoProfessor=array();
           foreach ($turnosProfessor as $turno) {
               foreach ($this->horarioProfesor as $tempo) {
                   
                   $tem="nao";
                   foreach($temposPorTurnoProfessor as $t){
                       if($t["tempo"]==$tempo->tempo && $t["turno"]==$turno){
                           $tem="sim";
                           break;
                       }
                   }
                   if($tem=="nao"){
                       $temposPorTurnoProfessor[] = array('tempo'=>$tempo->tempo, "turno"=>$turno);
                   }
               }
           }
           
           
           $diasProfessor = array();
           foreach ($this->horarioProfesor as $tempo) {
                if(!seTemValorNoArray($diasProfessor, $tempo->dia)){
                   $diasProfessor[] = $tempo->dia;
                }
                   
            }
            
            
            $contadorTemposProfessor=0;
            foreach ($turnosProfessor as $turno) {
                foreach ($temposPorTurnoProfessor as $tempo) {
                   if($tempo["turno"]==$turno){
                        
                        foreach ($diasProfessor as $dia) {
                            $numSala = $this->retornarCampo($tempo["tempo"], $tempo["turno"], $dia, "numeroSalaTurma", $idPEntidade);
                           
                            if($numSala=="" || $numSala==NULL){
                                $numSala="";
                           }else{
                            $numSala = completarNumero($numSala);
                            $contadorTemposProfessor++;
                           }
                        }
                   }
                }
            }
            return $contadorTemposProfessor;
           
           
        }
        
        function retornarCampo($tempo, $turno, $dia, $campoPesquisado="", $idPEntidade){
            $retorno="";
            
            foreach ($this->horarioProfesor as $horario) {
                if($horario->tempo==$tempo && $horario->periodoT==$turno && $horario->dia==$dia){
                    $retorno = $horario->$campoPesquisado;
                    break;
                }
            }
            return $retorno;
        }
    }

    new mapaForcaTrabalho(__DIR__);
?>
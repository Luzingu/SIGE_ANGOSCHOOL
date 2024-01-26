<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
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

            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
            $this->html .="
            <div style='page-break-after: always;'>".$this->cabecalho()."
            <p style='".$this->text_center.$this->bolder.$this->maiuscula."'>MAPA DE LEVANTAMENTO DA FORÇA DE TRABALO";
            if($this->periodoProfessor!="todos" && $this->periodoProfessor!=""){
                $this->html .=" DOS PROFESSORES DO PERÍODO ". $this->periodoProfessor;
            }
            
            $this->html .=" - ".$this->numAno."</p>
            
            <br/><table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>N.º</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Agente</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Nome Completo</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Função</td><td rowspan='2'style='".$this->bolder.$this->text_center.$this->border()."'>Categoria</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Hab.<br/>Literárias</td><td colspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Especialidade</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Disciplinas(S) <br/>a Leccionar</td><td colspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Tempos Lectivos</td><td rowspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>Total</td>
                </tr>
                <tr style='".$this->corDanger."'> 
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>Médio</td><td style='".$this->bolder.$this->text_center.$this->border()."'>Superior</td><td style='".$this->bolder.$this->text_center.$this->border()."'>Professor</td><td style='".$this->bolder.$this->text_center.$this->border()."'>Cargo<br/>Pedagógico</td>
                </tr>";

                $i=0;
                foreach ($this->entidades(array(), "docente", "V") as $professor) {

                    if($this->periodoProfessor=="todos"){
                        $horarioProfesor = $this->selectArray("horario", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idHorAno"=>$this->idAnoActual, "idPEntidade"=>$professor["idPEntidade"]]);
                    }else{
                        $horarioProfesor = $this->selectArray("horario", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idHorAno"=>$this->idAnoActual, "idPEntidade"=>$professor["idPEntidade"], "periodoT"=>$this->periodoProfessor]);
                    }


                    if(count($horarioProfesor)>0){
                    
                    
                        $i++;
                        $cargoSuperior="";
                        if($professor->nivelAcademicoEntidade=="Bacharel" || $professor->nivelAcademicoEntidade=="Licenciado"){
                            $cargoSuperior = $professor->cursoLicenciatura;
                        }else if($professor->nivelAcademicoEntidade=="Mestre"){
                            $cargoSuperior = $professor->cursoMestrado;
                        }else if($professor->nivelAcademicoEntidade=="Doutor"){
                            $cargoSuperior = $professor->cursoDoutoramento;
                        }
    
                        $disciplinasQueLecciona="";
                        $nDisc=0;
                        foreach (distinct2($horarioProfesor, "abreviacaoDisciplina1") as $d) {
                            $nDisc++;
                            if($disciplinasQueLecciona!=""){
                                $disciplinasQueLecciona .=", ";
                            }
                            $disciplinasQueLecciona .=$d;                              
                        }
    
                        $totCargoPedagogico = $professor["escola"]["cargoPedagogicoEnt"];
    
                        if($i%2==0){
                           $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                        }else{
                            $this->html .="<tr>";
                        }
    
                        $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$professor["numeroAgenteEntidade"]."</td><td style='".$this->border()."'>".$professor["nomeEntidade"]."</td><td style='".$this->border()."'>".$professor["escola"]["funcaoEnt"]."</td><td style='".$this->border().$this->text_center."'>".$professor["categoriaEntidade"]."</td><td style='".$this->text_center.$this->border()."'>".$professor["nivelAcademicoEntidade"]."</td><td style='".$this->border()."'>".$professor["cursoEnsinoMedio"]."</td><td style='".$this->border()."'>".$cargoSuperior."</td><td style='".$this->border()."'>".$disciplinasQueLecciona."</td><td style='".$this->text_center.$this->border()."'>".completarNumero(count($horarioProfesor))."</td><td style='".$this->text_center.$this->border()."'>".completarNumero($totCargoPedagogico)."</td><td style='".$this->text_center.$this->border()."'>".completarNumero(intval($totCargoPedagogico)+count($horarioProfesor))."</td></tr>";
                    }
                        
                }
                
                $this->html .="</table>
                <p style='font-size:16pt;".$this->bolder.$this->text_center.$this->maiuscula."'>".$this->rodape()."</p><div style='".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div>
                </div>";

            $this->exibir("", "Mapa de Levantamento da Força de Trabalho-".$this->numAno, "", $this->tamanhoFolha, "landscape");
        }
    }

    new mapaForcaTrabalho(__DIR__);
?>
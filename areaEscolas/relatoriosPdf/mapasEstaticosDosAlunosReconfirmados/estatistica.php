<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class mapa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa de Frequência");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->nomeCurso();
            $this->numAno();

            
            $this->conDb();

            $arrayCursos =array();
            foreach($this->arraySobre("nomecursos", ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>$this->tipoCurso], ['sort'=>["nomeCurso"=>1]], "cursos", ["idCursoEscola=".$_SESSION['idEscolaLogada'], "estadoCurso=A"]) as $curso){
              $arrayCursos[]=$curso["idPNomeCurso"];
            }

            $this->arrayAlunosReconfirmados = $this->selectArray("alunosmatriculados", ["reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatCurso"=>array('$in'=>$arrayCursos)], ['sort'=>array("nomeAluno"=>1)]);
            
            $this->alunosReconfirmados = zipador($this->arrayAlunosReconfirmados, [["reconfirmacoes", ["idReconfAno=".$this->idPAno, "idReconfEscola=".$_SESSION['idEscolaLogada']]], ["turmas", ["idTurmaAno=".$this->idPAno, "idTurmaEscola=".$_SESSION['idEscolaLogada']]], ["escola", ["idMatEscola=".$_SESSION['idEscolaLogada']]]]);

            if($this->verificacaoAcesso->verificarAcesso("", ["zonaPesquisa"], [], "")){                
              $this->mapaReg();
            }else{
              $this->negarAcesso();
            }
            
        }

        private function mapaReg(){
          $this->periodo="reg";

          $this->html .="<html style='margin:20px;'>
                <head>
                    <title>Mapa de Frequências</title>
                </head>
                <body>".$this->fundoDocumento("horizontal")."
          <div style='page-break-after: always;'>
                <div style='position: absolute;'>
                  <div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>
            <p style='".$this->text_center.$this->miniParagrafo."'>".$this->cabecalho()."<p style='".$this->text_center.$this->sublinhado.$this->maiuscula.$this->bolder."'>MATRÍCULA - ".$this->numAno."</p>";



          $this->html .="<table style='".$this->tabela." width:100%; font-size:12pt;'>";
          

          $this->html .="<tr style='".$this->corDanger.$this->text_center.$this->bolder."'><td rowspan='3' style='".$this->border().$this->text_center."'>CURSO</td><td rowspan='3' style='".$this->border().$this->text_center."'>N.º de Vagas</td><td rowspan='3' style='".$this->border().$this->text_center."'>Alunos Inscritos</td><td colspan='6' style='".$this->border()."'>10.ª Classe</td><td rowspan='2' colspan='2' style='".$this->border()."'>11.ª Classe</td><td rowspan='2' colspan='2' style='".$this->border()."'>12.ª Classe</td><td rowspan='2' colspan='2' style='".$this->border()."'>13.ª Classe</td></tr>
          <tr style='".$this->corDanger.$this->text_center .$this->bolder."'><td style='".$this->border()."' colspan='2'>Nova Matrícula</td><td style='".$this->border()."' colspan ='2'>&nbsp;Repetentes&nbsp;</td><td style='".$this->border()."' colspan='2'>Total</td></tr>
          <tr style='".$this->corDanger.$this->text_center .$this->bolder."'><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td></tr>";

          $contadorVagas =0;
          $contadorInscritos=0;
          foreach ($this->arraySobre("nomecursos", ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>$this->tipoCurso], ['sort'=>["nomeCurso"=>1]], "cursos", ["idCursoEscola=".$_SESSION['idEscolaLogada'], "estadoCurso=A"]) as $curso) {

            
            $this->html .="<tr><td style='".$this->border().$this->maiuscula."'>".$curso["nomeCurso"];

            if($curso["areaFormacaoCurso"]!="Geral" && $curso["areaFormacaoCurso"]!=NULL && $curso["areaFormacaoCurso"]!="TOT"){
              $this->html .=" (".$curso["areaFormacaoCurso"].")";
            }
            $this->conDb("inscricao");
            $vagas = $this->selectArray("gestorvagas", ["idGestAno"=>$this->idAnoActual, "idGestEscola"=>$_SESSION['idEscolaLogada'], "idGestCurso"=>$curso["idPNomeCurso"]]);

            $totVagas = (int)valorArray($vagas, "vagasReg")+(int)valorArray($vagas, "vagasPos"); 

            $totInscritos = count($this->arraySobre("alunos", ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], [], "inscricao", ["idInscricaoCurso=".$curso["idPNomeCurso"]]));

            $contadorVagas +=$totVagas;
            $contadorInscritos +=$totInscritos;
            $this->conDb();

            $this->html .="<td style='".$this->border().$this->text_center."'>".completarNumero($totVagas)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totInscritos)."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 10, "TOT", "nova"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 10, "F", "nova"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 10, "TOT", "rep"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 10, "F", "rep"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 10, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 10, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 11, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 11, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 12, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 12, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 13, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso["idPNomeCurso"], 13, "F"))."</td></tr>";
          }
          $this->html .="<tr style='background-color:rgba(0,0,0,0.3)'><td style='".$this->border().$this->text_center.$this->bolder.$this->maiuscula."'>TOTAL</td>

          <td style='".$this->border().$this->text_center."'>".completarNumero($contadorVagas)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($contadorInscritos)."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 10, "TOT", "nova"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 10, "F", "nova"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 10, "TOT", "rep"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 10, "F", "rep"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 10, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 10, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 11, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 11, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 12, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 12, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 13, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos("TOT", 13, "F"))."</td></tr></table>
            <br/><div class='maiuscula'>".$this->assinaturaDirigentes("mengi")."</div>
          </div>";

          $this->exibir("", "Mapa_Frequencias-".$this->idPAno, "", "A4", "landscape");
        }




        private function contadorAlunos($idCurso, $classe, $genero, $tipo=""){
          $contador=0;
          foreach ($this->alunosReconfirmados as $alunos) {
                if(seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, $alunos["classeReconfirmacao"]) && seComparador($idCurso, $alunos->idMatCurso)){

                  if($tipo==""){
                    $contador++;
                  }else{

                    $objectAluno=array();
                    foreach ($this->arrayAlunosReconfirmados as $piter){
                      if($piter["idPMatricula"]==$alunos["idPMatricula"]){
                        $objectAluno = $piter;
                        break;
                      }
                    }
                    $dg = listarItensObjecto($objectAluno, "reconfirmacoes", ["idReconfAno!=".$this->idPAno, "classeReconfirmacao=".$classe, "idReconfEscola=".$_SESSION['idEscolaLogada']]);

                    if($tipo=="nova" && count($dg)<=0){
                      $contador++;
                    }else if($tipo=="rep" && count($dg)>0){
                      $contador++;
                    }
                  }
                }

          }
          return completarNumero($contador);
        }
       
    }

new mapa(__DIR__);
    
    
  
?>
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
 
    class mapa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
            parent::__construct("Rel-Mapa de Frequência");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->nomeCurso();
            $this->numAno();

            
            $this->conDb();

            $this->alunosReconfirmados = $this->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso", "*", "idReconfEscola=:idReconfEscola AND idReconfEscola=idTurmaEscola AND idMatEscola=idReconfEscola AND nomeTurma IS NOT NULL AND estadoAluno in ('A', 'Y') AND idTurmaAno=idReconfAno AND idTurmaAno=:idTurmaAno AND classeTurma=classeReconfirmacao AND tipoCurso=:tipoCurso AND (tipoEntrada=:tipoEntrada OR tipoEntrada IS NULL)", [$_SESSION["idEscolaLogada"], $this->idPAno, $this->tipoCurso, "novaMatricula"]);

           $this->turmas = array();

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aPedagogica", "aDirectoria", "aAdministrativa"], "", "")){
                
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
                  <div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes("Director")."</div></div>
            <p style='".$this->text_center.$this->miniParagrafo."'>".$this->cabecalho()."<p style='".$this->text_center.$this->sublinhado.$this->maiuscula.$this->bolder."'>MATRÍCULA - ".$this->numAno."</p>";



          $this->html .="<table style='".$this->tabela." width:100%; font-size:12pt;'>";
          

          $this->html .="<tr style='".$this->corDanger.$this->text_center.$this->bolder."'><td rowspan='3' style='".$this->border().$this->text_center."'>CURSO</td><td rowspan='3' style='".$this->border().$this->text_center."'>N.º de Vagas</td><td rowspan='3' style='".$this->border().$this->text_center."'>Alunos Inscritos</td><td colspan='6' style='".$this->border()."'>10.ª Classe</td><td rowspan='2' colspan='2' style='".$this->border()."'>11.ª Classe</td><td rowspan='2' colspan='2' style='".$this->border()."'>12.ª Classe</td><td rowspan='2' colspan='2' style='".$this->border()."'>13.ª Classe</td></tr>
          <tr style='".$this->corDanger.$this->text_center .$this->bolder."'><td style='".$this->border()."' colspan='2'>Nova Matrícula</td><td style='".$this->border()."' colspan ='2'>&nbsp;Repetentes&nbsp;</td><td style='".$this->border()."' colspan='2'>Total</td></tr>
          <tr style='".$this->corDanger.$this->text_center .$this->bolder."'><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td><td style='".$this->border()."'>MF</td><td style='".$this->border()."'>F</td></tr>";

          $contadorVagas =0;
          $contadorInscritos=0;
          foreach ($this->selectArray("nomecursos LEFT JOIN cursos ON idPNomeCurso=idFNomeCurso", "*", "idCUrsoEscola=:idCUrsoEscola AND estadoCurso=:estadoCurso AND tipoCurso=:tipoCurso", [$_SESSION["idEscolaLogada"], "A", $this->tipoCurso]) as $curso) {

            
            $this->html .="<tr><td style='".$this->border().$this->maiuscula."'>".$curso->nomeCurso;

            if($curso->areaFormacaoCurso!="Geral" && $curso->areaFormacaoCurso!=NULL && $curso->areaFormacaoCurso!="TOT"){
              $this->html .=" (".$curso->areaFormacaoCurso.")";
            }
            $this->conDb("inscricao");
            $vagas = $this->selectArray("gestorvagas", "*", "idGestAno=:idGestAno AND idGestEscola=:idGestEscola AND idGestCurso=:idGestCurso", [$this->idAnoActual, $_SESSION['idEscolaLogada'], $curso->idPNomeCurso]);

            $totVagas = (int)valorArray($vagas, "vagasReg")+(int)valorArray($vagas, "vagasPos"); 

            $totInscritos = count($this->selectArray("alunos_inscritos LEFT JOIN alunos ON idPAluno=idFAluno", "*", "idInscricaoCurso=:idInscricaoCurso AND idInscricaoAno=:idInscricaoAno AND idInscricaoEscola=:idInscricaoEscola", [$curso->idPNomeCurso, $this->idAnoActual, $_SESSION['idEscolaLogada']]));
            $contadorVagas +=$totVagas;
            $contadorInscritos +=$totInscritos;
            $this->conDb();

            $this->html .="<td style='".$this->border().$this->text_center."'>".completarNumero($totVagas)."</td><td style='".$this->border().$this->text_center."'>".completarNumero($totInscritos)."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 10, "TOT", "nova"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 10, "F", "nova"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 10, "TOT", "rep"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 10, "F", "rep"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 10, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 10, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 11, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 11, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 12, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 12, "F"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 13, "TOT"))."</td>
            <td style='".$this->border().$this->text_center."'>".completarNumero($this->contadorAlunos($curso->idPNomeCurso, 13, "F"))."</td></tr>";
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
            <br/><div class='maiuscula'>".$this->assinaturaDirigentes(["Pedagógico", "Administrativo", "Chefe_da_Secretaria"])."</div>
          </div>";

          $this->exibir("", "Mapa_Frequencias-".$this->idPAno, "", "A4", "landscape");
        }




        private function contadorAlunos($idCurso, $classe, $genero, $tipo=""){
          $contador=0;
          foreach ($this->alunosReconfirmados as $alunos) {
                if(seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && seComparador($idCurso, $alunos->idMatCurso)){

                  if($tipo==""){
                    $contador++;
                  }else{
                    $dg = $this->selectArray("alunosreconfirmados", "*", "idReconfEscola=:idReconfEscola AND idReconfAno!=".$this->idPAno." AND classeReconfirmacao=:classeReconfirmacao AND idReconfMatricula=:idReconfMatricula", [$_SESSION['idEscolaLogada'], $classe, $alunos->idPMatricula]);
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
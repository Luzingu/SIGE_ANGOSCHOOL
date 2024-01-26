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
             $this->numAno();
             $this->nomeCurso();

            $arrayCursos =array();
            foreach($this->selectArray("nomecursos", ["idPNomeCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>$this->tipoCurso, "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
              $arrayCursos[]=$curso["idPNomeCurso"];
            }

            $this->alunosInscritos = $this->selectArray("alunosmatriculados", ["dataNascAluno", "sexoAluno", "reconfirmacoes.nomeTurma", "reconfirmacoes.classeReconfirmacao", "escola.idMatCurso", "escola.periodoAluno"], ["reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idMatCurso"=>array('$in'=>$arrayCursos), "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.estadoReconfirmacao"=>"A"], ["escola", "reconfirmacoes"], "", [], [], $this->matchMaeAlunos($this->idPAno));

            $this->turmas = $this->turmasEscola(array(), array(), $this->idPAno, $this->tipoCurso);

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
                <body>".$this->fundoDocumento("../../../")."
          <div style='page-break-after: always;'>
                <div style='position: absolute;'>
                  <div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>
            <p style='".$this->text_center.$this->miniParagrafo."'>".$this->cabecalho()."<p style='".$this->text_center.$this->sublinhado.$this->maiuscula.$this->bolder."'>MAPA DE FREQUÊNCIAS - ".$this->numAno."</p>";

          $periodosEscolas=$this->selectUmElemento("escolas", "periodosEscolas", ["idPEscola"=>$_SESSION["idEscolaLogada"]]);

          $cabecalhos=array();
          foreach (listarItensObjecto($this->sobreCurso, "classes") as $classe) {
              $cabecalhos[] = array('titulo'=>"TOT", "tituloDb"=>"numTurmas", "classe"=>$classe["identificador"]);
              $cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"TOT", "classe"=>$classe["identificador"]);
              $cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"F", "classe"=>$classe["identificador"]);
          }
          $cabecalhos[] = array('titulo'=>"TOT", "tituloDb"=>"numTurmas", "classe"=>"TOT");
          $cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"TOT", "classe"=>"TOT");
          $cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"F", "classe"=>"TOT");



          $this->html .="<table style='".$this->tabela." width:100%; font-size:9pt;'>";

          if($periodosEscolas=="regPos"){
              $this->html .="<tr><td colspan='".(count(listarItensObjecto($this->sobreCurso, "classes"))*3+4)."' style='".$this->text_center.$this->border()."'>REGIME: <span style='".$this->vermelha.$this->bolder."'>REGULAR</span></td></tr>";
          }
          

          $this->html .="<tr style='".$this->corDanger."'><td rowspan='3' style='".$this->border().$this->bolder.$this->text_center."'>CURSO</td>";
          foreach (listarItensObjecto($this->sobreCurso, "classes") as $classe) {
            $this->html .="<td colspan='3' style='".$this->border().$this->bolder.$this->maiuscula.$this->text_center."'>".$classe["designacao"]."</td>";
          }
          $this->html .="<td colspan='3' style='".$this->border().$this->bolder.$this->text_center."'>TOTAL</td>";
          $this->html .="</tr><tr style='".$this->corDanger."'>";

          foreach (listarItensObjecto($this->sobreCurso, "classes") as $classe) {
              $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'>Nº<br/>Turmas</td><td style='".$this->border().$this->bolder.$this->text_center."' colspan='2'>Nº de Alunos</td>";
          }
          $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'>Nº<br/>Turmas</td><td style='".$this->border().$this->bolder.$this->text_center."' colspan='2'>Nº de Alunos</td></tr>
          <tr style='".$this->corDanger."'>";

          foreach ($cabecalhos as $cab) {
              if($cab["titulo"]!="TOT"){
                $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".$cab["titulo"]."</td>";
              }
          }
          $this->html .="</tr>";

          foreach ($this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A", "tipoCurso"=>$this->tipoCurso], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso) {
  
            
            $this->html .="<tr><td style='".$this->border().$this->maiuscula."'>".$curso["nomeCurso"];

            if($curso["areaFormacaoCurso"]!="Geral" && $curso["areaFormacaoCurso"]!=NULL && $curso["areaFormacaoCurso"]!="TOT"){
              $this->html .=" (".$curso["areaFormacaoCurso"].")";
            }
            $this->html .="</td>";

            foreach ($cabecalhos as $cab) {
              if($cab["tituloDb"]=="numTurmas"){

                  $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorTurma($curso["idPNomeCurso"], $cab["classe"])."</td>";
              }else{
                $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorAlunos($curso["idPNomeCurso"], $cab["classe"], $cab["tituloDb"])."</td>";
              }
            }
              $this->html .="</tr>";
          }

          $this->html .="<tr style='background-color:rgba(0,0,0,0.3)'><td style='".$this->border().$this->text_center.$this->bolder.$this->maiuscula."'>TOTAL</td>";
          foreach ($cabecalhos as $cab) {
              if($cab["tituloDb"]=="numTurmas"){

                  $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorTurma("TOT", $cab["classe"])."</td>";
              }else{
                $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorAlunos("TOT", $cab["classe"], $cab["tituloDb"])."</td>";
              }
          }

          $this->html.="</tr>";
          $this->html .="</table><br/>

          <table style='".$this->tabela."width:50%; font-size:10pt;'>
            <tr><td style='".$this->border()." width:350px;'>NÚMERO DE TURMAS LECTIVAS</td><td style='".$this->border().$this->text_center." width:80px;'>".$this->contadorTurma("TOT", "TOT")."</td><td style='".$this->border().$this->text_center."'></td></tr>
            <tr><td style='".$this->border()."'>TOTAL DOS ALUNOS DO SEXO MASCULINO</td><td style='".$this->border().$this->text_center."'>".$this->contadorAlunos("TOT", "TOT", "M")."</td><td style='".$this->border().$this->text_center."'>".$this->percentagem($this->contadorAlunos("TOT", "TOT", "TOT"), $this->contadorAlunos("TOT", "TOT", "M"))."</td></tr>

            <tr><td style='".$this->border()."'>TOTAL DOS ALUNOS DO SEXO FEMININO</td><td style='".$this->border().$this->text_center."'>".$this->contadorAlunos("TOT", "TOT", "F")."</td><td style='".$this->border().$this->text_center."'>".$this->percentagem($this->contadorAlunos("TOT", "TOT", "TOT"), $this->contadorAlunos("TOT", "TOT", "F"))."</td></tr>
            <tr><td style='".$this->border()."'>TOTAL GERAL</td><td style='".$this->border().$this->text_center."'>".$this->contadorAlunos("TOT", "TOT", "TOT")."</td><td style='".$this->border().$this->text_center."'>100%</td></tr>
          </table><br/><div class='maiuscula'>".$this->assinaturaDirigentes("mengi")."</div>
          </div>";

          if($periodosEscolas=="regPos"){
            $this->mapaPos();
          }
          $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Frequências-".$this->numAno, "Mapa_Frequencias-".$this->idPAno);
        }


        private function mapaPos(){
          $this->periodo="pos";

          $this->html .="
          <div>".$this->fundoDocumento("../../../")."
                <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7, 25)."</div></div>".$this->cabecalho()."<p style='".$this->text_center.$this->sublinhado.$this->maiuscula.$this->bolder."'>MAPA DE FREQUÊNCIAS - ".$this->numAno."</p>";

          $periodosEscolas=$this->selectUmElemento("escolas", "periodosEscolas", ["idPEscola"=>$_SESSION["idEscolaLogada"]]);
          $cabecalhos=array();
          foreach (listarItensObjecto($this->sobreCurso, "classes") as $classe) {
              $cabecalhos[] = array('titulo'=>"TOT", "tituloDb"=>"numTurmas", "classe"=>$classe["identificador"]);
              $cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"TOT", "classe"=>$classe["identificador"]);
              $cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"F", "classe"=>$classe["identificador"]);
          }
          $cabecalhos[] = array('titulo'=>"TOT", "tituloDb"=>"numTurmas", "classe"=>"TOT");
          $cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"TOT", "classe"=>"TOT");
          $cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"F", "classe"=>"TOT");



          $this->html .="<table style='".$this->tabela." width:100%; font-size:9pt;'>";

         
          $this->html .="<tr><td colspan='".(count(listarItensObjecto($this->sobreCurso, "classes"))*3+4)."' style='".$this->text_center.$this->border()."'>REGIME: <span style='".$this->vermelha.$this->bolder."'>PÓS-LABORAL</span></td></tr>

          <tr style='".$this->corDanger."'><td rowspan='3' style='".$this->border().$this->bolder.$this->text_center."'>CURSO</td>";

          foreach (listarItensObjecto($this->sobreCurso, "classes") as $classe) {
            $this->html .="<td colspan='3' style='".$this->border().$this->bolder.$this->maiuscula.$this->text_center."'>".$classe["designacao"]."</td>";
          }
          $this->html .="<td colspan='3' style='".$this->border().$this->bolder.$this->text_center."'>TOTAL</td>";
          $this->html .="</tr><tr style='".$this->corDanger."'>";

          foreach (listarItensObjecto($this->sobreCurso, "classes") as $classe) {
              $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'>Nº<br/>Turmas</td><td style='".$this->border().$this->bolder.$this->text_center."' colspan='2'>Nº de Alunos</td>";
          }
          $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'>Nº<br/>Turmas</td><td style='".$this->border().$this->bolder.$this->text_center."' colspan='2'>Nº de Alunos</td></tr>
          <tr style='".$this->corDanger."'>";

          foreach ($cabecalhos as $cab) {
              if($cab["titulo"]!="TOT"){
                $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".$cab["titulo"]."</td>";
              }
          }
          $this->html .="</tr>";

          foreach ($this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A", "tipoCurso"=>$this->tipoCurso], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){

              $this->html .="<tr><td style='".$this->border().$this->maiuscula."'>".$curso["nomeCurso"];

              if($curso["areaFormacaoCurso"]!="Geral" && $curso["areaFormacaoCurso"]!=NULL && $curso["areaFormacaoCurso"]!="TOT"){
                $this->html .=" (".$curso["areaFormacaoCurso"].")";
              }
              $this->html .="</td>";

            foreach ($cabecalhos as $cab) {
              if($cab["tituloDb"]=="numTurmas"){

                  $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorTurma($curso["idPNomeCurso"], $cab["classe"])."</td>";
              }else{
                $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorAlunos($curso["idPNomeCurso"], $cab["classe"], $cab["tituloDb"])."</td>";
              }
            }
              $this->html .="</tr>";
          }

          $this->html .="<tr style='background-color:rgba(0,0,0,0.3)'><td style='".$this->border().$this->text_center.$this->bolder.$this->maiuscula."'>TOTAL</td>";
          foreach ($cabecalhos as $cab) {
              if($cab["tituloDb"]=="numTurmas"){

                  $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorTurma("TOT", $cab["classe"])."</td>";
              }else{
                $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorAlunos("TOT", $cab["classe"], $cab["tituloDb"])."</td>";
              }
          }

          $this->html.="</tr>";
          $this->html .="</table><br/>

          <table style='".$this->tabela."width:50%; font-size:10pt;'>
            <tr><td style='".$this->border()." width:350px;'>NÚMERO DE TURMAS LECTIVAS</td><td style='".$this->border().$this->text_center." width:80px;'>".$this->contadorTurma("TOT", "TOT")."</td><td style='".$this->border().$this->text_center."'></td></tr>
            <tr><td style='".$this->border()."'>TOTAL DOS ALUNOS DO SEXO MASCULINO</td><td style='".$this->border().$this->text_center."'>".$this->contadorAlunos("TOT", "TOT", "M")."</td><td style='".$this->border().$this->text_center."'>".$this->percentagem($this->contadorAlunos("TOT", "TOT", "TOT"), $this->contadorAlunos("TOT", "TOT", "M"))."</td></tr>

            <tr><td style='".$this->border()."'>TOTAL DOS ALUNOS DO SEXO FEMININO</td><td style='".$this->border().$this->text_center."'>".$this->contadorAlunos("TOT", "TOT", "F")."</td><td style='".$this->border().$this->text_center."'>".$this->percentagem($this->contadorAlunos("TOT", "TOT", "TOT"), $this->contadorAlunos("TOT", "TOT", "F"))."</td></tr>
            <tr><td style='".$this->border()."'>TOTAL GERAL</td><td style='".$this->border().$this->text_center."'>".$this->contadorAlunos("TOT", "TOT", "TOT")."</td><td style='".$this->border().$this->text_center."'>100%</td></tr>
          </table><br/><div class='maiuscula'>".$this->assinaturaDirigentes("mengi")."</div>


          </div></body></html>";
        }

        private function contadorAlunos($idCurso, $classe, $genero){
          $contador=0;
          foreach ($this->alunosInscritos as $alunos) {
            if(seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, $alunos["reconfirmacoes"]["classeReconfirmacao"]) && seComparador($idCurso, nelson($alunos, "idMatCurso", "reconfirmacoes")) && nelson($alunos, "periodoAluno", "escola")==$this->periodo){ 
                $contador++;
            }
          }
          return completarNumero($contador);
        }

        private function contadorTurma($idCurso, $classe){
          $contador=0;
          foreach ($this->turmas as $turma) {
              if(seComparador($classe, nelson($turma, "classe")) && seComparador($idCurso, nelson($turma, "idPNomeCurso")) && nelson($turma, "periodoTurma")==$this->periodo){
                  $contador++;
              }
          }
          return completarNumero($contador);
        }

        public function percentagem($valor1, $valor2){
            if($valor1==0){
                $perc = "0";
            }else{
                $perc=0;
                $perc = ($valor2/$valor1)*100;
            }
            if($perc==100){
                return "100%";
            }else{
                return number_format($perc, 2)."%";
            }
        }
       
    }

new mapa(__DIR__);
    
    
  
?>
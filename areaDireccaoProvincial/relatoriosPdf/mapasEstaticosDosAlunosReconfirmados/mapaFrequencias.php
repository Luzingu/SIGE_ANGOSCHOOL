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
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:"";
            $this->privacidade = isset($_GET["privacidade"])?$_GET["privacidade"]:"";
            $this->labelPrivacidade="Público";
            if($this->privacidade!="Privada" && $this->privacidade!="Pública"){
                $this->privacidade="Pública";
            }
            if($this->privacidade=="Privada"){
                $this->labelPrivacidade="Privado";
            }

            $this->numAno();
            $this->nomeCurso();

            $this->alunosInscritos=array();
            $this->turmas=array();


            foreach($this->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPEscola", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), $this->privacidade, "A"], "nomeEscola ASC") as $a){

                $array =$this->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso", "*", "idReconfEscola=:idReconfEscola AND idReconfEscola=idTurmaEscola AND idMatEscola=idReconfEscola AND nomeTurma IS NOT NULL AND estadoAluno in ('A', 'Y') AND idTurmaAno=idReconfAno AND idTurmaAno=:idTurmaAno AND classeTurma=classeReconfirmacao AND tipoCurso=:tipoCurso", [$a->idPEscola, $this->idPAno, $this->tipoCurso]);
                
              $this->alunosInscritos = array_merge($this->alunosInscritos, $array);
              $this->turmas = array_merge($this->turmas, $this->selectArray("listaturmas LEFT JOIN nomecursos ON idPNomeCurso=idListaCurso", "*", "idListaEscola=:idListaEscola AND idListaAno=:idListaAno AND nomeTurma IS NOT NULL AND tipoCurso=:tipoCurso", [$a->idPEscola  , $this->idPAno, $this->tipoCurso], "nomeTurma ASC"));  
            }

            $this->listaCursos=array();
            foreach($this->selectArray("nomecursos LEFT JOIN cursos ON idPNomeCurso=idFNomeCurso LEFT JOIN escolas ON idPEscola=idCursoEscola LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPNomeCurso", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola AND estadoCurso=:estadoCurso AND tipoCurso=:tipoCurso", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), $this->privacidade, "A", "A", $this->tipoCurso]) as $a){
              $this->listaCursos = array_merge($this->listaCursos, $this->selectArray("nomecursos", "*", "idPNomeCurso=:idPNomeCurso", [$a->idPNomeCurso]));
            }

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aRelEstatistica "])){
                
                $this->mapaReg();
            }else{
              $this->negarAcesso();
            }
            
        }

        private function mapaReg(){
          $this->periodo="reg";

          $this->html .="<html style='margin:20px;'>
              <style>
                  table tr td{
                    font-size:10pt !important;
                  }
              </style>
                <body>".$this->fundoDocumento()."
          <div style='page-break-after: always;'>
                <div style='position: absolute;'>
                  <div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>
            <p style='".$this->text_center.$this->miniParagrafo."'>".$this->cabecalho()."<p style='".$this->text_center.$this->sublinhado.$this->maiuscula.$this->bolder."'>MAPA DE FREQUÊNCIAS NO ENSINO ".$this->labelPrivacidade." - ".$this->numAno."</p>";


          $arrayClasses = array();
          for($i=10; $i<=(9+$this->duracaoCurso); $i++){
            $arrayClasses[]=$i;
          }

          $cabecalhos=array();
          foreach ($arrayClasses as $classe) {
              $cabecalhos[] = array('titulo'=>"TOT", "tituloDb"=>"numTurmas", "classe"=>$classe);
              $cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"TOT", "classe"=>$classe);
              $cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"F", "classe"=>$classe);
          }
          $cabecalhos[] = array('titulo'=>"TOT", "tituloDb"=>"numTurmas", "classe"=>"TOT");
          $cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"TOT", "classe"=>"TOT");
          $cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"F", "classe"=>"TOT");



          $this->html .="<table style='".$this->tabela." width:100%; font-size:10pt;'>";

          $this->html .="<tr><td colspan='".(count($arrayClasses)*3+4)."' style='".$this->text_center.$this->border()."'>REGIME: <span style='".$this->vermelha.$this->bolder."'>REGULAR</span></td></tr>";
          
          

          $this->html .="<tr style='".$this->corDanger."'><td rowspan='3' style='".$this->border().$this->bolder.$this->text_center." width:270px;'>CURSO</td>";
          foreach ($arrayClasses as $classe) {
            $this->html .="<td colspan='3' style='".$this->border().$this->bolder.$this->maiuscula.$this->text_center."'>".classeExtensa($classe, $this->sePorSemestre)."</td>";
          }
          $this->html .="<td colspan='3' style='".$this->border().$this->bolder.$this->text_center."'>TOTAL</td>";
          $this->html .="</tr><tr style='".$this->corDanger."'>";

          foreach ($arrayClasses as $classe) {
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

          foreach ($this->listaCursos as $curso) {

            
            $this->html .="<tr><td style='".$this->border().$this->maiuscula."'>".$curso->nomeCurso;

            if($curso->areaFormacaoCurso!="Geral" && $curso->areaFormacaoCurso!=NULL && $curso->areaFormacaoCurso!="TOT"){
              $this->html .=" (".$curso->areaFormacaoCurso.")";
            }
            $this->html .="</td>";

            foreach ($cabecalhos as $cab) {
              if($cab["tituloDb"]=="numTurmas"){

                  $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorTurma($curso->idPNomeCurso, $cab["classe"])."</td>";
              }else{
                $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorAlunos($curso->idPNomeCurso, $cab["classe"], $cab["tituloDb"])."</td>";
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
          </table><br/><div class='maiuscula'>".$this->assinaturaDirigentes(["CDEPE", "Administrativo", "Chefe da Secretaria"])."</div>
          </div>";

         
          $this->mapaPos();
          
          $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Frequências-".$this->numAno, "Mapa_Frequencias-".$this->idPAno);
        }


        private function mapaPos(){
          $this->periodo="pos";

          $this->html .="
          <div>".$this->fundoDocumento()."
                <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho()."<p style='".$this->text_center.$this->sublinhado.$this->maiuscula.$this->bolder."'>MAPA DE FREQUÊNCIAS NO ENSINO ".$this->labelPrivacidade." - ".retornarAnoLectivo($this->numAno)."</p>";

          $periodosEscolas=$this->selectUmElemento("escolas", "periodosEscolas", "idPEscola=:idPEscola", [$_SESSION["idEscolaLogada"]]);

          $arrayClasses = array();
          for($i=10; $i<=(9+$this->duracaoCurso); $i++){
            $arrayClasses[]=$i;
          }

          $cabecalhos=array();
          foreach ($arrayClasses as $classe) {
              $cabecalhos[] = array('titulo'=>"TOT", "tituloDb"=>"numTurmas", "classe"=>$classe);
              $cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"TOT", "classe"=>$classe);
              $cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"F", "classe"=>$classe);
          }
          $cabecalhos[] = array('titulo'=>"TOT", "tituloDb"=>"numTurmas", "classe"=>"TOT");
          $cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"TOT", "classe"=>"TOT");
          $cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"F", "classe"=>"TOT");



          $this->html .="<table style='".$this->tabela." width:100%; font-size:10pt;'>";

         
          $this->html .="<tr><td colspan='".(count($arrayClasses)*3+4)."' style='".$this->text_center.$this->border()."'>REGIME: <span style='".$this->vermelha.$this->bolder."'>PÓS-LABORAL</span></td></tr>

          <tr style='".$this->corDanger."'><td rowspan='3' style='".$this->border().$this->bolder.$this->text_center." width:270px;'>CURSO</td>";

          foreach ($arrayClasses as $classe) {
            $this->html .="<td colspan='3' style='".$this->border().$this->bolder.$this->maiuscula.$this->text_center."'>".classeExtensa($classe, $this->sePorSemestre)."</td>";
          }
          $this->html .="<td colspan='3' style='".$this->border().$this->bolder.$this->text_center."'>TOTAL</td>";
          $this->html .="</tr><tr style='".$this->corDanger."'>";

          foreach ($arrayClasses as $classe) {
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

          foreach ($this->listaCursos as $curso) {

              $this->html .="<tr><td style='".$this->border().$this->maiuscula."'>".$curso->nomeCurso;

              if($curso->areaFormacaoCurso!="Geral" && $curso->areaFormacaoCurso!=NULL && $curso->areaFormacaoCurso!="TOT"){
                $this->html .=" (".$curso->areaFormacaoCurso.")";
              }
              $this->html .="</td>";

            foreach ($cabecalhos as $cab) {
              if($cab["tituloDb"]=="numTurmas"){

                  $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorTurma($curso->idPNomeCurso, $cab["classe"])."</td>";
              }else{
                $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contadorAlunos($curso->idPNomeCurso, $cab["classe"], $cab["tituloDb"])."</td>";
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
          </table><br/><div class='maiuscula'>".$this->assinaturaDirigentes("CDEPE ")."</div>


          </div></body></html>";
        }

        private function contadorAlunos($idCurso, $classe, $genero){
          $contador=0;
          foreach ($this->alunosInscritos as $alunos) {
                if(seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && seComparador($idCurso, $alunos->idMatCurso) && $alunos->periodoAluno==$this->periodo){ 
                    $contador++;
                }

          }
          return completarNumero($contador);
        }

        private function contadorTurma($idCurso, $classe){
          $contador=0;
          foreach ($this->turmas as $turma) {
              if(seComparador($classe, $turma->classe) && seComparador($idCurso, $turma->idListaCurso) && $turma->periodoTurma==$this->periodo){
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
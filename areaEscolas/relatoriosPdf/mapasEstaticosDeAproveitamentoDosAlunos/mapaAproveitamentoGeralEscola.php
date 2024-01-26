<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
    include_once 'analizadorDados.php';

    class mapa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa de Aproveitamento Geral na Escola"); 

            if(isset($_GET["idPAno"])){
                $this->idPAno = $_GET["idPAno"];
            }else{
                $this->idPAno = $this->idAnoActual;
            }


            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:$this->selectUmElemento("nomecursos", "idPNomeCurso", ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"]]);


            $this->trimestreApartir = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;
            $this->idPDisciplina = isset($_GET["idPDisciplina"])?$_GET["idPDisciplina"]:null;
            $this->nomeCurso();
            $this->numAno();

            if($this->trimestreApartir=="I"){
                $this->trimestreApartirExtensa="I TRIMESTRE";
                $trimestreAbr="I";
            }else if($this->trimestreApartir=="II"){
                $this->trimestreApartirExtensa="II TRIMESTRE";
                $trimestreAbr="II";
            }else if($this->trimestreApartir=="III"){
                $this->trimestreApartirExtensa="III TRIMESTRE";
                $trimestreAbr="III";
            }else if($this->trimestreApartir=="IV"){
                $this->trimestreApartirExtensa="FINAL";
                $trimestreAbr="TOT";
            }

            $this->analizador = new analizador(__DIR__);
            $this->analizador->idPAno = $this->idPAno;
            $this->analizador->trimestreApartir=$this->trimestreApartir;
            $this->analizador->tipoAproveitamento="geral";
            $this->analizador->classe="todas";
            $this->analizador->tipoCurso=$this->tipoCurso;

            $this->cursos = $this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) ;

            $this->analizador->inicializador();

            if($this->verificacaoAcesso->verificarAcesso("", ["pautaGeral1"], [], "")){                                
                $this->exbirMapaPorTurma();
            }else{
              $this->negarAcesso();
            }
        }

        private function exbirMapaPorTurma(){

            $this->html="<html>
            <head>
                <title>Mapa de Aproveitamento</title>
                <style>
                    td{
                        font-size:11pt;
                    }
                </style>
            </head>
            <body style='margin:-30px;'>".$this->fundoDocumento("../../../", "horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

              if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS DO ANO ".$this->numAno."</p>";
              }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." DO ANO ".$this->numAno."</p>";
              }

        

          foreach ($this->cursos as $curso){

              $this->html .="<br/>";
              if($curso->tipoCurso=="pedagogico"){
                  $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$curso->areaFormacaoCurso."</strong></p>
                  <p style='".$this->maiuscula."'>OPÇÃO: <strong>".$curso->nomeCurso."</strong></p>";
              }else if($curso->tipoCurso=="tecnico"){
                  $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$curso->areaFormacaoCurso."</strong></p>
                  <p style='".$this->maiuscula."'>CURSO: <strong>".$curso->nomeCurso."</strong></p>";
              }else{
                  $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$curso->nomeCurso."</strong></p>";
              }

             $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;

             foreach (listarItensObjecto($curso, "classes") as $classe) {

                 $this->html .="<tr><td colspan='".(count($this->analizador->cabecalhos)+1)."' style='".$this->border().$this->text_center.$this->bolder.$this->maiuscula."background-color:rgba(0,0,0,0.3)'>".$classe["designacao"]."</td></tr>";

                    foreach ($this->turmasEscola([intval($curso["idPNomeCurso"])], [intval($classe["identificador"]."")], $this->idPAno) as $turma) {
                      
                      $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$turma["designacaoTurma"]."</td>"; 
                      foreach ($this->analizador->cabecalhos as $cab) {
                          
                          $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso["idPNomeCurso"], $classe["identificador"], $turma["nomeTurma"], $cab["tituloDb"], $cab["genero"])."</td>";
                      }
                      $this->html .="</tr>";
                    }

                 $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>SUBTOTAL</td>";
                      foreach ($this->analizador->cabecalhos as $cab) {
                          $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto($curso["idPNomeCurso"], $classe["identificador"], "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                      }
                      $this->html .="</tr>";
             }

             $this->html .="<tr style='background-color:rgba(0,0,0,0.3)'><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
              foreach ($this->analizador->cabecalhos as $cab) {
                  $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto($curso["idPNomeCurso"], "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
              }
              $this->html .="</tr>";
             $this->html .="</table>";
        }
        $this->html .="<div style='".$this->text_center."'>".$this->assinaturaDirigentes("mengi")."</div>";
        

           $this->exbirMapaPorClasse();            
        }

        private function exbirMapaPorClasse(){

            $this->html .="
            <div style='page-break-before: always;'>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

            if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS DO ANO ".$this->numAno."</p>";
            }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." DO ANO ".$this->numAno."</p>";
            }

            foreach ($this->cursos as $curso){

              $this->html .="<br/><p style='".$this->text_center.$this->maiuscula."'>CURSO: <strong>".$curso->nomeCurso;

              if($curso->areaFormacaoCurso!="Geral" && $curso->areaFormacaoCurso!=NULL && $curso->areaFormacaoCurso!="TOT"){
                $this->html .=" (".$curso->areaFormacaoCurso.")";
              }
              $this->html .="</strong></p>";

             $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;

             foreach (listarItensObjecto($curso, "classes") as $classe) {

                 $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".$classe["designacao"]."</td>";
                  foreach ($this->analizador->cabecalhos as $cab) {
                      $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso["idPNomeCurso"], $classe["identificador"], "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                  }
                  $this->html .="</tr>";
             }

             $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
              foreach ($this->analizador->cabecalhos as $cab) {
                  $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto($curso["idPNomeCurso"], "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
              }
              $this->html .="</tr></table>";
            }
            

           $this->html .="<div style='".$this->text_center."'>".$this->assinaturaDirigentes("mengi")."</div></div>";
           
           $this->exbirMapaPorCurso();
        }
        
        
        private function exbirMapaPorClasseResumo(){

            $this->html .="
            <div style='page-break-before: always;'>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

            if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS DO ANO ".$this->numAno."</p>";
            }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." DO ANO ".$this->numAno."</p>";
            }
    
            $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;

             foreach (listarItensObjecto($curso, "classes") as $classe) {

                 $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".$classe["identificador"]."</td>";
                  foreach ($this->analizador->cabecalhos as $cab) {
                      $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $classe["identificador"], "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                  }
                  $this->html .="</tr>";
             }

             $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
              foreach ($this->analizador->cabecalhos as $cab) {
                  $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("TOT", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
              }
              $this->html .="</tr></table>";

           $this->html .="<div style='".$this->text_center."'>".$this->assinaturaDirigentes("mengi")."</div></div>";
           //$this->exbirMapaPorCurso();
        }

        private function exbirMapaPorCurso(){

            $this->html .="
            <div style='page-break-before: always;'>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

              if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS DO ANO ".$this->numAno."</p>";
              }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." DO ANO ".$this->numAno."</p>";
              }
        $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
        foreach ($this->cursos as $curso){
           $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$curso->abrevCurso."</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso["idPNomeCurso"], "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>";
        }
        $this->html .="<tr style='background-color:rgba(0,0,0,0.3)'><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
        foreach ($this->analizador->cabecalhos as $cab) {
            $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("TOT", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
        }
        $this->html .="</tr>";

        $this->html .="</table><div style='".$this->text_center."'>".$this->assinaturaDirigentes("mengi")."</div></body></html>";

        $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Aproveitamento Geral-".$this->trimestreApartir."-".$this->numAno, "Mapa_de_Aproveitamento_Geral-".$this->trimestreApartir."-".$this->idPAno, "A4", "landscape");
        }        
    }
    new mapa(__DIR__);
?>
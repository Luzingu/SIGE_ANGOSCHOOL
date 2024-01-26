<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
    include_once 'analizadorDados.php';

    class mapa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa Aproveitamento por Disciplina na Escola"); 

            if(isset($_GET["idPAno"])){
                $this->idPAno = $_GET["idPAno"];
            }else{
                $this->idPAno = $this->idAnoActual;
            }


            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            if($this->idPCurso==""){
                $this->idPCurso = $this->idPCurso = $this->selectUmElemento2("nomecursos", ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"]], ["limit"=>1], "cursos", ["estadoCurso=A"], "idPNomeCurso");
            } 
            $this->trimestreApartir = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;
            $this->idPDisciplina = isset($_GET["idPDisciplina"])?$_GET["idPDisciplina"]:null;
            $this->nomeCurso();
            $this->numAno();

            if($this->trimestreApartir=="I"){
                $this->trimestreApartirExtensa="Iº TRIMESTRE";
                $trimestreAbr="I";
            }else if($this->trimestreApartir=="II"){
                $this->trimestreApartirExtensa="IIº TRIMESTRE";
                $trimestreAbr="II";
            }else if($this->trimestreApartir=="III"){
                $this->trimestreApartirExtensa="IIIº TRIMESTRE";
                $trimestreAbr="III";
            }else if($this->trimestreApartir=="IV"){
                $this->trimestreApartirExtensa="FINAL";
                $trimestreAbr="";
            }

            $this->analizador = new analizador(__DIR__);
            $this->analizador->idPAno = $this->idPAno;
            $this->analizador->trimestreApartir=$this->trimestreApartir;
            $this->analizador->tipoAproveitamento="porDisciplina";
            $this->analizador->classe="todas";
            $this->analizador->tipoCurso=$this->tipoCurso;
            
            $this->analizador->inicializador();
            $this->analizador->alunosAvaliadosPorDisciplina($this->idPDisciplina);

            $this->nomeDisciplina = $this->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$this->idPDisciplina]);

            $this->cursos = $this->selectArray("nomecursos", ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ['sort'=>["nomeCurso"=>1]], "cursos", ["tipoCurso=".$this->tipoCurso, "idCursoEscola=".$_SESSION['idEscolaLogada'], "estadoCurso=A"]) ;

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
              $this->html .="<p style='".$this->text_center.$this->maiuscula."'>DISCIPLINA: <strong>".$this->nomeDisciplina."</strong></p>";

        if(seEnsinoPrimario()){

              $this->html .="<br/>
              <p style='".$this->maiuscula."'><strong>ENSINO PRIMÁRIO</strong></p>";

             $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;

             for ($classe=0; $classe<=6; $classe++) {

                 $this->html .="<tr><td colspan='".(count($this->analizador->cabecalhos)+1)."' style='".$this->border().$this->text_center.$this->bolder.$this->maiuscula."background-color:rgba(0,0,0,0.3)'>".classeExtensa($classe)."</td></tr>";

                 foreach ($this->turmasEscola(array(), [intval("".$classe."")], $this->idPAno) as $turma) {
                      
                    $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$turma["designacaoTurma"]."</td>"; 
                    foreach ($this->analizador->cabecalhos as $cab) {
                          
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("ensinoPrimario", $classe, $turma["nomeTurma"], $cab["tituloDb"], $cab["genero"])."</td>"; 
                    }
                    $this->html .="</tr>";
                 }

                $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>SUBTOTAL</td>";

                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("ensinoPrimario", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
            }

            $this->html .="<tr style='background-color:rgba(0,0,0,0.3)'><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("ensinoPrimario", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr></table>";
        }

        if(seEnsinoBasico()){

              $this->html .="<br/>
              <p style='".$this->maiuscula."'><strong>Iº CICLO</strong></p>";

             $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;

             for ($classe=7; $classe<=9; $classe++) {

                $this->html .="<tr><td colspan='".(count($this->analizador->cabecalhos)+1)."' style='".$this->border().$this->text_center.$this->bolder.$this->maiuscula."background-color:rgba(0,0,0,0.3)'>".classeExtensa($classe)."</td></tr>";

                 foreach ($this->turmasEscola(array(), [intval("".$classe."")], $this->idPAno) as $turma) {
                      
                    $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$turma["designacaoTurma"]."</td>"; 
                    foreach ($this->analizador->cabecalhos as $cab) {
                          
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("1Ciclo", $classe, $turma["nomeTurma"], $cab["tituloDb"], $cab["genero"])."</td>"; 
                    }
                    $this->html .="</tr>";
                 }

                $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>SUBTOTAL</td>";

                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("1Ciclo", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
            }

            $this->html .="<tr style='background-color:rgba(0,0,0,0.3)'><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("ensinoPrimario", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr></table>";
        }

        if(seEnsinoSecundario()){
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

              $arrayClasses=array();
              for($i=10; $i<=(9+(int)$curso->duracao); $i++){
                $arrayClasses[] = $i;
              }
             $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;


             foreach ($arrayClasses as $classe) {
                 $this->html .="<tr><td colspan='".(count($this->analizador->cabecalhos)+1)."' style='".$this->border().$this->text_center.$this->bolder.$this->maiuscula."background-color:rgba(0,0,0,0.3)'>".classeExtensa($classe, $curso->duracao)."</td></tr>";

                 foreach ($this->turmasEscola([intval($curso["idPNomeCurso"])], [intval("".$classe."")], $this->idPAno) as $turma) {
                      
                      $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$turma["designacaoTurma"]."</td>"; 
                      foreach ($this->analizador->cabecalhos as $cab) {
                          
                          $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso["idPNomeCurso"], $classe, $turma["nomeTurma"], $cab["tituloDb"], $cab["genero"])."</td>"; 
                      }
                      $this->html .="</tr>";
                 }

                 $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>SUBTOTAL</td>";
                      foreach ($this->analizador->cabecalhos as $cab) {
                          $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto($curso["idPNomeCurso"], $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
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

            if(seEnsinoPrimario()){
                $this->html .="<br/><p style='".$this->maiuscula."'<strong>ENSINO PRIMÁRIO</strong></p><table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
                for ($classe=0; $classe<=6; $classe++) {
                    $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".classeExtensa($classe)."</td>";
                    foreach ($this->analizador->cabecalhos as $cab) {
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("ensinoPrimario", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                    }
                    $this->html .="</tr>";
                }

                $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("ensinoPrimario", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                  $this->html .="</tr></table>";
            }

            if(seEnsinoBasico()){
                $this->html .="<br/><p style='".$this->maiuscula."'<strong>Iº CICLO</strong></p><table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
                for ($classe=7; $classe<=9; $classe++) {
                    $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".classeExtensa($classe)."</td>";
                    foreach ($this->analizador->cabecalhos as $cab) {
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("1Ciclo", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                    }
                    $this->html .="</tr>";
                }

                $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("1Ciclo", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr></table>";
            }

            if(seEnsinoSecundario()){

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

                      $arrayClasses=array();
                      for($i=10; $i<=(9+(int)$curso->duracao); $i++){
                        $arrayClasses[]=$i;
                      }                     

                     $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;

                     foreach ($arrayClasses as $classe) {

                         $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".classeExtensa($classe, $curso->duracao)."</td>";
                          foreach ($this->analizador->cabecalhos as $cab) {
                              $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso["idPNomeCurso"], $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                          }
                          $this->html .="</tr>";
                     }

                     $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
                      foreach ($this->analizador->cabecalhos as $cab) {
                          $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto($curso["idPNomeCurso"], $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                      }
                      $this->html .="</tr>";


                     $this->html .="</table>";
                }
            }

           $this->html .="<div style='".$this->text_center."'>".$this->assinaturaDirigentes("mengi")."</div></div>";
           $this->exbirMapaPorCurso();
              
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
              $this->html .="<p style='".$this->text_center.$this->maiuscula."'>DISCIPLINA: <strong>".$this->nomeDisciplina."</strong></p>";
            $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;

            if(seEnsinoPrimario()){
                $this->html .="<tr><td style='".$this->border().$this->text_center."'>ENS. PRIM.</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("ensinoPrimario", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
            }
            if(seEnsinoBasico()){
                $this->html .="<tr><td style='".$this->border().$this->text_center."'>Iº CICLO</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("1Ciclo", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
            }

            if(seEnsinoSecundario()){
                foreach ($this->cursos as $curso){
                   $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$curso["abrevCurso"]."</td>";
                    foreach ($this->analizador->cabecalhos as $cab) {
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso["idPNomeCurso"], "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                    }
                    $this->html .="</tr>";
                }
            }

            $this->html .="<tr style='background-color:rgba(0,0,0,0.3)'><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto("TOT", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>";

            $this->html .="</table><div style='".$this->text_center."'>".$this->assinaturaDirigentes("mengi")."</div></body></html>";

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Aproveitamento de ".$this->nomeDisciplina."-".$this->trimestreApartir."-".$this->numAno, "Mapa_de_Aproveitamento_de-".$this->idPDisciplina."-".$this->trimestreApartir."-".$this->idPAno, "A4", "landscape");
        }        
    }



new mapa(__DIR__);
    
    
  
?>
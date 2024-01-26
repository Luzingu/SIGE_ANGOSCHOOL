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
    include_once '../analizadorDados.php';
    include_once '../factorizador.php';

    class mapa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
            parent::__construct("Rel-Mapa de Aproveitamento Geral no Curso"); 

            if(isset($_GET["idPAno"])){
                $this->idPAno = $_GET["idPAno"];
            }else{
                $this->idPAno = $this->idAnoActual;
            }
            $this->classe=7;
            $this->idPCurso = "1Ciclo";
            $this->trimestreApartir = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;

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
            $this->analizador->tipoAproveitamento="geral";
            
            $this->analizador->tipoCurso="1Ciclo";
            $this->analizador->inicializador();
            $this->factorizador = new factorizador();

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aRelEstatistica"])){                                
                $this->exbirMapaPorClasse();
                $this->exbirMapaPorEscola();
            }else{
              $this->negarAcesso();
            }
        }

        private function exbirMapaPorClasse(){

            $this->html .="
            <div>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho();

              if($this->trimestreApartir=="IV"){
                $this->html .="<br/><p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
              }else{
                $this->html .="<br/><p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
              }          


           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
           
           for ($classe=7; $classe<=9; $classe++) {

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".classeExtensa($classe)."</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->factorizador->factorCursoClasse("TOT", $this->analizador, "1Ciclo", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
           }

           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->factorizador->factorCurso("TOT", $this->analizador, "1Ciclo", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>
            </table>".$this->assinaturaDirigentes(["CDEPE"])."</div>";
        }

        private function exbirMapaPorEscola(){

            $this->html .="
            <div style='page-break-before: always;'>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho();

              if($this->trimestreApartir=="IV"){
                $this->html .="<br/><p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS DO ANO ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
              }else{
                $this->html .="<br/><p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." DO ANO ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
              }          


           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
           
           foreach($this->analizador->escolas as $escola) {

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".$this->selectUmElemento("escolas", "abrevNomeEscola", "idPEscola=:idPEscola", [$escola])."</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->factorizador->factorCursoClasse($escola, $this->analizador, "1Ciclo", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
           }

           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->factorizador->factorCurso("TOT", $this->analizador, "1Ciclo", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>
            </table>
           <div style='".$this->text_center."'>".$this->assinaturaDirigentes(["CDEPE"])."</div>
           </body></html>";

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Aproveitamento Geral do 1 Ciclo-".$this->trimestreApartir."-".$this->numAno, "Mapa_de_Aproveitamento_Geral_1_Ciclo-".$this->trimestreApartir."-".$this->idPAno, "A4", "landscape");
        }        
    }



new mapa(__DIR__);
    
    
  
?>
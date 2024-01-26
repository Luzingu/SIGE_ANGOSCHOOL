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
            $this->classe=1;
            $this->idPCurso = "ensinoPrimario";
            $this->trimestreApartir = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;


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
            
            $this->analizador->tipoCurso="ensinoPrimario";
            $this->analizador->inicializador();
            $this->factorizador = new factorizador();

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aPedagogica", "aDirectoria", "aAdministrativa"], "", "")){                                
                $this->exbirMapaPorClasse();
            }else{
              $this->negarAcesso();
            }
        }

        private function exbirMapaPorClasse(){

            $this->html="<html>
            <head>
                <title>Mapa de Aproveitamento</title>
                <style>
                    td{
                        font-size:10pt;
                    }
                </style>
            </head>
            <body style='margin:-30px;'>";

            $this->html .="
            <div>".$this->fundoDocumento("horizontal")."
            <div style='cabecalho'>
            <div style='position: absolute;'><div style='margin-top: 10px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho();

              if($this->trimestreApartir=="IV"){
                $this->html .="<br/><p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS DO ANO ".$this->numAno."</p>";
              }else{
                $this->html .="<br/><p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." DO ANO ".$this->numAno."</p>";
              }        


           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
           
           for ($classe=1; $classe<=6; $classe++) {

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".classeExtensa($classe)."</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->factorizador->factorCursoClasse("TOT", $this->analizador, "ensinoPrimario", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
           }

           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->factorizador->factorCurso("TOT", $this->analizador, "ensinoPrimario", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>
            </table> 
           <div style='".$this->text_center."'>".$this->assinaturaDirigentes(["CDEPE"])."</div>";
           $this->exbirMapaPorEscola();
        }
        private function exbirMapaPorEscola(){

            $this->html .="
            <div style='page-break-before: always;'>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 10px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho();

              if($this->trimestreApartir=="IV"){
                $this->html .="<br/><p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
              }else{
                $this->html .="<br/><p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
              }          


           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
           
           foreach($this->analizador->escolas as $escola) {

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".$this->selectUmElemento("escolas", "abrevNomeEscola", "idPEscola=:idPEscola", [$escola])."</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->factorizador->factorCursoClasse($escola, $this->analizador, "ensinoPrimario", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
           }
 
           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->factorizador->factorCurso("TOT", $this->analizador, "ensinoPrimario", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>
            </table>
           <div style='".$this->text_center."'>".$this->assinaturaDirigentes(["CDEPE"])."</div>
           </body></html>";

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Aproveitamento Geral do Ensino Primário-".$this->trimestreApartir."-".$this->numAno, "Mapa_de_Aproveitamento_Geral_ensino_primario-".$this->trimestreApartir."-".$this->idPAno, "A4", "landscape");
        }        
    }



new mapa(__DIR__);
    
    
  
?>
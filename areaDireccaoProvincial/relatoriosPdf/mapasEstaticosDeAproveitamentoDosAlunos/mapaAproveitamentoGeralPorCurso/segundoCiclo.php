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


            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = 10;
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
            $this->analizador->tipoAproveitamento="geral";
            
            $this->analizador->tipoCurso=$this->tipoCurso;
            $this->analizador->inicializador();
            $this->factorizador = new factorizador();

            $this->listaCursos=array();
            foreach($this->selectArray("nomecursos LEFT JOIN cursos ON idPNomeCurso=idFNomeCurso LEFT JOIN escolas ON idPEscola=idCursoEscola LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPNomeCurso", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola AND estadoCurso=:estadoCurso AND tipoCurso=:tipoCurso", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), $this->analizador->privacidade, "A", "A", $this->tipoCurso]) as $a){
                $this->listaCursos = array_merge($this->listaCursos, $this->selectArray("nomecursos", "*", "idPNomeCurso=:idPNomeCurso", [$a->idPNomeCurso]));
            }

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aPedagogica", "aDirectoria"], "", "")){                                
                $this->exbirMapaPorClasse();
            }else{
              $this->negarAcesso();
            }
        }

        private function exbirMapaPorClasse(){

            $this->html="<html>
            <head>
                <style>
                    td{
                        font-size:10pt;
                    }
                </style>
            </head>
            <body style='margin:-30px;'>

            <div>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho();

            if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado.$this->maiuscula."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
            }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado.$this->maiuscula."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
            }    

           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
           
           for ($classe=10; $classe<=(9+$this->duracaoCurso); $classe++) {

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".classeExtensa($classe, $this->sePorSemestre)."</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->factorizador->factorCursoClasse("TOT", $this->analizador, "TOT", $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
           }

           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->factorizador->factorCurso("TOT", $this->analizador, "TOT", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>
            </table>".$this->assinaturaDirigentes(["CDEPE"])."</div>";
            $this->exbirMapaPorCurso();
        }
        private function exbirMapaPorCurso(){

            $this->html .="<div style='page-break-before: always;'>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho();

            if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado.$this->maiuscula."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
            }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado.$this->maiuscula."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
            }    

           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
           
           foreach ($this->listaCursos as $c) {

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".$c->abrevCurso."</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->factorizador->factorCursoClasse("TOT", $this->analizador, $c->idPNomeCurso, "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
           }

           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->factorizador->factorCurso("TOT", $this->analizador, "TOT", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>
            </table>".$this->assinaturaDirigentes(["CDEPE"])."</div>";
            $this->exbirMapaPorEscola();
        }

        private function exbirMapaPorEscola(){

            $this->html .="<div style='page-break-before: always;'>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho();

            if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado.$this->maiuscula."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
            }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado.$this->maiuscula."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." - ".$this->numAno." NO ENSINO ".$this->analizador->labelPrivacidade."</p>";
            }    

           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;
           
           foreach ($this->analizador->escolas as $escola) {

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".$this->selectUmElemento("escolas", "abrevNomeEscola", "idPEscola=:idPEscola", [$escola])."</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->factorizador->factorCursoClasse($escola, $this->analizador, "TOT", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
           }

           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->factorizador->factorCurso("TOT", $this->analizador, "TOT", "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>
            </table>".$this->assinaturaDirigentes(["CDEPE"])."</div>";

           $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Aproveitamento Geral por Curso-".$this->nomeCursoAbr."-".$this->trimestreApartir."-".$this->numAno, "Mapa_de_Aproveitamento_Geral_por_Curso-".$this->idPCurso."-".$this->trimestreApartir."-".$this->idPAno, "A4", "landscape");
        }       
    }



new mapa(__DIR__);
    
    
  
?>
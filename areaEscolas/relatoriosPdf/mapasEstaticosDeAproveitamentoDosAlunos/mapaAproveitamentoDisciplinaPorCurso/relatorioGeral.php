<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../../funcoesAuxiliares.php');
    include_once ('../../../funcoesAuxiliaresDb.php');
    include_once '../analizadorDados.php';

    class mapa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa de Aproveitamento por Disciplina no Curso"); 

            if(isset($_GET["idPAno"])){
                $this->idPAno = $_GET["idPAno"];
            }else{
                $this->idPAno = $this->idAnoActual;
            }


            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
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
            $this->nomeDisciplina = $this->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$this->idPDisciplina]);
            $this->analizador->classe=10;
            $this->analizador->tipoCurso=$this->tipoCurso;
            $this->analizador->alunosAvaliadosPorDisciplina($this->idPDisciplina);
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
            <body style='margin:-30px;'><div style='cabecalho'>
            <div style='position: absolute;'>".$this->fundoDocumento("../../../../","horizontal")."
            <div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

            if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS DO ANO ".$this->numAno."</p>";
            }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." DO ANO ".$this->numAno."</p>";
            }
              $this->html .="<p style='".$this->text_center.$this->maiuscula."'>DISCIPLINA: <strong>".$this->nomeDisciplina."</strong></p>";

            if($this->tipoCurso=="pedagogico"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
            }else if($this->tipoCurso=="tecnico"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }else{
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }
            $arrayClasses=array();
            for($i=10; $i<=(9+$this->duracaoCurso); $i++){
                $arrayClasses[]=$i;
            }
            


           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;

           foreach ($arrayClasses as $classe) {
               $this->html .="<tr><td colspan='".(count($this->analizador->cabecalhos)+1)."' style='".$this->border().$this->text_center.$this->bolder.$this->maiuscula."background-color:rgba(0,0,0,0.3)'>".classeExtensa($classe, $this->sePorSemestre)."</td></tr>";

               foreach ($this->turmasEscola($this->idPCurso, $classe, $this->idPAno) as $turma) {
                     
                    $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$turma["designacaoTurma"]."</td>"; 
                    foreach ($this->analizador->cabecalhos as $cab) {
                        
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($this->idPCurso, $classe, $turma["nomeTurma"], $cab["tituloDb"], $cab["genero"])."</td>";
                    }
                    $this->html .="</tr>";
               }

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>SUBTOTAL</td>";
                    foreach ($this->analizador->cabecalhos as $cab) {
                        $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto($this->idPCurso, $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                    }
                    $this->html .="</tr>";
           }

           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto($this->idPCurso, "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr></table>
           <div style='".$this->text_center."'>".$this->assinaturaDirigentes(8)."</div>";    
           $this->exbirMapaPorClasse();
        }

        private function exbirMapaPorClasse(){

            $this->html .="
            <div style='page-break-before: always;'>".$this->fundoDocumento("horizontal")."
            <div style='position: absolute;'>
            <div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

              if($this->trimestreApartir=="IV"){
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO FINAL DOS ALUNOS DO ANO ".$this->numAno."</p>";
              }else{
                $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE APROVEITAMENTO DO ".$this->trimestreApartirExtensa." DO ANO ".$this->numAno."</p>";
              }
              $this->html .="<p style='".$this->text_center.$this->maiuscula."'>DISCIPLINA: <strong>".$this->nomeDisciplina."</strong></p>";

            if($this->tipoCurso=="pedagogico"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
            }else if($this->tipoCurso=="tecnico"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }else{
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }

            $arrayClasses=array();
            for($i=10; $i<=(9+$this->duracaoCurso); $i++){
                $arrayClasses[]=$i;
            }

           $this->html .="<table style='".$this->tabela." width:100%;'>".$this->analizador->varCabecalho;


           foreach ($arrayClasses as $classe) {

               $this->html .="<tr><td style='".$this->border().$this->text_center.$this->maiuscula."'>".classeExtensa($classe, $this->sePorSemestre)."</td>";
                foreach ($this->analizador->cabecalhos as $cab) {
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($this->idPCurso, $classe, "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
                }
                $this->html .="</tr>";
           }

           $this->html .="<tr><td style='".$this->border().$this->text_center.$this->bolder."'>TOTAL</td>";
            foreach ($this->analizador->cabecalhos as $cab) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->analizador->quanto($this->idPCurso, "TOT", "TOT", $cab["tituloDb"], $cab["genero"])."</td>";
            }
            $this->html .="</tr>
            </table>
           <div style='".$this->text_center."'>".$this->assinaturaDirigentes(8)."</div>
           </body></html>";

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Aproveitamento por Curso de ".$this->nomeDisciplina."-".$this->nomeCursoAbr."-".$this->trimestreApartir."-".$this->numAno, "Mapa_de_Aproveitamento_por_Curso-".$this->idPCurso."-".$this->idPDisciplina."-".$this->trimestreApartir."-".$this->idPAno, "A4", "landscape");
        }        
    }



new mapa(__DIR__);
    
    
  
?>
<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
    include_once 'analizadorDados.php';

    class mapa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Real-Mapa de Aproveitamento Final"); 

            if(isset($_GET["idPAno"])){
                $this->idPAno = $_GET["idPAno"];
            }else{
                $this->idPAno = $this->idAnoActual;
            }

            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->trimestreApartir = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:null;
            $this->nomeCurso();
            $this->numAno();


            $this->analizador = new analizador(__DIR__);
            $this->analizador->idPAno = $this->idPAno;
            $this->analizador->trimestreApartir="IV";
            $this->analizador->tipoAproveitamento="geral";

            $this->cursos = $this->selectArray("nomecursos", ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ['sort'=>["nomeCurso"=>1]], "cursos", ["tipoCurso=".$this->tipoCurso, "idCursoEscola=".$_SESSION['idEscolaLogada'], "estadoCurso=A"]);
            
            $this->analizador->tipoCurso=$this->tipoCurso;

            $this->analizador->inicializador();

            $this->html .="
            <html style='margin:10px;'>
            <head>
                <title>Mapa Estatístico de Avaliação Final</title>
                <style>
                  table tr td{
                    padding:0px;
                  }
                </style>
            </head>
            <body>".$this->fundoDocumento("../../../", "horizontal")."
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho()."
            <p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA ESTATÍSTICO DE AVALIAÇÃO FINAL - ".$this->numAno."</p>";

            if($this->verificacaoAcesso->verificarAcesso("", ["pautaGeral1"], [], "")){

              $this->periodosEscolas=$this->selectUmElemento("escolas", "periodosEscolas", ["idPEscola"=>$_SESSION["idEscolaLogada"]]);

                $this->condPeriodo="reg";
                $this->periodoExtenso="REGULAR";                             
                $this->mapa();
                if($this->periodosEscolas=="regPos"){
                  $this->condPeriodo="pos";
                  $this->periodoExtenso="PÓS-LABORAL";                 
                  $this->mapa();
                }


                $this->exibidor();
            }else{
              $this->negarAcesso();
            }
        }

        private function mapa(){

          $this->analizador->alunosTrans = $this->selectArray("AfonsoLuzingu", ["luzinguAno"=>$this->idPAno, "idTransfAno"=>$this->idPAno, "tipoCurso"=>$this->tipoCurso, "idTransfEscolaOrigem"=>$_SESSION['idEscolaLogada'], "periodoAluno"=>$this->condPeriodo, "tipoCurso"=>$this->tipoCurso]);

          $this->alunosInscritos = $this->selectArray("AfonsoLuzingu", ["luzinguAno"=>$this->idPAno, "idReconfAno"=>$this->idPAno, "tipoCurso"=>$this->tipoCurso, "luzinguEscola"=>$_SESSION['idEscolaLogada'], "periodoAluno"=>$this->condPeriodo, "tipoCurso"=>$this->tipoCurso]);

          $this->turmas = $this->turmasEscola(array(), array(), $this->idPAno, $this->tipoCurso);

          if($this->periodosEscolas=="regPos"){
            $this->html .="<p style='".$this->text_center."'>REGIME: <strong style='".$this->vermelha."'>".$this->periodoExtenso."</strong></p><br/>";
          }

          $this->html .="<table style='".$this->tabela." width:100%; font-size:11pt;'><tr><td rowspan='3' style='".$this->border().$this->bolder.$this->text_center."'>Curso</td>";
          for ($i=10; $i<=(9+$this->duracaoCurso); $i++) { 
            $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."' colspan='13'>".classeExtensa($i, $this->sePorSemestre)."</td>";
          }
          $this->html .="</tr><tr>";

          for ($i=10; $i<=(9+$this->duracaoCurso); $i++) {
            $this->html .="<td rowspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Tur.</td>
            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº alunos que<br/>iniciaram</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Excluido por<br/>falta</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Anulações de<br/>matriculas</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Transferidos</td>";

            if($i==(9+$this->duracaoCurso)){
                $this->html .="<td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº alunos<br/>Diplomados</td>";
            }else{
              $this->html .="<td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº alunos que<br/>Transitam para<br/>".($i+1)."ª Classe</td>";
            }
            $this->html .="<td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº alunos que<br/>não transitam</td>";
          }
          $this->html .="</tr><tr>";

          for ($i=10; $i<=(9+$this->duracaoCurso); $i++) {
            $this->html .="
            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>

            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>

            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>

            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>";
          }
          $this->html .="</tr>";

          foreach ($this->cursos as $curso) {
            
            $this->html .="<tr><td style='".$this->border()."'>".$curso->abrevCurso."</td>";
            for ($i=10; $i<=(9+$this->duracaoCurso); $i++) {
 
              $this->html .="
              <td style='".$this->border().$this->text_center."'>".$this->contadorTurma($curso->idPNomeCurso, $i)."</td><td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "matriculados", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "matriculados", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "excluidoF", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "excluidoF", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "matriculaAnulada", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "matriculaAnulada", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "transferidos", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "transferidos", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "aprovados", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "aprovados", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "reprovadoGeral", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto($curso->idPNomeCurso, $i, "TOT", "reprovadoGeral", "F")."</td>";
            }
            $this->html .="</tr>";
          }

          $this->html .="<tr><td style='".$this->border().$this->text_center."'>TOTAL</td>";
            for ($i=10; $i<=(9+$this->duracaoCurso); $i++) {
 
              $this->html .="
              <td style='".$this->border().$this->text_center."'>".$this->contadorTurma("TOT", $i)."</td><td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "matriculados", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "matriculados", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "excluidoF", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "excluidoF", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "matriculaAnulada", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "matriculaAnulada", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "transferidos", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "transferidos", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "aprovados", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "aprovados", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "reprovadoGeral", "TOT")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", $i, "TOT", "reprovadoGeral", "F")."</td>";
            }
            $this->html .="</tr>";
          $this->html .="</table>";
        }

        private function exibidor(){

          $this->analizador->alunosTrans = $this->selectArray("AfonsoLuzingu", [], ["luzinguAno"=>$this->idPAno, "idTransfAno"=>$this->idPAno, "tipoCurso"=>$this->tipoCurso, "idTransfEscolaOrigem"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>$this->tipoCurso, "nomeTurma"=>array('$ne'=>null)]);

          $this->alunosInscritos = $this->selectArray("AfonsoLuzingu", [], ["luzinguAno"=>$this->idPAno, "idReconfAno"=>$this->idPAno, "tipoCurso"=>$this->tipoCurso, "luzinguEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>$this->tipoCurso, "nomeTurma"=>array('$ne'=>null)]);

          $this->turmas = $this->turmasEscola(array(), array(), $this->idPAno, $this->tipoCurso);

          $this->html .="<p style='".$this->text_center.$this->bolder."width:50%;'>CONSOLIDADO GERAL DA ESCOLA</p>

          <table style='".$this->tabela."width:50%;'>
          <tr>
           <td rowspan='2' style='".$this->border().$this->bolder.$this->text_center."'></td>
          <td rowspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº Turmas</td> 
          <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº alunos que<br/>iniciaram</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Excluido por<br/>falta</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Anulações de<br/>matriculas</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Transferidos</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº alunos que<br/>não Transitam</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº alunos que<br/>Transitam</td>

            <td colspan='2' style='".$this->border().$this->bolder.$this->text_center."'>Nº alunos<br/>Diplomados</td>";
           
          $this->html .="</tr><tr>
            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>

            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>

            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>

            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
            <td style='".$this->border().$this->bolder.$this->text_center."'>MF</td><td style='".$this->border().$this->bolder.$this->text_center."'>F</td>
          </tr>
          <tr>
          <td style='".$this->border().$this->text_center."'>Total</td>
          ";

          $this->html .="
              <td style='".$this->border().$this->text_center."'>".$this->contadorTurma("TOT", "TOT")."</td><td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "matriculados", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "matriculados", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "excluidoF", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "excluidoF", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "matriculaAnulada", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "matriculaAnulada", "F")."</td>

              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "transferidos", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "transferidos", "F")."</td><td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "reprovadoGeral", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "reprovadoGeral", "F")."</td>";

              //Pegar alunos aprovados não finalistas
            //$this->analizador->alunos = $this->selectArray("alunosmatriculados LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN avaliacaoanualaluno ON idAvalMatricula=idPMatricula", "*", "idReconfEscola=:idReconfEscola AND idReconfEscola=idTurmaEscola AND idMatEscola=idReconfEscola AND nomeTurma IS NOT NULL AND estadoAluno in ('A', 'Y') AND observacaoF in ('A', 'TR') AND (idAvalAno=idReconfAno OR idAvalAno IS NULL) AND idTurmaAno=idReconfAno AND idTurmaAno=:idTurmaAno AND classeReconfirmacao<".(9+$this->duracaoCurso), [$_SESSION["idEscolaLogada"], $this->idPAno]);

            $this->analizador->alunos = array();
            
            $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "reprovadoGeral", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "reprovadoGeral", "F")."</td>";

            //Pegar alunos aprovados finalistas
            //$this->analizador->alunos = $this->selectArray("alunosmatriculados LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN avaliacaoanualaluno ON idAvalMatricula=idPMatricula", "*", "idReconfEscola=:idReconfEscola AND idReconfEscola=idTurmaEscola AND idMatEscola=idReconfEscola AND nomeTurma IS NOT NULL AND estadoAluno in ('A', 'Y') AND (idAvalAno=idReconfAno OR idAvalAno IS NULL) AND observacaoF in ('A', 'TR') AND idTurmaAno=idReconfAno AND idTurmaAno=:idTurmaAno AND classeReconfirmacao=".(9+$this->duracaoCurso), [$_SESSION["idEscolaLogada"], $this->idPAno]);

            $this->analizador->alunos = array();

            $this->html .="<td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "reprovadoGeral", "TOT")."</td>
              <td style='".$this->border().$this->text_center."'>".$this->analizador->quanto("TOT", "TOT", "TOT", "reprovadoGeral", "F")."</td><tr/></table><div style='".$this->text_center."'>".$this->assinaturaDirigentes("mengi")."</div></body></html>";

          $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Mapa de Aproveitamento Final-".$this->trimestreApartir."-".$this->numAno, "Mapa_de_Aproveitamento_Final-".$this->trimestreApartir."-".$this->idPAno, "A3", "landscape");
        } 


        private function contadorTurma($idPCurso, $classe){
            
            $contador=0;
            foreach ($this->turmas as $turma) {
              if(seComparador($idPCurso, nelson($turma, "idListaCurso")) && seComparador($classe, $turma["classe"])){
                $contador++;
              }
            }
            return completarNumero($contador);
        }
    }



new mapa(__DIR__);
    
    
  
?>
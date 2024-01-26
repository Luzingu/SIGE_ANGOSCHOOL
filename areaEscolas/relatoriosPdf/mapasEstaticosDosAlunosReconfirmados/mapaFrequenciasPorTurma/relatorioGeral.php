<?php
    
    class segundoCiclo{

        function __construct($db){
            $this->db = $db;
            $this->db->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->db->nomeCurso();
            $this->db->numAno();

            $arrayCursos =array();
            foreach($this->db->selectArray("nomecursos", ["idPNomeCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>$this->db->tipoCurso, "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
              $arrayCursos[]=$curso["idPNomeCurso"];
            }

            $this->db->alunosInscritos = $this->db->selectArray("alunosmatriculados", ["dataNascAluno", "sexoAluno", "reconfirmacoes.nomeTurma", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.idMatCurso", "reconfirmacoes.designacaoTurma"], ["reconfirmacoes.idReconfAno"=>$this->db->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idMatCurso"=>array('$in'=>$arrayCursos), "reconfirmacoes.estadoReconfirmacao"=>"A"], ["reconfirmacoes"], "", [], [], $this->db->matchMaeAlunos($this->db->idPAno));

            $this->db->turmas = $this->db->turmasEscola(array(), array(), $this->db->idPAno, $this->db->tipoCurso);        
        }

        public function visualizar(){

            $this->db->periodo="reg";

          $htmlRetorno="";
          $htmlRetorno.="<html style='margin:20px;'>
            <head>
                <title>Mapa de Frequências por Turma</title>
            </head>
            <body>
            <div style='position: absolute;'>
              <div style='margin-top: -10px; width:300px;'>".$this->db->assinaturaDirigentes(7)."</div></div>".$this->db->cabecalho()."<p style='".$this->db->text_center.$this->db->sublinhado.$this->db->maiuscula.$this->db->bolder."'>MAPA DE FREQUÊNCIAS POR TURMA - ".$this->db->numAno."</p>

        <table style='".$this->db->tabela."width:100%;font-size:10pt;'>
        <tr style='".$this->db->corDanger."'><td style='".$this->db->border().$this->db->text_center.$this->db->bolder."' rowspan='2'>Nº</td><td style='".$this->db->border().$this->db->text_center.$this->db->bolder."' rowspan='2'>Curso</td><td style='".$this->db->border().$this->db->text_center.$this->db->bolder."' rowspan='2'>Classe</td><td style='".$this->db->border().$this->db->text_center.$this->db->bolder."' rowspan='2'>Turma</td><td style='".$this->db->border().$this->db->text_center.$this->db->bolder."' colspan='2'>Nº de Alunos</td><td style='".$this->db->border().$this->db->text_center.$this->db->bolder."' rowspan='2'>Período</td></tr>
        <tr style='".$this->db->corDanger."'><td style='".$this->db->border().$this->db->bolder.$this->db->text_center."'>MF</td><td style='".$this->db->border().$this->db->bolder.$this->db->text_center."'>F</td></tr>";

          $i=0;
         foreach ($this->db->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A", "tipoCurso"=>$this->db->tipoCurso], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso) { 

            foreach (listarItensObjecto($curso, "classes") as $classe){
                $this->db->contadorTurma($curso["idPNomeCurso"], $classe["identificador"], "TOT", "TOT");

                foreach ($this->db->listaTurmas as $turma) {
                  $i++;
                   $teresa = $turma["designacaoTurma"];

                   $htmlRetorno.="<tr><td style='".$this->db->border().$this->db->text_center.$this->db->maiuscula."'>".completarNumero($i)."</td><td style='".$this->db->border()."'>".$curso["nomeCurso"];
                  if($curso["areaFormacaoCurso"]!="Geral" && $curso["areaFormacaoCurso"]!=NULL && $curso["areaFormacaoCurso"]!="TOT"){
                   $htmlRetorno.=" (".$curso["areaFormacaoCurso"].")";
                  }

                   $htmlRetorno.="</td><td style='".$this->db->text_center.$this->db->border()."'>".$classe["designacao"]."</td><td style='".$this->db->text_center.$this->db->border()."'>".$teresa."</td><td style='".$this->db->text_center.$this->db->border()."'>".$this->db->contadorAlunos($curso["idPNomeCurso"], $classe["identificador"], $turma["designacaoTurma"], "TOT")."</td><td style='".$this->db->text_center.$this->db->border()."'>".$this->db->contadorAlunos($curso["idPNomeCurso"], $classe["identificador"], $turma["designacaoTurma"], "F")."</td><td style='".$this->db->text_center.$this->db->border()."'>".$this->db->periodoTurma($curso["idPNomeCurso"], $classe["identificador"], $turma["designacaoTurma"])."</td></tr>";
                     
                }
            }
         }
         $htmlRetorno.="<tr style='".$this->db->bolder."background-color:rgba(0,0,0,0.3)'><td style='".$this->db->border().$this->db->text_center."' colspan='4'>TOTAL</td><td style='".$this->db->text_center.$this->db->border()."'>".$this->db->contadorAlunos("TOT", "TOT", "TOT", "TOT")."</td><td style='".$this->db->text_center.$this->db->border()."'>".$this->db->contadorAlunos("TOT", "TOT", "TOT", "F")."</td><td style='".$this->db->text_center.$this->db->border()."'></td></tr></table>
            <p style='".$this->db->maiuscula.$this->db->text_center."'>".$this->db->rodape()."</p><div class='maiuscula'>".$this->db->assinaturaDirigentes("mengi");

            return $htmlRetorno;

        }
       
    }  
?>
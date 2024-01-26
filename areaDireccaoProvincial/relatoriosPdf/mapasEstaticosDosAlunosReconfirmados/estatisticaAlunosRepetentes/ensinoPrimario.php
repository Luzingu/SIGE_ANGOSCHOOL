<?php
    
    class ensinoPrimario{

        function __construct($db){
            $this->db = $db;
            $this->db->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->db->classe=2;
            $this->db->nomeCurso();
            $this->db->numAno();

            $this->db->alunosTrans = $this->db->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN transferencia_alunos ON idTransfMatricula=idPMatricula", "*", "idTransfEscolaOrigem=idMatEscola AND idTransfEscolaOrigem=:idTransfEscolaOrigem AND idTransfAno=:idTransfAno AND classeReconfirmacao>=1 AND classeReconfirmacao<=6", [$_SESSION["idEscolaLogada"], $this->db->idPAno]);

            $this->db->alunosInscritos = $this->db->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula", "*", "idReconfEscola=:idReconfEscola AND classeTurma=classeReconfirmacao AND idReconfEscola=idTurmaEscola AND idMatEscola=idReconfEscola AND nomeTurma IS NOT NULL AND estadoAluno in ('A', 'Y') AND idTurmaAno=idReconfAno AND idTurmaAno=:idTurmaAno AND tipoEntrada=:tipoEntrada AND classeReconfirmacao>=1 AND classeReconfirmacao<=6", [$_SESSION["idEscolaLogada"], $this->db->idPAno, "novaMatricula"]);          
        }

        public function visualizar(){

            $cabecalho[] = array("titulo"=>"14 ANOS OU MENOS", "tituloDb"=>"<=14");
            $cabecalho[] = array("titulo"=>"15 ANOS", "tituloDb"=>"15");
            $cabecalho[] = array("titulo"=>"16 ANOS", "tituloDb"=>"16");
            $cabecalho[] = array("titulo"=>"17 ANOS", "tituloDb"=>"17");
            $cabecalho[] = array("titulo"=>"18 ANOS", "tituloDb"=>"18");
            $cabecalho[] = array("titulo"=>"19 ANOS", "tituloDb"=>"19");
            $cabecalho[] = array("titulo"=>"20 ANOS", "tituloDb"=>"20");
            $cabecalho[] = array("titulo"=>"21 ANOS OU MAIS", "tituloDb"=>">=21");
            $cabecalho[] = array("titulo"=>"TOTAL", "tituloDb"=>"TOT");

            $htmlRetorno ="<div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->db->assinaturaDirigentes("Director")."</div></div>".$this->db->cabecalho()."<p style='".$this->db->text_center.$this->db->maiuscula.$this->db->bolder."'>ALUNOS REPETENTES - ".$this->db->numAno."</p></div>";


                $htmlRetorno.="
                  <table class='tabela' style='width:100%;".$this->db->tabela."'>
                  <tr style='".$this->db->bolder.$this->db->corDanger."'><td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."' colspan='19'>ENSINO PRIMÁRIO</td></tr>

                  <tr style='".$this->db->bolder.$this->db->corDanger."'><td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."' rowspan='2'>TURMAS</td>";
                  foreach($cabecalho as $cab){
                    $htmlRetorno .="<td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."' colspan='2'>".$cab["titulo"]."</td>";
                  }
                  $htmlRetorno .="
                  </tr>
                  <tr style='".$this->db->bolder.$this->db->corDanger."'>";
                foreach($cabecalho as $cab){
                    $htmlRetorno .="<td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."'>MF</td><td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."'>F</td>";    
                }
                $htmlRetorno .="</tr>";

                for($i=1; $i<=6; $i++){
                    $htmlRetorno .="<tr style='".$this->db->bolder."background-color:rgba(0,0,0,0.3)'><td style='".$this->db->maiuscula.$this->db->text_center.$this->db->border()."' colspan='19'>".classeExtensa($i)."</td></tr>";

                    $turmas = $this->db->selectArray("listaturmas", "*", "idListaEscola=:idListaEscola AND idListaAno=:idListaAno AND classe=:classe AND nomeTurma IS NOT NULL", [$_SESSION["idEscolaLogada"], $this->db->idPAno, $i], "nomeTurma ASC");

                    $contadorTurma=0;
                    foreach ($turmas as $turma) {
                        
                        $jipe = $turma->designacaoTurma;
                        if($jipe==$turma->designacaoTurma){
                            $jipe = $i."-".$turma->designacaoTurma;
                        }
                        $contadorTurma++;

                        $htmlRetorno .="<tr><td style='".$this->db->border()."'>".$jipe."</td>";
                        foreach($cabecalho as $cab){
                            $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", $turma->nomeTurma, $i, "TOT");
                            $htmlRetorno .=$this->db->contador($cab["tituloDb"], "F", $turma->nomeTurma, $i, "TOT");
                        }
                        $htmlRetorno .="</tr>";
                    }

                    $htmlRetorno .="<tr><td style='".$this->db->border()."'>TOTAL</td>";
                    foreach($cabecalho as $cab){
                        $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", "TOT", $i, "TOT");
                        $htmlRetorno .=$this->db->contador($cab["tituloDb"], "F", "TOT", $i, "TOT");
                    }
                    $htmlRetorno .="</tr>";
                }

                $htmlRetorno .="<tr style='".$this->db->bolder."'><td style='".$this->db->border()."'>TOTAL GERAL</td>";
                foreach($cabecalho as $cab){
                    $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", "TOT", "TOT", "TOT");
                    $htmlRetorno .=$this->db->contador($cab["tituloDb"], "F", "TOT", "TOT", "TOT");
                }
                $htmlRetorno .="</tr>";
                $htmlRetorno .="</table><br/>";
            
            $htmlRetorno .="
              <table style='width:100%;".$this->db->tabela."'>
              <tr style='".$this->db->bolder.$this->db->corDanger."'><td style='".$this->db->maiuscula.$this->db->text_center.$this->db->border()."' colspan='19'>RESUMO DOS ALUNOS DO ENSINO PRIMÁRIO</td></tr>
              
              <tr style='".$this->db->bolder.$this->db->corDanger."'><td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."' rowspan='2'>TURMAS</td>";
              foreach($cabecalho as $cab){
                $htmlRetorno .="<td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."' colspan='2'>".$cab["titulo"]."</td>";
              }
              $htmlRetorno .="
              </tr>
              <tr style='".$this->db->bolder.$this->db->corDanger."'>";
            foreach($cabecalho as $cab){
                $htmlRetorno .="<td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."'>MF</td><td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."'>F</td>";    
            }
            $htmlRetorno .="</tr>";
            
            for($i=1; $i<=6; $i++){
                $htmlRetorno .="<tr><td style='".$this->db->bolder.$this->db->border().$this->db->maiuscula."'>".classeExtensa($i)."</td>";
                foreach($cabecalho as $cab){
                    $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", "TOT", $i, "TOT").$this->db->contador($cab["tituloDb"], "F", "TOT", $i, "TOT");
                }
                $htmlRetorno .="</tr>";
            }
             $htmlRetorno .="<tr class='limite'><td style='".$this->db->bolder.$this->db->border()."'>TOTAL</td>";
             foreach($cabecalho as $cab){
                $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", "TOT", "TOT", "TOT", "bolder").$this->db->contador($cab["tituloDb"], "F", "TOT", "TOT", "TOT", "bolder");
             }
            $htmlRetorno .="</tr></table>
            <p style='".$this->db->maiuscula.$this->db->text_center."'>".$this->db->rodape()."</p><div class='maiuscula'>".$this->db->assinaturaDirigentes(["Pedagógico", "Administrativo", "Chefe_da_Secretaria", "Secretário_Administrativo", "Secretário_Pedagógico"])."</div>";

            return $htmlRetorno;

        }
       
    }  
?>
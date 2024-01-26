<?php
    
    class segundoCiclo{

        function __construct($db){
            $this->db = $db;
            $this->db->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->db->classe=12;
            $this->db->nomeCurso();
            $this->db->numAno();
            
            $arrayCursos =array();
            foreach($this->db->selectArray("nomecursos", ["idPNomeCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>$this->db->tipoCurso, "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
              $arrayCursos[]=$curso["idPNomeCurso"];
            } 

            $this->db->alunosTrans = $this->db->selectArray("alunosmatriculados", ["dataNascAluno", "sexoAluno", "reconfirmacoes.nomeTurma", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.idMatCurso"], ["reconfirmacoes.idReconfAno"=>$this->db->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idMatCurso"=>array('$in'=>$arrayCursos), "reconfirmacoes.estadoReconfirmacao"=>"T"], ["reconfirmacoes"]);

            $this->db->alunosInscritos = $this->db->selectArray("alunosmatriculados", ["dataNascAluno", "sexoAluno", "reconfirmacoes.nomeTurma", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.idMatCurso"], ["reconfirmacoes.idReconfAno"=>$this->db->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idMatCurso"=>array('$in'=>$arrayCursos), "reconfirmacoes.estadoReconfirmacao"=>"A"], ["reconfirmacoes"], "", [], [], $this->db->matchMaeAlunos($this->db->idPAno));
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

            $htmlRetorno ="<div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->db->assinaturaDirigentes(7)."</div></div>".$this->db->cabecalho()."<p style='".$this->db->text_center.$this->db->maiuscula.$this->db->bolder."'>RESUMO DAS MATRÍCULAS - ".$this->db->numAno."</p></div>";


            foreach ($this->db->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A", "tipoCurso"=>$this->db->tipoCurso], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){

                $htmlRetorno.="
                  <table class='tabela' style='width:100%;".$this->db->tabela."'>
                  <tr style='".$this->db->bolder.$this->db->corDanger."'><td style='".$this->db->bolder.$this->db->maiuscula.$this->db->text_center.$this->db->border()."' colspan='19'>MATRÍCULA INICIAL DO CURSO DE ".$curso["nomeCurso"];

                if($curso["areaFormacaoCurso"]!="Geral" && $curso["areaFormacaoCurso"]!=NULL && $curso["areaFormacaoCurso"]!="TOT"){
                  $htmlRetorno .=" (".$curso["areaFormacaoCurso"].")";
                }

                  $htmlRetorno .="</td></tr>
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

                foreach(listarItensObjecto($curso, "classes") as $classe){
                    $htmlRetorno .="<tr style='".$this->db->bolder."background-color:rgba(0,0,0,0.3)'><td style='".$this->db->maiuscula.$this->db->text_center.$this->db->border()."' colspan='19'>".$classe["designacao"]."</td></tr>";

                    $contadorTurma=0; 
                    foreach ($this->db->turmasEscola([intval($curso["idPNomeCurso"])], [intval($classe["identificador"])], $this->db->idPAno) as $turma) {
                        
                        $jipe = $turma["designacaoTurma"];
                        if($jipe==$turma["designacaoTurma"]){
                            $jipe = $curso["abrevCurso"]."-".$classe["identificador"]."-".$turma["designacaoTurma"];
                        }
                        $contadorTurma++;

                            $htmlRetorno .="<tr><td style='".$this->db->border()."'>".$jipe."</td>";
                            foreach($cabecalho as $cab){
                                $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", $turma["nomeTurma"], $classe["identificador"], $curso["idPNomeCurso"]);
                                $htmlRetorno .=$this->db->contador($cab["tituloDb"], "F", $turma["nomeTurma"], $classe["identificador"], $curso["idPNomeCurso"]);
                            }
                            $htmlRetorno .="</tr>";
                    }

                    $htmlRetorno .="<tr><td style='".$this->db->border()."'>TOTAL</td>";
                    foreach($cabecalho as $cab){
                        $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", "TOT", $classe["identificador"], $curso["idPNomeCurso"]);
                        $htmlRetorno .=$this->db->contador($cab["tituloDb"], "F", "TOT", $classe["identificador"], $curso["idPNomeCurso"]);
                    }
                    $htmlRetorno .="</tr>";
                }

                $htmlRetorno .="<tr style='".$this->db->bolder."'><td style='".$this->db->border()."'>TOTAL GERAL</td>";
                foreach($cabecalho as $cab){
                    $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", "TOT", "TOT", $curso["idPNomeCurso"]);
                    $htmlRetorno .=$this->db->contador($cab["tituloDb"], "F", "TOT", "TOT", $curso["idPNomeCurso"]);
                }
                $htmlRetorno .="</tr>";
                $htmlRetorno .="</table><br/>";
            }
            $label="RESUMO DE MATRÍCULA INICIAL DO II CICLO DO ENSINO SECUNDÁRIO GERAL";

            if($this->db->tipoCurso=="pedagogico"){
                $label="RESUMO DE MATRÍCULA INICIAL DO II CICLO DO ENSINO SECUNDÁRIO PEDAGÓGICO";
            }else if($this->db->tipoCurso=="tecnico"){
                 $label="RESUMO DE MATRÍCULA INICIAL DO II CICLO DO ENSINO SECUNDÁRIO TÉCNICO";
            }else if($this->db->tipoCurso=="geral"){
                 $label="RESUMO DE MATRÍCULA INICIAL DO II CICLO DO ENSINO SECUNDÁRIO GERAL";
            }
            $htmlRetorno .="
              <table style='width:100%;".$this->db->tabela."'>
              <tr style='".$this->db->bolder.$this->db->corDanger."'><td style='".$this->db->maiuscula.$this->db->text_center.$this->db->border()."' colspan='19'>".$label."</td></tr>
              
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
            
            foreach ($this->db->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A", "tipoCurso"=>$this->db->tipoCurso], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){

                $htmlRetorno .="<tr><td style='".$this->db->bolder.$this->db->border().$this->db->maiuscula."'>".$curso["abrevCurso"]."</td>";
                foreach($cabecalho as $cab){
                    $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", "TOT", "TOT", $curso["idPNomeCurso"]).$this->db->contador($cab["tituloDb"], "F", "TOT", "TOT", $curso["idPNomeCurso"]);
                } 
                $htmlRetorno .="</tr>";
            }
             $htmlRetorno .="<tr class='limite'><td style='".$this->db->bolder.$this->db->border()."'>TOTAL</td>";
             foreach($cabecalho as $cab){
                $htmlRetorno .=$this->db->contador($cab["tituloDb"], "TOT", "TOT", "TOT", "TOT", "bolder").$this->db->contador($cab["tituloDb"], "F", "TOT", "TOT", "TOT", "bolder");
             }
            $htmlRetorno .="</tr></table>
            <p style='".$this->db->maiuscula.$this->db->text_center."'>".$this->db->rodape()."</p><div class='maiuscula'>".$this->db->assinaturaDirigentes("mengi")."</div>";

            return $htmlRetorno;

        }
       
    }  
?>
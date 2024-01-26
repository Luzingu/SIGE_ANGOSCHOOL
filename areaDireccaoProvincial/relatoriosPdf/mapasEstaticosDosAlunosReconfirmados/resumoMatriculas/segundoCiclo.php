<?php
    
    class segundoCiclo{

        function __construct($db){
            $this->db = $db;
            $this->db->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->db->classe=12;
            $this->db->nomeCurso();
            $this->db->numAno();

            $this->db->alunosTrans = array();
            $this->db->alunosInscritos = array();
            $this->escolasComIICiclo = array();
            $this->listaCursos=array();

            foreach($this->db->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPEscola", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->db->sobreUsuarioLogado, "provincia"), $this->db->privacidade, "A"], "nomeEscola ASC") as $a){

                $this->db->alunosTrans = array_merge($this->db->alunosTrans, $this->db->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN transferencia_alunos ON idTransfMatricula=idPMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso", "*", "idTransfEscolaOrigem=idMatEscola AND idTransfEscolaOrigem=:idTransfEscolaOrigem AND idTransfAno=:idTransfAno AND tipoCurso=:tipoCurso AND idReconfAno=idTransfAno", [$a->idPEscola, $this->db->idPAno, $this->db->tipoCurso]));
            }
            foreach($this->db->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPEscola", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->db->sobreUsuarioLogado, "provincia"), $this->db->privacidade, "A"], "nomeEscola ASC") as $a){

                $array = $this->db->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso", "*", "idReconfEscola=:idReconfEscola AND classeTurma=classeReconfirmacao AND idReconfEscola=idTurmaEscola AND idMatEscola=idReconfEscola AND nomeTurma IS NOT NULL AND estadoAluno in ('A', 'Y') AND idTurmaAno=idReconfAno AND idTurmaAno=:idTurmaAno AND (tipoEntrada=:tipoEntrada OR tipoEntrada IS NULL) AND tipoCurso=:tipoCurso", [$a->idPEscola, $this->db->idPAno, "novaMatricula", $this->db->tipoCurso]);
                $this->db->alunosInscritos =array_merge($this->db->alunosInscritos, $array);
                if(count($array)>0){
                    $this->escolasComIICiclo[]=$a->idPEscola;
                }
            }
            foreach($this->db->selectArray("nomecursos LEFT JOIN cursos ON idPNomeCurso=idFNomeCurso LEFT JOIN escolas ON idPEscola=idCursoEscola LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPNomeCurso", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola AND estadoCurso=:estadoCurso AND tipoCurso=:tipoCurso", ["escola", valorArray($this->db->sobreUsuarioLogado, "provincia"), $this->db->privacidade, "A", "A", $this->db->tipoCurso]) as $a){
                $this->listaCursos = array_merge($this->listaCursos, $this->db->selectArray("nomecursos", "*", "idPNomeCurso=:idPNomeCurso", [$a->idPNomeCurso]));
            }        
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

            $label="RESUMO DE MATRÍCULA INICIAL DO II CICLO DO ENSINO SECUNDÁRIO GERAL";

            if($this->db->tipoCurso=="pedagogico"){
                $label="RESUMO DE MATRÍCULA INICIAL DO II CICLO DO ENSINO SECUNDÁRIO PEDAGÓGICO";
            }else if($this->db->tipoCurso=="tecnico"){
                 $label="RESUMO DE MATRÍCULA INICIAL DO II CICLO DO ENSINO SECUNDÁRIO TÉCNICO";
            }else if($this->db->tipoCurso=="geral"){
                 $label="RESUMO DE MATRÍCULA INICIAL DO II CICLO DO ENSINO SECUNDÁRIO GERAL";
            }

            $htmlRetorno ="<div><div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->db->assinaturaDirigentes("DP")."</div></div>".$this->db->cabecalho()."<p style='".$this->db->text_center.$this->db->maiuscula.$this->db->bolder."'>RESUMO DAS MATRÍCULAS - ".$this->db->numAno."</p>

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
            
            for($i=10; $i<=(9+$this->db->duracaoCurso); $i++){
                $htmlRetorno .="<tr><td style='".$this->db->bolder.$this->db->border().$this->db->maiuscula."'>".classeExtensa($i, $this->db->sePorSemestre)."</td>";
                foreach($cabecalho as $cab){
                    $htmlRetorno .=$this->db->contador("TOT", $cab["tituloDb"], "TOT", "TOT", $i, "TOT").$this->db->contador("TOT", $cab["tituloDb"], "F", "TOT", $i, "TOT");
                }
                $htmlRetorno .="</tr>";
            }
             $htmlRetorno .="<tr class='limite'><td style='".$this->db->bolder.$this->db->border()."'>TOTAL</td>";
            foreach($cabecalho as $cab){
                $htmlRetorno .=$this->db->contador("TOT", $cab["tituloDb"], "TOT", "TOT", "TOT", "TOT", "bolder").$this->db->contador("TOT", $cab["tituloDb"], "F", "TOT", "TOT", "TOT", "bolder");
            }
            $htmlRetorno .="</tr></table>
            <p style='".$this->db->maiuscula.$this->db->text_center."'>".$this->db->rodape()."</p>".$this->db->assinaturaDirigentes(["CDEPE"])."</div>";


            $htmlRetorno .="<div style='page-break-before: always;'><div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->db->assinaturaDirigentes("DP")."</div></div>".$this->db->cabecalho()."<p style='".$this->db->text_center.$this->db->maiuscula.$this->db->bolder."'>RESUMO DAS MATRÍCULAS - ".$this->db->numAno."</p>

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
            
            foreach($this->listaCursos as $c){

                $htmlRetorno .="<tr><td style='".$this->db->bolder.$this->db->border().$this->db->maiuscula."'>".$c->abrevCurso."</td>";
                foreach($cabecalho as $cab){
                    $htmlRetorno .=$this->db->contador("TOT", $cab["tituloDb"], "TOT", "TOT", "TOT", $c->idPNomeCurso).$this->db->contador("TOT", $cab["tituloDb"], "F", "TOT", "TOT", $c->idPNomeCurso);
                }
                $htmlRetorno .="</tr>";
            }
             $htmlRetorno .="<tr class='limite'><td style='".$this->db->bolder.$this->db->border()."'>TOTAL</td>";
            foreach($cabecalho as $cab){
                $htmlRetorno .=$this->db->contador("TOT", $cab["tituloDb"], "TOT", "TOT", "TOT", "TOT", "bolder").$this->db->contador("TOT", $cab["tituloDb"], "F", "TOT", "TOT", "TOT", "bolder");
            }
            $htmlRetorno .="</tr></table>
            <p style='".$this->db->maiuscula.$this->db->text_center."'>".$this->db->rodape()."</p>".$this->db->assinaturaDirigentes(["CDEPE"])."</div>";


            $htmlRetorno .="<div style='page-break-before: always;'><div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->db->assinaturaDirigentes("DP")."</div></div>".$this->db->cabecalho()."<br/><p style='".$this->db->text_center.$this->db->maiuscula.$this->db->bolder."'>RESUMO DAS MATRICULAS NO ENSINO ".$this->db->labelPrivacidade." - ".$this->db->numAno."</p></div>


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
            
            foreach($this->escolasComIICiclo as $escola){
                $htmlRetorno .="<tr><td style='".$this->db->bolder.$this->db->border().$this->db->maiuscula."'>".$this->db->selectUmElemento("escolas", "abrevNomeEscola", "idPEscola=:idPEscola", [$escola])."</td>";
                foreach($cabecalho as $cab){
                    $htmlRetorno .=$this->db->contador($escola, $cab["tituloDb"], "TOT", "TOT", "TOT", "TOT").$this->db->contador($escola, $cab["tituloDb"], "F", "TOT", "TOT", "TOT");
                }
                $htmlRetorno .="</tr>";
            }
             $htmlRetorno .="<tr class='limite'><td style='".$this->db->bolder.$this->db->border()."'>TOTAL</td>";
            foreach($cabecalho as $cab){
                $htmlRetorno .=$this->db->contador("TOT", $cab["tituloDb"], "TOT", "TOT", "TOT", "TOT", "bolder").$this->db->contador("TOT", $cab["tituloDb"], "F", "TOT", "TOT", "TOT", "bolder");
            }
            $htmlRetorno .="</tr></table>
            <p style='".$this->db->maiuscula.$this->db->text_center."'>".$this->db->rodape()."</p>".$this->db->assinaturaDirigentes(["CDEPE"])."</div>";

            return $htmlRetorno;

        }
       
    }  
?>
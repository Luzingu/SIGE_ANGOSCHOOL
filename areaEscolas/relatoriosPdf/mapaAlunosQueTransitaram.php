<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class mapa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa Alunos que Transitaram"); 

            if(isset($_GET["idPAno"])){
                $this->idPAno = $_GET["idPAno"];
            }else{
                $this->idPAno = $this->idAnoActual;
            }

            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->nomeCurso();
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", "pautaGeral1", array($this->classe, $this->idPCurso), "")){
                $this->exbirMapa();
            }else{
              $this->negarAcesso();
            }
        }

        private function exbirMapa(){ 

            $this->html .="
            <html>
            <head>
              <title>Resumo de Alunos que transitaram com deficiência</title>
            </head>
            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;"."'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

              $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>RESUMO DOS ALUNOS QUE TRANSITARAM COM DEFICIÊNCIA - ".$this->numAno."</p>";

            if($this->tipoCurso=="pedagogico"){
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
            }else if($this->tipoCurso=="tecnico"){
                $this->html .="<p style='".$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }else{
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }

            $this->html .="<p style='".$this->maiuscula."'>CLASSE: <strong>".
            classeExtensa($this, $this->idPCurso, $this->classe)."</strong></p>";

            $this->html .="<table style='".$this->tabela."width:100%;'>
              <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."width:30px;'>Nº</td><td style='".$this->border().$this->bolder.$this->text_center."'>NOME COMPLETO</td><td style='".$this->border().$this->bolder.$this->text_center."width:50px;'>SEXO</td><td style='".$this->border().$this->text_center.$this->bolder."width:70px;'>TURMA</td><td style='".$this->border().$this->text_center.$this->bolder."'>DISCIPLINA</td><td style='".$this->border().$this->text_center.$this->bolder."width:40px;'>MF</td></tr>";

            

            $condicaoAlunos = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.observacaoF"=>"TR", "reconfirmacoes.classeReconfirmacao"=>$this->classe, "pautas.classePauta"=>$this->classe, "pautas.mf"=>['$lt'=>10], "pautas.idPautaCurso"=>$this->idPCurso, "escola.idMatCurso"=>$this->idPCurso];
            
            if($this->tipoCurso=="tecnico"){
                $disciplinas = array();
                foreach($this->disciplinas([intval($this->idPCurso)], [intval($this->classe)], "", "", "C", "", "A", [58, 59, 60, 231, 232, 233], ["idPNomeDisciplina"]) as $d){
                    $disciplinas[]=intval($d["idPNomeDisciplina"]);
                }
                $condicaoAlunos["pautas.idPautaDisciplina"]=['$in'=>$disciplinas];
            }

            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "pautas.classePauta", "grupo", "reconfirmacoes.designacaoTurma", "pautas.idPautaDisciplina", "sexoAluno", "idPMatricula", "pautas.mf"], $condicaoAlunos, ["reconfirmacoes", "pautas"], "", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idPAno, $this->idPCurso, $this->classe));

            $alunos = $this->anexarTabela2($alunos, "nomedisciplinas", "pautas", "idPNomeDisciplina", "idPautaDisciplina");

            $i=0;
            foreach ($alunos as $aluno) {
              $i++;

              $this->html .="<tr style=''><td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."padding:2px;'>".$aluno["nomeAluno"]."</td><td style='".$this->border().$this->text_center."padding:2px;'>".$aluno["sexoAluno"]."</td><td style='".$this->border().$this->text_center."padding:2px;'>".$aluno["reconfirmacoes"]["designacaoTurma"]."</td><td style='".$this->border()."padding:2px;'>".$aluno["nomeDisciplina"]."</td><td style='".$this->border().$this->text_center.$this->vermelha."padding:2px;'>".$aluno["pautas"]["mf"]."</td>";

              $this->html.="</tr>"; 
            }
            $this->html .="</table>";

              $this->html .="
               <p style='".$this->text_center.$this->maiuscula."'>".$this->rodape()."</p>
           <div style='".$this->text_center."'>".$this->assinaturaDirigentes(8)."</div>

           </body></html>";
            
           $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Resumo de Alunos que transitaram com deficiência-".$this->nomeCursoAbr."-".$this->numAno, "Resumo_de_Alunos_que_transitaram_com_deficiencia-".$this->idPCurso."-".$this->idPAno);
        } 

        function disciplinasQueOLevaramARecurso($aluno, $idPMatricula, $classeReconfirmacao){
            $retorno=array();

            foreach(listarItensObjecto($aluno, "pautas", ["classePauta=".$classeReconfirmacao, "idPautaCurso=".$this->idPCurso]) as $nota){

                if($this->tipoCurso=="tecnico"){
                    if(nelson($nota, "mf")>=7 && nelson($nota, "mf")<10){
                        $retorno[]=$nota;
                    }
                }else{
                    if(nelson($nota, "mf")<10 && nelson($nota, "recurso")<10){
                        $retorno[]=$nota;
                    }
                }   
            }
            return $this->anexarTabela($retorno, "nomedisciplinas", "idPNomeDisciplina", "idPautaDisciplina");
        }       
    }



new mapa(__DIR__);
    
    
  
?>
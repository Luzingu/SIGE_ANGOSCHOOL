<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class mapa extends funcoesAuxiliares{
        
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa Alunos ao Recurso"); 

            if(isset($_GET["idPAno"])){
                $this->idPAno = $_GET["idPAno"];
            }else{
                $this->idPAno = $this->idAnoActual;
            }
            


            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->nomeCurso();
            $this->numAno();
            $this->classe=isset($_GET["classe"])?$_GET["classe"]:null;
            $this->resultPauta="definitivo";

            if($this->verificacaoAcesso->verificarAcesso("", "pautaRecurso", array($this->classe, $this->idPCurso), "")){

                $this->exbirMapa();
            }else{
              $this->negarAcesso();
            }
        }

        private function exbirMapa(){
            $notaMedia=10;
            if($this->classe<=9){
                $notaMedia=5;
            }
            $this->html .="
            <html>
            <head>
              <title>Resumo de Alunos a serem submetidos a exame de recurso </title>
            </head>
            <div style='position: absolute;'>".$this->fundoDocumento("../../", "horizontal")."<div style='margin-top: 0px; width:300px;"."'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

              $this->html .="<p style='".$this->text_center.$this->bolder.$this->sublinhado."'>ALUNOS SUBMETIDOS AOS EXAMES DE RECURSO - ".$this->numAno."</p>";

            if($this->tipoCurso=="pedagogico"){
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
            }else if($this->tipoCurso=="tecnico"){
                $this->html .="<p style='".$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                <p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }else if($this->classe>=10){
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
            }

            $this->html .="<p style='".$this->maiuscula."'>CLASSE: <strong>".
            classeExtensa($this, $this->idPCurso, $this->classe)."</strong></p>";

            $this->html .="<table style='".$this->tabela."width:100%;'>
              <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."'>Nome Completo</td><td style='".$this->border().$this->text_center.$this->bolder."width:90px;'>Turma</td><td style='".$this->border().$this->text_center.$this->bolder."'>Disciplina</td>";
              if($this->tipoCurso=="tecnico"){
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."width:70px;'>CFD</td>";
              }else{
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."width:70px;'>MF</td>";
              }
              $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."width:90px;'>RECURSO</td></tr>";

              $condicaoAdicional = [];

              $alunos = $this->selectCondClasseCurso("array", "alunosmatriculados", ["nomeAluno", "pautas.classePauta", "pautas.seFoiAoRecurso", "pautas.cf", "grupo", "pautas.mf", "pautas.recurso", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma", "pautas.idPautaDisciplina", "reconfirmacoes.observacaoF", "sexoAluno", "idPMatricula", "pautas.mf", "pautas.cf"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.seAlunoFoiAoRecurso"=>"A", "pautas.seFoiAoRecurso"=>"A", "reconfirmacoes.classeReconfirmacao"=>$this->classe, "pautas.classePauta"=>$this->classe], $this->classe, ["pautas.idPautaCurso"=>$this->idPCurso, "escola.idMatCurso"=>$this->idPCurso], ["reconfirmacoes", "pautas"], "", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idPAno, $this->idPCurso, $this->classe));

              $alunos = $this->anexarTabela2($alunos, "nomedisciplinas", "pautas", "idPNomeDisciplina", "idPautaDisciplina");

            $i=0;
            foreach ($alunos as $a) {
                if($this->tipoCurso=="tecnico" && $this->campoAvaliar=="cfd"){
                    $classificacao = nelson($a, "cf", "pautas");
                }else{
                    $classificacao = nelson($a, "mf", "pautas");
                }
                
                $this->html .="<tr><td style='".$this->border()."padding:2px;'>".$a["nomeAluno"]."</td><td style='".$this->border().$this->text_center."'>".$a["reconfirmacoes"]["designacaoTurma"]."</td><td style='".$this->border()."'>".$a["nomeDisciplina"]."</td>".$this->tratarVermelha($classificacao, "", $notaMedia).$this->tratarVermelha(nelson($a, "recurso", "pautas"), "", $notaMedia)."</tr>"; 
            }
            $this->html .="</table>";

              $this->html .="<p style='".$this->maiuscula."'>".$this->rodape()."</p>
              
           <div style='".$this->text_center."'>".$this->assinaturaDirigentes(8)."</div>

           </body></html>";

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Resumo de Alunos a serem submetidos a exame de recurso-".$this->nomeCursoAbr."-".$this->numAno, "Resumo_de_Alunos_a_serem_submetidos_a_exame_de_recurso-".$this->idPCurso."-".$this->idPAno, "A4", "landscape");

        } 
    }



new mapa(__DIR__);
?>
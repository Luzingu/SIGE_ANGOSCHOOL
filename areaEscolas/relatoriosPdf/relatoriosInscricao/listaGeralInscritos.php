<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            
            parent::__construct("Rel-Lista de Inscricos");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", ["inscricaoAlunosInscritos"], [], "")){
                $this->listaInscritos();              
            }else{
                 $this->negarAcesso();
            }
            
        }

         private function listaInscritos(){
            $condicaoCurso =" ";

            $this->conDb("inscricao");
            $totRegular=0;
           $alunos = $this->selectArray("alunos", [], ["idAlunoAno"=>$this->idPAno, "idAlunoEscola"=>$_SESSION["idEscolaLogada"], "inscricao.idInscricaoCurso"=>$this->idPCurso], ["inscricao"], "", [], array("nomeAluno"=>1));

           $totF=0;
            foreach ($alunos as $aluno) {
                if($aluno["sexoAluno"]=="F"){
                    $totF++;
                }
                if($aluno["inscricao"]["periodoInscricao"]=="reg"){
                    $totRegular++;
                }
            }
            $this->conDb();
            $this->html .="<html>
            <head>
                <title>Lista dos Inscritos</title>
            </head>
            <body>".$this->fundoDocumento("../../../")."
            <div style='position: absolute;'><div style='margin-top: -30px; width:250px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho()."
             
            <p style='".$this->text_center.$this->bolder.$this->sublinhado."'>LISTA DOS ALUNOS INSCRITOS - ".$this->numAno."</p>"; 
            
            $this->conDb();

            $cur = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso]);

            if(valorArray($cur, "tipoCurso")=="pedagogico"){
                $this->html .="<p style='".$this->miniParagrafo."'>Curso: <strong>".valorArray($cur, "areaFormacaoCurso")."</strong></p>
                <p style='".$this->miniParagrafo."'>Opção: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            }else if(valorArray($cur, "tipoCurso")=="tecnico"){
                $this->html .="<p style='".$this->miniParagrafo."'>Área de Formação: <strong>".valorArray($cur, "areaFormacaoCurso")."</strong></p>
                <p style='".$this->miniParagrafo."'>Curso: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            }else{
                $this->html .="<p style='".$this->miniParagrafo."'>Curso: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            }

            $this->html .="<p style='".$this->miniParagrafo."'>Total: <strong> ".completarNumero(count($alunos))."</strong> / F: <strong>".completarNumero($totF)."</strong></p>
            
            <p>Regular: <strong> ".completarNumero($totRegular)."</strong></p>
            
            <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>
                        <td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."'>N.º</td><td style='".$this->bolder.$this->bolder.$this->border()."'>Nome Completo</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."'>Sexo</td><td style='".$this->text_center.$this->bolder.$this->border()."'>Idade</td>
                           <td style='".$this->text_center.$this->bolder.$this->border()."'>Período</td>
                    </tr>
                ";

            $n=0;
            $this->conDb("inscricao");
            foreach ($alunos as $aluno) {
                $n++;
                $periodo = $aluno["inscricao"]["periodoInscricao"];
                if($periodo=="auto"){
                    $periodosCurso = $this->selectUmElemento("gestorvagas", "periodosCurso", ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idAnoActual, "idGestCurso"=>$this->idPCurso]);
                    if($periodosCurso=="reg"){
                        $periodo="Regular";
                    }else if($periodosCurso=="pos"){
                        $periodo="Pós-Laboral";
                    }
                }else if($periodo=="reg"){
                    $periodo="Regular";
                }else{
                    $periodo="Pós-Laboral";
                }
                $this->html .="<tr><td style='".$this->text_center.$this->border()."'>".completarNumero($n)."</td><td style='".$this->border()."padding-left:5px;'>".$aluno["nomeAluno"]."</td><td style='".$this->text_center.$this->border()."'>".generoExtenso($aluno["sexoAluno"])."</td><td style='".$this->text_center.$this->border()."'>".calcularIdade(explode("-", $this->dataSistema)[0], $aluno["dataNascAluno"])." Anos</td>
                <td style='".$this->text_center.$this->border()."'>".$periodo."</td></tr>";
            } 
            
            $this->conDb();

            $this->html .="</table>
             <p>".$this->rodape()."</p>
            <div style='width:50%;'>".$this->assinaturaDirigentes("mengi")."</div>

            <div style='width:50%; margin-left:50%; margin-top:-100px;'>
                <p style='".$this->text_center."'>A Comissão</p>
                <p style='".$this->text_center."'>______________________________</p>
                <p style='".$this->text_center."'>______________________________</p>
                <p style='".$this->text_center."'>______________________________</p>
                <p style='".$this->text_center."'>______________________________</p>
                <p style='".$this->text_center."'>______________________________</p>
            </div>";

            $this->exibir("", "Lista dos Inscricos-".valorArray($cur, "abrevCurso")."-".$this->numAnoActual);
        }
    }

new lista(__DIR__);
    
    
  
?>
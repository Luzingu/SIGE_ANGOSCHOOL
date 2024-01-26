<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->grupo = isset($_GET["grupo"])?$_GET["grupo"]:0;
            parent::__construct("Rel-Lista de Grupos");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", ["divisaoGrupos"], [], "")){
                $this->listaInscritos();              
            }else{
                 $this->negarAcesso();
            }
            
        }

         private function listaInscritos(){
            $condicaoCurso =" ";

            $this->conDb("inscricao");
           $alunos = $this->selectArray("alunos", ["nomeAluno", "codigoAluno", "dataNascAluno", "sexoAluno"], ["idAlunoAno"=>$this->idPAno, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "grupo.idGrupoCurso"=>$this->idPCurso, "grupo.grupoNumero"=>$this->grupo], ["grupo"],"", [], array("nomeAluno"=>1));


           $totF=0;
            foreach ($alunos as $aluno) {
                if($aluno["sexoAluno"]=="F"){
                    $totF++;
                }
            }
            $this->conDb();
            
            $this->html .="<html>
            <head>
                <title>Lista dos Inscritos</title>
            </head>
            <body>".$this->fundoDocumento("../../../")."
            <div style='position: absolute;'><div style='margin-top: -30px; width:250px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho()."             
            <p style='".$this->text_center.$this->bolder.$this->sublinhado."'>LISTA DE EXAME DE ADMISSÃO - ".$this->numAno."</p>"; 
            
            $this->conDb();

            $cur = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso]);

            if(valorArray($cur, "tipoCurso")=="pedagogico"){
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".valorArray($cur, "areaFormacaoCurso")."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>OPÇÃO: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            }else if(valorArray($cur, "tipoCurso")=="tecnico"){
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>ÁREA DE FORMAÇÃO: <strong>".valorArray($cur, "areaFormacaoCurso")."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            }else{
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            } 

            $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>GRUPO N.º <strong>".completarNumero($this->grupo)."</strong></p>

            <p style='".$this->maiuscula."'>HORA DO EXAME:________________ Á ______________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SALA N.º:__________</p>

            <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>
                        <td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."'>N.º</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."'>Nome Completo</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."'>Código do Aluno</td><td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."'>Sexo</td><td style='".$this->text_center.$this->bolder.$this->border()."'>Idade</td>
                    </tr>
                ";
            $n=0;
            foreach ($alunos as $aluno) {
                $n++;
                $this->html .="<tr><td style='".$this->text_center.$this->border()."'>".completarNumero($n)."</td><td style='".$this->border()."padding-left:5px;'>".$aluno["nomeAluno"]."</td><td style='".$this->border().$this->text_center."padding-left:5px;'>".$aluno["codigoAluno"]."</td><td style='".$this->text_center.$this->border()."'>".generoExtenso($aluno["sexoAluno"])."</td><td style='".$this->text_center.$this->border()."'>".calcularIdade(explode("-", $this->dataSistema)[0], $aluno["dataNascAluno"])." Anos</td></tr>";
            } 

            $this->html .="</table>
            <p style='".$this->maiuscula."'>".$this->rodape()."</p>
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
<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class listaTurmas extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Colegas de Turmas");

            $vetor = $this->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso LEFT JOIN turmas ON idTurmaMatricula=idPMatricula", "*", "idPMatricula=:idPMatricula AND idMatEscola=:idMatEscola AND estadoAluno in ('A', 'Y') AND (idTurmaAno=".$this->idAnoActual." OR idTurmaAno IS NULL) AND (idTurmaEscola=".$_SESSION["idEscolaLogada"]." OR idTurmaEscola IS NULL)", [$_SESSION["idUsuarioLogado"], $_SESSION["idEscolaLogada"]]);

            $this->idPAno = $this->idAnoActual;
            $this->idPCurso = valorArray($vetor, "idMatCurso");
            $this->classe = valorArray($vetor, "classeActualAluno");
            $this->turma = valorArray($vetor, "nomeTurma");
                      
            $this->nomeCurso();
            $this->numAno();
            if($this->verificacaoAcesso->verificarAcesso(1, [], [], "")){
                $this->listaTurmasLiceu();
            }else{
                $this->negarAcesso();
            }
        }

         private function listaTurmasLiceu(){
            
                $cabecalho[]= array('titulo'=>"Nº", "tituloDb"=>"num", "css"=>$this->text_center."width:30px;");
                $cabecalho[]= array('titulo'=>"Nome Completo", "tituloDb"=>"nomeAluno", "css"=>"width:240px;");
                $cabecalho[]= array('titulo'=>"N.º de Telefone", "tituloDb"=>"telefoneAluno", "css"=>$this->text_center."width:80px;");
                $cabecalho[]= array('titulo'=>"E-mail", "tituloDb"=>"emailAluno", "css"=>"");

                $cabecalho[]= array('titulo'=>"S", "tituloDb"=>"sexoAluno", "css"=>$this->text_center."width:50px;");
            
                $alunos = $this->selectCondClasseCurso("array", "alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula LEFT JOIN escolas ON idPEscola=idReconfEscola", "*", "idReconfAno=:idReconfAno AND idTurmaAno=idReconfAno AND idReconfEscola=:idReconfEscola AND idTurmaEscola=idReconfEscola AND idReconfEscola=idMatEscola AND (classeReconfirmacao=".$this->classe." OR classeCadeirante=".$this->classe.") AND classeTurma=".$this->classe." AND nomeTurma=:nomeTurma", [$this->idPAno, $_SESSION["idEscolaLogada"], $this->turma, $this->idPCurso], $this->classe, " AND idMatCurso=:idMatCurso", "nomeAluno ASC");

             $sobreTurma = $this->selectCondClasseCurso("array", "listaturmas LEFT JOIN entidadesprimaria ON idCoordenadorTurma=idPEntidade", "*", "classe=:classe AND nomeTurma=:nomeTurma AND idListaAno=:idListaAno AND idListaEscola=:idListaEscola", [$this->classe, $this->turma, $this->idPAno, $_SESSION["idEscolaLogada"], $this->idPCurso], $this->classe, " AND idListaCurso=:idListaCurso");
             
             
            $totF=0;
            foreach ($alunos as $aluno) {
                if($aluno->sexoAluno=="F"){
                    $totF++;
                }
            }

            $this->html .="<html style='margin-left:0px; margin-right:0px;'>
            <head>
                <title>Colegas</title>
                <style>
                  table tr td{
                      padding:3px;
                  }
                  table, p{
                    font-size:12pt;
                  }
                </style>
            </head>
            <body style='margin-left:20px; margin-right:20px;'>".$this->fundoDocumento("../../../").$this->cabecalhoParaAluno(); 
             
            $top=150;
            if($this->classe>=10){

                $top=190;
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $top=190;
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }  
            }            

            $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CLASSE: <strong>".$this->classeExt." / ".$this->nomeTurma()."</strong></p>
            <p style='".$this->maiuscula.$this->miniParagrafo."'>PERÍODO: <strong>".valorArray($sobreTurma, "periodoT")."</strong> / SALA N.º: <strong> ".completarNumero(valorArray($sobreTurma, "numeroSalaTurma"))."</strong></p>
            <p style='".$this->maiuscula."'>TOTAL: <strong> ".completarNumero(count($alunos))."</strong> / F: <strong>".completarNumero($totF)."</strong></p>";
            
            $per="";
            if($this->periodoTurma=="reg"){
                $per="Regular";
            }else {
                $per="Pós-Laboral";
            }
            

           
            
            $this->html .="<div>
            <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>";
                    foreach ($cabecalho as $cab) {
                        $this->html .="<td style='".$this->text_center.$this->bolder.$this->bolder.$this->border()."'>".$cab["titulo"]."</td>";
                    }
                        
                    $this->html .="</tr>
                ";

            $n=0;
            foreach ($alunos as $aluno) {
                $n++;
                $seCadeirante = "";
                if($aluno->classeCadeirante!="" && $aluno->classeCadeirante!=NULL){
                    $seCadeirante=" (Cadeirante)";
                }

                $this->html .="<tr>";
                foreach ($cabecalho as $cab) {
                    $ok = $cab["tituloDb"];

                    if($ok=="num"){
                       $valor=$n;
                    }else if($ok=="nomeAluno"){
                        $valor = $aluno->nomeAluno.$seCadeirante;
                    }else if($ok=="numeroInterno"){
                        $valor = $aluno->numeroInterno."@".$aluno->endereco;
                    }else if($ok=="lingOpcao"){
                        $valor = $this->retornarDiscLinguaOpcao($aluno->idGestLinguaEspecialidade);
                    }else if($ok=="discOpcao"){
                        $valor = $this->retornarDiscLinguaOpcao($aluno->idGestDisEspecialidade);
                    }else{ 
                        $valor = $aluno->$ok;
                    }
                    $this->html .="<td style='".$cab["css"].$this->border()."'>".$valor."</td>";
                }
                $this->html .="</tr>";
            } 

            $this->html .="</table>
            </div>
            <div>";

            $this->exibir("", "Lista de Colegas de Turma-".$this->numAno);
        }
    }

new listaTurmas(__DIR__);
    
    
  
?>
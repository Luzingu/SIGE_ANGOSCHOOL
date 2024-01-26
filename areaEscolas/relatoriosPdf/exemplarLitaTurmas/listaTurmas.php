<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class listaTurmas extends funcoesAuxiliares{
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista de Turma");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null;

            $this->disciplinas = $this->selectArray("nomedisciplinas", [], ["idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9, 20, 21, 22, 23)]]); 
                      
            $this->nomeCurso();
            $this->numAno();
            if($this->verificacaoAcesso->verificarAcesso("", ["relatorioDasTurmas"], [$this->classe, $this->idPCurso], "") || count($this->selectArray("listaturmas", ["nomeTurma"], ["idListaAno"=>$this->idAnoActual, "idPEscola"=>$_SESSION['idEscolaLogada'], "idCoordenadorTurma"=>$_SESSION['idUsuarioLogado'], "classe"=>$this->classe, "nomeTurma"=>$this->turma, "idPNomeCurso"=>$this->idPCurso]))>0){
                $this->listaTurmasLiceu();
            }else{
                $this->negarAcesso();
            } 

        }

         private function listaTurmasLiceu(){
            $alunos =$this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), ["nomeAluno", "numeroInterno", "sexoAluno", "escola.idGestLinguaEspecialidade", "escola.idGestDisEspecialidade", "idPMatricula"]);
            $this->nomeTurma("", "", "", $this->idPAno);

            if($this->modLinguaEstrangeira=="opcional" || $this->tipoCurso=="geral"){
                $alunos = $this->anexarTabela2($alunos, "nomedisciplinas", "escola", "idPNomeDisciplina", "idGestLinguaEspecialidade");
                
                 $cabecalho[]= array('titulo'=>"Nº", "tituloDb"=>"num", "css"=>$this->text_center."width:30px;");
                $cabecalho[]= array('titulo'=>"Nome Completo", "tituloDb"=>"nomeAluno", "css"=>"width:200px;");
                $cabecalho[]= array('titulo'=>"Número Interno", "tituloDb"=>"numeroInterno", "css"=>$this->text_center." width:160px;");
                $cabecalho[]= array('titulo'=>"Sexo", "tituloDb"=>"sexoAluno", "css"=>$this->text_center."width:30px;");
                
                if($this->modLinguaEstrangeira=="opcional" || $this->idPCurso==3){
                    $cabecalho[]= array('titulo'=>"L. Opção", "tituloDb"=>"lingOpcao", "css"=>"width:110px;");
                }
    
                if($this->tipoCurso=="geral" && $this->classe>10 ){
                    $cabecalho[]= array('titulo'=>"Disc. Opção", "tituloDb"=>"discOpcao", "css"=>"width:110px;");
                }
                
            }else{
                $cabecalho[]= array('titulo'=>"Nº", "tituloDb"=>"num", "css"=>$this->text_center."width:30px;");
                $cabecalho[]= array('titulo'=>"Nome Completo", "tituloDb"=>"nomeAluno", "css"=>"width:200px;");
                $cabecalho[]= array('titulo'=>"Número Interno", "tituloDb"=>"numeroInterno", "css"=>$this->text_center." width:160px;");
                $cabecalho[]= array('titulo'=>"Sexo", "tituloDb"=>"sexoAluno", "css"=>$this->text_center."width:30px;");
            }            
             
            $totF=0;
            foreach ($alunos as $aluno) {
                if($aluno["sexoAluno"]=="F"){
                    $totF++;
                }
            }

            $this->html .="<html style='margin-left:0px; margin-right:0px;'>
            <head>
                <title>Lista de Turma</title>
                <style>
                  table tr td{
                      padding:3px;
                  }
                  table, p{
                    font-size:11pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:20px; margin-right:20px;'>".$this->fundoDocumento("../../../")."

            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho()."

            <p style='".$this->text_center.$this->bolder.$this->sublinhado."'>LISTA DOS ALUNOS - ".$this->numAno."</p>"; 
             
            $top=150;

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

            $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CLASSE: <strong>".$this->classeExt." / ".$this->nomeTurma()."</strong></p>
            <p style='".$this->maiuscula.$this->miniParagrafo."'>PERÍODO: <strong>".valorArray($this->sobreTurma, "periodoT")."</strong> / SALA N.º: <strong> ".completarNumero(valorArray($this->sobreTurma, "numeroSalaTurma"))."</strong></p>
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

                $this->html .="<tr>";
                foreach ($cabecalho as $cab) {
                    $ok = $cab["tituloDb"];

                    if($ok=="num"){
                       $valor=completarNumero($n);
                    }else if($ok=="nomeAluno"){
                        $valor = $aluno["nomeAluno"];
                    }else if($ok=="sexoAluno"){
                        $valor = generoExtenso($aluno["sexoAluno"]);
                    }else if($ok=="numeroInterno"){
                        $valor = $aluno["numeroInterno"];
                    }else if($ok=="lingOpcao"){
                        $valor = $this->retornarDiscLinguaOpcao(valorArray($aluno, "idGestLinguaEspecialidade", "escola"));
                    }else if($ok=="discOpcao"){
                        $valor = $this->retornarDiscLinguaOpcao(valorArray($aluno, "idGestDisEspecialidade", "escola"));
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

        $this->html .="<p style='padding-left:10px; padding-right:10px;".$this->maiuscula."'>".$this->rodape().".</p>".$this->assinaturaDirigentes("mengi");

            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Lista_Turmas", "Lista-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->numAno, "Lista-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPAno); 
        }

        private function retornarDiscLinguaOpcao($idPNomeDisciplina){
            $nome="";
            foreach($this->disciplinas as $disc){
                if($disc->idPNomeDisciplina==$idPNomeDisciplina){
                    $nome=$disc->nomeDisciplina;
                    break;
                }
            }
            return $nome;
        }
    }

new listaTurmas(__DIR__);
    
    
  
?>
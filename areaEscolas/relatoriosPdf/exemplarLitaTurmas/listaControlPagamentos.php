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
                      
            $this->nomeCurso();
            $this->numAno();
            if($this->verificacaoAcesso->verificarAcesso("", ["relatorioDasTurmas"], [$this->classe, $this->idPCurso], "")){
                $this->listaTurmasLiceu();
            }else{
                $this->negarAcesso();
            }
        }

         private function listaTurmasLiceu(){
            $alunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, array(), ["nomeAluno", "numeroInterno", "sexoAluno", "idPMatricula"]);

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

            <p style='".$this->text_center.$this->bolder.$this->sublinhado.$this->maiuscula."'>".valorArray($this->sobreEscolaLogada, "nomeEscola")."</p>
            <p style='".$this->maiuscula."'>LISTA DE PAG. DE COMPARTICIPAÇÕES &nbsp;&nbsp;&nbsp;"; 
            
            $this->html .="OPÇÃO: <strong>".$this->nomeCurso."</strong>
            &nbsp;&nbsp;&nbsp;CLASSE: <strong>".$this->classeExt."</strong>&nbsp;&nbsp;&nbsp; TURMA: <strong>".$this->nomeTurma()."</strong>";

            $cabecalho[]= array('titulo'=>"Nº", "tituloDb"=>"num", "css"=>$this->text_center."width:20px;");
            $cabecalho[]= array('titulo'=>"Nome Completo", "tituloDb"=>"nomeAluno", "css"=>"width:150px;");
            
            $this->html .="</p><div>
            <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>";

            foreach ($cabecalho as $cab) {
                $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."'>".$cab["titulo"]."</td>";
            }
            foreach($this->mesesAnoLectivo as $m){
                $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."'>".substr(nomeMes($m), 0, 1)."</td>";
            }
                    
            $this->html .="</tr>";

            $n=0;
            foreach ($alunos as $aluno) {
                $n++;

                $this->html .="<tr>";
                foreach ($cabecalho as $cab) {
                    $ok = $cab["tituloDb"];

                    if($ok=="num"){
                       $valor=completarNumero($n);
                    }else{ 
                        $valor = $aluno[$ok];
                    }
                    $this->html .="<td style='".$cab["css"].$this->border()."'>".$valor."</td>";
                }
                foreach($this->mesesAnoLectivo as $m){
                    $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."'></td>";
                }
                $this->html .="</tr>";
            } 

            $this->html .="</table>
            </div>
            <div>";

        $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Lista_Turmas", "Lista-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->numAno, "Lista-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->idPAno); 
        }
    }

new listaTurmas(__DIR__);
    
    
  
?>
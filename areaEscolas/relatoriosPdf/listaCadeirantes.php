<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class listaTurmas extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista de Cadeirantes");

            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
                      
            $this->nomeCurso();

            if($this->verificacaoAcesso->verificarAcesso("", "cadeirantes", array(), "")){                                
                $this->exibirCadeirantes();
            }else{
              $this->negarAcesso();
            }

        }

         private function exibirCadeirantes(){

            $cabecalho[]= array('titulo'=>"N.º", "tituloDb"=>"num", "css"=>$this->text_center."width:20px;");
            $cabecalho[]= array('titulo'=>"Nome Completo", "tituloDb"=>"nomeAluno", "css"=>"width:150px;");
            $cabecalho[]= array('titulo'=>"S", "tituloDb"=>"sexoAluno", "css"=>$this->text_center."width:20px;");

            $cabecalho[]= array('titulo'=>"Disciplina", "tituloDb"=>"nomeDisciplina", "css"=>"width:150px;");
            $cabecalho[]= array('titulo'=>"Ano", "tituloDb"=>"numAno", "css"=>"width:50px;".$this->text_center);
             $cabecalho[]= array('titulo'=>"Nota", "tituloDb"=>"", "css"=>"width:30px;");

            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "sexoAluno", "cadeiras_atraso.idPCadeirantes", "numeroInterno", "fotoAluno", "idPMatricula", "cadeiras_atraso.exameEspecial", "cadeiras_atraso.idCadAno", "grupo", "cadeiras_atraso.idCadDisciplina"], ["cadeiras_atraso.idCadEscola"=>$_SESSION['idEscolaLogada'], "cadeiras_atraso.classeCadeira"=>$this->classe, "cadeiras_atraso.idCadCurso"=>$this->idPCurso, "cadeiras_atraso.estadoCadeira"=>"F"], ["cadeiras_atraso"], "", [], ["nomeAluno"=>1]);
          $alunos = $this->anexarTabela2($alunos, "nomedisciplinas", "cadeiras_atraso", "idPNomeDisciplina", "idCadDisciplina");
          $alunos = $this->anexarTabela2($alunos, "anolectivo", "cadeiras_atraso", "idPAno", "idCadAno");
             
            $this->html .="<html style='margin-left:0px; margin-right:0px;'>
            <head>
                <title>Lista dos Cadeirante</title>
                <style>
                  table tr td{
                      padding:3px;
                  }
                  table, p{
                    font-size:11pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:20px; margin-right:20px;'>".$this->fundoDocumento("../../")."

            <div style='position: absolute;'><div style='margin-top: 0px; width:300px;".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div></div>

            <div class='cabecalho'>".$this->cabecalho()."

            <p style='".$this->text_center.$this->bolder.$this->sublinhado.$this->maiuscula."'>LISTA DOS CADEIRANTES DA ".$this->classeExtensa."</p>"; 
             
            $top=150;
            if($this->classe>=10){

                $top=190;
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->text_center.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->text_center.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->text_center.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->text_center.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $top=190;
                    $this->html .="<p style='".$this->text_center.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }  
            }            
            
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

                    if($ok==""){
                       $valor="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }else if($ok=="num"){
                       $valor=completarNumero($n);
                    }else if($ok=="nomeAluno"){
                        $valor = $aluno["nomeAluno"];
                    }else if($ok=="numAno"){
                        $valor = $aluno["numAno"];
                    }else{ 
                        $valor = $aluno[$ok];
                    }
                    $this->html .="<td style='".$cab["css"].$this->border()."'>".$valor."</td>";
                }
                $this->html .="</tr>";
            } 

            $this->html .="</table>
            </div>
            <div>";

        $this->html .="<p style='padding-left:10px; padding-right:10px;".$this->maiuscula."'>".$this->rodape().".</p>";
              
        $this->html .= "<div style='".$this->maiuscula."'>".$this->assinaturaDirigentes("mengi")."</div>";

            $this->exibir("", "Lista de Turma dos Cadeirantes-".$this->nomeCursoAbr);
        }
    }

new listaTurmas(__DIR__);
    
    
  
?>
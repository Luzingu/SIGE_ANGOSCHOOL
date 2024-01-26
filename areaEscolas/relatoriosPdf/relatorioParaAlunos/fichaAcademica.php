<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class certificadoDisciplina extends funcoesAuxiliares{
              
        public $efeitoDeclaracao="";
        public $numeroDeclaracao="";
        public $idPMatricula="";
        public $art1="";
        public $art2="";

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Ficha Académica");  
            $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:null;
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;

            $this->aluno = $this->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso LEFT JOIN anolectivo ON idPAno=idMatFAno", "*", "idPMatricula=:idPMatricula AND idMatEscola=:idMatEscola AND estadoAluno in ('A', 'Y')", [$_SESSION["idUsuarioLogado"], $_SESSION["idEscolaLogada"]]);

            $this->numAno();
            $this->idPCurso = valorArray($this->aluno, "idMatCurso");
            $this->nomeCurso();

            if($this->verificacaoAcesso->verificarAcesso(1, [], [], "")){
                $this->boletim();
            }else{
                $this->negarAcesso();
            }            

        }
        
        public function boletim(){
            
            if(valorArray($this->aluno, "sexoAluno")=="M"){
                $this->art1="o";
                $this->art2 ="";
            }else{
                $this->art1="a";
                $this->art2 ="a";
            }
             $periodo = valorArray($this->aluno, "periodoAluno");
             if($periodo=="reg"){
                $periodo="Regular";
             }else if($periodo=="pos"){
                $periodo="Pós-Laboral";
             }
                    
            $this->classe = valorArray($this->aluno, "classeActualAluno");
            $this->turma = valorArray($this->aluno, "designacaoTurma");
             $mediaMac=0;
           $mediaNpp=0;
           $mediaNpt=0;
           $mediaMt=0;
           $contador=0;
            
            $this->html .="
           <html style='margin:0px;'>
            <head>
                <title>Ficha Académica</title>
                <style>
                    .tabela tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:2px;

                    }
                </style>
            </head>
           <body style='margin:20px; margin-bottom:0px; margin-top:10px; padding-top:10px;'>".$this->fundoDocumento("../../../")."
           
           <div style='border:solid black 2px; padding:5px; height:150px;'>
            <div style='padding-top:20px;'>";

            $src = '../../../Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreUsuarioLogado, "logoEscola");
            
            if(!file_exists($src) || valorArray($this->sobreUsuarioLogado, "logoEscola")==NULL || valorArray($this->sobreUsuarioLogado, "logoEscola")==""){
              $src = '../../../icones/logoAngoSchool1.png';
            }

            $this->html .="<img src='".$src."' style='height:120px; width:120px;'></div>
            <div style='margin-left:125px; margin-top:-200px;'>
                <p style='".$this->miniParagrafo."'>REPÚBLICA DE ANGOLA</p>
                <p style='".$this->miniParagrafo."'>GOVERNO PROVINCIAL DO ZAIRE</p>
                <p style='".$this->miniParagrafo."'>GABINETE PROVINCIAL DA EDUCAÇÃO</p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>";

              if($_SESSION["idEscolaLogada"]==17 && $this->tipoCurso=="geral"){
                
                    $this->html .="Liceu do Tuku<br/>Mbanza Kongo";
                
              }else{
                    $this->html .=valorArray($this->sobreUsuarioLogado, "tituloEscola");
              }
              $this->html .="</p>
                <p style='".$this->bolder.$this->text_center.$this->miniParagrafo."'>FICHA ACADÉMICA</p>
            </div>
           </div>
           <div style='border:solid black 2px; margin-top:10px; background-color: rgba(0, 0, 0, 0.5); color:white;".$this->text_center." padding-top:5px;'>
            <strong>Dados do Aluno</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black; margin-top:5px; border-left:none; border-right:none;'>
                <table class='tabela' style='width:100%; '>

                    <tr>
                        <td style='".$this->text_right."'>Nome:</td>
                        <td colspan='3'><strong>".valorArray($this->aluno, "nomeAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Sexo:</td>
                        <td><strong>".generoExtenso(valorArray($this->aluno, "sexoAluno"))."</strong></td>
                        <td style='".$this->text_right."'>N.º Interno:</td>
                        <td><strong>".valorArray($this->aluno, "numeroInterno")."</strong></td>
                    </tr>";

                    if(valorArray($this->aluno, "classeActualAluno")>=10){
                         if($this->tipoCurso=="tecnico"){
                            $this->html .="
                            <tr>
                            <td style='".$this->text_right."'>Área de Formação:</td><td><strong>".valorArray($this->aluno, "areaFormacaoCurso")."</strong></td>

                                <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                            </tr>";
                        }else if($this->tipoCurso=="pedagogico"){
                            $this->html .="
                            <tr>
                            <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($this->aluno, "areaFormacaoCurso")."</strong></td>

                                <td style='".$this->text_right."'>Opção:</td><td><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                            </tr>";
                        }else{
                            $this->html .="
                            <tr>
                                <td style='".$this->text_right."'>Curso:</td><td colspan='3'><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                            </tr>";
                        }
                    }
                    $this->html .="
                    <tr>
                    <td style='".$this->text_right."'>Classe:</td><td><strong>";
                    if(valorArray($this->aluno, "classeActualAluno")==120){
                        $this->html .="FIN_".valorArray($this->aluno, "numAno");
                    }else{
                        $this->html .=classeExtensa(valorArray($this->aluno, "classeActualAluno"), valorArray($this->aluno, "sePorSemestre"));
                    }
                    $this->html .="</strong></td>

                        <td style='".$this->text_right."'>Período:</td><td><strong>".$periodo."</strong></td>
                    </tr>
                    <tr>
                    
                    </tr>";
                $this->html .="</table>

                <table class='tabela' style='width:100%;'>";

           foreach($this->selectArray("notasfinaisalunos", "DISTINCT classeNota", "idNotaAluno=:idNotaAluno AND classeNota!=:classeNota AND idNotaCurso=:idNotaCurso", [$_SESSION["idUsuarioLogado"], valorArray($this->aluno, "classeActualAluno"), valorArray($this->aluno, "idMatCurso")], "classeNota ASC") as $classe){

                $this->html .="<tr style='background-color: rgba(0, 0, 0, 0.5);'><td style='".$this->text_center."' colspan='4'><strong>".classeExtensa($classe->classeNota)."</strong></td></tr>";

                $counter=0;
                foreach($this->selectCondClasseCurso("array", "notasfinaisalunos LEFT JOIN nomedisciplinas ON idPNomeDisciplina=idNotaDisciplina LEFT JOIN disciplinas ON idPNomeDisciplina=idFNomeDisciplina", "*", "idNotaAluno=:idNotaAluno AND classeNota=:classeNota AND classeDisciplina=classeNota AND idDiscEscola=:idDiscEscola AND periodoDisciplina=:periodoDisciplina", [$_SESSION["idUsuarioLogado"], $classe->classeNota, $_SESSION["idEscolaLogada"], valorArray($this->aluno, "periodoAluno"), valorArray($this->aluno, "idMatCurso")], $classe->classeNota, " AND idDiscCurso=:idDiscCurso AND idNotaCurso=idDiscCurso", "ordenacao ASC") as $nota){
                  $counter++;
                    $this->html .="<tr><td style='".$this->text_center."'>".completarNumero($counter)."</td><td>".$nota->nomeDisciplina."</td><td>".tipoDisciplina($nota->tipoDisciplina)."</td>".$this->tratarVermelha($nota->mediaFinal, "", 10)."</tr>";
                }
            }

            $this->html .="</table></div></body></html>";
           
            
           $this->exibir("", "Ficha Académica");
        }

        private function bomMau($a){
            if($a=="Mau"){
                return "<span style='".$this->vermelha."'>".$a."</span>";
            }else if($a=="Suficiente"){
                return "<span style='".$this->azul."'>".$a."</span>";
            }else if($a=="Bom"){
                return "<span style='".$this->azul."'>".$a."</span>";
            }else if($a=="Muito Bom"){
                return "<span style='".$this->$this->verde."'>".$a."</span>";
            }else if($a=="Excelente"){
                return "<span style='".$this->verde."'>".$a."</span>";
            }else{
              return $a;
            }
        }

        private function observacaoFinal($obs, $mediaMt){
          if($_SESSION["trimestre"]==4){
            if($obs=="A"){
                return "<span style='".$this->verde."'>APTO(A)</span>";
            }else if($obs=="TR"){
                return "<span style='".$this->verde."'>TRANSITA</span>";
            }else if($obs=="REC"){
                return "<span style='".$this->azul."'>RECURSO</span>";
            }else if($obs=="D"){
                return "<span style='".$this->vermelha."'>DESISTENTE</span>";
            }else if($obs=="N"){
                return "<span style='".$this->vermelha."'>ANULADA</span>";
            }else if($obs=="EF"){
                return "<span style='".$this->vermelha."'>EX. FALTA</span>";
            }else if($obs=="D"){
                return "<span style='".$this->vermelha."'>REP. INDISC.</span>";
            }else if($obs=="F"){
                return "<span style='".$this->vermelha."'>REP. FALTAS.</span>";
            }else{
              return "<span style='".$this->vermelha."'>N. APTO(A)</span>";
            }
          }else{
            $i=1;
            if($this->classe>=7){
              $i=2;
            }
            if((int)$mediaMt<5*$i){
                return "<span style='".$this->vermelha."'>MAU</span>";
            }else if((int)$mediaMt<7.5*$i){
                return "<span style='".$this->azul."'>BOM</span>";
            }else{
                return "<span style='".$this->verde."'>MUITO BOM</span>";
            } 
          }
      }
    }
    new certificadoDisciplina(__DIR__);
?>
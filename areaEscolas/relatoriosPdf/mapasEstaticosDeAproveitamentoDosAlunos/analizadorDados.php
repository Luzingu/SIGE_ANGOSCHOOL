<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ($_SERVER["DOCUMENT_ROOT"].'/angoschool/areaEscolas/funcoesAuxiliares.php');
    include_once ($_SERVER["DOCUMENT_ROOT"].'/angoschool/areaEscolas/funcoesAuxiliaresDb.php');

    class analizador extends funcoesAuxiliares{

        public $alunosAvaliadosPorDisciplina = array();
        public $alunos = array();
        public $alunosTrans = array();
        public $tipoAproveitamento ="geral";
        public $trimestreApartir="I";
        public $notaMinima=10;
        public $cabecalhos=array();
         public $cabecalhos2=array();

        function __construct($caminhoAbsoluto){
            parent::__construct();

            $this->cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"matriculados", "genero"=>"TOT");

            $this->cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"matriculados", "genero"=>"F");
            //$this->cabecalhos[] = array('titulo'=>"%", "tituloDb"=>"matriculados", "genero"=>"%");

            /*$this->cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"desistentes", "genero"=>"TOT");
            $this->cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"desistentes", "genero"=>"F");
            $this->cabecalhos[] = array('titulo'=>"%", "tituloDb"=>"desistentes", "genero"=>"%");*/

            $this->cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"transfEntrada", "genero"=>"TOT");
            $this->cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"transfEntrada", "genero"=>"F");
            //$this->cabecalhos[] = array('titulo'=>"%", "tituloDb"=>"transfEntrada", "genero"=>"%");


            $this->cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"transfSaida", "genero"=>"TOT");
            $this->cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"transfSaida", "genero"=>"F");
            //$this->cabecalhos[] = array('titulo'=>"%", "tituloDb"=>"transfSaida", "genero"=>"%");

            $this->cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"naoAvaliados", "genero"=>"TOT");
            $this->cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"naoAvaliados", "genero"=>"F");
            //$this->cabecalhos[] = array('titulo'=>"%", "tituloDb"=>"naoAvaliados", "genero"=>"%");

            $this->cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"avaliados", "genero"=>"TOT");
            $this->cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"avaliados", "genero"=>"F");
            //$this->cabecalhos[] = array('titulo'=>"%", "tituloDb"=>"avaliados", "genero"=>"%");

            $this->cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"aprovados", "genero"=>"TOT");
            $this->cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"aprovados", "genero"=>"F");
            //$this->cabecalhos[] = array('titulo'=>"%", "tituloDb"=>"aprovados", "genero"=>"%");

            $this->cabecalhos[] = array('titulo'=>"MF", "tituloDb"=>"reprovados", "genero"=>"TOT");
            $this->cabecalhos[] = array('titulo'=>"F", "tituloDb"=>"reprovados", "genero"=>"F");
            //$this->cabecalhos[] = array('titulo'=>"%", "tituloDb"=>"reprovados", "genero"=>"%");

        $valBom ="BOM<br/> APROVEITAMENTO";
        $valMau = "MAU<br/>APROVEITAMENTO";
        $valNaoAvaliados = "NÃO AVALIADOS";
        if(isset($_GET["trimestreApartir"]) && $_GET["trimestreApartir"]=="IV"){
            $valBom="APROVADOS";
            $valMau="REPROVADOS";
            $valNaoAvaliados = "DESISTENTES";
        }

        $this->varCabecalho = "
           <tr style='".$this->corDanger."'>
           <td style='".$this->text_center.$this->border().$this->bolder."' rowspan='3'>TURMAS</td>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>MATRICULADOS</td>";

           /*$this->varCabecalho .="
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>DESISTENTES</td>";*/

           $this->varCabecalho .="
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='4'>TRANSFERÊNCIAS</td>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>".$valNaoAvaliados."</td>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>AVALIADOS</td>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>".$valBom."</td>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>".$valMau."</td></tr>

           <tr style='".$this->corDanger."'>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2'>ENTRADAS</td>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2'>SAÍDAS</td>
           </tr>
           <tr style='".$this->corDanger."'>";
            foreach ($this->cabecalhos as $cab) {
               $this->varCabecalho .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$cab["titulo"]."</td>";
              
            }
            $this->varCabecalho .="</tr>";
        }

        public function  inicializador(){
            $this->alunosTrans = array();
            $this->alunos = array();
            
            $arrayCursos =array();
            foreach($this->selectArray("nomecursos", ["idPNomeCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
              $arrayCursos[]=$curso["idPNomeCurso"];
            }
 
            $this->alunosTrans = array_merge($this->alunosTrans, $this->selectArray("alunosmatriculados", ["dataNascAluno", "sexoAluno", "reconfirmacoes.nomeTurma", "reconfirmacoes.classeReconfirmacao","reconfirmacoes.mfT1","reconfirmacoes.mfT2", "reconfirmacoes.mfT3", "reconfirmacoes.mfT4", "reconfirmacoes.observacaoF", "reconfirmacoes.estadoDesistencia", "reconfirmacoes.tipoEntrada"], ["reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idMatCurso"=>['$in'=>$arrayCursos], "reconfirmacoes.estadoReconfirmacao"=>"T"], ["reconfirmacoes"]));

            $this->alunos = array_merge($this->alunos, $this->selectArray("alunosmatriculados", ["dataNascAluno", "sexoAluno", "reconfirmacoes.nomeTurma", "reconfirmacoes.classeReconfirmacao","reconfirmacoes.mfT1","reconfirmacoes.mfT2", "reconfirmacoes.mfT3", "reconfirmacoes.mfT4", "reconfirmacoes.observacaoF", "reconfirmacoes.estadoDesistencia", "reconfirmacoes.tipoEntrada"], ["reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idMatCurso"=>['$in'=>$arrayCursos], "reconfirmacoes.estadoReconfirmacao"=>"A"], ["reconfirmacoes"], "", [], [], $this->matchMaeAlunos($this->idPAno)));
        }

        public function alunosAvaliadosPorDisciplina($idPDisciplina){

            $modelo="pautas";
            if($this->idPAno!=$this->idAnoActual){
                $modelo="arquivo_pautas";
            }
            $arrayAlunos = $arrayAlunos1 = $arrayAlunos2 = $arrayAlunos3=array();
            $alunosReconfirmados=$alunosReconfirmados1=$alunosReconfirmados2=$alunosReconfirmados3=array();

            $this->alunosAvaliadosPorDisciplina = array();
            $arrayAlunos = array_merge($arrayAlunos, $arrayAlunos1);
            $arrayAlunos = array_merge($arrayAlunos, $arrayAlunos2);
            $arrayAlunos = array_merge($arrayAlunos, $arrayAlunos3);

            $alunosReconfirmados = array_merge($alunosReconfirmados, $alunosReconfirmados1);
            $alunosReconfirmados = array_merge($alunosReconfirmados, $alunosReconfirmados2);
            $alunosReconfirmados = array_merge($alunosReconfirmados, $alunosReconfirmados3);

            $i=0;
            $this->alunosAvaliadosPorDisciplina=array();
            foreach($alunosReconfirmados as $aluno){

                $objectAluno=array();
                foreach ($arrayAlunos as $piter){
                  if($piter["idPMatricula"]==$aluno["idPMatricula"]){
                    $objectAluno = $piter;
                    break;
                  }
                }

                $condicaoPauta = ["classePauta=".$aluno["classeReconfirmacao"], "idPautaDisciplina=".$idPDisciplina];
                if($aluno["classeReconfirmacao"]>=10){
                    $condicaoPauta[]="idPautaCurso=".$aluno["idMatCurso"];
                }
                if($modelo=="arquivo_pautas"){
                    $condicaoPauta[]="idPautaAno=".$this->idPAno;
                }

                $this->alunosAvaliadosPorDisciplina[$i]=$aluno;
                foreach(listarItensObjecto($objectAluno, $modelo, $condicaoPauta) as $pauta){

                    foreach(retornarChaves($pauta) as $chavePauta){
                        if(isset($pauta[$chavePauta])){
                            $this->alunosAvaliadosPorDisciplina[$i][$chavePauta]=$pauta[$chavePauta];
                        }
                    }
                }
                $i++;
            }
            
        } 

        public function quanto($idCurso, $classe, $turma, $tipoDado, $genero){
            
            $contador=0;
            $array=$this->alunos;
            if($this->trimestreApartir=="I"){
                $campoTrimestreDB="mfT1";
            }else if($this->trimestreApartir=="II"){
                $campoTrimestreDB="mfT2";
            }else if($this->trimestreApartir=="III"){
                $campoTrimestreDB="mfT3";
            }else{
                $campoTrimestreDB="mfT4";
            }

            //Considera os alunos na disciplina caso o aproveitamento n達o seja geral...
            if($this->tipoAproveitamento!="geral" && ($tipoDado=="avaliados" || $tipoDado=="aprovados" || $tipoDado=="reprovados")){

                $array = $this->alunosAvaliadosPorDisciplina;

                if($this->trimestreApartir=="I" || $this->trimestreApartir=="II" || $trimestreApartir=="III"){
                    $campoTrimestreDB ="mt".$this->trimestreApartir;
                }else{
                    $campoTrimestreDB="mf";
                }
            }

            foreach ($array as $alunos) {
                if(nelson($alunos, "classeReconfirmacao", "reconfirmacoes")<=6){
                    $this->notaMinima=5;
                }else{
                    $this->notaMinima=10;
                }
                if($tipoDado=="matriculados" && seComparador($turma, nelson($alunos, "nomeTurma", "reconfirmacoes")) && seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && $this->seComparadorCurso($idCurso, nelson($alunos, "idMatCurso", "reconfirmacoes"), nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && (nelson($alunos, "tipoEntrada", "reconfirmacoes")=="novaMatricula" || nelson($alunos, "tipoEntrada", "reconfirmacoes")==NULL || nelson($alunos, "tipoEntrada", "reconfirmacoes")=="")){
                    
                    $contador++;
                }else if($tipoDado=="transfEntrada" && seComparador($turma, nelson($alunos, "nomeTurma", "reconfirmacoes")) && seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && $this->seComparadorCurso($idCurso, nelson($alunos, "idMatCurso", "reconfirmacoes"), nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && nelson($alunos, "tipoEntrada", "reconfirmacoes")=="porTransferencia"){ 
                    
                    $contador++;
                }else if(($tipoDado=="excluidoF") && seComparador($turma, nelson($alunos, "nomeTurma", "reconfirmacoes")) && seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && $this->seComparadorCurso($idCurso, nelson($alunos, "idMatCurso", "reconfirmacoes"), nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && nelson($alunos, "estadoDesistencia", "reconfirmacoes")=="F"){
                    
                    $contador++;

                }else if(($tipoDado=="matriculaAnulada") && seComparador($turma, nelson($alunos, "nomeTurma", "reconfirmacoes")) && seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && $this->seComparadorCurso($idCurso, nelson($alunos, "idMatCurso", "reconfirmacoes"), nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && nelson($alunos, "estadoDesistencia", "reconfirmacoes")=="N"){
                    
                    $contador++;

                }else if(($tipoDado=="avaliados" || $tipoDado=="naoAvaliados" || $tipoDado=="aprovados" ||  $tipoDado=="reprovados") && (seComparador($turma, nelson($alunos, "nomeTurma", "reconfirmacoes")) && seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && $this->seComparadorCurso($idCurso, nelson($alunos, "idMatCurso", "reconfirmacoes"), nelson($alunos, "classeReconfirmacao", "reconfirmacoes")))){
                    
                    if(nelson($alunos, "estadoDesistencia", "reconfirmacoes")=="N" || nelson($alunos, "estadoDesistencia", "reconfirmacoes")=="D" || nelson($alunos, "estadoDesistencia", "reconfirmacoes")=="F" || ($this->trimestreApartir!="IV" && number_format((double) nelson($alunos, $campoTrimestreDB, "reconfirmacoes"), 0)<=0)){
                        
                        if($tipoDado=="naoAvaliados"){
                            $contador++;
                        }
                    }else{
                        if($tipoDado=="avaliados"){
                            $contador++;
                        }
                        if(($this->tipoAproveitamento=="geral" && $this->trimestreApartir=="IV" && (nelson($alunos, "observacaoF", "reconfirmacoes")=="A" || nelson($alunos, "observacaoF", "reconfirmacoes")=="TR")) || (($this->tipoAproveitamento!="geral" || $this->trimestreApartir!="IV") && number_format((double) nelson($alunos, $campoTrimestreDB, "reconfirmacoes"), 0)>=$this->notaMinima)){
                            
                            if($tipoDado=="aprovados"){
                                $contador++;
                            }
                        }else{
                            if($tipoDado=="reprovados"){
                                $contador++;
                            }
                        }
                        
                    }

                }
            }
            foreach ($this->alunosTrans as $alunos) {
                if(($tipoDado=="matriculados" || $tipoDado=="transfSaida") && seComparador($turma, nelson($alunos, "nomeTurma", "reconfirmacoes")) && seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, nelson($alunos, "classeReconfirmacao", "reconfirmacoes")) && $this->seComparadorCurso($idCurso, nelson($alunos, "idMatCurso", "reconfirmacoes"), nelson($alunos, "classeReconfirmacao", "reconfirmacoes"))){                   
                    $contador++;                    
                }
            }
           
            return completarNumero($contador);
        }


        public function percentagem($valor1, $valor2){
            if($valor1==0){
                $perc = "0";
            }else{
                $perc=0;
                $perc = ($valor2/$valor1)*100;
            }
            if($perc==100){
                return "100";
            }else{
                return number_format($perc, 2);
            }
        }

        private function seComparadorCurso($valA, $valB, $classe){
            if($valA=="TOT"){
                return true;
            }else if($valA=="1Ciclo" && $classe>=7 && $classe<=9){
                return true;
            }else if($valA=="ensinoPrimario" && $classe>=1 && $classe<=6){
                return true;
            }else if($valA==$valB){
                return true;
            }else{
                return false;
            }
        }        
    }
new analizador(__DIR__); 
?>
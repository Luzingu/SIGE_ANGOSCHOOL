<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliaresDb.php';

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
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
            parent::__construct();

            $this->privacidade = isset($_GET["privacidade"])?$_GET["privacidade"]:"";
            $this->labelPrivacidade="Público";
            if($this->privacidade!="Privada" && $this->privacidade!="Pública"){
                $this->privacidade="Pública";
            }
            if($this->privacidade=="Privada"){
                $this->labelPrivacidade="Privado";
            }

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
        if($this->trimestreApartir=="IV"){
            $valBom="APROVADOS";
            $valMau="REPROVADOS";
        }

        $this->varCabecalho = "
           <tr style='".$this->corDanger."'>
           <td style='".$this->text_center.$this->border().$this->bolder."' rowspan='3'>TURMAS</td>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>MATRICULADOS</td>";

           /*$this->varCabecalho .="
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>DESISTENTES</td>";*/

           $this->varCabecalho .="
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='4'>TRANSFERÊNCIAS</td>
           <td style='".$this->text_center.$this->border().$this->bolder."' colspan='2' rowspan='2'>NÃO AVALIADOS</td>
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
            
            $condicao="";
            if($this->tipoCurso=="1Ciclo"){
                $condicao=" AND classeReconfirmacao>=7 AND classeReconfirmacao<=9";
            }else if($this->tipoCurso=="ensinoPrimario"){
                $condicao=" AND classeReconfirmacao>=0 AND classeReconfirmacao<=6";
            }else{
                $condicao=" AND tipoCurso='".$this->tipoCurso."'";
            }

            
            $this->alunos=array();
            $this->alunosTrans=array();
            $this->escolas=array();
            foreach($this->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPEscola", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), $this->privacidade, "A"], "nomeEscola ASC") as $a){

                $array = $this->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula LEFT JOIN avaliacaoanualaluno ON idAvalMatricula=idPMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso", "*", "idReconfEscola=:idReconfEscola AND idReconfEscola=idTurmaEscola AND classeTurma=classeReconfirmacao AND idMatEscola=idReconfEscola AND nomeTurma IS NOT NULL AND estadoAluno in ('A', 'Y') AND idAvalAno=idReconfAno AND idTurmaAno=idReconfAno AND idTurmaAno=:idTurmaAno".$condicao, [$a->idPEscola, $this->idPAno]);
                $this->alunos = array_merge($this->alunos, $array);
                if(count($array)>0){
                    $this->escolas[]=$a->idPEscola;
                }

                $this->alunosTrans = array_merge($this->alunosTrans, $this->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN transferencia_alunos ON idTransfMatricula=idPMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso", "*", "idTransfEscolaOrigem=idMatEscola AND idTransfEscolaOrigem=:idTransfEscolaOrigem AND idTransfAno=:idTransfAno".$condicao." AND idReconfAno=idTransfAno", [$a->idPEscola, $this->idPAno]));
            }
        }

        public function alunosAvaliadosPorDisciplina($idPDisciplina){

            $condicao="";
            if($this->tipoCurso=="1Ciclo"){
                $condicao=" AND classeReconfirmacao>=7 AND classeReconfirmacao<=9";
            }else if($this->tipoCurso=="ensinoPrimario"){
                $condicao=" AND classeReconfirmacao>=0 AND classeReconfirmacao<=6";
            }else{
                $condicao=" AND tipoCurso='".$this->tipoCurso."'";
            }

            $modelo="TOT";
            if($this->idPAno!=$this->idAnoActual){
                $modelo="_arq";
            }

            $this->alunosAvaliadosPorDisciplina = $this->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN alunosreconfirmados ON idReconfMatricula=idPMatricula LEFT JOIN turmas ON idTurmaMatricula=idPMatricula LEFT JOIN avaliacaoanualaluno ON idAvalMatricula=idPMatricula LEFT JOIN pautas".$modelo."_mod_2020 ON idPautaMatricula=idPMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso", "*", "idReconfEscola=idReconfEscola AND idReconfEscola=:idReconfEscola AND idReconfEscola=idTurmaEscola AND idAvalAno=idReconfAno AND classeTurma=classeReconfirmacao AND idReconfAno=:idReconfAno AND idPautaDisciplina=:idPautaDisciplina AND classePauta=classeReconfirmacao AND ((idPautaCurso=idMatCurso AND classeReconfirmacao>=10) OR (idPautaCurso IS NULL AND classeReconfirmacao<=9)) AND obs not in ('cad', 'melh')".$condicao."", [$_SESSION["idEscolaLogada"], $this->idPAno, $idPDisciplina]);
        } 

        public function quanto($idPEscola, $idCurso, $classe, $turma, $tipoDado, $genero){
            if($classe<=9){
                $this->notaMinima=5;
            }
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

                if($tipoDado=="matriculados" && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao) && ($alunos->tipoEntrada=="novaMatricula" || $alunos->tipoEntrada==NULL || $alunos->tipoEntrada=="")){
                    
                    $contador++;
                }else if($tipoDado=="transfEntrada" && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao) && $alunos->tipoEntrada=="porTransferencia"){
                    
                    $contador++;
                }else if(($tipoDado=="excluidoF") && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao) && $alunos->estadoDesistencia=="F"){
                    
                    $contador++;

                }else if(($tipoDado=="matriculaAnulada") && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao) && $alunos->estadoDesistencia=="N"){
                    
                    $contador++;

                }else if($tipoDado=="avaliados" && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao) && ($alunos->estadoDesistencia!="N" && $alunos->estadoDesistencia!="D" && $alunos->estadoDesistencia!="F") ){

                    if($alunos->$campoTrimestreDB!=NULL && $alunos->$campoTrimestreDB!=0){
                        $contador++;
                    }

                }else if($tipoDado=="naoAvaliados" && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao)){

                    if($alunos->estadoDesistencia=="N" || $alunos->estadoDesistencia=="D" || $alunos->estadoDesistencia=="F" || $alunos->$campoTrimestreDB==NULL || $alunos->$campoTrimestreDB==0){
                        $contador++;
                    }

                }else if($tipoDado=="aprovados" && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao) && ($alunos->estadoDesistencia!="N" && $alunos->estadoDesistencia!="D" && $alunos->estadoDesistencia!="F")){
                    
                    if($this->trimestreApartir=="IV"){

                        if($this->tipoAproveitamento=="geral" && ($alunos->observacaoF=="A" || $alunos->observacaoF=="TR")){
                            $contador++;
                        }else if($this->tipoAproveitamento!="geral" && $alunos->$campoTrimestreDB>=$this->notaMinima){
                            $contador++;
                        }
                        
                    }else if($alunos->$campoTrimestreDB>=$this->notaMinima){
                        $contador++;
                    }

                }else if($tipoDado=="reprovados" && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao)  && ($alunos->estadoDesistencia!="N" && $alunos->estadoDesistencia!="D" && $alunos->estadoDesistencia!="F")){
                    
                    if($this->trimestreApartir=="IV"){

                        if($this->tipoAproveitamento=="geral" && ($alunos->observacaoF!="A" && $alunos->observacaoF!="TR") && ($alunos->mfT4!=NULL && $alunos->mfT4!=0)){
                            $contador++;
                        }else if($this->tipoAproveitamento!="geral" && $alunos->$campoTrimestreDB<$this->notaMinima && ($alunos->$campoTrimestreDB!=NULL && $alunos->$campoTrimestreDB!=0)){
                            $contador++;
                        }

                    }else if($alunos->$campoTrimestreDB<$this->notaMinima && ($alunos->$campoTrimestreDB!=NULL && $alunos->$campoTrimestreDB!=0)){
                        $contador++;
                    }
                }else if($tipoDado=="reprovadoGeral" && seComparador($turma, $alunos->nomeTurma) && seComparador($idPEscola, $alunos->idReconfEscola) && seComparador($genero, $alunos->sexoAluno) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao)  && ($alunos->estadoDesistencia!="N" && $alunos->estadoDesistencia!="D" && $alunos->estadoDesistencia!="F")){
                    
                    if($this->trimestreApartir=="IV"){

                        if($this->tipoAproveitamento=="geral" && ($alunos->observacaoF!="A" && $alunos->observacaoF!="TR")){
                            $contador++;
                        }else if($this->tipoAproveitamento!="geral" && $alunos->$campoTrimestreDB<$this->notaMinima){
                            $contador++;
                        }

                    }else if($alunos->$campoTrimestreDB<$this->notaMinima){
                        $contador++;
                    }
                }
            }
            foreach ($this->alunosTrans as $alunos) {
                if(($tipoDado=="matriculados" || $tipoDado=="transfSaida") && ($turma=="TOT" || $turma=="A") && seComparador($genero, $alunos->sexoAluno) && seComparador($idPEscola, $alunos->idTransfEscolaOrigem) && seComparador($classe, $alunos->classeReconfirmacao) && $this->seComparadorCurso($idCurso, $alunos->idMatCurso, $alunos->classeReconfirmacao)){                    
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
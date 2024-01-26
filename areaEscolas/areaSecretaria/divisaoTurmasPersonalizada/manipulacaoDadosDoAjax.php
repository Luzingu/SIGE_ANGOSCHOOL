<?php 
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();

            

            if($this->accao=="criarNovaTurma"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoTurmasPersonalizada"], [$_GET["classe"], $_GET["idPCurso"]]) ){
                    
                    $this->criarNovaTurma();
                     
                }
            }else if($this->accao=="trocarTurmaAluno"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoTurmasPersonalizada"], [$_GET["classe"], $_GET["idPCurso"]])){
                    $this->trocarTurmaAluno();
                } 
            }else if($this->accao=="pegarTotalInscritos"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoTurmasPersonalizada"], [$_GET["classe"], $_GET["idPCurso"]])){
                    $this->pegarTotalInscritos();
                } 
            }else if($this->accao=="resetTurmas"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoTurmasPersonalizada"], [$_GET["classe"], $_GET["idPCurso"]])){
                    $this->resetTurmas();
                } 
            }else if($this->accao=="trocarDisciplinasOpcoes"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoTurmasPersonalizada"], [])){
                    $this->trocarDisciplinasOpcoes();
                }
            }else if($this->accao=="gravarTurmas"){
                $this->listaturmas();
            }else if($this->accao=="actualizarListaAlunosTurmas"){
                $this->actualizarListaAlunosTurmas();
            }
        }

        private function criarNovaTurma(){
            $classe=$_GET["classe"];
            $idPCurso =  $_GET["idPCurso"];
            $periodo = $_GET["periodo"];
            $turno = $_GET["turno"];
            $linguaEstangeira = $_GET["linguaEstangeira"];
            $disciplinaOpcao = $_GET["disciplinaOpcao"];
            $numeroAlunosTurma = $_GET["numeroAlunosTurma"];
            $idPAnexo = $_GET["idPAnexo"];

            $condicaoAluno = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "escola.classeActualAluno"=>$classe, "escola.periodoAluno"=>$periodo, "escola.idMatAnexo"=>$idPAnexo, "reconfirmacoes.nomeTurma"=>"", "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idPCurso];

            $condicaoTurma=["classe"=>$classe, "idPEscola"=>$_SESSION['idEscolaLogada'], "idListaAno"=>$this->idAnoActual, "periodoTurma"=>$periodo, "idAnexoTurma"=>$idPAnexo, "idPNomeCurso"=>$idPCurso];

            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $condicaoAluno["escola.turnoAluno"]=$turno;
                $condicaoTurma["periodoT"]=$turno;
            }

            $newTurma = count($this->selectArray("listaturmas", ["idPTurma"], $condicaoTurma))+1;
            
            $keyAtributo="";
            if($linguaEstangeira!=""){
                $keyAtributo=$linguaEstangeira;
                $condicaoAluno["escola.idGestLinguaEspecialidade"]=$linguaEstangeira;

            }

            if($disciplinaOpcao!=""){
                if($keyAtributo==""){
                    $keyAtributo =$disciplinaOpcao;
                }else{
                    $keyAtributo .="-".$disciplinaOpcao;
                }
                $condicaoAluno["escola.idGestDisEspecialidade"]=$disciplinaOpcao;
            }

            $turno = ($turno=="" || $turno==NULL)?"Matinal":$turno;
            $this->inserir("listaturmas", "idPListaTurma", "nomeTurma, classe, idPNomeCurso, idPEscola, idListaAno, periodoTurma, idAnexoTurma, periodoT, atributoTurma", [$newTurma, $classe, $idPCurso, $_SESSION["idEscolaLogada"], $this->idAnoActual, $periodo, $idPAnexo, $turno, $keyAtributo], "sim", "nao", [["nomecursos", $idPCurso, "idPNomeCurso"]]);

            $arrayAluno = $this->selectArray("alunosmatriculados", ["idPMatricula", "grupo"], $condicaoAluno, ["escola", "reconfirmacoes"], "", [], ["dataNascAluno"=>-1]);

            $contador=0;
            foreach ($arrayAluno as $aluno){
  
                $contador++;
                if($contador<=$numeroAlunosTurma){                     
                  $this->editarItemObjecto("alunos_".$aluno["grupo"], "reconfirmacoes", "nomeTurma, designacaoTurma", [$newTurma,$newTurma], ["idPMatricula"=>$aluno["idPMatricula"]], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>$idPCurso]);
                }else{
                    break;
                }                    
                
            }            
            $this->afectarNovamenteAsTurmas($idPCurso, $classe);    
        }
        private function afectarNovamenteAsTurmas($idPCurso, $classe){

            $listaAlunosTurma = $this->selectArray("alunosmatriculados", ["idPMatricula", "reconfirmacoes.nomeTurma", "grupo"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION["idEscolaLogada"], "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idPCurso], ["escola", "reconfirmacoes"], "", [], [], $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe));

            $periodos = ["reg", "pos"];
            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $turnos = ["Matinal", "Vespertino", "Noturno"];    
            }else{
                $turnos = ["Automático"];
            }

            $listaTurmas = array();
            

            foreach(ordenar(listarItensObjecto($this->sobreEscolaLogada, "anexos"), "ordenacaoAnexo ASC") as $anexo){
                foreach($periodos as $periodo){

                    foreach($turnos as $turno){

                        if($turno!="Automático"){
                            $kapaya["periodoT"]=$turno;
                        }
                        foreach($this->selectArray("listaturmas", ["nomeTurma", "idPListaTurma", "periodoT"], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "classe"=>$classe, "idAnexoTurma"=>$anexo["idPAnexo"], "periodoTurma"=>$periodo, "idPNomeCurso"=>$idPCurso], [], "", [], ["nomeTurma"=>1]) as $lista){

                            $listaTurmas[] = array("idPListaTurma"=>$lista["idPListaTurma"], "nomeTurma"=>$lista["nomeTurma"]);
                        }
                    }
                }
            }

            $contador=0; 
            foreach ($listaTurmas as $turma) {

                $nomeTurma = gerenciadorNomesTurma($contador, $classe, $idPCurso, $this);
                $this->editar("listaturmas", "nomeTurma, designacaoTurma", [$nomeTurma, $nomeTurma], ["idPListaTurma"=>$turma["idPListaTurma"]]);

                foreach(array_filter($listaAlunosTurma, function ($mamale) use ($turma){
                    return $mamale["reconfirmacoes"]["nomeTurma"]==$turma["nomeTurma"]; }) as $tur){

                    $this->editar("alunos_".$tur["grupo"], "turma_".$_SESSION["idEscolaLogada"], [$nomeTurma], ["idPMatricula"=>$tur["idPMatricula"]]);

                    $this->editarItemObjecto("alunos_".$tur["grupo"], "reconfirmacoes", "nomeTurma, designacaoTurma", [$nomeTurma, $nomeTurma], ["idPMatricula"=>$tur["idPMatricula"]], ["idReconfEscola"=>$_SESSION['idEscolaLogada'], "idReconfAno"=>$this->idAnoActual, "idMatCurso"=>$idPCurso]);

                    $this->actuazalizarReconfirmacaAluno($tur["idPMatricula"]);
                }
                $contador++; 
            }
        }

        private function trocarTurmaAluno(){
            $idPMatricula = $_GET["idPMatricula"];
            $turma = $_GET["turma"];
            $grupoAluno = $_GET["grupoAluno"];            
            $classe = $_GET["classe"];
            $idPCurso = $_GET["idPCurso"];

            $sobreTurma = $this->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "classe"=>$classe, "nomeTurma"=>$turma, "idPNomeCurso"=>$idPCurso]);

            $designacaoTurma =valorArray($sobreTurma, "designacaoTurma");
            if($this->editarItemObjecto("alunos_".$grupoAluno, "reconfirmacoes", "nomeTurma, designacaoTurma", [$turma, $designacaoTurma], ["idPMatricula"=>$idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION["idEscolaLogada"], "idMatCurso"=>$idPCurso])=="sim"){

                $this->actuazalizarReconfirmacaAluno($idPMatricula);
                echo "VA turma foi actualizada com sucesso.";
            }else{
                echo "FNão foi possível actualizar a turma.";
            }
        }

        private function listaturmas(){
            $classe = $_GET["classe"];
            $idCurso = $_GET["idCurso"];
            $periodo = $_GET["periodo"];
            $idPAnexo = $_GET["idPAnexo"];
            $turno = $_GET["turno"];

            $condicaoTurma = ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "classe"=>$classe, "periodoTurma"=>$periodo, "idAnexoTurma"=>$idPAnexo, "idPNomeCurso"=>$idCurso];
            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $condicaoTurma["periodoT"]=$turno;
            }
            echo $this->selectJson("listaturmas", [], $condicaoTurma, [], "", [], ["nomeTurma"=>1]);
        }

        private function actualizarListaAlunosTurmas(){
            $idPCurso = $_GET["idPCurso"];
            $classe = $_GET["classe"];
            $periodo = $_GET["periodo"];
            $idPAnexo = $_GET["idPAnexo"];
            $turno = $_GET["turno"];

            $condicaoAluno = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.periodoAluno"=>$periodo, "escola.idMatAnexo"=>$idPAnexo, "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idPCurso];

            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $condicaoAluno["escola.turnoAluno"]=$turno;
            }
            echo $this->selectJson("alunosmatriculados", ["nomeAluno", "numeroInterno", "reconfirmacoes.nomeTurma", "sexoAluno", "dataNascAluno", "idPMatricula", "grupo", "fotoAluno"], $condicaoAluno, ["escola", "reconfirmacoes"], 1000, [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe));
        }


        private function pegarTotalInscritos(){
            $linguaEstangeira = $_GET["linguaEstangeira"];
            $disciplinaOpcao = $_GET["disciplinaOpcao"];
            $classe = $_GET["classe"];
            $idPCurso = $_GET["idPCurso"];
            $periodo = $_GET["periodo"];
            $idPAnexo = $_GET["idPAnexo"];
            $turno = $_GET["turno"];

            $condicaoAluno = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.classeActualAluno"=>$classe, "escola.periodoAluno"=>$periodo, "escola.idMatAnexo"=>$idPAnexo, "reconfirmacoes.nomeTurma"=>"", "escola.idMatCurso"=>$idPCurso];


            if($idPCurso==3){
              if($linguaEstangeira==20){
                $linguaEstangeira=22;
              }else if($linguaEstangeira==21){
                $linguaEstangeira=23;
              }            
            }

            if($linguaEstangeira!=""){
                $condicaoAluno["escola.idGestLinguaEspecialidade"]=$linguaEstangeira;
            }

            if($disciplinaOpcao!=""){
                $condicaoAluno["escola.idGestDisEspecialidade"]=$disciplinaOpcao;
            }

            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $condicaoAluno["escola.turnoAluno"]=$turno;
            }

            echo count($this->selectArray("alunosmatriculados", ["idPMatricula"], $condicaoAluno, ["escola", "reconfirmacoes"], 10000, [], [], $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe)));
        }

        private function resetTurmas(){
            $classe = $_GET["classe"];
            $idPCurso = $_GET["idPCurso"];
            $periodo = $_GET["periodo"];
            $idPAnexo = $_GET["idPAnexo"];
            $turno = $_GET["turno"];

            $condicaoAluno = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "escola.classeActualAluno"=>$classe, "escola.periodoAluno"=>$periodo, "escola.idMatAnexo"=>$idPAnexo, "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.nomeTurma"=>array('$ne'=>""), "escola.idMatCurso"=>$idPCurso];

            $condicaoTurma=["classe"=>$classe, "idPEscola"=>$_SESSION['idEscolaLogada'], "idListaAno"=>$this->idAnoActual, "periodoTurma"=>$periodo, "idAnexoTurma"=>$idPAnexo, "idPNomeCurso"=>$idPCurso];

            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $condicaoAluno["escola.turnoAluno"]=$turno;
                $condicaoTurma["periodoT"]=$turno;
            }

            $arrayAluno = $this->selectArray("alunosmatriculados", ["idPMatricula", "grupo"], $condicaoAluno, ["escola", "reconfirmacoes"], "", [], [], $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe));

            foreach ($arrayAluno as $aluno){  
                $this->editarItemObjecto("alunos_".$aluno["grupo"], "reconfirmacoes", "nomeTurma, designacaoTurma", ["", ""], ["idPMatricula"=>$aluno["idPMatricula"]], ["idReconfAno"=>$this->idAnoActual, "idMatCurso"=>$idPCurso, "idReconfEscola"=>$_SESSION['idEscolaLogada']]); 
                $this->actuazalizarReconfirmacaAluno($aluno["idPMatricula"]);           
            }
            $this->excluir("listaturmas", $condicaoTurma);
        }

        function trocarDisciplinasOpcoes(){
            $idPMatricula = isset($_POST["idPMatricula"])?$_POST["idPMatricula"]:"";
            $idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";
            $classe = isset($_POST["classe"])?$_POST["classe"]:"";
            $grupo = isset($_POST["grupo"])?$_POST["grupo"]:"";
            $periodo = isset($_POST["periodo"])?$_POST["periodo"]:"";
            $discEspecialidade = isset($_POST["discEspecialidade"])?$_POST["discEspecialidade"]:"";
            $lingEspecialidade = isset($_POST["lingEspecialidade"])?$_POST["lingEspecialidade"]:"";

            $gerenciador_matriculas = listarItensObjecto($this->sobreEscolaLogada, "gerencMatricula", ["classe=".$classe, "periodoClasse=".$periodo, "idCurso=".$idPCurso]);
 
            $sobreCursoAluno = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$idPCurso]);
            
            if(valorArray($sobreCursoAluno, "tipoCurso")=="geral" && ($discEspecialidade==NULL || $discEspecialidade=="")){
                echo "FDeves seleccionar a disciplina de opção do(a) aluno(a).";
            }else if(($lingEspecialidade==NULL || $lingEspecialidade=="") && $classe>=7){
                echo "FDeves seleccionar a língua de opção do(a) aluno(a).";
            }else if(!seTemValorNoArray(explode(",", valorArray($gerenciador_matriculas, "idsLinguasEtrang")), $lingEspecialidade) ){
                echo "FSelecciones outra língua de opção.";
            }else if(!seTemValorNoArray(explode(",", valorArray($gerenciador_matriculas, "idsDisciplOpcao")), $discEspecialidade) && valorArray($sobreCursoAluno, "tipoCurso")=="geral"){
                echo "FSelecciones outra disciplina de opção.";
            }else{
                $this->seleccionadorEspecialidades($idPCurso, $classe, $lingEspecialidade, $discEspecialidade, $idPMatricula, $grupo);
                $this->atualizarTurma($idPMatricula, $classe, $idPCurso, "", $grupo);

                $alunosSemTurma = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "escola.idGestLinguaEspecialidade", "fotoAluno", "escola.idGestDisEspecialidade", "grupo"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "escola.classeActualAluno"=>$classe, "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.periodoAluno"=>$periodo, "reconfirmacoes.nomeTurma"=>"", "escola.idMatCurso"=>$idPCurso], ["escola", "reconfirmacoes"], 10000, [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe));
                $alunosSemTurma=$this->anexarTabela2($alunosSemTurma, "nomedisciplinas", "escola", "idPNomeDisciplina", "idGestLinguaEspecialidade");

                echo json_encode($alunosSemTurma);
            }
        }

        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
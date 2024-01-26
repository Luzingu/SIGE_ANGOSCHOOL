<?php 
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php'); 
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            

            if($this->accao=="dividirTurmas"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoTurmasPorIdade"], [$_GET["classe"], $_GET["idPCurso"]])){

                    $this->dividirTurmas();                         
                }
            }else if($this->accao=="trocarTurmaAluno"){
                
                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoTurmasPorIdade"], [$_GET["classe"], $_GET["idPCurso"]])){
                    $this->trocarTurmaAluno();
                } 
            }else if($this->accao=="gravarTurmas"){
                $this->listaturmas();
            }else if($this->accao=="actualizarListaAlunosTurmas"){
                $this->actualizarListaAlunosTurmas();
            }
        }
        private function trocarTurmaAluno(){
            $idPMatricula = $_GET["idPMatricula"];
            $grupoAluno = $_GET["grupoAluno"];
            $turma = $_GET["turma"];
            $classe = $_GET["classe"];
            $idPCurso = $_GET["idPCurso"];

            $sobreTurma = $this->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "classe"=>$classe, "nomeTurma"=>$turma, "idPNomeCurso"=>$idPCurso]);

            $designacaoTurma =valorArray($sobreTurma, "designacaoTurma");
            if($this->editarItemObjecto("alunos_".$grupoAluno, "reconfirmacoes", "nomeTurma, designacaoTurma", [$turma, $designacaoTurma], ["idPMatricula"=>$idPMatricula], ["idReconfAno"=>$this->idAnoActual, "idMatCurso"=>$idPCurso, "idReconfEscola"=>$_SESSION["idEscolaLogada"]])=="sim"){

                $this->actuazalizarReconfirmacaAluno($idPMatricula);
                echo "VA turma foi actualizada com sucesso.";
            }else{
                echo "FNão foi possível actualizar a turma.";
            }
        }

        private function dividirTurmas(){
            $classe=$_GET["classe"];
            $posTurmaDividir = $_GET["posTurmaDividir"];
            $comeco = $_GET["comeco"];
            $final = $_GET["final"];
            $idPCurso =  $_GET["idPCurso"];
            $periodo = $_GET["periodo"];
            $turno = $_GET["turno"];
            $idPAnexo = $_GET["idPAnexo"];
            $turmasFaltasParaDividir = $_GET["turmasFaltasParaDividir"];

            $condicaoAlunos = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.periodoAluno"=>$periodo, "escola.idMatAnexo"=>$idPAnexo, "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idPCurso];

            $condicaoTurma=["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "classe"=>$classe, "periodoTurma"=>$periodo, "idAnexoTurma"=>$idPAnexo, "idPNomeCurso"=>$idPCurso];

            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $condicaoAlunos["escola.turnoAluno"]=$turno;
                $condicaoTurma["periodoT"]=$turno;
            }

            $arrayAlunos = $this->selectArray("alunosmatriculados", ["idPMatricula", "grupo", "dataNascAluno"], $condicaoAlunos, ["escola", "reconfirmacoes"], 1000, [], ["dataNascAluno"=>-1]);

            if((int) $comeco==1){ // Para Fazer essa instrucao somente uma vez 
                $this->excluir("listaturmas", $condicaoTurma);
            }

            $turno = ($turno=="" || $turno==NULL)?"Matinal":$turno;
            $this->inserir("listaturmas", "idPListaTurma", "nomeTurma, classe, idPNomeCurso, idPEscola, idListaAno, periodoTurma, idAnexoTurma, periodoT, atributoTurma", [($posTurmaDividir-1), "".$classe."", $idPCurso, $_SESSION["idEscolaLogada"], $this->idAnoActual, $periodo, $idPAnexo, $turno, null], "sim", "nao", [["nomecursos", $idPCurso, "idPNomeCurso"]]);

            $i=1;
            foreach ($arrayAlunos as $aluno){
                    
                if($i>=$comeco && $i<=$final){

                 $this->editarItemObjecto("alunos_".$aluno["grupo"], "reconfirmacoes", "nomeTurma, designacaoTurma", [($posTurmaDividir-1), ($posTurmaDividir-1)], ["idPMatricula"=>$aluno["idPMatricula"]], ["idReconfAno"=>$this->idAnoActual, "idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>$idPCurso]);
                }
                $i++;
            }
            if($turmasFaltasParaDividir==0){
                unset($_SESSION['optTurmas']);
                unset($_SESSION['turmaInicial']);
                unset($_SESSION['turmasEscola']);
                $this->afectarNovamenteAsTurmas($idPCurso, $classe);
            }            
        }

        
        private function afectarNovamenteAsTurmas($idPCurso, $classe){

            $listaAlunosTurma = $this->selectArray("alunosmatriculados", ["idPMatricula", "reconfirmacoes.nomeTurma", "grupo"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION["idEscolaLogada"], "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.nomeTurma"=>array('$ne'=>""), "escola.idMatCurso"=>$idPCurso], ["escola", "reconfirmacoes"], 1000, [], array(), $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe));

            $periodos = ["reg", "pos"];
            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $turnos = ["Matinal", "Vespertino", "Noturno"];    
            }else{
                $turnos = ["Automático"];
            }

            $listaTurmas = array();
            

            foreach(ordenar(listarItensObjecto($this->sobreEscolaLogada, "anexos"), "ordenacaoAnexo ASC") as $anexo){
                foreach($periodos as $periodo){

                    $kapaya = ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "classe"=>$classe, "idAnexoTurma"=>$anexo["idPAnexo"], "periodoTurma"=>$periodo, "idPNomeCurso"=>$idPCurso];

                    foreach($turnos as $turno){

                        if($turno!="Automático"){
                            $kapaya["periodoT"]=$turno;
                        }
                        foreach($this->selectArray("listaturmas", ["nomeTurma", "idPListaTurma", "periodoT"], $kapaya, [], "", [], ["nomeTurma"=>1]) as $lista){

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

        private function listaturmas(){
            $classe = $_GET["classe"];
            $idCurso = $_GET["idCurso"];
            $periodo = $_GET["periodo"];
            $turno = $_GET["turno"];
            $idPAnexo = $_GET["idPAnexo"];

            $condicaoTurma = ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "classe"=>$classe, "periodoTurma"=>$periodo, "idAnexoTurma"=>$idPAnexo, "idPNomeCurso"=>$idCurso];
            if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
                $condicaoTurma["periodoT"]=$turno;
            }
            echo $this->selectJson("listaturmas", [], $condicaoTurma, [], 100, [], ["nomeTurma"=>1]);
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
            echo $this->selectJson("alunosmatriculados", ["nomeAluno", "numeroInterno", "reconfirmacoes.nomeTurma", "sexoAluno", "fotoAluno", "dataNascAluno", "idPMatricula", "grupo"], $condicaoAluno, ["escola", "reconfirmacoes"], 1000, [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe));
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
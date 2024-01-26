<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();

            $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->conDb("inscricao");

            if($this->accao=="dividirGrupos"){

                

                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoGrupos"])){

                    $this->editar("gestorvagas", "estadoTransicaoCurso", ["F"], ["idGestCurso"=>$idPCurso, "idGestAno"=>$this->idAnoActual, "idGestEscola"=>$_SESSION["idEscolaLogada"]]);
                    $this->dividirGrupos();                        
                    
                }
            }else if($this->accao=="trocarGrupoAluno"){
                if($this->verificacaoAcesso->verificarAcesso("", ["divisaoGrupos"])){
                    
                    $this->editar("gestorvagas", "estadoTransicaoCurso", ["F"], ["idGestCurso"=>$idPCurso, "idGestAno"=>$this->idAnoActual, "idGestEscola"=>$_SESSION["idEscolaLogada"]]);
                    $this->trocarGrupoAluno();
                } 
            }else if($this->accao=="gravarGrupos"){
                $this->listarGrupos();
            }else if($this->accao=="actualizarListaAlunosGrupos"){
                $this->actualizarListaAlunosGrupos();
            }
        }

        private function dividirGrupos(){
            $grupoNumero = $_GET["grupoNumero"];
            $comeco = $_GET["comeco"];
            $final = $_GET["final"];
            $idPCurso =  $_GET["idPCurso"];
            $gruposFaltasParaDividir = $_GET["gruposFaltasParaDividir"];
            

            if((int) $comeco==1){ // Para Fazer essa instrucao somente uma vez
                $this->excluir("lista_grupos", ["idListaCurso"=>$idPCurso, "idListaAno"=>$this->idAnoActual, "idListaEscola"=>$_SESSION["idEscolaLogada"]]);

                $this->excluirItemObjecto("alunos", "grupo", ["idAlunoEscola"=>$_SESSION["idEscolaLogada"], "idAlunoAno"=>$this->idAnoActual], ["idGrupoCurso"=>$idPCurso]);                    
            }

            $this->inserir("lista_grupos", "idPListaGrupo", "idListaAno, idListaEscola, idListaCurso, numeroGrupo", [$this->idAnoActual, $_SESSION["idEscolaLogada"], $idPCurso, $grupoNumero]);

            $i=1;

            foreach ($this->selectArray("alunos", ["idPAluno"], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION["idEscolaLogada"], "inscricao.idInscricaoCurso"=>$idPCurso], ["inscricao"],"", [], ["dataNascAluno"=>-1]) as $aluno){
                
                if($i>=$comeco && $i<=$final){
                    $this->inserirObjecto("alunos", "grupo", "idPGrupo", "idGrupoAluno, idGrupoEscola, idGrupoCurso, grupoNumero, idGrupoAno", [$aluno["idPAluno"], $_SESSION["idEscolaLogada"], $idPCurso, $grupoNumero, $this->idAnoActual], ["idPAluno"=>$aluno["idPAluno"]]);
                }
                $i++;
            }
        }

        private function trocarGrupoAluno(){
            $idPAluno = $_GET["idPAluno"];
            $numeroGrupo = $_GET["numeroGrupo"];
            $idPCurso = $_GET["idPCurso"];
            
            $this->editarItemObjecto("alunos", "grupo", "grupoNumero", [$numeroGrupo], ["idPAluno"=>$idPAluno, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$this->idAnoActual],["idGrupoCurso"=>$idPCurso], "VO grupo foi alterado com sucesso.", "FNão foi possível alterar o grupo."); 
        }

        private function listarGrupos(){
            $idCurso = $_GET["idPCurso"];

             echo $this->selectJson("lista_grupos", [], ["idListaAno"=>$this->idAnoActual, "idListaCurso"=>$idCurso, "idListaEscola"=>$_SESSION["idEscolaLogada"]], [],"", [], ["numeroGrupo"=>1]);
        }

        private function actualizarListaAlunosGrupos(){
            $idPCurso = $_GET["idPCurso"];

            echo $this->selectJson("alunos", [], ["idAlunoAno"=>$this->idAnoActual, "idAlunoEscola"=>$_SESSION["idEscolaLogada"], "grupo.idGrupoCurso"=>$idPCurso], ["grupo"], "", [], ["nomeAluno"=>1]);
            
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
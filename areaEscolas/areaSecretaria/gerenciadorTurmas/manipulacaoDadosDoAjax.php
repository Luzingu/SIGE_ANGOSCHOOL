<?php 
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        
        function __construct($caminhoAbsoluto){
            parent::__construct();
            if($this->accao=="manipularGerenciadorTurma"){

                $this->classe = isset($_POST["classe"])?$_POST["classe"]:"";
                $this->idPCurso = isset($_POST["idPCurso"])?$_POST["idPCurso"]:"";
                $this->idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:"";
                
                if($this->verificacaoAcesso->verificarAcesso("", ["gerenciadorTurmas"], [$this->classe, $this->idPCurso])){
                    $this->alterarGerenciador();
                } 
            }
        }

        private function alterarGerenciador(){
            $periodoT = filter_input(INPUT_POST, "periodoT", FILTER_SANITIZE_STRING);
            $designacaoTurma = trim(filter_input(INPUT_POST, "designacaoTurma", FILTER_SANITIZE_STRING));
            $numeroTurma = filter_input(INPUT_POST, "numeroTurma", FILTER_SANITIZE_NUMBER_INT);
            $listaProfessor = filter_input(INPUT_POST, "listaProfessor", FILTER_SANITIZE_NUMBER_INT);
            $idPListaTurma = filter_input(INPUT_POST, "idPListaTurma", FILTER_SANITIZE_NUMBER_INT);

            
            $numeroPauta = filter_input(INPUT_POST, "numeroPauta", FILTER_SANITIZE_STRING);
            
            $sobreLista = $this->selectArray("listaturmas", [], ["idPListaTurma"=>$idPListaTurma]);

            $outrosCamposAfectar =", periodoT, numeroSalaTurma, idCoordenadorTurma, designacaoTurma";
            if($this->idPAno!=$this->idAnoActual){
                $outrosCamposAfectar="";
            }            

            if($this->editar("listaturmas", "numeroPauta".$outrosCamposAfectar, [$numeroPauta, $periodoT, $numeroTurma, $listaProfessor, $designacaoTurma], ["idPListaTurma"=>$idPListaTurma])=="sim"){

                $this->editar("horario", "periodoT, numeroSalaTurma", [$periodoT, $numeroTurma], ["classe"=>valorArray($sobreLista, "classe"), "turma"=>valorArray($sobreLista, "nomeTurma"), "idHorAno"=>$this->idPAno, "idPEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>valorArray($sobreLista, "idPNomeCurso")]);


                if(valorArray($this->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional" && valorArray($sobreLista, "periodoT")!=$periodoT && $this->idPAno==$this->idAnoActual){
                    $this->trocarDesignacoesDasTurmas($periodoT, valorArray($sobreLista, "classe"), valorArray($sobreLista, "nomeTurma"), valorArray($sobreLista, "idPNomeCurso"));
                }

                if(valorArray($sobreLista, "designacaoTurma")!=$designacaoTurma && $this->idPAno==$this->idAnoActual){
                    $this->trocarDesignacoesDasTurmas($designacaoTurma, valorArray($sobreLista, "classe"), valorArray($sobreLista, "nomeTurma"), valorArray($sobreLista, "idPNomeCurso"));
                }

                $array = $this->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idListaAno"=>$this->idPAno, "classe"=>$this->classe, "idPNomeCurso"=>$this->idPCurso]);
                $array = $this->anexarTabela($array, "entidadesprimaria", "idPEntidade", "idCoordenadorTurma");
                echo json_encode($array);

            }else{
                echo "FNão Foi Possível Alterar os Dados.";
            }
        }

        private function trocarDesignacoesDasTurmas($novaDesignacao, $classe, $nomeTurma, $idListaCurso){

            $this->editar("horario", "designacaoTurmaHor", [$novaDesignacao], ["classe"=>$classe, "turma"=>$nomeTurma, "idHorAno"=>$this->idAnoActual, "idPEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$idListaCurso]);

            $this->editar("divisaoprofessores", "designacaoTurmaDiv", [$novaDesignacao], ["classe"=>$classe, "nomeTurmaDiv"=>$nomeTurma, "idDivAno"=>$this->idAnoActual, "idDivEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$idListaCurso]);

            foreach($this->alunosPorTurma($idListaCurso, $classe, $nomeTurma, $this->idPAno, array(), ["idPMatricula", "grupo"]) as $a){
                
                $this->editarItemObjecto("alunos_".$a["grupo"], "reconfirmacoes", "designacaoTurma", [$novaDesignacao], ["idPMatricula"=>$a["idPMatricula"]], ["idReconfEscola"=>$_SESSION['idEscolaLogada'], "idMatCurso"=>$idListaCurso, "idReconfAno"=>$this->idPAno]);
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
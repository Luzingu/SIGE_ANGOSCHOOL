<?php 
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        private $classe ="";
        private $idPCurso="";

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:"";
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:"";
            $this->dados = isset($_GET["dados"])?$_GET["dados"]:"";
            $this->dados = explode(",", $this->dados);

            $this->semestreActivo = retornarSemestreActivo($this, $this->idPCurso, $this->classe);

            if($this->accao=="gravarHorarios"){

                if($this->verificacaoAcesso->verificarAcesso("", ["horarioTurmas"], [])){
                    $this->gravarHorarios();  
                    
                }

            }else if($this->accao=="manipularHorario"){
                if($this->verificacaoAcesso->verificarAcesso("", ["horarioTurmas"], [$this->classe, $this->idPCurso])){
                    $this->manipularHorario();
                    
                }
            }
        }

        private function gravarHorarios(){

            foreach ($this->selectArray("listaturmas", [], ["classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "nomeTurma"=>$this->turma, "idPNomeCurso"=>$this->idPCurso]) as $turma) {

                for ($dia=1; $dia<=6; $dia++){
                    for($tempo=1; $tempo<=6; $tempo++){

                        $chave = $_SESSION["idEscolaLogada"]."-".$this->idAnoActual."-".$this->idPCurso."-".$this->classe."-".$turma["nomeTurma"]."-".$dia."-".$tempo."-".$this->semestreActivo;

                        $this->inserir("horario", "idPHorario", "semestre, classe, turma, designacaoTurmaHor, periodoTurma, periodoT, numeroSalaTurma, dia, tempo, idHorAno, chaveH", [$this->semestreActivo, $this->classe, $turma["nomeTurma"], $turma["designacaoTurma"], $turma["periodoTurma"], nelson($turma, "periodoT"), nelson($turma, "numeroSalaTurma"), $dia, $tempo, $this->idAnoActual, $chave], "sim", "nao",[["nomecursos", $this->idPCurso, "idPNomeCurso"], ["escolas", $_SESSION['idEscolaLogada'], "idPEscola"]]);
                    }
                }
               
                foreach ($this->disciplinas($this->idPCurso, $this->classe, $turma["periodoTurma"], "", array(), [51, 140], ["idPNomeDisciplina", "disciplinas.semestreDisciplina"]) as $disciplina){

                    
                    $chave = $_SESSION["idEscolaLogada"]."-".$this->idAnoActual."-".$this->idPCurso."-".$this->classe."-".$turma["nomeTurma"]."-".$disciplina["idPNomeDisciplina"]."-".$disciplina["disciplinas"]["semestreDisciplina"];
                  
                    $this->inserir("divisaoprofessores", "idPDivisao", "classe, nomeTurmaDiv, designacaoTurmaDiv, idDivAno, chavePrincipal, estadoComissaoExame, semestre, periodoTurmaDiv", [$this->classe, $turma["nomeTurma"], $turma["designacaoTurma"], $this->idAnoActual, $chave, "V", $disciplina["disciplinas"]["semestreDisciplina"], $turma["periodoTurma"]], "sim", "nao", [["nomecursos", $this->idPCurso, "idPNomeCurso"], ["escolas", $_SESSION['idEscolaLogada'], "idPEscola"], ["nomedisciplinas", $disciplina["idPNomeDisciplina"], "idPNomeDisciplina"]]);                        
                }                  
            }
            $this->listar();
        }

        private function manipularHorario(){

            $sobreTurma = $this->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idAnoActual, "classe"=>$this->classe, "nomeTurma"=>$this->turma, "idPNomeCurso"=>$this->idPCurso]);

            $gerenciador_periodo = listarItensObjecto($this->sobreEscolaLogada, "gerencPerido", ["idGerPerAno=".$this->idAnoActual, "periodoGerenciador=".valorArray($sobreTurma, "periodoT")]);

            //Actualizar Divisão de Professores
            $listaDivisaoProfessores = $_GET['listaDivisaoProfessores'];
            $classeModoDocencia = $_GET['classeModoDocencia'];
            $listaDivisaoProfessores = json_decode($listaDivisaoProfessores);

            $contadorLinhas=0;
            $idPEntidadePrimeiraLinha=0;
            $avaliacoesContinuasPrimLinha="I";
            foreach($listaDivisaoProfessores as $a){
                $idDivEntidade = $a->idPEntidade;
                $avaliacoesContinuas = $a->avalContinuas;
                $contadorLinhas++;
                if($contadorLinhas==1){
                  $idPEntidadePrimeiraLinha = $a->idPEntidade;
                  $avaliacoesContinuasPrimLinha=$a->avalContinuas;
                }
                //Para classes de monodociência, considera-se o professor da primeira disciplina para todas outras disciplinas.
                if($contadorLinhas>=2 && $this->classe<=$classeModoDocencia){
                  $idDivEntidade=$idPEntidadePrimeiraLinha;
                  $avaliacoesContinuas = $avaliacoesContinuasPrimLinha;
                }
                //Reiniciar variaveis
                $this->editar("divisaoprofessores", "idPEntidade, nomeEntidade", [null, null], ["idPDivisao"=>$a->idPDivisao]); 

                $this->editar("divisaoprofessores", "idPresidenteComissaoExame, avaliacoesContinuas", [$idDivEntidade, $avaliacoesContinuas], ["idPDivisao"=>$a->idPDivisao], "sim", "nao", [["entidadesprimaria", $idDivEntidade, "idPEntidade"]]);                
            }

            for($tempo=1; $tempo<=valorArray($gerenciador_periodo, "numeroTempos"); $tempo++){

                for($dia=1; $dia<=valorArray($gerenciador_periodo, "numeroDias"); $dia++){

                    //Reiniciar variáveis...

                    $this->editar("horario", "idPNomeDisciplina, nomeDisciplina, abreviacaoDisciplina1, abreviacaoDisciplina2, idPEntidade, nomeEntidade", [null, null, null, null, null, null], ["classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idHorAno"=>$this->idAnoActual, "turma"=>$this->turma, "dia"=>$dia, "tempo"=>$tempo, "semestre"=>$this->semestreActivo, "idPNomeCurso"=>$this->idPCurso]);

                    $idPNomeDisciplina = $this->retornarDisciplinas($tempo, $dia);
                    $idPEntidade = $this->selectUmElemento("divisaoprofessores", "idPEntidade", ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$this->idAnoActual, "classe"=>$this->classe, "nomeTurmaDiv"=>$this->turma, "idPNomeDisciplina"=>$idPNomeDisciplina, "idPNomeCurso"=>$this->idPCurso]);


                    $this->editar("horario", "idPNomeDisciplina", [$idPNomeDisciplina], ["classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idHorAno"=>$this->idAnoActual, "turma"=>$this->turma, "dia"=>$dia, "tempo"=>$tempo, "semestre"=>$this->semestreActivo, "idPNomeCurso"=>$this->idPCurso], "sim", "nao", [["nomedisciplinas", $idPNomeDisciplina, "idPNomeDisciplina"], ["entidadesprimaria", $idPEntidade, "idPEntidade"]]);
                }
            }
            $this->listar();
        }

        private function retornarDisciplinas($tempo, $dia){
            $retorno="";
            foreach ($this->dados as $dados) {
                $explode = explode("=>", $dados);
                $idPNomeDisciplina = isset($explode[1])?$explode[1]:NULL;
                if(trim($explode[0])==$tempo."-".$dia){
                    $retorno = $idPNomeDisciplina;
                    break;
                }
            }
            return $retorno;
        }

        private function listar(){

            $retorno[0] = $this->selectArray("horario", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idHorAno"=>$this->idAnoActual, "classe"=>$this->classe, "turma"=>$this->turma, "semestre"=>$this->semestreActivo, "idPNomeCurso"=>$this->idPCurso]);

            $periodo = retornarPeriodoTurma($this, $this->idPCurso, $this->classe, $this->turma, $this->idAnoActual);
            $disciplinas = $this->disciplinas($this->idPCurso, $this->classe, $periodo, "", array(), [51, 140], ["idPNomeDisciplina"]);

            $retorno[1] = array();
            foreach($this->selectArray("divisaoprofessores", ["idPDivisao", "idPNomeDisciplina", "idPEntidade", "avaliacoesContinuas", "nomeDisciplina"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$this->idAnoActual, "classe"=>$this->classe, "nomeTurmaDiv"=>$this->turma, "semestre"=>$this->semestreActivo, "idPNomeCurso"=>$this->idPCurso]) as $d)
            {
                if (count(array_filter($disciplinas, function ($mamale) use ($d){
                    return ($mamale["idPNomeDisciplina"]==$d["idPNomeDisciplina"]) ;
                  })) > 0)
                  $retorno[1][] = $d;
            }
            echo json_encode($retorno);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
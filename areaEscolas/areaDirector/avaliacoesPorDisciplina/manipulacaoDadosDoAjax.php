<?php
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct(){
            parent::__construct();

            $this->idPDisciplina = isset($_POST["idPDisciplina"])?$_POST["idPDisciplina"]:"";
            $this->conjuntoDados = isset($_POST["conjuntoDados"])?$_POST["conjuntoDados"]:"";
            $this->conjuntoDadosExt = isset($_POST["conjuntoDadosExt"])?$_POST["conjuntoDadosExt"]:"";
            $this->idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:"";
            $this->idPNomeCurso = isset($_POST["idPNomeCurso"])?$_POST["idPNomeCurso"]:"";
            $this->idPNomeDisciplina = isset($_POST["idPNomeDisciplina"])?$_POST["idPNomeDisciplina"]:"";

            $this->escolaAfectar = isset($_POST["escolaAfectar"])?$_POST["escolaAfectar"]:"";
            $this->cursoAfectar = isset($_POST["cursoAfectar"])?$_POST["cursoAfectar"]:"";
            $this->classeAfectar = isset($_POST["classeAfectar"])?$_POST["classeAfectar"]:"";
            $this->periodoAfectar = isset($_POST["periodoAfectar"])?$_POST["periodoAfectar"]:"";
            $this->disciplinasAfectar = isset($_POST["disciplinasAfectar"])?$_POST["disciplinasAfectar"]:"";

            if($_SESSION["idEscolaLogada"]==4){
                $this->conDb("teste");
            }
            if($this->accao=="actualizarAvaliacoes"){

                $array = $this->selectArray("nomecursos", ["cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$this->idPNomeCurso], ["cursos"], 1);
                $this->tipoCurriculo = valorArray($array, "tipoCurriculo", "cursos");

                if (valorArray($array, "curriculo1") == 0 || valorArray($array, "curriculo1") == $_SESSION['idEscolaLogada'] || valorArray($array, "curriculo2") == $_SESSION['idEscolaLogada'] || valorArray($array, "curriculo3") == $_SESSION['idEscolaLogada'])
                {
                    if($this->verificacaoAcesso->verificarAcesso("", ["avaliacoesPorDisciplina"])){
                        $this->actualizarAvaliacoes();
                    }
                }
                else
                    echo "FNão tens permissão de alterar as avaliações.";
            }

        }

        private function actualizarAvaliacoes(){


            if($this->disciplinasAfectar==0){
                $condicaoDisciplina = array("idPNomeDisciplina"=>array('$gte'=>0));
            }else{
                $condicaoDisciplina = array("idPNomeDisciplina"=>$this->idPNomeDisciplina);
            }

            $muluay = $this->selectArray("nomedisciplinas", [], ["idPNomeDisciplina"=>$this->idPNomeDisciplina, "disciplinas.idPDisciplina"=>$this->idPDisciplina], ["disciplinas"]);

            $condicaoDisciplina2["idDiscCurriculo"]=$this->tipoCurriculo;

            if($this->cursoAfectar==1){
                $condicaoDisciplina2["idDiscCurso"]=$this->idPNomeCurso;
            }
            if($this->classeAfectar==1){
                $condicaoDisciplina2["classeDisciplina"]=valorArray($muluay, "classeDisciplina", "disciplinas");
            }
            if($this->periodoAfectar==1){
                $condicaoDisciplina2["periodoDisciplina"]=valorArray($muluay, "periodoDisciplina", "disciplinas");
            }
            $this->editarItemObjecto("nomedisciplinas", "disciplinas", "camposAvaliacoes-".$this->idPAno.", camposAvaliacoesExt-".$this->idPAno, [$this->conjuntoDados, $this->conjuntoDadosExt], $condicaoDisciplina, $condicaoDisciplina2);

            echo $this->selectJson("nomedisciplinas", [], ["disciplinas.idDiscCurriculo"=>$this->tipoCurriculo, "disciplinas.idDiscCurso"=>$this->idPNomeCurso], ["disciplinas"], "", [], ["disciplinas.periodoDisciplina"=>1, "disciplinas.classeDisciplina"=>1]);
        }

    }
    new manipulacaoDadosDoAjaxInterno();
?>

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
            $this->classe = isset($_POST["classe"])?$_POST["classe"]:"";

            $this->escolaAfectar = isset($_POST["escolaAfectar"])?$_POST["escolaAfectar"]:"";
            $this->cursoAfectar = isset($_POST["cursoAfectar"])?$_POST["cursoAfectar"]:"";
            $this->classeAfectar = isset($_POST["classeAfectar"])?$_POST["classeAfectar"]:"";

            $array = $this->selectArray("nomecursos", ["cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$this->idPNomeCurso], ["cursos"], 1);
            $this->idEscola = "";
            if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo1")
                $this->idEscola = valorArray ($array, "curriculo1");
            else if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo2")
                $this->idEscola = valorArray ($array, "curriculo2");
            else if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo3")
                $this->idEscola = valorArray ($array, "curriculo3");

            if($this->accao=="actualizarAvaliacoes"){
                if ($this->idEscola == $_SESSION["idEscolaLogada"] || $this->idEscola == 0)
                {
                    if($this->verificacaoAcesso->verificarAcesso("", ["cabecalhosAvaliacoesPorClasse"]))
                        $this->actualizarAvaliacoes();
                }
                else
                    echo "FNão tens permissão de alterar os dados.";
            }

        }

        private function actualizarAvaliacoes(){

            $condicaoCurso["idPNomeCurso"]=array('$gte'=>0);
            if($this->cursoAfectar==1){
                $condicaoCurso["idPNomeCurso"]=$this->idPNomeCurso;
            }else if($this->cursoAfectar==2){
                $tipoCurso = $this->selectUmElemento("nomecursos", "tipoCurso", ["idPNomeCurso"=>$this->idPNomeCurso]);
                $luzl=array();
                foreach($this->selectArray("nomecursos", ["idPNomeCurso"], ["tipoCurso"=>$tipoCurso]) as $curso){
                    $luzl[]=intval($curso["idPNomeCurso"]);
                }
                $condicaoCurso["idPNomeCurso"]=array('$in'=>$luzl);
            }
            $condicaoCurso2["idCursoEscola"]=$this->idEscola;

            $sobreCurso = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPNomeCurso]);

            $campos ="";
            $valores=array();
            if($this->classeAfectar==1){
                $campos ="cabecalhoAvaliacoes".$this->classe."-".$this->idPAno.", cabecalhoAvaliacoesExt".$this->classe."-".$this->idPAno;
                $valores = [$this->conjuntoDados, $this->conjuntoDadosExt];
            }else{
                foreach(listarItensObjecto($sobreCurso, "classes") as $classe){
                    if($campos!=""){
                        $campos .=",";
                    }
                    $campos .="cabecalhoAvaliacoes".$classe["identificador"]."-".$this->idPAno.", cabecalhoAvaliacoesExt".$classe["identificador"]."-".$this->idPAno;
                    $valores[]=$this->conjuntoDados;
                    $valores[]=$this->conjuntoDadosExt;
                }
            }
            $this->editarItemObjecto("nomecursos", "cursos", $campos, $valores, $condicaoCurso, $condicaoCurso2);
            echo $this->selectJson("nomecursos", [], ["cursos.idCursoEscola"=>$this->idEscola, "idPNomeCurso"=>$this->idPNomeCurso], ["cursos"]);
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>

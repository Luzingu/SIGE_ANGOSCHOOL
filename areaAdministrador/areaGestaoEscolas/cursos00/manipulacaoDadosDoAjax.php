<?php
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($tipoBaseDados){
            parent::__construct();
            $this->tipoBaseDados=$tipoBaseDados;
            $this->idPCurso = filter_input(INPUT_POST, "idPCurso", FILTER_SANITIZE_NUMBER_INT);

            $this->ordem = isset($_POST["ordem"])?$_POST["ordem"]:"";
            $this->idSubSistema = isset($_POST["idSubSistema"])?$_POST["idSubSistema"]:"";
            $this->primeiraClasse = isset($_POST["primeiraClasse"])?$_POST["primeiraClasse"]:"";
            $this->ultimaClasse = isset($_POST["ultimaClasse"])?$_POST["ultimaClasse"]:"";
            $this->curriculo1 = isset($_POST["curriculo1"])?$_POST["curriculo1"]:"";
            $this->curriculo2 = isset($_POST["curriculo2"])?$_POST["curriculo2"]:"";
            $this->curriculo3 = isset($_POST["curriculo3"])?$_POST["curriculo3"]:"";

            $this->duracaoCurso = filter_input(INPUT_POST, "duracaoCurso", FILTER_SANITIZE_NUMBER_INT);
            $this->nomeCurso = limpadorEspacosDuplicados(filter_input(INPUT_POST, "nomeCurso", FILTER_SANITIZE_STRING));
            $this->tipoCurso = limpadorEspacosDuplicados(filter_input(INPUT_POST, "tipoCurso", FILTER_SANITIZE_STRING));
            $this->areaFormacao = limpadorEspacosDuplicados(filter_input(INPUT_POST, "areaFormacao", FILTER_SANITIZE_STRING));
            $this->sePorSemestre = limpadorEspacosDuplicados(filter_input(INPUT_POST, "sePorSemestre", FILTER_SANITIZE_STRING));

            $this->abrevCurso = limpadorEspacosDuplicados(filter_input(INPUT_POST, "abrevCurso", FILTER_SANITIZE_STRING));
            $this->especialidadeCurso = limpadorEspacosDuplicados(filter_input(INPUT_POST, "especialidadeCurso", FILTER_SANITIZE_STRING));
            $this->conDb($tipoBaseDados);

            if($this->accao=="editarCurso"){
                if($this->verificacaoAcesso->verificarAcesso("", ["cursos00"])){
                  $this->editarCurso();
                }
            }else if($this->accao=="salvarCurso"){

                if($this->verificacaoAcesso->verificarAcesso("", ["cursos00"])){
                      $this->salvarCurso();
                }
            }else if ($this->accao=="excluirCurso"){
                if($this->verificacaoAcesso->verificarAcesso("", ["cursos00"])){
                  $this->excluirCurso();
                }
            }
        }

        private function salvarCurso(){
            if($this->inserir("nomecursos", "idPNomeCurso", "nomeCurso, tipoCurso, areaFormacaoCurso, abrevCurso, especialidadeCurso, sePorSemestre, ordem, primeiraClasse, ultimaClasse, idSubSistema, curriculo1, curriculo2, curriculo3, desCurriculo1, desCurriculo2, desCurriculo3", [$this->nomeCurso, $this->tipoCurso, $this->areaFormacao, $this->abrevCurso, $this->especialidadeCurso, $this->sePorSemestre, $this->ordem, $this->primeiraClasse, $this->ultimaClasse, $this->idSubSistema, $this->curriculo1, $this->curriculo2, $this->curriculo3, $this->selectUmElemento("escolas", "abrevNomeEscola", ["idPEscola"=>$this->curriculo1]), $this->selectUmElemento("escolas", "abrevNomeEscola", ["idPEscola"=>$this->curriculo2]), $this->selectUmElemento("escolas", "abrevNomeEscola", ["idPEscola"=>$this->curriculo3])])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível cadastrar o curso.";
            }
        }

        private function editarCurso(){

          $presidente = $this->selectUmElemento("escolas", "abrevNomeEscola", ["idPEscola"=>$this->curriculo1]);
          if ($this->curriculo1 == 0)
            $presidente = "Todas";

            if($this->editar("nomecursos", "nomeCurso, tipoCurso, areaFormacaoCurso, abrevCurso, especialidadeCurso, sePorSemestre, ordem, primeiraClasse, ultimaClasse, idSubSistema, curriculo1, curriculo2, curriculo3, desCurriculo1, desCurriculo2, desCurriculo3", [$this->nomeCurso, $this->tipoCurso, $this->areaFormacao, $this->abrevCurso, $this->especialidadeCurso, $this->sePorSemestre, $this->ordem, $this->primeiraClasse, $this->ultimaClasse, $this->idSubSistema, $this->curriculo1, $this->curriculo2, $this->curriculo3, $presidente, $this->selectUmElemento("escolas", "abrevNomeEscola", ["idPEscola"=>$this->curriculo2]), $this->selectUmElemento("escolas", "abrevNomeEscola", ["idPEscola"=>$this->curriculo3])], ["idPNomeCurso"=>$this->idPCurso])=="sim"){

                $this->editar("horario", "nomeCurso, abrevCurso, areaFormacaoCurso, tipoCurso, sePorSemestre", [$this->nomeCurso, $this->abrevCurso, $this->especialidadeCurso, $this->tipoCurso, $this->sePorSemestre], ["idPNomeCurso"=>$this->idPCurso]);
                $this->editar("divisaoprofessores", "nomeCurso, abrevCurso, areaFormacaoCurso, tipoCurso, sePorSemestre", [$this->nomeCurso, $this->abrevCurso, $this->especialidadeCurso, $this->tipoCurso, $this->sePorSemestre], ["idPNomeCurso"=>$this->idPCurso]);

                $this->listar();
            }else{
                echo "FNão foi possível editar os dados do curso.";
            }
        }

        private function excluirCurso(){
            if(count($this->selectArray("nomecursos", ["cursos.idCursoEscola"], ["cursos.idFNomeCurso"=>$this->idPCurso]))>0){
                echo "FNão podes excluir este curso.";
            }else{
                if($this->editar("nomecursos", "nomeCurso, duracao, tipoCurso, areaFormacaoCurso, abrevCurso, especialidadeCurso, sePorSemestre", [null, null, null, null, null, null, null], ["idPNomeCurso"=>$this->idPCurso])=="sim"){
                    $this->listar();
                }else{
                    echo "FNão foi possível excluir o curso.";
                }
            }
        }
        private function listar(){
            if($this->tipoBaseDados=="escola"){
                echo $this->selectJson("nomecursos", [], ["nomeCurso"=>array('$ne'=>null)], [], "", [], array("ordem"=>1));
            }
        }

    }
    new manipulacaoDadosDoAjaxInterno("escola");
    new manipulacaoDadosDoAjaxInterno("teste");
?>

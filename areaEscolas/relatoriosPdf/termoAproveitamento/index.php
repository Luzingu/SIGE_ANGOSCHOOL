<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php');
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliaresDb.php'); 
    include_once 'ensinoMedioTecnico.php';
    include_once 'ensinoMedioPedagogico.php';
    include_once 'ensinoMedioGeral.php';
    include_once 'ensinoMedioPorSemestre.php';

    class termoFrequencia extends funcoesAuxiliares{
        
        function __construct($caminhoAbsoluto){

            parent::__construct("Rel-Termo de Aproveitamento");
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->documento = isset($_GET["documento"])?$_GET["documento"]:null;
            $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:null;
            
            if($this->verificacaoAcesso->verificarAcesso("", "relatorioAluno", array(), "")){

                $this->sobreAluno($this->idPMatricula);

                $this->sobreAluno = $this->sobreEscreverAluno($this->sobreAluno, $this->idPCurso);
                $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_provincias", "idPProvincia", "provNascAluno");
                 $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_municipios", "idPMunicipio", "municNascAluno");
                 $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_comunas", "idPComuna", "comunaNascAluno");
                 $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "nomecursos", "idPNomeCurso", "idMatCurso");
                $this->sobreCursoAluno = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

                $this->idPMatricula = valorArray($this->sobreAluno, "idPMatricula", "escola");
                $this->idPCurso = valorArray($this->sobreAluno, "idMatCurso", "escola");
                $this->nomeCurso();

                if($this->tipoCurso=="primaria" || $this->tipoCurso=="basica" || $this->tipoCurso=="geral"){
                    $termoFrequencia = new ensinoGeral(__DIR__);
                }else if($this->tipoCurso=="tecnico"){
                    if($this->sePorSemestre=="sim"){
                       // $termoFrequencia = new ensinoMedioPorSemestre(__DIR__);
                    }else{
                        $termoFrequencia = new ensinoMedioTecnico(__DIR__);
                    }
                }else if($this->tipoCurso=="pedagogico"){
                    $termoFrequencia = new ensinoMedioPedagogico(__DIR__);
                }
                if(valorArray($this->sobreAluno, "sexoAluno")=="M"){
                    $termoFrequencia->art1="o";
                    $termoFrequencia->art2 ="";
                }else{
                    $termoFrequencia->art1="a";
                    $termoFrequencia->art2 ="a";
                }
                $termoFrequencia->sobreAluno=$this->sobreAluno;
                $termoFrequencia->idPMatricula=$this->idPMatricula;
                $termoFrequencia->idPCurso=$this->idPCurso;
                $termoFrequencia->sobreCursoAluno=$this->sobreCursoAluno;
                $termoFrequencia->exibirTermo();
            }else{
                $this->negarAcesso();
            }
            
        }
    }

new termoFrequencia(__DIR__);
?>
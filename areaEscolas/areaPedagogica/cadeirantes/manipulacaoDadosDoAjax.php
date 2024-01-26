<?php 
	session_start();
	
   include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            if($this->accao=="manipularPautasCadeirantes"){

                if($this->verificacaoAcesso->verificarAcesso("", "cadeirantes")){
                    $this->manipularPautasCadeirantes();
                }
            }
    	}

        private function manipularPautasCadeirantes(){
            $idPCadeirantes = $_POST["idPCadeirantes"];
            $exameEspecial = $_POST["exameEspecial"];
            $classe = $_POST["classe"];
            $idPCurso = $_POST["idPCurso"];
            $idPNomeDisciplina = $_POST["idPNomeDisciplina"];
            $idPMatricula = $_POST["idPMatricula"];
            $grupo = $_POST["grupo"];

            $this->editarItemObjecto("alunos_".$grupo, "pautas", "exameEspecial", [$exameEspecial], ["idPMatricula"=>$idPMatricula], ["classePauta"=>$classe, "idPautaDisciplina"=>$idPNomeDisciplina, "idPautaCurso"=>$idPCurso]);

            $this->editarItemObjecto("alunos_".$grupo, "cadeiras_atraso", "exameEspecial", [$exameEspecial], ["idPMatricula"=>$idPMatricula], ["idPCadeirantes"=>$idPCadeirantes]);


            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "cadeiras_atraso.idPCadeirantes", "numeroInterno", "fotoAluno", "idPMatricula", "cadeiras_atraso.exameEspecial", "cadeiras_atraso.idCadAno", "grupo", "cadeiras_atraso.idCadDisciplina"], ["cadeiras_atraso.idCadEscola"=>$_SESSION['idEscolaLogada'], "cadeiras_atraso.classeCadeira"=>$classe, "cadeiras_atraso.idCadCurso"=>$idPCurso, "cadeiras_atraso.estadoCadeira"=>"F"], ["cadeiras_atraso"], "", [], ["nomeAluno"=>1]);
            $alunos = $this->anexarTabela2($alunos, "nomedisciplinas", "cadeiras_atraso", "idPNomeDisciplina", "idCadDisciplina");
            $alunos = $this->anexarTabela2($alunos, "anolectivo", "cadeiras_atraso", "idPAno", "idCadAno");
            echo json_encode($alunos);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
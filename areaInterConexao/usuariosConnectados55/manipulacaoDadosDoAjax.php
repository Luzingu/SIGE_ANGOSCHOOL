<?php 
	session_start();
  
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();

          $this->conDb("escola", true);
          if($this->accao=="pesqUsuarios"){
            $this->pesqUsuarios();
          }
    	}

      private function pesqUsuarios(){
        $usuarioPesq = isset($_GET["usuarioPesq"])?$_GET["usuarioPesq"]:"";

        $professor = $this->selectArray("entidadesprimaria", ['$or'=>[array("nomeEntidade"=>new \MongoDB\BSON\Regex($usuarioPesq)),  array("nomeEntidade"=>new \MongoDB\BSON\Regex(ucwords($usuarioPesq)))]], ["limit"=>50]);
        $professor = zipador($professor, [["escola", ["estadoActividadeEntidade=A"]]]);

        $professor = $this->anexarTabela($professor, "escolas", "idPEscola", "idEntidadeEscola");

        $aluno=array();
        $aluno = $this->selectArray("alunosmatriculados", ['$or'=>[array("nomeAluno"=>new \MongoDB\BSON\Regex($usuarioPesq)),  array("nomeAluno"=>new \MongoDB\BSON\Regex(ucwords($usuarioPesq)))]], ["limit"=>50]);
        $aluno = zipador($aluno, [["escola", ["estadoAluno=A"]]]);
        $aluno = $this->anexarTabela($aluno, "escolas", "idPEscola", "idMatEscola");
        $aluno = $this->anexarTabela($aluno, "nomecursos", "idPNomeCurso", "idMatCurso");

        echo json_encode(array_merge($professor, $aluno));     
        
      }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
<?php 
	session_start();
	 include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        private $idPMatricula="";
        private $classe="";
        private $idPCurso="";
        private $idAnoMatriculadoAluno="";

        private $campoPesquisado=array();
        private $valorPesquisado=array();
        private $operador=array();

        private $condicaoFinal=array();


    	function __construct($caminhoAbsoluto){
    	parent::__construct();
          if($this->accao=="fazerPesquisaAlunos"){
            if($this->verificacaoAcesso->verificarAcesso("", ["zonaPesquisa"], [])){
              $this->fazerPesquisa();
            }
          }	        
    	} 


        private function fazerPesquisa(){

            $campoPesquisado=$_GET["campoPesquisado"];
            $valorPesquisado=$_GET["valorPesquisado"];
            $operador=$_GET["operador"];

            $classe = $_GET["classe"];
            $turma = $_GET["turma"];
            $idAnoLectivo = $_GET["idAnoLectivo"];
            $idPCurso = $_GET["idPCurso"];

            
            if($idPCurso=="T" && $classe=="T"){
                $condicao = ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$idAnoLectivo];
                $idPCurso="";
                $classe="";
            }else if($idPCurso=="T"){
                $condicao = ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$idAnoLectivo, "reconfirmacoes.classeReconfirmacao"=>$classe];
                $idPCurso="";
            }else if($classe=="T"){
                $condicao = ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$idAnoLectivo, "reconfirmacoes.idMatCurso"=>$idPCurso];
                $classe="";
            }else{
                $condicao = ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$idAnoLectivo, "reconfirmacoes.idMatCurso"=>$idPCurso, "reconfirmacoes.classeReconfirmacao"=>$classe];
            }

            $alunos = $this->selectArray("alunosmatriculados", ["dataNascAluno", "nomeAluno", "sexoAluno", "reconfirmacoes.classeReconfirmacao", "numeroInterno", "idPMatricula", "fotoAluno", "reconfirmacoes.idMatCurso"], $condicao, ["reconfirmacoes"], "", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idAnoActual, $idPCurso, $classe));

            $arrayRetorno=array();
            foreach($alunos as $aluno){

                if(trim($valorPesquisado)==""){
                    $arrayRetorno[]=$aluno;
                }else if($campoPesquisado=="idade"){
                    if(seComparador($valorPesquisado, calcularIdade($this->ano, $aluno["dataNascAluno"]), $operador)){
                        $arrayRetorno[]=$aluno;
                    }
                }else if($campoPesquisado=="dataNascAluno"){
                    $dataNascimento = isset($aluno["dataNascAluno"])?$aluno["dataNascAluno"]:"";
                    $dataNas = explode("-", $dataNascimento);

                    $ano = $dataNas[0];
                    $mes = isset($dataNas[1])?$dataNas[1]:0;
                    $dia = isset($dataNas[2])?$dataNas[2]:0;
                    if($operador=="dia"){
                        if($dia==$valorPesquisado){
                            $arrayRetorno[]=$aluno;
                        }
                    }else if($operador=="mes"){
                        if($mes==$valorPesquisado){
                            $arrayRetorno[]=$aluno;
                        }
                    }else if($operador=="ano"){
                        if($ano==$valorPesquisado){
                            $arrayRetorno[]=$aluno;
                        }
                    }else{
                        if(seComparador($valorPesquisado, $dataNas, $operador)){
                            $arrayRetorno[]=$aluno;
                        }
                    }

                }else{
                    $valorDb = isset($aluno[$campoPesquisado])?$aluno[$campoPesquisado]:"";
                    if($operador=="comecar"){
                        if(stripos($valorDb, trim($valorPesquisado))==0){
                            $arrayRetorno[]=$aluno;
                        }  
                    }else if($operador=="tem"){
                        if(stripos($valorDb, trim($valorPesquisado))>0){
                            $arrayRetorno[]=$aluno;
                        }  
                    }else{
                        if(seComparador($valorPesquisado, $valorDb, $operador) || seComparador(ucwords($valorPesquisado), $valorDb, $operador)){
                            $arrayRetorno[]=$aluno;
                        }  
                    }
                }
            }
            $arrayRetorno = $this->anexarTabela2($arrayRetorno, "nomecursos", "reconfirmacoes", "idPNomeCurso", "idMatCurso");

            echo json_encode($arrayRetorno);                
        }    	
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
    include_once 'miniPautasModeloGeral.php';
    include_once 'miniPautasPorSemestre.php';

    class listaTurmas extends funcoesAuxiliares{
        function __construct($caminhoAbsoluto){

            parent::__construct("Rel-Mini Pautas"); 
            if(isset($_GET["idPAno"])){
                $idPAno = $_GET["idPAno"];
            }else{
                $idPAno = $this->idAnoActual;
            }

            $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $turma = isset($_GET["turma"])?$_GET["turma"]:null;
            $trimestreApartir = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;
            $idPDisciplina = isset($_GET["idPDisciplina"])?$_GET["idPDisciplina"]:null;
            $semestre = isset($_GET["semetre"])?$_GET["semetre"]:null;
            if($semestre!="I" && $semestre!="II"){
                $semestre="I";
            }
            
            if($trimestreApartir<1 || $trimestreApartir>4){
                $trimestreApartir=1;
            }

            if($trimestreApartir==1){
                $trimestreApartirExtensa="Iº TRIMESTRE";
                $trimestreAbr="I";
            }else if($trimestreApartir==2){
                $trimestreApartirExtensa="IIº TRIMESTRE";
                $trimestreAbr="II";
            }else if($trimestreApartir==3){
                $trimestreApartirExtensa="IIIº TRIMESTRE";
                $trimestreAbr="III";
            }else if($trimestreApartir==4){
                $trimestreApartirExtensa="FINAL";
                $trimestreAbr="";
            }

            if($classe<=6){
                $notaMinima=5;
            }else{
                $notaMinima=10;
            }

             $dadosProfessor = $this->selectCondClasseCurso("array", "divisaoprofessores", [], ["classe"=>$classe, "nomeTurmaDiv"=>$turma, "idDivAno"=>$idPAno, "idPNomeDisciplina"=>$idPDisciplina, "idPEscola"=>$_SESSION["idEscolaLogada"]], $classe, ["idPNomeCurso"=>$idPCurso]);
             
            if(valorArray($dadosProfessor, "sePorSemestre")=="sim"){
               $modelo = new modeloPorSemestre(__DIR__);
            }else{
               $modelo = new modeloGeral(__DIR__);
            }

            $modelo->idPAno=$idPAno;
            $modelo->idPCurso=$idPCurso;
            $modelo->classe=$classe;
            $modelo->turma=$turma;
            $modelo->trimestreApartir=$trimestreApartir;
            $modelo->idPDisciplina=$idPDisciplina;
            $modelo->trimestreApartir=$trimestreApartir;
            $modelo->trimestreApartirExtensa=$trimestreApartirExtensa;
            $modelo->trimestreAbr=$trimestreAbr;
            $modelo->notaMinima=$notaMinima;
            $modelo->dadosProfessor=$dadosProfessor;
            $modelo->semestre=$semestre;

            if($this->verificacaoAcesso->verificarAcesso(2, ["pautaGeral1", "pautasArquivadas"], [], "")){
               $modelo->exibirRelatorio();
            }else{                
                if(count($dadosProfessor)>0){
                    $modelo->exibirRelatorio();
                }else{
                    $this->negarAcesso();
                }
            }
        }
    }



new listaTurmas(__DIR__);
    
    
  
?>
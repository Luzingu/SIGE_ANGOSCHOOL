<?php 
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipuladorPauta.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
           

        function __construct($caminhoAbsoluto){
            parent::__construct();
           $this->manipuladorPautas = new manipuladorPauta();

            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->idPNomeCurso = isset($_GET["idPNomeCurso"])?$_GET["idPNomeCurso"]:null;
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:null;
            $this->idAnoActual = $this->idPAno;
            $this->manipuladorPautas->idAnoActual = $this->idPAno;

            if($this->accao=="alterarNotas"){ 
                if($this->verificacaoAcesso->verificarAcesso("", ["pautaConselhoNotas1"], [$this->classe, $this->idPNomeCurso])){
                    $this->manipularPauta();
                }      
            }else if($this->accao=="buscarDadosPauta"){
                $this->pegarDados();                
            }
        }

        private function manipularPauta(){
            
            $notas = json_decode($_GET["dados"]);

            $msgRetorno=array();

            foreach ($notas as $nota) {
                $idPMatricula = $nota->idPMatricula;
                $idPDisciplina = $nota->idPDisciplina;
                $grupo = $nota->grupo;

                $recurso = isset($nota->recurso)?$nota->recurso:null;

                $condicaoPauta = ["classePauta"=>$this->classe, "idPautaDisciplina"=>$idPDisciplina, "idPautaCurso"=>$this->idPNomeCurso];
                
                $this->editarItemObjecto("alunos_".$grupo, "pautas", "recurso", [$recurso], ["idPMatricula"=>$idPMatricula], $condicaoPauta);
                $condicaoPauta["idPautaAno"]=$this->idAnoActual;
                $condicaoPauta["idPautaEscola"]=$_SESSION['idEscolaLogada'];
                $this->editarItemObjecto("alunos_".$grupo, "arquivo_pautas", "recurso", [$recurso], ["idPMatricula"=>$idPMatricula], $condicaoPauta);
                $this->manipuladorPautas->calcularObservacaoFinalDoAluno($idPMatricula);           
            }
           $this->pegarDados();
        }


        private function pegarDados(){

            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "grupo", "numeroInterno", "pautas.classePauta", "pautas.seFoiAoRecurso", "pautas.cf", "pautas.mf", "pautas.recurso", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma", "pautas.idPautaDisciplina", "reconfirmacoes.observacaoF", "sexoAluno", "idPMatricula", "pautas.mf", "pautas.cf"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.seAlunoFoiAoRecurso"=>"A", "pautas.seFoiAoRecurso"=>"A", "reconfirmacoes.classeReconfirmacao"=>$this->classe, "pautas.classePauta"=>$this->classe, "pautas.idPautaCurso"=>$this->idPNomeCurso, "reconfirmacoes.idMatCurso"=>$this->idPNomeCurso], ["reconfirmacoes", "pautas"], "", [], ["nomeAluno"=>1]);
            $alunos = $this->anexarTabela2($alunos, "nomedisciplinas", "pautas", "idPNomeDisciplina", "idPautaDisciplina");

            echo json_encode($alunos);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>